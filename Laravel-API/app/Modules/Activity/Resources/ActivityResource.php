<?php

namespace Rhf\Modules\Activity\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        if (isset($this['average'])) {
            $return = [
                'average' => $this['average'],
                'value' => $this['value'],
                'date_logged_for' => $this['date']->format('Y-m-d'),
                'friendly_date_logged_for' => $this['date']->format('d/m/Y'),
                'type' => $this['type'],
            ];
        } elseif ($this['type'] === 'weight') {
            $return = [
                'value' => $this->value,
                'date_logged_for' => $this->date->format('Y-m-d'),
                'friendly_date_logged_for' => $this->date->format('d/m/Y'),
                'type' => $this->type,
                'details' => is_null($this->notes) ?
                    [
                        'note' => null,
                        'period' => 'unknown'
                    ] :
                    [
                        'note' => $this->notes->note,
                        'period' => $this->notes->period
                    ]
            ];
        } else {
            $return = [
                'value' => $this->value,
                'date_logged_for' => $this->date->format('Y-m-d'),
                'friendly_date_logged_for' => $this->date->format('d/m/Y'),
                'type' => $this->type,
            ];
        }

        return $return;
    }
}
