<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Throwable;

class HomeController extends Controller
{
    /**
     * Display the home resource.
     *
     * @return Application|Factory|View|string
     * @throws Throwable
     */
    final public function index(): Application|Factory|View|string
    {
        $products = Product::fastPaginate(16, PRODUCT_ITEM_ATTRIBUTES);

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

        if (request()?->ajax()) {
            return view(USER_PRODUCTS_PAGINATION, compact(PRODUCTS_TABLE, PRODUCTS_PAGINATION_ROUTE));
        }

        return showView(USER_HOME_VIEW, compact(PRODUCTS_TABLE, SERVICES, PRODUCTS_PAGINATION_ROUTE));
    }
}
