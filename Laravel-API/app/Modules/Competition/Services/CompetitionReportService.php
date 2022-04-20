<?php

namespace Rhf\Modules\Competition\Services;

use Illuminate\Support\Facades\Auth;
use Rhf\Modules\Competition\Models\CompetitionReports;

class CompetitionReportService
{
    public function exists($entryId, $userId)
    {
        return CompetitionReports::where('entry_id', $entryId)->where('user_id', $userId)->exists();
    }

    public function createReport($entryId, $report)
    {
        return CompetitionReports::create([
            'entry_id' => $entryId,
            'report' => $report,
            'user_id' => Auth::id()
        ]);
    }
}
