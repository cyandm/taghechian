<?php

/**
 * WooCommerce Customizations
 * Custom modifications for WooCommerce checkout and other features
 * @package Cyan\Theme\Classes
 */

namespace Cyan\Theme\Classes;

use Cyan\Theme\Helpers\Templates;

class WooCommerce
{
    /**
     * User meta key used to persist wishlist product IDs.
     *
     * @var string
     */
    private const WISHLIST_META_KEY = '_cyn_wishlist_product_ids';

    /**
     * Query-string action for removing a product from wishlist.
     *
     * @var string
     */
    private const WISHLIST_REMOVE_ACTION = 'cyn_wishlist_remove';

    /**
     * Nonce action used for wishlist remove links.
     *
     * @var string
     */
    private const WISHLIST_REMOVE_NONCE_ACTION = 'cyn_wishlist_remove_item';
    private const WISHLIST_TOGGLE_ACTION = 'cyn_wishlist_toggle';
    private const WISHLIST_TOGGLE_NONCE_ACTION = 'cyn_wishlist_toggle_item';

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

        // Cart page: custom layout (totals output in cart.php), custom proceed button
        remove_action('woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10);
        remove_action('woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20);
        add_action('woocommerce_proceed_to_checkout', [__CLASS__, 'cartProceedButton'], 20);
        add_filter('woocommerce_product_cross_sells_products_heading', [__CLASS__, 'crossSellsHeading']);

        // Remove "Your cart is currently empty" default message
        remove_action('woocommerce_cart_is_empty', 'wc_empty_cart_message', 10);

        // Clear variation transient cache when products/variations are saved so get_available_variations() never returns stale data
        add_action('woocommerce_update_product', [__CLASS__, 'clearVariableProductTransients']);
        add_action('woocommerce_update_product_variation', [__CLASS__, 'clearVariableProductTransientsFromVariation']);
        add_action('save_post_product', [__CLASS__, 'clearVariableProductTransientsOnSavePost'], 10, 3);
        add_action('save_post_product_variation', [__CLASS__, 'clearVariableProductTransientsOnSaveVariation'], 10, 3);

        // Manual clear of variation cache: open product URL with ?wc_clear_variations=1 (admins) to fix wrong cached data
        add_action('template_redirect', [__CLASS__, 'maybeClearVariationsOnView'], 5);
        add_action('template_redirect', [__CLASS__, 'redirectAccountAliasToMyAccount'], 1);
        add_filter('wp_redirect', [__CLASS__, 'maybeRedirectAccountSaveToDashboard'], 10, 2);
        add_action('init', [__CLASS__, 'registerAccountWishlistEndpoint']);
        add_action('init', [__CLASS__, 'registerAccountSupportEndpoint']);
        add_action('template_redirect', [__CLASS__, 'maybeHandleWishlistToggle'], 8);
        add_action('template_redirect', [__CLASS__, 'maybeHandleWishlistRemove'], 9);
        add_action('woocommerce_account_wishlist_endpoint', [__CLASS__, 'renderWishlistEndpointContent']);
        add_action('woocommerce_account_support_endpoint', [__CLASS__, 'renderSupportEndpointContent']);

        add_action('wp_enqueue_scripts', function () {
            if (is_product()) {
                wp_enqueue_script('wc-add-to-cart-variation');
            }
        });

        // Cart AJAX: enqueue params + handlers
        add_action('wp_head', [__CLASS__, 'addCartAjaxParams']);
        add_action('wp_ajax_update_cart_quantity', [__CLASS__, 'ajaxUpdateCartQuantity']);
        add_action('wp_ajax_nopriv_update_cart_quantity', [__CLASS__, 'ajaxUpdateCartQuantity']);

        // Delete coupon AJAX
        add_action('wp_ajax_delete_coupon_code', [__CLASS__, 'ajaxDeleteCoupon']);
        add_action('wp_ajax_nopriv_delete_coupon_code', [__CLASS__, 'ajaxDeleteCoupon']);
    }

    /**
     * Enqueue AJAX parameters for cart page (inline script in <head>)
     */
    public static function addCartAjaxParams()
    {
        if (!is_cart()) {
            return;
        }
?>
        <script type="text/javascript">
            var cart_ajax_params = {
                ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>',
                nonce: '<?php echo wp_create_nonce('cart_update_nonce'); ?>'
            };
        </script>
<?php
    }

    /**
     * AJAX handler for updating cart quantity
     */
    public static function ajaxUpdateCartQuantity()
    {
        // Security check
        if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'cart_update_nonce')) {
            wp_send_json_error(array('message' => 'خطای امنیتی'));
            return;
        }

        if (!isset($_POST['cart_key']) || !isset($_POST['quantity'])) {
            wp_send_json_error(array('message' => 'داده‌های ناقص'));
            return;
        }

        $cart_key = sanitize_text_field($_POST['cart_key']);
        $quantity = intval($_POST['quantity']);

        // Get cart
        $cart = WC()->cart;

        if ($quantity === 0) {
            // Remove item
            $cart->remove_cart_item($cart_key);

            wp_send_json_success(array(
                'removed' => true,
                'cart_subtotal' => WC()->cart->get_cart_subtotal(),
                'cart_total' => WC()->cart->get_total(),
            ));
            return;
        }

        // Update quantity
        $cart->set_quantity($cart_key, $quantity, true);
        $cart->calculate_totals();

        // Get updated values
        $cart_item = $cart->get_cart_item($cart_key);
        $_product = $cart_item ? $cart_item['data'] : null;

        $response = array(
            'cart_subtotal' => WC()->cart->get_cart_subtotal(),
            'cart_total' => WC()->cart->get_total(),
            'item_subtotal' => $_product ? WC()->cart->get_product_subtotal($_product, $quantity) : '',
            'cart_saving'   => wc_price(self::cyn_get_cart_special_price_saving()),
        );

        wp_send_json_success($response);
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
     * Cart proceed to checkout button (تایید و تکمیل سفارش)
     */
    public static function cartProceedButton()
    {
        echo '<a href="' . esc_url(wc_get_checkout_url()) . '" class="primary-btn wc-forward block w-full text-center py-3 rounded-xl">' . esc_html__('تایید و تکمیل سفارش', 'taghechian') . '</a>';
    }

    /**
     * Cross-sells section heading on cart page (محصولات پیشنهادی)
     *
     * @param string $heading
     * @return string
     */
    public static function crossSellsHeading($heading)
    {
        return __('محصولات پیشنهادی', 'taghechian');
    }

    /**
     * Clear variable product transients when product is updated so get_available_variations() is fresh.
     */
    public static function clearVariableProductTransients($product_id)
    {
        $product = wc_get_product($product_id);
        if ($product && $product->is_type('variable')) {
            wc_delete_product_transients($product_id);
        }
    }

    /**
     * Clear parent variable product transients when a variation is updated.
     */
    public static function clearVariableProductTransientsFromVariation($variation_id)
    {
        $variation = wc_get_product($variation_id);
        if ($variation && $variation->get_parent_id()) {
            wc_delete_product_transients($variation->get_parent_id());
        }
    }

    /**
     * When any product is saved (including from bulk edit, REST, etc.), clear variable product transients
     * so variation data is never stale or from another product.
     *
     * @param int      $post_id
     * @param \WP_Post $post
     * @param bool     $update
     */
    public static function clearVariableProductTransientsOnSavePost($post_id, $post, $update)
    {
        if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
            return;
        }
        if ($post->post_type === 'product_variation') {
            $parent_id = wp_get_post_parent_id($post_id);
            if ($parent_id) {
                wc_delete_product_transients($parent_id);
            }
        }
        $product = wc_get_product($post_id);
        if ($product && $product->is_type('variable')) {
            wc_delete_product_transients($post_id);
        }
    }

    /**
     * When a variation is saved (save_post), clear parent product transients.
     *
     * @param int      $post_id
     * @param \WP_Post $post
     * @param bool     $update
     */
    public static function clearVariableProductTransientsOnSaveVariation($post_id, $post, $update)
    {
        if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
            return;
        }
        $parent_id = wp_get_post_parent_id($post_id);
        if ($parent_id) {
            wc_delete_product_transients($parent_id);
        }
    }

    /**
     * Clear variable product transients on single product when ?wc_clear_variations=1 (admins only).
     * Open product URL with ?wc_clear_variations=1 once to fix wrong cached variation data, then reload.
     */
    public static function maybeClearVariationsOnView()
    {
        if (!isset($_GET['wc_clear_variations']) || $_GET['wc_clear_variations'] !== '1' || !is_singular('product')) {
            return;
        }
        if (!current_user_can('manage_woocommerce') && !current_user_can('edit_products')) {
            return;
        }
        $product_id = get_the_ID();
        if ($product_id) {
            wc_delete_product_transients($product_id);
            wp_safe_redirect(remove_query_arg('wc_clear_variations'));
            exit;
        }
    }

    /**
     * Redirect /account alias URLs to canonical /my-account URLs.
     */
    public static function redirectAccountAliasToMyAccount()
    {
        if (is_admin() || wp_doing_ajax() || wp_is_json_request()) {
            return;
        }

        $request_uri = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '';
        if ($request_uri === '') {
            return;
        }

        $parsed_url = wp_parse_url($request_uri);
        $request_path = isset($parsed_url['path']) ? (string) $parsed_url['path'] : '';
        if ($request_path === '') {
            return;
        }

        $normalized_request_path = trim($request_path, '/');
        if ($normalized_request_path === '' || strpos($normalized_request_path, 'account') !== 0) {
            return;
        }

        $my_account_path = preg_replace('/^account\b/', 'my-account', $normalized_request_path);
        $redirect_url = home_url('/' . ltrim((string) $my_account_path, '/') . '/');

        if (isset($parsed_url['query']) && $parsed_url['query'] !== '') {
            $redirect_url = $redirect_url . '?' . $parsed_url['query'];
        }

        wp_safe_redirect($redirect_url, 301);
        exit;
    }

    /**
     * Keep dashboard-origin account forms on the My Account page.
     *
     * WooCommerce redirects some account forms to dedicated endpoints by default.
     * This only overrides those redirects when the submit came from dashboard UI forms.
     *
     * @param string $location Redirect destination.
     * @param int    $status   Redirect status code.
     * @return string
     */
    public static function maybeRedirectAccountSaveToDashboard($location, $status)
    {
        unset($status);

        if (!is_string($location) || $location === '') {
            return $location;
        }

        if (!is_account_page() || !is_user_logged_in()) {
            return $location;
        }

        $posted_action = isset($_POST['action']) ? sanitize_text_field(wp_unslash($_POST['action'])) : '';
        $my_account_url = wc_get_page_permalink('myaccount');

        if ($posted_action === 'save_account_details') {
            $form_source = isset($_POST['cyn_account_form_source']) ? sanitize_text_field(wp_unslash($_POST['cyn_account_form_source'])) : '';
            if ($form_source !== 'dashboard_names') {
                return $location;
            }

            $edit_account_url = wc_get_endpoint_url('edit-account', '', $my_account_url);
            if (untrailingslashit($location) !== untrailingslashit($edit_account_url)) {
                return $location;
            }

            return $my_account_url;
        }

        if ($posted_action === 'edit_address') {
            $form_source = isset($_POST['cyn_address_form_source']) ? sanitize_text_field(wp_unslash($_POST['cyn_address_form_source'])) : '';
            if ($form_source !== 'dashboard_modal') {
                return $location;
            }

            $edit_address_url = wc_get_endpoint_url('edit-address', '', $my_account_url);
            if (untrailingslashit($location) !== untrailingslashit($edit_address_url)) {
                return $location;
            }

            return $my_account_url;
        }

        return $location;
    }

    /**
     * Register "wishlist" endpoint under My Account.
     */
    public static function registerAccountWishlistEndpoint()
    {
        add_rewrite_endpoint('wishlist', EP_ROOT | EP_PAGES);
    }

    /**
     * Register "support" endpoint under My Account.
     */
    public static function registerAccountSupportEndpoint()
    {
        add_rewrite_endpoint('support', EP_ROOT | EP_PAGES);
    }

    /**
     * Output wishlist endpoint content.
     */
    public static function renderWishlistEndpointContent()
    {
        wc_get_template('myaccount/wishlist.php');
    }

    /**
     * Output support endpoint content.
     */
    public static function renderSupportEndpointContent()
    {
        wc_get_template('myaccount/support.php');
    }

    /**
     * Return current user's normalized wishlist product IDs.
     *
     * @return int[]
     */
    public static function getCurrentUserWishlistProductIds()
    {
        $user_id = get_current_user_id();
        if ($user_id <= 0) {
            return [];
        }

        $stored = get_user_meta($user_id, self::WISHLIST_META_KEY, true);
        if (! is_array($stored)) {
            return [];
        }

        $wishlist_product_ids = array_values(array_unique(array_filter(array_map('absint', $stored))));

        return $wishlist_product_ids;
    }

    /**
     * Remove a product from current user's wishlist.
     *
     * @param int $product_id Product ID.
     * @return bool
     */
    public static function removeProductFromCurrentUserWishlist($product_id)
    {
        $user_id = get_current_user_id();
        if ($user_id <= 0 || $product_id <= 0) {
            return false;
        }

        $wishlist_product_ids = self::getCurrentUserWishlistProductIds();
        $updated_wishlist_ids = array_values(array_filter($wishlist_product_ids, static function ($wishlist_product_id) use ($product_id) {
            return (int) $wishlist_product_id !== (int) $product_id;
        }));

        update_user_meta($user_id, self::WISHLIST_META_KEY, $updated_wishlist_ids);

        return true;
    }

    /**
     * Add a product to current user's wishlist.
     *
     * @param int $product_id Product ID.
     * @return bool
     */
    public static function addProductToCurrentUserWishlist($product_id)
    {
        $user_id = get_current_user_id();
        $product_id = absint($product_id);

        if ($user_id <= 0 || $product_id <= 0) {
            return false;
        }

        $product = wc_get_product($product_id);
        if (! $product || ! $product->get_id()) {
            return false;
        }

        $wishlist_product_ids = self::getCurrentUserWishlistProductIds();
        if (! in_array($product_id, $wishlist_product_ids, true)) {
            $wishlist_product_ids[] = $product_id;
        }

        $wishlist_product_ids = array_values(array_unique(array_filter(array_map('absint', $wishlist_product_ids))));
        update_user_meta($user_id, self::WISHLIST_META_KEY, $wishlist_product_ids);

        return true;
    }

    /**
     * Check whether a product exists in current user's wishlist.
     *
     * @param int $product_id Product ID.
     * @return bool
     */
    public static function isProductInCurrentUserWishlist($product_id)
    {
        $product_id = absint($product_id);
        if ($product_id <= 0 || ! is_user_logged_in()) {
            return false;
        }

        $wishlist_product_ids = self::getCurrentUserWishlistProductIds();

        return in_array($product_id, $wishlist_product_ids, true);
    }

    /**
     * Handle add/remove toggle action from single product page.
     */
    public static function maybeHandleWishlistToggle()
    {
        if (! isset($_GET[self::WISHLIST_TOGGLE_ACTION])) {
            return;
        }

        $product_id = absint(wp_unslash($_GET[self::WISHLIST_TOGGLE_ACTION]));
        $nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';
        $redirect_url = wp_get_referer();
        if (! is_string($redirect_url) || $redirect_url === '') {
            $redirect_url = $product_id > 0 ? get_permalink($product_id) : home_url('/');
        }
        $redirect_url = remove_query_arg([self::WISHLIST_TOGGLE_ACTION, '_wpnonce'], $redirect_url);

        if (! is_user_logged_in()) {
            wp_safe_redirect($redirect_url);
            exit;
        }

        if ($product_id <= 0 || $nonce === '' || ! wp_verify_nonce($nonce, self::WISHLIST_TOGGLE_NONCE_ACTION . '_' . $product_id)) {
            wc_add_notice(__('درخواست نامعتبر است. لطفاً دوباره تلاش کنید.', 'taghechian'), 'error');
            wp_safe_redirect($redirect_url);
            exit;
        }

        $is_liked = self::isProductInCurrentUserWishlist($product_id);
        $result = $is_liked
            ? self::removeProductFromCurrentUserWishlist($product_id)
            : self::addProductToCurrentUserWishlist($product_id);

        if (! $result) {
            wc_add_notice(__('امکان بروزرسانی علاقه‌مندی وجود ندارد.', 'taghechian'), 'error');
            wp_safe_redirect($redirect_url);
            exit;
        }

        if ($is_liked) {
            wc_add_notice(__('محصول از علاقه‌مندی‌ها حذف شد.', 'taghechian'), 'success');
        } else {
            wc_add_notice(__('محصول به علاقه‌مندی‌ها اضافه شد.', 'taghechian'), 'success');
        }

        wp_safe_redirect($redirect_url);
        exit;
    }

    /**
     * Handle secure remove action from My Account wishlist page.
     */
    public static function maybeHandleWishlistRemove()
    {
        if (! is_user_logged_in() || ! is_account_page()) {
            return;
        }

        if (! isset($_GET[self::WISHLIST_REMOVE_ACTION])) {
            return;
        }

        $product_id = isset($_GET[self::WISHLIST_REMOVE_ACTION]) ? absint(wp_unslash($_GET[self::WISHLIST_REMOVE_ACTION])) : 0;
        $nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';
        $wishlist_url = wc_get_account_endpoint_url('wishlist');

        if ($product_id <= 0 || $nonce === '' || ! wp_verify_nonce($nonce, self::WISHLIST_REMOVE_NONCE_ACTION . '_' . $product_id)) {
            wc_add_notice(__('درخواست نامعتبر است. لطفاً دوباره تلاش کنید.', 'taghechian'), 'error');
            wp_safe_redirect($wishlist_url);
            exit;
        }

        $removed = self::removeProductFromCurrentUserWishlist($product_id);
        if (! $removed) {
            wc_add_notice(__('امکان حذف محصول از علاقه‌مندی‌ها وجود ندارد.', 'taghechian'), 'error');
            wp_safe_redirect($wishlist_url);
            exit;
        }

        wc_add_notice(__('محصول از علاقه‌مندی‌ها حذف شد.', 'taghechian'), 'success');
        wp_safe_redirect($wishlist_url);
        exit;
    }

    /**
     * Build secure remove URL for a wishlist product.
     *
     * @param int $product_id Product ID.
     * @return string
     */
    public static function getWishlistRemoveUrl($product_id)
    {
        $product_id = absint($product_id);
        if ($product_id <= 0) {
            return '';
        }

        $url = add_query_arg(
            [
                self::WISHLIST_REMOVE_ACTION => $product_id,
            ],
            wc_get_account_endpoint_url('wishlist')
        );

        return wp_nonce_url($url, self::WISHLIST_REMOVE_NONCE_ACTION . '_' . $product_id);
    }

    /**
     * Build secure toggle URL for single-product wishlist button.
     *
     * @param int $product_id Product ID.
     * @return string
     */
    public static function getWishlistToggleUrl($product_id)
    {
        $product_id = absint($product_id);
        if ($product_id <= 0) {
            return '';
        }

        $url = add_query_arg(
            [
                self::WISHLIST_TOGGLE_ACTION => $product_id,
            ],
            get_permalink($product_id)
        );

        return wp_nonce_url($url, self::WISHLIST_TOGGLE_NONCE_ACTION . '_' . $product_id);
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
        $fields_to_remove = [
            'billing_address_2',
        ];

        foreach ($fields_to_remove as $field_key) {
            if (isset($fields['billing'][$field_key])) {
                $fields['billing'][$field_key]['required'] = false;
                unset($fields['billing'][$field_key]);
            }
        }

        if (isset($fields['billing']['billing_email'])) {
            $fields['billing']['billing_email']['required'] = false;

            $fields['billing']['billing_email']['class'][] = 'hidden';

            $fields['billing']['billing_email']['label'] = '';
            $fields['billing']['billing_email']['placeholder'] = '';
        }

        add_filter('woocommerce_checkout_fields', function ($fields) {

            $fields['billing']['billing_country']['default'] = 'IR';
            $fields['billing']['billing_country']['class'][] = 'hidden';

            return $fields;
        });

        remove_action(
            'woocommerce_before_checkout_form',
            'woocommerce_checkout_coupon_form',
            10
        );

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
        if (isset($fields['billing']['billing_first_name'])) {
            $fields['billing']['billing_first_name']['placeholder'] = __('نام', 'taghechian');
        }

        if (isset($fields['billing']['billing_last_name'])) {
            $fields['billing']['billing_last_name']['placeholder'] = __('نام خانوادگی', 'taghechian');
        }

        if (isset($fields['billing']['billing_city'])) {
            $fields['billing']['billing_city']['placeholder'] = __('نام شهر خود را وارد کنید', 'taghechian');
        }

        if (isset($fields['billing']['billing_postcode'])) {
            $fields['billing']['billing_postcode']['placeholder'] = __('کد پستی ده رقمی را وارد کنید', 'taghechian');
        }

        if (isset($fields['billing']['billing_phone'])) {
            $fields['billing']['billing_phone']['placeholder'] = __('09xxxxxxxxxx', 'taghechian');
        }

        $fields['order']['order_comments']['custom_attributes'] = [
            'rows' => 4,
        ];

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
            case 100:
                $err = '⚠️ کد تخفیف وارد شده معتبر نیست.';
                break;
            case 101:
                $err = '❌ کد تخفیف معتبر نیست و حذف شد.';
                break;
            case 102:
                $err = '🚫 این کد به حساب کاربری شما تعلق ندارد.';
                break;
            case 103:
                $err = '🔁 این کد تخفیف قبلاً اعمال شده است.';
                break;
            case 104:
                $err = '⚠️ این کد فقط به صورت انفرادی قابل استفاده است. ابتدا سایر کدها را حذف کنید.';
                break;
            case 105:
                $err = '❌ کد تخفیفی که وارد کردید وجود ندارد.';
                break;
            case 106:
                $err = '🚫 سقف استفاده از این کد تخفیف پر شده است.';
                break;
            case 107:
                $err = '⏰ این کد تخفیف منقضی شده است.';
                break;
            case 108:
                $min_spend = $coupon->get_minimum_amount();
                $err = '💰 برای استفاده از این کد باید حداقل ' . \wc_price($min_spend) . ' خرید کنید.';
                break;
            case 109:
                $err = '⚠️ این کد برای محصولات انتخاب‌شده قابل استفاده نیست.';
                break;
            case 110:
                $err = '❗ این کد روی کالاهای دارای تخفیف قابل استفاده نیست.';
                break;
            case 111:
                $err = '🔤 لطفاً یک کد تخفیف وارد کنید.';
                break;
            case 112:
                $err = '🔤 لطفاً یک کد تخفیف معتبر وارد کنید.';
                break;
        }
        return $err;
    }

    public static function ajaxDeleteCoupon()
    {
        // اعتبارسنجی nonce (اختیاری)
        if (isset($_POST['nonce']) && !wp_verify_nonce($_POST['nonce'], 'delete_coupon_nonce')) {
            wp_send_json_error(['message' => 'خطای امنیتی']);
            return;
        }

        // حذف همه کوپن‌ها
        foreach (WC()->cart->get_applied_coupons() as $code) {
            WC()->cart->remove_coupon($code);
        }

        WC()->cart->calculate_totals();

        wp_send_json_success([
            'message' => __('کد تخفیف با موفقیت حذف شد.', 'taghechian')
        ]);
    }


    /**
     * Remove WooCommerce breadcrumb
     */
    public static function removeBreadcrumb()
    {
        remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
    }

    /**
     * Product loop hooks
     */
    public static function archiveProduct()
    {
        remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);

        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
    }

    public static function cyn_get_cart_special_price_saving()
    {
        if (! WC()->cart) {
            return 0;
        }

        $total_saving = 0;

        foreach (WC()->cart->get_cart() as $cart_item) {
            $product  = $cart_item['data'];
            $quantity = $cart_item['quantity'];

            if (! $product || $quantity < 1) {
                continue;
            }

            $regular_price = (float) $product->get_regular_price();
            $sale_price    = (float) $product->get_sale_price();

            if ($sale_price > 0 && $regular_price > $sale_price) {
                $saving_per_item = $regular_price - $sale_price;
                $total_saving += $saving_per_item * $quantity;
            }
        }

        return $total_saving;
    }
}
