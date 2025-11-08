<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2022/6/24
 * Time: 16:03
 */

namespace Ycore\Http\Controllers\Admin;


use Illuminate\Support\Facades\Artisan;
use Ycore\Tool\Json;
use Illuminate\Http\Request;
use Ycore\Tool\Mail;

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


        foreach ($post as $key => $value) {


            setOption($key, $value, true);

        }


        return Json::code(1, 'success', $post);

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
            'bing_token',
            'mail_host',
            'mail_username',
            'mail_password',
            'mail_port',
            'notice_mail',
            'open_watermark',
            'watermark',
            'statistics_pc',
            'statistics_mobile',
            'disable_device_jump',
            'enable_channel_domain',
            'disable_content_link',
            'icp_province',
            'disable_mobile'

        ];


        $data = [];

        foreach ($list as $value) {


            $data[$value] = getOption($value, "");

        }

        return Json::code(1, 'success', $data);
    }


    function setBeian()
    {


        $is_beian = \request()->input('is_beian', 0);


        setOption('is_beian', $is_beian);


        \Artisan::call('HomeStatic');


        return Json::code(1, 'success');

    }


    function themeList()
    {


        return Json::code(1, "success", themeList());
    }


    function switchTheme()
    {

        $theme = \request()->input('theme');

        if (!$theme) {

            return Json::code(2, "请填写主题！");
        }

        $themes = themeList();

        if (!in_array($theme, $themes)) {

            return Json::code(2, "该主题不存在！");
        }

        Artisan::call("SwitchTheme " . $theme);


        return Json::code(1, 'success');

    }


    function theme()
    {


        return Json::code(1, 'success', getOption('theme', 'demo'));
    }


    /**
     * 发布当前样式
     * @return string
     * @throws \JsonException
     */
    function pushAsset()
    {

        try {

            Artisan::call("PushAsset");

        } catch (\Exception $exception) {

            return Json::code(2, $exception->getMessage());

        }


        return Json::code(1, "success", "PushAsset " . getOption("theme", "demo"));

    }


    function sendTestMail()
    {

        try {

            Mail::send([getOption('notice_mail')], '测试邮件', '<h1>测试内容</h1>');

        } catch (\Exception $exception) {


            return Json::code(2, 'success', $exception->getMessage());
        }


        return Json::code(1, 'success');


    }


}
