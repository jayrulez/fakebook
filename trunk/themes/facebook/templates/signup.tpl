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
								
{include file='global/quicklogin.tpl'}

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
