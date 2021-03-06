<?php
/**
 * AJAX call handler for Dokukiwix plugin
 * Most of this file is based on the indexer plugin by Andreas Gohr.
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Andreas Gohr <andi@splitbrain.org>
 * @author     Yann Hamon <yann@mandragor.org>
 */

//fix for Opera XMLHttpRequests
if(!count($_POST) && $HTTP_RAW_POST_DATA){
  parse_str($HTTP_RAW_POST_DATA, $_POST);
}

if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

define("dokukiwix_plugin", 1);


$archivePath = '';

require_once(DOKU_PLUGIN.'dokukiwix/common.php');
require_once(DOKU_INC.'inc/init.php');
require_once(DOKU_INC.'inc/common.php');
require_once(DOKU_INC.'inc/pageutils.php');
require_once(DOKU_INC.'inc/auth.php');
require_once(DOKU_INC.'inc/search.php');
require_once(DOKU_INC.'inc/html.php');
require_once(DOKU_INC.'inc/template.php');
require_once(DOKU_INC.'inc/actions.php');

$lock = $conf['lockdir'].'/_dokukiwix.lock';

//close sesseion
session_write_close();

header('Content-Type: text/plain; charset=utf-8');

//we only work for admins!
if (auth_quickaclcheck($conf['start']) < AUTH_ADMIN){
    die('access denied');
}

//call the requested function
$call = 'ajax_'.$_POST['call'];
if(function_exists($call)){
    $call();
}else{
    print "The called function '".htmlspecialchars($call)."' does not exist!";
}


/**
 * Searches for pages
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 * @author     Yann Hamon <yann@mandragor.org>
 */
function ajax_pagelist(){
    global $conf;
    $data = array();
    search($data,$conf['datadir'],'search_allpages',array());

    foreach($data as $val){
        print $val['id']."\n";
    }
}

/**
 * Startup routines, called after the lock is created
 * Creates the directory structure, copyes CSS files, ...
 *
 * @author     Yann Hamon <yann@mandragor.org>
 */
function ajax_dokukiwix_start(){
  global $lock;
  global $conf;

  if (file_exists($lock))
      $archivePath = DOKU_PLUGIN.'dokukiwix/archive/'.file_get_contents($lock).'/';
  else
      die('Critical error: The lock file has been removed!');

  io_mkdir_p($archivePath.'images/_extern/');
  io_mkdir_p($archivePath.'pages/');
  io_mkdir_p($archivePath.'css/');

  copy(DOKU_INC.'lib/tpl/offline/offline.css', $archivePath.'css/offline.css');

  $home_page = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html><head><meta http-equiv="refresh" content="0;url=./pages/'.$conf['start'].'.html"></head><body></body></html>';

  io_saveFile($archivePath.'index.html', $home_page,$info);
}

/**
 * Creation of the lock file, that file containing the 
 * date and time the lock was created.
 *
 * @author     Yann Hamon <yann@mandragor.org>
 */
function ajax_createLock(){
    global $lock;

    // try to aquire a lock
    if(!file_exists($lock)){
        if ($dokukiwix_fp = fopen($lock, 'w+')) {
            fwrite($dokukiwix_fp, date('Y-m-d_H:i'));
            fclose($dokukiwix_fp);
        }
    }
    else {
        print '1';
    }
}

/**
 * Delete the lock file.
 *
 * @author     Andreas Gohr  <andi@splitbrain.org>
 */
function ajax_removeLock(){
    global $lock;
    unlink($lock);

    print '1';
}


/**
 * Build and save the static HTML for the requested page
 *
 * @author Yann Hamon <yann.hamon@gmail.com>
 */
function ajax_buildOfflinePage(){
    global $conf;
    global $_POST;
    global $lock;
    global $archivePath;

    if (file_exists($lock))
        $archivePath = DOKU_PLUGIN.'dokukiwix/archive/'.file_get_contents($lock).'/';
    else
        die('Critical error: The lock file has been removed!');

    if(!$_POST['page']){
        print 1;
        exit;
    }

    // keep running
    @ignore_user_abort(true);

    global $ID, $ACT;
    $ID = $_POST['page'];
    $ACT = 'show';
    $conf['template']='offline';

    // We put the translated wiki page in the buffer $data
    ob_start();
    include(template('main.php'));
    $data = ob_get_contents();
    ob_end_clean();

    io_saveFile($archivePath.'pages/'.str_replace(':', '/', $_POST['page']).'.html', $data);

    print 1; 
}


//Setup VIM: ex: et ts=4 enc=utf-8 :
?>
