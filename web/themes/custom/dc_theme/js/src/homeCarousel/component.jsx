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
            <h3>
              <strong>ID:</strong> {node.id},
              <strong>Title:</strong> {node.title},
            </h3>
            <img src={node.url} alt={node.alt} />
          </SwiperSlide>
        ))}
      </Swiper>
    </>
  );
}

export default NodeList;
