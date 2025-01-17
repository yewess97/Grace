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
     * @return Response
     * @throws ValidationException|RandomException
     */
    final public function storeOrUpdate(string $operation): Response
    {
        $this->categoryService->createOrUpdateCategory($operation);

        return responseSuccess();
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
     * @param Category $category
     * @return Response
     * @throws NotFoundHttpException
     */
    final public function destroyMultiple(Category $category): Response
    {
        $this->categoryService->deleteMultipleCategories($category);

        return responseSuccess();
    }
}
