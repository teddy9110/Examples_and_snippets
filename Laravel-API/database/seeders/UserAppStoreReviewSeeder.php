<?php

use Illuminate\Database\seeder;
use Carbon\Carbon;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\User\Models\UserAppStoreReview;

class UserAppStoreReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::doesntHave('appStoreReview')->customer()->chunk(100, function ($users) {
            $chunkedUsers = array();
            foreach ($users as $user) {
                $days = rand(10, 13);
                $reviewDate = Carbon::parse($user->last_active)->addDays($days);
                if ($reviewDate->lte(Carbon::now())) {
                    $reviewDate = Carbon::now()->addDays($days);
                }
                $createdUser = [
                    'user_id' => $user->id,
                    'present_review_dialog' => false,
                    'next_review_request' => $reviewDate,
                    'last_review_submitted' => null,
                ];
                array_push($chunkedUsers, $createdUser);
            }
            UserAppStoreReview::insert($chunkedUsers);
        });
    }
}
