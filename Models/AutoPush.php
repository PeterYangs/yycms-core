<?php

namespace Ycore\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;

class AutoPush extends Mode
{

    protected $table = 'auto_push';

    protected $fillable = [
        'type',
        'category_id',
        'time_range',
        'cycle',
        'min',
        'hour',
        'day',
        'number',
        'rule',
        'status',
        'push_status'
    ];

    protected $appends = ['cycle_desc'];


    function cycleDesc(): Attribute
    {


        return new Attribute(

            get: function ($value, $data) {


                switch ($data['cycle']) {

                    case "day":

                        return "每" . $data['day'] . "天的" . $data['hour'] . "点" . $data['min'] . "分";


                    case "hour":


                        return "每" . $data['hour'] . "小时的" . $data['min'] . "分";


                    case "min":

                        return "每" . $data['min'] . "分钟";

                }

                return "";
            }


        );

    }


    function category()
    {


        return $this->belongsTo(Category::class, 'category_id', 'id');
    }


    function timeRange(): Attribute
    {

        return new Attribute(

            get: function ($value) {


                if (!$value) {

                    return "";
                }


                $arr = explode(",", $value);

                if (count($arr) < 2) {


                    return "";
                }

                return $arr;

            },
            set: function ($value) {

                if (!$value) {

                    return "";
                }

                return implode(",", $value);

            }


        );

    }


}
