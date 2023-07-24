<?php

namespace Ycore\Models;

class ExpandData extends Base
{

    protected $table = 'expand_data';

    protected $fillable = ['article_id', 'article_expand_detail_id', 'article_expand_id', 'name', 'desc', 'type', 'select_list', 'model_name', 'label', 'condition', 'default_condition', 'show_field', 'value'];

}
