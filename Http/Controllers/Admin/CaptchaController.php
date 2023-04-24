<?php

namespace Ycore\Http\Controllers\Admin;

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;

class CaptchaController extends BaseController
{

    function getCaptcha()
    {


        $captcha = new PhraseBuilder;

        $code = $captcha->build(4);

        $builder = new CaptchaBuilder($code, $captcha);

        //禁用所有效果
        $builder->setIgnoreAllEffects(false);
        //设置倾斜角度
        $builder->setMaxAngle(random_int(20, 50));

        $builder->build($width = 150, $height = 60, $font = null);

        //验证码
        $phrase = $builder->getPhrase();


        session()->put('captcha', $phrase);

        ob_start();

        $builder->output();

        $out = ob_get_clean();


        return response($out, 200, ['Content-Type' => "image/jpeg"]);

    }

}
