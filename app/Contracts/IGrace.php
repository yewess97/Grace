<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Casts\Attribute;

interface IGrace
{
    /**
     * Get the data of the specified model.
     *
     * @return Attribute
     */
    public function data(): Attribute;
}
