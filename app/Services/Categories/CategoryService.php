<?php

namespace App\Services\Categories;

use App\Models\Category;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryService {

    public function getAllCategories(bool  $includeChildren, bool $includeProducts): object {
        if ($includeChildren && $includeProducts) {
            $categories = Category::
                where('parent_id', Null)
                ->with(['children_products', 'products', 'children'])
                ->withCount(['children_products', 'products', 'children'])
                ->get();

            return $categories;
        }

        if ($includeChildren) {
            $categories = Category::
                where('parent_id', Null)
                ->with(['children'])
                ->withCount(['children'])
                ->get();

            return $categories;
        }

        if ($includeProducts) {
            $categories = Category::
                where('parent_id', Null)
                ->with(['children_products', 'products'])
                ->withCount(['children_products', 'products'])
                ->get();

            return $categories;
        }

        return $categories = Category::
            where('parent_id', Null)
            ->get();

    }

    public function getSingleCategory(int $id, bool $includeChildren, bool $includeProducts) {
        if ($includeProducts && $includeChildren) {
            $category = Category::
                where('id', $id)
                ->with(['children_products', 'products', 'children'])
                ->withCount(['children_products', 'products', 'children'])
                ->get();

            if ($category->isEmpty()) {
                throw new NotFoundHttpException("No query results for Category 13");
            }
            return $category->first();
        }

        if ($includeProducts) {
            $category = Category::
                where('id', $id)
                ->with(['children_products', 'products'])
                ->withCount(['children_products', 'products'])
                ->get();

                if ($category->isEmpty()) {
                    throw new NotFoundHttpException("No query results for Category 13");
                }
                return $category->first();
        }

        if ($includeChildren) {
            $category = Category::
                where('id', $id)
                ->with(['children'])
                ->withCount(['children'])
                ->get();

                if ($category->isEmpty()) {
                    throw new NotFoundHttpException("No query results for Category 13");
                }
                return $category->first();
        }

        $category = Category::findOrFail($id);

        return $category;
    }

}
