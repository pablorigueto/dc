import React from 'react';
import { createRoot } from 'react-dom/client';
import { QueryClient, QueryClientProvider } from 'react-query';
import NodeList from './component';

const queryClient = new QueryClient();

document.addEventListener('DOMContentLoaded', function () {
  const root = document.getElementById('carousel-content');

  if (root) {
    const rootElement = createRoot(root);
    rootElement.render(
      <QueryClientProvider client={queryClient}>
        <NodeList />
      </QueryClientProvider>
    );
  }
});
