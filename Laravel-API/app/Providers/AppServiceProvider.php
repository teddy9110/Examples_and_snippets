<?php

namespace Rhf\Providers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Rhf\Modules\Workout\Models\Workout;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Morph map
         */
        Relation::morphMap([
            'workouts' => Workout::class,
        ]);

        /**
         * Custom validators
         */
        Validator::extendImplicit('not_before_if_set', function ($attr, $value, $param, $validator) {
            $data = request()->input($param[0]);
            if (!is_null($data)) {
                $startDate = Carbon::parse($data);
                $endDate = Carbon::parse($value);
                if ($startDate->gte($endDate)) {
                    return false;
                }
            }
            return true;
        });

        Validator::replacer('not_before_if_set', function ($message, $attr, $rule, $param) {
            return $attr . ' cannot be before ' . $param[0];
        });

        Validator::extendImplicit('not_after_if_set', function ($attr, $value, $param, $validator) {
            $data = request()->input($param[0]);
            if (!is_null($data)) {
                $startDate = Carbon::parse($value);
                $endDate = Carbon::parse($data);
                if ($startDate->gte($endDate)) {
                    return false;
                }
            }
            return true;
        });

        Validator::replacer('not_after_if_set', function ($message, $attr, $rule, $param) {
            return $attr . ' cannot be after ' . $param[0];
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
