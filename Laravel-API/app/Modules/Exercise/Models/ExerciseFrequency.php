<?php

namespace Rhf\Modules\Exercise\Models;

use Illuminate\Database\Eloquent\Model;
use Database\Factories\ExerciseFrequencyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExerciseFrequency extends Model
{
    use HasFactory;

    protected $table = 'exercise_frequency';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'amount',
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
        return ExerciseFrequencyFactory::new();
    }

    /**
     * Relation to exercise categories.
     */
    public function exerciseCategories()
    {
        return $this->belongsToMany(
            'Rhf\Modules\Exercise\Models\ExerciseCategory',
            'exercise_frequency_to_exercise_category',
            'exercise_frequency_id',
            'exercise_category_id'
        );
    }

    /**
     * Relation to users.
     */
    public function users()
    {
        return $this->hasMany('Rhf\Modules\User\Models\User');
    }
}
