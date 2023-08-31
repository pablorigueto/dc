import React from 'react';
import { createRoot } from 'react-dom/client';
import App from '../components/bannerPage'; // Import your App component from the appropriate path

const root = document.getElementById('block-rf-content');

const rootElement = createRoot(root);

rootElement.render(<App />);
