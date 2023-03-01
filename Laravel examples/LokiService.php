<?php

namespace App\Http\Services\LokiLogs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use GuzzleHttp\Client;
use App\Models\Tapestry\ServiceLog;
use App\Http\Interfaces\ExternalLogServiceInterface;

class LokiService implements ExternalLogServiceInterface
{
    private Client $client;
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getServiceLogs(ServiceLog $serviceLog): ?Collection
    {
        $url = sprintf('%s/loki/api/v1/query_range?', config('loki-logs.url'));
        $query = sprintf('{container=~"*-worker|*-worker-scaledjob"} | json | run_id = "%s"', $serviceLog->id);
        $start = strtotime($serviceLog->started_at);
        $end = $serviceLog->finished_at !== null ? strtotime($serviceLog->finished_at) : strtotime($serviceLog->started_at) + 600;
        $response = $this->client->request('GET', $url, ['query' => ['query' => $query, 'start' => $start, 'end' => $end]]);
        return $this->formatResults(json_decode((string) $response->getBody())->data->result);
    }

    public function formatResults(?array $results): ?Collection
    {
        $logs = [];
        if (is_null($results)) {
            return null;
        }
        $logs = collect($results)
            ->flatMap(fn ($result) => $result->values)
            ->map(fn ($value) => json_decode($value[1]));
        return collect($logs);
    }
}
