<?php

namespace Rhf\Modules\Admin\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Admin\Services\AdminManagementService;
use Rhf\Modules\User\Models\User;

class AdminManagementController extends Controller
{
    private $managementService;

    public function __construct(AdminManagementService $managementService)
    {
        $this->managementService = $managementService;
    }

    public function index()
    {
        return response()->json(
            [
                'data' => [
                    'totalUsersCount' => $this->managementService->totalUsersCount(),
                    'totalNetUsers' => $this->managementService->totalNetUsers(),
                    'totalActiveUserCount' => $this->managementService->totalActiveUserCount(),
                    'newUsersCurrentMonth' => $this->managementService->newUsersCurrentMonth(),
                    'netUserChangeCurrentMonth' => $this->managementService->netUsersCurrentMonth(),
                    'expiredUserCurrentMonth' => $this->managementService->expiredUserCurrentMonth(),
                    'expiring30Days' => $this->managementService->membersExpiringInNDays(30),
                    'expiring60Days' => $this->managementService->membersExpiringInNDays(60),
                    'expiring90Days' => $this->managementService->membersExpiringInNDays(90),
                    'gymMember' => $this->managementService->workoutType('gym'),
                    'homeMember' => $this->managementService->workoutType('home'),
                    'grhaftMember' => $this->managementService->workoutType('grhaft'),
                ],
            ]
        );
    }

    /**
     * Return a json response of new/expired users within a set period
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function fixedPeriod(Request $request)
    {
        $startDate = new Carbon($request->get('from'));
        $endDate = new Carbon($request->get('to'));

        // dates flipped as query is looking to the past
        // 2020-11-18 - 2020-11-25
        $from = $startDate->startOfDay()->toDateTimeString();
        $to = $endDate->endOfDay()->toDateTimeString();

        return response()->json(
            [
                'data' => [
                    'newUsers' => $this->managementService->getNewUserCountDuringPeriod($from, $to),
                    'expiredUsers' => $this->managementService->getExpiringUserCountDuringPeriod($from, $to),
                ]
            ]
        );
    }

    /**
     * Return a collection of data for all users of type within the requested dates
     * @param Request $request
     * @return BinaryFileResponse
     * @throws Exception
     */
    public function getUsersForCSVExport(Request $request)
    {
        $startDate = $request->get('startDate') != null ? new Carbon($request->get('startDate')) : null;
        $endDate = $request->get('endDate') != null ? new Carbon($request->get('endDate')) : null;
        $type = $request->get('type') == 'expired' ? 'expiry_date' : 'created_at';

        $query = User::query()
            ->customer()
            ->whereBetween(
                $type,
                [
                    $startDate,
                    $endDate
                ]
            )->get();

        //generate CSV with headers file
        $csvOutput = $this->generateCsv($query);

        $filename = $type . '-users ' . $startDate . ' - ' . $endDate . '.csv';
        $handle = fopen($filename, 'w+');
        foreach ($csvOutput as $output) {
            fputcsv($handle, $output);
        }
        fclose($handle);

        return response()->download(
            $filename,
            $filename,
            [
                'Content-Type: text/csv',
            ],
            'attachment'
        )->deleteFileAfterSend($filename);
    }

    /**
     * Generate CSV from provided data
     * hardcoded headers
     *
     * @param $users
     * @return array
     */
    private function generateCsv($users)
    {
        $headers[] = [
            'Name',
            'Email',
            'Paid',
            'Active',
            'Created_at',
            'Expire_at'
        ];

        $dataOutput = [];
        foreach ($users as $data) {
            $dataOutput[] = [
                $data['first_name'] . ' ' . $data['surname'],
                $data['email'],
                $data['paid'],
                $data['active'],
                $data['created_at'],
                $data['expiry_date']
            ];
        }

        $csvOutput = array_merge($headers, $dataOutput);
        return $csvOutput;
    }
}
