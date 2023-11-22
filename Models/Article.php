<?php

namespace Ycore\Models;

use Ycore\Events\ArticleDestroy;
use Ycore\Events\ArticleUpdate;
use Ycore\Scope\ArticleScope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Ycore\Models\Article
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $category_id 分类id
 * @property string|null $push_time 发布时间
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $content 内容
 * @property string $img 主图
 * @property string $title 标题
 * @property string|null $expand 拓展数据，json
 * @property string $seo_title seo标题
 * @property string $seo_desc seo描述
 * @property string $seo_keyword seo关键字
 * @property int $hits 点击量
 * @property \Ycore\Models\Admin|null $admin_id_create 创建编辑
 * @property \Ycore\Models\Admin|null $admin_id_update 最后修改编辑
 * @property int $status 发布状态,1是发布，2是下架，默认发布
 * @property int $push_status 自动发布状态，1为已发布，2为定时发布，3为自动发布
 * @property-read \Ycore\Models\Category|null $category
 * @method static \Illuminate\Database\Eloquent\Builder|Article newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Article newQuery()
 * @method static \Illuminate\Database\Query\Builder|Article onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Article query()
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereAdminIdCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereAdminIdUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereExpand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereHits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article wherePushStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article wherePushTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereSeoDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereSeoKeyword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereSeoTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Article withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Article withoutTrashed()
 * @mixin \Eloquent
 * @property int $special_id 特殊属性id
 * @property int $select_order 自定义排序,不影响前台
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ycore\Models\ArticleTag[] $article_tag
 * @property-read int|null $article_tag_count
 * @property-read \Ycore\Models\Special|null $special
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereSelectOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereSpecialId($value)
 */
class Article extends Base
{


    use SoftDeletes;

    protected $table = 'article';

    protected $fillable = [
        'category_id',
        'push_time',
        'content',
        'img',
        'title',
        'expand',
        'seo_title',
        'seo_desc',
        'seo_keyword',
        'hits',
        'admin_id_create',
        'admin_id_update',
        'status',
        'push_status',
        'special_id',
        'select_order',
        'updated_at'
    ];

    protected $appends = ['ex'];

    static function booted()
    {

        //正常文章状态
        static::addGlobalScope(new ArticleScope);


        static::deleted(function ($article) {

            event(new ArticleDestroy($article->id));

        });


        static::updated(function (Article $article) {


            //变更状态后进行相关操作
            if ($article->isDirty('status')) {

                //下架删除静态文件
                if ($article->status === 2) {

                    event(new ArticleDestroy($article->id));

                }


                //重新上架重新生成静态
                if ($article->status === 1) {


                    event(new ArticleUpdate($article->id));

                }


            }


        });


    }


    function article_download()
    {

        return $this->hasOne(ArticleDownload::class, 'article_id', 'id');
    }


    /**
     * 获取下载地址
     * @return Attribute
     */
    function downloadUrl(): Attribute
    {


        return new Attribute(
            get: function ($value, $data) {

                $articleDownload = $this->article_download;

                if (!$articleDownload) {

                    return "";
                }

                $file_path = $articleDownload->file_path;

                $download_site = $articleDownload->download_site;

                if (!$download_site) {

                    return "";
                }

                return str_replace("{path}", $file_path, $download_site->rule);

            }
        );
    }


    /**
     * Create by Peter Yang
     * 2022-06-23 14:02:04
     * @return Attribute
     */
    function expand(): Attribute
    {

        return new Attribute(

            get: function ($value) {


                if (!$value) {

                    return [];
                }


                $list = json_decode($value, true, 512, JSON_THROW_ON_ERROR);


                //一对多长度截取(防止关联长度过大，显示出问题)
                foreach ($list as $k => $item) {

                    if ($item['type'] === 7) {


                        $list[$k]["value"] = array_slice($item['value'], 0, config('admin.show_expand_max_length'));

                    }

                }


                return $list;

            },
            set: function ($value) {

                if (!$value) {


                    return "";
                }


                return json_encode($value, JSON_THROW_ON_ERROR);

            }


        );

    }

    function category()
    {


        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    function admin_id_create()
    {


        return $this->belongsTo(Admin::class, 'admin_id_create', 'id');
    }

    function admin_id_update()
    {

        return $this->belongsTo(Admin::class, 'admin_id_update', 'id');
    }


    function article_tag()
    {


        return $this->hasMany(ArticleTag::class, 'article_id', 'id')->with('tag');
    }


    function article_tag_only()
    {

        return $this->hasMany(ArticleTag::class, 'article_id', 'id');
    }


    function special()
    {


        return $this->belongsTo(Special::class, 'special_id', 'id');
    }


    function ex(): Attribute
    {


        return new Attribute(

            get: function ($value, $attrs) {


                $expand = $attrs['expand'] ?? null;


                return $this->getEx($expand);

            }


        );

    }

    function getEx($ex): array
    {

        $expand = $ex ?? null;


        if (!$expand) {

            return [];

        }

        $expand = json_decode($expand, true, 512, JSON_THROW_ON_ERROR);


        $arr = [];


        foreach ($expand as $k => $v) {


            $arr[$v['name']] = $v['value'];
        }


        return $arr;

    }

    function specialEx(): Attribute
    {

        return new Attribute(

            get: function ($value, $attrs) {

                $expand = $this->getEx($attrs['expand']);

                if ($attrs['special_id'] !== 0) {


                    $category = Category::where('id', $attrs['category_id'])->first();

                    if (!$category) {


                        return [];
                    }

                    $ex = ExpandChange::where('special_id', $attrs['special_id'])->whereIn('category_id',
                        [$category->id, $category->pid])->first();


                    if ($ex && $expand) {


                        $temp_ex = $expand;

                        foreach ($temp_ex as $k => $v) {


                            foreach ($ex->detail as $k1 => $v1) {


                                if ($k === $v1["field"] && $v1["value"]) {


                                    $temp_ex[$k] = $v1["value"];

                                }

                            }


                        }


                        return $temp_ex;

                    }


                }


                return $expand;

            }

        );

    }


    /**
     * 判断是否应该显示所属文章块
     * @return Attribute
     */
    function hasCollect(): Attribute
    {

        return new Attribute(

            get: function ($value, $attrs) {

                $category_arr = [];

                $category_id = $attrs['category_id'];

                $category_arr[] = $category_id;


                $p_id = Category::where('id', $category_id)->first();


                if ($p_id) {

                    $category_arr[] = $p_id->pid;
                }


                $re = Collect::whereIn('son_id', $category_arr)->get();

                return $re->count() > 0;

            }

        );

    }


    /**
     * 当前文章关联的子文章
     * Create by Peter Yang
     * 2022-12-09 09:49:25
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    function collect_num()
    {


        return $this->hasMany(ArticleAssociationObject::class, 'main', 'id');
    }


    function collect_name()
    {


        return $this->hasMany(ArticleAssociationObject::class, 'slave', 'id');
    }


}



