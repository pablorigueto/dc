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

  return (
    <>
      <Swiper
        slidesPerView={4}
        spaceBetween={30}
        centeredSlides={true}
        pagination={{
          type: 'progressbar',
        }}
        modules={[Pagination, Navigation]}
        className="homepage-carousel"
      >

        {nodes.map((node) => (
          <SwiperSlide key={node.id}>
            <div className="frontpage-result-thumbnail">
              <div className="teaser-tag-group">
                <div className="frontpage-label page environment">Environment</div>
                <div className="frontpage-label page drupal">Drupal</div>
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
