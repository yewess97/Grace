<?php

namespace App\Contracts;

interface HasImages
{
    /**
     * Configure all image properties for the model.
     *
     * @return array
     */
    public function imageProperties(): array;
}
