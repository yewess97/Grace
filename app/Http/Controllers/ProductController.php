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
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
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
     * @return Application|Factory|View|JsonResponse
     * @throws Throwable
     */
    final public function index(): Application|Factory|View|JsonResponse
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
        return $this->productService->getProductDetails($productSlug);
    }

    /**
     * Store or Update a product
     * and its images in the database and storage.
     *
     * @param string $operation
     * @return JsonResponse
     * @throws ValidationException|NotFoundHttpException|ServiceUnavailableHttpException|RandomException|Throwable
     */
    final public function storeOrUpdate(string $operation): JsonResponse
    {
        [$product, $last_page] = $this->productService->createOrUpdateProduct($operation);

        $row = view(PRODUCT_ROW_PARTIAL, compact(PRODUCT_MODEL))->render();

        return responseWithData(compact(PRODUCT_MODEL, ROW, LAST_PAGE));
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
        return responseWithData([PRODUCT_MODEL => $product->data]);
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
     * @param Product $products
     * @return Response
     * @throws NotFoundHttpException
     */
    final public function destroyMultiple(Product $products): Response
    {
        $this->productService->deleteMultipleProducts($products);

        return responseSuccess();
    }

    /**
     * Restore a specified product.
     *
     * @param Product $product
     * @return Response
     */
    final public function restore(Product $product): Response
    {
        $this->productService->restoreProduct($product);

        return responseSuccess();
    }

    /**
     * Restore the selected products.
     *
     * @param Product $products
     * @return Response
     */
    final public function restoreMultiple(Product $products): Response
    {
        $this->productService->restoreMultipleProducts($products);

        return responseSuccess();
    }
}
