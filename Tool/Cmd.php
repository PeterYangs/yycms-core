<?php

namespace Ycore\Tool;

use Symfony\Component\Process\Process;

class Cmd
{


    /**
     * 执行命令行
     * @param $cmd
     * @param int $timeout
     * @return string
     */
    public static function commandline($cmd, int $timeout = 30): string
    {

        $process = Process::fromShellCommandline($cmd);

        $process->setWorkingDirectory(base_path());

        $process->setTimeout($timeout);

        $msg = "";

        $process->run(function ($type, $buffer) use (&$msg) {


            $msg .= $buffer;

        });

        return $msg;
    }


    /**
     * @param $name
     * @return string
     */
    public static function getCommandlineByName($name): string
    {


        if (in_array(PHP_OS, ['WIN32', 'WINNT', 'Windows'])) {


            return ".\storage\\app\\public\\" . $name . ".exe";
        }

        if (in_array(PHP_OS, ['Darwin', 'FreeBSD', 'Linux'])) {


            return "./storage/app/public/" . $name;
        }

        return "";

    }

}
