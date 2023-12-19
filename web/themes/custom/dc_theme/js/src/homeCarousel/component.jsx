// NodeList.js
import React from 'react';
import { useQuery } from 'react-query';
import { fetchNodes } from './controller';

const NodeList = () => {
  const { data: nodes, isLoading, error } = useQuery('nodes', fetchNodes, {
    retry: 5,
    retryDelay: 1000,
  });

  if (isLoading) {
    return <div>Loading...</div>;
  }

  if (error) {
    return <div>Error: {error.message}</div>;
  }

  return (
    <div>
      <h2>Node List</h2>
      <ul>
        {nodes.map((node) => (
          <li key={node.id}>
            <strong>ID:</strong> {node.id},
            <strong>Title:</strong> {node.title},
            <img src={node.url} alt={node.alt} />
          </li>
        ))}
      </ul>
    </div>
  );
};

export default NodeList;
