<?php

/**
 * Product Attributes - Handle color attribute fields
 * @package Cyan\Theme\Classes
 */

namespace Cyan\Theme\Classes;

class ProductAttributes
{
	public static function init()
	{
		// Add color field to attribute terms
		add_action('pa_color_add_form_fields', [__CLASS__, 'add_color_field']);
		add_action('pa_color_edit_form_fields', [__CLASS__, 'edit_color_field'], 10, 2);
		
		// Save color field
		add_action('created_pa_color', [__CLASS__, 'save_color_field']);
		add_action('edited_pa_color', [__CLASS__, 'save_color_field']);
		
		// Add column in attributes list
		add_filter('manage_edit-pa_color_columns', [__CLASS__, 'add_color_column']);
		add_filter('manage_pa_color_custom_column', [__CLASS__, 'add_color_column_content'], 10, 3);
	}

	/**
	 * Add color field when creating new term
	 */
	public static function add_color_field()
	{
		?>
		<div class="form-field">
			<label for="term_color"><?php _e('کد رنگ', 'taghechian'); ?></label>
			<input type="color" name="term_color" id="term_color" value="#000000">
			<p class="description"><?php _e('رنگ این آیتم را انتخاب کنید', 'taghechian'); ?></p>
		</div>
		<?php
	}

	/**
	 * Add color field when editing term
	 */
	public static function edit_color_field($term, $taxonomy)
	{
		$color = get_term_meta($term->term_id, 'term_color', true);
		$color = $color ? $color : '#000000';
		?>
		<tr class="form-field">
			<th scope="row">
				<label for="term_color"><?php _e('کد رنگ', 'taghechian'); ?></label>
			</th>
			<td>
				<input type="color" name="term_color" id="term_color" value="<?php echo esc_attr($color); ?>">
				<p class="description"><?php _e('رنگ این آیتم را انتخاب کنید', 'taghechian'); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save color field
	 */
	public static function save_color_field($term_id)
	{
		if (isset($_POST['term_color'])) {
			update_term_meta($term_id, 'term_color', sanitize_hex_color($_POST['term_color']));
		}
	}

	/**
	 * Add color column to attributes list
	 */
	public static function add_color_column($columns)
	{
		$columns['color'] = __('رنگ', 'taghechian');
		return $columns;
	}

	/**
	 * Display color in column
	 */
	public static function add_color_column_content($content, $column_name, $term_id)
	{
		if ($column_name === 'color') {
			$color = get_term_meta($term_id, 'term_color', true);
			if ($color) {
				$content = '<span style="display:inline-block;width:30px;height:30px;background:' . esc_attr($color) . ';border:1px solid #ddd;border-radius:4px;"></span>';
			}
		}
		return $content;
	}
}
