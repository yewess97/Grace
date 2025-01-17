@extends(key(viewLayoutTitle(USER_MODEL)), current(viewLayoutTitle(USER_MODEL)))

@section('content')

    {{-- Reset Password Main --}}
    <main role="main" class="log-reg-main py-6">
        <div class="container">
            <div class="row col-12 justify-content-center align-items-center">
                <section class="col-4">
                    <div class="box-content border rounded">
                        <form action="{{route(RESET_PASSWORD_USER)}}" method="post" role="form" id="reset_password_form" class="grace-form auth-form row mt-2">
                            @csrf
                            {{-- Form Header --}}
                            <article class="grace-form-header row col-12 text-center">
                                <h2 class="fs-4 fw-600">Reset your {{PASSWORD}}</h2>
                            </article>
                            {{-- Form Body --}}
                            <article class="grace-form-body row col-12">
                                {{-- Email --}}
                                <input type="hidden" name="reset_password_user_email" value="{{$email}}">
                                {{$reset_password_user_error(EMAIL)}}

                                {{-- Token --}}
                                <input type="hidden" name="reset_password_user_token" value="{{$token}}">
                                {{$reset_password_user_error(TOKEN)}}

                                {{-- Password --}}
                                <div class="reset-password-user-password">
                                    <div class="form-outline col-12">
                                        <input type="password" name="reset_password_user_password" id="reset_password_user_password" class="form-control fs-7 rounded-2" min="8" autocomplete="on" aria-required="true" autofocus="autofocus">
                                        <label for="reset_password_user_password" class="form-label">
                                            <sup class="me-1">*</sup>{{ucfirst(PASSWORD)}}
                                        </label>
                                    </div>
                                    {{$reset_password_user_error(PASSWORD)}}
                                </div>

                                {{-- Password Confirmation --}}
                                <div class="reset-password-user-password-confirmation">
                                    <div class="form-outline col-12">
                                        <input type="password" name="reset_password_user_password_confirmation" id="reset_password_user_password_confirmation" class="form-control fs-7 rounded-2" min="8" autocomplete="on" aria-required="true" autofocus="autofocus">
                                        <label for="reset_password_user_password_confirmation" class="form-label">
                                            <sup class="me-1">*</sup>{{capitalizeAll(PASSWORD_CONFIRMATION)}}
                                        </label>
                                    </div>
                                    {{$reset_password_user_error(PASSWORD_CONFIRMATION)}}
                                </div>

                                {{-- Submit --}}
                                <div class="form-group">
                                    <button type="submit" role="button" title="Reset" class="btn btn-block">Reset</button>
                                </div>
                            </article>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </main>
@endsection
