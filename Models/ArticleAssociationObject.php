<?php

namespace Ycore\Models;

/**
 * App\Models\ArticleAssociationObject
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $main 主文章id
 * @property int $slave 关联文章id
 * @property-read \App\Models\Article|null $mainArticle
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleAssociationObject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleAssociationObject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleAssociationObject query()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleAssociationObject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleAssociationObject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleAssociationObject whereMain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleAssociationObject whereSlave($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleAssociationObject whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ArticleAssociationObject extends Base
{

    protected $table = "article_association_object";

    protected $fillable = ['main', 'slave'];


    function mainArticle()
    {


        return $this->belongsTo(Article::class, 'main', 'id');
    }


}
