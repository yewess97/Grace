<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use App\Services\SubcategoryService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Random\RandomException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
     * @return Application|Factory|View|string
     * @throws Throwable
     */
    final public function index(string $subcategory_slug): Application|Factory|View|string
    {
        return userProductsView(SUBCATEGORIES_TABLE, $subcategory_slug);
    }

    /**
     * Store or Update a subcategory
     * and its main image in the database and storage.
     *
     * @param string $operation
     * @return Response
     * @throws ValidationException|RandomException
     */
    final public function storeOrUpdate(string $operation): Response
    {
        $this->subcategoryService->createOrUpdateSubcategory($operation);

        return responseSuccess();
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
        return $this->subcategoryService->getSubcategoryData($subcategory);
    }

    /**
     * Delete a specified subcategory
     * and its main image from the database and storage.
     *
     * @param Subcategory $subcategory
     * @return Response
     * @throws NotFoundHttpException
     */
    final public function destroy(Subcategory $subcategory): Response
    {
        $this->subcategoryService->deleteSubcategory($subcategory);

        return responseSuccess();
    }

    /**
     * Delete the selected subcategories
     * and their main images from the database and storage.
     *
     * @param Subcategory $subcategory
     * @return Response
     * @throws NotFoundHttpException
     */
    final public function destroyMultiple(Subcategory $subcategory): Response
    {
        $this->subcategoryService->deleteMultipleSubcategories($subcategory);

        return responseSuccess();
    }

    /**
     * Restore a specified subcategory.
     *
     * @param Subcategory $subcategory
     * @return Response
     */
    final public function restore(Subcategory $subcategory): Response
    {
        $this->subcategoryService->restoreSubcategory($subcategory);

        return responseSuccess();
    }

    /**
     * Restore the selected subcategories.
     *
     * @param Subcategory $subcategory
     * @return Response
     */
    final public function restoreMultiple(Subcategory $subcategory): Response
    {
        $this->subcategoryService->restoreMultipleSubcategories($subcategory);

        return responseSuccess();
    }
}
