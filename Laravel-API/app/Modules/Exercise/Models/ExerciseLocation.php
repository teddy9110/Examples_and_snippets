<?php

namespace Rhf\Modules\Exercise\Models;

use Database\Factories\ExerciseLocationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseLocation extends Model
{
    use HasFactory;

    protected $table = 'exercise_location';

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
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return ExerciseLocationFactory::new();
    }

    /**
     * Relation to exercise categories.
     */
    public function exerciseCategories()
    {
        return $this->hasMany('Rhf\Modules\Exercise\Models\ExerciseCategory');
    }

    /**
     * Relation to users.
     */
    public function users()
    {
        return $this->hasMany('Rhf\Modules\User\Models\User');
    }
}
