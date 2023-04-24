<?php

namespace Ycore\Http\Controllers\Admin;

use App\Tool\Json;
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

//        mb_strlen()

        if ($ids) {

            $cmd = "  ./script/staticTool start --cid " . implode(",", $ids) . " -d";

        } else {

            $cmd = "  ./script/staticTool start  " . " -d";
        }


        $process = Process::fromShellCommandline($cmd);


        $process->setWorkingDirectory($this->path);


        $msg = "success";

        try {


            $process->mustRun();

            $msg = $process->getOutput();

        } catch (\Exception $exception) {


            $msg = $exception->getMessage();


            return Json::code(2, $msg, $cmd);

        }


        return Json::code(1, "success", $cmd);


    }


    function process()
    {


        $process = Process::fromShellCommandline("./script/staticTool process");


        $process->setWorkingDirectory($this->path);


        $msg = "";

        try {


            $process->mustRun();

            $msg = $process->getOutput();

        } catch (\Exception $exception) {


            $msg = $exception->getMessage();


            return Json::code(2, $msg);

        }


        return Json::code(1, $msg);

    }


    function stop()
    {


        $process = Process::fromShellCommandline("./script/staticTool stop");


        $process->setWorkingDirectory($this->path);


        $msg = "";

        try {


            $process->mustRun();

            $msg = $process->getOutput();

        } catch (\Exception $exception) {


            $msg = $exception->getMessage();


            return Json::code(2, $msg);

        }


        return Json::code(1, $msg);

    }


}
