<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use App\Services\SubcategoryService;
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

class SubcategoryController extends Controller
{
    /**
     * Subcategory Controller Constructor.
     *
     * @param SubcategoryService $subcategoryService
     * @return void
     */
    final public function __construct(private readonly SubcategoryService $subcategoryService){}


    /**
     * Display the subcategory products' resource.
     *
     * @param string $subcategory_slug
     * @return Application|Factory|View|JsonResponse
     * @throws Throwable
     */
    final public function index(string $subcategory_slug): Application|Factory|View|JsonResponse
    {
        return userProductsView(SUBCATEGORIES_TABLE, $subcategory_slug);
    }

    /**
     * Store or Update a subcategory
     * and its main image in the database and storage.
     *
     * @param string $operation
     * @return JsonResponse
     * @throws ValidationException|NotFoundHttpException|ServiceUnavailableHttpException|RandomException|CacheInvalidArgumentException|Throwable
     */
    final public function storeOrUpdate(string $operation): JsonResponse
    {
        $subcategory = $this->subcategoryService->createOrUpdateSubcategory($operation);

        $row       = view(SUBCATEGORY_ROW_PARTIAL, compact(SUBCATEGORY_MODEL))->render();
        $last_page = getLastPage($subcategory);

        return responseWithData(compact(ROW, LAST_PAGE));
    }

    /**
     * Get the data of a specified subcategory
     * with its related categories.
     *
     * @param Subcategory $subcategory
     * @return JsonResponse
     */
    final public function edit(Subcategory $subcategory): JsonResponse
    {
        return responseWithData([SUBCATEGORY_MODEL => $subcategory->data]);
    }

    /**
     * Delete a specified subcategory
     * and its main image from the database and storage.
     *
     * @param Subcategory $subcategory
     * @return Response
     * @throws NotFoundHttpException|CacheInvalidArgumentException|ModelNotFoundException
     */
    final public function destroy(Subcategory $subcategory): Response
    {
        $subcategory_deleted = $this->subcategoryService->deleteSubcategory($subcategory);

        return $subcategory_deleted
            ? responseSuccess()
            : throw new ModelNotFoundException(clearExceptionMessage(SUBCATEGORY_MODEL, REMOVE_OR_DELETE));
    }

    /**
     * Delete the selected subcategories
     * and their main images from the database and storage.
     *
     * @param Subcategory $subcategories
     * @return Response
     * @throws NotFoundHttpException|CacheInvalidArgumentException|ModelNotFoundException
     */
    final public function destroyMultiple(Subcategory $subcategories): Response
    {
        $subcategories_deleted = $this->subcategoryService->deleteMultipleSubcategories($subcategories);

        return $subcategories_deleted
            ? responseSuccess()
            : throw new ModelNotFoundException(clearExceptionMessage(SUBCATEGORY_MODEL, REMOVE_OR_DELETE, true));
    }

    /**
     * Restore a specified subcategory.
     *
     * @param Subcategory $subcategory
     * @return Response
     * @throws CacheInvalidArgumentException|ModelNotFoundException
     */
    final public function restore(Subcategory $subcategory): Response
    {
        $subcategory_restored = $this->subcategoryService->restoreSubcategory($subcategory);

        return $subcategory_restored
            ? responseSuccess()
            : throw new ModelNotFoundException(clearExceptionMessage(SUBCATEGORY_MODEL, RESTORE));
    }

    /**
     * Restore the selected subcategories.
     *
     * @param Subcategory $subcategories
     * @return Response
     * @throws CacheInvalidArgumentException|ModelNotFoundException
     */
    final public function restoreMultiple(Subcategory $subcategories): Response
    {
        $subcategories_restored = $this->subcategoryService->restoreMultipleSubcategories($subcategories);

        return $subcategories_restored
            ? responseSuccess()
            : throw new ModelNotFoundException(clearExceptionMessage(SUBCATEGORY_MODEL, RESTORE, true));
    }
}
