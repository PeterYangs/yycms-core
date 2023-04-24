<?php

namespace Ycore\Models;

/**
 * App\Models\Special
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $title 描述
 * @property int $js_pc_hide js pc端隐藏,1是 2否
 * @property int $js_mobile_hide js mobile端隐藏,1是 2否
 * @property int $real_pc_hide 真实 pc端隐藏,1是 2否
 * @property int $real_mobile_hide 真实 mobile端隐藏,1是 2否
 * @property int $list_pc_hide 列表 pc端隐藏,1是 2否
 * @property int $list_mobile_hide 列表 mobile端隐藏,1是 2否
 * @method static \Illuminate\Database\Eloquent\Builder|Special newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Special newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Special query()
 * @method static \Illuminate\Database\Eloquent\Builder|Special whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Special whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Special whereJsMobileHide($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Special whereJsPcHide($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Special whereListMobileHide($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Special whereListPcHide($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Special whereRealMobileHide($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Special whereRealPcHide($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Special whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Special whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Special extends Base
{

    protected $table = 'special';

    protected $fillable = [
        'title',
        'js_pc_hide',
        'js_mobile_hide',
        'real_pc_hide',
        'real_mobile_hide',
        'list_pc_hide',
        'list_mobile_hide',
        'list_pc_without_search_hide',
        'list_mobile_without_search_hide',
    ];

}
