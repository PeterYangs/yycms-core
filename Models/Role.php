<?php

namespace Ycore\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * App\Models\Role
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $title 角色描述
 * @property string $rules 规则列表
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereRules($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Role extends Base
{

    protected $table = 'role';

    protected $fillable = ['title', 'rules'];


    protected function rules(): Attribute
    {
        return new Attribute(

            get: function ($value) {

                if ($value === "") {


                    return "";
                }

                return array_map(function ($v) {


                    return (int)$v;

                }, explode(",", $value));

            },
            set: function ($value) {

                if (!is_array($value)) {

                    return "";
                }

                return implode(',', $value);
            }

        );
    }


}
