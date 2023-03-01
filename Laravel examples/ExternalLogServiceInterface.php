<?php

namespace App\Http\Interfaces;

use App\Models\Tapestry\ServiceLog;
use Illuminate\Support\Collection;

interface ExternalLogServiceInterface
{
    public function getServiceLogs(ServiceLog $serviceLog): ?Collection;

    public function formatResults(array $results): ?Collection;
}
