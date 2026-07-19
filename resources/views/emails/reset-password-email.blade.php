@component('mail::message')

    <h2 class="fs-5 fw-600">Hi {{ $user->{FULL_NAME} }},</h2>
    <p>You are receiving this email because we received a password reset request for your account.</p>
    <div class="col-3 d-table button" align="center">
        <a href="{{route(RESET_PASSWORD_USER, [TOKEN => $token, EMAIL => $user->{EMAIL}])}}" type="button" role="link" class="btn d-table-cell fw-500 text-center vertical-center">{{capitalizeAll(RESET_PASSWORD)}}</a>
    </div>
    <p>This password reset link will be expired in 60 minutes.</p>
    <p>If you did not request a password reset, no further action is required.</p>
    <p>Regards,</p>
    <p>{{ config('app.name') }} Fashion</p>
    <hr/>
    <p>If you are having trouble clicking the "{{capitalizeAll(RESET_PASSWORD)}}" button, copy and paste the URL below into your web browser:</p>
    <p>{{route(RESET_PASSWORD_USER, [TOKEN => $token, EMAIL => $user->{EMAIL}])}}</p>

@endcomponent
