<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Random\RandomException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Psr\SimpleCache\InvalidArgumentException as CacheInvalidArgumentException;
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
     * @return Application|Factory|View|JsonResponse
     * @throws Throwable
     */
    final public function index(string $category_slug): Application|Factory|View|JsonResponse
    {
        return userProductsView(CATEGORIES_TABLE, $category_slug);
    }

    /**
     * Store or Update a category
     * and its images in the database and storage.
     *
     * @param string $operation
     * @return JsonResponse
     * @throws ValidationException|NotFoundHttpException|ServiceUnavailableHttpException|RandomException|CacheInvalidArgumentException|Throwable
     */
    final public function storeOrUpdate(string $operation): JsonResponse
    {
        $category = $this->categoryService->createOrUpdateCategory($operation);

        $row       = view(CATEGORY_ROW_PARTIAL, compact(CATEGORY_MODEL))->render();
        $last_page = getLastPage($category);

        return responseWithData(compact(ROW, LAST_PAGE));
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
     * @throws NotFoundHttpException|CacheInvalidArgumentException|ModelNotFoundException
     */
    final public function destroy(Category $category): Response
    {
        $category_deleted = $this->categoryService->deleteCategory($category);

        return $category_deleted
            ? responseSuccess()
            : throw new ModelNotFoundException('The '.CATEGORY_MODEL.' you are trying to '.REMOVE.'/'.DELETE.' is not found!');
    }

    /**
     * Delete the selected categories
     * and their images from the database and storage.
     *
     * @param Category $categories
     * @return Response
     * @throws NotFoundHttpException|CacheInvalidArgumentException|ModelNotFoundException
     */
    final public function destroyMultiple(Category $categories): Response
    {
        $categories_deleted = $this->categoryService->deleteMultipleCategories($categories);

        return $categories_deleted
            ? responseSuccess()
            : throw new ModelNotFoundException('The '.CATEGORIES_TABLE.' (or some of them) you are trying to '.REMOVE.'/'.DELETE.' are not found!');
    }

    /**
     * Restore a specified category.
     *
     * @param Category $category
     * @return Response
     * @throws CacheInvalidArgumentException|ModelNotFoundException
     */
    final public function restore(Category $category): Response
    {
        $category_restored = $this->categoryService->restoreCategory($category);

        return $category_restored
            ? responseSuccess()
            : throw new ModelNotFoundException('The '.CATEGORY_MODEL.' you are trying to '.RESTORE.' is not found!');
    }

    /**
     * Restore the selected categories.
     *
     * @param Category $categories
     * @return Response
     * @throws CacheInvalidArgumentException|ModelNotFoundException
     */
    final public function restoreMultiple(Category $categories): Response
    {
        $categories_restored =  $this->categoryService->restoreMultipleCategories($categories);

        return $categories_restored
            ? responseSuccess()
            : throw new ModelNotFoundException('The '.CATEGORIES_TABLE.' (or some of them) you are trying to '.RESTORE.' are not found!');
    }
}
