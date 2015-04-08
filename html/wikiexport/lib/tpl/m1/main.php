<?php
/**
 * DokuWiki Starter Template
 *
 * @link     http://dokuwiki.org/template:starter
 * @author   Anika Henke <anika@selfthinker.org>
 * @license  GPL 2 (http://www.gnu.org/licenses/gpl.html)
 */

if (!defined('DOKU_INC')) die(); /* must be run from within DokuWiki */
@require_once(dirname(__FILE__).'/tpl_functions.php'); /* include hook for template functions */
header('X-UA-Compatible: IE=edge,chrome=1');

$showTools = !tpl_getConf('hideTools') || ( tpl_getConf('hideTools') && !empty($_SERVER['REMOTE_USER']) );
$showSidebar = page_findnearest($conf['sidebar']) && ($ACT=='show');
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $conf['lang'] ?>"
  lang="<?php echo $conf['lang'] ?>" dir="<?php echo $lang['direction'] ?>" class="no-js">
<head>
    <meta charset="UTF-8" />
    <title><?php tpl_pagetitle() ?> [<?php echo strip_tags($conf['title']) ?>]</title>
    <script>(function(H){H.className=H.className.replace(/\bno-js\b/,'js')})(document.documentElement)</script>
    <?php tpl_metaheaders() ?>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <?php echo tpl_favicon(array('favicon', 'mobile')) ?>
    <?php tpl_includeFile('meta.html') ?>
    <!-- ********** GOOGLE ANALYTICS ********** -->
	<script type="text/javascript">
  	var _gaq = _gaq || [];
  	_gaq.push(['_setAccount', 'UA-16741284-1']);
  	_gaq.push(['_setDomainName', 'paddlingabc.com']);
  	_gaq.push(['_trackPageview']);
  	(function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  	})();
	</script>
	<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
</head>
<body>
    
    <div id="dokuwiki__site">
    <div id="dokuwiki__top" class="site <?php echo tpl_classes(); ?> <?php echo ($showSidebar) ? 'hasSidebar' : ''; ?>">
    <?php html_msgarea() /* occasional error and info messages on top of the page */ ?>
   
    <!-- ********** HEADER AND FULL MENU ********** -->
    <?php include('m1-header.php') ?>
       
	<!-- ********** MOBILE MENUS ********** -->  
	<?php include('m1-mobilemenu.php') ?>
	
    
    <!-- ********** CONTENT ********** -->
    <div id="m1-pagewrapper"> <!-- Used to control site width on desktop browsers -->  
         <div id="dokuwiki__content">
            <?php tpl_flush() /* flush the output buffer */ ?>
            <?php tpl_includeFile('pageheader.html') ?>
                <div class="page">
                    <!-- wikipage start -->
                    <?php tpl_content() /* the main content */ ?>
                    <!-- wikipage stop -->
                    <div class="clearer"></div>
                </div>
            <?php tpl_flush() ?>
            <?php tpl_includeFile('pagefooter.html') ?>
    </div><!-- /content -->
            
    
    <!-- ********** SIDEBAR ********** -->
    <div id="dokuwiki__aside">
        <?php tpl_includeFile('sidebarheader.html') ?>
        <?php tpl_include_page(tpl_getConf('sidebar1'), 1, 1) /* includes the nearest sidebar page */ ?>
        <?php tpl_includeFile('m1-sidebar-one.php') ?>
        <?php tpl_includeFile('sidebarfooter.html') ?>
        <div class="clearer"></div>
    </div><!-- /aside -->
        
    </div> <!-- /m1-pagewrapper -->
        
    <!-- ********** FOOTER ********** -->
        <?php include('m1-footer.php') ?>

    </div><!-- /dokuwiki__top -->
    </div><!-- /dokuwiki__site -->

    <div class="no"><?php tpl_indexerWebBug()?></div>
</body>
</html>
