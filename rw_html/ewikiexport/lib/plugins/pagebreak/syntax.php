    <?php
    /**
     * Plugin Tab: Inserts a pagebreak into the document for every <pagebreak> it encounters.  Based on the tab plugin by Tim Skoch <timskoch@hotmail.com>
     * 
     * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
     * @author     Jonathan McBride and Chris Sturm - The University of Texas at Austin
     * 
     */
     
    if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
    if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
    require_once(DOKU_PLUGIN.'syntax.php');
     
    /**
     * All DokuWiki plugins to extend the parser/rendering mechanism
     * need to inherit from this class
     */
    class syntax_plugin_pagebreak extends DokuWiki_Syntax_Plugin {
     
        /**
         * return some info
         */
        function getInfo(){
            return array(
                'author' => 'Jonathan McBride and Chris Sturm',
                'email'  => 'j.mcbride@mail.utexas.edu',
                'date'   => '2007-08-08',
                'name'   => 'Pagebreak Plugin',
                'desc'   => 'Inserts " <br style="page-break-after:always;"> " into the html of the document for every <pagebreak> it encounters',
                'url'    => 'NA',
            );
        }
     
        /**
         * What kind of syntax are we?
         */
        function getType(){
            return 'substition';
        }
     
        /**
         * What kind of syntax do we allow (optional)
         */
    //    function getAllowedTypes() {
    //        return array();
    //    }
     
        /**
         * What about paragraphs? (optional)
         */
    //    function getPType(){
    //        return 'normal';
    //    }
     
        /**
         * Where to sort in?
         */ 
        function getSort(){
            return 999;
        }
     
     
        /**
         * Connect pattern to lexer
         */
        function connectTo($mode) {
          $this->Lexer->addSpecialPattern('<pagebreak>',$mode,'plugin_pagebreak');
    //      $this->Lexer->addEntryPattern('<TEST>',$mode,'plugin_test');
        }
     
    //    function postConnect() {
    //      $this->Lexer->addExitPattern('</TEST>','plugin_test');
    //    }
     
     
        /**
         * Handle the match
         */
        function handle($match, $state, $pos, &$handler){
            switch ($state) {
              case DOKU_LEXER_ENTER : 
                break;
              case DOKU_LEXER_MATCHED :
                break;
              case DOKU_LEXER_UNMATCHED :
                break;
              case DOKU_LEXER_EXIT :
                break;
              case DOKU_LEXER_SPECIAL :
                break;
            }
            return array();
        }
     
        /**
         * Create output
         */
        function render($mode, &$renderer, $data) {
            if($mode == 'xhtml'){
                $renderer->doc .= "<br style=\"page-break-after:always;\">";            // ptype = 'normal'
    //            $renderer->doc .= "<p>Hello World!</p>";     // ptype = 'block'
                return true;
            }
            return false;
        }
    }
     
    //Setup VIM: ex: et ts=4 enc=utf-8 :


