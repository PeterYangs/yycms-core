<?php
namespace Ycore\Tests;

use Illuminate\Container\Container;
use Illuminate\Config\Repository;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Ycore\Tool\SpiderIdentity;
use Ycore\Http\Middleware\home\StaticRender;

class SpiderIdentityTest extends TestCase
{
    private $app;
    protected function setUp(): void
    {
        $this->app = new Container();
        Container::setInstance($this->app);
        $this->app->instance('config', new Repository());
    }

    /** @dataProvider cases */
    public function testTrustedServerParameter($mode, $server, $expected): void
    {
        if ($mode !== null) { $this->app['config']->set('yycms.spider_identity', $mode); }
        $request = Request::create('https://www.test/', 'GET', [], [], [], $server);
        self::assertSame($expected, SpiderIdentity::isSpider($request));
    }
    public function cases(): array
    {
        return [
            [null, ['HTTP_USER_AGENT'=>'Googlebot'], true],
            ['ua', ['HTTP_USER_AGENT'=>'Mozilla','YYCMS_VERIFIED_SPIDER'=>'1'], false],
            ['verified_ip', ['HTTP_USER_AGENT'=>'Googlebot'], false],
            ['verified_ip', ['HTTP_USER_AGENT'=>'Googlebot','YYCMS_VERIFIED_SPIDER'=>'0'], false],
            ['verified_ip', ['HTTP_USER_AGENT'=>'Mozilla','YYCMS_VERIFIED_SPIDER'=>'1'], true],
            ['verified_ip', ['HTTP_YYCMS_VERIFIED_SPIDER'=>'1','HTTP_X_FORWARDED_FOR'=>'66.249.66.1'], false],
            ['verified_ip', ['YYCMS_VERIFIED_SPIDER'=>'true'], false],
            ['verified_ip', ['YYCMS_VERIFIED_SPIDER'=>'01'], false],
            ['verified_ip', ['YYCMS_VERIFIED_SPIDER'=>1], false],
            ['verified_ip_typo', ['YYCMS_VERIFIED_SPIDER'=>'1','HTTP_USER_AGENT'=>'Googlebot'], false],
        ];
    }

    public function testIpModeNeverServesLegacyStaticFiles(): void
    {
        $this->app['config']->set('static.open', true);
        $this->app['config']->set('yycms.spider_identity', 'verified_ip');
        $request=Request::create('https://www.test/');
        // No filesystem binding: attempting to read an old file would fail this test.
        self::assertSame('rendered', (new StaticRender())->handle($request, fn()=> 'rendered'));
    }
}
