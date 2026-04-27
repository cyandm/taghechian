/**
 * Opens sitewide login-required modal when guest clicks wishlist heart.
 */
export function Wishlist() {
  const guestHeartButtons = document.querySelectorAll(".wishlist-heart-guest");
  if (!guestHeartButtons.length) {
    return;
  }

  const loginRequiredModalName = "login-required-modal";
  const loginRequiredModal = document.querySelector(
    `[modal][data-modal-name="${loginRequiredModalName}"]`,
  );
  const modalBackdrop = document.querySelector("[modal-backdrop]");
  const loginLink = document.querySelector("#wishlist-login-required-link");

  const currentUrl = window.location.href;
  if (loginLink instanceof HTMLAnchorElement) {
    loginLink.href = `/my-account/?redirect_to=${encodeURIComponent(currentUrl)}`;
  }

  guestHeartButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
      event.preventDefault();

      if (!loginRequiredModal) {
        return;
      }

      loginRequiredModal.setAttribute("data-active", "true");
      document.body.style.overflow = "hidden";

      if (modalBackdrop) {
        modalBackdrop.setAttribute("data-active", "true");
      }
    });
  });
}
