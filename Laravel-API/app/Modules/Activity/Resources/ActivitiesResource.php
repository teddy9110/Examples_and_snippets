<?php

namespace Rhf\Modules\Activity\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivitiesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->type === 'weight') {
            $return = [
                'activity_id' => $this->id,
                'type' => $this->type,
                'value' => $this->value,
                'date' => $this->parseDate($this->date)->format('Y-m-d'),
                'friendly_date' => $this->parseDate($this->date)->format('d/m/Y'),
                'details' => is_null($this->notes) ?
                [
                    'note' => null,
                    'period' => 'unknown'
                ] :
                [
                    'note' => $this->notes->note,
                    'period' => $this->notes->period,
                    'body_fat_percentage' => $this->notes->body_fat_percentage
                ]
            ];
        } else {
            $return = [
                'activity_id' => $this->id,
                'type' => $this->type,
                'value' => $this->value,
                'date' => $this->parseDate($this->date)->format('Y-m-d'),
                'friendly_date' => $this->parseDate($this->date)->format('d/m/Y'),
            ];
        }

        return $return;
    }

    // TODO: not sure if a fan of this, but sometimes get a string when cached not an object
    private function parseDate($date): Carbon
    {
        return Carbon::parse($date);
    }
}
