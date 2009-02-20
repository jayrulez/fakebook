{if $islogged}
      <div id="menubar" class="menubar_logged_in clearfix">
        <div id="logobar">
          <a class="home" href="home.php"></a>
        </div>
         <div id="main_menu">
          <ul id="main_menu_list">
            <li class="main_menu" id="home"><a href="home.php">{$lang.link_home}</a></li>
            <li class="main_menu" id="profile"><a href="profile.php">{$lang.link_profile}</a></li>
            <li class="main_menu" id="friends"><a href="friends.php">{$lang.link_friends}</a></li>
            <li class="main_menu" id="inbox"><a href="inbox.php">{$lang.link_inbox}</a></li>
          </ul>
        </div>
        <div id="account_menu">
          <ul id="account_menu_list">
            <li class="account_menu" id="logout"><a href="logout.php">{$lang.link_logout}</a></li>
            <li class="account_menu" id="account"><a href="account.php">{$lang.link_settings}</a></li>
            <li class="account_menu" id="user"><a href="profile.php">my name</a></li>
          </ul>
        </div>
      </div>
{else}
      <div id="menubar" class="menubar_logged_out clearfix">
        <div id="logobar">
          <a class="home" href="home.php"></a>
        </div>
        <div id="menu_login_container" class="clearfix">
          <form id="menubar_login" name="menubar_login" action="#" method="post">
            <table cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td class="login_form_label_field">
                    <label>
                      <input type="checkbox" value="1" name="persistent"/>
                      Remember Me
                    </label>
                  </td>
                  <td class="login_form_label_field">
                    <a rel="nofollow" href="#">Forgot your password?</a>
                  </td>
                  <td />
                </tr>
                <tr>
                  <td>
                    <input id="email" class="inputtext" type="text" value="abc@123.com" name="email"/>
                  </td>
                  <td>
                    <input id="pass" class="inputpassword" type="password" value="" name="pass"/>
                  </td>
                  <td>
                    <div class="inner">
                      <div class="UILinkButton">
                        <input class="UILinkButton_A" type="submit" value="Login"/>
                        <div class="UILinkButton_RW">
                          <div class="UILinkButton_R"/>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </form>
        </div>
      </div>
{/if}
