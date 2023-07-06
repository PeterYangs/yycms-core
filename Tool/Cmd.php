<?php

namespace Ycore\Tool;

use Symfony\Component\Process\Process;

class Cmd
{


    /**
     * 执行命令行
     * @param $cmd
     * @param int $timeout
     * @param bool $isBackground 是否后台运行
     * @return string
     */
    public static function commandline($cmd, int $timeout = 30, bool $isBackground = false): string
    {


        $process = Process::fromShellCommandline($cmd);

        $process->setWorkingDirectory(base_path());

        $process->setTimeout($timeout);

        $msg = "";

        if ($isBackground) {

            $process->start();

            return "";
        }


        $code = $process->run(function ($type, string $buffer) use (&$msg) {


            $ch = mb_detect_encoding($buffer, ["ASCII", 'UTF-8', "GB2312", "GBK", 'BIG5']);


            if ($ch != "UTF-8") {


                $buffer = mb_convert_encoding($buffer, 'UTF-8', $ch);

            }


            $msg .= $buffer;

        });


        if ($code !== 0) {


            throw new \Exception($msg);
        }


        return $msg;
    }


    /**
     * @param $name
     * @return string
     */
    public static function getCommandlineByName($name): string
    {


        if (in_array(PHP_OS, ['WIN32', 'WINNT', 'Windows'])) {


            return ".\storage\app\public\\" . $name . ".exe";
        }

        if (in_array(PHP_OS, ['Darwin', 'FreeBSD', 'Linux'])) {


            return "./storage/app/public/" . $name;
        }

        return "";

    }

}
