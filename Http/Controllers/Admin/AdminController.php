<?php

namespace Ycore\Http\Controllers\Admin;

use Ycore\Models\Admin;
use Ycore\Models\Role;
use Ycore\Models\Rules;
use Ycore\Tool\Json;

class AdminController extends AuthCheckController
{


    /**
     * 管理员新增和修改
     * Create by Peter Yang
     * 2022-06-17 15:30:58
     */
    function update()
    {


        $post = request()->post();

        $va = [
//            'username' => ['required', 'string', 'min:3', 'unique:admin'],
//            'email' => ['required', 'email', 'unique:admin'],
            'nick_name' => ['required'],

        ];


        if (!$post['id']) {


            $va['password'] = ['required', 'string', 'min:6'];

            $va['repassword'] = ['required', 'string', 'min:6', 'same:password'];

            $va['username'] = ['required', 'string', 'min:3', 'unique:admin'];

            $va['email'] = ['required', 'email', 'unique:admin'];

        } else {

//            $va['password'] = ['string'];
//
//            $va['repassword'] = ['string', 'same:password'];


            $va['username'] = ['required', 'string', 'min:3'];

            $va['email'] = ['required', 'email'];


        }


        $data = \Validator::make($post, $va);


        if ($data->fails()) {


            return Json::code(2, $data->errors()->first());
        }


        if ($post['id']) {


            if (isset($post['username'])) {
                unset($post['username']);
            }

            //不修改密码
            if ($post['password'] === "" || $post['password'] === null) {


                unset($post['password']);
            }


        }


        Admin::updateOrCreate(['id' => $post['id']], $post);


        return Json::code(1, 'success', $post);


    }


    function list()
    {

        $list = Admin::with('role')->orderBy('id', 'desc');


        return Json::code(1, 'success', paginate($list, request()->input('p', 1)));

    }


    function detail()
    {

        $id = request()->input('id');

        $info = Admin::where('id', $id)->firstOrFail();


        return Json::code(1, 'success', $info);

    }


    function groupList()
    {


        $list = Rules::groupBy('group_name')->select('group_name')->get();


        $arr = [];

        foreach ($list as $value) {


            $arr[] = ['value' => $value->group_name];

        }

        return Json::code(1, 'success', $arr);

    }


    /**
     * Create by Peter Yang
     * 2022-06-20 15:58:30
     * @return string
     */
    function roleList()
    {


        return Json::code(1, 'success', Role::orderBy('id', 'desc')->get());
    }



    /**
     * @Auth(type='skip_auth')
     * 获取后台管理员信息
     * @return false|string
     */
    function info(){


        $user_info=resolve('adminInfo');



        $user_info['admin_key']=env('ADMIN_KEY');

        $user_info['site_name']=getOption("site_name","");

        return Json::code(1,'success',$user_info);


    }


    /**
     * @Auth(type='skip_auth')
     * 获取管理员列表
     */
    function getAdminList(){


        $list=Admin::all();

        return Json::code(1,'success',$list);

    }


}
