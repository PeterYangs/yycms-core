<?php

namespace Ycore\Models;

class AccessKey extends Base
{

    protected $table = 'access_key';

    protected $fillable = ['app_id', 'app_secret', 'status', 'last_use'];

}
