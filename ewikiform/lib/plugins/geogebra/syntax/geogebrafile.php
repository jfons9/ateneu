<?php
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

class syntax_plugin_geogebra_geogebrafile extends DokuWiki_Syntax_Plugin {
	/*
   <applet
       code="geogebra.GeoGebraApplet"
       codebase="./"
       archive="./././sources/geogebra/geogebra.jar"
       width="551" height="359">
       <param name="filename" value="./././data/ggb/einheitsvektor.ggb">
       <param name="framePossible" value="false">
   </applet>
*/

  var $dflt = array(
  'name' => '',
  'width' => 551,
  'height' => 359,
  'language' => '',
  'country' => '',
  'showToolBar' => false,
  'showToolBarHelp' => false,
  'showAlgebraInput' => false,
  'showResetIcon' => true,
  'showMenuBar' => false,
  'enableShiftDragZoom' => true,
  'enableLabelDrags' => true,
  'enableRightClick' => true,
  'borderColor' => '',
  'bgcolor' => '',
  'framePossible' => 'true'
	); 

    /**
     * return some info
     */
    function getInfo(){
        return array(
            'author' => 'jaloma',
            'email'  => 'jaloma.ac@googlemail.com',
            'date'   => '28/10/2008',
            'name'   => 'GeoGebra Plugin',
            'desc'   => 'Include GeoGebra-Files to your Wiki with <geogebra name="" width="" height="">. See http://www.geogebra.org/source/program/applet/geogebra_applet_param.html',
		 'url'    => 'http://jaloma.ac.googlepages.com/plugin:geogebra'
        );
    }
/**
 * Plugin Type
 */
 
 	function getType(){ return 'substition'; }
 	function getSort(){ return 316; }
 	function connectTo($mode) { 
	$this->Lexer->addSpecialPattern("<geogebra.*?/>",$mode,'plugin_geogebra_geogebrafile');
	 }

	function matchLength() {
		return strlen("<geogebra ");
	}
	
    function handle($match, $state, $pos, &$handler){
	  // strip markup
      $match = html_entity_decode(substr($match,$this->matchLength(),-2));
      $gmap = $this->_extract_params($match);
      return $gmap;
	}

  /**
   * extract parameters for the gmsdisplay from the parameter string
   *
   * @param   string    $str_params   string of key="value" pairs
   * @return  array                   associative array of parameters key=>value
   */
  function _extract_params($str_params) {
    $param = array();
    preg_match_all('/(\w*)="(.*?)"/us',$str_params,$param,PREG_SET_ORDER);
    if (sizeof($param) == 0) {
      preg_match_all("/(\w*)='(.*?)'/us",$str_params,$param,PREG_SET_ORDER);
    }
    // parse match for instructions, break into key value pairs      
    $gmap = $this->dflt;
    foreach($param as $kvpair) {
      list($match,$key,$val) = $kvpair;
      if (isset($gmap[$key])) $gmap[$key] = $val;        
    }

    return $gmap;
  }
	
/**
 * Create output
 */
    function render($mode, &$renderer, $data) {
        if($mode == 'xhtml'){
            $renderer->doc .= $this->_contact($data);
            return true;
        }
        return false;
    }

	function getArchive() {
		return "geogebra.jar";
	}
	function getCode() {
		return "geogebra.GeoGebraApplet";
	}
	
    function _contact($data){
    	global $conf;
    	$file = $data['name'];
		$hasfile = $file != null && $file != '';
     	//$file = $conf['savedir'] . '/media/' . str_replace( ":", "/", $file );
		//$file = $conf['savedir'] . '/media' . str_replace( ":", "/", $file );
		//$file = "/formaciotic/data/media'. str_replace( ":", "/", $file );
//		$file = 'http://phobos.xtec.cat/formaciotic/wikiform/data/media' . str_replace( ":", "/", $file );
		$file = 'http://php-int.educacio.intranet/wikiform/wikiform/data/media' . str_replace( ":", "/", $file );
		$width = $data['width'];
		$height = $data['height'];
		$showToolBar = $data['showToolBar'];
		$showToolBarHelp = $data['showToolBarHelp'];
		$showAlgebraInput = $data['showAlgebraInput'];
		$showResetIcon = $data['showResetIcon'];
		$showMenuBar = $data['showMenuBar'];
		$enableShiftDragZoom = $data['enableShiftDragZoom'];
		$enableLabelDrags = $data['enableLabelDrags'];
		$enableRightClick = $data['enableRightClick'];
		$borderColor = $data['borderColor'];
		$bgcolor = $data['bgcolor'];
		$framePossible = $data['framePossible'];
		$language = $data['language'];
		$country = $data['country'];
    	$txt = "";
        //$file = "data/media/cursos/test/funciinterval2.ggb";
  //  	$txt = $file;
        $archive = $this->getConf('jarbase').'/'.$this->getArchive();
	   // $archive = $this->getConf('jarbase').$this->getArchive();
	  	$txt .= '<applet'.
       		' code="'.$this->getCode().'"'.
       		' archive="'.$archive.'"'.
       		' width="'.$width.'" height="'.$height.'">';
		if ($hasfile != '') {
       		$txt .= '<param name="filename" value="'.$file.'"/>';
		}
		$txt .=	' <param name="showToolBar" value="'.$showToolBar.'"/>';
		$txt .=	' <param name="showToolBarHelp" value="'.$showToolBarHelp.'"/>';
		$txt .=	' <param name="showAlgebraInput" value="'.$showAlgebraInput.'"/>';
		$txt .=	' <param name="showResetIcon" value="'.$showResetIcon.'"/>';
		$txt .= ' <param name="showMenuBar" value="'.$showMenuBar.'"/>';
		$txt .= ' <param name="enableShiftDragZoom" value="'.$enableShiftDragZoom.'"/>';
		$txt .= ' <param name="enableLabelDrags" value="'.$enableLabelDrags.'"/>';
		$txt .= ' <param name="enableRightClick" value="'.$enableRightClick.'"/>';
       $txt .= ' <param name="framePossible" value="'.$framePossible.'"/>';
		if ($borderColor != '') {
			$txt .= ' <param name="borderColor" value="'.$borderColor.'"/>';
		}
		if ($bgcolor != '') {
			$txt .= ' <param name="bgcolor" value="'.$bgcolor.'"/>';
		}
	   if ($language != '') {
	   	$txt .= ' <param name="language" value="'.$language.'"/>';
		if ($country != '') {
			$txt .= ' <param name="country" value="'.$country.'"/>';
		}
		}
	   $txt .= 'Si us plau <a href="http://www.java.com">instal·leu Java 1.4.2</a> (orposterior) per treballar amb aquesta pàgina.'.
   			'</applet>';
		$txt .= '<br />';
		if ($this->getConf('showHelpUrl')) {
			//$txt .= 'Ajuda en línia ';	
			$txt .= '<a href="http://www.geogebra.org/help/docude/" target="help_geogebra" title="Ajuda en anglès"> Ajuda en línia</a> &mdash;';
			//$txt .= '<a href="http://www.geogebra.org/cms/ca/help" target="help_geogebra">Ajuda en línia (cat)</a> &mdash;';
		}
		if ($this->getConf('showDownloadUrl') && $hasfile) {
			$txt .= ' <a href="'.$file.'" title="Descarrega l\'arxiu">Descarrega</a> &mdash;';
		}
		$user = $_SERVER['REMOTE_USER'];
		//$txt .= ' Creat per © <a href="http://www.geogebra.at/" target="geogebra">GeoGebra</a> durch '.$user;
	//	$txt .= ' Creat per © <a href="http://www.geogebra.at/" target="geogebra">GeoGebra</a>';
    	return $txt;	
	}
	
}//class
