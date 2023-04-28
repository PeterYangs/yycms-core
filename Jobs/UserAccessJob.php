<?php

namespace Ycore\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UserAccessJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $ip;

    public string $url;

    public string $referer;

    public string $query;

    public string $agent;

    /**
     * @param string $ip
     * @param string $url
     * @param string $referer
     * @param string $query
     * @param string $agent
     */
    public function __construct(string $ip, string $url, string $referer, string $query, string $agent)
    {
        $this->ip = $ip;
        $this->url = $url;
        $this->referer = $referer;
        $this->query = $query;
        $this->agent = $agent;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //

        \Ycore\Models\UserAccess::create([
            'ip' => $this->ip,
            'url' => $this->url,
            'referer' => $this->referer,
            'query' => $this->query,
            'agent' => $this->agent,
        ]);
    }
}
