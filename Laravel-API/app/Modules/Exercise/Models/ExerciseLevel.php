<?php

namespace Rhf\Modules\Exercise\Models;

use Database\Factories\ExerciseLevelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseLevel extends Model
{
    use HasFactory;

    protected $table = 'exercise_level';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
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
        return ExerciseLevelFactory::new();
    }

    /**
     * Relation to exercises.
     */
    public function exercises()
    {
        return $this->hasMany('Rhf\Modules\Exercise\Models\Exercise', 'exercise_level_id', 'id');
    }

    /**
     * Relation to exercise categories.
     */
    public function exerciseCategories()
    {
        return $this->hasMany('Rhf\Modules\Exercise\Models\ExerciseCategory', 'exercise_category_id', 'id');
    }
}
