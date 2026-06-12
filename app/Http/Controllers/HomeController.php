<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\SettingsService;
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
        $products_ids = cache()->remember(HOME_PRODUCTS, now()->addMinutes(30), static fn() =>
            Product::query()->mostSelling()
                ->pluck(ID)
                ->toArray()
            ?:
            Product::query()
                ->pluck(ID)
                ->toArray()
        );

        $products = paginateWithFallback(Product::class, $products_ids);

        $services                  = SettingsService::getHomeServices();
        $products_pagination_route = HOME;

//        $services = SettingsService::getNavbarDropdowns([;
//            CATEGORIES_TABLE    => cache()->remember(CATEGORIES_TABLE, now()->addMinutes(60), static fn() =>
//                object_from_array(
//                    app(CategoriesController::class)->index()->getData()[CATEGORIES_TABLE]
//                )
//            ),
//            SUBCATEGORIES_TABLE => cache()->remember(SUBCATEGORIES_TABLE, now()->addMinutes(60), static fn() =>
//                object_from_array(
//                    app(SubcategoriesController::class)->index()->getData()[SUBCATEGORIES_TABLE]
//                )
//            ),
//        ]);

        return request()?->ajax()
            ? ajaxPaginationResponse($products, USER_PRODUCTS_PAGINATION, PRODUCTS_TABLE, [PRODUCTS_PAGINATION_ROUTE => $products_pagination_route])
            : showView(USER_HOME_VIEW, compact(PRODUCTS_TABLE, 'services', PRODUCTS_PAGINATION_ROUTE));
    }
}
