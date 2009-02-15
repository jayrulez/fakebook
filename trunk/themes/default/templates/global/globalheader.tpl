			<div id="globalheader">
				<div id="globalheader-content" class="clearfix">
					<div id="logobar">
						<div id="logobar-inner">
							<a id="home" href="home.php"></a>
						</div>
					</div>
					<div id="menubar" class="clearfix">
						<div id="menubar-inner" class="clearfix">
						
							{if $islogged}
							
							<div id="main-menu" class="clearfix">
								<ul id="main-menu-list">
									<li class="main-menu" id="home"><a href="home.php">{$lang.link_home}</a></li>
									<li class="main-menu" id="profile"><a href="profile.php">{$lang.link_profile}</a></li>
									<li class="main-menu" id="friends"><a href="friends.php">{$lang.link_friends}</a></li>
									<li class="main-menu" id="inbox"><a href="inbox.php">{$lang.link_inbox}</a></li>
								</ul>
							</div>
							<div id="account-menu" class="clearfix">
								<ul id="account-menu-list">
									<li class="account-menu" id="user"><a href="profile.php">my name</a></li>
									<li class="account-menu" id="account"><a href="account.php">{$lang.link_settings}</a></li>
									<li class="account-menu" id="signout"><a href="signout.php">{$lang.link_signout}</a></li>
								</ul>
							</div>
							
							{else}

							<div id="main-menu" class="clearfix">
								<!--<ul id="main-menu-list"></ul>-->
							</div>
							<div id="account-menu" class="clearfix">
								<ul id="account-menu-list">
									<li class="account-menu" id="signin"><a href="signin.php">{$lang.link_signin}</a></li>
									<li class="account-menu" id="signup"><a href="signup.php">{$lang.link_signup}</a></li>
								</ul>
							</div>
							
							{/if}
							
						</div>
					</div>
				</div>
			</div>
			<div id="globalheader-bottom"></div>