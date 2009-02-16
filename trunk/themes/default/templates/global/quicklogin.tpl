									<div id="quicklogin">
										<div id="quicklogin-top"></div>
										<div id="quicklogin-content">
											<div id="quicklogin-content-inner">
												<div id="quicklogin-form">
													<form id="Qlogin" method="post" action="signin.php">
														<div id="quicklogin-form-inner" class="clearfix">
															<input type="hidden" name="submit" value="1"/>
															<label for="loginId">{$lang.field_login_id}</label>
															<input name="loginId" id="loginId" class="inputtext" type="text" value={$loginId_val}/>
															<label for="loginPwd">{$lang.field_login_pwd}</label>
															<input name="loginPwd" id="loginPwd" class="inputpass" type="password" value={$loginPwd_val}/>
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