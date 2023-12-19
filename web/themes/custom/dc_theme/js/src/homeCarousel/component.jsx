import React from 'react';
import { useQuery } from 'react-query';
import { fetchNodes } from './controller';
import Slider from 'react-slick';
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css';
import './assets/index.css';

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

  // Configure settings for the carousel
  const settings = {
    dots: true,
    infinite: true,
    speed: 500,
    slidesToShow: 4,
    slidesToScroll: 4,
    initialSlide: 0,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 3,
          infinite: true,
          dots: true
        }
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2,
          initialSlide: 2
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
    ]
  };

  return (
    <div class="home-carousel">
      <h2>Node List</h2>
      <Slider {...settings}>
        {nodes.map((node) => (
          <div key={node.id}>
            <h3>
              <strong>ID:</strong> {node.id},
              <strong>Title:</strong> {node.title},
            </h3>
            <img src={node.url} alt={node.alt} />
          </div>
        ))}
      </Slider>
    </div>
  );
};

export default NodeList;
