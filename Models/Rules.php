<?php

namespace Ycore\Models;

/**
 * App\Models\Rules
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $title 规则描述
 * @property string $rule 路由规则
 * @property string $group_name 分钟名称
 * @method static \Illuminate\Database\Eloquent\Builder|Rules newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rules newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rules query()
 * @method static \Illuminate\Database\Eloquent\Builder|Rules whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rules whereGroupName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rules whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rules whereRule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rules whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rules whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Rules extends Base
{

    protected $table = 'rules';

    protected $fillable = ['title', 'rule', 'group_name'];

}
