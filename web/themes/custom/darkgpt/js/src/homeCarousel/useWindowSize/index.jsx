import { useState, useEffect } from 'react';

const useWindowSize = () => {
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

  return isMobile;
};

export default useWindowSize;
