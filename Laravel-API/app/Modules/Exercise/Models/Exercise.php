<?php

namespace Rhf\Modules\Exercise\Models;

use Database\Factories\ExerciseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $table = 'exercise';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'exercise_level_id',
        'sort_order',
        'title',
        'quantity',
        'content',
        'video',
        'content_video',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return ExerciseFactory::new();
    }


    /*
    *
    * RELATIONSHIPS
    *
    */

    /**
     * Relation to exercise level.
     */
    public function level()
    {
        return $this->belongsTo('Rhf\Modules\Exercise\Models\ExerciseLevel', 'id', 'exercise_level_id');
    }

    /**
     * Relation to exercise categories.
     */
    public function categories()
    {
        return $this->belongsToMany(
            'Rhf\Modules\Exercise\Models\ExerciseCategory',
            'exercise_to_exercise_category',
            'exercise_id',
            'exercise_category_id'
        );
    }


    /*
    *
    * CUSTOM QUERY SCOPES
    *
    */

    /**
     * Filter by category
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param (int) category ID
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCategory($query, $category_id)
    {
        return $query->where('exercise_category_id', '=', $category_id);
    }

    /**
     * Filter by level
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param (int) exercise level ID
     * @return \Illuminate\Database\Eloquent\Builder
     */
/* NEEDS REFACTOR TO NEW DB ARCHITECTURE
    public function scopeByLevel($query, $exercise_level_id)
    {
        return $query->where('exercise_level_id', '=', $exercise_level_id);
    }
*/
}
