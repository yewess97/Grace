<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
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

        $row = view(CATEGORY_ROW_PARTIAL, compact(CATEGORY_MODEL))->render();

        return responseWithData(compact(CATEGORY_MODEL, ROW));
    }

    /**
     * Get the data of a specified category.
     *
     * @param Category $category
     * @return JsonResponse
     */
    final public function edit(Category $category): JsonResponse
    {
        return responseWithData([CATEGORY_MODEL => $category->data]);
    }

    /**
     * Delete a specified category
     * and its images from the database and storage.
     *
     * @param Category $category
     * @return Response
     * @throws NotFoundHttpException
     */
    final public function destroy(Category $category): Response
    {
        $this->categoryService->deleteCategory($category);

        return responseSuccess();
    }

    /**
     * Delete the selected categories
     * and their images from the database and storage.
     *
     * @param Category $categories
     * @return Response
     * @throws NotFoundHttpException
     */
    final public function destroyMultiple(Category $categories): Response
    {
        $this->categoryService->deleteMultipleCategories($categories);

        return responseSuccess();
    }

    /**
     * Restore a specified category.
     *
     * @param Category $category
     * @return Response
     */
    final public function restore(Category $category): Response
    {
        $this->categoryService->restoreCategory($category);

        return responseSuccess();
    }

    /**
     * Restore the selected categories.
     *
     * @param Category $categories
     * @return Response
     */
    final public function restoreMultiple(Category $categories): Response
    {
        $this->categoryService->restoreMultipleCategories($categories);

        return responseSuccess();
    }
}
