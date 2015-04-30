<?php
global $conf;

$conf['send404'] = 0; //Send a HTTP 404 status for non existing pages?
//$conf['template'] = 'm1';
//$conf['template'] = 'doogiestpl';
//
$conf['template.follow']=false;
$conf['template.nomedialinks']=true;
$conf['template.noedit']=true;
$conf['template.title']='';
$conf['plugin']['sidebar']['enable'] = 0;
$conf['cachedir']='data/cache';
$conf['tmpdir']='data/tmp';
$conf['opmenu']='3';
// Directoris possibles on se situaran els cursos sol.licitats
// Cal que estiguin ordenats des de 'dins' cap a 'fora' (primer els m�s profunds) ( jordi )
$conf['directoris']=array("cma","cco","cci","fic_orientacio","fic","lle","bib","ose", "tac", "eso_btx", "inf_pri", "interniv","actic","tallers", "tic", "dirs", "tutorials", "escola_inclusiva", "equips_directius", "gestio_centres", "gestio", "cmd", "pas", "curriculum", "biblioteques", "altres_materials", "autoria", "cursos", "formgest", "materials", "z_gestio", "z_test","wikiexport");

$conf['menus']=array('tasques' => 'Tasques',
'introduccio' => 'Introducci&oacute;',
'index' => '<',
'guia' => 'Guia',
'modul' => 'M&ograve;dul',
'projecte' => 'Projecte',
'competencies' => 'Compet&egrave;ncies',
'continguts' => 'Continguts',
'g rapida' => 'Refer&egrave;ncies',
'avis' => 'Av&iacute;s',
'fitxa' => 'Fitxa',
'annex' => 'Annex',
'lectures' => 'Lectures',
'glossari' => 'Glossari',
'taula' => 'Taula',
'taula eines' => 'Eines',
'ppartida' => 'Punt de partida',
'00 introduccio' => 'Introducci&oacute;',
'10 estructura' => 'Estructura',
'20 continguts' => 'Continguts',
'30 redaccio materials' => 'Redacci&oacute; de materials',
'33 citacions documentals' => 'Citacions documentals',
'34 referencia imatges' => 'Refer&egrave;ncia imatges',
'40 aspectes tecnics' => 'Aspectes t&egrave;cnics',
'50 aspectes legals' => 'Aspectes legals',
'60 accessibilitat' => 'Accessibilitat',
'71 dw sintaxi' => 'Sintaxi DW',
'72 nova versio' => 'Nova versi&oacute;',
'74 pmf' => 'PMF',
'75 cercadors' => 'Cercadors',
'1.autoria' => 'Autoria',
'2.materials' => 'Materials',
'3.encarrecs' => 'Enc&#224;rrecs',
'4.utilitats' => 'Utilitats',
'5.especificacions' => 'Especificacions',
'filezilla'=> 'Filezilla',
'bloc' => 'Bloc');


// array amb noms de pàgines que no volem que apareguin al menú
$conf['amaga']=array(formularis_exemples, discusio, discussio, discussion, exercicis, exercici, proposta_exercicis, tasques, gtaf,
orientacio_f2,orientacio_f4,orientacio_f3,diferent, orientacio, orientacio_bloc, orientacio_activitats, orientacio_f1,
orientacio_07,orientacio_08,orientacio_01, orientacio_02, orientacio_03, orientacio_04, orientacio_05, orientacio_06, );

$conf['tags_destinataris'] = array("llar_infants" => "Llar d'infants",
"parvulari" => "Parvulari",
"primària" => "Primària",
"eso" => "ESO",
"batxillerat" => "Batxillerat",
"fp" => "Formació professional",
"especial" => "Ed. especial",
"ed_distància" => "Ed. distància",
"ed_adults" => "Ed. adults",
"formació_permanent" => "Formació permanent",
"biblioteca" => "Biblioteca/Centre doc.",
"administració_educativa" => "Ad. educativa",
"política_educativa" => "Política educativa",
"altres" => "Altres"
);

$conf['tags_tematiques'] = array("diversitat" => "Diversitat",
"avaluació" => "Avaluació",
"biblioteques_escolars" => "Biblioteques escolars",
"competències_bàsiques" => "Competències bàsiques",
"didàctica" => "Didàctica",
"organització_educativa" => "Organització educativa",
"orientació" => "Orientació",
"tecnologia_educativa" => "Tecnologia educativa"
);

$conf['tags_materies'] = array("biologia" => "Biologia",
"ciències_naturalesa" => "Ciències de la naturalesa",
"ciències_experimentals" => "Ciències experimentals",
"ciències_socials" => "Ciències socials",
"música" => "Música",
"cultura_clàssica" => "Cultura clàssica",        
"dibuix" => "Dibuix",
"educació_física" => "Educació física",
"educació_ visual" => "Educació visual i plàstica",
"filosofia" => "Filosofia",
"física" => "Física",
"geografia" => "Geografia",
"geologia" => "Geologia",
"història" => "Història",
"història_art" => "Història de l'Art",
"literatura" => "Literatura",
"llengua_aranesa" => "Llengua aranesa",
"llengua_castellana" => "Llengua castellana",
"Llengua_catalana" => "Llengua catalana",
"anglès" => "Llengua estrangera: Anglès",
"francès" => "Llengües estrangera: Francès",
"matemàtiques" => "Matemàtiques",
"química" => "Química",
"religió" => "Religió",
"tecnologies" => "Tecnologies",
"treball_recerca" => "Treball de recerca",
"treball síntesi" => "Treball de síntesi",
"coeducació" => "Coeducació"       
 );
