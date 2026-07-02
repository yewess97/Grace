@extends(key(viewLayoutTitle(USER_MODEL)), current(viewLayoutTitle(USER_MODEL)))

@section('content')

    {{-- Payment Main --}}
    <main role="main" class="payment-main py-6">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="box-content py-4 border rounded">
                        {{-- Payment Policy Title --}}
                        <article class="box-title">
                            <h2 class="fs-9 fw-600 text-center">{{capitalizeAll(PAYMENT.' policy')}}</h2>
                        </article>
                        {{-- Payment Policy Content --}}
                        <article class="payment-content">
                            <ul role="list" class="payment-questions-list">
                                @foreach ($payment_inquiries as $payment_inquiry)
                                    <li role="listitem">
                                        <h3 class="payment-question fs-6 text-white rounded">{{$payment_inquiry->question}}</h3>
                                        {!! $payment_inquiry->answer !!}
                                    </li>
                                @endforeach
                            </ul>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </main>

@endsection
