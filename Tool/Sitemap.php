<?php

namespace Ycore\Tool;

class Sitemap
{


    /**
     * 获取网站地图列表
     * @return array[]
     */
    public static function getSitemapList()
    {

        $pc = [];

        $mobile = [];

        foreach (\File::files(storage_path('sitemap/pc')) as $file) {

            $pc[] = ['url' => getOption('domain') . "/sitemap/" . $file->getBasename(), 'time' => date("Y-m-d H:i", $file->getFileInfo()->getMTime())];
        }


        foreach (\File::files(storage_path('sitemap/mobile')) as $file) {

            $mobile[] = ['url' => getOption('m_domain') . "/sitemap/" . $file->getBasename(), 'time' => date("Y-m-d H:i", $file->getFileInfo()->getMTime())];
        }

        return ['pc' => $pc, 'mobile' => $mobile];
    }


}
