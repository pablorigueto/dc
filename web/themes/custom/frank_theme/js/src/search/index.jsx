import React from 'react';
import { createRoot } from 'react-dom/client';
import App from './searchAnimation';

document.addEventListener("DOMContentLoaded", function () {
  const root = document.getElementById('block-dc-theme-searchbtn-2');
  const root_mobile = document.getElementById('search_mobile_icon');

  if (root && window.innerWidth >= 1024) {
    const rootElement = createRoot(root);
    rootElement.render(<App />);
  }
  else if(root_mobile && window.innerWidth < 1024) {
    const rootElement = createRoot(root_mobile);
    rootElement.render(<App />);
  }
//   else {
//     // console.log("The target container 'home-content' does not exist in the DOM.");
//   }
});
