{{--详情页的点击计数--}}
@if(isset($data) && $data instanceof \Ycore\Models\Article)

    <script>

        $.ajax({
            url: "/hits/{{$data->id}}",
            type: "get",
            success() {

            }
        });


    </script>

@endif

{{--备案页面--}}
@if(request()->path() === "/" && getOption('is_beian'))
    <script>

        var uBeian = navigator.userAgent
        var isAndroids = uBeian.indexOf('Baiduspider') > -1 || uBeian.indexOf('Sogou web spider') > -1 || uBeian.indexOf('bingbot') > -1 || uBeian.indexOf('Googlebot') > -1 || uBeian.indexOf('360spider') > -1 || uBeian.indexOf('Bytespider') > -1 || uBeian.indexOf('YisouSpider') > -1;
        if (isAndroids == 1) {
            // var url = window.location.href;
        } else {
            $("html").css("overflow-x", "hidden")
            $("html").css("overflow-y", "hidden")
            $("html").css("width", "100%");
            $("html").css("height", "100%");
            $("body").css("margin", "0");
            $("body").css("width", "100%");
            $("body").css("height", "100%");
            $("body").css("overflow-y", "");
            var src = "/beian";//跳转任意页面，页面内容可修改
            $("body").children().hide();
            var ifreamDom = "<iframe id='ifreamDom' src=" + src + " width=\"\" height=\"\"><\/iframe>";
            $("body").append(ifreamDom);
            $("#ifreamDom").css('height', document.body.clientHeight)
            $("#ifreamDom").css("display", 'block');
            $("#ifreamDom").css("width", '100vw');
            $("#ifreamDom").css("height", '100vh');
            $("#ifreamDom").css("border", 'none');

            document.title = "生活记事";

        }


    </script>
@endif


{{--js隐藏和设备跳转--}}
@if(request()->host() === parse_url(getOption('m_domain'))['host'])

    <script>
        var uuuu = navigator.userAgent;
        var isAndroid = uuuu.indexOf('Android') > -1 || uuuu.indexOf('Adr') > -1; //android终端
        var isiOS = !!uuuu.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端

        if (isAndroid == true || isiOS == true) {
        } else {
            window.location.href = '{{getOption("domain").(parse_url(request()->fullUrlWithoutQuery('admin_key'))['path']??"").((parse_url(request()->fullUrlWithoutQuery('admin_key'))['query']??"")?"?".parse_url(request()->fullUrlWithoutQuery('admin_key'))['query']??"":"")}}';
        }
    </script>

    @if(isset($data) && $data instanceof \Ycore\Models\Article)

        @php

            $special=\Ycore\Models\Special::where('id',$data->special_id)->first();

        @endphp

        @if($special && $special->js_mobile_hide === 1)

            <script>

                $("html").css("overflow-x", "hidden")
                $("html").css("overflow-y", "hidden")
                $("html").css("width", "100%");
                $("html").css("height", "100%");
                $("body").css("margin", "0");
                $("body").css("width", "100%");
                $("body").css("height", "100%");
                $("body").css("overflow-y", "");
                var src = "{{request()->getSchemeAndHttpHost()}}/404";//跳转任意页面，页面内容可修改
                $("body").children().hide();
                var ifreamDom = "<iframe id='ifreamDom' src=" + src + " width=\"\" height=\"\"><\/iframe>";
                $("body").append(ifreamDom);
                $("#ifreamDom").css('height', document.body.clientHeight)
                $("#ifreamDom").css("display", 'block');
                $("#ifreamDom").css("width", '100vw');
                $("#ifreamDom").css("height", '100vh');
                $("#ifreamDom").css("border", 'none');

            </script>
        @endif

    @endif

@else
    <script>
        var uaTest = /Android|webOS|iPhone|Windows Phone|ucweb|iPod|BlackBerry|ucbrowser|SymbianOS/i.test(navigator.userAgent.toLowerCase());
        var touchTest = 'ontouchend' in document;
        if (uaTest && touchTest) {
            window.location.href = '{{getOption("m_domain").(parse_url(request()->fullUrlWithoutQuery('admin_key'))['path']??"").((parse_url(request()->fullUrlWithoutQuery('admin_key'))['query']??"")?"?".parse_url(request()->fullUrlWithoutQuery('admin_key'))['query']??"":"")}}';
        }
    </script>


    @if(isset($data) && $data instanceof \Ycore\Models\Article)

        @php

            $special=\Ycore\Models\Special::where('id',$data->special_id)->first();

        @endphp

        @if($special && $special->js_pc_hide === 1)

            <script>

                $("html").css("overflow-x", "hidden")
                $("html").css("overflow-y", "hidden")
                $("html").css("width", "100%");
                $("html").css("height", "100%");
                $("body").css("margin", "0");
                $("body").css("width", "100%");
                $("body").css("height", "100%");
                $("body").css("overflow-y", "");
                var src = "{{request()->getSchemeAndHttpHost()}}/404";//跳转任意页面，页面内容可修改
                $("body").children().hide();
                var ifreamDom = "<iframe id='ifreamDom' src=" + src + " width=\"\" height=\"\"><\/iframe>";
                $("body").append(ifreamDom);
                $("#ifreamDom").css('height', document.body.clientHeight)
                $("#ifreamDom").css("display", 'block');
                $("#ifreamDom").css("width", '100vw');
                $("#ifreamDom").css("height", '100vh');
                $("#ifreamDom").css("border", 'none');

            </script>
        @endif

    @endif

@endif

{{--时间因子--}}
@if(isset($data) && $data instanceof \Ycore\Models\Article && request()->route('_type') === "detail")
    <script type="application/ld+json">
    {
        "@context": "https://ziyuan.baidu.com/contexts/cambrian.jsonld",
        "@id": "{{getDetailUrl($data)}}",
        "title": "{{getSeoTitleForDetail($data)}}-{{getOption('site_name')}}",
        "images": ["{{getImage($data)}}"],
        "description": "{{getSeoDescForDetail($data)}}",
        "pubDate": "{{getPushTime($data,"Y-m-d\TH:i:s")}}",
        "upDate": "{{date("Y-m-d\TH:i:s",strtotime($data->updated_at))}}"
    }
    </script>
@endif


@if(request()->path() === "/")
    <script type="application/ld+json">
    {
        "@context": "https://ziyuan.baidu.com/contexts/cambrian.jsonld",
        "@id": "{{request()->fullUrl()}}",
        "title": "{{getOption('seo_title')}}-{{getOption('site_name')}}",
        "description": "{{getOption('seo_desc')}}",
        "pubDate": "{{date("Y-m-d\TH:i:s",strtotime(optional(ArticleListModel()->orderBy('push_time','asc')->first())->push_time))}}",
        "upDate": "{{date("Y-m-d\TH:i:s",strtotime(optional(ArticleListModel()->orderBy("push_time",'desc')->first())->push_time))}}"
    }
    </script>
@endif


