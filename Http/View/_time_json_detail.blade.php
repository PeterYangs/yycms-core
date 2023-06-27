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
