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
          //wide
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