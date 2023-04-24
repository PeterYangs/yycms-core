<?php

namespace Ycore\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * App\Models\Spider
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $title 描述
 * @property string $host 域名
 * @property string $channel 栏目
 * @property string $list_selector 列表选择器
 * @property string $href_selector a标签选择器(相当于列表选择器)
 * @property int $page_start 起始页码
 * @property int $length 爬取页码长度
 * @property string $title_selector 详情a标签(相当于列表选择器)
 * @property int $detail_coroutine_number 协程数量
 * @property int $status 状态，1正常，0禁用
 * @property int $number 采集个数
 * @property string $time 采集时间,范围是0-23,单位是小时
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SpiderItem[] $spider_item
 * @property-read int|null $spider_item_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\StoreArticle[] $storeArticle
 * @property-read int|null $store_article_count
 * @method static \Illuminate\Database\Eloquent\Builder|Spider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Spider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Spider query()
 * @method static \Illuminate\Database\Eloquent\Builder|Spider whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Spider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Spider whereDetailCoroutineNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Spider whereHost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Spider whereHrefSelector($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Spider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Spider whereLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Spider whereListSelector($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Spider whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Spider wherePageStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Spider whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Spider whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Spider whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Spider whereTitleSelector($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Spider whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Spider extends Base
{

    protected $table = 'spider';

    protected $fillable = [
        'host',
        'channel',
        'list_selector',
        'href_selector',
        'page_start',
        'length',
        'title_selector',
        'detail_coroutine_number',
        'title',
        'number',
        'time',
        'special_id',
        'title_keywords',
        'headers',
        'title_not_keywords'
    ];


    function storeArticle()
    {


        return $this->hasMany(StoreArticle::class, 'spider_id', 'id');
    }

    function spider_item()
    {


        return $this->hasMany(SpiderItem::class, 'spider_id', 'id');
    }

    function time(): Attribute
    {

        return new Attribute(

            get: function ($value) {


                if (!$value) {

                    return [];
                }


                $list = explode(',', $value);


                return array_map(static function ($value) {

                    if ($value <= 9) {


                        return "0" . $value . ":00";
                    }

                    return $value . ":00";

                }, $list);

            },
            set: function ($value) {

                if (!$value) {


                    return "";
                }


                if (!is_array($value)) {


                    return "";
                }

                $value = array_map(function ($item) {


                    return (int)(explode(":", $item)[0]);

                }, $value);


                return implode(',', $value);

            }


        );

    }


    function headers(): Attribute
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
