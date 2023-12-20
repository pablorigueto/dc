import React from 'react';
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

  if (isLoading) {
    return <div>Loading...</div>;
  }

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

        {nodes.map((node) => (
          <SwiperSlide key={node.id}>
            <div className="frontpage-result-thumbnail">
              <div className="teaser-tag-group">

              {node.tags.map((tag) => (
                <div key={tag.id} className={`frontpage-label page ${tag.alias.toLowerCase()}`}>
                  {tag.alias}
                </div>
              ))}

              </div>
              <div className='frontpage-title-display'>
                <div className="frontpage-title">{node.title}</div>
                  <div className="profile-frontpage">

                    <div className="profile-img-name">
                      <img className="profile-frontpage-img" src="/sites/default/files/pictures/2023-11/cane-corso-p.jpg" alt="User Profile Image" />
                      <div className="owner-frontpage">
                        admin
                      </div>
                    </div>

                    <div className="views_node_count">
                      382
                      <span className="material-symbols-outlined">
                        visibility
                      </span>
                    </div>
                    
                    <div className="comments_count">
                      13
                      <span className="material-symbols-outlined">
                        chat_bubble
                      </span>
                    </div>

                    <div className="likes_count">
                      9
                      <span className="material-symbols-outlined">
                        thumb_up
                      </span>
                    </div>
                    Nov, 23
                  </div>
                </div>
                <img src={node.url} alt={node.alt} />
            </div>
          </SwiperSlide>
        ))}
      </Swiper>
    </>
  );
}

export default NodeList;
