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
          <div class="group_title">
            <img alt="" src="images/group.jpg"/>
            <h2>Jilin University</h2>
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
          <div id="group_members" class="group_members">
            <div class="box_header">Members</div>
            <div class="box_subheader clearfix">
              <span class="subtitle">Displaying 1 of <a href="#members.php?id=49911">40 members</a></span>
              <span class="actionlink">
                <a href="#members.php?id=49911">View All</a>
              </span>
            </div>
            <div class="box_content clearfix">
              <ul>
                <li>
                  <a onclick="" href="#"><img alt="" src="{$theme_path}/images/silhouette_s.jpg"/></a><br/>
                  <a onclick="" href="#"><span class="user_name">Tom</span></a>
                </li>
              </ul>
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
                    <a href="profile.php?id=41">User1</a> worte
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
          <div id="photos">
            <div class="box_header">Photos</div>
            <div class="box_subheader clearfix">
              <span class="subtitle">Displaying 5 of <a href="album.php?id=49911">85 photos</a></span>
              <span class="actionlink">
                <a href="album.php?id=49911">View All</a>
              </span>
            </div>
            <div id="photos">
              <div class="item">
                <a href="photo.php?id=45"><img alt="" src="{$theme_path}/images/group_s.jpg"/></a>
              </div>
            </div>
          </div>
        </div>
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