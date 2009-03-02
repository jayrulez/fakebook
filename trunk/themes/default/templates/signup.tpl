{include file='global/pghead.tpl'}
<head>
<title>{$conf.sitename} | {$lang.pagetitle}</title>
{include file='global/header.tpl'}
</head>
<body class="WelcomePage {$cbid} {$conf.lang_id}">
  <div id="non_footer" >
    <div id="page_height">
      <div id="menubar_container">
        <div id="menubar" class="menubar_logged_out clearfix">
          <div id="logobar">
            <a class="home" title="Go to Fakebook Home" href="{$home}"></a>
          </div>
        </div>
      </div>
    </div>
  </div>
{include file='global/footer.tpl'}
</body>
{include file='global/pgfoot.tpl'}