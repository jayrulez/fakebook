{include file='global/pghead.tpl'}

	<head>
		<title>{$conf.sitename} | {$lang.pagetitle}</title>
{include file='global/header.tpl'}
	</head>
	<body>
		<div id="UIPage" class="{$cbid}">
{include file='global/globalheader.tpl'}

			<div id="globalbody">
				<div id="globalbody-inner" class="clearfix">
					<div id="welcome">
						<div id="welcome-inner" class="clearfix">
							<div id="sidebar" class="clearfix">
								<div id="sidebar-inner">
									<div id="quicklogin">
										<div id="quicklogin-top"></div>
										<div id="quicklogin-content">
											<div id="quicklogin-content-inner">
												<div id="quicklogin-form">
													<form id="Qlogin" method="post" action="login.php">
														<div id="quicklogin-form-inner" class="clearfix">
															<label for="loginId">{$lang.field_login_id}</label>
															<input name="loginId" id="loginId" class="inputtext" type="text"/>
															<label for="loginPwd">{$lang.field_login_pwd}</label>
															<input name="loginPwd" id="loginPwd" class="inputpass" type="password"/>
															<label for="autologin" class="autologin">
																<input name="autologin" id="autologin" class="inputcheckbox" type="checkbox"/>
																<span class="autologin-text">{$lang.field_autologin}</span>
															</label>
															<div class="submitbutton clearfix">
																<input type="submit" class="submitbutton-a" value="{$lang.quicklogin_submit}" />
																<div class="submitbutton-end">
																	<div class="submitbutton-a-end"></div>
																</div>
															</div>
														</div>
													</form>
													<a href="">{$lang.link_forgot_password}</a>
												</div>
											</div>
										</div>
										<div id="quicklogin-bottom"></div>
									</div>
									<!--!use for featured!<div class="sidebar-extra">
										<div class="sidebar-extra-top"></div>
										<div class="sidebar-extra-content">
											<div class="sidebar-extra-content-inner">

											</div>
										</div>
										<div class="sidebar-extra-bottom"></div>
									</div>-->
								</div>
							</div>
							<div id="widebar" class="clearfix">
							</div>
						</div>
					</div>
				</div>
			</div>
			
{include file='global/globalfooter.tpl'}


		</div>
	</body>
{include file='global/pgfoot.tpl'}
