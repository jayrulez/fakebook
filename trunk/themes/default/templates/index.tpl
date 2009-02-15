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
											</div>
										</div>
										<div id="quicklogin-bottom"></div>
									</div>
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
							</div>
						</div>
					</div>
				</div>
			</div>
			
{include file='global/globalfooter.tpl'}


		</div>
	</body>
{include file='global/pgfoot.tpl'}
