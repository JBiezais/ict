<?php

namespace App\Category\Services\CategoryStore;

use App\Category\Database\Models\Category;
use App\Category\Services\CategoryStore\DTO\CategoryStoreDto;

class CategoryStoreService
{
    public function execute(CategoryStoreDto $dto): Category
    {
        return Category::query()->create([
            'name' => trim($dto->name),
        ]);
    }
}
