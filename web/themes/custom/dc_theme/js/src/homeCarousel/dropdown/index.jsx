// Dropdown.js
import React from 'react';

const Dropdown = ({ displayLabel, isMenuOpen, toggleMenu, handleSortChange }) => {
  return (
    <div className={`custom-dropdown ${isMenuOpen ? 'open' : ''}`}>
      <span className="selected-option" onClick={toggleMenu}>
        {displayLabel}
      </span>
      <ul className="options-list">
        <li onClick={() => handleSortChange('mostViewed')} className="option">
          Most Viewed
        </li>
        <li onClick={() => handleSortChange('lessViewed')} className="option">
          Less Viewed
        </li>
        <li onClick={() => handleSortChange('mostLiked')} className="option">
          Most Liked
        </li>
        <li onClick={() => handleSortChange('lessLiked')} className="option">
          Less Liked
        </li>
        <li onClick={() => handleSortChange('mostCommented')} className="option">
          Most Commented
        </li>
        <li onClick={() => handleSortChange('lessCommented')} className="option">
          Less Commented
        </li>
      </ul>
    </div>
  );
};

export default Dropdown;
