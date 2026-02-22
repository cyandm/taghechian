/**
 * سوئیچ «نمایش کالاهای موجود» در سایدبار فروشگاه
 */
export function InstockToggle() {
	document.querySelectorAll('.shop-sidebar [data-instock-toggle]').forEach((btn) => {
		btn.addEventListener('click', function () {
			const on = this.getAttribute('aria-checked') !== 'true';
			this.setAttribute('aria-checked', on ? 'true' : 'false');
			this.classList.toggle('is-on', on);
			const url = new URL(window.location.href);
			if (on) {
				url.searchParams.set('instock', '1');
			} else {
				url.searchParams.delete('instock');
			}
			window.location.href = url.toString();
		});
	});
}
