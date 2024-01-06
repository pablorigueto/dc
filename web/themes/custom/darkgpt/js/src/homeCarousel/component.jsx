import React, { useState } from 'react';
import { useQuery } from 'react-query';
import { fetchNodes } from './controller';
import './assets/index.css';
import Dropdown from './dropdown';
import getSwiperConfig from './swiperBreakPoints';
import SwiperComponent from './swiperComponent';
import NodeListContainer from './nodeList'; 

const NodeList = () => {

  const swiperConfig = getSwiperConfig();

  const { data: nodes, isLoading, error } = useQuery('nodes', fetchNodes, {
    retry: 5,
    retryDelay: 1000,
  });

  const [selectedValue, setSelectedValue] = useState('');
  const [displayLabel, setDisplayLabel] = useState('Filter');
  const [isMenuOpen, setIsMenuOpen] = useState(false);

  const handleSortChange = (option) => {
    setIsMenuOpen(false);
    const optionLabels = {
      '': 'Filter',
      mostViewed: 'Most Viewed',
      lessViewed: 'Less Viewed',
      mostLiked: 'Most Liked',
      lessLiked: 'Less Liked',
      mostCommented: 'Most Commented',
      lessCommented: 'Less Commented',
    };

    setSelectedValue(option);
    setDisplayLabel(optionLabels[option]);
  };

  const toggleMenu = () => {
    setIsMenuOpen(!isMenuOpen);
  };
  
  if (isLoading) {
    return <div>Loading...</div>;
  }

  const sortingOptions = {
    default: (a, b) => b.node_view_count - a.node_view_count,
    mostViewed: (a, b) => b.node_view_count - a.node_view_count,
    lessViewed: (a, b) => a.node_view_count - b.node_view_count,
    mostLiked: (a, b) => b.likes_count - a.likes_count,
    lessLiked: (a, b) => a.likes_count - b.likes_count,
    mostCommented: (a, b) => b.comments_count - a.comments_count,
    lessCommented: (a, b) => a.comments_count - b.comments_count,
  };

  const sortedNodes = nodes.slice().sort(sortingOptions[selectedValue]);

  {/* List node, more recent first. */}
  const nodeTimeStamp = nodes
  .slice()
  .sort((a, b) => b.node_timestamp - a.node_timestamp);

  if (error) {
    return <div>Error: {error.message}</div>;
  }

  return (
    <>
      <div className="carousel-filter">
        <Dropdown
          displayLabel={displayLabel}
          isMenuOpen={isMenuOpen}
          toggleMenu={toggleMenu}
          handleSortChange={handleSortChange}
        />
      </div>

      {/* <SwiperComponent sortedNodes={sortedNodes} swiperConfig={swiperConfig} /> */}

      <NodeListContainer nodeTimeStamp={nodeTimeStamp} />

    </>
  );
}

export default NodeList;
