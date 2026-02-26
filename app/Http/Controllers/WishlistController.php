<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Services\WishlistService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Psr\SimpleCache\InvalidArgumentException as CacheInvalidArgumentException;
use Throwable;

class WishlistController extends Controller
{
    /**
     * Wishlist Controller Constructor.
     *
     * @param WishlistService $wishlistService
     * @return void
     */
    final public function __construct(private readonly WishlistService $wishlistService){}

    /**
     * Display the wishlist resource.
     *
     * @return Application|Factory|View|JsonResponse
     * @throws Throwable
     */
    final public function index(): Application|Factory|View|JsonResponse
    {
        $user_wishlist_items = userCollectionsData()[WISHLIST_MODEL][ITEMS];

        return request()?->ajax()
            ? ajaxPaginationResponse($user_wishlist_items, WISHLIST_PAGINATION, USER_WISHLIST_ITEMS)
            : showView(USER_WISHLIST_VIEW, compact(USER_WISHLIST_ITEMS));
    }


    /**
     * Store a wishlist.
     *
     * @return JsonResponse
     * @throws ModelNotFoundException|ValidationException|CacheInvalidArgumentException|Throwable
     */
    final public function store(): JsonResponse
    {
        $this->wishlistService->createWishlist();

        $last_page = getLastPage(new Wishlist(), 5);

        return responseWithData($this->wishlistService->getWishlistData([LAST_PAGE => $last_page]));
    }

    /**
     *  Delete a specified wishlist.
     *
     * @param Wishlist $wishlist
     * @return JsonResponse
     * @throws ModelNotFoundException|CacheInvalidArgumentException|Throwable
     */
    final public function destroy(Wishlist $wishlist): JsonResponse
    {
        $wishlist_deleted = $this->wishlistService->deleteWishlist($wishlist);

        $compact_vars = $this->wishlistService->getWishlistData();

        return responseWithData($compact_vars);
    }


    /**
     * Delete all user's wishlists.
     *
     * @return JsonResponse
     * @throws CacheInvalidArgumentException|Throwable
     */
    final public function destroyMultiple(): JsonResponse
    {
        $this->wishlistService->deleteAllWishlists();

        $row = view(USER_WISHLIST_VIEW)->render();

        return responseWithData(array_merge(array_diff_key($this->wishlistService->getWishlistData(), [ROW]), compact(ROW)));
    }
}
