<?php
/**
 * DokuWiki Default Template
 *
 * This is the template you need to change for the overall look
 * of DokuWiki.
 *
 * You should leave the doctype at the very top - It should
 * always be the very first line of a document.
 *
 * @link   http://wiki.splitbrain.org/wiki:tpl:templates
 * @author Andreas Gohr <andi@splitbrain.org>
 */

// must be run from within DokuWiki
if (!defined('DOKU_INC')) die();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $conf['lang']?>"
 lang="<?php echo $conf['lang']?>" dir="<?php echo $lang['direction']?>">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>
    <?php tpl_pagetitle(); ?>
  </title>

  <?php
  if (defined("dokukiwix_plugin")) {
    // We build a link that is relative to the current page
    $xlink = './';
    $page_depth = substr_count($_POST[page], ":");
    for($i=0;$i<=$page_depth;$i++)
      $xlink .= "../";

    echo '<link rel="stylesheet" type="text/css" href="'.$xlink.'css/offline.css" />';
  }
  ?>

</head>
<body>
  <?php tpl_content(); ?>
</body>
</html>
