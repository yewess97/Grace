<?php

namespace App\Traits\Login;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Cache\RateLimiter;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

trait ThrottlesLogins
{
    protected int $maxAttempts  = 5;
    protected int $decayMinutes = 1;

    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param string $email
     * @return bool
     */
    final protected function hasTooManyLoginAttempts(string $email): bool
    {
        return $this->limiter()->tooManyAttempts($this->throttleKey($email), $this->maxAttempts);
    }

    /**
     * Increment the login attempts for the user.
     *
     * @param string $email
     * @return int
     */
    final protected function incrementLoginAttempts(string $email): int
    {
        return $this->limiter()->hit($this->throttleKey($email), $this->decayMinutes * 60);
    }

    /**
     * Redirect the user after determining they are locked out.
     *
     * @param string $email
     * @return Response
     * @throws ValidationException
     */
    final protected function sendLockoutResponse(string $email): Response
    {
        $seconds = $this->limiter()->availableIn($this->throttleKey($email));

        throw ValidationException::withMessages([LOGIN_USER.'_'.MANY_ATTEMPTS => $seconds])
            ->status(Response::HTTP_TOO_MANY_REQUESTS);
    }

    /**
     * Clear the login locks for the given user credentials.
     *
     * @param string $email
     * @return void
     */
    final public function clearLoginAttempts(string $email): void
    {
        $this->limiter()->clear($this->throttleKey($email));
    }

    /**
     * Fire an event when a lockout occurs.
     *
     * @return array|null
     */
    final protected function fireLockoutEvent(): ?array
    {
        return event(new Lockout(request()));
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param string $email
     * @return string
     */
    final protected function throttleKey(string $email): string
    {
        return str(str($email)->lower().'|'.request()?->ip())->ascii();
    }

    /**
     * Get the rate limiter instance.
     *
     * @return RateLimiter
     */
    final protected function limiter(): RateLimiter
    {
        return app(RateLimiter::class);
    }
}
