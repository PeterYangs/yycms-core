<?php

namespace Ycore\Http\Controllers\Admin;

use Ycore\Models\Spider;
use Ycore\Models\SpiderItem;
use Ycore\Tool\Json;
use Ycore\Tool\Search;
use Symfony\Component\Process\Process;

class SpiderController extends AuthCheckController
{

    function update()
    {


        $post = request()->input();

        $id = $post['id'] ?? 0;


        $spider = Spider::updateOrCreate(['id' => $id], $post);


        foreach ($post['spider_item'] as $value) {


            $value['spider_id'] = $spider->id;

            SpiderItem::updateOrCreate(['id' => $value['id']], $value);


        }


        return Json::code(1, 'success');

    }


    function list()
    {


        $list = Spider::withCount([
            'storeArticle as today' => function (\Illuminate\Database\Eloquent\Builder $query) {


                $query->whereDate('created_at', \Date::now()->format("Y-m-d"));

            },
            'storeArticle as yesterday' => function (\Illuminate\Database\Eloquent\Builder $query) {

                $query->whereDate('created_at', \Date::now()->subDay()->format("Y-m-d"));

            }
        ])->orderBy('id', 'desc');

        Search::searchList($list, request()->input('search', '[]'));

        return Json::code(1, 'success', paginate($list, request()->input('p', 1)));
    }


    function detail()
    {

        $id = request()->input('id');


        $data = Spider::with('spider_item')->find($id);

        return Json::code(1, 'success', $data);
    }


    /**
     * 变更状态
     * @return string
     */
    function status()
    {


        $id = request()->input('id');

        $status = request()->input('status');

        $spider = Spider::where('id', $id)->firstOrFail();


        $spider->status = $status;


        $spider->save();


        return Json::code(1, 'success');


    }


    /**
     * 执行所有配置
     */
    function runAll()
    {


        $process = Process::fromShellCommandline('./script/spider start --skip');

        $process->setWorkingDirectory(base_path());


        $process->run();

        $out = $process->getOutput();

        return Json::code(1, 'success', $out);

    }


    /**
     * 以调试模式运行一个配置
     * @return string
     */
    function debug()
    {

        $id = request()->input('id');

        $process = Process::fromShellCommandline('./script/spider start --id ' . $id . " --debug");

        $process->setWorkingDirectory(base_path());


        try {

            $process->mustRun();

            $msg = $process->getOutput();


            \Log::channel('spider')->info($msg);


        } catch (\Exception $exception) {

            \Log::error($exception->getMessage());

            return Json::code(2, $exception->getMessage());

        }


        return Json::code(1, $msg);
    }


}
