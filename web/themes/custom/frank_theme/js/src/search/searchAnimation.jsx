import React from 'react';
import '../css/homeStyles.css';
import Lottie from "lottie-react";
import animation_lm8lf2yt from "../assets/find/animation_lm8lf2yt.json";

const App = () => {
  return (
    <div className='search-ico'>
      <Lottie
        className='lottiefile'
        animationData={animation_lm8lf2yt}
        loop={true}
      />
    </div>
  );
};

export default App;
