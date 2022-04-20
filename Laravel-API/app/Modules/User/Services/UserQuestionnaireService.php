<?php

namespace Rhf\Modules\User\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Rhf\Modules\User\Models\UserQuestionnaire;
use Rhf\Modules\Zendesk\Services\ZendeskService;
use Illuminate\Support\Str;

class UserQuestionnaireService
{
    private $zendeskService;
    private $userQuestionnaire;
    private $user;

    private $questions = [
        'workout_with_rh_app' => 'Are you doing the workouts inside App?',
        'own_workouts' => 'Are you doing your own workouts?',
        'issues_preventing_workouts' => 'Do you have any issues preventing you working out?',
        'lifting_as_heavy_as_possible' => 'Are you lifting as heavy as possible and doing as many reps as you can?',
        'taking_progress_photos' => 'Are you taking progress photos, If so, Do you upload to app?',
        'achieve_steps' => 'How do you get your steps?',
        'step_goal_increased_days' => 'Have you recently increased your steps goal, and how long ago?',
        'hunger_level' => 'How is your hunger?',
        'period_due_in_days' => 'If you have periods, when is your next one due',
        'started_medication' => 'Have you started any new Medication in the last 2 weeks?',
        'processed_food' => 'Is more than 20% of your calories coming from processed food?',
        'workouts_in_weeks' => 'How long in weeks/months have you been doing workouts?',
        'changed_workouts' => 'Have you changed your workouts in the last 4 weeks?'
    ];

    public function __construct(Authenticatable $user)
    {
        $this->zendeskService = new ZendeskService();
        $this->userQuestionnaire = new UserQuestionnaire();
        $this->user = $user;
    }

    /**
     * Create Table row
     * Zendesk ticket
     * Update row with ticket identifier
     *
     * @param array $values
     * @return mixed
     */
    public function createUserQuestionnaire(array $values)
    {
        $subject = 'User Questionnaire - ' . $this->user->name;
        $body = $this->transformFieldData($values);
        $tags = ['user_questionnaire_response'];
        if (isset($values['platform'])) {
            $tags[] = strtolower($values['platform']);
        }
        if (isset($values['app_version'])) {
            $tags[] = $values['app_version'];
        }
        $newTicket = $this->createZendeskTicket($subject, $body, $tags);

        // Add ticket to values and create row
        $values['zendesk_ticket_id'] = $newTicket->ticket->id;
        $row = $this->createRow($values);

        // Update with internal note
        $note = config('app.admin_panel_url') . config('app.admin_panel_users') . $this->user->id;
        $internalNote = $this->createInternalNote($newTicket->ticket->id, $note);
        return $row;
    }

    /**
     * Create table row in UserQuestionnaire table
     *
     * @param $values
     * @return mixed
     */
    private function createRow($values)
    {
        return $this->userQuestionnaire->create(
            [
                'user_id' => $this->user->id,
                'workouts_per_week' => $this->getValue($values, 'weight_resistance_workouts_per_week'),
                'max_weights' => $this->getValue($values, 'lifting_as_heavy_as_possible', false),
                'workout_with_rh_app' => $this->getValue($values, 'workout_with_rh_app', false),
                'own_workouts' => $this->getValue($values, 'own_workouts'),
                'issues_preventing_workouts' => $this->getValue($values, 'issues_preventing_workouts'),
                'tracking_progress' => $this->getValue($values, 'taking_progress_photos', false),
                'achieve_steps' => $this->getValue($values, 'achieve_steps'),
                'step_goal_increased_days' => $this->getValue($values, 'step_goal_increased_days'),
                'hunger_level' => $this->getValue($values, 'hunger_level'),
                'period_due_in_days' => $this->getValue($values, 'period_due_in_days'),
                'started_medication' => $this->getValue($values, 'started_medication', false),
                'questionnaire_date' => now()->format('Y-m-d'),
                'processed_food' => $this->getValue($values, 'processed_food', false),
                'workouts_in_weeks' => $this->getValue($values, 'workouts_in_weeks', 0),
                'changed_workouts' => $this->getValue($values, 'changed_workouts', false),
                'zendesk_ticket_id' => $this->getValue($values, 'zendesk_ticket_id'),
            ]
        );
    }

    /**
     * Check if a key is set on values and return. If not - return null.
     *
     * @param $values
     * @param $key
     * @return mixed
     */
    private function getValue(array $values, string $key, $default = null)
    {
        return isset($values[$key]) ? $values[$key] : $default;
    }

    /**
     * Create Zendesk ticket and return ticket object
     * User User name/email
     *
     * @param $subject
     * @param $body
     * @return \stdClass|null
     */
    private function createZendeskTicket($subject, $body, array $tags = [])
    {
        return $this->zendeskService->createNewTicket($this->user->name, $this->user->email, $subject, $body, $tags);
    }

    /**
     * Build Message text out
     * TODO: Could be cleaner
     *
     * @param $values
     * @return string
     */
    private function transformFieldData($values)
    {
        $message = $this->user->name . ' has submitted the following responses: ' . PHP_EOL;
        $message .= PHP_EOL;
        foreach ($values as $key => $value) {
            if ($key == 'weight_resistance_workouts_per_week') {
                $message .= $this->buildMessage('Are you doing weight resistance workouts?');
                if ($value > 0) {
                    $message .= $this->buildAnswer('true');
                    $message .= PHP_EOL;
                    $message .= $this->buildMessage('How many?');
                    $message .= $this->buildAnswer($value, 'many');
                } else {
                    $message .= $this->buildMessage('No');
                }
            }

            if (array_key_exists($key, $this->questions)) {
                $message .= PHP_EOL;
                $message .= $this->buildMessage($this->questions[$key]);
                $message .= $this->buildAnswer($value, $key);
            }
        }
        $message .= $this->buildMessage('');
        $message .= 'Thank you';
        return $message;
    }

    /**
     * Return a message with an new line
     *
     * @param $value
     * @return string
     */
    private function buildMessage($value)
    {
        return $value . PHP_EOL;
    }

    private function buildAnswer($value, $key = null)
    {
        $message = '';
        switch ($value) {
            // set as string to catch the true variables correctly
            case 'true':
                $message .= $this->buildMessage('Yes');
                break;
            // set as boolean/false to work correctly
            case false:
                $message .= $this->buildMessage('No');
                break;
            case (is_integer($value)) && !Str::contains($key, ['hunger', 'many', 'tracking', 'weeks']):
                $message .= $this->buildMessage($value . ' days');
                break;
            case Str::contains($key, 'hunger_level'):
                $message .= $this->buildMessage($value . ' - ' . $this->hungerLevel($value));
                break;
            case Str::contains($key, 'workouts_in_weeks'):
                $message .= $this->buildMessage($value . ' week(s)');
                break;
            default:
                $message .= $this->buildMessage($value);
                break;
        }
        return $message;
    }

    private function hungerLevel($value)
    {
        if ($value < 4) {
            return 'Not Hungry';
        } elseif ($value < 7) {
            return 'Quite Hungry';
        } elseif ($value < 10) {
            return 'Hungry';
        } else {
            return 'Starving';
        }
    }

    private function tracking($value)
    {
        switch ($value) {
            case 1:
                return '100%, 7 days a week for the last 4 weeks';
            case 2:
                return 'Monday-Friday, but struggle at weekends';
            case 3:
                return 'I lied previously and my tracking needs to improve';
        }
    }

    private function createInternalNote($id, string $note)
    {
        $this->zendeskService->internalNote($id, $note);
    }
}
