<?php

namespace Ycore\Models;

/**
 * Ycore\Models\Tag
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $title 标签名称
 * @property-read \App\Models\ArticleTag|null $article_tag
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ArticleTag[] $article_tags
 * @property-read int|null $article_tags_count
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Tag extends Base
{

    protected $table = 'tag';

    protected $fillable = ['title'];

    function article_tag()
    {


        return $this->hasOne(ArticleTag::class, "tag_id", 'id');
    }


    function article_tags()
    {


        return $this->hasMany(ArticleTag::class, "tag_id", 'id');
    }

}
