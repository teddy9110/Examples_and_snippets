<?php

namespace Rhf\Modules\Admin\Controllers;

use Carbon\Carbon;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Admin\Requests\AdminMockRequest;
use Rhf\Modules\Admin\Services\AdminMockService;

class AdminMockController extends Controller
{
    protected $mockService;
    public $activities = [
        'weight', 'calories', 'water', 'fiber', 'protein', 'fat', 'exercise', 'steps',
    ];

    public function __construct()
    {
        $this->mockService = new AdminMockService();
    }

    public function createUserData($id, AdminMockRequest $request)
    {
        $type = $request->input('type');
        $period = $request->input('period');
        $count = $request->input('add');

        $startDate = Carbon::parse($request->input('from'));
        $endDate = $startDate->copy()->add($period, $count, false);
        $this->mockService->createFactoryActivity($startDate, $endDate, $type, $id);

        return response('Success', 200);
    }

    public function medalsFactory($id, AdminMockRequest $request)
    {
        $period = $request->input('period');
        $count = $request->input('add');
        $medal = $request->input('medal-type', 'bronze');

        $startDate = Carbon::parse($request->input('from'));
        $endDate = $startDate->copy()->add($period, $count, false);

        $arrayLengthBasedOnMedal = $this->mockService->getStarsForMedal($medal);

        $activities = array_splice($this->activities, 1, $arrayLengthBasedOnMedal);

        foreach ($activities as $activity) {
            $this->mockService->createFactoryActivity($startDate, $endDate, $activity, $id);
        }
        return response('Success', 200);
    }
}
