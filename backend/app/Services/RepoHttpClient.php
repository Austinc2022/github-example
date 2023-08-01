<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
class RepoHttpClient
{
    protected $httpClient;
    protected const ENDPOINT  = 'search/repositories';
    protected const BASEURL   = 'https://api.github.com';

    public function __construct()
    {
        $this->httpClient = Http::baseUrl(self::BASEURL)
            ->withHeaders([
                // 'CJ-Access-Token' => config('constants.CJDROPSHIP_TOKEN')
                'X-GitHub-Api-Version' => '2022-11-28',
                'Accept' => 'application/vnd.github+json',
            ])
            ->timeout(10);
    }

    public function getReposByTopic(int $page = 1, int $perPage = 100, string $topic = 'php')
    {
        $response = $this->httpClient->get(self::ENDPOINT, [
            'q' => 'topic:' . $topic,
            'page' => $page,
            'per_page' => $perPage,
            'sort' => 'stars',
            'order' => 'desc'
        ]);
        if ($response->ok()) {
            return $response->collect()['items'];
        }
    }

}
