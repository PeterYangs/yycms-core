<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2022/6/24
 * Time: 16:03
 */

namespace Ycore\Http\Controllers\Admin;


use App\Tool\Json;
use Illuminate\Http\Request;

class SiteSettingController extends AuthCheckController
{

    /**
     * 网站设置
     * Create by Peter Yang
     * 2023-01-30 16:23:24
     * @param Request $request
     * @return string
     * @throws \JsonException
     */
    public function settingUpdate(Request $request)
    {
        $post = $request->input();
////        dd($post);
//        $id = $post['id'] ?? null;
//
//        $bool = SiteSetting::updateOrCreate(['id' => $id], $post);
//        if ($bool) {
//            return Json::code(1, 'success');
//        }


        foreach ($post as $key => $value) {


            setOption($key, $value, true);

        }


        return Json::code(1, 'success', $post);

//        return Json::code(2, '失败');
    }

    /**
     * @return string
     * Notes:网站设置 展示
     * User: Zy
     * Date: 2022/6/24 16:58
     */
    public function getSetting()
    {

        $list = [
            'code',
            'domain',
            'email',
            'icp',
            'is_beian',
            'm_domain',
            'pc_token',
            'public',
            'seo_desc',
            'seo_keyword',
            'seo_title',
            'site_name',
            'sm_token',
        ];

//        $data = SiteSetting::first();

        $data = [];

        foreach ($list as $value) {


            $data[$value] = getOption($value,"");

        }

        return Json::code(1, 'success', $data);
    }


    function setBeian()
    {


        $is_beian = \request()->input('is_beian', 0);



        setOption('is_beian',$is_beian);


        \Artisan::call('HomeStatic');


        return Json::code(1, 'success');

    }


}
