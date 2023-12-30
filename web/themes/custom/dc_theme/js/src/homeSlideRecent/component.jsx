import React from 'react';
import { useQuery } from 'react-query';
import { fetchNodes } from './controller';
import './assets/index.css';

import { Swiper, SwiperSlide } from 'swiper/react';

// Import Swiper styles
import 'swiper/css';
import 'swiper/css/effect-fade';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

// import required modules
import { Autoplay, EffectFade, Navigation, Pagination } from 'swiper/modules';

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
        effect={'fade'}
        spaceBetween={30}
        navigation={false}
        // pagination={{
        //   clickable: true,
        // }}
        autoplay={{
          delay: 100000,
          disableOnInteraction: false,
        }}
        modules={[Autoplay, EffectFade, Navigation, Pagination]}
        className="homepage-slide-recent"
      >

        {nodes.map((node) => (
          <SwiperSlide key={`hs-${node.id}`}>

          <div id="homepage-highlight" className="homepage-highlight" data-drupal-selector="homepage-highlight">

            <div className="text-highlight-container" data-drupal-selector="text-highlight-container">
              <div className="text-highlight">
                <a href={node.node_path} className="highlight-link">
                <p className="most-recent-node">{node.button_new_content}</p>
                  <div className="title-highlight">
                    <h2>{node.title}</h2><p>{node.sub_title}</p>
                  </div>
                </a>
                <p className="node_created_recent">{node.node_created}</p>
              </div>
            </div>

            <div className="img-highlight-main" data-drupal-selector="img-highlight">
              <a href={node.node_path} className="highlight-link">
                <img className="img-highlight" src={node.large_url} alt={node.alt}/>
              </a>
            </div>

          </div>

          </SwiperSlide>
        ))}
      </Swiper>
    </>
  );
}

export default NodeList;
