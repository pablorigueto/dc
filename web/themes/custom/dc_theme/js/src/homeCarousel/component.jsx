import React, { useState } from 'react';
import { useQuery } from 'react-query';
import { fetchNodes } from './controller';
import './assets/index.css';
import Dropdown from './dropdown';
import getSwiperConfig from './swiperBreakPoints';
import SwiperComponent from './swiperComponent';

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

      <SwiperComponent sortedNodes={sortedNodes} swiperConfig={swiperConfig} />

      <div className="node-list-container">
        <div className="recent-section">
          <h1>Recent Section</h1>
        </div>
        {/* List node, more recent first. */}
        {nodeTimeStamp.map((node) => (
          <a className="node-list-path" href={node.node_path} key={`mr-${node.id}`}>

            <div className="node-list-main">

              <div className="node-list-tags-main">
                {node.tags.map((tag) => (
                  <div key={`tag-${tag.id}`} className={`node-list-tags ${tag.alias.toLowerCase()}`}>
                    {tag.alias}
                  </div>
                ))}
              </div>

              <div className="node-list-title-main">
                <h3 className="node-list-title">
                  {node.title}
                </h3>
              </div>

              <div className="node-list-details-main">
                <div className="node-list-created">
                  {node.node_created}
                </div>

                <div className="node-list-views-count">
                  {node.node_view_count}
                  <span className="material-symbols-outlined">
                    visibility
                  </span>
                </div>

                <div className="node-list-comments-count">
                  {node.comments_count}
                  <span className="material-symbols-outlined">
                    chat_bubble
                  </span>
                </div>

                <div className="node-list-likes-count">
                  {node.likes_count}
                  <span className="material-symbols-outlined">
                    thumb_up
                  </span>
                </div>

              </div>

            </div>
  
            <div className="node-list-image">
              <img className="node-list-img" src={node.url} alt={node.alt} />
            </div>

          </a>
        ))}
      </div>
    </>
  );
}

export default NodeList;
