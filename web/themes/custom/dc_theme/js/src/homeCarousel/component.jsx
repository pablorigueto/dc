import React, { useState } from 'react';
import { useQuery } from 'react-query';
import { fetchNodes } from './controller';
import './assets/index.css';

import { Swiper, SwiperSlide } from 'swiper/react';

// Import Swiper styles
import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/navigation';

// import required modules
import { Pagination, Navigation } from 'swiper/modules';

const NodeList = () => {
  
  const { data: nodes, isLoading, error } = useQuery('nodes', fetchNodes, {
    retry: 5,
    retryDelay: 1000,
  });

  const [sortOption, setSortOption] = useState('Filter');

  const handleSortChange = (option) => {
    setSortOption(option);
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

  const sortedNodes = nodes.slice().sort(sortingOptions[sortOption]);

  {/* List node, more recent first. */}
  const nodeTimeStamp = nodes
  .slice()
  .sort((a, b) => b.node_timestamp - a.node_timestamp);

  if (error) {
    return <div>Error: {error.message}</div>;
  }

  const breakpoints = {
    // when window width is >= 320px
    320: {
      slidesPerView: 1,
      spaceBetween: 10,
    },
    // when window width is >= 480px
    480: {
      slidesPerView: 2,
      spaceBetween: 20,
    },
    // when window width is >= 640px
    640: {
      slidesPerView: 3,
      spaceBetween: 30,
    },
    // when window width is >= 768px
    768: {
      slidesPerView: 4,
      spaceBetween: 30,
    },
  };

  return (
    <>
      {/* <div className="carousel-filter">
        <select value={sortOption} onChange={(e) => handleSortChange(e.target.value)}>
          <option disabled selected>Filter</option>
          <option value="mostViewed">Most Viewed</option>
          <option value="lessViewed">Less Viewed</option>
          <option value="mostLiked">Most Liked</option>
          <option value="lessLiked">Less Liked</option>
          <option value="mostCommented">Most Commented</option>
          <option value="lessCommented">Less Commented</option>
        </select>
      </div> */}

      <div class="carousel-filter">
        <div class="custom-dropdown">
          <span class="selected-option">Select an option</span>
          <ul class="options-list">
            <li data-value="" class="option disabled">Select an option</li>
            <li data-value="mostViewed" class="option disabled">Most Viewed</li>
            <li data-value="lessViewed" class="option disabled">Less Viewed</li>
            <li data-value="mostLiked" class="option">Most Liked</li>
            <li data-value="lessLiked" class="option">Less Liked</li>
            <li data-value="mostCommented" class="option">Most Commented</li>
            <li data-value="lessCommented" class="option">Less Commented</li>
          </ul>
        </div>
      </div>

      <Swiper
        slidesPerView={4}
        spaceBetween={30}
        centeredSlides={false}
        pagination={{
          type: 'progressbar',
        }}
        modules={[Pagination, Navigation]}
        className="homepage-carousel"
        breakpoints={breakpoints}
      >

        {sortedNodes.map((node) => (
          <SwiperSlide key={node.id}>

            <a className="node-path" href={node.node_path}>

              <div className="frontpage-result-thumbnail">

                <div className="teaser-tag-group">
                  {node.tags.map((tag) => (
                    <div key={tag.id} className={`frontpage-label ${tag.alias.toLowerCase()}`}>
                      {tag.alias}
                    </div>
                  ))}
                </div>

                <div className='frontpage-title-display'>
                  <div className="frontpage-title">{node.title}</div>
                    <div className="profile-frontpage">

                      <div className="profile-img-name">
                        <img className="profile-frontpage-img" src={node.node_owner_image_profile} alt="User Profile Image" />
                        <div className="owner-frontpage">
                          {node.node_owner_name}
                        </div>
                      </div>

                      <div className="views_node_count">
                        {node.node_view_count}
                        <span className="material-symbols-outlined">
                          visibility
                        </span>
                      </div>

                      <div className="comments_count">
                        {node.comments_count}
                        <span className="material-symbols-outlined">
                          chat_bubble
                        </span>
                      </div>

                      <div className="likes_count">
                        {node.likes_count}
                        <span className="material-symbols-outlined">
                          thumb_up
                        </span>
                      </div>
                      {node.node_created}
                    </div>
                  </div>
                  <img src={node.url} alt={node.alt} />
              </div>
            </a>
          </SwiperSlide>
        ))}
      </Swiper>

      <div className="node-list-container">             
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
                <h2 className="node-list-title">
                  {node.title}
                </h2>
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
