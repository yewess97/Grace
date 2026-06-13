<?php

namespace App\Http\Controllers;

use App\Services\SettingsService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Throwable;

class PaymentAboutContactController extends Controller
{
    /**
     * Display the payment resource.
     *
     * @return Application|Factory|View
     * @throws Throwable
     */
    final public function payment(): Application|Factory|View
    {
        $payment_inquiries = SettingsService::getPaymentInquiries();

        return showView(USER_PAYMENT_VIEW, compact('payment_inquiries'));
    }

    /**
     * Display the about-us resource.
     *
     * @return Application|Factory|View
     * @throws Throwable
     */
    final public function aboutUs(): Application|Factory|View
    {
        return showView(USER_ABOUT_US_VIEW);
    }

    /**
     * Display the contact-us resource.
     *
     * @return Application|Factory|View
     * @throws Throwable
     */
    final public function contactUs(): Application|Factory|View
    {
        return showView(USER_CONTACT_US_VIEW);
    }
}
