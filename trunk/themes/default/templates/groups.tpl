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
          <div class="maincolumn">
            <div class="groups_header">
              <h1>Groups</h1>
            </div>
            <div class="leftcolumn">
              <div class="groups_subheader">
                <h3>Recently joined by your friends</h3>
              </div>
              <div class="group clearfix">
                <div class="image">
                  <a href="group.php?id=10"><img alt="" src="{$theme_path}/images/group_m.jpg"/></a>
                </div>
                <div class="info">
                  <h4>
                    <a href="group.php?id=1">Group Name 1</a>
                  </h4>
                  <small>Entertainment & Arts - Online Media</small>
                  <dl>
                    <dt>Other</dt>
                    <dd><a href="group.php?id=49911">Hello world</a></dd>
                    <dt>Size</dt>
                    <dd>40 members</dd>
                  </dl>
                </div>
              </div>
            </div>
            <div class="rightcolumn">
              <div class="groups_subheader">
                <h3>Your recently updated groups</h3>
              </div>
              <div class="group clearfix">
                <div class="image">
                  <a href="group.php?id=10"><img alt="" src="{$theme_path}/images/group_m.jpg"/></a>
                </div>
                <div class="info">
                  <h4>
                    <a href="group.php?id=1">Group Name 2</a>
                  </h4>
                  <small>Entertainment & Arts - Online Media</small>
                  <dl>
                    <dt>Other</dt>
                    <dd><a href="group.php?id=49911">Hello world</a></dd>
                    <dt>Size</dt>
                    <dd>40 members</dd>
                  </dl>
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