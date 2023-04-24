<?php

namespace Ycore\Models;

class Collect extends Base
{


    protected $table = 'collect';

    protected $fillable = ['category_id', 'son_id'];


    function category()
    {


        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    function son(){



        return $this->belongsTo(Category::class,'son_id','id');
    }

}
