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
            <h2>Jilin University</h2>
            Global
          </div>
          <div class="group_info_section">
            <h4>Basic Info</h4>
            <dl class="clearfix">
              <dt>Type:</dt>
              <dd>
                <a href="#">Internet & Technology</a> - <a href="#">Websites</a>
              </dd>
              <dt>Description:</dt>
              <dd>
                fake the facebook...<br />
                <br />
                The Project:<br />
                <a href="http://code.google.com/p/fakebook/">http://code.google.com/p/fakebook/</a>
              </dd>
            </dl>
            <h4>Contact Info</h4>
            <dl class="clearfix">
              <dt>Email:</dt>
              <dd>name@domain.com</dd>
              <dt>Website:</dt>
              <dd>http://www.domain.com </dd>
            </dl>
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
                    <a href="profile.php?id=41">User1</a> wrote
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
              <li><a href="#">Message All Members</a></li>
              <li><a href="#">Edit Group</a></li>
              <li><a href="#">Edit Members</a></li>
              <li><a href="#">Edit Group Officers</a></li>
              <li><a href="#">Invite People to Join</a></li>
              <li><a href="#">Create Related Event</a></li>
              <li><a href="#">Leave Group</a></li>
            <ul/>
          </div>
          <div class="roundbox">
            <div class="UIProfileBox_Container">
              <div class="box_header">Group Type</div>
              <div class="box_content clearfix">
                This is an open group. Anyone can join and invite others to join.
              </div>
            </div>
            <div class="UIProfileBox_Container">
              <div class="box_header">Admins</div>
              <div class="box_content">
                <ul>
                  <li>
                    <span>
                      <a class="group_name" href="group.php?id=49911">User1</a> (creator)
                    </span>
                  </li>
                  <li>
                    <span>
                      <a class="group_name" href="group.php?id=49911">User2</a>
                    </span>
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