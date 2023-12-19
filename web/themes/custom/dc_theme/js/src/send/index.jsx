// import React from 'react';
// import ReactDOM from 'react-dom/client';
// import App from './sendAnimation';

// document.addEventListener("DOMContentLoaded", function () {
//   const post = document.getElementById('edit-submit--2');
//   console.log(post);
//   if (post) {
//     const rootElement = ReactDOM.createRoot(post);
//     rootElement.render(<App />);
//   }
// });


// import React from 'react';
// import { createRoot } from 'react-dom/client';
// import App from './sendAnimation';

// document.addEventListener("DOMContentLoaded", function () {
//   const elements = document.querySelectorAll('[id^="postAnimation"]');

//   elements.forEach((root) => {
//     const rootElement = createRoot(root);
//     rootElement.render(<App />);
//   });
// });


// import React from 'react';
// import { createRoot } from 'react-dom/client';
// import App from './sendAnimation';

// function initializeReactComponents() {
//   const elements = document.querySelectorAll('[id^="postAnimation"]');

//   elements.forEach((root) => {
//     const rootElement = createRoot(root);
//     rootElement.render(<App />);
//   });
// }

// if (document.readyState === 'complete' || (document.readyState !== 'loading' && !document.documentElement.doScroll)) {
//   // The DOM is already ready, or it's ready because it doesn't need to be scrolled.
//   initializeReactComponents();
// } else {
//   // Wait for the DOMContentLoaded event to ensure all content, including asynchronously loaded content, is present.
//   document.addEventListener('DOMContentLoaded', initializeReactComponents);
// }

// // Fallback to window.onload in case DOMContentLoaded doesn't fire (e.g., if the page contains an external script).
// window.onload = initializeReactComponents;



// form ID = comment-form
// Field that I want to render this react js:
// <input data-drupal-selector="edit-submit" type="submit" id="edit-submit--2" name="op" value="Post" class="button button--primary js-form-submit form-submit">


// // Import necessary modules
// import React from 'react';
// import { createRoot } from 'react-dom/client';
// import App from './sendAnimation';

// // Function to render the App component
// function renderApp() {
//   const root = document.getElementById("comment-form");
//   if (root) {
//     const rootElement = createRoot(root);

//     // Assuming you want to render the App component when the form is submitted
//     root.addEventListener('submit', function (event) {
//       event.preventDefault(); // Prevent the default form submission
//       rootElement.render(<App />);
//     });
//   }
// }

// // Call the renderApp function when the DOM is ready
// document.addEventListener('DOMContentLoaded', renderApp);