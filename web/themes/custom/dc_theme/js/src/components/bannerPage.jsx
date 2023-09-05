import React from 'react';

const Banner = ({ text, color }) => {
  const bannerStyle = {
    backgroundColor: color,
    padding: '10px',
    textAlign: 'center',
    color: 'white',
    fontWeight: 'bold',
  };

  return (
    <div style={bannerStyle}>
      {text}
    </div>
  );
};

const App = () => {
  return (
    <div>
      <h1>Welcome to Our Website</h1>
      <Banner text="Special Offer: 20% off on all products!" color="yellow" />
      <p>Explore our amazing products and enjoy the discounts.</p>
    </div>
  );
};

export default App;
