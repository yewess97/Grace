<?php

namespace App\Services;

class SettingsService {
    /*========================================== Layout ==========================================*/
    /**
     * Get the dropdown items for the navbar.
     *
     * @param array $vars
     * @return array
     */
    final public static function getNavbarDropdowns(array $vars = []): array
    {
        return object_from_array([
            [
                'title'      => CATEGORIES_TABLE,
                'collection' => $vars[CATEGORIES_TABLE],
                'route_name' => CATEGORY_MODEL,
            ],
            [
                'title'      => 'collections',
                'collection' => $vars[SUBCATEGORIES_TABLE],
                'route_name' => SUBCATEGORY_MODEL,
            ],
        ]);
    }

    /**
     * Get the items for the navbar.
     *
     * @return array
     */
    final public static function getNavbarItems(): array
    {
        return object_from_array([
            [
                'route_name' => PAYMENT,
            ],
            [
                'route_name' => ABOUT_US,
            ],
            [
                'route_name' => CONTACT_US,
            ],
        ]);
    }

    /**
     * Get the offers for the navbar.
     *
     * @return string[]
     */
    final public static function getNavbarOffers(): array
    {
        return [
            'Every day up to 45% off',
            'End of hot summer sale',
            'Get 50% off on four orders',
        ];
    }

    /**
     * Get the items for the footer.
     *
     * @return array
     */
    final public static function getFooterMenus(): array
    {
        return object_from_array([
            'information' => [
                ucfirst(pluralize(PRICE)).' Drop',
                capitalizeAll(NEW_PRODUCTS),
                'Best Sales',
                'Sitemap',
                'Store',
            ],
            'our company' => [
                'Delivery',
                'Legal Notice',
                capitalizeAll(ABOUT_US),
                'Secure Payment',
                capitalizeAll(CONTACT_US),
            ],
            'your account' => [
                'Personal Info',
                ucfirst(ORDERS_TABLE),
                'Credit Slips',
                ucfirst(ADDRESSES_TABLE),
                ucfirst(CART_MODEL),
            ],
        ]);
    }
    /*========================================== End Layout ==========================================*/

    /*========================================== App ==========================================*/
    /**
     * Get the services for the home page.
     *
     * @return array
     */
    final public static function getHomeServices(): array
    {
        return object_from_array([
            [
                MAIN_IMAGE        => "1",
                TITLE             => "Free fast delivery",
                SHORT_DESCRIPTION => "Fast order delivery tracking",
            ],
            [
                MAIN_IMAGE        => "2",
                TITLE             => "24 X 7 Supports",
                SHORT_DESCRIPTION => "If you need help, we are opening 24 x 7",
            ],
            [
                MAIN_IMAGE        => "3",
                TITLE             => "Best quality",
                SHORT_DESCRIPTION => "We offer the best quality squishies",
            ],
            [
                MAIN_IMAGE        => "4",
                TITLE             => "Gift Voucher",
                SHORT_DESCRIPTION => "Best terms and conditions for gift vouchers",
            ],
        ]);
    }

    /**
     * Get the items for the top and bottom wear dropdowns.
     *
     * @return array
     */
    final public static function getTopBottomWearMenu(): array
    {
        $accessories_menu_item = [
            'Top Accessories' => [
                'Sports T-Shirts',
                'Track pants',
                'Cargos',
                'Top wear',
                'Track pants',
            ],
        ];

        $sunglasses_menu_item = [
            'Sunglasses' => [
                'Shirts',
                'Boxers',
                'Vests',
                'Belts',
                'Accessories',
            ],
        ];

        $top_wear = [
            ...$accessories_menu_item,
            ...$sunglasses_menu_item,
            'Top Wear' => [
                'Shirts',
                'Kurtas',
                'T-Shirts',
                'Belts',
                'Jewellery',
            ],
        ];

        $bottom_wear = [
            'Bottom Accessories' => [
                'Vests',
                'Sunglasses',
                'Bottom wear',
                'Jeans',
                'Cargos',
            ],
            ...$sunglasses_menu_item,
            ...$accessories_menu_item,
            'Bottom Wear' => [
                'Sports T-Shirts',
                'Jewellery',
                'Track pants',
                'Cargos',
                'Boxer',
            ],
        ];

        return [
            'top_wear'    => object_from_array($top_wear),
            'bottom_wear' => object_from_array($bottom_wear),
        ];
    }

    /**
     * Get the items for the payment inquiries.
     *
     * @return array
     */
    final public static function getPaymentInquiries(): array
    {
        return object_from_array([
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
        ]);
    }

    /*========================================== End App ==========================================*/
}
