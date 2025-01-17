<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Random\RandomException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ProductController extends Controller
{
    /**
     * Product Controller Constructor.
     *
     * @param ProductService $productService
     * @return void
     */
    final public function __construct(private readonly ProductService $productService){}

    /**
     * Display the products' resource.
     *
     * @return Application|Factory|View|string
     * @throws Throwable
     */
    final public function index(): Application|Factory|View|string
    {
        return userProductsView(PRODUCTS_TABLE);
    }

    /**
     * Show the details page of the product.
     *
     * @param string $productSlug
     * @return Application|Factory|View|array|string
     * @throws Throwable
     */
    final public function show(string $productSlug): Application|Factory|View|array|string
    {
        return $this->productService->showProductDetails($productSlug);
    }

    /**
     * Store or Update a product
     * and its images in the database and storage.
     *
     * @param string $operation
     * @return Response
     * @throws ValidationException|RandomException
     */
    final public function storeOrUpdate(string $operation): Response
    {
        $this->productService->createOrUpdateProduct($operation);

        return responseSuccess();
    }

    /**
     * Get the data of a specified product
     * with its related sizes, subcategories, and thumbnail images.
     *
     * @param Product $product
     * @return JsonResponse
     */
    final public function edit(Product $product): JsonResponse
    {
        return $this->productService->getProductData($product);
    }

    /**
     * Delete a specified product
     * and its images from the database and storage.
     *
     * @param Product $product
     * @return Response
     * @throws NotFoundHttpException
     */
    final public function destroy(Product $product): Response
    {
        $this->productService->deleteProduct($product);

        return responseSuccess();
    }

    /**
     * Delete the selected products
     * and their images from the database and storage.
     *
     * @param Product $product
     * @return Response
     * @throws NotFoundHttpException
     */
    final public function destroyMultiple(Product $product): Response
    {
        $this->productService->deleteMultipleProducts($product);

        return responseSuccess();
    }
}
