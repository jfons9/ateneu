<?php
  /**
  * Plugin Style/Verse: More styles for dokuwiki
  * Format: see README
  *
  * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
  * @author     Ivan A-R <ivan@iar.spb.ru>
  * @page       http://iar.spb.ru/en/projects/doku/styler
  * @version	0.2
  */

  if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
  if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
  require_once(DOKU_PLUGIN.'syntax.php');

  /**
  * All DokuWiki plugins to extend the parser/rendering mechanism
  * need to inherit from this class
  */
  class syntax_plugin_styler_verse extends DokuWiki_Syntax_Plugin {

    /**
    * return some info
    */
    function getInfo(){
      return array(
        'author' => 'Ivan A-R',
        'email'  => 'ivan@iar.spb.ru',
        'date'   => '2007-07-23',
        'name'   => 'Verse. Styler plugin',
        'desc'   => 'More formatings: verse, quote, epigraph, style [left, right, center, justify, float-left, float-right, box, background]',
        'url'    => 'http://iar.spb.ru/en/projects/doku/styler',
        'version' => '0.2',
      );
    }

    function getType(){ return 'protected';}
    //function getAllowedTypes() { return array('container','substition','protected','disabled','formatting','paragraphs'); }
    function getPType(){ return 'block';}

    function getSort(){ return 205; }

    function connectTo($mode) {
      $this->Lexer->addEntryPattern('<verse.*?>(?=.*?\x3C/verse\x3E)', $mode, 'plugin_styler_verse');
    }
    function postConnect() {
      $this->Lexer->addExitPattern('</verse>', 'plugin_styler_verse');
    }


    /**
    * Handle the match
    */
    function handle($match, $state, $pos, &$handler){
      global $conf;
      switch ($state) {
        case DOKU_LEXER_ENTER :
          $match = str_replace(array('<', '>'), array('', ''), $match);
          $attrib = preg_split('/\s+/', strtolower($match));
          if ($attrib[0])
          {
            return array(array_shift($attrib), $state, $attrib);
          }
          else
          {
            return array($match, $state, array());
          }
        case DOKU_LEXER_UNMATCHED : return array($match, $state, array());
        case DOKU_LEXER_EXIT :      return array('',     $state, array());
      }
      return array();
    }

    /**
    * Create output
    */
    function render($mode, &$renderer, $data) {
      global $st;
      global $et;
      global $conf;
      global $prt;
      if($mode == 'xhtml'){
        switch ($data[1]) {
        case DOKU_LEXER_ENTER :
          $class = '';
          foreach(array('left', 'right', 'center', 'justify', 'box', 'float-left', 'float-right', 'background') as $v)
          {
            if (in_array($v, $data[2]))
            {
              $class .= ' styler-'.$v;
            }
          }
          //$renderer->doc .= "</p>\n"; // It is hack
          $renderer->doc .= '<div class="verse'.$class.'">'."\n".'<pre>';
          break;
        case DOKU_LEXER_UNMATCHED :
            $renderer->doc .= preg_replace("/\b([A-H][#]?[m]?[75]?)\b/m", "<span>\\1</span>", $data[0]);;
          break;
        case DOKU_LEXER_EXIT :
          $renderer->doc .= "</pre>\n</div>";// "</p>" and "\n</p>" is hack
          break;
}
        return true;
      }
      return false;
    }
  }
//Setup VIM: ex: et ts=4 enc=utf-8 :
?>
