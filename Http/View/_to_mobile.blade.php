<script>
    var uaTest = /Android|webOS|iPhone|Windows Phone|ucweb|iPod|BlackBerry|ucbrowser|SymbianOS/i.test(navigator.userAgent.toLowerCase());
    var touchTest = 'ontouchend' in document;
    if (uaTest && touchTest) {
        window.location.href = '{{getOption("m_domain").(parse_url(request()->fullUrlWithoutQuery('admin_key'))['path']??"").((parse_url(request()->fullUrlWithoutQuery('admin_key'))['query']??"")?"?".parse_url(request()->fullUrlWithoutQuery('admin_key'))['query']??"":"")}}';
    }
</script>
