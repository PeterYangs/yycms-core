<?php

namespace Ycore\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Base
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Base newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Base newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Base query()
 * @mixin \Eloquent
 */
class Base extends Model
{


    /**
     * Prepare a date for array / JSON serialization.
     */
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

}
