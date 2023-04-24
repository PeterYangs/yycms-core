<?php

namespace Ycore\Models;

/**
 * App\Models\ArticleTag
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $article_id 文章id
 * @property int $tag_id 标签id
 * @property-read \App\Models\Tag|null $tag
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTag whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTag whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ArticleTag extends Base
{

    protected $table = 'article_tag';

    protected $fillable = ['article_id', 'tag_id'];


    function tag()
    {


        return $this->belongsTo(Tag::class, 'tag_id', 'id');
    }


}
