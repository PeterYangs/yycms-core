<?php

namespace Ycore\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * App\Models\ArticleExpandDetail
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $article_expand_id
 * @property string $name 字段名称
 * @property string $desc 字段描述
 * @property int $type 字段类型,1输入框,2文本框,3单选框,4复选框,5时间,6图片,7模型
 * @property string $select_list 单选框和复选框的选项
 * @property string $model_name 模型名称
 * @property string $label 弹窗显示字段,json存储
 * @property string $condition 查询条件,json存储
 * @property string $default_condition 默认查询条件,json存储
 * @property string $show_field 选择返回字段,json存储
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleExpandDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleExpandDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleExpandDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleExpandDetail whereArticleExpandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleExpandDetail whereCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleExpandDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleExpandDetail whereDefaultCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleExpandDetail whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleExpandDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleExpandDetail whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleExpandDetail whereModelName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleExpandDetail whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleExpandDetail whereSelectList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleExpandDetail whereShowField($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleExpandDetail whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleExpandDetail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ArticleExpandDetail extends Base
{


    protected $table = 'article_expand_detail';

    protected $fillable = [
        'article_expand_id',
        'name',
        'desc',
        'type',
        'select_list',
        'model_name',
        'label',
        'condition',
        'default_condition',
        'show_field'
    ];


    /**
     * Create by Peter Yang
     * 2022-06-22 17:11:49
     * @return Attribute
     */
    function selectList(): Attribute
    {

        return new Attribute(

            get: function ($value) {

                if (!$value) {

                    return [];

                }

                return explode(",", $value);
            }
        );
    }

    /**
     * Create by Peter Yang
     * 2022-06-22 17:11:49
     * @return Attribute
     */
    function condition(): Attribute
    {

        return new Attribute(

            get: function ($value) {

                if (!$value) {

                    return [];

                }

                return json_decode($value, true);
            }
        );
    }

    /**
     * Create by Peter Yang
     * 2022-06-22 17:11:49
     * @return Attribute
     */
    function defaultCondition(): Attribute
    {

        return new Attribute(

            get: function ($value) {

                if (!$value) {

                    return [];

                }

                return json_decode($value, true);
            }
        );
    }


    /**
     * Create by Peter Yang
     * 2022-06-22 17:11:49
     * @return Attribute
     */
    function label(): Attribute
    {

        return new Attribute(

            get: function ($value, $attributes) {

                if (!$value) {

                    return [];

                }


                return json_decode($value, true);
            }
        );
    }


    /**
     * Create by Peter Yang
     * 2022-06-22 17:11:49
     * @return Attribute
     */
    function showField(): Attribute
    {

        return new Attribute(

            get: function ($value) {

                if (!$value) {

                    return [];

                }

                return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            }
        );
    }


}
