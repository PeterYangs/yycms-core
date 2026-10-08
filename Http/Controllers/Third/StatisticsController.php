<?php

namespace Ycore\Http\Controllers\Third;

use Illuminate\Support\Facades\DB;
use Ycore\Models\Options;
use Ycore\Models\Special;
use Ycore\Tool\AccessStatistics;
use Ycore\Tool\Signature;
use Ycore\Tool\SwitchCore;

/**
 * 内容库批量设置站点访问统计
 */
class StatisticsController extends BaseController
{

    /**
     * 全局统计代码，pc 对应 /_common.js 的电脑端，mobile 对应移动端
     */
    private const OPTION_KEYS = ['pc' => 'statistics_pc', 'mobile' => 'statistics_mobile'];

    /**
     * 特殊属性统计代码，只在该特殊属性文章的详情页输出（/_detail-{id}.js）
     */
    private const SPECIAL_FIELDS = ['pc' => 'pc_js', 'mobile' => 'mobile_js'];


    /**
     * 统计代码现状
     * @return array
     */
    function info()
    {
        $global = [];

        // content 为原始脚本，统计项目导出给人工核对用（v0.3.59 起）
        foreach (self::OPTION_KEYS as $device => $key) {
            $content = (string)getOption($key, '');
            $global[$device] = AccessStatistics::describe($content) + ['content' => $content];
        }

        $specials = Special::orderBy('id')->get(['id', 'title', 'pc_js', 'mobile_js'])->map(function (Special $special) {
            return [
                'id' => $special->id,
                'title' => $special->title,
                'pc' => AccessStatistics::describe((string)$special->pc_js) + ['content' => (string)$special->pc_js],
                'mobile' => AccessStatistics::describe((string)$special->mobile_js) + ['content' => (string)$special->mobile_js],
            ];
        });

        return Signature::success([
            'domain' => getOption('domain', ''),
            'm_domain' => getOption('m_domain', ''),
            'site_name' => getOption('site_name', ''),
            'special_enabled' => SwitchCore::enabled(SwitchCore::ARTICLE_SPECIAL_ATTRIBUTE),
            // v0.3.59 起：remove_legacy 写入时可删除早期手动安装的旧统计代码；content 返回原始脚本
            'features' => ['remove_legacy', 'content'],
            'global' => $global,
            'specials' => $specials,
        ]);
    }


    /**
     * 写入统计代码，dry_run 为 true 时只返回将要执行的动作，不写库
     * @return array
     * @throws \Throwable
     */
    function sync()
    {
        $post = request()->post();

        $validator = \Validator::make($post, [
            'dry_run' => 'required|boolean',
            'items' => 'required|array|min:1|max:50',
            'items.*.target' => 'required|in:global,special',
            'items.*.device' => 'required|in:pc,mobile',
            'items.*.special_id' => 'required_if:items.*.target,special|integer',
            'items.*.code' => 'required|string|max:5000',
            'items.*.remove_legacy' => 'boolean',
        ]);

        if ($validator->fails()) {
            return Signature::fail(Signature::PARAMS_ERROR, $validator->errors()->first());
        }

        $dryRun = (bool)$post['dry_run'];

        $items = DB::transaction(function () use ($post, $dryRun) {

            $items = [];

            foreach ($post['items'] as $item) {
                $items[] = $this->syncItem($item, $dryRun);
            }

            return $items;
        });

        return Signature::success(['dry_run' => $dryRun, 'items' => $items]);
    }


    private function syncItem(array $item, bool $dryRun): array
    {
        $result = [
            'target' => $item['target'],
            'device' => $item['device'],
            'special_id' => (int)($item['special_id'] ?? 0),
        ];

        $special = null;

        if ($item['target'] === 'global') {
            $key = self::OPTION_KEYS[$item['device']];
            $current = (string)Options::where('key', $key)->lockForUpdate()->value('value');
        } else {
            $special = Special::where('id', $result['special_id'])->lockForUpdate()->first();

            if (!$special) {
                return $result + ['action' => 'missing'];
            }

            $field = self::SPECIAL_FIELDS[$item['device']];
            $current = (string)$special->$field;
        }

        $before = AccessStatistics::describe($current);

        $content = $current;
        $removed = 0;
        $removeLegacy = !empty($item['remove_legacy']);

        if ($removeLegacy) {
            [$content, $removed] = AccessStatistics::removeLegacy($current);
        }

        $merged = AccessStatistics::merge($content, $item['code']);

        $result += ['removed_legacy' => $removed, 'before' => $before, 'before_content' => $current, 'after_content' => $merged];

        // 要求清理旧代码但还有认不出格式的旧统计引用：这个位置不写，留给人工处理
        if ($removeLegacy && AccessStatistics::describe($merged)['unrecognized'] > 0) {
            return ['action' => 'legacy_unmatched'] + $result;
        }

        if ($merged === $current) {
            $action = 'unchanged';
        } elseif ($removed > 0) {
            $action = 'replace_legacy';
        } elseif ($before['managed']) {
            $action = 'replace';
        } else {
            $action = $before['empty'] ? 'add' : 'append';
        }

        if (!$dryRun && $action !== 'unchanged') {

            if ($special) {
                $special->$field = $merged;
                $special->save();
            } else {
                // 与后台网站设置保存方式一致
                setOption($key, $merged, true);
            }

        }

        return ['action' => $action] + $result;
    }

}
