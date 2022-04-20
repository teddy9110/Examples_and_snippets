<?php

namespace Rhf\Modules\Content\Services;

use Rhf\Modules\Content\Models\Category;

class CategoryService
{
    /**
     * Create a new Service instance.
     *
     * @return void
     */
    public function __construct()
    {
    }


    /**************************************************
    *
    * PUBLIC METHODS
    *
    ***************************************************/

    /**
     * Return filtered list of items.
     *
     * @return query
     */
    public static function filtered()
    {
        $categoryModel = new Category();

        // Check for relevant filter conditions and apply to query object
        if (request()->get('search')['value'] != null) {
            $categoryModel = $categoryModel->search(request()->get('search'));
        }

        // Check for order by
        if (request()->has('order')) { // TODO - Check we are setting the correct request key for "order"
            $order = request()->get('columns')[request()->get('order')[0]['column']]['name'];
            $direction = request()->get('order')[0]['dir'];
            $categoryModel = $categoryModel->orderBy($order, $direction);
        }

        return $categoryModel;
    }

    /**
     * Update the item.
     *
     * @param array
     * @return self
     */
    public function update($data)
    {
        // TODO
    }
}
