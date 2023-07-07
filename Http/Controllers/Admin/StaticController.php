<?php

namespace Ycore\Http\Controllers\Admin;

use Ycore\Tool\Cmd;
use Ycore\Tool\Json;
use Symfony\Component\Process\Process;

class StaticController extends AuthCheckController
{


    protected string $path;


    function __construct()
    {
        parent::__construct();

        $this->path = base_path();
    }

    function run()
    {

        $ids = request()->input('ids', []);

        if (!is_array($ids)) {


            return Json::code(2, '类型错误');
        }


        if ($ids) {


            $cmd = Cmd::getCommandlineByName('goScript') . " staticTool --cid " . implode(",", $ids);

        } else {


            $cmd = Cmd::getCommandlineByName('goScript') . " staticTool";
        }


        try {

            Cmd::commandline($cmd, 10, true);

        } catch (\Exception $exception) {


            return Json::code(2, $exception->getMessage());

        }

        return Json::code(1, "success", $cmd);

    }


    function process()
    {


        $out = Cmd::commandline(Cmd::getCommandlineByName('goScript') . " process");


        return Json::code(1, $out);

    }


    function stop()
    {


        $out = Cmd::commandline(Cmd::getCommandlineByName('goScript') . " process-stop");


        return Json::code(1, $out);

    }


}
