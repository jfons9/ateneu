<?php
/**
 * Tagentry plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Robin Gareus <robin@gareus.org>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');

class action_plugin_tagentry extends DokuWiki_Action_Plugin {
  /**
   * return some info
   */
  function getInfo(){
    return array(
      'author' => 'Robin Gareus',
      'email'  => 'robin@gareus.org',
      'date'   => '2009-09-18',
      'name'   => 'tagentry Plugin',
      'desc'   => 'adds a tag-selection table below the entry form.',
      'url'    => 'http://mir.dnsalias.com/wiki/tagentry',
    );
  }

  /**
   * register the eventhandlers
   */
  function register(&$controller){
    // old hook
    if ($this->getConf('oldhook')===true) 
    $controller->register_hook('HTML_EDITFORM_INJECTION',
                               'BEFORE',
                               $this,
                               'handle_editform_output',
                               array('editform' => true, 'oldhook' => true));

    // new hook
    $controller->register_hook('HTML_EDITFORM_OUTPUT',
                               'BEFORE',
                               $this,
                               'handle_editform_output',
                               array('editform' => true, 'oldhook' => false));
  }

  /**
   * Create the additional fields for the edit form.
   */
  function handle_editform_output(&$event, $param){
    global $ID;
    global $conf;
   
    $curs = $this->get_curs($ID);  
    $adreca = explode(":",$ID);

    // Afegit Jordi
    // Només es  mostra selector de tags quan es tracta de pàgina index
    if (($adreca[count($adreca)-2] != $curs) or ($adreca[count($adreca)-2] == $curs and $adreca[count($adreca)-1] != 'index' ) ){
      return;  
    // fi afegit
    }elseif(!$param['oldhook']){
      $pos = $event->data->findElementByAttribute('type','submit');
      if(!$pos) return; // no button -> source view mode

      //echo "DEBUG: <pre>".print_r($event->data,true).'</pre>';
      // 
      // Comentat per fer-lo funcionar amb les darreres versions de dokuwiki 
      // http://blog.dinel.org.uk/?p=272
      /*if (   !empty($event->data->_hidden['prefix'])
          || !empty($event->data->_hidden['suffix'])) return;
      if ($event->data->findElementByType('wikitext')===false) return;
       * 
       */
      
    }elseif(!$event->data['writable']){       
      return;
    }
    
    // get all tags
    $tagns=$this->getConf('namespace');
    if ($thlp=&plugin_load('helper', 'tag')) {
      if ($this->getConf('tagsrc') == 'Pagenames in tag NS') {
        $tagnst=$thlp->getConf('namespace');
        if(!empty($tagnst)) $tagns=$tagnst;
      }
    }
   //echo "DEBUG: <pre>".print_r($this, true).'</pre>';
   /*
    if ($this->getConf('tagsrc') == 'All tags' && $thlp) {
      $alltags=$this->_gettags($thlp);
    } else {
      $alltags=$this->_getpages($tagns);
    } 
 */

    if ($this->getConf('tagsrc') == 'All tags') { 
       $alltags=array_map('trim', idx_getIndex('subject', '_w')); 
    } else { 
       $alltags=$this->_getpages($tagns);            
    }    

    // get already assigned tags for this page
    $assigned = false;
    if(1) {  # parse wiki-text to pick up tags for draft/prevew
      $wikipage = '';
      if(!$param['oldhook']){
        $wt = $event->data->findElementByType('wikitext');
        if ($wt!==false) {
          $wikipage = $event->data->_content[$wt]['_text'];
        }
      } else { 
        # TODO get current draft or latest.wiki-text
        # for /old/ DokuWiki's using HTML_EDITFORM_INJECTION
      }

      if (!empty($wikipage))
        if (preg_match('@\{\{tag>(.*?)\}\}@',$wikipage, $m)) {
          $assigned = split(' ',$m[1]);
        }
    }
    if (!is_array($assigned)) { # those are from the prev. saved version.
      global $ID;
      $meta= array();
      $meta = p_get_metadata($ID); 
      $assigned = $meta['subject'];
    } 

    $options=array(
      'tagboxtable' => $this->getConf('table'),
      'tablerowcnt' => intval($this->getConf('tablerowcnt')),
      'limit'       => intval($this->getConf('limit')),
      'blacklist'   => split(' ',$this->getConf('blacklist')),
      'assigned'    => $assigned,
      'class'       => '',
      'height'      => $this->getConf('height'),
    );

    $out  = '';
    $out .= '<div id="plugin__tagentry_wrapper">';
    $out .= $this->_format_tags($alltags,$options);
    $out .= '</div>';

    if($param['oldhook']){
      // old wiki - just print
      echo $out;
    }else{
      // new wiki - insert at correct position
      $event->data->insertElement($pos++,$out);
    }
  }

  /**
   * callback function for dokuwiki search()
   *
   * Build a list of tags from the tag namespace
   * $opts['ns'] is the namespace to browse
   */
  function _tagentry_search_tagpages(&$data,$base,$file,$type,$lvl,$opts){
    $return = true;
    $item = array();
    if($type == 'd') {
      // TODO: check if namespace mismatch -> break recursion early.
      return true;
    }elseif($type == 'f' && !preg_match('#\.txt$#',$file)){
      return false;
    }

    $id = pathID($file);
    if (getNS($id) != $opts['ns']) return false;

    if(isHiddenPage($id)){
      return false;
    }

    if($type=='f' && auth_quickaclcheck($id) < AUTH_READ){
      return false;
    }

    $data[]=noNS($id);
    return $return;
  }

  /**
   * list all tags from the topic index.
   * (requires newer version of the tag plugin)
   *
   * @param $thlp  pointer to tag plugin's helper 
   * @return array list of tag names, sorted by frequency
   */
  function _gettags(&$thlp) {
    $data = array();
    if (!is_array($thlp->topic_idx)) return $data;
    foreach ($thlp->topic_idx as $k => $v) {
        if (!is_array($v) || empty($v) || (!trim($v[0]))) continue;
        $data[$k] = count($v);
    }
    arsort($data);
    return(array_keys($data)); 
  }

  /**
   * list all pages in the namespace.
   *
   * @param $tagns namespace to search.
   * @return array list of tag names.
   */
  function _getpages($tagns='wiki:tags') {
    global $conf;
    require_once(DOKU_INC.'inc/search.php');
    $data = array();
    search($data, $conf['datadir'], array($this, '_tagentry_search_tagpages'),
           array('ns' => $tagns));
    return($data); 
  }

  function clipstring($s, $len=22) {
    return substr($s,0,$len).((strlen($s)>$len)?'..':'');
  }

  function escapeJSstring ($o) {
    return ( # TODO: use JSON ?!
      str_replace("\n", '\\n', 
        str_replace("\r", '', 
          str_replace('\'', '\\\'', 
            str_replace('\\', '\\\\', 
        $o)))));
  }

  /** case insenstive in_array();.
   */
  function _in_casearray($needle, $haystack) {
    if (!is_array($haystack)) return false;
    foreach ($haystack as $t) {
      if (strcasecmp($needle,$t)==0) return true;
    }
    return false;
  }


  
  /** 
   * render and return the tag-select box.
   *
   * @param $alltags array of tags to display.
   * @param $options array 
   * @return string XHTML form.
   */
  function _format_tags($alltags, $options) {
     global $conf;
      
     $tot = array("Destinatari" => $conf['tags_destinataris'], 
        "Temàtica" => $conf['tags_tematiques'],
        "Matèries" => $conf['tags_materies'] );
         
    $rv='';
    if (!is_array($alltags)) return $rv;
    if (count($alltags)<1) return $rv;

    if ($options['tablerowcnt'] < 1 || $options['tablerowcnt]'] > 10)
      $options['tablerowcnt']=5;

    $ohi=floatval($options['height']);
    $ohu='em';
    if ($ohi!=0) {
      if (preg_match('/[0-9\w]+(px|em|pt)\b/',$options['height'],$m)) {
        $ohu=$m[1];
      }
    }
    if ($ohi<0) {
      $t=count($alltags);
      if ($options['limit']>0) $t=min($t,$options['limit']);
      $dstyle=' style="max-height:'.ceil(1.0+$ohi*(-$t)).$ohu.';"';
    }
    elseif ($ohi>0) {
      $dstyle=' style="max-height:'.$ohi.$ohu.';"';
    }
    else 
      $dstyle='';
       
    $rv.='<div class="'.$options['class'].'">';
    //$rv.=' <div><label>'.$this->getLang('assign').'</label></div>';
    $rv.=' <div class="taglist"'.$dstyle.'>';
    $rv .= '<br> <br><section class="tagwfContainer"> ';
    $rv .='<header>';
    $rv .="<h5>Assigna tags</h5>";
    $rv .='</header>';
    $rv .='<div class="content">'; 
    
    foreach ($tot as $k => $v ) {
        $rv .= '<header> '.$k.'</header>';
        $rv .='<div class="users">';
        
    if ($options['tagboxtable']) $rv.='<table><tr>';
    else $rv.='  <div>';
    
    $i=0;
    natcasesort($alltags);

      foreach ($v as $t => $d) {
      if (is_array($options['blacklist']) 
          && $this->_in_casearray($t, $options['blacklist'])) 
        continue;

      if (($i%$options['tablerowcnt'])==0 && $i!=0) { 
        if ($options['tagboxtable']) $rv.="</tr>\n<tr>";
        ##else $rv.="<br/>\n";
        #else $rv.="  </div><div>\n";
      }
      $i++;
      if ($options['tagboxtable']) $rv.='<td>';
      $rv.='<label title="'.$d.'" ><input type="checkbox" id="plugin__tagentry_cb'.$t.'"    title="'.$d.'" ';
      $rv.=' value="1" name="'.$t.'"';
      if ($this->_in_casearray($t,$options['assigned']))
        $rv.=' checked="checked"';
      $rv.=' onclick="tagentry_clicktag(\''.$this->escapeJSstring($t).'\', this);"';
      $rv.=' /> '.$this->clipstring($t).'</label>&nbsp;';
      $rv.="\n";
        if ($options['tagboxtable']) $rv.='</td>';
        if ($options['limit']>0 && $i>$options['limit']) { 
          $rv.="&nbsp;...";
          break;
        }
    }    

    if ($options['tagboxtable']) $rv.='</tr></table>';
    else $rv.='  </div>'; 
    $rv.=' </div>';
}
    $rv.=' </section>';
    
    $rv.=' </div>';
    $rv.='</div>';
    return ($rv);
  }
  
  
  function get_curs($url){
    global $conf; 
    $items = explode(":",$url);

    $fet = 0;
    $directoris = $conf['directoris'];
    // Captem el codi del curs (l'extraiem de la url passada)
    foreach ($directoris as $directori){
    //    echo $directori."<br>";
      if (strpos($url, $directori) !==false)   {
	  $i = 0;
        for ($i; $i <= count($items); $i++) {
           if ($items[$i] == $directori && $fet == 0)  {
		   $curs = $items[$i+1];
		   $fet = 1;
           }
        }      
      }  
    }
    return  $curs;
  }
  
         }
//Setup VIM: ex: et sw=2 ts=2 enc=utf-8 :
