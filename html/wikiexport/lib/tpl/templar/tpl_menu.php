<li class="dropdown" id="custom">
<a class="dropdown-toggle" data-toggle="dropdown" href="#">
Custom Dropdown<b class="caret"></b></a>
<ul class="dropdown-menu">
	<li><a href="./wiki:syntax">Templar Dokuwiki Syntax</a></li>
	<li><a href="./wiki:sidebar">Sidebar Page</a></li>
</ul>
</li>
<li class="dropdown" id="dokuwiki__sitetools">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo tpl_getLang('site_tools') ?><b class="caret"></b></a>
	<ul class="dropdown-menu">
		<?php
			tpl_action('recent', 1, 'li');
			tpl_action('media', 1, 'li');
			tpl_action('index', 1, 'li');
		?>
	</ul>
</li>
