import React from 'react';
import { createRoot } from 'react-dom/client';
import App from './searchAnimation'; // Import your App component from the appropriate path

document.addEventListener("DOMContentLoaded", function () {
    const root = document.getElementById('block-dc-theme-searchbtn');
  
    if (root) {
        const rootElement = createRoot(root);
        rootElement.render(<App />);
    } else {
        console.error("The target container 'home-content' does not exist in the DOM.");
    }
});
