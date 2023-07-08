<?php

namespace Ycore\Core;

class Core
{


    public static function GetVersion()
    {


        return self::GetJson()['VERSION'] ?? "";
    }


    public static function GetAdminVersion()
    {

        return self::GetJson()['ADMIN_VERSION'] ?? "";
    }


    public static function GetGoVersion()
    {

        return self::GetJson()['GO_VERSION'] ?? "";
    }


    private static function GetJson()
    {

        $file = str_replace("\n", "", \File::get(dirname(__DIR__) . "/version.json"));

        return json_decode($file, true);

    }


}
