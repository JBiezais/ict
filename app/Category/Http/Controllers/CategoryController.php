<?php

namespace App\Category\Http\Controllers;

use App\Category\Http\Requests\CategoryStoreRequest;
use App\Category\Services\CategoryStore\CategoryStoreService;
use App\Category\Services\CategoryStore\DTO\CategoryStoreDto;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * Store a newly created category (AJAX endpoint). Returns JSON.
     */
    public function store(CategoryStoreRequest $request, CategoryStoreService $categoryStoreService): JsonResponse
    {
        $dto = CategoryStoreDto::fromRequest($request);
        $category = $categoryStoreService->execute($dto);

        return response()->json(['id' => $category->id, 'name' => $category->name], 201);
    }
}
