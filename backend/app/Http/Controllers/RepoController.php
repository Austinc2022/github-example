<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Sleep;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RepoController extends Controller
{
    public function index(Request $request)
    {
        $repos = $this->getCachedData();
        $repos = $this->applyQuicksearch($request, $repos);
        $repos = $this->applySorting($request, $repos);

        return [
            'data' => collect($repos)->skip($request->skip)->take($request->take)->toArray(),
            'total' => count($repos)
        ];
    }

    private function getCachedData(): array
    {
        $repos = [];
        if (Storage::disk('local')->exists('repos.json')){
            $repos = Storage::json('repos.json');
        }else{
            $repos = $this->getRepos(500);
            $jsonData = json_encode($repos, JSON_PRETTY_PRINT);
            Storage::disk('local')->put('repos.json', $jsonData);
        }
        return $repos;
    }

    private function getRepos(int $num): array
    {
        $pages = ($num % 100) ? ($num / 100 + 1) : ($num / 100);
        $repos = collect();

        for($i = 1; $i < $pages+1; $i++){
            $data = app('RepoHttpClient')->getReposByTopic($i);
            $data = collect($data)->map(fn ($repo) => [
                    'id' => $repo['id'],
                    'name' => $repo['name'],
                    'stargazers_count' => $repo['stargazers_count'],
                    'updated_at' => $repo['updated_at'],
                ]);

            $repos = $repos->concat($data);
            Sleep::for(200)->milliseconds();
        }

        return $repos->take($num)->toArray();
    }

    private function applyQuicksearch(Request $request, array $repos):array
    {
        if (!$request->quicksearch) return $repos;

        return collect($repos)->filter(fn ($repo) => Str::contains(
            strtolower($repo['name']),
            strtolower($request->quicksearch)
        ))->toArray();

    }

    private function applySorting(Request $request, array $repos):array
    {
        if (!$request->sort_by) return $repos;

        if ($request->descending && $request->descending !== 'false'){
            $result = collect($repos)->sortByDesc($request->sort_by);
        }else{
            $result = collect($repos)->sortBy($request->sort_by);
        }
        return $result->values()->all();
    }
}
