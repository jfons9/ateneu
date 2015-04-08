<?php
/**
 * M1 Mobile Menu, included in the main and detail files.  Also includes all mobile menu information.
 */

// must be run from within DokuWiki
if (!defined('DOKU_INC')) die();
?>

	<div id="dokuwiki__footer">
        <div class="doc">
        <?php tpl_pageinfo() /* 'Last modified' etc */ ?>
        </div>
        <?php tpl_license($img=0) /* content license, parameters: img=*badge|button|0, imgonly=*0|1, return=*0|1 */ ?>
    </div><!-- /footer -->