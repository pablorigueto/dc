// swiperConfig.js
import { Pagination, Navigation, EffectCreative } from 'swiper/modules';
import useWindowSize from '../useWindowSize';

const getSwiperConfig = () => {
  const isMobile = useWindowSize();

  const breakpoints = {
    520: { slidesPerView: 2, spaceBetween: 0 },
    677: { slidesPerView: 2.5, spaceBetween: 0 },
    767: { slidesPerView: 3.5, spaceBetween: 0 },
    768: { slidesPerView: 3, spaceBetween: 0 },
    1024: { slidesPerView: 4, spaceBetween: 0 },
    1171: { slidesPerView: 4.5, spaceBetween: 0 },
  };

  const commonConfig = {
    className: 'homepage-carousel',
  };

  // Configuration for mobile
  const nonMobileConfig = {
    spaceBetween: 0,
    centeredSlides: false,
    pagination: {
      type: 'progressbar',
    },
    modules: [Pagination, Navigation],
    breakpoints: breakpoints,
  };

  // Configuration for non-mobile
  const mobileConfig = {
    grabCursor: true,
    effect: 'creative',
    creativeEffect: {
      prev: {
        shadow: false,
        translate: [0, 0, -400],
      },
      next: {
        translate: ['100%', 0, 0],
      },
    },
    modules: [EffectCreative],
    initialSlide: 999 - 1,//sortedNodes.length - 1,
  };

  // Merge commonConfig with the specific configuration based on the isMobile condition
  return isMobile ? { ...commonConfig, ...mobileConfig } : { ...commonConfig, ...nonMobileConfig };
};

export default getSwiperConfig;
