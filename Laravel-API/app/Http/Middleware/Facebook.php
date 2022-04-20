<?php

namespace Rhf\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Rhf\Modules\System\Models\ActivityLog;
use Rhf\Exceptions\FitnessHttpException;

class Facebook
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $date = Carbon::now()->subMinutes(5);

        $log = ActivityLog::where('action', '=', 'FacebookFail')->where('created_at', '>', $date);

        if ($log->count() > 0) {
            throw new FitnessHttpException('Unable to retrieve video content. Please try again later', 424);
        }

        return $next($request);
    }
}
