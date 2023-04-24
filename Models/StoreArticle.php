<?php

namespace Ycore\Models;

/**
 * App\Models\StoreArticle
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $category_id 分类id
 * @property string $content 内容
 * @property string $img 主图
 * @property string $title 标题
 * @property string $seo_title seo标题
 * @property string $seo_desc seo描述
 * @property string $seo_keyword seo关键字
 * @property int $admin_id_create
 * @property int $admin_id_update
 * @property string|null $expand_data 拓展表数据
 * @property int $status 1是正常，2是已使用
 * @property int $debug 是否为调试文章，0否，1是(调试文章不会发布到正式文章)
 * @property int $spider_id 采集表id,用于采集计数
 * @property-read \App\Models\Category|null $category
 * @method static \Illuminate\Database\Eloquent\Builder|StoreArticle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StoreArticle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StoreArticle query()
 * @method static \Illuminate\Database\Eloquent\Builder|StoreArticle whereAdminIdCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoreArticle whereAdminIdUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoreArticle whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoreArticle whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoreArticle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoreArticle whereDebug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoreArticle whereExpandData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoreArticle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoreArticle whereImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoreArticle whereSeoDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoreArticle whereSeoKeyword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoreArticle whereSeoTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoreArticle whereSpiderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoreArticle whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoreArticle whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoreArticle whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StoreArticle extends Base
{

    protected $table = 'store_article';

    protected $fillable = [
        'category_id',
        'content',
        'img',
        'title',
        'seo_title',
        'seo_desc',
        'seo_keyword',
        'admin_id_create',
        'admin_id_update',
        'expand_data',
        'status',
        'debug',
        'spider_id',
        'special_id'
    ];


    function category()
    {


        return $this->belongsTo(Category::class, 'category_id', 'id');
    }


    function special()
    {


        return $this->belongsTo(Special::class, 'special_id', 'id');
    }


}
