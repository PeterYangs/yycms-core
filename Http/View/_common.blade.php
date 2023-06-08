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


@if(request()->host() === parse_url(getOption('m_domain'))['host'])

    <script>
        var uuuu = navigator.userAgent;
        var isAndroid = uuuu.indexOf('Android') > -1 || uuuu.indexOf('Adr') > -1; //android终端
        var isiOS = !!uuuu.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端

        if (isAndroid == true || isiOS == true) {
        } else {
            window.location.href = '{{getOption("domain").request()->getRequestUri()}}';
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
            window.location.href = '{{getOption("m_domain").request()->getRequestUri()}}';
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


