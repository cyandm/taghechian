<?php

/**
 * WooCommerce Customizations
 * Custom modifications for WooCommerce checkout and other features
 * @package Cyan\Theme\Classes
 */

namespace Cyan\Theme\Classes;

class WooCommerce
{

    public static function init()
    {
        // Remove specific checkout fields
        add_filter('woocommerce_checkout_fields', [__CLASS__, 'removeCheckoutFields'], 9999);

        // Make phone field required
        add_filter('woocommerce_checkout_fields', [__CLASS__, 'makePhoneRequired'], 9999);

        // Add placeholders to fields
        add_filter('woocommerce_checkout_fields', [__CLASS__, 'addPlaceholders'], 9999);

        // Customize coupon success messages
        add_filter('woocommerce_coupon_message', [__CLASS__, 'customizeCouponMessages'], 10, 3);

        // Customize coupon error messages
        add_filter('woocommerce_coupon_error', [__CLASS__, 'customizeCouponErrors'], 10, 3);

        // Remove default WooCommerce breadcrumb
        self::removeBreadcrumb();

        // Filter "in stock only" on shop archive
        add_action('woocommerce_product_query', [__CLASS__, 'filterByInstock']);
        // Filter by gender, poet, collection, side via URL (no full page reload)
        add_action('woocommerce_product_query', [__CLASS__, 'filterByTaxonomyQueryVars'], 20);
        // orderby=sale: only sale products + sort by date
        add_filter('woocommerce_get_catalog_ordering_args', [__CLASS__, 'catalogOrderBySale'], 10, 3);
        add_action('woocommerce_product_query', [__CLASS__, 'filterByOrderbySale'], 25);
        add_filter('woocommerce_catalog_orderby', [__CLASS__, 'catalogOrderbyWithSale']);
        add_filter('woocommerce_catalog_orderby', [__CLASS__, 'catalogOrderbyRemoveOptions'], 20);

        add_filter('loop_shop_per_page', [__CLASS__, 'shopPerPage'], 20);

        add_action('widgets_init', [__CLASS__, 'registerShopSidebar']);

        self::archiveProduct();
    }

    /**
     * Products per page on shop archive (enables pagination when more than one page).
     *
     * @return int
     */
    public static function shopPerPage()
    {
        return 20;
    }

    /**
     * Register shop sidebar for widgets (Appearance > Widgets).
     */
    public static function registerShopSidebar()
    {
        register_sidebar([
            'name'          => 'سایدبار فروشگاه',
            'id'            => 'shop',
            'description'   => 'Widgets in this area appear on the shop archive.',
            'before_widget' => '<div id="%1$s" class="widget %2$s mb-4">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="widget-title font-bold mb-2">',
            'after_title'   => '</h3>',
        ]);
    }

    /**
     * When instock=1 in URL, show only in-stock products.
     * Simple products: use own _stock_status. Variable: WooCommerce sets parent by variant availability.
     *
     * @param \WP_Query $q
     */
    public static function filterByInstock($q)
    {
        if (!is_shop() && !is_product_category()) {
            return;
        }
        if (!isset($_GET['instock']) || $_GET['instock'] !== '1') {
            return;
        }
        $meta_query = $q->get('meta_query') ?: [];
        $meta_query[] = [
            'key'     => '_stock_status',
            'value'   => 'instock',
            'compare' => '=',
        ];
        $q->set('meta_query', $meta_query);
    }

    /**
     * Apply taxonomy filters (gender, poet, collection, side) from URL params.
     *
     * @param \WP_Query $q
     */
    public static function filterByTaxonomyQueryVars($q)
    {
        if (! $q->is_main_query() || (! is_shop() && ! is_product_taxonomy())) {
            return;
        }
        $tax_query = is_array($q->get('tax_query')) ? $q->get('tax_query') : [];
        $filter_taxonomies = ['product_gender', 'product_poet', 'product_collection', 'product_side'];
        foreach ($filter_taxonomies as $taxonomy) {
            if (! taxonomy_exists($taxonomy)) {
                continue;
            }
            if (! isset($_GET[$taxonomy]) || $_GET[$taxonomy] === '') {
                continue;
            }
            $slugs = array_map('sanitize_title', explode(',', wc_clean(wp_unslash($_GET[$taxonomy]))));
            $slugs = array_filter($slugs);
            if (empty($slugs)) {
                continue;
            }
            $tax_query[] = [
                'taxonomy' => $taxonomy,
                'field'    => 'slug',
                'terms'    => $slugs,
                'operator' => count($slugs) > 1 ? 'IN' : '=',
            ];
        }
        if (! empty($tax_query)) {
            $q->set('tax_query', $tax_query);
        }
    }

    /**
     * When orderby=sale, return valid ordering args so WooCommerce query does not error.
     *
     * @param array  $args
     * @param string $orderby
     * @param string $order
     * @return array
     */
    public static function catalogOrderBySale($args, $orderby, $order)
    {
        if ($orderby !== 'sale') {
            return $args;
        }
        return [
            'orderby'  => 'date',
            'order'    => 'DESC',
            'meta_key' => '',
        ];
    }

    /**
     * When orderby=sale in URL, show only sale products on archive.
     *
     * @param \WP_Query $q
     */
    public static function filterByOrderbySale($q)
    {
        if (! $q->is_main_query() || (! is_shop() && ! is_product_taxonomy())) {
            return;
        }
        if (! isset($_GET['orderby']) || sanitize_text_field(wp_unslash($_GET['orderby'])) !== 'sale') {
            return;
        }
        $on_sale_ids = wc_get_product_ids_on_sale();
        $q->set('post__in', ! empty($on_sale_ids) ? $on_sale_ids : [0]);
    }

    /**
     * Add "sale products" option to shop catalog orderby dropdown.
     *
     * @param array $options
     * @return array
     */
    public static function catalogOrderbyWithSale($options)
    {
        $options['sale'] = __('محصولات تخفیف‌دار', 'taghechian');
        return $options;
    }

    /**
     * Remove selected options from shop catalog orderby dropdown.
     * Keys: menu_order, popularity, rating, date, price, price-desc, sale.
     *
     * @param array $options
     * @return array
     */
    /**
     * Active archive filters for display as removable tags.
     *
     * @param string $base_url Base URL (shop or term).
     * @return array List of [ 'label' => '...', 'url' => '...' ].
     */
    public static function getArchiveActiveFilters($base_url)
    {
        $filters = [];
        $get    = isset($_GET) ? wp_unslash($_GET) : [];
        unset($get['paged']);

        $orderby_labels = [
            'menu_order' => __('مرتب‌سازی پیش‌فرض', 'taghechian'),
            'popularity' => __('مرتب‌سازی بر اساس محبوبیت', 'taghechian'),
            'rating'     => __('مرتب‌سازی بر اساس امتیاز', 'taghechian'),
            'date'       => __('مرتب‌سازی بر اساس جدیدترین', 'taghechian'),
            'price'      => __('مرتب‌سازی بر اساس ارزان‌ترین', 'taghechian'),
            'price-desc' => __('مرتب‌سازی بر اساس گران‌ترین', 'taghechian'),
            'sale'       => __('محصولات تخفیف‌دار', 'taghechian'),
        ];

        // Price
        if (! empty($get['min_price']) || ! empty($get['max_price'])) {
            $parts = [];
            if (! empty($get['min_price'])) {
                $parts[] = sprintf(__('از %s', 'taghechian'), strip_tags(wc_price($get['min_price'])));
            }
            if (! empty($get['max_price'])) {
                $parts[] = sprintf(__('تا %s', 'taghechian'), strip_tags(wc_price($get['max_price'])));
            }
            $params = $get;
            unset($params['min_price'], $params['max_price']);
            $filters[] = ['label' => __('قیمت', 'taghechian') . ': ' . implode(' ', $parts), 'url' => add_query_arg($params, $base_url)];
        }

        // In stock
        if (! empty($get['instock']) && $get['instock'] === '1') {
            $params = $get;
            unset($params['instock']);
            $filters[] = ['label' => __('موجود در انبار', 'taghechian'), 'url' => add_query_arg($params, $base_url)];
        }

        // Size (filter_size)
        $filter_size = 'filter_' . wc_attribute_taxonomy_slug('pa_size');
        if (! empty($get[$filter_size])) {
            $slugs   = array_map('sanitize_title', explode(',', wc_clean($get[$filter_size])));
            $names   = [];
            foreach ($slugs as $slug) {
                $t = get_term_by('slug', $slug, 'pa_size');
                $names[] = $t && ! is_wp_error($t) ? $t->name : $slug;
            }
            $params = $get;
            unset($params[$filter_size], $params['query_type_size']);
            $filters[] = ['label' => __('سایز', 'taghechian') . ': ' . implode(', ', $names), 'url' => add_query_arg($params, $base_url)];
        }

        // Color (filter_color)
        $filter_color = 'filter_' . wc_attribute_taxonomy_slug('pa_color');
        if (! empty($get[$filter_color])) {
            $slugs   = array_map('sanitize_title', explode(',', wc_clean($get[$filter_color])));
            $names   = [];
            foreach ($slugs as $slug) {
                $t = get_term_by('slug', $slug, 'pa_color');
                $names[] = $t && ! is_wp_error($t) ? $t->name : $slug;
            }
            $params = $get;
            unset($params[$filter_color], $params['query_type_color']);
            $filters[] = ['label' => __('رنگ', 'taghechian') . ': ' . implode(', ', $names), 'url' => add_query_arg($params, $base_url)];
        }

        // product_gender
        if (! empty($get['product_gender'])) {
            $slug = sanitize_title($get['product_gender']);
            $t   = get_term_by('slug', $slug, 'product_gender');
            $params = $get;
            unset($params['product_gender']);
            $filters[] = ['label' => ($t && ! is_wp_error($t) ? $t->name : $slug), 'url' => add_query_arg($params, $base_url)];
        }

        // product_poet
        if (! empty($get['product_poet'])) {
            $slug = sanitize_title($get['product_poet']);
            $t   = get_term_by('slug', $slug, 'product_poet');
            $params = $get;
            unset($params['product_poet']);
            $filters[] = ['label' => ($t && ! is_wp_error($t) ? $t->name : $slug), 'url' => add_query_arg($params, $base_url)];
        }

        // product_collection
        if (! empty($get['product_collection'])) {
            $slug = sanitize_title($get['product_collection']);
            $t   = get_term_by('slug', $slug, 'product_collection');
            $params = $get;
            unset($params['product_collection']);
            $filters[] = ['label' => ($t && ! is_wp_error($t) ? $t->name : $slug), 'url' => add_query_arg($params, $base_url)];
        }

        // product_side
        if (! empty($get['product_side'])) {
            $slug = sanitize_title($get['product_side']);
            $t   = get_term_by('slug', $slug, 'product_side');
            $params = $get;
            unset($params['product_side']);
            $filters[] = ['label' => ($t && ! is_wp_error($t) ? $t->name : $slug), 'url' => add_query_arg($params, $base_url)];
        }

        // orderby
        if (! empty($get['orderby'])) {
            $ob   = sanitize_text_field($get['orderby']);
            $label = isset($orderby_labels[$ob]) ? $orderby_labels[$ob] : $ob;
            $params = $get;
            unset($params['orderby']);
            $filters[] = ['label' => $label, 'url' => add_query_arg($params, $base_url)];
        }

        return $filters;
    }

    /**
     * In archive-product, mobile/desktop orderby are handled separately; this applies elsewhere.
     */
    public static function catalogOrderbyRemoveOptions($options)
    {
        $remove = [
            'rating',     // sort by rating
            'popularity', // popularity
            'date',       // newest
            'menu_order', // default
            'sale',       // sale products
            // On xl, archive-product shows only price and price-desc in its own dropdown.
        ];
        foreach ($remove as $key) {
            unset($options[$key]);
        }
        return $options;
    }

    /**
     * Remove specific fields from checkout form
     * First makes them non-required, then removes them
     * 
     * @param array $fields Checkout fields
     * @return array Modified checkout fields
     */
    public static function removeCheckoutFields($fields)
    {
        // Fields to remove
        $fields_to_remove = [
            'billing_address_1',
            'billing_address_2',
            'billing_city',
            'billing_state',
            'billing_email',
        ];

        // First, make fields non-required to avoid validation errors
        foreach ($fields_to_remove as $field_key) {
            if (isset($fields['billing'][$field_key])) {
                $fields['billing'][$field_key]['required'] = false;
            }
        }

        // Then remove the fields
        foreach ($fields_to_remove as $field_key) {
            if (isset($fields['billing'][$field_key])) {
                unset($fields['billing'][$field_key]);
            }
        }

        return $fields;
    }

    /**
     * Make phone field required
     * 
     * @param array $fields Checkout fields
     * @return array Modified checkout fields
     */
    public static function makePhoneRequired($fields)
    {
        if (isset($fields['billing']['billing_phone'])) {
            $fields['billing']['billing_phone']['required'] = true;
        }

        return $fields;
    }

    /**
     * Add placeholders to checkout fields
     * 
     * @param array $fields Checkout fields
     * @return array Modified checkout fields
     */
    public static function addPlaceholders($fields)
    {
        // Add placeholder for first name
        if (isset($fields['billing']['billing_first_name'])) {
            $fields['billing']['billing_first_name']['placeholder'] = __('نام', 'jonubgard');
        }

        // Add placeholder for last name
        if (isset($fields['billing']['billing_last_name'])) {
            $fields['billing']['billing_last_name']['placeholder'] = __('نام خانوادگی', 'jonubgard');
        }

        // Add placeholder for phone
        if (isset($fields['billing']['billing_phone'])) {
            $fields['billing']['billing_phone']['placeholder'] = __('شماره تلفن', 'jonubgard');
        }

        return $fields;
    }

    /**
     * Customize WooCommerce coupon success messages
     * Localizes coupon applied/removed messages
     * 
     * @param string $msg Message text
     * @param int $msg_code Message code
     * @param object $coupon Coupon object
     * @return string Modified message
     */
    public static function customizeCouponMessages($msg, $msg_code, $coupon)
    {
        switch ($msg_code) {
            case \WC_Coupon::WC_COUPON_SUCCESS:
                $msg = '🎉 تبریک! کد تخفیف "' . esc_html($coupon->get_code()) . '" با موفقیت اعمال شد.';
                break;
            case \WC_Coupon::WC_COUPON_REMOVED:
                $msg = 'کد تخفیف حذف شد.';
                break;
        }
        return $msg;
    }

    /**
     * Customize WooCommerce coupon error messages
     * Localizes all coupon validation error messages
     * 
     * @param string $err Error message
     * @param int $err_code Error code
     * @param object $coupon Coupon object
     * @return string Modified error message
     */
    public static function customizeCouponErrors($err, $err_code, $coupon)
    {
        switch ($err_code) {
            case 100: // INVALID_FILTERED
                $err = '⚠️ کد تخفیف وارد شده معتبر نیست.';
                break;

            case 101: // INVALID_REMOVED
                $err = '❌ کد تخفیف معتبر نیست و حذف شد.';
                break;

            case 102: // NOT_YOURS_REMOVED
                $err = '🚫 این کد به حساب کاربری شما تعلق ندارد.';
                break;

            case 103: // ALREADY_APPLIED
                $err = '🔁 این کد تخفیف قبلاً اعمال شده است.';
                break;

            case 104: // ALREADY_APPLIED_INDIV_USE_ONLY
                $err = '⚠️ این کد فقط به صورت انفرادی قابل استفاده است. ابتدا سایر کدها را حذف کنید.';
                break;

            case 105: // NOT_EXIST
                $err = '❌ کد تخفیفی که وارد کردید وجود ندارد.';
                break;

            case 106: // USAGE_LIMIT_REACHED
                $err = '🚫 سقف استفاده از این کد تخفیف پر شده است.';
                break;

            case 107: // EXPIRED
                $err = '⏰ این کد تخفیف منقضی شده است.';
                break;

            case 108: // MIN_SPEND_LIMIT_NOT_MET
                $min_spend = $coupon->get_minimum_amount();
                $err = '💰 برای استفاده از این کد باید حداقل ' . \wc_price($min_spend) . ' خرید کنید.';
                break;

            case 109: // NOT_APPLICABLE
                $err = '⚠️ این کد برای محصولات انتخاب‌شده قابل استفاده نیست.';
                break;

            case 110: // NOT_VALID_SALE_ITEMS
                $err = '❗ این کد روی کالاهای دارای تخفیف قابل استفاده نیست.';
                break;

            case 111: // PLEASE_ENTER
                $err = '🔤 لطفاً یک کد تخفیف وارد کنید.';
                break;

            case 112: // MAX_SPEND_LIMIT_MET
                $max_spend = $coupon->get_maximum_amount();
                $err = '💸 حداکثر مبلغ خرید برای این کد ' . \wc_price($max_spend) . ' است.';
                break;

            case 113: // EXCLUDED_PRODUCTS
                $err = '🚫 برخی از محصولات شما شامل این کد تخفیف نمی‌شوند.';
                break;

            case 114: // EXCLUDED_CATEGORIES
                $err = '📦 برخی از دسته‌بندی‌های انتخابی مشمول این کد تخفیف نیستند.';
                break;

            case 115: // USAGE_LIMIT_COUPON_STUCK
            case 116: // USAGE_LIMIT_COUPON_STUCK_GUEST
                $err = '⚠️ استفاده از این کد تخفیف به‌دلیل محدودیت تعداد استفاده، امکان‌پذیر نمی‌باشد.';
                break;

            default:
                $err = '❗ خطایی در بررسی کد تخفیف رخ داده است.';
                break;
        }

        return $err;
    }

    /**
     * Remove default WooCommerce breadcrumb
     * Removes breadcrumb from all WooCommerce pages to use custom breadcrumb
     */
    public static function removeBreadcrumb()
    {
        // Remove from main content area
        remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

        // Remove from single product page
        add_action('woocommerce_before_single_product', function () {
            remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
        }, 1);

        // Remove from archive pages
        add_action('woocommerce_before_shop_loop', function () {
            remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
        }, 1);
    }

    public static function archiveProduct()
    {
        // remove results count
        remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);

        // remove default sorting
        remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
    }
}
