<?php

namespace VanguardLTE\Transformers;

use League\Fractal\TransformerAbstract;
use VanguardLTE\Category;

class CategoryTransformer extends TransformerAbstract
{
    public function transform(Category $category)
    {
        return [
            'id' => (int) $category->id,
            'title' => $category->title,
            'parent' => $category->parent,
            'position' => $category->position,
            'href' => $category->href,
            'shop_id' => (int) $category->shop_id,
        ];
    }
}
