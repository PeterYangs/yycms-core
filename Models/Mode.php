<?php

namespace Ycore\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * App\Models\Mode
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $title 描述
 * @property string $code 搜索标识，相当于id
 * @property string $tip_img 标识图片
 * @property string $field_list 表头
 * @property string $list 存储数据
 * @property int $hide 隐藏 1代表正常 2代表隐藏 默认为1
 * @property string|null $expired_time 过期时间
 * @method static \Illuminate\Database\Eloquent\Builder|Mode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mode query()
 * @method static \Illuminate\Database\Eloquent\Builder|Mode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mode whereExpiredTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mode whereFieldList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mode whereHide($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mode whereList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mode whereTipImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mode whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mode whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Mode extends Base
{

    protected $table = 'mode';

    protected $fillable = ['title', 'code', 'tip_img', 'field_list', 'list', 'hide', 'expired_time'];


    /**
     * Create by Peter Yang
     * 2022-06-24 11:51:14
     * @return Attribute
     */
    function fieldList(): Attribute
    {

        return new Attribute(

            get: function ($value) {


                if (!$value) {

                    return [];
                }

                return json_decode($value, true, 512, JSON_THROW_ON_ERROR);

            },
            set: function ($value) {

                if (!$value) {


                    return "";
                }


                return json_encode($value, JSON_THROW_ON_ERROR);

            }


        );

    }


    /**
     * Create by Peter Yang
     * 2022-06-24 11:51:14
     * @return Attribute
     */
    function list(): Attribute
    {

        return new Attribute(

            get: function ($value) {


                if (!$value) {

                    return [];
                }

                return json_decode($value, true, 512, JSON_THROW_ON_ERROR);

            },
            set: function ($value) {

                if (!$value) {


                    return "";
                }


                return json_encode($value, JSON_THROW_ON_ERROR);

            }


        );

    }


}
