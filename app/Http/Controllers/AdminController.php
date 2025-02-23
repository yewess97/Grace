<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\Subcategory;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Throwable;

class AdminController extends Controller
{
    private array $id_name;
    private string|null $status;

    /**
     * Admin Dashboard Constructor.
     *
     * @return void
     */
    final public function __construct(private readonly DashboardService $dashboardService)
    {
        $this->id_name = [ID, NAME];
        $this->status = request()?->input(STATUS);
    }
    /**
     * Dashboard.
     *
     * @return Application|Factory|View|string
     * @throws Throwable
     */
    final public function dashboard(): Application|Factory|View|string
    {
        return $this->dashboardService->dashboardData();
    }

    /**
     * Categories.
     *
     * @return Application|Factory|View
     */
    final public function categories(): Application|Factory|View
    {
        $categories = Category::when($this->status === TRASHED, static fn($query) => $query->onlyTrashed())
            ->fastPaginate(16);

        $add_category_error    = static fn(string $attributeName) => formError(ADD, CATEGORY_MODEL, $attributeName);
        $update_category_error = static fn(string $attributeName) => formError(UPDATE, CATEGORY_MODEL, $attributeName);

        return view(ADMIN_CATEGORIES_VIEW, compact(CATEGORIES_TABLE, ADD_CATEGORY_ERROR, UPDATE_CATEGORY_ERROR));
    }

    /**
     * Subcategories.
     *
     * @return Application|Factory|View
     */
    final public function subcategories(): Application|Factory|View
    {
        $subcategories = Subcategory::with($this->relatedCategories())
            ->when($this->status === TRASHED, static fn($query) => $query->onlyTrashed())
            ->fastPaginate(16);

        $categories = Category::all($this->id_name);

        $add_subcategory_error    = static fn(string $attributeName) => formError(ADD,    SUBCATEGORY_MODEL, $attributeName);
        $update_subcategory_error = static fn(string $attributeName) => formError(UPDATE, SUBCATEGORY_MODEL, $attributeName);

        return view(ADMIN_SUBCATEGORIES_VIEW, compact(SUBCATEGORIES_TABLE, CATEGORIES_TABLE, ADD_SUBCATEGORY_ERROR, UPDATE_SUBCATEGORY_ERROR));
    }

    /**
     * Products.
     *
     * @return Application|Factory|View
     */
    final public function products(): Application|Factory|View
    {
        $id_name_attributes = $this->id_name;

        $products = Product::query()->latest()
            ->with([
                ...$this->relatedCategories(),
                SUBCATEGORIES_TABLE => static fn(BelongsToMany $subcategory) => $subcategory->select($id_name_attributes)->withTrashed(),
                THUMB_IMAGES        => static fn(HasMany $thumbImage)  => $thumbImage->select(THUMB_IMAGE, PRODUCT_ID),
                SIZES               => static fn(HasMany $size)        => $size->select(SIZE, PRODUCT_ID),
            ])
            ->fastPaginate(16);

        $categories    = Category::all($id_name_attributes);
        $subcategories = Subcategory::all($id_name_attributes);
        $sizes         = PRODUCT_SIZE_ENUM;

        $add_product_error    = static fn(string $attributeName) => formError(ADD, PRODUCT_MODEL, $attributeName);
        $update_product_error = static fn(string $attributeName) => formError(UPDATE, PRODUCT_MODEL, $attributeName);

        return view(ADMIN_PRODUCTS_VIEW, compact(PRODUCTS_TABLE, CATEGORIES_TABLE, SUBCATEGORIES_TABLE, SIZES, ADD_PRODUCT_ERROR, UPDATE_PRODUCT_ERROR));
    }

    /**
     * Customers and Admins Users.
     *
     * @return RedirectResponse|Application|Factory|View|string
     * @throws Throwable
     */
    final public function users(): RedirectResponse|Application|Factory|View|string
    {
        $users = User::query()->fastPaginate(16);

        $roles = USER_ROLE_ENUM;

        $add_user_error     = static fn(string $attributeName) => formError(ADD,    USER_MODEL,  $attributeName);
        $update_user_error  = static fn(string $attributeName) => formError(UPDATE, USER_MODEL,  $attributeName);
        $filter_users_error = static fn(string $attributeName) => formError(FILTER, USERS_TABLE, $attributeName);

        if (request()?->ajax()) {
            return view(ADMIN_USERS_PAGINATION, compact(USERS_TABLE))->render();
        }

        return view(ADMIN_USERS_VIEW, compact(USERS_TABLE, pluralize(ROLE), ADD_USER_ERROR, UPDATE_USER_ERROR, FILTER_USERS_ERROR));
    }

    /**
     * Orders.
     *
     * @return RedirectResponse|Application|Factory|View|string
     * @throws Throwable
     */
    final public function orders(): RedirectResponse|Application|Factory|View|string
    {
        $last_valid_status = session('last_valid_status');

        if (!$this->status || !in_array((int) $this->status, array_values(ORDER_STATUS_ENUM), true)) {
            // Use the last valid status if available, otherwise, get the latest order status
            $redirect_status = $last_valid_status ?? Order::query()->latest()->first()?->{STATUS};

            return to_route(ADMIN_ORDERS_ROUTE, [STATUS => $redirect_status]);
        }

        session()->push('last_valid_status', $this->status);

        $orders = Order::query()->latest()
            ->whereStatus($this->status)
            ->fastPaginate(16);

        $statuses     = ORDER_STATUS_ENUM;
        $orders_title = key(array_intersect($statuses, (array) $this->status)).' '.ucfirst(ORDERS_TABLE);
        $order_status = current(array_intersect($statuses, (array) $this->status));

        $update_order_error  = static fn(string $attributeName) => formError(UPDATE, ORDER_MODEL,  $attributeName);
        $filter_orders_error = static fn(string $attributeName) => formError(FILTER, ORDERS_TABLE, $attributeName);

        if (request()?->ajax()) {
            return view(ADMIN_ORDERS_PAGINATION, compact(ORDERS_TABLE))->render();
        }

        return view(ADMIN_ORDERS_VIEW, compact(ORDERS_TABLE, pluralize(STATUS), ORDERS_TITLE, ORDER_MODEL.'_'.STATUS, UPDATE_ORDER_ERROR, FILTER_ORDERS_ERROR));
    }

    /**
     * Reviews.
     *
     * @return RedirectResponse|Application|Factory|View
     */
    final public function reviews(): RedirectResponse|Application|Factory|View
    {
        $rating = request()?->input(RATING);

        $last_valid_rating = session('last_valid_rating');

        if (!$rating || !in_array((int) $rating, array_values(REVIEW_RATING_ENUM), true)) {
            // Use the last valid rating if available, otherwise, get the latest review rating
            $redirect_rating = $last_valid_rating ?? Review::query()->latest()->first()?->{RATING};

            return to_route(ADMIN_REVIEWS_ROUTE, [RATING => $redirect_rating]);
        }

        session()->push('last_valid_rating', $rating);

        $reviews = Review::with([
                PRODUCT_MODEL => fn(BelongsTo $product) => $product->select($this->id_name),
                USER_MODEL    => static fn(BelongsTo $user)    => $user->select(USER_SELECTED_ATTRIBUTES),
            ])
            ->where(RATING, $rating)
            ->fastPaginate(16);

        $review_rating = current(array_intersect(REVIEW_RATING_ENUM, (array) $rating));

        $update_review_error = static fn(string $attributeName) => reviewData($attributeName, UPDATE);

        return view(ADMIN_REVIEWS_VIEW, compact(REVIEWS_TABLE, REVIEW_RATING, UPDATE_REVIEW_ERROR));
    }

    /**
     * Categories Relation.
     *
     * @return array
     */
    private function relatedCategories(): array
    {
        return [
            CATEGORIES_TABLE => fn(BelongsToMany $category) => $category->select($this->id_name)->withTrashed(),
        ];
    }
}
