<?php
/**
 * Templar - Dokuwiki Template - 09/2012
 * based on Andreas's Gohr template dokuwiki/main.php
 *
 * @link     http://templar.cavalie.ro
 * @author   Tudor Vaida
 * @license  GPL 3 (http://www.gnu.org/licenses/gpl.html)
 */

if (!defined('DOKU_INC')) die(); 

$customSidebar= 'custom'==$conf['sidebar'];
$showSidebar = ($ACT=='show') &&  ($customSidebar || page_findnearest($conf['sidebar']));
?><!DOCTYPE html>
<html lang="<?php echo $conf['lang'] ?>" dir="<?php echo $lang['direction'] ?>" class="no-js">
<head>
    <meta charset=utf-8" />
    <title><?php tpl_pagetitle() ?> [<?php echo strip_tags($conf['title']) ?>]</title>
    <script>(function(H){H.className=H.className.replace(/\bno-js\b/,'js')})(document.documentElement)</script>
    <?php tpl_metaheaders() ?>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <?php echo tpl_favicon(array('favicon', 'mobile')) ?>
    <?php tpl_includeFile('meta.html') ?>
</head>

<body>
<div id='dokuwiki__top' ></div>
<!-- == NAVBAR == -->
<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="navbar-inner">
  <div class="container">
	  <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	  </button>
	<!-- == LOGO/TITLE == -->
<?php 
	$logo = @tpl_getMediaFile(array(':wiki:logo.png', 'images/logo.png'), false);
	tpl_link( wl(), 
		'<img src="'.$logo.'" alt="" class="logo"/><span>'.$conf['title'].'</span>',
		'accesskey="h" title="[H]" class="brand"'
	);
	?>
	<div class="nav-collapse collapse">
	<ul class="nav">
		<?php include('tpl_menu.php') ?>
		<?php if ($conf['useacl']): ?>
		<li class="dropdown user" id="dokuwiki__usertools">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php
			if ($_SERVER['REMOTE_USER']) {
				tpl_userinfo();
			} else {
				echo tpl_getLang('user_tools');
			}
			?><b class="caret"></b></a>
			<ul class="dropdown-menu">
				<?php /* the optional second parameter of tpl_action() switches between a link and a button,
						 e.g. a button inside a <li> would be: tpl_action('edit',0,'li') */
					tpl_action('admin', 1, 'li');
					tpl_action('profile', 1, 'li');
					tpl_action('register', 1, 'li'); /* DW versions > 2011-02-20 can use the core function tpl_action('register', 1, 'li') */
					tpl_action('login', 1, 'li');
				?>
			</ul>
		</li>
		<form action="'.wl().'" accept-charset="utf-8" class="search navbar-form pull-right" id="dw__search" method="get"><div class="no">
		<input type="hidden" name="do" value="search" />
		<?php		    print '<input type="text" ';
			if($ACT == 'search') print 'value="'.htmlspecialchars($QUERY).'" ';
			if(!$autocomplete) print 'autocomplete="off" ';
			print 'id="qsearch__in" accesskey="f" name="id" class="span2" title="[F]" /> ';
			print '<input type="submit" value="'.$lang['btn_search'].'" class="btn" title="'.$lang['btn_search'].'" />';
			if($ajax) print '<div id="qsearch__out" class="ajax_qsearch JSpopup"></div>';
		?>
		</div></form>
		<?php endif; ?>
	</ul><!-- nav -->
	</div>
  </div>
  </div>
</div>
<div class='container' id="dokuwiki__site">
<?php include('tpl_header.php') ?>
<div class="dokuwiki site mode_<?php echo $ACT; echo $showSidebar?' showSidebar ':' '; ?>">
	<div class='row'>
	<?php $tocInsidePage=true;
	if($showSidebar){ ?>
	<!-- == ASIDE == -->
		<?php tpl_flush() ?>
	<div id="dokuwiki__aside" class='span2'>
	<div class='sidebar affix-top' data-spy='affix' data-offset-top='40'>
		<?php
		$tocPlace=tpl_getConf('toc_place');
		if('sidebar-up'==$tocPlace || 'sidebar-down'==$tocPlace)
			$tocInsidePage=false;
		if($customSidebar) {
			include 'tpl_sidebar.php';
		} else { 
			if('sidebar-up'==$tocPlace) {
				tpl_toc(); ?>
				<div class='clearfix'></div>
			<?php } ?>
			<div class='content sidebar_menu'>
				<?php 
				if(tpl_getConf('sidebar_head'))
					tpl_includeFile('tpl_sidebar_head.php');
				tpl_include_page($conf['sidebar'], 1, 1);
				if(tpl_getConf('sidebar_foot'))
					tpl_includeFile('tpl_sidebar_foot.php');
				?>
			</div>
			<?php if('sidebar-down'==$tocPlace) {
				tpl_toc(); ?>
				<div class='clearfix'></div>
			<?php } ?>
			<div id="dokuwiki__pagetools">
				<ul class='nav nav-tabs nav-stacked'>
				<?php
					tpl_action('edit', 1, 'li');
					tpl_action('history', 1, 'li');
					tpl_action('backlink', 1, 'li');
					tpl_action('subscribe', 1, 'li');
					tpl_action('revert', 1, 'li');
					tpl_action('top', 1, 'li');
				?>
				</ul>
			</div>
		<?php } ?>
	</div>
	</div>
	<?php };//endif ?>
	<!-- == CONTENT == -->
	<div id="dokuwiki__content" class="<?php echo $showSidebar?'span10':'span12'; ?>">
		<?php tpl_flush() /* flush the output buffer */ ?>
		<?php tpl_includeFile('pageheader.php') ?>

		<div class="page">
			<?php tpl_content($tocInsidePage?true:false); /* the main content */ ?>
		</div><!-- page -->

		<?php tpl_flush() ?>
		<?php tpl_includeFile('pagefooter.php') ?>
	</div>
	</div><!-- row -->

	<!-- == FOOTER ==  -->
	<div class='row'>
	<div id="dokuwiki__footer" class="span10 <?php echo $showSidebar?'offset2':'';?>">
		<!-- PAGE ACTIONS -->
		<hr />
		<div id="dokuwiki__pagetools">
			<h3 class="a11y"><?php echo tpl_getLang('page_tools'); ?></h3>
			<div class='btn-group'>
				<?php
					tpl_action('edit', 1, 'span class="btn"');
					tpl_action('history', 1, 'span class="btn"');
					tpl_action('backlink', 1, 'span class="btn"');
					tpl_action('subscribe', 1, 'span class="btn"');
					tpl_action('revert', 1, 'span class="btn"');
					tpl_action('top', 1, 'span class="btn"');
				?>
			</div>
		</div>
		<div class="doc"><?php tpl_pageinfo() /* 'Last modified' etc */ ?></div>
		<?php tpl_license('button') /* content license, parameters: img=*badge|button|0, imgonly=*0|1, return=*0|1 */ ?>
		<?php tpl_includeFile('footer.html') ?>
	</div>
	</div>
</div><!-- /site --><div class="no"><?php tpl_indexerWebBug()?></div>
</div>
</body>
</html>
