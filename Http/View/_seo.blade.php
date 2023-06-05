@if(request()->route('_type') === "channel" && isset($category) && $category instanceof \Ycore\Models\Category)
    <title>{{getSeoTitleForChannel($category)}}</title>
    <meta name="keywords" content="{{getSeoKeywordForChannel($category)}}"/>
    <meta name="description" content="{{getSeoDescForChannel($category)}}"/>
@elseif(request()->route('_type') === "detail" && isset($data) && $data instanceof \Ycore\Models\Article)
    <title>{{getSeoTitleForDetail($data)}}</title>
    <meta name="keywords" content="{{getSeoKeywordForDetail($data)}}"/>
    <meta name="description" content="{{getSeoDescForDetail($data)}}"/>
@else
    @if(isset($_seo_title)&&$_seo_title!=="")<title>{{$_seo_title}}</title>@else<title>{{getOption('seo_title')}}</title>@endif

    @if(isset($_seo_keyword)&&$_seo_keyword!=="")<meta name="keywords" content="{{$_seo_keyword}}"/>@else<meta name="keywords" content="{{getOption('seo_keyword')}}"/>@endif

    @if(isset($_seo_desc)&&$_seo_desc!=="")<meta name="description" content="{{$_seo_desc}}"/>@else<meta name="description" content="{{getOption('seo_desc')}}"/>@endif

@endif
