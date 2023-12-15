import React from 'react';
import { createRoot } from 'react-dom/client';
import App from './likeAnimation';

document.addEventListener("DOMContentLoaded", function () {
  const elements = document.querySelectorAll('[id^="like-animated"]');

  elements.forEach((root) => {
    const rootElement = createRoot(root);
    rootElement.render(<App />);
  });
});
