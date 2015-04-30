<?php
 /**
  * dw2Pdf Plugin: Conversion from dokuwiki content to pdf.
  *
  * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
  * @author     Luigi Micco <l.micco@tiscali.it>
  * @author     Andreas Gohr <andi@splitbrain.org>
  */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

class action_plugin_dw2pdf extends DokuWiki_Action_Plugin {

    private $tpl;

    /**
     * Constructor. Sets the correct template
     */
    public function __construct(){
        $tpl = false;
        if(isset($_REQUEST['tpl'])){
            $tpl = trim(preg_replace('/[^A-Za-z0-9_\-]+/','',$_REQUEST['tpl']));
        }
        if(!$tpl) $tpl = $this->getConf('template');
        if(!$tpl) $tpl = 'default';
        if(!is_dir(DOKU_PLUGIN.'dw2pdf/tpl/'.$tpl)) $tpl = 'default';

        $this->tpl = $tpl;
    }

    /**
     * Register the events
     */
    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'convert', array());
        $controller->register_hook('TEMPLATE_PAGETOOLS_DISPLAY', 'BEFORE', $this, 'addbutton', array());
    }

    /**
     * Do the HTML to PDF conversion work
     *
     * @param Doku_Event $event
     * @param array      $param
     * @return bool
     */
    public function convert(&$event, $param) {
        global $ACT;
        global $REV;
        global $ID;
        global $conf; //jordi


///jordi
        /*
        $base_inici = trim($this->get_base($_SERVER["REQUEST_URI"]));
        $base = str_replace(':', '/', $base_inici);
        $base = str_replace('_export/pdf/', '', $base);

        $path = realpath($conf['savedir'] . "/pages/" . trim($base));
*/

        // our event?
        if (( $ACT != 'export_pdfbook' ) && ( $ACT != 'export_pdf' )) return false;

        // check user's rights
        if ( auth_quickaclcheck($ID) < AUTH_READ ) return false;

        // one or multiple pages?
        $list  = array();

        if($ACT == 'export_pdf') {
            $list[0] = $ID;
            
            $title = $ID;
            //$title = p_get_first_heading($ID);
            //if ($title == '')
            //    $title = $ID;
        } elseif(isset($_COOKIE['list-pagelist']) && !empty($_COOKIE['list-pagelist'])) {
            //is in Bookmanager of bookcreator plugin title given
            if(!$title = $_GET['pdfbook_title']) {  //TODO when title is changed, the cached file contains the old title
                /** @var $bookcreator action_plugin_bookcreator */
                $bookcreator = plugin_load('action', 'bookcreator');
                msg($bookcreator->getLang('needtitle'), -1);

                $event->data               = 'show';
                $_SERVER['REQUEST_METHOD'] = 'POST'; //clears url
                return false;
            }
            $list = explode("|", $_COOKIE['list-pagelist']);
        } else {
            /** @var $bookcreator action_plugin_bookcreator */
            $bookcreator = plugin_load('action', 'bookcreator');
            msg($bookcreator->getLang('empty'), -1);

            $event->data               = 'show';
            $_SERVER['REQUEST_METHOD'] = 'POST'; //clears url
            return false;
        }

        // it's ours, no one else's
        $event->preventDefault();

        // prepare cache
        $cache = new cache(join(',',$list).$REV.$this->tpl,'.dw2.pdf');
        $depends['files']   = array_map('wikiFN',$list);
        $depends['files'][] = __FILE__;
        $depends['files'][] = dirname(__FILE__).'/renderer.php';
        $depends['files'][] = dirname(__FILE__).'/mpdf/mpdf.php';
        $depends['files']   = array_merge($depends['files'], getConfigFiles('main'));

        // hard work only when no cache available
        if(!$this->getConf('usecache') || !$cache->useCache($depends)){
            // initialize PDF library
            require_once(dirname(__FILE__)."/DokuPDF.class.php");
            $mpdf = new DokuPDF();

            // let mpdf fix local links
            $self = parse_url(DOKU_URL);
            $url  = $self['scheme'].'://'.$self['host'];
            if($self['port']) $url .= ':'.$self['port'];
            $mpdf->setBasePath($url);

            // Set the title
            $mpdf->SetTitle($title);

            // some default settings
            $mpdf->mirrorMargins = 1;
            $mpdf->useOddEven    = 1;
            $mpdf->setAutoTopMargin = 'stretch';
            $mpdf->setAutoBottomMargin = 'stretch';

            // load the template
            $template = $this->load_template($title);

            // prepare HTML header styles
            $html  = '<html><head>';
            $html .= '<style type="text/css">';
            $html .= $this->load_css();
            $html .= '@page { size:auto; '.$template['page'].'}';
            $html .= '@page :first {'.$template['first'].'}';
            $html .= '</style>';
            $html .= '</head><body>';
            
            //**** afegit jordi 11/06/2014
            // Recuperació i presentació del títol del curs          
            $base = str_replace(':', '/', $this->get_base($_SERVER["REQUEST_URI"]));
            $base = str_replace('_export/pdf/', '', $base);
            $file = $conf['savedir'] . "/pages/" . trim($base) . "/index.txt";
            $file = str_replace("_export/pdf/", "", $file);
            $titolcurs = $this->titol_curs($file);

            // Nom de la pàgina
            $prefixlen = strlen($NS);
            $pageName = substr($ID, $prefixlen);

            $fullPageName = substr($ID, strlen($base_inici) + 1);

            if ($pageName == 'index') {
                $len = strlen('index');
                if (strpos($fullPageName, ':') !== false) {
                    $len++;
                }
                $fullPageName = substr($fullPageName, 0, strlen($fullPageName) - $len);
            }

            $len_base = strlen($base);
            //echo "<br>".$base."<br>".$ID ;  
            $name = substr($ID, $len_base);
            $name = str_replace('_', ' ', $name);
            $aname = explode(":", $name);

            foreach ($aname as $nom) {
                if ($nom != "index") {
                    $noms .= ucfirst($nom) . " - ";
                }
            }
            $noms = substr($noms, 0, -3);
            if ($noms == "Index") {
                $noms = '';
            }
            $fullPageName = substr($ID, strlen($base_inici) + 1);

            if ($titolcurs != '') {
                $html = $html . "<h2 style='background:#CD7972; padding:6px; font-family:Arial; -moz-border-radius: 8px;border-radius: 8px;' >" . $titolcurs . "</h2>";
            }
            //if ($noms != ''){

            if ($noms != '' && strpos($file, '/fic/') == true) {
                $html = $html . "<h4 style='background:#E2E2E2; padding:6px; font-family:Arial; -moz-border-radius: 8px;border-radius: 8px;' >" . $noms . "</h4>";
            }
            //**** fi afegit jordi

            
            
            
            
            $html .= $template['html'];
            $html .= '<div class="dokuwiki">';

            // loop over all pages
            $cnt = count($list);
            for($n=0; $n<$cnt; $n++){
                $page = $list[$n];

                $html .= p_cached_output(wikiFN($page,$REV),'dw2pdf',$page);
                $html .= $this->page_depend_replacements($template['cite'], cleanID($page));
                if ($n < ($cnt - 1)){
                    $html .= '<pagebreak />';
                }
            }

            $html .= '</div>';
            
            
            //**** afegit jordi 11/06/2014
            // Fem que no aparegui la icona de PDF en el document pdf generat
            // l'enllaç continuarà existint però sense res que hi apunti
            $html = str_replace('<img src="/wikiform/wikiexport/lib/plugins/dw2pdf/pdf2.jpg" align="right" alt="" width="48" height="48" />', '', $html); // jordi                                 
            //**** fi afegit jordi
            // En l'exportació a pdf es produïa una modificació del tag <h2>...</h2>) pel de <h0>...</h0> i es perdia la jerarquia
            // Canviem el tag h0 per una definició d'estil de font que manifesti la jerarquia del títol 
            $html = str_replace('<h0>', '<span style=" font-size: 120%; font-weight: bold; margin-left: 0;">', $html); // jordi   
            $html = str_replace('</h0>', '</span>', $html); // jordi   
            
            
            
            $mpdf->WriteHTML($html);

            // write to cache file
            $mpdf->Output($cache->cache, 'F');
        }

        // deliver the file
        header('Content-Type: application/pdf');
        header('Cache-Control: must-revalidate, no-transform, post-check=0, pre-check=0');
        header('Pragma: public');
        http_conditionalRequest(filemtime($cache->cache));

        $filename = rawurlencode(cleanID(strtr($title, ':/;"','    ')));
        if($this->getConf('output') == 'file'){
            header('Content-Disposition: attachment; filename="'.$filename.'.pdf";');
        }else{
            header('Content-Disposition: inline; filename="'.$filename.'.pdf";');
        }

        if (http_sendfile($cache->cache)) exit;

        $fp = @fopen($cache->cache,"rb");
        if($fp){
            http_rangeRequest($fp,filesize($cache->cache),'application/pdf');
        }else{
            header("HTTP/1.0 500 Internal Server Error");
            print "Could not read file - bad permissions?";
        }
        exit();
    }

    /**
     * Add 'export pdf'-button to pagetools
     *
     * @param Doku_Event $event
     * @param mixed      $param not defined
     */
    public function addbutton(&$event, $param) {
        global $ID, $REV, $conf;

        if($this->getConf('showexportbutton') && $event->data['view'] == 'main') {
            $params = array('do' => 'export_pdf');
            if($REV) $params['rev'] = $REV;

            switch($conf['template']) {
                case 'dokuwiki':
                case 'arago':
                    $event->data['items']['export_pdf'] =
                        '<li>'
                        .'<a href='.wl($ID, $params).'  class="action export_pdf" rel="nofollow" title="'.$this->getLang('export_pdf_button').'">'
                        .'<span>'.$this->getLang('export_pdf_button').'</span>'
                        .'</a>'
                        .'</li>';
                    break;
            }
        }
    }

    /**
     * Load the various template files and prepare the HTML/CSS for insertion
     */
    protected function load_template($title){
        global $ID;
        global $conf;
        $tpl = $this->tpl;

        // this is what we'll return
        $output = array(
            'html'  => '',
            'page'  => '',
            'first' => '',
            'cite'  => '',
        );

        // prepare header/footer elements
        $html = '';
        foreach(array('header','footer') as $t){
            foreach(array('','_odd','_even','_first') as $h){
                if(file_exists(DOKU_PLUGIN.'dw2pdf/tpl/'.$tpl.'/'.$t.$h.'.html')){
                    $html .= '<htmlpage'.$t.' name="'.$t.$h.'">'.DOKU_LF;
                    $html .= file_get_contents(DOKU_PLUGIN.'dw2pdf/tpl/'.$tpl.'/'.$t.$h.'.html').DOKU_LF;
                    $html .= '</htmlpage'.$t.'>'.DOKU_LF;

                    // register the needed pseudo CSS
                    if($h == '_first'){
                        $output['first'] .= $t.': html_'.$t.$h.';'.DOKU_LF;
                    }elseif($h == '_even'){
                        $output['page'] .= 'even-'.$t.'-name: html_'.$t.$h.';'.DOKU_LF;
                    }elseif($h == '_odd'){
                        $output['page'] .= 'odd-'.$t.'-name: html_'.$t.$h.';'.DOKU_LF;
                    }else{
                        $output['page'] .= $t.': html_'.$t.$h.';'.DOKU_LF;
                    }
                }
            }
        }

        //**** afegit jordi 11/06/2014
        // Fem que es mostri encapçalament 'a mida' si es tracta de material fic
        $categoria = '';
        if (substr($ID, 0, strpos($ID, ':')) == 'fic') {
            $categoria = "Formació interna de centre";
        }
        //***** fi afegit
        
        // prepare replacements
        $replace = array(
                '@PAGE@'    => '{PAGENO}',
                '@PAGES@'   => '{nb}',
                '@TITLE@'   => hsc($title),
                '@WIKI@'    => "Departament d'Ensenyament. ".$conf['title'],
                '@WIKIURL@' => DOKU_URL,
                '@DATE@'    => dformat(time()),
                '@BASE@'    => DOKU_BASE,
                '@TPLBASE@' => DOKU_BASE.'lib/plugins/dw2pdf/tpl/'.$tpl.'/',
                '@CATEGORIA@' => $categoria, // jordi
        );

        // set HTML element
        $html = str_replace(array_keys($replace), array_values($replace), $html);
        //TODO For bookcreator $ID (= bookmanager page) makes no sense
        $output['html'] = $this->page_depend_replacements($html, $ID);

        // citation box
        if(file_exists(DOKU_PLUGIN.'dw2pdf/tpl/'.$tpl.'/citation.html')){
            $output['cite'] = file_get_contents(DOKU_PLUGIN.'dw2pdf/tpl/'.$tpl.'/citation.html');
            $output['cite'] = str_replace(array_keys($replace), array_values($replace), $output['cite']);
        }

        return $output;
    }

    /**
     * @param string $raw code with placeholders
     * @param string $id  pageid
     * @return string
     */
    protected function page_depend_replacements($raw, $id){
        global $REV;

        // generate qr code for this page using google infographics api
        $qr_code = '';
        if ($this->getConf('qrcodesize')) {
            $url = urlencode(wl($id,'','&',true));
            $qr_code = '<img src="https://chart.googleapis.com/chart?chs='.
                $this->getConf('qrcodesize').'&cht=qr&chl='.$url.'" />';
        }
        // prepare replacements
        $replace['@ID@']      = $id;
        $replace['@UPDATE@']  = dformat(filemtime(wikiFN($id, $REV)));
        $replace['@PAGEURL@'] = wl($id, ($REV) ? array('rev'=> $REV) : false, true, "&");
        $replace['@QRCODE@']  = $qr_code;

        return str_replace(array_keys($replace), array_values($replace), $raw);
    }

    /**
     * Load all the style sheets and apply the needed replacements
     */
    protected function load_css(){
        global $conf;
        //reusue the CSS dispatcher functions without triggering the main function
        define('SIMPLE_TEST',1);
        require_once(DOKU_INC.'lib/exe/css.php');

        // prepare CSS files
        $files = array_merge(
                    array(
                        DOKU_INC.'lib/styles/screen.css'
                            => DOKU_BASE.'lib/styles/',
                        DOKU_INC.'lib/styles/print.css'
                            => DOKU_BASE.'lib/styles/',
                    ),
                    css_pluginstyles('all'),
                    $this->css_pluginPDFstyles(),
                    array(
                        DOKU_PLUGIN.'dw2pdf/conf/style.css'
                            => DOKU_BASE.'lib/plugins/dw2pdf/conf/',
                        DOKU_PLUGIN.'dw2pdf/tpl/'.$this->tpl.'/style.css'
                            => DOKU_BASE.'lib/plugins/dw2pdf/tpl/'.$this->tpl.'/',
                        DOKU_PLUGIN.'dw2pdf/conf/style.local.css'
                            => DOKU_BASE.'lib/plugins/dw2pdf/conf/',
                    )
                 );
        $css = '';
        foreach($files as $file => $location){
            $display = str_replace(fullpath(DOKU_INC), '', fullpath($file));
            $css .= "\n/* XXXXXXXXX $display XXXXXXXXX */\n";
            $css .= css_loadfile($file, $location);
        }

        if(function_exists('css_parseless')) {
            // apply pattern replacements
            $styleini = css_styleini($conf['template']);
            $css = css_applystyle($css, $styleini['replacements']);

            // parse less
            $css = css_parseless($css);
        } else {
            // @deprecated 2013-12-19: fix backward compatibility
            $css = css_applystyle($css,DOKU_INC.'lib/tpl/'.$conf['template'].'/');
        }

        return $css;
    }

    /**
     * Returns a list of possible Plugin PDF Styles
     *
     * Checks for a pdf.css, falls back to print.css
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     */
    protected function css_pluginPDFstyles(){
        $list = array();
        $plugins = plugin_list();

        $usestyle = explode(',',$this->getConf('usestyles'));
        foreach ($plugins as $p){
            if(in_array($p,$usestyle)){
                $list[DOKU_PLUGIN."$p/screen.css"] = DOKU_BASE."lib/plugins/$p/";
                $list[DOKU_PLUGIN."$p/style.css"] = DOKU_BASE."lib/plugins/$p/";
            }

            if(file_exists(DOKU_PLUGIN."$p/pdf.css")){
                $list[DOKU_PLUGIN."$p/pdf.css"] = DOKU_BASE."lib/plugins/$p/";
            }else{
                $list[DOKU_PLUGIN."$p/print.css"] = DOKU_BASE."lib/plugins/$p/";
            }
        }
        return $list;
    }
    
    /**
     * Funció titol_curs()
     * Recupera títol del curs actual
     * Torna  un string amb el títol
     * Paràmetres: $fitxer = str (path i nom del fitxer)
     * Jordi
     */
    function titol_curs($fitxer) {
        if (!file_exists($fitxer))
            return;

        //$fitxer = '/data/pages/fic/cma/cma01/index.txt';

        $linies = file($fitxer);

        foreach ($linies as $linia) {
            // Mirem el títol del curs
            // Si hi ha marcat nomenu, ho desestimem com a títol
            if (trim($linia) == "<html><!--nomenu--></html>") {
                $titol = '';
            } else {
                $inici = strpos($linia, "<html><!--");    // primer
                $fi = strrpos($linia, "-->");
                if ($fi > 0) {
                    $titol = substr($linia, $inici + 10, $fi - 10);
                }
            }
        }
        return $titol;
    }

    /**
     * Funció get_base()
     * Recupera camí base del curs
     * Substitueix el que es feia amb 'prepara.php' pel que fa al camí base del curs sol·licitat
     * No cal cap crida ni a un fitxer extern ni cal cap procés previ
     * Això permetrà&nbsp;accés simultani al servidor des de diferents cursos
     * Jordi Fons
     */
    function get_base($url) {
        global $conf;
        
       if ($url != '/wikiform/wikiexport/'){
        $curs = $this->get_curs($url);
        // Recuperem base inici a la variable $cami_curs 
        $cami_curs = str_replace("/wikiform/wikiexport/", " ", $url);
        $cami_curs = substr($cami_curs, 0, strpos($cami_curs, $curs) + strlen($curs));
        $cami_curs = str_replace("/", ":", $cami_curs);
        // Definim el camí com a global
        $GLOBALS['base_curs'] = trim($cami_curs);
       }
        return $cami_curs;
    }

    /**
     * Funció get_curs()
     * Recupera el curs a exportar
     * Passem la url i ens torna el curs a exportar
     * Jordi Fons  
     */
    function get_curs($url) {
        global $conf;

        $items = explode("/", $url);
        $fet = 0;
        $directoris = $conf['directoris'];

        // Captem el codi del curs (l'extraiem de la url passada)
        foreach ($directoris as $directori) {
            if (strpos($url, $directori) !== false) {
                $i = 0;
                for ($i; $i <= count($items); $i++) {
                    if ($items[$i] == $directori && $fet == 0) {
                        $curs = $items[$i + 1];
                        $fet = 1;
                    }
                }
            }
        }
        return $curs;
    }
    
}