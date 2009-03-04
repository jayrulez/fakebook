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
          <div class="friends_header">
            <h2>Showing <a href="profile.php?id=5">User1</a>'s friends.</h2>
          </div>
          <div class="friends_subheader">
            <a href="profile.php?id=5">User1</a> has 2 friends.
          </div>
          <div class="friend_item clearfix">
            <div class="image">
              <a href="profile.php?id=10"><img alt="" src="{$theme_path}/images/silhouette_s.jpg"/></a>
            </div>
            <div class="text">
              <a href="profile.php?id=10">Friend1</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{include file='global/footer.tpl'}
</body>
{include file='global/pgfoot.tpl'}