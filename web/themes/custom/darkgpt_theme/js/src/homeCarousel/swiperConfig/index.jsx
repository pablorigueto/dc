// SwiperConfig.js
import { Pagination, Navigation, EffectCreative } from 'swiper/react';

export const sortingOptions = {
  default: (a, b) => b.node_view_count - a.node_view_count,
  mostViewed: (a, b) => b.node_view_count - a.node_view_count,
  lessViewed: (a, b) => a.node_view_count - b.node_view_count,
  mostLiked: (a, b) => b.likes_count - a.likes_count,
  lessLiked: (a, b) => a.likes_count - b.likes_count,
  mostCommented: (a, b) => b.comments_count - a.comments_count,
  lessCommented: (a, b) => a.comments_count - b.comments_count,
};

export const getSortedNodes = (nodes, selectedValue) =>
  nodes.slice().sort(sortingOptions[selectedValue]);

export const getTimestampSortedNodes = (nodes) =>
  nodes.slice().sort((a, b) => b.node_timestamp - a.node_timestamp);

export const breakpoints = {
  520: { slidesPerView: 2, spaceBetween: 0 },
  677: { slidesPerView: 2.5, spaceBetween: 0 },
  767: { slidesPerView: 3.5, spaceBetween: 0 },
  768: { slidesPerView: 3, spaceBetween: 0 },
  1024: { slidesPerView: 4, spaceBetween: 0 },
  1171: { slidesPerView: 4.5, spaceBetween: 0 },
};

export const commonConfig = {
  className: 'homepage-carousel',
  // Add any other common settings here
};

export const nonMobileConfig = {
  spaceBetween: 0,
  centeredSlides: false,
  pagination: {
    type: 'progressbar',
  },
  modules: [Pagination, Navigation],
  breakpoints: breakpoints,
};

export const mobileConfig = (sortedNodes) => ({
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
  initialSlide: sortedNodes.length - 1,
});
