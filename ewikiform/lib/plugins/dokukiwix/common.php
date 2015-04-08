<?php
/**
 * Media Link handler
 * 
 * Copy the file into the archive directory, downloads it
 * if it is external.
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Yann Hamon <yann@mandragor.org>
 * @return     A link to the file, relative to the page asked.
 */

function dokukiwix_ml($id='',$more='',$direct=true,$sep='&'){
  global $_POST;
  global $offlineVersionPath;

  // We build a link relative to the current page
  $xlink = './';
  $page_depth = substr_count($_POST['page'], ":");
  for($i=0;$i<=$page_depth;$i++)
    $xlink .= "../";

  // External pictures: dowload in /images/_extern
  if(preg_match('#^(https?|ftp)://#i',$id)){
    io_download($id, $offlineVersionPath.'images/_extern/'.$_POST['page'].'-'.basename($id));
    $xlink .= 'images/_extern/'.$_POST['page'].'-'.basename($id);
  }
  else
  // Files starting with tag_ are only tags
  if(!preg_match('#\/?tag_#i',$id)) { 
    $id = str_replace(":", "/", $id);
    io_mkdir_p(dirname($offlineVersionPath.'images/'.$id));
    copy(DOKU_INC.'data/media/'.$id,$offlineVersionPath.'images/'.$id);
    $xlink .= 'images/'.$id;
  }

  return $xlink;
}

/**
 * Wiki Link handler
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Yann Hamon <yann@mandragor.org>
 * @return     A link to the page linked, relative to the page asked.
 */
function dokukiwix_wl($id='',$more='',$abs=false,$sep='&amp;'){
  global $_POST;

  $xlink = './';

  // If $id does not contain any :, the link is relative; in all other case, it is absolute.
  if(!(preg_match('#^\.#i',$id) || (!preg_match('#^\.#i',$id) && !preg_match('#:#i',$id))) ){ // Link is absolute
    $page_depth = substr_count($_POST['page'], ":");
    for($i=0;$i<$page_depth;$i++)
      $xlink .= "../";
  }

  $id = str_replace(":", "/", $id);
  $id = preg_replace("#^\.(.*)#i", "\\1", $id);
  $xlink .= $id.'.html';

  return $xlink;
}


