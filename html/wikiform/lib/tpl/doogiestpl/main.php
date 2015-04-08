<?php
/**
 * DokuWiki Default Template
 *
 * This is the template you need to change for the overall look
 * of DokuWiki.
 *
 * You should leave the doctype at the very top - It should
 * always be the very first line of a document.
 *
 * @link   http://wiki.splitbrain.org/wiki:tpl:templates
 * @author Andreas Gohr <andi@splitbrain.org>
 */


 // Inici per a depuració: recuperació de missatges d'error
 ini_set('display_errors', 1);
 ini_set('log_errors', 1);
 ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
 //error_reporting(E_ALL);
 error_reporting(E_ALL & ~E_NOTICE);

// must be run from within DokuWiki
if (!defined('DOKU_INC')) die();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $conf['lang']?>"
 lang="<?php echo $conf['lang']?>" dir="<?php echo $lang['direction']?>">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>
    <?php echo strip_tags($conf['title'])?>
    [<?php tpl_pagetitle()?>]
  </title>

  <?php tpl_metaheaders()?>

  <link rel="shortcut icon" href="<?php echo DOKU_TPL?>images/favicon.ico" />

  <?php /*old includehook*/ @include(dirname(__FILE__).'/meta.html')?>
 <!--  <script type="text/javascript" src="/wikiform/html/scripts/MathJax/MathJax.js?config=TeX-AMS-MML_HTMLorMML"></script>  -->
</head>

<?php
/**
 * prints a horizontal navigation bar (composed of <li> items and some CSS tricks)
 * with the current active item highlited
 */
function tpl_tabnavi(){
  global $ID;
  global $ACT;
  global $conf;// afegit jordi
  global $lang;// afegit jordi
  global $auth;// afegit jordi
  global $USERINFO;// afegit jordi

  $buttons = array();  
  parse_str(tpl_getConf('navbar_buttons'), $buttons);
    
  echo("<ul>\n");
  foreach ($buttons as $title => $pagename) {
    echo '<li';
	// MODIFICAT  JORDI
	// Afegim control, per mostrar/no mostrar opció "Autoria" segons si l'usuari està validat (hi ha nom d'usuari) o no 
		if($USERINFO[name] == "")  {
			if($title != "Autoria"){
				if (strcasecmp($ID, $pagename) == 0 && $ACT == 'show') {
					echo ' id="current"><div id="current_inner">'.$title.'</div>';
				} else {
					echo '>';
					tpl_link(wl($pagename), $title);  
				}	
			}
		}else{
			if (strcasecmp($ID, $pagename) == 0 && $ACT == 'show') {
				echo ' id="current"><div id="current_inner">'.$title.'</div>';
			} else {
				echo '>';
				tpl_link(wl($pagename), $title);  
			}
		}
	echo "</li>\n";
  }
		
  // MODIFICAT JORDI
  
  //always add link to recent page, unless $action id already 'recent'
  if (tpl_getConf('navbar_recent')) {
    if ($ACT == 'recent') {
      echo('<li id="current"><div id="current_inner">'.tpl_getConf('navbar_recent').'</div></li>');
    } else {
      echo('<li>'); tpl_actionlink('recent', '','',tpl_getConf('navbar_recent')); echo("</li>\n");
    }
  }
  echo("</ul>\n");
}

/**
 * prints a horizontal navigation bar (composed of <li> items and some CSS tricks)
 * with the current active item highlited
 */
/*function tpl_tabnavi(){
  global $ID;
  global $ACT;
  $buttons = array();  
  parse_str(tpl_getConf('navbar_buttons'), $buttons);
    
  echo("<ul>\n");
  foreach ($buttons as $title => $pagename) {
    echo '<li';
    if (strcasecmp($ID, $pagename) == 0 && $ACT == 'show') {
      echo ' id="current"><div id="current_inner">'.$title.'</div>';
    } else {
      echo '>';
      tpl_link(wl($pagename), $title);  
    }
    echo "</li>\n";
  }
  //always add link to recent page, unless $action id already 'recent'
  if (tpl_getConf('navbar_recent')) {
    if ($ACT == 'recent') {
      echo('<li id="current"><div id="current_inner">'.tpl_getConf('navbar_recent').'</div></li>');
    } else {
      echo('<li>'); tpl_actionlink('recent', '','',tpl_getConf('navbar_recent')); echo("</li>\n");
    }
  }
  echo("</ul>\n");
}*/

?>


<body>
<?php /*old includehook*/ @include(dirname(__FILE__).'/topheader.html')?>
<div class="dokuwiki">
  <?php html_msgarea()?>

  <div class="stylehead">

    <div class="header">
      <div class="header_left"></div>
      <div class="logo">
		<a class="logologo" href="http://www.xtec.cat/formacio/index.htm" title="Formació del professorat" accesskey="f"> <!--<img src="images/logo.png" alt="Formació del professorat" title="Formació del professorat" /> --></a>
        <?php tpl_link(wl(),$conf['title'],'name="dokuwiki__top" id="dokuwiki__top" accesskey="h" title="[ALT+H]"')?>
        
      </div>
      <div class="header_right">
      <a href="http://www20.gencat.cat/portal/site/ensenyament" title="Departament d'Ensenyament" accesskey="d"><!-- <img src="images/edu.png" alt="Departament d'Ensenyament" title="Departament d'Ensenyament" /> --></a>
      </div>
      <div class="pagename">
        [[<?php tpl_link(wl($ID,'do=backlink'),tpl_pagetitle($ID,true),'title="'.$lang['btn_backlink'].'"')?>]]
      </div>

      <div id="tabnavi" class="tabnavi">
	    <?php tpl_tabnavi() ?>
	  </div>
	  
	  <div class="clearer"></div>
      <?php /*old includehook*/ @include(dirname(__FILE__).'/header.html')?>
	</div>  
  </div>
  <?php flush()?>
    
  
  <?php /*old includehook*/ @include(dirname(__FILE__).'/pageheader.html')?>

  <div class="page">
<!--  <a href="<?php echo exportlink($ID, '.odt')?>"><img src="<?php echo DOKU_BASE?>lib/plugins/odt/odt.png" alt="ODT Export"></a> -->
    <!-- ......... wikipage start ......... -->
    <?php tpl_content()?>
    <!-- ......... wikipage stop  ......... -->
  </div>

  <div class="clearer">&nbsp;</div>

  <?php flush()?>

 
  <!--  footer -->  
  <div class="stylefoot">

    <?php /*old includehook*/ @include(dirname(__FILE__).'/pagefooter.html')?>
    <div class="meta">
      <div class="user">
	    <?php tpl_userinfo()?>
      </div>
      <div class="doc">
		<?php tpl_pageinfo()?>
	  </div>
	</div>

    <div class="bar" id="bar__bottom">
       <!--  breadcrumbs and search -->
	  <?php if($conf['breadcrumbs']){?>
		<div class="breadcrumbs">
		  <?php tpl_breadcrumbs()?>
		</div>
	  <?php }?>
	
	  <?php if($conf['youarehere']){?>
		<div class="breadcrumbs">
		  <?php tpl_youarehere() ?>
		</div>
	  <?php }?>

      <div class="bar-left" id="bar__bottomleft">
        <?php tpl_button('edit')?>
        <?php tpl_button('login')?>
      
		<?php 
		// AFEGIT Jordi	10/02/2011
		$exporta=str_replace('wikiform/wikiform', 'wikiform/wikiexport', $_SERVER["REQUEST_URI"]) 
		//FI AFEGIT
		?>  
	 <a  style="     background-color: #FFFFFF;
    border: 1px solid #CCCCCC;
    color: #000000;
    cursor: pointer;
    font-size: 100%;
    margin: 1px;
    padding: 0.125em 0.4em;
    text-decoration: none;
    vertical-align: middle;
	background-color: #FFFFFF; background: url('/wikiform/wikiform/lib/tpl/doogiestpl/images/buttonshadow.png') repeat-x bottom #FFFFFF;" 
	href="<?php  echo $exporta?>" target= "_new">Exporta</a>
	</div>
	
	  
      <div class="bar-right" id="bar__bottomright">
        <?php tpl_actionlink('history','',' | ')?>
        <?php tpl_actionlink('subscription','',' | ')?> 
        <?php tpl_actionlink('admin','',' | ')?>
        <?php tpl_actionlink('profile','',' | ')?>
        <?php tpl_actionlink('index','',' | ') ?>
        <?php /*tpl_button('top')*/ ?>
		        
        <?php tpl_searchform() ?>
      </div>
       
      <div class="clearer"></div>
    </div>

  </div>

</div>
<?php /*old includehook*/ @include(dirname(__FILE__).'/footer.html')?>

<div class="no"><?php /* provide DokuWiki housekeeping, required in all templates */ tpl_indexerWebBug()?></div>
</body>
</html>
