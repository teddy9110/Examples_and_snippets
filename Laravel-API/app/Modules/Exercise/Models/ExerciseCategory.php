<?php

namespace Rhf\Modules\Exercise\Models;

use Database\Factories\ExerciseCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseCategory extends Model
{
    use HasFactory;

    protected $table = 'exercise_category';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'exercise_level_id',
        'exercise_location_id',
        'facebook_id',
        'youtube'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'pivot'
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return ExerciseCategoryFactory::new();
    }

    /**
     * Relation to exercises.
     */
    public function exercises()
    {
        return $this->hasMany('Rhf\Modules\Exercise\Models\Exercise');
    }

    /**
     * Relation to exercise frequencies.
     */
    public function exerciseFrequencies()
    {
        return $this->belongsToMany(
            'Rhf\Modules\Exercise\Models\ExerciseFrequency',
            'exercise_frequency_to_exercise_category',
            'exercise_category_id',
            'exercise_frequency_id'
        );
    }

    /**
     * Relation to exercise level.
     */
    public function exerciseLevel()
    {
        return $this->belongsTo('Rhf\Modules\Exercise\Models\ExerciseLevel');
    }

    /**
     * Relation to exercise location.
     */
    public function exerciseLocation()
    {
        return $this->belongsTo('Rhf\Modules\Exercise\Models\ExerciseLocation');
    }


    /**
     * Filter by level
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param (int) exercise level ID
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByLevel($query, $exercise_level_id)
    {
        return $query->where('exercise_level_id', '=', $exercise_level_id);
    }
}
