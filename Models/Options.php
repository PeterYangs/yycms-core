<?php

namespace Ycore\Models;

class Options extends Base
{


    protected $table = 'options';

    protected $fillable = ['key', 'value', 'type', 'autoload'];


}
