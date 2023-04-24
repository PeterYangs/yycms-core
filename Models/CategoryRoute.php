<?php

namespace Ycore\Models;

/**
 * App\Models\CategoryRoute
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $category_id 分类id
 * @property string $title 描述
 * @property string $route 路由
 * @property string $controller 控制器
 * @property string $action 方法
 * @property string $alias 路由别名
 * @property string|null $tag 标签
 * @property int $type 路由类型，1pc,2mobile
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryRoute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryRoute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryRoute query()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryRoute whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryRoute whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryRoute whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryRoute whereController($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryRoute whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryRoute whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryRoute whereRoute($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryRoute whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryRoute whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryRoute whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryRoute whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CategoryRoute extends Base
{

    protected $table = 'category_route';

    protected $fillable = ['title', 'route', 'controller', 'action', 'alias', 'tag', 'type', 'category_id'];

}
