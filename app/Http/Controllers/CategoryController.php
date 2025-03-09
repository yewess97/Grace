<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Random\RandomException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Throwable;

class CategoryController extends Controller
{
    /**
     * Category Controller Constructor.
     *
     * @param CategoryService $categoryService
     * @return void
     */
    final public function __construct(private readonly CategoryService $categoryService){}

    /**
     * Display the category products' resource.
     *
     * @param string $category_slug
     * @return Application|Factory|View|string
     * @throws Throwable
     */
    final public function index(string $category_slug): Application|Factory|View|string
    {
        return userProductsView(CATEGORIES_TABLE, $category_slug);
    }

    /**
     * Store or Update a category
     * and its images in the database and storage.
     *
     * @param string $operation
     * @return JsonResponse
     * @throws ValidationException|NotFoundHttpException|ServiceUnavailableHttpException|RandomException|Throwable
     */
    final public function storeOrUpdate(string $operation): JsonResponse
    {
        $category = $this->categoryService->createOrUpdateCategory($operation);
        $key = Category::count() - 1;

        $row = view(CATEGORY_ROW_PARTIAL, compact(CATEGORY_MODEL, KEY))->render();

        return responseSuccess(null, compact(CATEGORY_MODEL, ROW));
    }

    /**
     * Get the data of a specified category.
     *
     * @param Category $category
     * @return JsonResponse
     */
    final public function edit(Category $category): JsonResponse
    {
        return $this->categoryService->getCategoryData($category);
    }

    /**
     * Delete a specified category
     * and its images from the database and storage.
     *
     * @param Category $category
     * @return JsonResponse
     * @throws NotFoundHttpException
     */
    final public function destroy(Category $category): JsonResponse
    {
        $this->categoryService->deleteCategory($category);

        return responseSuccess(200);
    }

    /**
     * Delete the selected categories
     * and their images from the database and storage.
     *
     * @param Category $categories
     * @return JsonResponse
     * @throws NotFoundHttpException
     */
    final public function destroyMultiple(Category $categories): JsonResponse
    {
        $this->categoryService->deleteMultipleCategories($categories);

        return responseSuccess(200);
    }

    /**
     * Restore a specified category.
     *
     * @param Category $category
     * @return JsonResponse
     */
    final public function restore(Category $category): JsonResponse
    {
        $this->categoryService->restoreCategory($category);

        return responseSuccess(200);
    }

    /**
     * Restore the selected categories.
     *
     * @param Category $categories
     * @return JsonResponse
     */
    final public function restoreMultiple(Category $categories): JsonResponse
    {
        $this->categoryService->restoreMultipleCategories($categories);

        return responseSuccess(200);
    }
}
