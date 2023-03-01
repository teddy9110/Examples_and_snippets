<?php

namespace App\Http\Services\DataDog;

use App\Http\Interfaces\ExternalLogServiceInterface;
use App\Models\Tapestry\ServiceLog;
use Exception;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class DataDogService implements ExternalLogServiceInterface
{
    /**
     * Collection of all logs pulled from the Datadog Paginated API
     *
     * @var Collection
     */
    private Collection $allLogs;

    /**
     * DataDogService constructor
     */
    public function __construct()
    {
        $this->setAllLogs(collect([]));
    }

    /**
     * Get all logs
     *
     * @return Collection
     */
    public function getAllLogs(): Collection
    {
        return $this->allLogs;
    }

    /**
     * Set all logs
     *
     * @param Collection $allLogs
     */
    public function setAllLogs(Collection $allLogs): void
    {
        $this->allLogs = $allLogs;
    }

    /**
     * Get service logs from data dog using the service log ID
     *
     * @param ServiceLog $serviceLog
     * @param string|null $nextPage
     *
     * @return Collection|null
     */
    public function getServiceLogs(ServiceLog $serviceLog, ?string $nextPage = null): ?Collection
    {
        $url = sprintf('%s/api/v2/logs/events/search', config('data-dog.url'));

        $page = ['limit' => 200];
        if ($nextPage) {
            $page['cursor'] = $nextPage;
        }
        
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'DD-API-KEY' => config('data-dog.api_key'),
            'DD-APPLICATION-KEY' => config('data-dog.application_key')
        ])->post(
            $url,
            [
                'filter' => ['query' => sprintf('@run_id:%s', $serviceLog->id)],
                'from' => strtotime($serviceLog->started_at)*1000,
                'to' => is_null($serviceLog->finished_at) ? (strtotime($serviceLog->started_at)+300)*1000 : strtotime($serviceLog->finished_at)*1000,
                'page' => $page,
                'sort' => 'timestamp'
            ]
        );
        
        $collectedLogs = collect($response->json('data'))->map(function ($log) use ($serviceLog) {
            $deepAttributes = $log['attributes']['attributes'];
            if($deepAttributes['run_id'] === $serviceLog->id){
                return (object)[
                    'message' => $log['attributes']['message'],
                    'level_name' => $deepAttributes['level_name'],
                    'datetime' => $deepAttributes['datetime']
                ];
            }
        });

        $this->setAllLogs($this->getAllLogs()->merge($collectedLogs));

        if ($response->json('meta.page.after')) {
            return $this->getServiceLogs($serviceLog, $response->json('meta.page.after'));
        }

        return $this->getAllLogs();
    }

    public function formatResults(array $results): ?Collection
    {
        return collect($results);
    }
}
