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

/*
 // Inici per a depuració: recuperació de missatges d'error
 ini_set('display_errors', 1);
 ini_set('log_errors', 1);
 ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
 //error_reporting(E_ALL);
 error_reporting(E_ALL & ~E_NOTICE);
*/

// must be run from within DokuWiki
if (!defined('DOKU_INC')) die();

   // Afegit per recuperar cami base ( jordi )
   // Definim  la base inicial de manera dinàmica
   // Això deixa sense utilitat  el 'prepara.php' anterior
   $base_inici =  get_base($_SERVER["REQUEST_URI"]);
   //echo "Base inici: ".$base_inici."<br>";
   //echo "DOKU_BASE: ".DOKU_BASE."<br>";
   //echo "DOKU_REL: ".DOKU_REL."<br>";
   //echo "DOKU_INC: ".DOKU_INC."<br>";
   // Llegim títol del curs
   // $base = str_replace(':', '/', $conf['template.base']);
   $base = str_replace(':', '/', $base_inici);

   // Modificat Jordi : canviem adreça absoluta per l'emmagatzemada a la configuració 13/05/2010 
   $file  = $conf['savedir']."/pages/".trim($base)."/index.txt";
   //$file = "/dades/wikiform/data/pages/".trim($base)."/index.txt";
   $titolcurs = llegeixtitols($file);
   // afegit jordi 13/06/2012
   //$conf['titolpag'] = $titolcurs;
   // fi afegit
   $fileini = $conf['savedir']."/pages/".trim($base)."/ini.txt";
   $ini = get_ini($fileini) ;   
   //if (isset($ini))
   //    echo "MENÚ CUSTOM"; 
        
//echo $fileini;
//print_r($ini);
//--------------------------------//
//Redireccions per "maquillar" l'adreça 
// Afegit Jordi 13/05/2010
// En cas que vulguem entrar a un subdirectori contenidor de cursos -> redirecció cap a índex general de cursos  
$senseindex = str_replace(':index','',$base_inici); // traiem paraula 'index'
$abase_ini =  explode(":", $senseindex);	
/*
if (in_array( $abase_ini[count($abase_ini)-1], $conf['directoris']) 
		or trim($base_inici)=="index" 
		or trim($base_inici)==""
		or trim($base_inici)=="/")

		{
  	header( "Location: /wikiform/wikiexport/cursos/index");
}else{
	// En cas que l'adreça d'entrada sigui del tipus '/cursos/..../dxxx' (sense barra final)
	//  hi afegim la barra i tornem a carregar l'adreça
	// (si ja hi  hem posat la barra final, ja s'hi afegeix  'index' al final i funciona)
	$abase =  explode("/", $_SERVER["REQUEST_URI"]);
	
	if ($abase[count($abase)-1] == get_curs($_SERVER["REQUEST_URI"])
		and $abase[count($abase)-1] != "index"
		and $abase[count($abase)-1] != "presentacio"){

		$desti =  $_SERVER["REQUEST_URI"]."/";
		//header( "Location: $desti");
	}  
} */        
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $conf['lang']?>" lang="<?php echo $conf['lang']?>" dir="<?php echo $lang['direction']?>">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>
    <?php 
    //     echo strip_tags($conf['title'])
      echo "Ateneu - Materials i recursos per a la formaci&#243; - Departament d'Ensenyament - ".$titolcurs[0];
    ?>

  </title>

  <?php tpl_metaheaders()?>

  <link rel="shortcut icon" href="<?php echo DOKU_TPL?>images/favicon.ico" />

  <?php /*old includehook*/ @include(dirname(__FILE__).'/meta.html')?>
  
</head>

<?php
/**
 * prints a horizontal navigation bar (composed of <li> items and some CSS tricks)
 * with the current active item highlited
 */
function tpl_tabnavi(){
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
      echo '> ';
      tpl_link(wl($pagename), $title);  
    }
    echo "</li>\n";
  }
  //always add link to recent page, unless $action id already 'recent'
  if (tpl_getConf('navbar_recent')) {
    if ($ACT == 'recent') {
      echo('<li id="current"><div id="current_inner">'.tpl_getConf('navbar_recent').'</div></li>  ');
    } else {
      echo('<li>'); tpl_actionlink('recent', '','',tpl_getConf('navbar_recent')); echo("</li> \n");
    }
  }
  echo("</ul>\n");
}
?>


<body>

<?php /*old includehook*/ @include(dirname(__FILE__).'/topheader.html')?>

<div class="dokuwiki">

<?php html_msgarea()?>

<?php
//global $NS; //modificat nova versió 12/2013
global $ID;
global $conf;
$base_inici = $GLOBALS['base_curs'] ;

$NS = getNS($ID); //modificat nova versió 12/2013
function get_sitelist($NS) {
    global $conf;

    $NS = cleanID($NS);
	$namespace = utf8_encodeFN(str_replace(':', '/', $NS));

    $opts = array( 'depth' => '4', 'skipacl' => true);
	$data = array();
	require_once (DOKU_INC.'inc/search.php');
	//search($data, $conf['datadir'], 'search_allpages', $opts, $namespace);
	search($data, $conf['datadir'], 'search_index', $opts, $namespace);

	return $data;
}

function getFullLink($link){
    global $conf;
    $base_inici = $GLOBALS['base_curs'] ;
    
    $link=$link;
    if($conf['template.follow']){
      $link .= strpos($lnk, '?')===false ? "?" : "&";
      $link.="tpl=".$conf['template'];
 
    // MODIFICAT Canviem la base del curs ( jordi ) 
    //  if(isset($conf['template.base']))   // modificat
    if(isset($base_inici))      
         //$link.="&docbase=".$conf['template.base'];  // modificat
         $link.="&docbase=".$base_inici;
      if(isset($conf['template.title']))
         $link.="&title=".$conf['template.title'];
    }
    return $link;
}

function getCleanName($name, $num_mod, $num_prac){
    global $conf;
//echo $name;
    $menuops = $conf['menus'];
    $name=str_replace('_', ' ', $name);
    $name=str_replace(':', ' - ', $name);
    
    if ($num_prac >9){
    	$name=str_replace('practica', 'Pr&agrave;c.', $name);
    }else{
    	$name=str_replace('practica', 'Pr&agrave;ctica', $name);
    }    	
    
    if ($num_mod >9){
    	$name=str_replace('modul', 'M&ograve;d.', $name);
    }else{
    	$name=str_replace('modul', 'M&ograve;dul', $name);
    } 
	
    if (array_key_exists($name, $menuops)) {
  	  $name=str_replace($name, $menuops[$name], $name);
      //$name = $menuops[$name];  
    }else{
      $name = ucfirst($name);
    }

    $elements = explode(" - ",$name);  
    foreach($elements as $e)
        $nom .= ucfirst($e)." - ";
    
    $name = substr($nom, 0, -3);
     
 return $name;
}

/**
 * Funció per a la generació del nom en menús custom
 * 
 * 
 */
function getCleanName2($name, $num_mod, $num_prac, $menuini){
    // global $conf;

    // Si seriat,traiem el número d'ordre
    if (seriat($name)){
	$nom = $name;
	$name = treu_numero($name);
    }

    if ( array_key_exists($name,$menuini) ){          
        $name=str_replace($name, $menuini[$name], $name);
    }elseif ( array_key_exists($name.'*',$menuini) ) {
        $name=str_replace($name, $menuini[$name.'*'], $nom);
        $name=str_replace('_', ' ', $name);
    }else{
        return;
    }
 return $name;
}


/**
 * Funció per a la generació del nom per als subtítols als menús custom
 * 
 * 
 */
function getCleanNameSub($name, $num_mod, $num_prac, $menuini){
    $name=str_replace(':', ' - ', $name);

    // Si seriat,traiem el número d'ordre
 	$elements = explode(" - ",$name);  

    foreach($elements as $e){
        if (seriat($e)){		
	    $name = treu_numero($e);
        }               
        if ( array_key_exists($e,$menuini) ){          
            $name=str_replace($e, $menuini[$e], $e);
        }elseif ( array_key_exists($name.'*',$menuini) ) {				
            $name=str_replace($name, $menuini[$name.'*'], $e);
            $name=str_replace('_', ' ', $name);
        }
        $nom .= $name." - ";       
    }
    $name = substr($nom, 0, -3);
     
 return $name;
}

$data=get_sitelist($NS);
$prefixlen=strlen($NS)+1;
$first=true;

// Si està definit $ini i $ini['menu'], preparem per omplir array $data
if (isset($ini['menu'])) {
    $datanou = array();

    foreach ($ini['menu'] as $clau => $menu) {
        if (substr($clau, -1) == '*') {
            foreach ($data as $site) {
                $iden = substr($site['id'], $prefixlen);
                if (treu_numero($iden) == substr($clau, 0, strlen($clau) - 1)) {
                    array_push($datanou, $site);
                }
            }
        } else {
            foreach ($data as $site) {
                //echo $iden." "; 
                $iden = substr($site['id'], $prefixlen);
                if ($iden == $clau) {
                    array_push($datanou, $site);
                }
            }
        }
    }
}

// modificat canvi base ( jordi )
// $inBase = $NS==$conf['template.base'];     // modificat
$inBase = $NS==$base_inici;

// Nom de la pàgina
$pageName=substr($ID, $prefixlen);

// Nom complet de la pàgina
//if(isset($conf['template.base'])){   // modificat
if(isset($base_inici)){
   //$fullPageName=substr($ID, strlen($conf['template.base'])+1);    // modificat
   $fullPageName=substr($ID, strlen($base_inici)+1);
   if($pageName=='index'){
      $len=strlen('index');
      if(strpos($fullPageName, ':')!==false){
        $len++;
      }
      $fullPageName=substr($fullPageName, 0, strlen($fullPageName)-$len);
  }
}
else{
  $fullPageName=$pageName; 
}   
  
  // Ordenem els items: Guia - Competències - Mòduls - Exercicis - Projecte
  $data_moduls=array();
  $data_exer=array();
  $data_comp=array();
  $data_guia=array();
  $data_proj=array();
  $data_gloss=array();
  // AFEGIT Jordi afegit per a l'índex quan ha de sortir
  $data_ind=array();
  $data_ref=array(); // Guia ràpida

  // AFEGIT Jordi afegit per al cas que es tracti d'un curs curricular
  $data_curri=array();

  // AFEGIT Jordi afegit per al cas que es tracti d'un curs curricular
  $data_annex=array();

  foreach($data as $site) {
  // MODIFICAT
    if(strpos($site[id], 'exercici')!==false)
       array_push($data_exer, $site);
    else if(strpos($site[id], 'competenci')!==false)
        array_push($data_comp, $site);
    else if(strpos($site[id], 'index')!==false)
        array_push($data_ind, $site);
    else if(strpos($site[id], 'guia')!==false)
        array_push($data_guia, $site);
    else if(strpos($site[id], 'projecte')!==false)
        array_push($data_proj, $site);
  	else if(strpos($site[id], 'g_rapida')!==false)
        array_push($data_ref, $site);		
  	else if(strpos($site[id], 'annex')!==false)
        array_push($data_annex, $site);	
	else if ((strpos($site[id], 'glossari')!==false)  or strpos($site[id], 'lectures'))		
	    array_push($data_gloss, $site);	
	else if(strpos($site[id], 'ppartida')!==false or
		    strpos($site[id], 'ppartidae')!==false or
		    strpos($site[id], 'ppartidai')!==false)
        array_push($data_curri, $site);	
    else                                            
        array_push($data_moduls, $site);
 }

// AFEGIT jordi 20/10/2010
// podem controlar número de pràctiques i mòduls i escurçar-los si volem
if(strpos(substr($data_moduls[0]['id'],-11), 'modul')!==false){
	$num_mod = count($data_moduls)+count($data_curri)+count($data_ref)+count($data_gloss)+count($data_annex);
}	

if(strpos(substr($data_moduls[0]['id'],-11), 'practica')!==false){
	$num_prac = count($data_moduls);
}		

if (isset($ini['menu'])){
    $data = $datanou;
}else{
    $data=array_merge($data_ind,$data_guia, $data_proj, $data_curri, $data_comp, $data_moduls, $data_exer, $data_ref, $data_gloss, $data_annex);
}

// 09/03/2011 afegit jordi :Si hem marcat  a la pàgina wiki amb "<html><!--nomenu--></html>", no apareixerà tampoc el títol del curs (es posarà manualment)
//print_r($titolcurs);

print('<div class="headermain">');

print( '<a href="/wikiform/wikiexport"><span class="logoateneu"></span></a>');
/*
echo isset($ini['mostratitol']);
echo isset($ini['titol']);
echo "!".$ini['mostratitol']."!";
echo $ini['titol'];
*/
//print_r($ini);
//echo "(".isset($ini['mostratitol'])." -- ".trim($ini['mostratitol'])." -- ".isset($ini['titol']).") -- ".!empty($ini['titol']) ;

// Mostrem el títol tenint en compte $ini['mostratitol'] si existeix
if ( isset($ini['mostratitol']) and trim($ini['mostratitol']) == "1" and isset($ini['titol']) and !empty($ini['titol'])){    
    $codi = '';
    if (!empty($ini['codi'])){
       $codi  = $ini['codi']. " - ";
    }
    print('<div class="titol">'.$codi.$ini['titol'].'</div>');
}else{
	
   //if ($titolcurs['nomenu'] != 1 or trim($ini['mostratitol']) == "1" ){
   if ($titolcurs['nomenu'] != 1 and !isset($ini['mostratitol']) ){
       print('<div class="titol">'.$titolcurs[0].'</div>');
   }   
}

    if (isset($ini['menu'])){   
	$cleanName=trim(getCleanNameSub($fullPageName, $num_mod, $num_prac, $ini['menu']));
    }else{
        $cleanName=trim(getCleanName($fullPageName, $num_mod, $num_prac));
    }
    if($cleanName!='')
      $cleanName=' - '.$cleanName;
    //print('<span class="titol">'.$conf['template.title'].$cleanName.'</span><br>');
    // Afegim salt de línia entre títol i navegació
    //  print('<div class="titol">'.$titolcurs[0].'</div>');
 
    //  print('<div class="subtitol">'.substr($cleanName,2).'</div>');
//}
global $conf;

// afegit jordi 13-06-2012
$conf['cleanName'] = substr($cleanName,2); 
// fi afegit  

// 09/03/2011 afegit jordi :Si hem marcat  a la pàgina wiki amb "<html><!--nomenu--></html>", no apareixerà tampoc el títol del curs (es posarà manualment)
if (isset($ini) and trim($ini['mostrasubtitol']) == "1" and trim(substr($cleanName,2)) != 'ini'  ){	
    print('<div class="subtitol">'.substr($cleanName,2).'</div>');   
// afegit comprovació 'tag' per tal que no es mostri titol a pàgina resum de tags
}else if( !isset($ini) and $titolcurs['nomenu'] != 1 and $base_inici != "tag" and trim(substr($cleanName,2)) != 'ini' ){
    print('<div class="subtitol">'.substr($cleanName,2).'</div>');
 //  print('<div class="subtitol">'.str_replace('.html', '', substr($cleanName,2)).'</div>');
}


print('</div>'); // tanquem div class="headermain"
  
// Creació de menú segons opció triada
// Triem tipus de menú
$opmenu = $conf['opmenu'];

switch ($opmenu) {
////////////////////////
//  Opció 3
//  Opció triada per defecte per a la presentació de les exportacions
    case '3':

// Afegit Jordi 06/05/2010
// En cas que sigui la pàgina índex inicial de cursos, no mostrem el menú
$nomenu = 0;
if (trim($base_inici)=="index"
			or trim($base_inici)==""
			or trim($base_inici)=="cursos:index"
			or trim($base_inici)=="cursos:presentacio"
			or in_array( $abase_ini[count($abase_ini)-1], $conf['directoris'])){
	$nomenu = 1;
}	



 if ((isset($ini) and trim($ini['mostramenu']) == '1') or (!isset($ini))){


if ($nomenu != 1){
// AFEGIT jordi : si és pàgina índex de cursos, saltem el menú
// AFEGIT jordi 08/03/2011 : si hem marcat a la pàgina '<html><!--nomenu--></html>'
//  $titolcurs['nomenu'] = 1   
// Si hem marcat  a la pàgina wiki amb "<html><!--nomenu--></html>", no apareixerà el menú superior

if ($titolcurs['nomenu'] != 1 and page_exists($ID) == 1){

if (substr(trim($base_inici), 0, 12) != "cursos:index"  ){
//if (substr(trim($base_inici), 0, 12) != "cursos:index" ){
print ('<div class="navega">');

print('<div id="navcontainer">');
print('<ul id="navlist">');


// Fi afegit
/*print_r($abase_ini);
$re = in_array( $abase_ini[count($abase_ini)-1], $conf['directoris']);
print($abase_ini[count($abase_ini)-1]);
*/

if(!$inBase and $nomenu !=1){
    //if (isset($ini['mostramenu']) and trim($ini['mostramenu']) == '1'){
    print('<li><a href="'.getFullLink("../index").'" title="Inici">&lt;&lt;</a></li>');
	$first=false;
}

// Afegit Jordi 13/05/2010
// Si volem mostrar una primera icona al menú, que porti a l'índex general de cursos.
// Ho podem deixar comentat si no interessa
/*
$compara = get_curs($_SERVER["REQUEST_URI"])."/index";
$mida = strlen($compara);
if ($compara == substr($_SERVER["REQUEST_URI"],-$mida)){
	print('<li><a href="/wikiform/wikiexport/cursos/index" title="Índex cursos"><-</a></li>');
	$first=false;
}*/
// Fi afegit

foreach ( $data as $site ) {

    $link=substr($site['id'], $prefixlen);

    $link=str_replace(':','/',$link);

    $name=$link;

    if ($nomenu == 1){
      continue;
    }

    global $conf;

    $amaga = $conf['amaga'];
     
    if (in_array($name, $amaga)) {
      continue;
    }

    if($name=="index" && $pageName==$name)
      continue;
      
    if($site['type']=='d')
     	$link.='/index';


   if(!$first)
     print('  ');
   else
     $first=false;
  
   if($site['id']==$ID){
      if (isset($ini) and trim($ini['mostramenu']) == "1"){          
           print('<li id="active"><a id="current" href="#">'.getCleanName2($name, $num_mod, $num_prac,$ini['menu']).'</a></li>');
      }else{
           print('<li id="active"><a id="current" href="#">'.getCleanName($name, $num_mod, $num_prac).'</a></li>');
      }
   } else {
       if (isset($ini) and trim($ini['mostramenu']) == "1"){
           print('<li><a href="'.getFullLink($link).$ext.'">'.getCleanName2($name, $num_mod, $num_prac, $ini['menu']).'</a></li>');
       }else{
           print('<li><a href="'.getFullLink($link).$ext.'">'.getCleanName($name, $num_mod, $num_prac).'</a></li>');
       }      
      
   }
}
print('</ul>');
print('</div>');

print('</div>');
} // afegit jordi : si és l'índex general de cursos, hem saltat el menú. Aquí tamquem l'if corresponent 06/05/2010

} //afegit jordi : si s'ha marcat que no volem menú superior

} // if $nomenu != 1

break;
 }
// Fi opció 3
///////////////

}   // fi switch

//print('</div>');
?>


 <?php
 // Ho posem dins de php perquè no es vegi en el codi html
 " <!-- div class='stylehead'>
  </div -->
      
  <!-- ?php /*old includehook*/ @include(dirname(__FILE__).'/pageheader.html')?-->"
 ?>
 
  <div class="page">

    <!-- ......... wikipage start ......... -->
    <?php 
    // si es tracta d'una pàgina ini (amb definicions de menús, títols, etc.)
    // ocultem el contingut
    if (trim(substr($cleanName,2)) != 'ini'){        
        tpl_content();
    }
    ?>
    <!-- ......... wikipage stop  ......... -->

  </div>
  <div class="clearer">&nbsp;</div>

  <?php flush()?>
 
<?php /*old includehook*/ @include(dirname(__FILE__).'/footer.html')?>

</div>

</body>
</html>

<?php
///////////////////////////////////////////////////////////////////////////////
// Funció llegeixtitols()
// Recupera títols del curs i de cada mòdul
// Torna un array amb [0] = titol del curs i, la resta, titols de mòduls
// Paràmetres: $fitxer = str (path i nom del fitxer)
// Jordi
function llegeixtitols($fitxer){
    
    if (!file_exists($fitxer))
    	return;
    
   
    $linies = file($fitxer);

    $titols = array();
	
	 // jordi Afegit per marcar que no volem que es generi el menú en aquesta pàgina
    // $titols['nomenu'] = 0 ;
    
	foreach($linies as $linia) {
  //echo "|".trim($linia)."|";
      // Mirem el títol del curs
      $inici = strpos($linia,"<html><!--");    // primer
      $fi = strrpos($linia,"-->");
      if ($fi > 0){
        $titols[] = substr($linia,$inici+10,$fi-10 );
      }
    
	// jordi Afegit per marcar que no volem que es generi el menú en aquesta pàgina
      if (trim($linia) === "<html><!--nomenu--></html>") {
        $titols['nomenu'] = 1; 
      }
	
      //echo $titols[0]."<br>";
  /*    
      $inici = strpos($linia,"=====");    // primer
      $fi = strrpos($linia,"=====");
      if ($fi > 0){
        $titols[] = substr($linia,$inici+7,$fi-7 );
      }
 */
      // Mirem títols de cada mòdul
      if ( (strpos($linia,"modul") > 0) || (strpos($linia,"mòdul") > 0) ){
        $inici = strpos($linia,"|");
        $fi = strrpos($linia,"]]")-1;
    
        if ($inici > 0) {
          $titols[] =  substr($linia,$inici+1,$fi-$inici);
        }
      } 
    }
    return $titols;
}


///////////////////////////////////////////////////////////////////////////////
// Funció get_base()
// Recupera camí base del curs
// Substitueix el que es feia amb 'prepara.php' pel que fa al camí base del curs sol·licitat
// No cal cap crida ni a un fitxer extern ni cal cap procés previ
// Això permetrà accés simultani al servidor des de diferents cursos
// Jordi
function get_base($url) {
    global $conf;
      
  if ($url != '/wikiform/wikiexport/'){
    $curs  = get_curs($url);

    // Recuperem base inici a la variable $cami_curs 
    $cami_curs = str_replace( "/wikiform/wikiexport/", " ", $url);
    $cami_curs = substr($cami_curs, 0, strpos($cami_curs,$curs)+strlen($curs) );
    $cami_curs = str_replace( "/", ":", $cami_curs);
//echo $cami_curs;
   // Definim el camí com a global
   $GLOBALS['base_curs'] = trim($cami_curs);
  }
   return $cami_curs;
}
  
/*
 *  Funció get_curs()

 Recupera el curs a exportar
 Passem la url i ens torna el curs a exportar
 * 
 */
function get_curs($url){
    global $conf; 

    $items = explode("/",$url);
  	$fet = 0;
    $directoris = $conf['directoris'];
    // Captem el codi del curs (l'extraiem de la url passada)
    foreach ($directoris as $directori){
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

/**
 *  Funció get_ini()
*
 *Recuperem definicions inicla per al curs des de ini.txt
 */
function get_ini($fileini){
 
   if (file_exists($fileini))   {
      $torna = array();  
      $linies = file($fileini);

      foreach($linies as $linia){
         $trans = explode('|',$linia);         
         if (trim($trans[3]) == ''){
            $torna[trim($trans[1])] = $trans[2];
         }else{
            $torna['menu'][trim($trans[2])] = trim($trans[3]);
         }
      }
   }else{
     return ;
   }
          
    return $torna;
}  

/*
 *  Funció seriat()

 Torna si l'element forma part d'una sèrie (s'acaba en número: ex. modul_1, modul_2, etc)
 */
function seriat($nom){
    $tros = explode("_",$nom);
    
    $seriat = false;
    if (is_numeric($tros[count($tros)-1])){
        $seriat = true;
    }
    
    return $seriat;   
}  


/*
 *  Funció treu_numero()
 * 
 *  Treu el número d'un element seriat
 */
function treu_numero($nom){
    $tros = explode("_",$nom);
    
    $seriat = false;
    if (is_numeric($tros[count($tros)-1])){
        $seriat = true;
    }
    
    $tros = explode("_", $nom);
    if ($seriat){
        for ($i = 0; $i < count($tros)-1; $i++)
        $torna .= $tros[$i];
    }

    return $torna;   
}  

?>
