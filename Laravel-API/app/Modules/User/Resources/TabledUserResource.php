<?php

namespace Rhf\Modules\User\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TabledUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $return = [];
        if ($this->checkColumn('id')) {
            $return['id'] = $this->id;
        }
        if ($this->checkColumn('name')) {
            $return['name'] = $this->first_name . ' ' . $this->surname;
        }
        if ($this->checkColumn('email')) {
            $return['email'] = $this->email;
        }
        if ($this->checkColumn('status')) {
            $return['status'] = $this->paid == 1 ? 'Paid' : 'Unpaid';
        }
        if ($this->checkColumn('created_at')) {
            $return['created_at'] = isset($this->created_at) ? $this->created_at->format('d/m/Y') : '';
        }

        // Check if MyFitnessPal has been connected
        $return['mfp_connected'] = $this->hasConnectedMfp();

        return $return;
    }

    /**
     * Check if the column is requested for the result set.
     *
     * @return bool
     */
    private function checkColumn($key)
    {
        if (request()->has('columns')) {
            foreach (request()->get('columns') as $column) {
                if ($column['data'] == $key) {
                    return true;
                }
            }
        }
        return false;
    }
}
