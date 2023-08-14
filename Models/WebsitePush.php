<?php

namespace Ycore\Models;

class WebsitePush extends Base
{

    protected $table = 'website_push';

    protected $fillable = ['article_id', 'link', 'spider', 'platform','msg'];


}
