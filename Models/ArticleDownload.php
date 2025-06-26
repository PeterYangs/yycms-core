<?php

namespace Ycore\Models;

class ArticleDownload extends Base
{

    protected $table = 'article_download';

    protected $fillable = [
        'article_id',
        'library_id',
        'apk_id',
        'download_site_id',
        'file_path',
        'save_type',
        'pan_password'
    ];

    function download_site()
    {

        return $this->belongsTo(DownloadSite::class, 'download_site_id', 'id');
    }


    function article()
    {
        return $this->belongsTo(Article::class, 'article_id', 'id');
    }

}
