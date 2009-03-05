{include file='global/pghead.tpl'}
<head>
<title>{$conf.sitename} | {$lang.pagetitle}</title>
{include file='global/header.tpl'}
</head>
<body class="{$cbid} {$conf.lang_id}">
  <div id="non_footer" >
    <div id="page_height">
      <div id="menubar_container">
{include file='global/menubar.tpl'}
      </div>
      <div class="top_wash"> </div>
      <div id="content" class="clearfix">
      <div class="UIFullPage_Container">
        <div class="narrowcolumn">
          <div class="picture">
            <img alt="" src="{$theme_path}/images/silhouette_l.jpg"/>
          </div>
          <div id="profile_actions">
            <ul>
              <li><a href="#">View Photos of Tom (2)</a></li>
              <li><a href="#">Suggest Friends for Tom</a></li>
              <li><a href="#">Send Tom a Message</a></li>
              <li><a href="#">Poke Tom</a></li>
            <ul/>
          </div>
          <div class="roundbox">
            <div id="user_friends" class="user_friends">
              <div class="box_header">Friends</div>
              <div class="box_subheader">
                <a href="friends.php?id=10">1 friends</a>
              </div>
              <div class="box_content clearfix">
                <ul>
                  <li>
                    <a href="profile.php?id=40">
                      <span class="user_picture"><img alt="" src="{$theme_path}/images/silhouette_s.jpg"/></span>
                    </a>
                    <a href="profile.php?id=40">
                      <span class="user_name">test</span>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
            <div id="user_groups" class="user_groups">
              <div class="box_header">Groups</div>
              <div class="box_content">
                <ul>
                  <li>
                    <a class="group_name" href="group.php?id=49911">Jilin University</a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="widecolumn">
          <div class="profile_name_and_status">
            <h2 id="profile_name">Yang Guang</h2>
            <div class="mobile_status">
              <span id="profile_status">
                <span id="status_text"> is nothing</span>
                <small>
                  <span id="status_time">
                    <span id="status_time_inner">a moment ago</span>
                    <a class="status_edit">clear</a>
                  </span>
                </small>
              </span>
            </div>
          </div>
          <div class="tab_menu">
            <ul>
              <li>Info</li>
            </ul>
          </div>
          <div class="basic_info clearfix">
            <span class="info_header">Basic</span>
            <dt>Nickname</dt>
            <dd>dingo </dd>
            <dt>Sex</dt>
            <dd>Male </dd>
            <dt>Birthday</dt>
            <dd>1980-07-31 </dd>
          </div>
          <div id="mini_feed">
            <div class="box_header">Mini-feed</div>
            <div class="box_subheader">Displaying User1's latest news</div>
            <div class="box_content">
            
            
              <!-- feed comment -->
              <div class="feed clearfix">
                <div class="feed_icon">
                  <img class="spritemap_icons sx_comment" src="{$theme_path}/images/spacer.gif"/>
                </div>
                <div class="feed_story">
                  <div class="story_header">
                    <a href="profile.php?id=1">User1</a> commented on <a href="profile.php?id=2">User2</a>'s photo.
                    <span class="story_time">February 2</span>
                  </div>
                  <div class="story_body">
                    <div class="photo_border">
                      <a href="photo.php?id=46"><img alt="" src="{$theme_path}/images/group_m.jpg"/></a>
                    </div>
                    <div class="story_quote">
                      <span class="em">Comments...</span>
                    </div>
                  </div>
                </div>
              </div>
          
          
              <!-- feed wall -->
              <div class="feed clearfix">
                <div class="feed_icon">
                  <img class="spritemap_icons sx_wall" src="{$theme_path}/images/spacer.gif"/>
                </div>
                <div class="feed_story">
                  <div class="story_header">
                    <a href="group.php?id=1">User2</a> wrote on <a href="group.php?id=2">User2</a>'s wall.
                    <span class="story_time">2 minutes ago</span>
                  </div>
                </div>
              </div>
              
              
              <!-- feed friends -->
              <div class="feed clearfix">
                <div class="feed_icon">
                  <img class="spritemap_icons sx_friend" src="{$theme_path}/images/spacer.gif"/>
                </div>
                <div class="feed_story">
                  <div class="story_header">
                    <a href="profile.php?id=1">User1</a> and <a href="profile.php?id=2">User2</a> are now friends.
                    <span class="story_time">February 2</span>
                  </div>
                </div>
              </div>
          
          
              <!-- feed group -->
              <div class="feed clearfix">
                <div class="feed_icon">
                  <img class="spritemap_icons sx_group" src="{$theme_path}/images/spacer.gif"/>
                </div>
                <div class="feed_story">
                  <div class="story_header">
                    <a href="profile.php?id=1">User1</a> joined the group <a href="group.php?id=2">Group1</a>.
                    <span class="story_time">February 2</span>
                  </div>
                </div>
              </div>
          
          
              <!-- feed photo -->
              <div class="feed clearfix">
                <div class="feed_icon">
                  <img class="spritemap_icons sx_photo" src="{$theme_path}/images/spacer.gif"/>
                </div>
                <div class="feed_story">
                  <div class="story_header">
                    <a href="profile.php?id=1">User1</a> added a new photo.
                    <span class="story_time">February 21</span>
                  </div>
                  <div class="story_body">
                    <div class="photo_border">
                      <a href="photo.php?id=46"><img alt="" src="{$theme_path}/images/group_l.jpg"/></a>
                    </div>
                  </div>
                </div>
              </div>
          
          
              <!-- feed translations -->
              <div class="feed clearfix">
                <div class="feed_icon">
                  <img class="spritemap_icons sx_translations" src="{$theme_path}/images/spacer.gif"/>
                </div>
                <div class="feed_story">
                  <div class="story_header">
                    <a href="profile.php?id=1">User1</a> is now using Fakebook in <a href="editprofile.php">English (US)</a>.
                    <span class="story_time">February 2</span>
                  </div>
                </div>
              </div>
              
              
            </div>
          </div>
          <div id="wall">
            <div class="box_header">Wall</div>
            <div class="box_subheader clearfix">
              <span class="subtitle">Displaying all 1 wall posts</span>
              <span class="actionlink">
                <a href="wall.php?id=10">View All</a>
              </span>
            </div>
            <div id="inputarea">
              <form action="wallpost.php" method="post" name="form">
                <textarea name="content"></textarea>
                <br/>
                <input type="submit" value="Post"/>
                <input type="hidden" value="profile.php?id=" name="url"/>
                <input type="hidden" value="10" name="wid"/>
              </form>
            </div>
            <div id="posts">
              <div class="post_item clearfix">
                <div class="picture">
                  <a href="profile.php?id=41">
                    <img alt="" src="{$theme_path}/images/silhouette_s.jpg"/>
                  </a>
                </div>
                <div class="item">
                  <div class="user">
                    <a href="profile.php?id=41">Robert Campbell</a> wrote
                    <br/>
                    <span class="DateAndTime">at February 2</span>
                  </div>
                  <div class="content">testing</div>
                  <div class="wallactions">
                    <a onclick="" href="#">Delete</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="adcolumn">
          //ad
        </div>
      </div>
      </div>
    </div>
  </div>
{include file='global/footer.tpl'}
</body>
{include file='global/pgfoot.tpl'}