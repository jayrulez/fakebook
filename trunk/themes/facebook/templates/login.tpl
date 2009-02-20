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
        <div class="signup_box clearfix">
          <div class="UILinkButton UILinkButton_SU">
            <input class="UILinkButton_A" type="submit" value="Sign Up" />
            <div class="UILinkButton_RW">
              <div class="UILinkButton_R"> </div>
            </div>
           <span class="signup_box_message">Fakebook helps you connect and share with the people in your life.</span>
          </div>
        </div>
      </div>
      <div id="content">
        <div class="error">{$error}</div>
      </div>
    </div>
  </div>
{include file='global/footer.tpl'}
</body>
{include file='global/pgfoot.tpl'}