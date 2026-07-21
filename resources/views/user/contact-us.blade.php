@extends(key(viewLayoutTitle(USER_MODEL)), current(viewLayoutTitle(USER_MODEL)))

@section('content')

    {{-- Contact Us Main --}}
    <main role="main" class="contact-us-main py-6">
        <div class="container">
            <div class="row">
                {{-- Contact Us --}}
                <div class="col">
                    <div class="box-content d-grid place-items-center gap-5 py-4 rounded-5">
                        {{-- Contact Us Title --}}
                        <article class="box-title text-center">
                            <h2 class="fs-9 fw-600">{{capitalizeAll(CONTACT_US)}}</h2>
                            <p class="mt-2">
                                We are here to help and answer any question you might have. Also
                                you can contact us by phone or email. <br> We look forward to hearing from you.
                            </p>
                        </article>
                        {{-- Contact Us Content --}}
                        <article class="contact-us-content row row-cols-1 row-cols-lg-2 align-items-baseline rounded-3">
                            <!----======= Left Side =======---->
                            <div class="col">
                                <form method="post" role="form" id="contact_us_form" class="row grace-form contact-us-form">
                                    @csrf
                                    {{-- Form Header --}}
                                    <article class="grace-form-header contact-us-content-header mb-4">
                                        <h3 class="fs-6 fw-600">Drop us a message</h3>
                                    </article>
                                    {{-- Form Body --}}
                                    <article class="grace-form-body row col-12">
                                        {{-- Name --}}
                                        <div class="add-contact-name">
                                            <div class="form-outline col-12">
                                                <input type="text" name="add_contact_name" id="add_contact_name" class="form-control fs-7 rounded-2" min="2" max="50" aria-required="true">
                                                <label for="add_contact_name" class="form-label">
                                                    <sup class="me-1">*</sup>Your {{ucfirst(NAME)}}
                                                </label>
                                            </div>
                                            {{formError(ADD, 'contact', NAME)}}
                                        </div>
                                        {{-- Email --}}
                                        <div class="add-contact-email">
                                            <div class="form-outline col-12">
                                                <input type="email" name="add_contact_email" id="add_contact_email" class="form-control fs-7 rounded-2" aria-required="true">
                                                <label for="add_contact_email" class="form-label">
                                                    <sup class="me-1">*</sup>Your {{ucfirst(EMAIL)}}
                                                </label>
                                            </div>
                                            {{formError(ADD, 'contact', EMAIL)}}
                                        </div>
                                        {{-- Message --}}
                                        <div class="add-contact-message">
                                            <div class="form-outline col-12">
                                                <textarea name="add_contact_message" id="add_contact_message" class="form-control fs-7" rows="4" aria-required="true"></textarea>
                                                <label for="add_contact_message" class="form-label textarea-label">
                                                    <sup class="me-1">*</sup>Message
                                                </label>
                                            </div>
                                            {{formError(ADD, 'contact', 'message')}}
                                        </div>
                                        {{-- Send Button --}}
                                        <div class="form-group col-12">
                                            <button type="submit" role="button" title="Send" class="btn col-12 col-md-auto d-flex justify-content-center align-items-center overflow-hidden">
                                                <span class="svg-wrapper-1 d-block">
                                                    <span class="svg-wrapper d-block">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                                            <path fill="none" d="M0 0h24v24H0z"></path>
                                                            <path fill="currentColor" d="M1.946 9.315c-.522-.174-.527-.455.01-.634l19.087-6.362c.529-.176.832.12.684.638l-5.454 19.086c-.15.529-.455.547-.679.045L12 14l6-8-8 6-8.054-2.685z"></path>
                                                        </svg>
                                                    </span>
                                                </span>
                                                <span class="ms-1 send">Send</span>
                                            </button>
                                        </div>
                                    </article>
                                </form>
                            </div>

                            <!----======= Right Side =======---->
                            <div class="col d-grid gap-4 pt-5 pt-lg-0 ps-lg-5">
                                {{-- Contact Us Info Header --}}
                                <article class="contact-us-content-header">
                                    <h3 class="fs-8 fw-600">Get in Touch</h3>
                                    <p class="mt-2">Feel free to contact us by phone or email, or visit our office.</p>
                                </article>
                                {{-- Contact Us Info Content --}}
                                <article class="contact-us-info-content d-grid gap-3">
                                    {{-- Addresses --}}
                                    <article class="info d-flex align-items-center gap-3">
                                        {{-- Icon --}}
                                        <div role="img" class="info-icon" aria-label="{{ucfirst(ADDRESS_MODEL)}} Icon">
                                            <i class="fa-solid fa-street-view fs-5"></i>
                                        </div>
                                        {{-- Details --}}
                                        <div class="info-details">
                                            <h4 class="fw-600">{{ucfirst(ADDRESS_MODEL)}}</h4>
                                            <p class="mt-1">Al-Mohandiseen 71511 Street, Asyut, Egypt</p>
                                        </div>
                                    </article>
                                    {{-- Phone --}}
                                    <article class="info d-flex align-items-center gap-3">
                                        {{-- Icon --}}
                                        <div role="img" class="info-icon" aria-label="Phone Icon">
                                            <i class="fa-solid fa-phone-alt fs-5"></i>
                                        </div>
                                        {{-- Details --}}
                                        <div class="info-details">
                                            <h4 class="fw-600">Phone</h4>
                                            <p class="mt-1">+20 101 183 6243</p>
                                        </div>
                                    </article>
                                    {{-- Email --}}
                                    <article class="info d-flex align-items-center gap-3">
                                        {{-- Icon --}}
                                        <div role="img" class="info-icon" aria-label="{{ucfirst(EMAIL)}} Icon">
                                            <i class="fa-solid fa-envelope fs-5"></i>
                                        </div>
                                        {{-- Details --}}
                                        <div class="info-details">
                                            <h4 class="fw-600">{{ucfirst(EMAIL)}}</h4>
                                            <p class="mt-1">yewess97@gmail.com</p>
                                        </div>
                                    </article>
                                </article>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </main>

@endsection



@section('user-js-links')
    <script type="application/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
    <script type="module" src="{{asset('js/contact-us.js')}}"></script>
@endsection

@section('user-js')
    <script nonce="{{$nonce}}" type="application/javascript">
        (function(){
            emailjs.init("Tw1jJXLceJmoL24nI");
        })();
    </script>
@endsection
