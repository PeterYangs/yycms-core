<?php

namespace Ycore\Models;

class ErrorAccess extends Base
{

    protected $table = 'error_access';

    protected $fillable = ['ip', 'url', 'referer', 'query', 'agent'];

}
