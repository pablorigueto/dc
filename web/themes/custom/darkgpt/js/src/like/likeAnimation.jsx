import React, { useState } from 'react';
import '../css/homeStyles.css';
import Lottie from "lottie-react";
import like_animation from "../assets/like/likeAnimation4.json";

const App = () => {
  const [isAnimationPlaying, setAnimationPlaying] = useState(false);

  const handleClick = () => {
    setAnimationPlaying(true);
  };

  const onAnimationComplete = () => {
    setAnimationPlaying(false);
  };

  return (
    <div className='like-ico'>
      <Lottie
        className='like-animation'
        animationData={like_animation}
        loop={isAnimationPlaying}
        autoplay={isAnimationPlaying}
        onLoopComplete={onAnimationComplete}
        onClick={handleClick}
      />
    </div>
  );
};

export default App;
