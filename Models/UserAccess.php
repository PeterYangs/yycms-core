<?php

namespace Ycore\Models;

/**
 * App\Models\UserAccess
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $ip ip
 * @property string $url 访问完整链接
 * @property string|null $referer 跳转来源
 * @property string|null $query query数据
 * @property string|null $agent 设备
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccess newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccess newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccess query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccess whereAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccess whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccess whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccess whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccess whereQuery($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccess whereReferer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccess whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAccess whereUrl($value)
 * @mixin \Eloquent
 */
class UserAccess extends Base
{

    protected $table = 'user_access';

    protected $fillable = ['ip', 'url', 'referer', 'query','agent'];

}
