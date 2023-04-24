<?php

namespace Ycore\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * App\Models\ExpandChange
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $special_id 特殊属性表id
 * @property int $category_id 分类id
 * @property string $detail 属性替换，json存储
 * @property-read \App\Models\Category|null $category
 * @property-read \App\Models\Special|null $special
 * @method static \Illuminate\Database\Eloquent\Builder|ExpandChange newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpandChange newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpandChange query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpandChange whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpandChange whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpandChange whereDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpandChange whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpandChange whereSpecialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExpandChange whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExpandChange extends Base
{

    protected $table = 'expand_change';

    protected $fillable = ['special_id', 'category_id', 'detail'];


    function detail(): Attribute
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


    function category()
    {


        return $this->belongsTo(Category::class, 'category_id', 'id');
    }


    function special()
    {


        return $this->belongsTo(Special::class, 'special_id', 'id');
    }


}
