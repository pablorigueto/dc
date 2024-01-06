import React, { useState } from 'react';
import '../css/homeStyles.css';
import Lottie from "lottie-react";
import send_animation from "../assets/send/sendAnimation.json";

const App = () => {
  const [isAnimationPlaying, setAnimationPlaying] = useState(false);

  const handleClick = () => {
    setAnimationPlaying(true);
  };

  const onAnimationComplete = () => {
    setAnimationPlaying(false);
  };

  return (
    <div className='send-ico'>
      <Lottie
        className='send-animation'
        animationData={send_animation}
        loop={isAnimationPlaying}
        autoplay={isAnimationPlaying}
        onLoopComplete={onAnimationComplete}
        onClick={handleClick}
      />
    </div>
  );
};

export default App;
