<?php

namespace App\Services\Categories;

use App\Models\Category;
use App\DataTransfareObjects\V1\CustomJson;

class CategoryValidateParentId {
    public function validate(int $parentId): bool {
        $parent = Category::find($parentId);
        if($parent && $parent->parent_id) {
            return false;
        }
        return true;
    }
}
