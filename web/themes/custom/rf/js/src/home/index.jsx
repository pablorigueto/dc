import React from 'react';
import { createRoot } from 'react-dom/client';
import App from '../components/banner'; // Import your App component from the appropriate path

const root = document.getElementById('main-content');

const rootElement = createRoot(root);

rootElement.render(<App />);