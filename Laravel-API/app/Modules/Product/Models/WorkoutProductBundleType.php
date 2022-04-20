<?php

namespace Rhf\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkoutProductBundleType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'exercise_location_id',
        'exercise_level_id',
        'exercise_frequency_id',
        'bundle_id'
    ];

    /**
     * Returns an ID for the bundle that supports the users selected preferences
     *
     * @param $exerciseLocation
     * @param $exerciseLevel
     * @param null $exerciseFreq
     * @return mixed
     */
    public static function getBundleId($exerciseLocation, $exerciseLevel, $exerciseFreq)
    {
        $query = self::select('bundle_id');
        if (isset($exerciseFreq)) {
            $query = $query->where('exercise_frequency_id', $exerciseFreq);
        }
        if (isset($exerciseLocation)) {
            $query = $query->where('exercise_location_id', $exerciseLocation);
        }
        if (isset($exerciseLevel)) {
            $query = $query->where('exercise_level_id', $exerciseLevel);
        }
        $query = $query->first();
        // if null, returns the zero bundle
        return !is_null($query) ? $query->bundle_id : self::where('exercise_frequency_id', 1)->first()->bundle_id;
    }
}
