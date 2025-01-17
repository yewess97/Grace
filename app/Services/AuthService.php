<?php

namespace App\Services;

use App\Http\Requests\AuthRequest;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use App\Traits\Login\LoginHelpers;
use App\Traits\Login\RememberMeExpiration;
use App\Traits\Login\ThrottlesLogins;
use Cache;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Mail\SentMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class AuthService {
    use LoginHelpers, RememberMeExpiration, ThrottlesLogins;

    /**
     * Register a new user.
     *
     * @return null
     * @throws ValidationException
     */
    final public function registerUser(): null
    {
        $user = storeOrUpdateUser(REGISTER);

        return auth()->login($user);
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
     * Logout the user.
     */
    final public function logoutUser(): null
    {
        $user = $this->guard()->user();

        $this->guard()->logout();

        if ($user) {
            $user->{'remember_token'} = null;
            $user->save();

            Cache::delete('is_online_'.$user->{ID});
        }

        return request()?->session()
            ->flush()
            ?->invalidate()
            ->regenerateToken();
    }

    /**
     * Mailing the user to reset his/her password.
     *
     * @return SentMessage
     * @throws ValidationException
     */
    final public function forgotPasswordUser(): SentMessage
    {
        $forgot_password_attributes = [LOGIN_ATTRIBUTES[0]];

        $forgot_password_request = new AuthRequest(FORGOT_PASSWORD, USER_MODEL, $forgot_password_attributes);

        validateAttributes($forgot_password_request, FORGOT_PASSWORD);

        [$email] = $forgot_password_attributes;

        [$email_value] = $forgot_password_request->dataValues();

        $token = encrypt(Str::random(64));
        $user = User::query()->whereEmail($email_value)->first(USER_SELECTED_ATTRIBUTES);

        DB::table(PASSWORD_RESETS_TABLE)->insert([
            $email   => $email_value,
            TOKEN    => $token,
            DATES[0] => Carbon::now(),
        ]);

        $reset_password_data = [
            TOKEN      => $token,
            USER_MODEL => $user,
        ];

        return Mail::to($email_value)->send(new ResetPasswordMail($reset_password_data));
    }

    /**
     * Reset user's password.
     *
     * @return int
     * @throws ValidationException
     */
    final public function resetPasswordUser(): int
    {
        $reset_password_request = new AuthRequest(RESET_PASSWORD, USER_MODEL, RESET_PASSWORD_ATTRIBUTES);

        validateAttributes($reset_password_request, RESET_PASSWORD);

        [$email, $token, $password] = RESET_PASSWORD_ATTRIBUTES;

        [$email_value, $token_value, $password_value] = $reset_password_request->dataValues();

        User::query()->whereEmail($email_value)->update([
            $password => bcrypt($password_value)
        ]);

        return DB::table(PASSWORD_RESETS_TABLE)->where([
            $email => $email_value,
            $token => $token_value,
        ])->delete();
    }
}
