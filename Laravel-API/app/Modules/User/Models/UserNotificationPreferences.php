<?php

namespace Rhf\Modules\User\Models;

use Illuminate\Database\Eloquent\Model;
use Rhf\Modules\Notifications\Models\SubTopics;
use Rhf\Modules\Notifications\Models\Topics;

class UserNotificationPreferences extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'device_ids',
        'topics_ids',
        'subtopics_ids',
    ];

    protected $casts = [
        'device_ids' => 'array',
        'topics_ids' => 'array',
        'subtopics_ids' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo('Rhf\Modules\User\Models\User', 'id', 'user_id');
    }

    /**
     * Return an array of slugs the user is subscribed to
     * @return array
     */
    public function getSlugAttribute()
    {
        $slugs = [];
        foreach ($this->topics_ids as $id) {
            array_push($slugs, Topics::findOrFail($id)->slug);
        }
        return $slugs;
    }

    /**
     * Return an array of devices the user has
     * @return array
     */
    public function getDeviceAttribute()
    {
        $devices = [];
        foreach ($this->device_ids as $id) {
            array_push($devices, UserDevices::FindOrFail($id)->firebase_id);
        }
        return $devices;
    }

    public function getSubtopicSlugAttribute()
    {
        $subtopics = [];
        foreach ($this->subtopics_ids as $id) {
            array_push($subtopics, SubTopics::findOrFail($id)->slug);
        }
        return $subtopics;
    }
}
