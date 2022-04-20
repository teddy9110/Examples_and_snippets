<?php

namespace Rhf\Modules\Activity\Services;

use Exception;
use Illuminate\Support\Facades\Auth;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Modules\Activity\Models\ActivityNotes;

class ActivityNoteService
{
    protected $activityNote;
    protected $note = null;

    public function __construct()
    {
        $this->activityNote = new ActivityNotes();
    }

    public function createNote($activity, $date, $details)
    {
        $this->retrieveNote($activity, $date);

        if (!is_null($this->note)) {
            $this->setNote($this->note);
        } else {
            $this->setNote(new ActivityNotes());
        }

        $this->updateNote(
            $activity,
            $date,
            $details
        );
        return $this->note;
    }

    public function updateNote($activity, $date, $details)
    {
        $this->note['activity_id'] = $activity;
        $this->note['date'] = $date;
        $this->note['user_id'] = Auth::id();

        if (array_key_exists('period', $details)) {
            $this->note['period'] = $details['period'];
        } elseif (!$this->note->exists) {
            $this->note['period'] = 'false';
        }

        if (array_key_exists('note', $details)) {
            $this->note['note'] = $details['note'];
        }

        if (array_key_exists('body_fat_percentage', $details)) {
            $this->note['body_fat_percentage'] = $details['body_fat_percentage'];
        }

        $this->note->save();
    }

    public function deleteNote()
    {
        if (!is_null($this->note)) {
            $this->activityNote->where('id', $this->note->id)->delete();
        }
        $this->note = null;
    }

    public function retrieveNote($activity, $date)
    {
        $this->note = $this->activityNote->where('user_id', Auth::id())
            ->where('activity_id', $activity)
            ->where('date', $date)
            ->first();
    }

    public function getNote($activity, $date)
    {
        $this->retrieveNote($activity, $date);

        if (!isset($this->note)) {
            throw new FitnessBadRequestException('Note is not set');
        }
        return $this->note;
    }

    public function setNote(ActivityNotes $note)
    {
        $this->note = $note;
        return $this;
    }

    /**
     * Checks if a note should be made
     *
     * @param $type
     * @param null $note
     * @param string $period
     * @return bool
     */
    public function shouldNote($type, array $activityDetails): bool
    {
        if (
            $this->isNotableActivity($type) &&
            (
                (array_key_exists('note', $activityDetails) && $activityDetails['note'] !== null) ||
                (array_key_exists('period', $activityDetails) && $activityDetails['period'] === 'true') ||
                (array_key_exists('body_fat_percentage', $activityDetails) &&
                    $activityDetails['body_fat_percentage'] !== null))
        ) {
            return true;
        }
        return false;
    }

    /**
     * Checks what type of activity allows notes
     *
     * @param $type
     * @return bool
     */
    private function isNotableActivity($type): bool
    {
        return in_array($type, ['weight']);
    }
}
