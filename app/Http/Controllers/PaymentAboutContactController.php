<?php

namespace App\Http\Controllers;

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
        $payment_inquiries = [
            [
                'question' => "How do I pay for a Grace’s purchase?",
                'answer'   => "<p>Grace offers you multiple payment methods. Whatever your online mode of payment, you can trust assured that Grace's trusted payment gateway partners use secure encryption technology to keep your transaction details confidential at all times.</p><p>You may use Internet Banking, Debit Card, Credit Card and Cash on Delivery to make your purchase. We also accept payments made using Visa, MasterCard, American Express and Any Club credit/debit cards.</p>",
            ],
            [
                'question' => "Can I make a credit/debit card or Internet Banking payment through my mobile?",
                'answer'   => "<p>Yes, you can make credit card payments through the Grace mobile site. Grace uses 256-bit encryption technology to protect your card information while securely transmitting it to the secure and trusted payment gateways managed by leading banks.</p>",
            ],
            [
                'question' => "Is it safe to use my credit/debit card on Grace?",
                'answer'   => "<p>All Credit/Debit card details remain confidential and private. Grace and our trusted payment gateways use SSL encryption technology to protect your card information.</p>",
            ],
            [
                'question' => "Does Grace store my credit card information?",
                'answer'   => "<p>No, Grace does not collect or store your account information at all. Your transaction is authorized at multiple points, first by EBS/CCAvenue and subsequently by Visa/MasterCard/Amex secure directly without any information passing through us.</p>",
            ],
        ];

        $payment_inquiries = object_from_array($payment_inquiries);

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
