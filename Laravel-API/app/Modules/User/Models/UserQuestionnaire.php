<?php

namespace Rhf\Modules\User\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserQuestionnaire extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'workouts_per_week',
        'max_weights',
        'workout_with_rh_app',
        'own_workouts',
        'issues_preventing_workouts',
        'tracking_progress',
        'achieve_steps',
        'step_goal_increased_days',
        'hunger_level',
        'period_due_in_days',
        'started_medication',
        'questionnaire_date',
        'zendesk_ticket_id',
        'processed_food',
        'workouts_in_weeks',
    ];

    protected $casts = [
        'max_weights' => 'boolean',
        'workout_with_rh_app' => 'boolean',
        'tracking_progress' => 'boolean',
        'started_medication' => 'boolean'
    ];

    /**
     * Relation to user.
     */
    public function user()
    {
        return $this->belongsTo('Rhf\Modules\User\Models\User', 'id', 'user_id');
    }
}
