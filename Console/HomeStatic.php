<?php

namespace Ycore\Console;

use Illuminate\Console\Command;

class HomeStatic extends Command
{
    protected $signature = 'HomeStatic';
    protected $description = '主页静态化';

    public function handle()
    {
        $failed = false;
        foreach (['pc' => 'domain', 'mobile' => 'm_domain'] as $mode => $option) {
            $temporary = null;
            $host = '';
            try {
                $domain = rtrim((string) getOption($option, ''), '/');
                // PC-only installations need not configure a mobile domain.
                if ($mode === 'mobile' && $domain === '') {
                    continue;
                }
                $key = (string) env('ADMIN_KEY');
                $host = (string) parse_url($domain, PHP_URL_HOST);
                if ($host === '' || $key === '' || !in_array(parse_url($domain, PHP_URL_SCHEME), ['http', 'https'], true)) {
                    throw new \RuntimeException('Homepage configuration incomplete');
                }
                $headers = $mode === 'mobile'
                    ? ['User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 Version/13.0.3 Mobile/15E148 Safari/604.1']
                    : [];
                $response = \Http::withHeaders($headers)->connectTimeout(5)->timeout(30)
                    ->get($domain . '/', ['admin_key' => $key])->throw();
                $body = $response->body();
                // Small but complete themes are valid too; do not impose a byte-size threshold.
                if ($response->status() !== 200
                    || !preg_match('/<html\b[^>]*>.*<body\b[^>]*>.*<\/body\s*>.*<\/html\s*>/is', $body)
                    || stripos((string) $response->header('Content-Type'), 'text/html') === false
                    || (string) $response->header('is_static') === '1') {
                    throw new \RuntimeException('Homepage response validation failed');
                }
                $disk = \Storage::disk('static');
                if (!$disk->makeDirectory($mode)) {
                    throw new \RuntimeException('Homepage directory creation failed');
                }
                $destination = $disk->path($mode . '/index.html');
                // Same-directory rename keeps the previous complete homepage until commit.
                $temporary = tempnam(dirname($destination), '.homepage-');
                if ($temporary === false || file_put_contents($temporary, $body, LOCK_EX) !== strlen($body)
                    || !chmod($temporary, 0644) || !rename($temporary, $destination)) {
                    throw new \RuntimeException('Homepage atomic write failed');
                }
                $temporary = null;
                \Log::info('homepage_static_success', ['mode' => $mode, 'host' => $host, 'bytes' => strlen($body)]);
                $this->info('Homepage static generated: ' . $mode);
            } catch (\Throwable $e) {
                $failed = true;
                preg_match('/cURL error (\d+)/', $e->getMessage(), $curl);
                // HTTP exceptions contain URLs/admin_key: never log their raw message.
                \Log::error('homepage_static_failed', ['mode' => $mode, 'host' => $host,
                    'exception' => get_class($e), 'curl_errno' => isset($curl[1]) ? (int) $curl[1] : null]);
                $this->error('Homepage static failed: ' . $mode . '; see sanitized application log.');
            } finally {
                if (is_string($temporary) && is_file($temporary)) {
                    unlink($temporary);
                }
            }
        }
        return $failed ? 1 : 0;
    }
}
