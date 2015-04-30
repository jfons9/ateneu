<?php
/**
 * Sidebar Action Plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Markus Birth <markus@birth-online.de>
 */

if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');

class action_plugin_sidebar extends DokuWiki_Action_Plugin {
    protected static $done = false;

    /**
     * return some info
     */
    function getInfo(){
        return confToHash(dirname(__FILE__).'/INFO.txt');
    }

    /*
     * plugin should use this method to register its handlers with the dokuwiki's event controller
     */
    function register(&$controller) {
        $controller->register_hook('TPL_ACT_RENDER', 'AFTER', $this, '_output');
    }

    function _debug(&$event, $param) {
        ptln($param);
        ptln('<!--');
        print_r($event);
        ptln('-->');
    }

    function _output(&$event, $param) {
        if (!$this->getConf('enable') || self::$done) return;
        self::$done = true;
        $bodyClass = 'sidebar sidebar_' . $this->getConf('layout') . '_' . $this->getConf('orientation');
        ptln('</div>', 2);   // close the main content area
        ptln('<script type="text/javascript">', 2);
        ptln('var body = document.getElementsByTagName(\'BODY\')[0].className = \'' . $bodyClass . '\';', 4);
        ptln('</script>', 2);
        ptln('<div id="sidebar">', 2);
        ptln('<div id="sidebartop">', 4); $this->tpl_sidebar_editbtn(); ptln('</div>', 4);
        ptln('<div id="sidebar_content">', 4); $this->tpl_sidebar_content(); ptln('</div>', 4);
        // the </div> for closing the "sidebar"-div will be provided by DokuWiki main template
    }

    // recursive function to establish best sidebar file to be used
    function getSidebarFN($ns, $file) {
        // check for wiki page = $ns:$file (or $file where no namespace)
        $nsFile = ($ns) ? "$ns:$file" : $file;
        if (file_exists(wikiFN($nsFile)) && auth_quickaclcheck($nsFile)) return $nsFile;

        // no namespace left, exit with no file found
        if (!$ns) return '';

        // remove deepest namespace level and call function recursively
        $i = strrpos($ns, ":");
        $ns = ($i) ? substr($ns, 0, $i) : false;
        return $this->getSidebarFN($ns, $file);
    }

    // print a sidebar edit button - if appropriate
    function tpl_sidebar_editbtn() {
        global $ID, $conf, $lang;

        // check sidebar configuration
        if (!$this->getConf('showeditbtn') || !$this->getConf('page')) return;

        // check sidebar page exists
        $fileSidebar = $this->getSidebarFN(getNS($ID), $this->getConf('page'));
        if (!$fileSidebar) return;

        // check user has edit permission for the sidebar page
        if (auth_quickaclcheck($fileSidebar) < AUTH_EDIT) return;

        ptln('<div class="secedit">', 6);
        ptln('<form class="button" method="post" action="' . wl($fileSidebar, 'do=edit') . '" onsubmit="return svchk()">', 8);
        ptln('<input type="hidden" name="do" value="edit" />', 10);
        ptln('<input type="hidden" name="rev" value="" />', 10);
        ptln('<input type="hidden" name="id" value="' . $fileSidebar . '" />', 10);
        ptln('<input type="submit" value="' . $lang['btn_sidebaredit'] . '" class="button" />', 10);
        ptln('</form>', 8);
        ptln('</div>', 6);
    }

    // display the sidebar
    function tpl_sidebar_content() {
        global $ID, $REV, $ACT, $conf;

        // save globals
        $saveID = $ID;
        $saveREV = $REV;
        $saveACT = $ACT;

        // discover file to be displayed in navigation sidebar
        $fileSidebar = '';

        if ($this->getConf('page')) {
                $fileSidebar = $this->getSidebarFN(getNS($ID), $this->getConf('page'));
        }

        // determine what to display
        if ($fileSidebar) {
            $ID = $fileSidebar;
            $REV = '';
            $ACT = 'show';
            // ptln(p_wiki_xhtml($ID, $REV, false));
            tpl_content();
        } else {
#            global $IDX;
#            html_index($IDX);
#            $ID = getNS($ID);
            $REV = '';
            $ACT = 'index';
            tpl_content();
        }

        // restore globals
        $ID = $saveID;
        $REV = $saveREV;
        $ACT = $saveACT;
    }
}