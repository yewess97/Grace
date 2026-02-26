<?php

namespace App\Services;

use App\Http\Requests\AuthRequest;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use App\Traits\Login\LoginHelpers;
use App\Traits\Login\RememberMeExpiration;
use App\Traits\Login\ThrottlesLogins;
use Carbon\Carbon;
use Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Psr\SimpleCache\InvalidArgumentException as CacheInvalidArgumentException;
use InvalidArgumentException;
use Laravel\Socialite\Facades\Socialite;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class AuthService {
    use LoginHelpers, RememberMeExpiration, ThrottlesLogins;

    /**
     * Register a new user.
     *
     * @return void
     * @throws ValidationException|CacheInvalidArgumentException
     */
    final public function registerUser(): void
    {
        /**  @var mixed $user */
        $user = storeOrUpdateUser(REGISTER);

        auth()->guard()->login($user);
    }

    /**
     * Login the user.
     *
     * @return JsonResponse|Response|HttpResponse
     * @throws ValidationException
     */
    final public function loginUser(): JsonResponse|Response|HttpResponse
    {
        $login_request = new AuthRequest(LOGIN, USER_MODEL, LOGIN_ATTRIBUTES);

        validateAttributes($login_request, LOGIN);

        [$email, $password] = LOGIN_ATTRIBUTES;

        [$email_value, $password_value] = $login_request->dataValues();

        $credentials_attributes = [
            $email    => $email_value,
            $password => $password_value,
        ];

        if (method_exists($this, 'hasTooManyLoginAttempts') && $this->hasTooManyLoginAttempts($email_value)) {
            $this->fireLockoutEvent();

            return $this->sendLockoutResponse($email_value);
        }

        if ($this->attemptLogin($credentials_attributes)) {
            if (request()?->hasSession()) {
                request()?->session()->put(AUTH.'.'.PASSWORD.'_confirmed_at', time());
            }

            if (request()?->input('remember')) {
                $this->setRememberMeExpiration();
            }

            return $this->sendLoginResponse($email_value);
        }

        $this->incrementLoginAttempts($email_value);

        return $this->sendFailedLoginResponse();
    }

    /**
     * Redirect the user to the social provider authentication page.
     *
     * @param string $provider
     * @return JsonResponse
     * @throws InvalidArgumentException|RuntimeException
     */
    final public function redirectToSocialProvider(string $provider): JsonResponse
    {
        if (!in_array($provider, LOGIN_SOCIAL_PROVIDERS, true)) {
            throw new InvalidArgumentException("The provider *$provider* is not supported");
        }

        if (!config("services.$provider")
            || !config("services.$provider.client_id")
            || !config("services.$provider.client_secret")
            || !config("services.$provider.redirect")
        )
        {
            throw new RuntimeException("The provider *$provider* is not configured properly");
        }

        return responseWithData(['redirect_to' => Socialite::driver($provider)->redirect()->getTargetUrl()]);
    }

    /**
     * Handle the callback from the social provider after authentication.
     *
     * @param string $provider
     * @return RedirectResponse
     */
    final public function handleSocialProviderCallback(string $provider): RedirectResponse
    {
        $social_user = Socialite::driver($provider)->user();

        if (!$social_user) {
            return to_route(LOGIN)
                ->with('loginSocialError', "Failed to authenticate with the provider *$provider*")
                ->setStatusCode(HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        $social_id = collectionId($provider);

        $user = User::query()
            ->where($social_id, $social_user->getId())
            ->first(USER_SELECTED_ATTRIBUTES);

        if ($user) {
            /**  @var mixed $user */
            auth()->guard()->login($user);

            return redirect()->intended();
        }

        $new_or_current_user = User::updateOrCreate(
            [EMAIL => $social_user->getEmail()],
            [
                FIRST_NAME => str($social_user->getName())->before(' ')->value(),
                LAST_NAME  => str($social_user->getName())->after(' ')->value(),
                PASSWORD   => bcrypt(Str::random(16)), // Temporary password
                ROLE       => 0, // Default role
                $social_id => $social_user->getId(),
            ]
        );

        auth()->guard()->login($new_or_current_user);

        return redirect()->intended();
    }

    /**
     * Logout the user.
     *
     * @return void
     */
    final public function logoutUser(): void
    {
        $guard = $this->guard();
        $user = $guard->user();

        $guard->logout();

        if ($user) {
            cache()->forget('is_online_'.$user->getKey());
        }

        request()?->session()->invalidate();
        request()?->session()->regenerateToken();
    }


    /**
     * Mailing the user to reset his/her password.
     *
     * @return bool
     * @throws ValidationException|ModelNotFoundException|RuntimeException
     */
    final public function forgotPasswordUser(): bool
    {
        $forgot_password_attributes = [LOGIN_ATTRIBUTES[0]];

        $forgot_password_request = new AuthRequest(FORGOT_PASSWORD, USER_MODEL, $forgot_password_attributes);

        validateAttributes($forgot_password_request, FORGOT_PASSWORD);

        [$email] = $forgot_password_attributes;

        [$email_value] = $forgot_password_request->dataValues();

        $token = Hash::make(Str::random(64));
        $user  = User::query()->whereEmail($email_value)->first(USER_SELECTED_ATTRIBUTES);

        if (!$user) {
            throw new ModelNotFoundException("The ".USER_MODEL." with this ".EMAIL." *$email_value* is not found");
        }

        DB::table(PASSWORD_RESETS_TABLE)->insert([
            $email   => $email_value,
            TOKEN    => $token,
            DATES[0] => Carbon::now(),
        ]);

        $reset_password_data = [
            TOKEN      => $token,
            USER_MODEL => $user,
        ];

        try {
            Mail::to($email_value)->send(new ResetPasswordMail($reset_password_data));
            return true;
        }
        catch (RuntimeException) {
            throw new RuntimeException("Failed to send the ".EMAIL." to reset the ".PASSWORD."!");
        }
    }

    /**
     * Reset user's password.
     *
     * @return bool
     * @throws ValidationException|InvalidArgumentException|ModelNotFoundException
     */
    final public function resetPasswordUser(): bool
    {
        $reset_password_request = new AuthRequest(RESET_PASSWORD, USER_MODEL, RESET_PASSWORD_ATTRIBUTES);

        validateAttributes($reset_password_request, RESET_PASSWORD);

        [$email, $token, $password] = RESET_PASSWORD_ATTRIBUTES;

        [$email_value, $token_value, $password_value] = $reset_password_request->dataValues();

        $reset_entry = DB::table(PASSWORD_RESETS_TABLE)
            ->where($email, $email_value)
            ->first([$token]);

        if (!$reset_entry) {
            throw new InvalidArgumentException("Invalid or expired ".PASSWORD." reset request");
        }

        if ($token_value !== $reset_entry->{$token}) {
            throw new InvalidArgumentException("Invalid ".PASSWORD." reset token");
        }

        $user = User::query()->whereEmail($email_value)->first(USER_SELECTED_ATTRIBUTES);

        if (!$user) {
            throw new ModelNotFoundException("The ".USER_MODEL." with this ".EMAIL." *$email_value* is not found");
        }

        $user->update([
            $password => bcrypt($password_value)
        ]);

        DB::table(PASSWORD_RESETS_TABLE)->whereEmail($email_value)->delete();

        return true;
    }
}
