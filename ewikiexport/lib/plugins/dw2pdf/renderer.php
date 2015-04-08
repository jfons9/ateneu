<?php
/**
 * DokuWiki Plugin dw2pdf (Renderer Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Andreas Gohr <gohr@cosmocode.de>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");
require_once DOKU_INC.'inc/parser/xhtml.php';

class renderer_plugin_dw2pdf extends Doku_Renderer_xhtml {

    private $lastheadlevel = 0;

    /**
     * Make available as XHTML replacement renderer
     */
    public function canRender($format){
        if($format == 'xhtml') return true;
        return false;
    }

    /**
     * Simplified header printing with PDF bookmarks
     */
    function header($text, $level, $pos) {
        if(!$text) return; //skip empty headlines

        $hid = $this->_headerToLink($text,true);

        // add PDF bookmark
        $bmlevel = $this->getConf('maxbookmarks');
        if($bmlevel && $bmlevel >= $level){
            // PDF readers choke on invalid nested levels
            $step = $level - $this->lastheadlevel;
            if($step > 1) $level = $this->lastheadlevel;
            $this->lastheadlevel = $level;

            $this->doc .= '<bookmark content="'.$this->_xmlEntities($text).'" level="'.($level-1).'" />';
        }

        // print header
        $this->doc .= DOKU_LF."<h$level>";
        $this->doc .= "<a name=\"$hid\">";
        $this->doc .= $this->_xmlEntities($text);
        $this->doc .= "</a>";
        $this->doc .= "</h$level>".DOKU_LF;
    }

    /**
     * Wrap centered media in a div to center it
     */
    function _media ($src, $title=NULL, $align=NULL, $width=NULL,
                      $height=NULL, $cache=NULL, $render = true) {

        $out = '';
        if($align == 'center'){
            $out .= '<div align="center" style="text-align: center">';
        }

        $out .= parent::_media ($src, $title, $align, $width, $height, $cache, $render);

        if($align == 'center'){
            $out .= '</div>';
        }

        return $out;
    }

    /**
     * hover info makes no sense in PDFs, so drop acronyms
     */
    function acronym($acronym) {
        $this->doc .= $this->_xmlEntities($acronym);
    }


    /**
     * reformat links if needed
     */
/*
    function _formatLink($link){
        // prefix interwiki links with interwiki icon
        if($link['name'][0] != '<' && preg_match('/\binterwiki iw_(.\w+)\b/',$link['class'],$m)){
            if(file_exists(DOKU_INC.'lib/images/interwiki/'.$m[1].'.png')){
                $img = DOKU_BASE.'lib/images/interwiki/'.$m[1].'.png';
            }elseif(file_exists(DOKU_INC.'lib/images/interwiki/'.$m[1].'.gif')){
                $img = DOKU_BASE.'lib/images/interwiki/'.$m[1].'.gif';
            }else{
                $img = DOKU_BASE.'lib/images/interwiki.png';
            }

            $link['name'] = '<img src="'.$img.'" width="16" height="16" style="vertical-align: center" class="'.$link['class'].'" />'.$link['name'];
        }
        return parent::_formatLink($link);
    }
  */  
    
    /**
     * reformat links if needed
     * versió modificada josep i jordi
     */

    function _formatLink($link){
       // prefix interwiki links with interwiki icon
       //if($link['name'][0] != '<' && preg_match('/binterwiki iw_(.w+)b/',$link['class'],$m)){
       // modificat  josep
       // no es presentava la icona prèvia als links externs i interns
       // revisem creació d'icones per als diferents tipus de documnet enllaçat         
       $pattern = 'interwiki iw_';
       $pos = strpos($link['class'], $pattern);   
       if( $link['name'][0] != '<' && $pos!==false){
            $m[1] = substr($link['class'], $pos+strlen($pattern)) ;
            if(file_exists(DOKU_INC.'lib/images/interwiki/'.$m[1].'.png')){
                $img = DOKU_BASE.'lib/images/interwiki/'.$m[1].'.png';
            }elseif(file_exists(DOKU_INC.'lib/images/interwiki/'.$m[1].'.gif')){
                $img = DOKU_BASE.'lib/images/interwiki/'.$m[1].'.gif';
            }else{
                $img = DOKU_BASE.'lib/images/interwiki.png';
            }

            $link['name'] = '<img src="'.$img.'" width="16" height="16" style="vertical-align: center" class="'.$link['class'].'" /> '.$link['name'];
       }
       // ******************************************************************************************
       // if($link['name'][0] != '<' && preg_match('/bmf_(.w+)b/',$link['class'],$m)){
       // modificat jordi josep 
       $pos = strpos($link['class'], 'mf_');
       if( $link['name'][0] != '<' && $pos!==false){
            $m[1] = substr($link['class'], $pos+3) ;
         
            if(file_exists(DOKU_INC.'lib/images/fileicons/'.$m[1].'.png')){
                $img = DOKU_BASE.'lib/images/fileicons/'.$m[1].'.png';
            }elseif(file_exists(DOKU_INC.'lib/images/fileicons/'.$m[1].'.gif')){
                $img = DOKU_BASE.'lib/images/fileicons/'.$m[1].'.gif';
            }else{
                $img = DOKU_BASE.'lib/images/interwiki.png';
            }
                
            $link['name'] = '<img src="'.$img.'" width="16" height="16" style="vertical-align: center" class="'.$link['class'].'" /> '.$link['name'];          
        }
        if($link['class']=='urlextern'){
            if(file_exists(DOKU_INC.'lib/tpl/default/images/link_icon.gif')){
                $img = DOKU_BASE.'lib/tpl/default/images/link_icon.gif';
            }else{
                $img = DOKU_BASE.'lib/images/interwiki.png';
            }
            $link['name'] = '<img src="'.$img.'" width="16" height="16" style="vertical-align: center" class="'.$link['class'].'" /> '.$link['name']; 

       }

       //if($link['class']=='mail'){
       $pattern = 'mail';
       if (strpos($link['class'], $pattern) !==false){        
            if(file_exists(DOKU_INC.'lib/tpl/default/images/mail_icon.gif')){
                $img = DOKU_BASE.'lib/tpl/default/images/mail_icon.gif';
            }else{
                $img = DOKU_BASE.'lib/images/interwiki.png';
            }
            $link['name'] = '<img src="'.$img.'" width="16" height="16" style="vertical-align: center" class="'.$link['class'].'" />'.$link['name'];            
        }

        return parent::_formatLink($link);
    }

    /**
     * no obfuscation for email addresses
     */
    function emaillink($address, $name = NULL) {
        global $conf;
        $old = $conf['mailguard'];
        $conf['mailguard'] = 'none';
        parent::emaillink($address, $name);
        $conf['mailguard'] = $old;
    }

}

