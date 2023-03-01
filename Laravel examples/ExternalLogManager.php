<?php

namespace App\Http\Managers;

use App\Http\Interfaces\ExternalLogServiceInterface;
use App\Http\Services\DataDog\DataDogService;
use App\Http\Services\LokiLogs\LokiService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Manager;

class ExternalLogManager extends Manager
{
    public function createDataDogDriver(): ExternalLogServiceInterface
    {
        return App::make(DataDogService::class);
    }

    public function createLokiDriver(): ExternalLogServiceInterface
    {
        return App::make(LokiService::class);
    }

    public function getDefaultDriver(): string
    {
        return 'Loki';
    }
}
