import React from 'react';
import { createRoot } from 'react-dom/client';
import App from './homeStructure'; // Import your App component from the appropriate path

document.addEventListener("DOMContentLoaded", function () {
    const root = document.getElementById('homepage-id');
  
    if (root) {
        const rootElement = createRoot(root);
        rootElement.render(<App />);
    } else {
        console.error("The target container 'home-content' does not exist in the DOM.");
    }
  });

