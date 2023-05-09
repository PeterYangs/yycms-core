<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2022/6/20
 * Time: 16:35
 */

namespace Ycore\Models;

use Illuminate\Database\Eloquent\Builder;


/**
 * App\Models\Category
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $pid 父id
 * @property int $lv 分类等级
 * @property string $name 分类名称
 * @property string|null $img 分类图片
 * @property string $category_list 栏目列表
 * @property string $category_detail 栏目详情
 * @property string|null $seo_title 分类seo标题
 * @property string|null $seo_keywords 分类seo关键词
 * @property string|null $seo_description 分类seo描述
 * @property int $status 分类上架状态 0未上架 1上架
 * @property int $sort 排序值,越小越靠前
 * @property-read \Illuminate\Database\Eloquent\Collection|Category[] $children
 * @property-read int|null $children_count
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCategoryDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCategoryList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereLv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereSeoDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereSeoKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereSeoTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CategoryRoute[] $category_route
 * @property-read int|null $category_route_count
 */
class Category extends Base
{
    protected $table = 'category';
    protected $fillable = [
        'pid',
        'lv',
        'name',
        'img',
        'category_list',
        'category_detail',
        'seo_title',
        'seo_keywords',
        'seo_description',
        'status',
        'sort'
    ];

    protected static function booted()
    {
        parent::booted(); // TODO: Change the autogenerated stub


        //全局作用域
        static::addGlobalScope('statusScope', function (Builder $builder) {


            $builder->where('status', 1);

        });

    }

    public function allChildren()
    {

        return $this->children()->with('allChildren');
    }

    public function children()
    {

        return $this->hasMany(get_class($this), 'pid', 'id')->select(['id', 'pid']);
    }

    public function category_route()
    {


        return $this->hasMany(CategoryRoute::class, 'category_id', 'id');
    }


    function collect(){


        return $this->hasMany(Collect::class,'category_id','id');
    }


    function collect_son(){


        return $this->hasMany(Collect::class,'son_id','id');
    }



}