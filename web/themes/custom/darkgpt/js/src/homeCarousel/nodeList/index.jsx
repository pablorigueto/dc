import React, { useState } from 'react';

const NodeListContainer = ({ nodeTimeStamp }) => {

  const [itemsToShow, setItemsToShow] = useState(5);

  const handleLoadMore = () => {
    // Increase the number of items to show by 5 each time the button is clicked.
    setItemsToShow(itemsToShow + 5);
  };

  return (
    <>
    <div className="node-list-container">
      <div className="recent-section">
        <h1>Recent Section</h1>
      </div>
      {/* List node, more recent first. */}
      {/* {nodeTimeStamp.map((node) => ( */}
      {nodeTimeStamp.slice(0, itemsToShow).map((node) => (
        <a className="node-list-path" href={node.node_path} key={`mr-${node.id}`}>

          <div className="node-list-main">

            <div className="node-list-tags-main">
              {node.tags.map((tag) => (
                <label key={`tag-${tag.id}`} className={`node-list-tags ${tag.alias.toLowerCase()}`}>
                  {tag.alias}
                </label>
              ))}
            </div>

            <div className="node-list-title-main">
              <h2 className="node-list-title">
                {node.title}
              </h2>
            </div>

            <div className="node-list-details-main">
              <div className="node-list-created">
                {node.node_created}
              </div>

              <div className="node-list-views-count">
                {node.node_view_count}
                <span className="material-symbols-outlined">
                  visibility
                </span>
              </div>

              <div className="node-list-comments-count">
                {node.comments_count}
                <span className="material-symbols-outlined">
                  chat_bubble
                </span>
              </div>

              <div className="node-list-likes-count">
                {node.likes_count}
                <span className="material-symbols-outlined">
                  thumb_up
                </span>
              </div>

            </div>

          </div>

          <div className="node-list-image">
            <img className="node-list-img" src={node.img_thumbnail} alt={node.alt} />
          </div>

        </a>
      ))}

      {itemsToShow < nodeTimeStamp.length && (
        <div className="border-top-load-more">
          <button className="load-more-button" onClick={handleLoadMore}>-Load More-</button>
        </div>
      )}

    </div>
    </>
  );
};

export default NodeListContainer;
