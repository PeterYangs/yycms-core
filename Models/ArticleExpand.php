<?php

namespace Ycore\Models;

/**
 * App\Models\ArticleExpand
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $category_id 分类id
 * @property-read \App\Models\Category|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ArticleExpandDetail[] $list
 * @property-read int|null $list_count
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleExpand newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleExpand newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleExpand query()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleExpand whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleExpand whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleExpand whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleExpand whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ArticleExpand extends Base
{

    protected $table = 'article_expand';

    protected $fillable = ['category_id'];


    /**
     * Create by Peter Yang
     * 2022-06-22 14:19:19
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    function category()
    {


        return $this->belongsTo(Category::class, 'category_id', 'id');
    }


    function list()
    {


        return $this->hasMany(ArticleExpandDetail::class, 'article_expand_id', 'id');
    }


}
