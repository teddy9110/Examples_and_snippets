<?php

namespace Rhf\Modules\Content\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TabledContentResource extends JsonResource
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
        if ($this->checkColumn('title')) {
            $return['title'] = $this->title;
        }
        if ($this->checkColumn('category')) {
            $return['category'] = $this->category->title;
        }
        if ($this->checkColumn('type')) {
            $return['type'] = $this->type;
        }
        if ($this->checkColumn('status')) {
            $return['status'] = $this->status;
        }
        if ($this->checkColumn('created_at')) {
            $return['created_at'] = isset($this->created_at) ? $this->created_at->format('d/m/Y') : '';
        }

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
