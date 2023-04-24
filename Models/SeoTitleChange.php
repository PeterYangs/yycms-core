<?php

namespace Ycore\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;

class SeoTitleChange extends Base
{

    protected $table = 'seo_title_change';


    protected $fillable = ['article_fields', 'category_item'];


    function categoryItem(): Attribute
    {

        return new Attribute(

            get: function ($value) {


                if (!$value) {

                    return [];
                }


                return json_decode($value, true, 512, JSON_THROW_ON_ERROR);

            },
            set: function ($value) {

                if (!$value) {


                    return "";
                }


                return json_encode($value, JSON_THROW_ON_ERROR);

            }


        );

    }


    function articleFields(): Attribute
    {

        return new Attribute(

            get: function ($value) {


                if (!$value) {

                    return [];
                }


                return explode(",", $value);

            },
            set: function ($value) {

                if (!$value) {


                    return "";
                }


                return implode(",", $value);

            }


        );

    }


}
