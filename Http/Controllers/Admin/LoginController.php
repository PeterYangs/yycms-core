<?php

namespace Ycore\Http\Controllers\Admin;


use App\Tool\Json;
use Ycore\Models\Admin;

class LoginController extends BaseController
{

    /**
     * Create by Peter Yang
     * 2022-06-20 16:27:26
     * @return string
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    function login()
    {


        try {

            $post = request()->input();

            $username = $post['username'] ?? null;

            $password = $post['password'] ?? null;

            //验证码
            $captcha = $post['captcha'] ?? null;


            $realCaptcha = session()->get('captcha');


            if (strtolower($captcha) !== strtolower($realCaptcha)) {

                return Json::code(2, '验证码错误！', session()->get('captcha'));
            }

            $re = Admin::where('username', $username)->first();


            if (!$re) {


                return Json::code(2, '未找到该用户！');
            }

            if ($re->status === 2) {


                return Json::code(2, '该账号已被禁用，请联系管理员！');
            }

            $realPassword = $re->password;


            if (!\Hash::check($password, $realPassword)) {


                return Json::code(2, '密码错误！');

            }

            session()->put('admin_id', $re->id);

            return Json::code(1, '登录成功！', session()->get('captcha'));


        } finally {


            session()->forget('captcha');

        }


    }

}
