<?php

namespace Ycore\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;


/**
 * App\Models\Admin
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $username 用户名
 * @property string $email 邮箱
 * @property string $password 密码
 * @property string $nick_name 昵称
 * @property int $status 状态，1是正常，2是禁用
 * @method static \Illuminate\Database\Eloquent\Builder|Admin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Admin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Admin query()
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereNickName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereUsername($value)
 * @mixin \Eloquent
 * @property int $role_id 角色id
 * @property-read \App\Models\Role|null $role
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereRoleId($value)
 */
class Admin extends Base
{


    protected $table = 'admin';

    protected $fillable = ['username', 'email', 'password', 'nick_name', 'status','role_id'];

    protected $hidden = ['password'];


    protected function password(): Attribute
    {

        return new Attribute(

            set: function ($value) {


                return \Hash::make($value);
            }
        );

    }


    function role(){


        return $this->belongsTo(Role::class,'role_id','id');
    }


}
