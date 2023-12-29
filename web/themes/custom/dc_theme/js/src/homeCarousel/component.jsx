import React, { useState, useEffect } from 'react';
import { useQuery } from 'react-query';
import { fetchNodes } from './controller';
import './assets/index.css';
import Dropdown from './dropdown';
import { Swiper, SwiperSlide } from 'swiper/react';

// Import Swiper styles
import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/navigation';

// import required modules
import { Pagination, Navigation, EffectCreative } from 'swiper/modules';

const NodeList = () => {
  
  const { data: nodes, isLoading, error } = useQuery('nodes', fetchNodes, {
    retry: 5,
    retryDelay: 1000,
  });

  const [selectedValue, setSelectedValue] = useState(''); // Actual value used for sorting
  const [displayLabel, setDisplayLabel] = useState('Filter'); // Label displayed in the dropdown

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
  
  const [isMobile, setIsMobile] = useState(false);

  useEffect(() => {
    // Function to handle window resize
    const handleResize = () => {
      // Check the window width and set isMobile accordingly
      setIsMobile(window.innerWidth <= 519);
    };

    // Add event listener for window resize
    window.addEventListener('resize', handleResize);

    // Initial call to set isMobile based on the current window width
    handleResize();

    // Clean up the event listener on component unmount
    return () => {
      window.removeEventListener('resize', handleResize);
    };
  }, []); // Empty dependency array to ensure the effect runs only once on mount

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

  const breakpoints = {
    520: { slidesPerView: 2, spaceBetween: 0 },
    677: { slidesPerView: 2.5, spaceBetween: 0 },
    767: { slidesPerView: 3.5, spaceBetween: 0 },
    768: { slidesPerView: 3, spaceBetween: 0 },
    1024: { slidesPerView: 4, spaceBetween: 0 },
    1171: { slidesPerView: 4.5, spaceBetween: 0 },
  };

  const commonConfig = {
    className: 'homepage-carousel',
    // Add any other common settings here
  };

  // Configuration for mobile
  const nonMobileConfig = {
    spaceBetween: 0,
    centeredSlides: false,
    pagination: {
      type: 'progressbar',
    },
    modules: [Pagination, Navigation],
    breakpoints: breakpoints,
  };

  // Configuration for non-mobile
  const mobileConfig = {
    grabCursor: true,
    effect: 'creative',
    creativeEffect: {
      prev: {
        shadow: false,
        translate: [0, 0, -400],
      },
      next: {
        translate: ['100%', 0, 0],
      },
    },
    modules: [EffectCreative],
    initialSlide: sortedNodes.length - 1,
  };

    // Merge commonConfig with the specific configuration based on the isMobile condition
    const swiperConfig = isMobile
    ? { ...commonConfig, ...mobileConfig }
    : { ...commonConfig, ...nonMobileConfig };

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
        
      <Swiper {...swiperConfig}>
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
                    <div className="count-frontpage">

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
                    </div>

                    <div className="profile-frontpage">

                      <div className="profile-img-name">
                        <img className="profile-frontpage-img" src={node.node_owner_image_profile} alt="User Profile Image" />
                        <div className="owner-frontpage">
                          {node.node_owner_name}
                        </div>
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
        <div class="recent-section">
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
