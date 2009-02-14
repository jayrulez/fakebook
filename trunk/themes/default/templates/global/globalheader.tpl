			<div id="globalheader">
				<div id="globalheader-inner" class="clearfix">
					<div id="logobar" class="clearfix">
						<div id="logobar-inner">
							<a class="logo" href="home.php">&nbsp;</a>
						</div>
					</div>
					<div id="menubar" class="clearfix">
						<div id="menubar-inner">
							<div id="menus" class="clearfix">
							
								{if $islogged}
								
								
								<div id="main-menu" class="clearfix">
									<ul id="main-menu-list">
										<li id="home"><a href="home.php">{$lang.link_home}</a></li>
										<li id="profile"><a href="profile.php">{$lang.link_profile}</a></li>
										<li id="friends"><a href="friends.php">{$lang.link_friends}</a></li>
										<li id="inbox"><a href="inbox.php">{$lang.link_inbox}</a></li>
									</ul>
								</div>
								<div id="account-menu" class="clearfix">
									<ul id="account-menu-list">
										<li id="user"><a href="profile.php">my name</a></li>
										<li id="account"><a href="account.php">{$lang.link_settings}</a></li>
										<li id="signout"><a href="signout.php">{$lang.link_signout}</a></li>
									</ul>
								</div>
								
								
								{else}
								
								
								<div id="main-menu" class="clearfix">&nbsp;</div>
								<div id="account-menu" class="clearfix">
									<ul id="account-menu-list">
										<li id="signin"><a href="signin.php">{$lang.link_signin}</a></li>
										<li id="signup"><a href="signup.php">{$lang.link_signup}</a></li>
									</ul>
								</div>
								
								
								{/if}
								
								
							</div>
						</div>
					</div>
				</div>
			</div>