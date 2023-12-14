import React from 'react';
import { createRoot } from 'react-dom/client';
import App from './likeAnimation';

document.addEventListener("DOMContentLoaded", function () {
  const root = document.getElementById('like-animated');

  if (root) {
    const rootElement = createRoot(root);
    rootElement.render(<App />);
  }

});
