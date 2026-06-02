@extends(key(viewLayoutTitle(USER_MODEL)), current(viewLayoutTitle(USER_MODEL)))

@section('content')

    {{-- Forgot Password Main --}}
    <main role="main" class="log-reg-main py-6">
        <div class="container">
            <div class="row col-12 justify-content-center align-items-center">
                <section class="col-12 col-lg-4 col-md-7">
                    <div class="box-content border rounded">
                        @backTo(LOGIN)
                        <form action="{{route(FORGOT_PASSWORD_USER)}}" method="post" role="form" id="forgot_password_form" class="grace-form auth-form row mt-2">
                            @csrf
                            {{-- Form Header --}}
                            <article class="grace-form-header row col-12 text-center">
                                <h2 class="fs-4 fw-600">Reset your {{PASSWORD}}</h2>
                                <p>We will {{EMAIL}} you to reset your {{PASSWORD}}</p>
                            </article>
                            {{-- Form Body --}}
                            <article class="grace-form-body row col-12">
                                {{-- Email --}}
                                <div class="forgot-password-user-email">
                                    <div class="form-outline col-12">
                                        <input type="email" name="forgot_password_user_email" id="forgot_password_user_email" class="form-control fs-7 rounded-2" value="{{old(EMAIL)}}" autocomplete="email" aria-required="true" autofocus="autofocus">
                                        <label for="forgot_password_user_email" class="form-label">
                                            <sup class="me-1">*</sup>{{ucfirst(EMAIL)}}
                                        </label>
                                    </div>
                                    {{$forgot_password_user_error(EMAIL)}}
                                </div>

                                {{-- Submit --}}
                                <div class="form-group">
                                    <button type="submit" role="button" title="Send {{ucfirst(EMAIL)}}" class="btn btn-block auth-btn">Send {{ucfirst(EMAIL)}}</button>
                                </div>
                            </article>

                            {{-- Form Footer --}}
                            <article class="grace-form-footer col-12 text-center">
                                <a href="{{route(LOGIN)}}" role="link" class="fw-500 text-main">Cancel</a>
                            </article>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </main>

@endsection
