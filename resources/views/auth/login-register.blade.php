@extends(key(viewLayoutTitle(USER_MODEL)), current(viewLayoutTitle(USER_MODEL)))

@section('content')

    {{-- Login/Register Main --}}
    <main role="main" class="log-reg-main py-6">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="main-sides row col-12 justify-content-center align-items-center">
                    <!----======= Left Side =======---->
                    <section class="left-side col">
                        <div class="box-content border rounded">
                            <form action="{{route($auth_action === REGISTER ? REGISTER_USER : LOGIN_USER)}}" method="post" role="form" id="{{$auth_action}}_form" class="grace-form auth-form row" data-loading_spinner="{{imageSource('loading2.png')}}">
                                @csrf
                                {{-- Form Header --}}
                                <article class="grace-form-header row col-12 text-center">
                                    <h2 class="fs-3 fw-600">{{$auth_action === REGISTER ? 'Create Account' : ucfirst(LOGIN)}}</h2>
                                    <p>Please {{$auth_action}} below account detail</p>
                                </article>
                                {{-- Form Body --}}
                                <article class="grace-form-body row col-12">
                                    @if($auth_action === REGISTER)
                                        {{-- First Name --}}
                                        <div class="register-user-first-name col-12">
                                            <div class="form-outline">
                                                <input type="text" name="register_user_first_name" id="register_user_first_name" class="form-control fs-7 rounded-2" min="2" max="50" aria-required="true">
                                                <label for="register_user_first_name" class="form-label">
                                                    <sup class="me-1">*</sup>{{capitalizeFirst(FIRST_NAME)}}
                                                </label>
                                            </div>
                                            {{$register_user_error(FIRST_NAME)}}
                                        </div>

                                        {{-- Last Name --}}
                                        <div class="register-user-last-name col-12">
                                            <div class="form-outline">
                                                <input type="text" name="register_user_last_name" id="register_user_last_name" class="form-control fs-7 rounded-2" min="2" max="50" aria-required="true">
                                                <label for="register_user_last_name" class="form-label">
                                                    <sup class="me-1">*</sup>{{capitalizeFirst(LAST_NAME)}}
                                                </label>
                                            </div>
                                            {{$register_user_error(LAST_NAME)}}
                                        </div>
                                    @else
                                        {{-- Many Login Attempts Error --}}
                                        {{$login_user_error(MANY_ATTEMPTS)}}
                                    @endif

                                    {{-- Email --}}
                                    <div class="{{$auth_action}}-user-email col-12">
                                        <div class="form-outline">
                                            <input type="email" name="{{$auth_action}}_user_email" id="{{$auth_action}}_user_email" class="form-control fs-7 rounded-2" aria-required="true">
                                            <label for="{{$auth_action}}_user_email" class="form-label">
                                                <sup class="me-1">*</sup>{{ucfirst(EMAIL)}}
                                            </label>
                                        </div>
                                        {{$auth_action === REGISTER ? $register_user_error(EMAIL) : $login_user_error(EMAIL)}}
                                    </div>

                                    {{-- Password --}}
                                    <div class="{{$auth_action}}-user-password col-12">
                                        <div class="form-outline">
                                            <input type="password" name="{{$auth_action}}_user_password" id="{{$auth_action}}_user_password" class="form-control fs-7 rounded-2" min="8" aria-required="true">
                                            <label for="{{$auth_action}}_user_password" class="form-label">
                                                <sup class="me-1">*</sup>{{ucfirst(PASSWORD)}}
                                            </label>
                                        </div>
                                        {{$auth_action === REGISTER ? $register_user_error(PASSWORD) : $login_user_error(PASSWORD)}}
                                        @if($auth_action === LOGIN) {{$login_user_error(INVALID_CREDENTIALS)}} @endif
                                    </div>

                                    @if($auth_action === LOGIN)
                                        <div class="login-user-actions col-12 d-flex justify-content-between align-items-center mb-2">
                                            {{-- Remember Me --}}
                                            <div class="form-group">
                                                <label for="remember" class="remember-check position-relative d-flex align-items-center user-select-none">
                                                    <input type="checkbox" role="checkbox" name="remember" id="remember" aria-labelledby="remember_me" @checked(old('remember'))>
                                                    <span role="checkbox" class="custom-check position-absolute start-0" aria-labelledby="remember_me"></span>
                                                    <span id="remember_me" class="remember-title text-capitalize">Remember me</span>
                                                </label>
                                            </div>
                                            
                                            {{-- Forgot Password --}}
                                            <a href="{{route(FORGOT_PASSWORD)}}" role="link" class="fw-500 text-main">
                                                {{capitalizeFirst(FORGOT_PASSWORD)}}?
                                            </a>
                                        </div>
                                    @endif

                                    {{-- Submit Button --}}
                                    <div class="form-group">
                                        <button type="submit" role="button" title="{{ucfirst($auth_action)}}" class="btn {{$auth_action}}-btn d-flex justify-content-center align-items-center gap-2 w-100">
                                            <span>{{ucfirst($auth_action)}}</span>
                                        </button>
                                    </div>
                                </article>

                                @if($auth_action === LOGIN)
                                    {{-- Social Login --}}
                                    <article class="grace-form-footer col-12 text-center">
                                        <h1 class="title d-flex align-items-center mb-3">
                                            <span>{{strtoupper('or '.LOGIN.' with')}}</span>
                                        </h1>
                                        <div class="social-login row justify-content-center align-items-center gap-3">
                                            @foreach (LOGIN_SOCIAL_PROVIDERS as $provider)
                                                <a href="{{route('social_login', $provider)}}" title="{{ucfirst(LOGIN)}} using {{ucfirst($provider)}} Account" class="social-login-provider {{$loop->last ? 'col-12' : 'col'}} position-relative d-flex justify-content-center align-items-center gap-2 rounded-1">
                                                    <img src="{{imageSource("socialite/$provider-login.png")}}" alt="{{ucfirst($provider)}} Logo" class="social-login-icon" width="18">
                                                    <span class="fw-500">{{ucfirst($provider)}}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </article>
                                @endif
                            </form>
                        </div>
                    </section>

                    <!----======= Right Side =======---->
                    <section class="log-reg-right row col">
                        <div class="log-reg-title row w-100 text-center text-main">
                            <h6 class="fs-6 fw-600">{{$auth_action === REGISTER ? "Already" : "Don't"}} have an account?</h6>
                            <a href="{{route($auth_action === REGISTER ? LOGIN_USER : REGISTER_USER)}}" type="button" role="link" title="{{$auth_action === REGISTER ? ucfirst(LOGIN) : "Create Account"}}" class="position-relative w-100 fw-500 lh-sm rounded overflow-hidden">
                                {{$auth_action === REGISTER ? ucfirst(LOGIN) : "Create Account"}}
                            </a>
                        </div>
                        <div class="log-reg-terms-privacy fs-7 text-main">
                            <p>
                                <span><sup>*</sup></span>
                                <a href="javascript:;" role="link" class="fw-500">Terms and Conditions</a>
                            </p>
                            <p>
                                <span>Your privacy and security are important to us. For more information on how we use your data read our</span>
                                <a href="javascript:;" role="link" class="fw-500">privacy policy</a>
                            </p>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </main>

@endsection
