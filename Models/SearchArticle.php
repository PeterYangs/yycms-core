<?php

namespace Ycore\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class SearchArticle extends Base
{

    use SoftDeletes;

    protected $table = 'search_article';

    protected $fillable = ['title', 'category_id', 'seo_desc', 'seo_keyword', 'content', 'type', 'keyword'];

    function category()
    {


        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

}
