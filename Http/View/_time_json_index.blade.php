<script type="application/ld+json">
    {
        "@context": "https://ziyuan.baidu.com/contexts/cambrian.jsonld",
        "@id": "{{request()->fullUrlWithoutQuery('admin_key')}}",
        "title": "{{getOption('seo_title')}}-{{getOption('site_name')}}",
        "description": "{{getOption('seo_desc')}}",
        "pubDate": "{{date("Y-m-d\TH:i:s",strtotime(optional(ArticleListModel()->orderBy('push_time','asc')->first())->push_time))}}",
        "upDate": "{{date("Y-m-d\TH:i:s",strtotime(optional(ArticleListModel()->orderBy("push_time",'desc')->first())->push_time))}}"
    }
</script>
