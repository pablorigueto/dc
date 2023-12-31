import React from 'react';
import { Swiper, SwiperSlide } from 'swiper/react';
import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/navigation';

const SwiperComponent = ({ sortedNodes, swiperConfig }) => {
  return (
    <Swiper {...swiperConfig}>
      {sortedNodes.map((node) => (
        <SwiperSlide key={node.id}>

          <a className="node-path" href={node.node_path}>

            <div className="frontpage-result-thumbnail">

              <div className="teaser-tag-group">
                {node.tags.map((tag) => (
                  <div key={tag.id} className={`frontpage-label ${tag.alias.toLowerCase()}`}>
                    {tag.alias}
                  </div>
                ))}
              </div>

              <div className='frontpage-title-display'>
                <div className="frontpage-title">{node.title}</div>
                  <div className="count-frontpage">

                    <div className="views_node_count">
                      {node.node_view_count}
                      <span className="material-symbols-outlined">
                        visibility
                      </span>
                    </div>

                    <div className="comments_count">
                      {node.comments_count}
                      <span className="material-symbols-outlined">
                        chat_bubble
                      </span>
                    </div>

                    <div className="likes_count">
                      {node.likes_count}
                      <span className="material-symbols-outlined">
                        thumb_up
                      </span>
                    </div>
                  </div>

                  <div className="profile-frontpage">

                    <div className="profile-img-name">
                      <img className="profile-frontpage-img" src={node.node_owner_image_profile} alt="User Profile Image" />
                      <div className="owner-frontpage">
                        {node.node_owner_name}
                      </div>
                    </div>

                    {node.node_created}
                  </div>

                </div>
                {/* <img src={node.url} alt={node.alt} /> */}
                <img src={node.medium_url} alt={node.alt} />
            </div>
          </a>
        </SwiperSlide>
      ))}
    </Swiper>
  );
};

export default SwiperComponent;
