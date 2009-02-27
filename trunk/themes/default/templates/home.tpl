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
      <div id="content" class="clearfix">
      <div class="UIFullPage_Container">
        <div class="widecolumn">
          <div class="picture clearfix"><a href="profile.php"><img src="{$theme_path}/images/silhouette_s.jpg" alt="" /></a></div>
          <div class="new_user_todo_holder clearfix">
            <div class="new_user_todo_icon">
              <a href="findfriends.php">
                <img alt="" src="images/search_profile.gif"/>
              </a>
            </div>
            <div class="new_user_todo">
              <h3>
                <a href="findfriends.php">
                  Find people you know <img class="spritemap_icons sx_see_more" alt="" src="{$theme_path}/images/spacer.gif"/>
                </a>
              </h3>
              You can search by name or look for classmates or coworkers.
            </div>
          </div>
          <div class="tab"> </div>
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
                  <a href="photo.php?id=46"><img alt="" src="album/49911/p49911_123192915.thumbnail.jpg"/></a>
                </div>
                <div class="story_quote">
                  <span class="em">Comments...</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="narrowcolumn">
          <div class="UIRoundedBox">
            <div class="T"> </div>
            <div class="C">
              <div class="sidebar_item clearfix">
                <div class="sidebar_item_header">Applications</div>
                <div class="sidebar_item_body">
                  <ul>
                    <li>
                      <a class="app_group" href="groups.php">Groups</a>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="sidebar_item first clearfix">
                <div class="sidebar_item_header">Find Your Friends</div>
                <div class="sidebar_item_body">
                  <span class="friend_finder">
                    To find people you know who are already using Facebook, check out the <a href="findfriends.php">Friend Finder</a>.
                  </span>
                </div>
              </div>
            </div>
            <div class="B"> </div>
          </div>
        </div>
      </div>
      </div>
    </div>
  </div>
{include file='global/footer.tpl'}
</body>
{include file='global/pgfoot.tpl'}