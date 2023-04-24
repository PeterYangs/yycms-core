<?php

namespace Ycore\Models;

/**
 * App\Models\StoreApk
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $title 游戏包名称
 * @property string $url 链接
 * @method static \Illuminate\Database\Eloquent\Builder|StoreApk newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StoreApk newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StoreApk query()
 * @method static \Illuminate\Database\Eloquent\Builder|StoreApk whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoreApk whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoreApk whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoreApk whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoreApk whereUrl($value)
 * @mixin \Eloquent
 */
class StoreApk extends Base
{

    protected $table = 'store_apk';

    protected $fillable = ['title', 'url'];

}
