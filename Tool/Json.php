<?php


namespace Ycore\Tool;


class Json
{


    /**
     * Create by Peter Yang
     * 2022-06-17 16:19:23
     * @param $code
     * @param $msg
     * @param $data
     * @return string
     */
    static function code($code, $msg = '', $data = []):string
    {


        return json_encode(['code' => $code, 'msg' => $msg, 'data' => $data]);
    }

    /**
     * Create by Peter Yang
     * 2022-06-17 16:19:30
     * @param $code
     * @param $msg
     * @param $data
     * @return array
     */
    static function codeArray($code, $msg = '', $data = []):array
    {

        return ['code' => $code, 'msg' => $msg, 'data' => $data];
    }


}
