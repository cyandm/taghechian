/**
 * Adds a fixed text slide that stays in the middle of visible slides
 */
export function SwiperEmptySlide() {
  document.addEventListener("DOMContentLoaded", () => {
    const swiperContainers = document.querySelectorAll("swiper-container");

    swiperContainers.forEach((swiperContainer) => {
      // Small delay to ensure Swiper is fully initialized and DOM is ready
      setTimeout(() => {
        const swiper = swiperContainer.swiper;

        if (!swiper) return;

        // Create the custom slide template (once per swiper instance setup)
        const customSlideTemplate = document.createElement("swiper-slide");
        customSlideTemplate.className = "!w-[130px]"; // Ensure fixed width
        customSlideTemplate.setAttribute("data-custom-slide", "true"); // Marker for easy identification

        const customContent = document.createElement("div");
        customContent.className =
          "relative h-[380px] flex items-center justify-center bg-gradient-to-br from-cyan-500 to-blue-600 rounded-lg overflow-hidden";
        customContent.innerHTML = `
          <div class="absolute inset-0 bg-black/20"></div>
          <div class="relative z-10 p-8 text-center text-white flex flex-col gap-4">
            <p class="text-2xl md:text-3xl font-bold">مطالب ویژه</p>
            <p class="text-base md:text-lg font-medium opacity-90">جدیدترین مقالات مد و فشن</p>
          </div>
        `;
        customSlideTemplate.appendChild(customContent);

        let isUpdatingCustomSlide = false; // Flag for this specific swiper instance to prevent re-entrancy

        // Define the actual logic for placing the custom slide
        const placeCustomSlideLogic = () => {
          // Don't update if already updating or if swiper is currently animating
          if (isUpdatingCustomSlide || swiper.animating) {
            return;
          }
          isUpdatingCustomSlide = true;

          // Temporarily remove event listener to prevent recursion
          swiper.off("transitionEnd", placeCustomSlideLogic);

          // Remove any existing custom slide in this container
          const existingCustomSlide = swiperContainer.querySelector(
            '[data-custom-slide="true"]'
          );
          if (existingCustomSlide) {
            existingCustomSlide.remove();
          }

          // Get all current Swiper slides in the DOM (excluding duplicates and our custom one)
          const allSwiperSlides = Array.from(swiper.slides).filter(
            (slide) => !slide.getAttribute("data-custom-slide")
          );

          if (allSwiperSlides.length === 0) {
            isUpdatingCustomSlide = false;
            setTimeout(() => {
              swiper.on("transitionEnd", placeCustomSlideLogic);
            }, 0);
            return;
          }

          // Find the slide with swiper-slide-next class (the slide that comes after active)
          const nextSlide = swiperContainer.querySelector(".swiper-slide-next");

          if (nextSlide && nextSlide.parentNode) {
            const swiperWrapper = nextSlide.parentNode;
            const newCustomSlide = customSlideTemplate.cloneNode(true);

            // Insert the custom slide after the next slide
            const insertAfterNext = nextSlide.nextElementSibling;
            if (insertAfterNext) {
              swiperWrapper.insertBefore(newCustomSlide, insertAfterNext);
            } else {
              swiperWrapper.appendChild(newCustomSlide);
            }
          } else {
            // Fallback: Try to find the swiper-wrapper directly
            const swiperWrapper =
              swiperContainer.querySelector(".swiper-wrapper");
            if (swiperWrapper && allSwiperSlides.length > 0) {
              const newCustomSlide = customSlideTemplate.cloneNode(true);
              // Insert after the first slide as a fallback
              if (allSwiperSlides[0] && allSwiperSlides[0].nextElementSibling) {
                swiperWrapper.insertBefore(
                  newCustomSlide,
                  allSwiperSlides[0].nextElementSibling
                );
              } else {
                swiperWrapper.appendChild(newCustomSlide);
              }
            }
          }

          // Important: Call swiper.update() to recalculate sizes and positions
          swiper.update();

          // Re-attach event listener after a small delay
          setTimeout(() => {
            swiper.on("transitionEnd", placeCustomSlideLogic);
            isUpdatingCustomSlide = false; // Reset the flag
          }, 0);
        };

        // Initial placement when DOM is ready and Swiper is initialized
        requestAnimationFrame(() => {
          placeCustomSlideLogic();
        });

        // Only use transitionEnd event to avoid jumping during transitions
        swiper.on("transitionEnd", placeCustomSlideLogic);
      }, 0); // Delay to ensure Swiper is fully initialized
    });
  });
}
