<?php

namespace App\Helpers;

use App\Models\Category;
use Illuminate\Support\Arr;

/**
 * Categories helper
 */
class Categories
{

    /**
     * Returns array [catId => (catDepth indicator)catName, ...]
     *
     * @return array
     */
    public static function pluckAllCategories(): array
    {
        // $categories = Arr::pluck(Category::defaultOrder()->withDepth()->get()->all(), 'name', 'id');
        $categories = Category::defaultOrder()->withDepth()->get();
        $clCats = $categories->map(function($item, $key) {
            $depthInd = str_repeat('⇾', $item->depth);
            return ['id' => $item->id, 'name' => $depthInd . $item->name];
        });

        return Arr::pluck($clCats->all(), 'name', 'id');
    }

}
