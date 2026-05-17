<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Psr\SimpleCache\InvalidArgumentException as CacheInvalidArgumentException;

interface ServiceData
{
    /**
     * Validate attributes of the request,
     * and return the collection's request.
     *
     * @param string $operation
     * @param array $extra
     * @return FormRequest
     * @throws ValidationException
     */
    public function validateRequest(string $operation, array $extra = []): FormRequest;

    /**
     * Create or Update a collection's database record,
     * and store the collection's images if there.
     *
     * @param FormRequest $collectionRequest
     * @param array $extra
     * @return Model|JsonResponse|array
     */
    public function createOrUpdateCollection(FormRequest $collectionRequest, array $extra): Model|JsonResponse|array;

    /**
     * Forget the collection's cache and related chaches.
     *
     * @param Model|null $model
     * @return void
     * @throws CacheInvalidArgumentException
     */
    public function forgetCollectionCache(Model $model = null): void;
}
