<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2022/6/24
 * Time: 15:59
 */

namespace Ycore\Models;


/**
 * App\Models\SiteSetting
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $site_name 网站名称
 * @property string $ipc ICP备
 * @property string $public 公网安备
 * @property string $email 站长邮箱
 * @property string $code 统计代码
 * @property string $domain 网站pc端域名
 * @property string $m_domain 网站移动端域名
 * @property string $sm_token 神马token
 * @property string $pc_token 百度pc推送token
 * @property string $seo_title SEO标题
 * @property string $seo_keyword SEO关键字
 * @property string $seo_desc SEO描述
 * @property int $is_beian 是否开启备案，1开启，0关闭
 * @method static \Illuminate\Database\Eloquent\Builder|SiteSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SiteSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SiteSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|SiteSetting whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteSetting whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteSetting whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteSetting whereIpc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteSetting whereIsBeian($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteSetting whereMDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteSetting wherePcToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteSetting wherePublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteSetting whereSeoDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteSetting whereSeoKeyword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteSetting whereSeoTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteSetting whereSiteName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteSetting whereSmToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteSetting whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SiteSetting extends Base
{
    protected $table = 'site_setting';
    protected $fillable = [
        'site_name',
        'ipc',
        'public',
        'email',
        'code',
        'domain',
        'm_domain',
        'sm_token',
        'pc_token',
        'seo_title',
        'seo_keyword',
        'seo_desc',
        'is_beian'

    ];

}
