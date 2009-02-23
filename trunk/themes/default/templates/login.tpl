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
					<div id="mainbody">
						<div id="mainbody-inner" class="clearfix">
							<div id="sidebar" class="clearfix">
								<div id="sidebar-inner">
									<div class="sidebar-extra">
										<div class="sidebar-extra-top"></div>
										<div class="sidebar-extra-content">
											<div class="sidebar-extra-content-inner">

											</div>
										</div>
										<div class="sidebar-extra-bottom"></div>
									</div>
								</div>
							</div>
							<div id="widebar" class="clearfix">
								<div id="signin-page">
									<div id="signin-page-inner">
										<div id="intro-top"></div>
										<div id="intro">
											<div id="intro-inner">
												<div id="intro-title">
													<span><!--Sitename Signin--></span>
												</div>
												<div id="intro-body">
													<span><!--Signin Hint--></span>
												</div>
											</div>
										</div>
										<div id="intro-bottom"></div>
										<div id="signin-form">
											<div id="signin-form-top"></div>
											<div id="signin-form-content">
												<form id="Sform" method="post" action="login.php">
													{if isset($error) }
													<!--
													<div id="error-message">
														<span class="error">{$error}</span>
													</div>
													-->
													{/if}
													<input type="hidden" name="signin" value="1"/>
													<div class="item clearfix">
														<div class="label clearfix">
															<label for="signinId">Sign In ID:</label>
														</div>
														<div class="field clearfix">
															<input type="text" class="inputtext" name="loginId" id="signinId" value="{$loginId}"/>
														</div>
														<div class="field clearfix" id="signinId_error">
															&nbsp;
														</div>
													</div>
													<div class="item clearfix">
														<div class="label clearfix">
															<label for="password">Password:</label>
														</div>
														<div class="field clearfix">
															<input type="password" class="inputpass" name="loginPwd" id="password" value="{$loginPwd}"/>
														</div>
														<div class="error clearfix" id="password_error">
															&nbsp;
														</div>
													</div>
													<div class="item clearfix">
														<div class="label clearfix">
															<label>&nbsp;</label>
														</div>
														<div class="field clearfix">
															<label for="autosignin">
																<input type="checkbox" class="checkbox" name="autosignin" id="autosignin"/>
																<span>Remember me</span>
															</label>
														</div>
														<div class="error clearfix" id="autosignin_error">
															&nbsp;
														</div>
													</div>
													<div class="item clearfix">
														<div class="label clearfix">
															<label>&nbsp;</label>
														</div>
														<div class="field clearfix">
															<div class="submitbutton clearfix">
																<input type="submit" class="submitbutton-a" value="{$lang.login_submit}"/>
																<div class="submitbutton-end">
																	<div class="submitbutton-a-end"></div>
																</div>
															</div>
														</div>
														<div class="error clearfix">
															&nbsp;
														</div>
													</div>
													<div class="item clearfix">
														<div class="label clearfix">
															<label>&nbsp;</label>
														</div>
														<div class="field clearfix">
															<label for="autosignin">
																<a href="">Forgot Password?</a>
															</label>
														</div>
														<div class="error clearfix">
															&nbsp;
														</div>
													</div>
												</form>
											</div>
											<div id="signin-form-bottom"></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
{include file='global/globalfooter.tpl'}


		</div>
	</body>
{include file='global/pgfoot.tpl'}
