<?php

namespace App\Traits\Login;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

trait RememberMeExpiration
{
    /**
     * Set default minutes expiration.
     *
     * @var int
     */
    protected int $minutes_expiration = 14400; //equivalent of 10 days

    /**
     * Customize the user logged remember me expiration.
     *
     * @return void
     */
    final public function setRememberMeExpiration(): void
    {
        Cookie::queue($this->getRememberMeSessionName(), encrypt($this->setRememberMeValue()), $this->minutes_expiration);
    }

    /**
     * Generate remember me value.
     *
     * @return string
     */
    final protected function setRememberMeValue(): string
    {
        return auth()->user()?->{ID}.'|'.auth()->user()?->{'remember_token'}.'|'.auth()->user()?->{PASSWORD};
    }

    /**
     * Get remember me session name.
     *
     * @return string
     */
    final protected function getRememberMeSessionName(): string
    {
        return Auth::getRecallerName();
    }
}
