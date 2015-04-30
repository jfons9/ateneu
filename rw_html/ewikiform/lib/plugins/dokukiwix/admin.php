<?php
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'admin.php');
 
/**
 * All DokuWiki plugins to extend the admin function
 * need to inherit from this class
 */
class admin_plugin_dokukiwix extends DokuWiki_Admin_Plugin {
 		var $cmd;

    /**
     * Constructor
     */
    function admin_plugin_dokukiwix(){
        $this->setupLocale();
    }

    /**
     * return some info
     */
    function getInfo(){
        return array(
            'author' => 'Yann Hamon',
            'email'  => 'yann.hamon@gmail.com',
            'date'   => '2007-06-30',
            'name'   => 'Dokukiwix',
            'desc'   => 'Allows to create an offline version of your Wiki',
            'url'    => 'http://wiki.splitbrain.org/plugin:dokukiwix',
        );
    }
 
    /**
     * return sort order for position in admin menu
     */
    function getMenuSort() {
        return 40;
    }
 
    /**
     * handle user request
     */
    function handle() {
    }
 
    /**
     * output appropriate html
     */
    function html() {
        print $this->plugin_locale_xhtml('intro');

        print '<fieldset id="pl_dokukiwix_form">';
        print '<span id="pl_dokukiwix_out"></span>';
        print '<img src="'.DOKU_BASE.'lib/images/loading.gif" id="pl_dokukiwix_throbber" />';
        print '<div id="pl_dokukiwix_actions">';
        print '<a href="#" onclick="plugin_dokukiwix_toggle_startpause(); return false;" title="Start"><img src="'.DOKU_BASE.'lib/plugins/dokukiwix/images/play.png" alt="Start" id="pl_dokukiwix_toggle_startpause" /></a> ';
        //print '<a href="#" onclick="plugin_dokukiwix_cb_skip()" title="Skip"><img src="'.DOKU_BASE.'lib/plugins/dokukiwix/images/skip.png" alt="Skip" /></a>';
        print '<a href="#" onclick="plugin_dokukiwix_stop()" title="Stop" id="pl_dokukiwix_stop" style="visibility:hidden;"><img src="'.DOKU_BASE.'lib/plugins/dokukiwix/images/stop.png" alt="Stop" /></a>';
        print '</div>';
        print '<div><textarea readonly="1" id="pl_dokukiwix_log"></textarea></div>';
        print '</fieldset>';


        print '<p>Dokukiwix is subject to the <a href="http://www.gnu.org/copyleft/gpl.html">GPL v2</a> license.</p>'; 
    }

 
}
//Setup VIM: ex: et ts=4 enc=utf-8 :
