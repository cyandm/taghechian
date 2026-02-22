/**
 * Main entry point for the theme's JavaScript.
 * you must add any functions for every javascript file to the import statement below.
 */

import { Modals } from "./functions/modals";
import { register } from "swiper/element/bundle";
import { SubMenuDesktop } from "./functions/subMenuDesktop";
import { SubMenuMobile } from "./functions/subMenuMobile";
import { Htmx } from "./functions/htmx";
import { FaqTabs, FaqCard } from "./functions/faq";
import videoCover from "./modules/videoCover";
import { VideoPlyr } from "./modules/plyr";
import fancybox from "./modules/fancybox";
import { shareBtn } from "./functions/shareBtn";
import { SwiperEmptySlide } from "./functions/swiperEmptySlide";
import { Accordion } from "./functions/accordion";
import { SearchPage } from "./functions/search";
import { InstockToggle } from "./functions/instockToggle";

Modals();
register();
SubMenuDesktop();
SubMenuMobile();
Htmx();
FaqTabs();
FaqCard();
VideoPlyr();
videoCover();
fancybox();
shareBtn();
//SwiperEmptySlide();
Accordion();
SearchPage();
InstockToggle();