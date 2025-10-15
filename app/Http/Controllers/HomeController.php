<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Throwable;

class HomeController extends Controller
{
    /**
     * Display the home resource.
     *
     * @return Application|Factory|View|JsonResponse
     * @throws Throwable
     */
    final public function index(): Application|Factory|View|JsonResponse
    {
        $products_ids = cache()->remember(HOME_PRODUCTS, 1800, static fn() =>
            Product::query()->mostSelling()
                ->pluck(ID)
                ->toArray()
        );

        $products = paginateWithFallback(Product::class, $products_ids);

        $services = [
            [
                MAIN_IMAGE        => "1",
                TITLE             => "Free fast delivery",
                SHORT_DESCRIPTION => "Fast order delivery tracking",
            ],
            [
                MAIN_IMAGE        => "2",
                TITLE             => "24 X 7 Supports",
                SHORT_DESCRIPTION => "If you need help, we are opening 24 x 7",
            ],
            [
                MAIN_IMAGE        => "3",
                TITLE             => "Best quality",
                SHORT_DESCRIPTION => "We offer the best quality squishies",
            ],
            [
                MAIN_IMAGE        => "4",
                TITLE             => "Gift Voucher",
                SHORT_DESCRIPTION => "Best terms and conditions for gift vouchers",
            ],
        ];

        $services                  = object_from_array($services);
        $products_pagination_route = 'home';

        return request()?->ajax()
            ? ajaxPaginationResponse($products, USER_PRODUCTS_PAGINATION, PRODUCTS_TABLE, [PRODUCTS_PAGINATION_ROUTE => $products_pagination_route])
            : showView(USER_HOME_VIEW, compact(PRODUCTS_TABLE, SERVICES, PRODUCTS_PAGINATION_ROUTE));
    }
}
