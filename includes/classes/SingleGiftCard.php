<?php

/**
 * Single Gift Card product template
 * @package Cyan\Theme\Classes
 */

namespace Cyan\Theme\Classes;

class SingleGiftCard
{

    public static function init()
    {
        add_filter('template_include', [__CLASS__, 'useGiftCardTemplate'], 20);
        add_action('pre_get_posts', [__CLASS__, 'excludeGiftCardsFromLoops'], 20);
        add_filter('woocommerce_add_to_cart_validation', [__CLASS__, 'validateGiftCardFields'], 10, 3);
        add_filter('woocommerce_add_cart_item_data', [__CLASS__, 'addGiftCardCartItemData'], 10, 3);
        add_filter('woocommerce_get_item_data', [__CLASS__, 'displayGiftCardItemData'], 10, 2);
        add_action('woocommerce_checkout_create_order_line_item', [__CLASS__, 'saveGiftCardOrderItemMeta'], 10, 4);
    }

    /**
     * Exclude gift-card products from all product loops; they are only visible on their own single product page.
     *
     * @param \WP_Query $query Query object.
     */
    public static function excludeGiftCardsFromLoops($query)
    {
        if (is_admin() || !$query->get('post_type')) {
            return;
        }
        $post_type  = $query->get('post_type');
        $is_product = ($post_type === 'product') || (is_array($post_type) && in_array('product', $post_type, true));
        if (!$is_product) {
            return;
        }
        if ($query->is_singular()) {
            return;
        }
        if (is_page_template('templates/gift-card.php')) {
            return;
        }
        $tax_query = (array) $query->get('tax_query');
        $tax_query[] = [
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => 'gift-card',
            'operator' => 'NOT IN',
        ];
        $query->set('tax_query', $tax_query);
    }

    /**
     * Save recipient name and message to cart item for gift-card products.
     */
    public static function addGiftCardCartItemData($cart_item_data, $product_id, $variation_id)
    {
        if (!has_term('gift-card', 'product_cat', $product_id)) {
            return $cart_item_data;
        }
        $name = isset($_POST['gift_recipient_name']) ? trim(sanitize_text_field(wp_unslash($_POST['gift_recipient_name']))) : '';
        $msg  = isset($_POST['gift_card_message']) ? trim(sanitize_textarea_field(wp_unslash($_POST['gift_card_message']))) : '';
        $cart_item_data['gift_recipient_name'] = $name;
        $cart_item_data['gift_card_message']   = $msg;
        $cart_item_data['unique_key']         = md5(serialize([$name, $msg]));
        return $cart_item_data;
    }

    /**
     * Show recipient name and message in cart and checkout.
     */
    public static function displayGiftCardItemData($item_data, $cart_item)
    {
        if (!empty($cart_item['gift_recipient_name'])) {
            $item_data[] = [
                'key'   => __('نام گیرنده', 'taghechian'),
                'value' => $cart_item['gift_recipient_name'],
            ];
        }
        if (!empty($cart_item['gift_card_message'])) {
            $item_data[] = [
                'key'   => __('جمله روی پاکت کارت', 'taghechian'),
                'value' => $cart_item['gift_card_message'],
            ];
        }
        return $item_data;
    }

    /**
     * Save gift card fields to order line item so they appear in the order.
     */
    public static function saveGiftCardOrderItemMeta($item, $cart_item_key, $values, $order)
    {
        if (!empty($values['gift_recipient_name'])) {
            $item->add_meta_data(__('نام گیرنده', 'taghechian'), $values['gift_recipient_name'], true);
        }
        if (!empty($values['gift_card_message'])) {
            $item->add_meta_data(__('جمله روی پاکت کارت', 'taghechian'), $values['gift_card_message'], true);
        }
    }

    /**
     * Require gift_recipient_name and gift_card_message for gift-card products.
     */
    public static function validateGiftCardFields($passed, $product_id, $quantity)
    {
        if (!has_term('gift-card', 'product_cat', $product_id)) {
            return $passed;
        }
        $name = isset($_POST['gift_recipient_name']) ? trim(sanitize_text_field(wp_unslash($_POST['gift_recipient_name']))) : '';
        $msg  = isset($_POST['gift_card_message']) ? trim(sanitize_text_field(wp_unslash($_POST['gift_card_message']))) : '';
        if ('' === $name) {
            wc_add_notice(__('لطفاً نام گیرنده را وارد کنید.', 'taghechian'), 'error');
            $passed = false;
        }
        if ('' === $msg) {
            wc_add_notice(__('لطفاً جمله روی پاکت کارت را وارد کنید.', 'taghechian'), 'error');
            $passed = false;
        }
        return $passed;
    }

    /**
     * Use single-product-gift-card.php when viewing a product in product_cat "gift-card".
     *
     * @param string $template Path to template file.
     * @return string
     */
    public static function useGiftCardTemplate($template)
    {
        if (!is_singular('product')) {
            return $template;
        }

        $product_id = get_queried_object_id();
        if (!$product_id || !has_term('gift-card', 'product_cat', $product_id)) {
            return $template;
        }

        $custom = get_stylesheet_directory() . '/woocommerce/single-product-gift-card.php';
        if (file_exists($custom)) {
            return $custom;
        }

        return $template;
    }
}
