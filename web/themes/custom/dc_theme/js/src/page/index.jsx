import React from 'react';
import { createRoot } from 'react-dom/client';
import App from '../components/bannerPage'; // Import your App component from the appropriate path

document.addEventListener("DOMContentLoaded", function () {
    const root = document.getElementById('block-dc-content');
  
    if (root) {
        const rootElement = createRoot(root);
        rootElement.render(<App />);
    } else {
        //console.error("The target container 'block-dc-content' does not exist in the DOM.");
    }
  });
