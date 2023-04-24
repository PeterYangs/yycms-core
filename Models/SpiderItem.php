<?php

namespace Ycore\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * App\Models\SpiderItem
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $spider_id 采集表id
 * @property string $type
 * @property string $field 字段名称
 * @property string $selector 选择器
 * @property string $not_selector 排重选择器
 * @property string $image_prefix 图片前缀
 * @property string $replace_string_list 结果替换字符串,例：{"hhh":"ccc","kkkk":"jjjj"}
 * @property int $is_expand 是否是拓展字段数据，0不是,1是
 * @property string $attr_key 标签属性key值
 * @property int $get_source_link 是否获取源链接,0否，1是
 * @property int $category_map_id 映射分类id
 * @property string $lazy_image_attr_name 懒加载属性
 * @method static \Illuminate\Database\Eloquent\Builder|SpiderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SpiderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SpiderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|SpiderItem whereAttrKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpiderItem whereCategoryMapId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpiderItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpiderItem whereField($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpiderItem whereGetSourceLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpiderItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpiderItem whereImagePrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpiderItem whereIsExpand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpiderItem whereLazyImageAttrName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpiderItem whereNotSelector($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpiderItem whereReplaceStringList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpiderItem whereSelector($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpiderItem whereSpiderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpiderItem whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpiderItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SpiderItem extends Base
{

    protected $table = 'spider_item';

    protected $fillable = [
        'spider_id',
        'type',
        'selector',
        'not_selector',
        'image_prefix',
        'replace_string_list',
        'is_expand',
        'field',
        'attr_key',
        'get_source_link',
        'category_map_id',
        'lazy_image_attr_name'
    ];


    /**
     *
     * Create by Peter Yang
     * 2022-09-02 20:15:38
     * @return Attribute
     */
    function notSelector(): Attribute
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
     * 2022-09-02 20:16:37
     * @return Attribute
     */
    function replaceStringList(): Attribute
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
