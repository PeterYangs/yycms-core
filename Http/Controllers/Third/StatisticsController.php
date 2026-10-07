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

        foreach (self::OPTION_KEYS as $device => $key) {
            $global[$device] = AccessStatistics::describe((string)getOption($key, ''));
        }

        $specials = Special::orderBy('id')->get(['id', 'title', 'pc_js', 'mobile_js'])->map(function (Special $special) {
            return [
                'id' => $special->id,
                'title' => $special->title,
                'pc' => AccessStatistics::describe((string)$special->pc_js),
                'mobile' => AccessStatistics::describe((string)$special->mobile_js),
            ];
        });

        return Signature::success([
            'domain' => getOption('domain', ''),
            'm_domain' => getOption('m_domain', ''),
            'site_name' => getOption('site_name', ''),
            'special_enabled' => SwitchCore::enabled(SwitchCore::ARTICLE_SPECIAL_ATTRIBUTE),
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

        $merged = AccessStatistics::merge($current, $item['code']);

        if ($merged === $current) {
            $action = 'unchanged';
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

        return $result + ['action' => $action, 'before' => $before];
    }

}
