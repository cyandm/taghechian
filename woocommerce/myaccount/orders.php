<?php

/**
 * Orders
 *
 * Shows orders on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/orders.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.5.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_account_orders', $has_orders); ?>

<?php if ($has_orders) : ?>

	<div class="bg-white border-gray-200 overflow-hidden">
		<!-- Header - Desktop -->
		<div class="hidden lg:flex items-center justify-between px-6 py-4 border-gray-200">
			<h2 class="text-3xl font-[dinar] text-gray-900">تاریخچه سفارش ها</h2>
			<div class="flex items-center gap-2">
				<label for="orders-sort-desktop" class="sr-only">مرتب سازی سفارش ها</label>
				<svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
				</svg>
				<select id="orders-sort-desktop"
					class="text-sm border border-gray-300 rounded-full px-4 py-2 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">
					<option value="newest">جدیدترین</option>
					<option value="oldest">قدیمی ترین</option>
				</select>
			</div>
		</div>

		<!-- Mobile Header with Sort Button -->
		<div class="lg:hidden flex px-1 items-center justify-between lg:px-4 py-4">
			<h2 class="text-xl font-[dinar] text-gray-900">تاریخچه سفارش ها</h2>
			<div class="flex items-center gap-2">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
				</svg>
				<label for="orders-sort-mobile" class="sr-only">مرتب سازی سفارش ها</label>
				<select id="orders-sort-mobile"
					class="text-sm border border-gray-300 rounded-full px-3 py-2 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">
					<option value="newest">جدیدترین</option>
					<option value="oldest">قدیمی ترین</option>
				</select>
			</div>
		</div>

		<!-- Tabs -->
		<div class="px-1  lg:px-6 pt-2">

			<!-- Mobile Tabs -->
			<div class="flex lg:hidden gap-2 mb-4" id="order-tabs-mobile">
				<button
					class="tab-btn-mobile flex-1 text-center py-1.5 rounded-full border text-xs font-medium transition-colors duration-200 bg-blue-50 text-blue-600 border-blue-500"
					data-filter="current">
					جاری
				</button>

				<button
					class="tab-btn-mobile flex-1 text-center py-1.5 rounded-full border text-xs font-medium transition-colors duration-200 bg-gray-100 text-gray-500 border-gray-300"
					data-filter="completed">
					تحویل داده شده
				</button>

				<button
					class="tab-btn-mobile flex-1 text-center py-1.5 rounded-full border text-xs font-medium transition-colors duration-200 bg-gray-100 text-gray-500 border-gray-300"
					data-filter="cancelled">
					لغو شده
				</button>
			</div>

			<!-- Desktop Tabs -->
			<div class="hidden lg:flex items-center gap-8 border-b border-gray-200 pb-3" id="order-tabs-desktop">
				<button class="tab-btn-desktop text-sm font-medium border-b-2 border-yellow-400 text-gray-900 pb-3"
					data-filter="current">
					جاری
				</button>

				<button class="tab-btn-desktop text-sm font-medium text-gray-500 pb-3" data-filter="completed">
					تحویل داده شده
				</button>

				<button class="tab-btn-desktop text-sm font-medium text-gray-500 pb-3" data-filter="cancelled">
					لغو شده
				</button>
			</div>

		</div>

		<!-- Orders List Container - Desktop -->
		<div class="hidden lg:block divide-y divide-gray-100" id="orders-list-desktop">
			<?php
			foreach ($customer_orders->orders as $customer_order) {
				$order = wc_get_order($customer_order);
				if (!$order) continue;

				$item_count = $order->get_item_count() - $order->get_item_count_refunded();

				// Get first product image & name
				$items = $order->get_items();
				$first_item = reset($items);
				$product = $first_item ? $first_item->get_product() : null;
				$image_url = $product ? wp_get_attachment_image_url($product->get_image_id(), 'thumbnail') : wc_placeholder_img_src();
				$product_name = $first_item ? $first_item->get_name() : 'محصول حذف شده';

				// Shipping method
				$shipping_method = $order->get_shipping_method();
				if (empty($shipping_method)) $shipping_method = '—';

				$order_created = $order->get_date_created();
				$order_timestamp = $order_created ? $order_created->getTimestamp() : 0;

				// Status badge and filter group
				$status = $order->get_status();
				switch ($status) {
					case 'completed':
						$badge_text = 'تحویل داده شده';
						$badge_class = 'bg-green-50 text-green-600';
						$filter_group = 'completed';
						break;
					case 'processing':
						$badge_text = 'در حال پردازش';
						$badge_class = 'bg-blue-50 text-blue-600';
						$filter_group = 'current';
						break;
					case 'pending':
						$badge_text = 'در انتظار پرداخت';
						$badge_class = 'bg-yellow-50 text-yellow-600';
						$filter_group = 'current';
						break;
					case 'on-hold':
						$badge_text = 'در انتظار';
						$badge_class = 'bg-orange-50 text-orange-600';
						$filter_group = 'current';
						break;
					case 'cancelled':
						$badge_text = 'لغو شده';
						$badge_class = 'bg-red-50 text-red-600';
						$filter_group = 'cancelled';
						break;
					case 'refunded':
						$badge_text = 'بازپرداخت شده';
						$badge_class = 'bg-gray-50 text-gray-600';
						$filter_group = 'cancelled';
						break;
					case 'failed':
						$badge_text = 'ناموفق';
						$badge_class = 'bg-red-50 text-red-600';
						$filter_group = 'cancelled';
						break;
					default:
						$badge_text = $status;
						$badge_class = 'bg-gray-50 text-gray-600';
						$filter_group = 'current';
				}
			?>
				<div class="order-row flex items-center gap-4 px-6 py-4 hover:bg-gray-50"
					data-status="<?php echo esc_attr($filter_group); ?>"
					data-order-date="<?php echo esc_attr($order_timestamp); ?>">
					<!-- Product Image -->
					<div class="flex-shrink-0">
						<img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($product_name); ?>"
							class="w-16 h-16 object-cover rounded-lg">
					</div>

					<!-- Order Details -->
					<div class="flex-1 flex items-center justify-between gap-4 text-sm">
						<!-- Order Number & Product Name -->
						<div class="text-right min-w-[120px]">
							<p class="text-gray-500 mb-1"><?php echo esc_html($product_name); ?></p>
							<a href="<?php echo esc_url($order->get_view_order_url()); ?>" class="text-gray-900 font-medium">
								#<?php echo esc_html($order->get_order_number()); ?>
							</a>
						</div>

						<!-- Shipping Method -->
						<div class="text-center min-w-[100px]">
							<p class="text-gray-900"><?php echo esc_html($shipping_method); ?></p>
						</div>

						<!-- Date -->
						<div class="text-center min-w-[120px]">
							<time datetime="<?php echo esc_attr($order_created ? $order_created->date('c') : ''); ?>"
								class="text-gray-900">
								<?php echo esc_html($order_created ? $order_created->date_i18n('j F Y') : '—'); ?>
							</time>
						</div>

						<!-- Total -->
						<div class="text-center min-w-[100px]">
							<p class="text-gray-900 font-medium">
								<?php echo wp_kses_post($order->get_formatted_order_total()); ?>
							</p>
						</div>

						<!-- Status Badge -->
						<div class="text-center min-w-[120px]">
							<span
								class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm <?php echo esc_attr($badge_class); ?>">
								<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
									<path fill-rule="evenodd"
										d="M16.707 5.293a1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
										clip-rule="evenodd" />
								</svg>
								<?php echo esc_html($badge_text); ?>
							</span>
						</div>

						<!-- Actions -->
						<div class="flex items-center gap-2">
							<a href="<?php echo esc_url($order->get_view_order_url()); ?>"
								class="text-blue-500 flex items-center gap-1">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
								</svg>
								مشاهده فاکتور
							</a>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>

		<!-- Orders List Container - Mobile -->
		<div class="lg:hidden space-y-4 py-4" id="orders-list-mobile">
			<?php
			foreach ($customer_orders->orders as $customer_order) {
				$order = wc_get_order($customer_order);
				if (!$order) continue;

				// Get first product image & name
				$items = $order->get_items();
				$first_item = reset($items);
				$product = $first_item ? $first_item->get_product() : null;
				$image_url = $product ? wp_get_attachment_image_url($product->get_image_id(), 'thumbnail') : wc_placeholder_img_src();
				$product_name = $first_item ? $first_item->get_name() : 'محصول حذف شده';

				// Category from first product
				$product_category_label = '--';
				if ($product instanceof WC_Product) {
					$category_ids = $product->get_category_ids();
					if (!empty($category_ids)) {
						$first_category = get_term((int) $category_ids[0], 'product_cat');
						if ($first_category && !is_wp_error($first_category)) {
							$product_category_label = $first_category->name;
						}
					}
				}

				$order_created = $order->get_date_created();
				$order_timestamp = $order_created ? $order_created->getTimestamp() : 0;

				// Status badge and filter group
				$status = $order->get_status();
				switch ($status) {
					case 'completed':
						$badge_text = 'تحویل داده شده';
						$badge_class = 'text-green-600';
						$badge_icon = '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>';
						$filter_group = 'completed';
						break;
					case 'processing':
					case 'pending':
					case 'on-hold':
						$badge_text = 'در انتظار پرداخت';
						$badge_class = 'text-gray-700';
						$badge_icon = '<svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>';
						$filter_group = 'current';
						break;
					case 'cancelled':
					case 'failed':
						$badge_text = 'لغو شده';
						$badge_class = 'text-red-600';
						$badge_icon = '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>';
						$filter_group = 'cancelled';
						break;
					case 'refunded':
						$badge_text = 'بازپرداخت شده';
						$badge_class = 'text-gray-600';
						$badge_icon = '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/></svg>';
						$filter_group = 'cancelled';
						break;
					default:
						$badge_text = $status;
						$badge_class = 'text-gray-600';
						$badge_icon = '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8"/></svg>';
						$filter_group = 'current';
				}
			?>
				<div class="order-card rounded-2xl border border-gray-200 overflow-hidden bg-white w-full"
					data-status="<?php echo esc_attr($filter_group); ?>"
					data-order-date="<?php echo esc_attr($order_timestamp); ?>">

					<!-- Product Row -->
					<div class="flex border-b border-gray-200">
						<div class="w-24 shrink-0 bg-gray-50 flex items-center justify-center border-l border-gray-200 p-3">
							<span class="text-sm font-medium text-gray-800">محصول</span>
						</div>
						<div class="flex-1 flex items-center justify-start gap-3 p-3 bg-white">
							<img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($product_name); ?>"
								class="w-12 h-12 object-cover rounded-lg shrink-0 border border-gray-100">
							<span class="text-sm text-gray-700  line-clamp-2"><?php echo esc_html($product_name); ?></span>
						</div>
					</div>

					<!-- Category Row -->
					<div class="flex border-b border-gray-200">
						<div class="w-24 shrink-0 bg-gray-50 flex items-center justify-center border-l border-gray-200 p-3">
							<span class="text-sm font-medium text-gray-800">دسته بندی</span>
						</div>
						<div class="flex-1 flex items-center p-3 bg-white">
							<span class="text-sm text-gray-700"><?php echo esc_html($product_category_label); ?></span>
						</div>
					</div>

					<!-- Date Row -->
					<div class="flex border-b border-gray-200">
						<div class="w-24 shrink-0 bg-gray-50 flex items-center justify-center border-l border-gray-200 p-3">
							<span class="text-sm font-medium text-gray-800">تاریخ</span>
						</div>
						<div class="flex-1 flex items-center  p-3 bg-white">
							<time datetime="<?php echo esc_attr($order_created ? $order_created->date('c') : ''); ?>"
								class="text-sm text-gray-700">
								<?php echo esc_html($order_created ? $order_created->date_i18n('j F Y') : '—'); ?>
							</time>
						</div>
					</div>

					<!-- Price Row -->
					<div class="flex border-b border-gray-200">
						<div class="w-24 shrink-0 bg-gray-50 flex items-center justify-center border-l border-gray-200 p-3">
							<span class="text-sm font-medium text-gray-800">قیمت</span>
						</div>
						<div class="flex-1 flex items-center p-3 bg-white">
							<span class="text-sm text-gray-700">
								<?php echo number_format($order->get_total(), 0, '.', ',') . ' تومان'; ?>
							</span>
						</div>
					</div>

					<!-- Status Row -->
					<div class="flex">
						<div class="w-24 shrink-0 bg-gray-50 flex items-center justify-center border-l border-gray-200 p-3">
							<span class="text-sm font-medium text-gray-800">وضعیت</span>
						</div>
						<div class="flex-1 flex items-center justify-between p-3 bg-white">
							<span class="inline-flex items-center gap-1 text-sm <?php echo esc_attr($badge_class); ?>">
								<?php echo esc_html($badge_text); ?>
								<?php echo $badge_icon; ?>
							</span>
							<a href="<?php echo esc_url($order->get_view_order_url()); ?>"
								class="text-sm text-blue-500 flex items-center gap-1 hover:text-blue-600 transition-colors">
								مشاهده فاکتور
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
								</svg>
							</a>

						</div>
					</div>

				</div>
			<?php } ?>
		</div>


		<?php do_action('woocommerce_before_account_orders_pagination'); ?>

		<?php if (1 < $customer_orders->max_num_pages) : ?>
			<div class="flex justify-center gap-4 mt-6">
				<?php if (1 !== $current_page) : ?>
					<a class="px-6 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
						href="<?php echo esc_url(wc_get_endpoint_url('orders', $current_page - 1)); ?>">قبلی</a>
				<?php endif; ?>

				<?php if (intval($customer_orders->max_num_pages) !== $current_page) : ?>
					<a class="px-6 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
						href="<?php echo esc_url(wc_get_endpoint_url('orders', $current_page + 1)); ?>">بعدی</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<!-- JavaScript for tab filtering -->
		<script>
			(function() {
				const desktopSortControl = document.getElementById('orders-sort-desktop');
				const mobileSortControl = document.getElementById('orders-sort-mobile');
				const mobileTabs = document.querySelectorAll('#order-tabs-mobile .tab-btn-mobile');
				const desktopTabs = document.querySelectorAll('#order-tabs-desktop .tab-btn-desktop');
				const desktopContainer = document.getElementById('orders-list-desktop');
				const mobileContainer = document.getElementById('orders-list-mobile');
				const desktopRows = document.querySelectorAll('#orders-list-desktop .order-row');
				const mobileCards = document.querySelectorAll('#orders-list-mobile .order-card');

				function filterOrders(status) {
					// Filter desktop rows
					desktopRows.forEach(row => {
						if (status === 'current') {
							if (row.getAttribute('data-status') === 'current') {
								row.style.display = '';
							} else {
								row.style.display = 'none';
							}
						} else {
							if (row.getAttribute('data-status') === status) {
								row.style.display = '';
							} else {
								row.style.display = 'none';
							}
						}
					});

					// Filter mobile cards
					mobileCards.forEach(card => {
						if (status === 'current') {
							if (card.getAttribute('data-status') === 'current') {
								card.style.display = '';
							} else {
								card.style.display = 'none';
							}
						} else {
							if (card.getAttribute('data-status') === status) {
								card.style.display = '';
							} else {
								card.style.display = 'none';
							}
						}
					});
				}

				function getOrderTimestamp(orderNode) {
					const dateValue = orderNode.getAttribute('data-order-date');
					const parsedDate = Number.parseInt(dateValue || '0', 10);
					return Number.isNaN(parsedDate) ? 0 : parsedDate;
				}

				function sortOrders(sortMode) {
					if (desktopContainer) {
						const sortedDesktopRows = Array.from(desktopContainer.querySelectorAll('.order-row')).sort((a,
							b) => {
							const first = getOrderTimestamp(a);
							const second = getOrderTimestamp(b);
							return sortMode === 'oldest' ? first - second : second - first;
						});
						sortedDesktopRows.forEach(row => desktopContainer.appendChild(row));
					}

					if (mobileContainer) {
						const sortedMobileCards = Array.from(mobileContainer.querySelectorAll('.order-card')).sort((a,
							b) => {
							const first = getOrderTimestamp(a);
							const second = getOrderTimestamp(b);
							return sortMode === 'oldest' ? first - second : second - first;
						});
						sortedMobileCards.forEach(card => mobileContainer.appendChild(card));
					}
				}

				function handleSortChange(sortMode, source) {
					sortOrders(sortMode);

					if (source !== 'desktop' && desktopSortControl) {
						desktopSortControl.value = sortMode;
					}

					if (source !== 'mobile' && mobileSortControl) {
						mobileSortControl.value = sortMode;
					}
				}

				function setActiveMobileTab(activeTab) {
					mobileTabs.forEach(tab => {
						tab.classList.remove('bg-blue-50', 'text-blue-600', 'border-blue-500');
						tab.classList.add('bg-gray-100', 'text-gray-500', 'border-gray-300');

						if (tab === activeTab) {
							tab.classList.remove('bg-gray-100', 'text-gray-500', 'border-gray-300');
							tab.classList.add('bg-blue-50', 'text-blue-600', 'border-blue-500');
						}
					});
				}

				function setActiveDesktopTab(activeTab) {
					desktopTabs.forEach(tab => {
						tab.classList.remove('border-b-2', 'border-yellow-400', 'text-gray-900');
						tab.classList.add('text-gray-500');

						if (tab === activeTab) {
							tab.classList.remove('text-gray-500');
							tab.classList.add('border-b-2', 'border-yellow-400', 'text-gray-900');
						}
					});
				}

				mobileTabs.forEach(tab => {
					tab.addEventListener('click', function() {
						const filter = this.getAttribute('data-filter');
						filterOrders(filter);
						setActiveMobileTab(this);
					});
				});

				desktopTabs.forEach(tab => {
					tab.addEventListener('click', function() {
						const filter = this.getAttribute('data-filter');
						filterOrders(filter);
						setActiveDesktopTab(this);
					});
				});

				if (desktopSortControl) {
					desktopSortControl.addEventListener('change', function() {
						handleSortChange(this.value, 'desktop');
					});
				}

				if (mobileSortControl) {
					mobileSortControl.addEventListener('change', function() {
						handleSortChange(this.value, 'mobile');
					});
				}

				// Initial filter: show "جاری" (current)
				sortOrders('newest');
				filterOrders('current');
			})();
		</script>

	<?php else : ?>

		<div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
			<p class="text-gray-500 mb-4">هنوز سفارشی ثبت نشده است.</p>
			<a class="inline-block px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600"
				href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>">مشاهده
				محصولات</a>
		</div>

	<?php endif; ?>

	<?php do_action('woocommerce_after_account_orders', $has_orders); ?>