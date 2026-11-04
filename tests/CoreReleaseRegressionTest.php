<?php

namespace Ycore\Tests;

use Illuminate\Container\Container;
use Illuminate\Config\Repository;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Illuminate\Console\OutputStyle;
use Ycore\Models\Article;
use Ycore\Scope\ArticleSpecialScope;
use Ycore\Tool\ArticleListQuery;

/** Isolated regressions: SQLite memory + fake HTTP + a private temporary disk, no app boot. */
class CoreReleaseRegressionTest extends TestCase
{
    private $app;
    private $db;
    private $root;
    private $logs;

    protected function setUp(): void
    {
        $_ENV['TOP_DOMAIN'] = 'test';
        $_ENV['ADMIN_KEY'] = 'regression-only-key';
        $this->root = sys_get_temp_dir() . '/yycms-core-test-' . bin2hex(random_bytes(8));
        $this->app = new Container();
        Container::setInstance($this->app);
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($this->app);
        $this->app->instance('config', new Repository(['filesystems' => ['default' => 'static', 'disks' => ['static' => ['driver' => 'local', 'root' => $this->root]]]]));
        $this->app->instance('request', Request::create('http://www.test/'));
        $this->app->instance('events', new Dispatcher($this->app));
        $this->app->instance('files', new Filesystem());
        $this->app->singleton('filesystem', fn () => new FilesystemManager($this->app));
        foreach (['Cache' => \Illuminate\Support\Facades\Cache::class, 'Http' => \Illuminate\Support\Facades\Http::class, 'Storage' => \Illuminate\Support\Facades\Storage::class,
                  'Log' => \Illuminate\Support\Facades\Log::class, 'Artisan' => \Illuminate\Support\Facades\Artisan::class] as $name => $class) {
            if (!class_exists($name)) { class_alias($class, $name); }
        }
        \Http::swap(new Factory());
        $this->logs = new class extends \Psr\Log\AbstractLogger {
            public $entries = [];
            public function log($level, $message, array $context = []): void { $this->entries[] = [$level, $message, $context]; }
        };
        \Log::swap($this->logs);
        $this->app->instance('option_domain', 'http://www.test');
        $this->app->instance('option_m_domain', 'http://m.test');
        $this->db = new Manager($this->app);
        $this->db->addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
        $this->db->setAsGlobal();
        $this->db->bootEloquent();
        Article::clearBootedModels();
        $schema = $this->db->schema();
        $schema->create('article', function ($t) {
            $t->increments('id'); $t->integer('category_id'); $t->integer('special_id')->default(0);
            $t->integer('status')->default(1); $t->integer('push_status')->default(1);
            $t->timestamp('deleted_at')->nullable(); $t->integer('issue_time');
            $t->text('content'); $t->text('expand')->nullable();
        });
        $schema->create('category', function ($t) { $t->increments('id'); $t->integer('pid')->default(0); $t->integer('status')->default(1); });
        $schema->create('special', function ($t) {
            $t->increments('id');
            foreach (['list_pc_hide','list_pc_without_search_hide','list_mobile_hide','list_mobile_without_search_hide','real_pc_hide','real_mobile_hide'] as $k) { $t->integer($k)->default(2); }
        });
        $schema->create('article_association_object', function ($t) { $t->integer('main'); $t->integer('slave'); $t->string('name'); });
        $this->db->table('category')->insert(['id' => 1, 'status' => 1]);
        $this->db->table('special')->insert(['id' => 1, 'list_pc_hide' => 1, 'list_pc_without_search_hide' => 1, 'real_pc_hide' => 2, 'real_mobile_hide' => 1]);
        foreach (range(1, 10) as $id) {
            $this->db->table('article')->insert(['id' => $id, 'category_id' => 1, 'issue_time' => $id,
                'content' => str_repeat('body', 1024), 'expand' => '[]', 'special_id' => $id === 10 ? 1 : 0,
                'status' => $id === 9 ? 2 : 1, 'push_status' => $id === 8 ? 2 : 1,
                'deleted_at' => $id === 7 ? '2026-01-01' : null]);
            $this->db->table('article_association_object')->insert(['main' => 100, 'slave' => $id, 'name' => 'association_object']);
        }
        Article::addGlobalScope(new ArticleSpecialScope());
    }

    protected function tearDown(): void
    {
        (new Filesystem())->deleteDirectory($this->root);
        \Mockery::close();
        Facade::clearResolvedInstances();
        Article::clearBootedModels();
        $this->db->getConnection()->disconnect();
    }

    /** @dataProvider visibilityCases */
    public function testAssociationVisibilityOrderingAndNarrowSelection($host, $agent, $direction, $expected): void
    {
        $request = Request::create('http://' . $host . '/');
        $request->headers->set('User-Agent', $agent);
        $this->app->instance('request', $request);
        $main = new Article(); $main->id = 100;
        $this->db->getConnection()->enableQueryLog();
        $items = \getExGameList($main, 3, '', 'issue_time', $direction);
        $queries = $this->db->getConnection()->getQueryLog();
        self::assertSame($expected, $items->modelKeys());
        self::assertStringContainsString('select "article"."id"', $queries[0]['query']);
        self::assertStringNotContainsString('select * from "article" ', $queries[0]['query']);
        foreach ($items as $item) {
            self::assertSame(str_repeat('body', 1024), $item->content);
            self::assertTrue($item->relationLoaded('category'));
            self::assertSame(1, $item->category->id);
        }
        // The full-field hydration query must not sort the large fields.
        self::assertStringNotContainsString('order by', $queries[1]['query']);
    }

    public function testVerifiedIpControlsActualArticleQueries(): void
    {
        $this->app['config']->set('yycms.spider_identity', 'verified_ip');
        // Same UA, opposite server decisions must produce opposite visibility.
        foreach (['0' => false, '1' => true] as $flag => $visible) {
            $request = Request::create('http://www.test/', 'GET', [], [], [], [
                'HTTP_USER_AGENT' => 'Googlebot', 'YYCMS_VERIFIED_SPIDER' => (string) $flag,
            ]);
            $this->app->instance('request', $request);
            self::assertSame($visible, \ArticleListModel()->where('id', 10)->exists());
            self::assertTrue(\ArticleListModel()->where('id', 6)->exists());
            self::assertFalse(\ArticleListModel()->where('id', 9)->exists());
        }
        $this->app['config']->set('yycms.spider_identity', 'ua');
    }

    public function visibilityCases(): array
    {
        return [['www.test', 'browser', 'desc', [6,5,4]], ['www.test', 'Baiduspider', 'desc', [10,6,5]],
            ['m.test', 'Baiduspider', 'desc', [6,5,4]], ['www.test', 'browser', 'asc', [1,2,3]]];
    }

    public function testUnlimitedEmptyAndOffsetLists(): void
    {
        $main = new Article(); $main->id = 100;
        self::assertSame([6,5,4,3,2,1], \getExGameList($main)->modelKeys());
        self::assertSame([], \getExGameList($main, 3, 'missing')->modelKeys());
        $query = \ArticleListModel()->orderBy('issue_time', 'desc')->offset(2)->limit(2);
        self::assertSame([4,3], ArticleListQuery::get($query)->modelKeys());
    }

    public function testRelatedTagListPreservesExclusionCategoryAndFullModels(): void
    {
        \Illuminate\Support\Facades\Cache::swap(new \Illuminate\Cache\Repository(new \Illuminate\Cache\ArrayStore()));
        $this->db->schema()->create('article_tag', function ($t) { $t->integer('article_id'); $t->integer('tag_id'); });
        foreach ([1,2,4,6,9] as $id) { $this->db->table('article_tag')->insert(['article_id'=>$id,'tag_id'=>1]); }
        $article = new Article(); $article->id = 6;
        $article->setRelation('article_tag', collect([(object) ['tag_id'=>1]]));
        $items = \getRelatedByTag($article, [1], 2);
        self::assertSame([4,2], $items->modelKeys());
        self::assertSame(str_repeat('body', 1024), $items->first()->content);
        self::assertTrue($items->first()->relationLoaded('category'));
        self::assertSame([], \getRelatedByTag($article, [99], 2)->modelKeys());
    }

    private function runHomepage(): int
    {
        $command = new \Ycore\Console\HomeStatic();
        $command->setLaravel($this->app);
        $command->setOutput(new OutputStyle(new ArrayInput([]), new BufferedOutput()));
        return $command->handle();
    }

    private function seedOldHomepages(): void
    {
        \Storage::disk('static')->put('pc/index.html', 'old-pc');
        \Storage::disk('static')->put('mobile/index.html', 'old-mobile');
    }

    public function testCompleteSmallHomepageAtomicReplacementAndMobileHeaders(): void
    {
        $this->seedOldHomepages();
        $html = '<html><body>Small valid theme</body></html>';
        \Http::fake(['*' => \Http::response($html, 200, ['Content-Type' => 'text/html; charset=utf-8'])]);
        self::assertSame(0, $this->runHomepage());
        foreach (['pc','mobile'] as $mode) {
            self::assertSame($html, \Storage::disk('static')->get($mode . '/index.html'));
            self::assertSame([], glob($this->root . '/' . $mode . '/.homepage-*'));
        }
        \Http::assertSent(fn ($r) => str_contains($r->url(), 'm.test') && str_contains($r->header('User-Agent')[0], 'iPhone') && $r['admin_key'] === 'regression-only-key');
    }

    /** @dataProvider badResponses */
    public function testBadResponsesKeepExistingFiles($body, $status, $headers): void
    {
        $this->seedOldHomepages();
        \Http::fake(['*' => \Http::response($body, $status, $headers)]);
        self::assertSame(1, $this->runHomepage());
        self::assertSame('old-pc', \Storage::disk('static')->get('pc/index.html'));
        self::assertSame('old-mobile', \Storage::disk('static')->get('mobile/index.html'));
        self::assertStringNotContainsString('regression-only-key', json_encode($this->logs->entries));
    }

    public function badResponses(): array
    {
        $html = '<html><body>complete</body></html>';
        return [[$html,500,['Content-Type'=>'text/html']], ['<html><body>partial',200,['Content-Type'=>'text/html']],
            [$html,200,['Content-Type'=>'application/json']], [$html,200,['Content-Type'=>'text/html','is_static'=>'1']]];
    }

    public function testTimeoutPreservesOldFileAndStillAttemptsMobile(): void
    {
        $this->seedOldHomepages();
        \Http::fake(function ($r) {
            if (str_contains($r->url(), 'www.test')) { throw new \Illuminate\Http\Client\ConnectionException('cURL error 28: http://www.test/?admin_key=regression-only-key'); }
            return \Http::response('<html><body>mobile</body></html>', 200, ['Content-Type'=>'text/html']);
        });
        self::assertSame(1, $this->runHomepage());
        self::assertSame('old-pc', \Storage::disk('static')->get('pc/index.html'));
        self::assertStringContainsString('mobile</body>', \Storage::disk('static')->get('mobile/index.html'));
        self::assertStringNotContainsString('regression-only-key', json_encode($this->logs->entries));
    }

    public function testPcOnlyAndJobFailurePropagation(): void
    {
        $this->app->instance('option_m_domain', '');
        \Http::fake(['*' => \Http::response('<html><body>PC</body></html>', 200, ['Content-Type'=>'text/html'])]);
        self::assertSame(0, $this->runHomepage());
        \Http::assertSentCount(1);
        $kernel = \Mockery::mock(\Illuminate\Contracts\Console\Kernel::class);
        $kernel->shouldReceive('call')->once()->with('HomeStatic')->andReturn(1);
        \Artisan::swap($kernel);
        $this->expectException(\RuntimeException::class);
        (new \Ycore\Jobs\HomeStatic())->handle();
    }
}
