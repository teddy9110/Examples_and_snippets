<?php

namespace Rhf\Modules\Zendesk\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Rhf\Modules\Zendesk\Services\ZendeskService;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'comment' => $this->body,
            'date' => Carbon::parse($this->created_at)->format('Y-m-d G:i:s'),
            'user' => $this->getZendeskUserName(),
            'attachments' => !empty($this->attachments) ? $this->getAttachments() : null
        ];
    }

    /**
     * @return mixed|string
     */
    private function getZendeskUserName()
    {
        $zendeskUser = (new ZendeskService())->getUserById($this->author_id)->user;
        return [
            'name' => explode(' ', $zendeskUser->name)[0],
            'role' => $zendeskUser->role
        ];
    }

    /**
     * Loop over and build an array of attachments for a ticket comment
     * @return array
     */
    private function getAttachments(): array
    {
        $attachments = [];

        if (empty($this->attachments)) {
            return $attachments;
        }

        foreach ($this->attachments as $attachment) {
            $type = explode('/', $attachment->content_type)[1];
            $thumbnails = (array) $attachment->thumbnails;
            $attachments[] = [
                'name' => $attachment->file_name,
                'type' => $type !== 'pdf' ? 'image' : 'document',
                'mime' => $attachment->content_type,
                'url' => $attachment->mapped_content_url,
                'thumbnail' => $type !== 'pdf' ? $thumbnails[0]->mapped_content_url : null
            ];
        }
        return $attachments;
    }
}
