@extends(key(viewLayoutTitle(USER_MODEL)), current(viewLayoutTitle(USER_MODEL)))

@section('content')

    {{-- About Us Main --}}
    <main role="main" class="about-us-main py-6">
        <div class="container">
            <div class="row">
                {{-- About Us --}}
                <div class="col-12">
                    <div class="box-content d-flex flex-column justify-content-center align-items-center gap-4 py-4 rounded-5">
                        {{-- About Us Title --}}
                        <article class="box-title">
                            <h2 class="fs-9 fw-600 text-center">{{capitalizeAll(ABOUT_US)}}</h2>
                            <p class="mt-2 text-center">
                                The best shop for selling all kinds of different clothes and accessories with the finest materials according to the highest quality standards
                            </p>
                        </article>
                        {{-- About Us Content --}}
                        <article class="about-us-content row row-cols-1 row-cols-md-2 rounded-3">
                            {{-- About Us Image --}}
                            <div class="col">
                                <img src="{{imageSource('about/about-us.png')}}" alt="Our Company" class="rounded">
                            </div>
                            {{-- About Us Company --}}
                            <div class="row col">
                                <div class="about-our-company d-flex flex-column justify-content-center gap-3 py-4">
                                    <h3 class="comp-title fs-10 fw-600">Our Company</h3>
                                    <p class="comp-desc">Our artisans craft all of Grace's garments and accessories according to the highest quality standards. Our latest collection is a combination of our designers' vision and our technical experts' inventions.</p>
                                    <ul role="list" class="comp-services d-grid gap-2 ps-3">
                                        <li role="listitem">Top quality products</li>
                                        <li role="listitem">Best customer service</li>
                                        <li role="listitem">30-day money-back guarantee</li>
                                    </ul>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
                {{-- Our Mission & Vision --}}
                <div class="col-12">
                    <div class="box-content row row-cols-1 row-cols-md-2 align-items-center text-center rounded-5">
                        {{-- Our Mission --}}
                        <article class="our-mission col">
                            <img src="{{imageSource('about/our-mission.png')}}" alt="Our Mission" class="w-auto">
                            <h3 class="our-mission-title my-2 fs-10 fw-500 text-uppercase">Our Mission</h3>
                            <p class="our-mission-desc w-75 mx-auto fs-6">
                                Our mission is to provide the best quality products and services to our customers. We are committed to providing the best customer service and the best shopping experience.
                            </p>
                        </article>
                        {{-- Our Vision --}}
                        <article class="our-vision col">
                            <img src="{{imageSource('about/our-vision.png')}}" alt="Our Vision" class="w-auto">
                            <h3 class="our-vision-title my-2 fs-10 fw-500 text-uppercase">Our Vision</h3>
                            <p class="our-vision-desc w-75 mx-auto fs-6">
                                Our vision is to be the best online store in the world. We are committed to providing the best customer service and the best shopping experience.
                            </p>
                        </article>
                    </div>
                </div>
                {{-- About New --}}
                <div class="col-12">
                    <div class="box-content about-new position-relative d-grid place-items-center rounded-5" style="background: url('{{imageSource('about/about-new.png')}}'), fixed no-repeat center center/cover">
                        <div class="about-new-content col-12 col-md-6 d-grid place-items-center gap-4 text-center">
                            <h2 class="about-new-title fs-4 fw-600 text-white">New In!</h2>
                            <p class="fs-6 text-white">
                                Our latest collection is a combination of our designer's vision and our technical expert's invention; take Dobrada, for example, an eye-catching lightweight hybrid with boat shoe detailing.
                            </p>
                            <a href="{{route(PRODUCTS_LIST)}}" type="button" role="link" class="btn">Shop Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

@endsection
