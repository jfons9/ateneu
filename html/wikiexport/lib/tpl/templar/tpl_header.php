<?php
/**
 * Template header, included in the main and detail files
 */

// must be run from within DokuWiki
if (!defined('DOKU_INC')) die();
?>
<!-- == HEADER == -->
<div id="dokuwiki__header">
    <?php html_msgarea() ?>
    <?php tpl_includeFile('header.html') ?>

	<!-- BREADCRUMBS -->
	<?php if($conf['youarehere']){ ?>
		<ul class="breadcrumb"><li><?php tpl_youarehere('<span class="divider">/</span></li><li>') ?></li></ul>
	<?php } ?>
	<?php if($conf['breadcrumbs']){ ?>
		<ul class="breadcrumb"><li><?php tpl_breadcrumbs('<span class="divider">/</span></li><li>') ?></li></ul>
	<?php } ?>
</div>
