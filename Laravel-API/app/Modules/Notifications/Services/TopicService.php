<?php

namespace Rhf\Modules\Notifications\Services;

use Illuminate\Support\Facades\DB;
use Rhf\Modules\Notifications\Models\Topics;

class TopicService
{
    protected $topic;

    public function createTopic(array $data)
    {
        $topic = new Topics();
        $this->setTopic($topic);
        $this->updateTopic($data);
        return $topic;
    }

    public function updateTopic(array $data)
    {
        DB::transaction(
            function () use ($data) {
                $topic = $this->getTopic();
                foreach ($topic->getPlainKeys() as $key) {
                    if (isset($data[$key])) {
                        $topic[$key] = $data[$key];
                    }
                }
                $topic->save();
            }
        );
    }

    public function getTopic()
    {
        return $this->topic;
    }

    public function setTopic(Topics $topic)
    {
        $this->topic = $topic;
        return $this;
    }
}
