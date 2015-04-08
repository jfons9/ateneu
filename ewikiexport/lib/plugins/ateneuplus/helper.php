<?php

/**
 * DokuWiki Plugin ateneuplus (Helper Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Jordi Fons <jfons@xtec.cat>
 */
// must be run within Dokuwiki
if (!defined('DOKU_INC'))
    die();

class helper_plugin_ateneuplus extends DokuWiki_Plugin {
    /*
     * Definim camí principal 
     */

    var $arrel = "/dades/wikiform/data/pages/";

    /**
     * Return info about supported methods in this Helper Plugin
     *
     * @return array of public methods
     */
    public function getMethods() {
        return array(
            array(
                'name' => 'getCurs',
                'desc' => 'torna el curs a partir de la seva url',
                'params' => array(
                    'url' => 'string',
                    'tipus' => 'integer'
                ),
                'return' => array('curs' => 'string')
            ),
            array(
                'name' => 'getTitolCurs',
                'desc' => 'torna el títol del curs a partir curs del seu ns (tipus=1) o la seva url (tipus=2)',
                'params' => array(
                    'pagina' => 'string',
                    'tipus' => 'integer'
                ),
                'return' => array('titol' => 'string')
            ),
            array(
                'name' => 'indexArray',
                'desc' => 'torna un array amb lels directoris de data',
                'params' => array(
                    'directory' => 'string',
                    'pattern' => 'string',
                    'recursive' => 'boolean'
                ),
                'return' => array('array_items' => 'array')
            ),
            array(
                'name' => '_hasSubdirs',
                'desc' => '',
                'params' => array(
                    'path' => 'string'
                ),
                'return' => array('resposta' => 'boolean')
            ),
            array(
                'name' => '_deleteFromArray',
                'desc' => '',
                'params' => array(
                    'array' => 'array',
                    'deleteIt' => 'string',
                    'useOldKeys' => 'boolean'
                ),
                'return' => array('resposta' => 'boolean')
            ),
            array(
                'name' => 'getCursos',
                'desc' => 'recupera array amb tos els cursos: path, codi i títol',
                'params' => array(
                    'cursos' => 'array'
                ),
                'return' => array('cursos' => 'array')
            ),
            array(
                'name' => 'llegeixIndexCurs',
                'desc' => 'recorre fitxer index.txt arrel del curs i retorna informació',
                'params' => array(
                    'curs' => 'string'
                ),
                'return' => array('info' => 'array')
            ),
            array(
                'name' => 'esPrincipal',
                'desc' => 'retorna si el darrer directori és el principal (inicial) d\'un curs o material',
                'params' => array(
                    'curs' => 'string'
                ),
                'return' => array('info' => 'array')
            )
        );
    }

    /*
     *  Funció getCurs
     * 
     *  Recupera el codi del curs
     *  
     *  @param $url string
     *  @param $tipus int
     *  @return $string
     * 
     *  @author Jordi Fons
     * 
     */

    function getCurs($url) {
        global $conf;

        if (strpos($url, ':') !== false) {
            $separador = ":";
        } else {
            $separador = "/";
        }
        //  if ($tipus == 2)
        //      $separador = "/";
        //echo $separador;
        $items = explode("$separador", $url);

        $fet = 0;
        // llegim  array amb els directoris "contenidors"
        $directoris = $conf['directoris'];

        // Captem el codi del curs (l'extraiem de la url passada)
        foreach ($directoris as $directori) {
            //echo $directori."<br>";
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

    function getCursos($paths) {

        $cursos = array();
        foreach ($paths as $i) {
            $cursos[] = array('cami' => $i,
                'codi' => $this->getCurs($i),
                'titol' => $this->gettitolCurs($i));
        }

        return $cursos;
    }

    /*
     *  Funció getTitolCurs
     * 
     *  Recupera títols del curs
     *  Torna títol
     *  @param $pagina string
     *  @param $tipus int
     *
     *  @author Jordi Fons
     * 
     */

    function getTitolCurs($pagina, $tipus = "1") {
        global $conf;


        //   echo "| " . $pagina . " |";
        if (strpos($pagina, ':') == false) {
            $separador = ":";
            $fitxer = str_replace(":", "/", $pagina);
        }

        $codi = explode('/', $fitxer);
        $codi_curs = trim($codi[count($codi) - 2]);

        $existeix = false;
        // Llegeix l'arxiu index.txt on ha d'haver-hi el títol del curs    
        //$linies = file('/dades/wikiform/data/pages/' . $fitxer . ".txt");
        if (substr($fitxer, -4) == ".txt") {
            //$linies = file($conf['datadir'] . $fitxer);
            $linies = file($this->arrel . $fitxer);
        } else {
            //$linies = file($conf['datadir'] . $fitxer . ".txt");
            $linies = file($this->arrel . $fitxer . ".txt");
        }

        $fitxerini = str_replace('index.txt', 'ini.txt', $pagina);
        $fitxerini = $this->arrel . $fitxerini;
        if (file_exists($fitxerini)) {
            $contingut = file($fitxerini);
            foreach ($contingut as $r) {
                $trossejat = explode('|', $r);
                if (trim($trossejat[1]) == 'codi') {
                    $codi = trim($trossejat[2]) . " - ";
                }
                if (trim($trossejat[1]) == 'titol') {
                    $titol0 = trim($trossejat[2]);
                }
            }
            $titol = $codi . $titol0;
        } else {
            // agafem el títol definit a la primera línia
            $ok = 0;
            for ($i = 0; $i < 5; $i++) {
                if (strpos($linies[$i], "<html><!--") !== false) {
                    $inici = strpos($linia, "<html><!--");    // primer
                    $titol = trim(substr($linies[$i], $inici + 10, $inici + strlen($linies[$i]) - 21));
                    break;
                }
            }
        }

        return $titol;
    }

    /**
     * get_titol
     * 
     * @global type $conf
     * @param type $pagina ($NS)
     * @param type $tipus
     * @return type
     * 
     */
    function get_titol($pagina, $tipus = "1") {
        global $conf;

        //$pagina = getNS($pagina);

        $base_inici = trim($this->get_base($_SERVER["REQUEST_URI"]));

        $fitxerindex = str_replace(":", "/", $this->arrel . $base_inici . "/index.txt");
        $fitxerini = str_replace(":", "/", $this->arrel . $base_inici . "/ini.txt");

        // Si exiteix el fitxer ini.txt llegim el títol i codi i els tornem ...
        /* if (file_exists($fitxerini)) {
          $result_ini = $this->get_ini($pagina);

          $codi = '';
          if (trim($result_ini['codi']) != '') {
          $codi = $result_ini['codi'] . " - ";
          }
          $titol = $codi . $result_ini['titol'];

          // si no existeix el fitxer ini.txt, gnerm títol i codi ple mètode "clàssic"
          } else {
          // echo $fitxerindex . "<br>";
          $linies = file($fitxerindex);
          // agafem el títol definit a la primera línia
          $ok = 0;
          for ($i = 0; $i < 5; $i++) {
          if (strpos($linies[$i], "<html><!--") !== false) {
          $inici = strpos($linia, "<html><!--");    // primer
          $busca = array("<html>", "<!--", "</html>", "-->");
          //$titol = trim(substr($linies[$i], $inici + 10, $inici + strlen($linies[$i]) - 21));
          $titol = trim(str_replace($busca, "", $linies[$i]));
          break;
          }
          }
          }
         * 
         */
        // Si exiteix el fitxer ini.txt llegim el títol i codi i els tornem ...
        if (file_exists($fitxerini)) {
            $ini = $this->get_ini($pagina);
            /*
              $codi = '';
              if (trim($ini['codi']) != '') {
              $codi = $ini['codi'] . " - ";
              }
             * 
             */
            if (isset($ini['mostratitol']) and trim($ini['mostratitol']) == "1" and isset($ini['titol']) and ! empty($ini['titol'])) {
                $codi = '';
                if (!empty($ini['codi'])) {
                    $codi = $ini['codi'] . " - ";
                }
                $titol = $codi . $ini['titol'];
            }

            // si no existeix el fitxer ini.txt, gnerm títol i codi ple mètode "clàssic"    
        } else {
            if (file_exists($fitxerindex)) {
                $titol = '';
                $linies = file($fitxerindex);
                // agafem el títol definit a la primera línia
                $ok = 0;
                for ($i = 0; $i < 5; $i++) {
                    if (strpos($linies[$i], "<html><!--") !== false) {
                        $inici = strpos($linia, "<html><!--");    // primer
                        $busca = array("<html>", "<!--", "</html>", "-->");
                        //$titol = trim(substr($linies[$i], $inici + 10, $inici + strlen($linies[$i]) - 21));
                        $titol = trim(str_replace($busca, "", $linies[$i]));

                        break;
                    }
                }
            }
            //  echo "|".$titol."|";
            // si a primera línia hi ha definit <!--nomeu--> sortim sense cap títol
            //if ($titol == 'nomenu')
            //    return 'nomenu';
        }

        return $titol;
    }

    /**
     * get_subtitol
     * 
     * @global type $conf
     * @param type $ID ($ID)
     * @param type $tipus
     * @return type
     * 
     */
    function get_subtitol($ID, $tipus = "1") {
        global $conf;

        $pagina = getNS($ID);
        $prefixlen = strlen($pagina) + 1;
        $first = true;

        $base_inici = $this->get_base($_SERVER["REQUEST_URI"]);

        // Nom de la pàgina
        $pageName = substr($ID, $prefixlen);

        // Nom complet de la pàgina
        if (isset($base_inici)) {
            $fullPageName = substr($ID, strlen($base_inici) + 1);
            if ($pageName == 'index') {
                $len = strlen('index');
                if (strpos($fullPageName, ':') !== false) {
                    $len++;
                }
                $fullPageName = substr($fullPageName, 0, strlen($fullPageName) - $len);
            }
        } else {
            $fullPageName = $pageName;
        }
        
        $data = array();
        $data_moduls = array();
        $data_exer = array();
        $data_comp = array();
        $data_guia = array();
        $data_proj = array();
        $data_gloss = array();
        // afegit per a l'índex quan ha de sortir
        $data_ind = array();
        $data_ref = array(); // Guia ràpida
        // afegit per al cas que es tracti d'un curs curricular
        $data_curri = array();

        // afegit per al cas que es tracti d'un curs curricular
        $data_annex = array();

        foreach ($data as $site) {
            if (strpos($site[id], 'exercici') !== false)
                array_push($data_exer, $site);
            else if (strpos($site[id], 'competenci') !== false)
                array_push($data_comp, $site);
            else if (strpos($site[id], 'index') !== false)
                array_push($data_ind, $site);
            else if (strpos($site[id], 'guia') !== false)
                array_push($data_guia, $site);
            else if (strpos($site[id], 'projecte') !== false)
                array_push($data_proj, $site);
            else if (strpos($site[id], 'g_rapida') !== false)
                array_push($data_ref, $site);
            else if (strpos($site[id], 'annex') !== false)
                array_push($data_annex, $site);
            else if ((strpos($site[id], 'glossari') !== false) or strpos($site[id], 'lectures'))
                array_push($data_gloss, $site);
            else if (strpos($site[id], 'ppartida') !== false or
                    strpos($site[id], 'ppartidae') !== false or
                    strpos($site[id], 'ppartidai') !== false)
                array_push($data_curri, $site);
            else
                array_push($data_moduls, $site);
        }
        /*
         * podem controlar número de pràctiques i mòduls i escurçar-los si volem
         */
        if (strpos(substr($data_moduls[0]['id'], -11), 'modul') !== false) {
            $num_mod = count($data_moduls) + count($data_curri) + count($data_ref) + count($data_gloss) + count($data_annex);
        }
        if (strpos(substr($data_moduls[0]['id'], -11), 'practica') !== false) {
            $num_prac = count($data_moduls);
        }


        $ini = $this->get_ini($ID);

        if (isset($ini['menu'])) {
            $cleanName = trim($this->getCleanNameSub($fullPageName, $num_mod, $num_prac, $ini['menu']));
        } else {
            $cleanName = trim($this->getCleanName($fullPageName, $num_mod, $num_prac));
        }
        if ($cleanName != '')
            $cleanName = ' - ' . $cleanName;



        //       print_r($ini);
        // Si l'array ini té com a mínim un element (està ple)
        if (count($ini) > 0 and trim($ini['mostrasubtitol']) == "1" and trim(substr($cleanName, 2)) != 'ini') {
            
        } else {
            
        }


        /*
          if (isset($ini) and trim($ini['mostrasubtitol']) == "1" and trim(substr($cleanName, 2)) != 'ini') {
          print('<div class="subtitol">' . substr($cleanName, 2) . '</div>');
          // afegit comprovació 'tag' per tal que no es mostri titol a pàgina resum de tags
          } else if (!isset($ini) and $titolcurs['nomenu'] != 1 and $base_inici != "tag" and trim(substr($cleanName, 2)) != 'ini') {
          print('<div class="subtitol">' . substr($cleanName, 2) . '</div>');
          //  print('<div class="subtitol">'.str_replace('.html', '', substr($cleanName,2)).'</div>');
          }
         * 
         * 
         */

        return substr($cleanName, 2);
    }

    /*
     *  Funció indexArray
     * 
     *  Genera array amb amb els fitxers index principals
     *  Torna array 
     *  
     *  @param $directory string
     *  @param $pattern int
     *  @recursive 
     *
     *  @author Jordi Fons
     * 
     */

    function indexArray($directory = '', $pattern = 'index.txt', $recursive = true) {
        global $conf;
        //print_r($conf);

        if ($directory == '') {
            $directory = $this->arrel;
        }
        $array_items = array();

        if ($handle = opendir($directory)) {

            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    if (is_dir($directory . "/" . $file)) {

                        if ($recursive) {
                            if (substr($file, 0, 4) != ".tmp" and substr($file, 0, 2) != "z_") {
                                $array_items = array_merge($array_items, $this->indexArray($directory . "/" . $file, $pattern, $recursive));
                            }
                        }
                    } else { // No és un directori
                        // mirar si s'inclou a la llista
                        $newdir = str_replace("//", "/", $directory) . '/';
                        $subdirs = $this->_hasSubdirs($newdir);
                        $principal = $this->esPrincipal(str_replace("//", "/", $directory));
                        $ext = substr(strtolower($file), -strlen($pattern));

                        // si no és un directori && és index.txt && té subdirectoris && no és fase_3 (fic_orientacions) ...                         
                        if (!is_dir($file) && ($ext == $pattern) && $subdirs && $principal && strpos($directory, "fase_3") === false) {
                            $file = $directory . "/" . $file;
                            $array_items[] = preg_replace("/\/\//si", "/", $file);
                        }
                    }
                }
            }
            closedir($handle);
        }
        foreach ($array_items as &$item) {
            $item = str_replace($this->arrel, "", $item);
        }
        $this->_deleteFromArray($array_items, $pattern, $useOldKeys = FALSE);
        $result = natsort($array_items);

        return $array_items;
    }

    /*
     * Funció: _has_subdirs
     * Verificar si un directori donat conté subdirectoris
     * 
     * @param $path string
     * 
     * Retorna true si el camí indicat ($path) conté altres directoris
     */

    function _hasSubdirs($path) {
        if (is_dir($path)) {
            if ($dh = opendir($path)) {
                while (($file = readdir($dh)) !== false) {
                    if (is_dir($path . $file) && $file != '.' && $file != '..') {
                        closedir($dh);
                        return true;
                    }
                }
                closedir($dh);
            }
        }
        return false;
    }

    function _deleteFromArray(&$array, $deleteIt, $useOldKeys = FALSE) {
        $key = array_search($deleteIt, $array, TRUE);
        if ($key === FALSE)
            return FALSE;
        unset($array[$key]);
        if (!$useOldKeys)
            $array = array_values($array);
        return TRUE;
    }

    /*
     *  Funció llegeixIndexCurs()
     * 
     * Llegeix el contingut del fitxer index.txt principal
     * Recupera títols del curs i de cada mòdul
     * Recupera també dataversio i tags
     * 
     * @param $fitxer string
     *    
     * @author Jordi Fons
     */

    function llegeixIndexCurs($fitxer) {

        if (!file_exists($fitxer))
            return;

        $codi = explode('/', $fitxer);
        $codi_curs = trim($codi[count($codi) - 2]);

        $existeix = false;
        // Llegeix l'arxiu index.txt on hi ha d'haver el títol del curs    
        $linies = file($fitxer);

        $info = array();
        foreach ($linies as $linia) {
            // Mirem el títol del curs
            $inici = strpos($linia, "<html><!--");    // primer
            $fi = strrpos($linia, "-->");

            if ($fi > 0) { //Hi ha títol
                // Mirar si el codi del curs coincideix amb el directori
                $info[] = substr($linia, $inici + 10, $fi - 10);
                $aux = explode("-", substr($linia, $inici + 10, $fi - 10));

                if (strtoupper($codi_curs) == strtoupper(trim($aux[0])))
                    $existeix = true;
                else
                    $no_concorda = true;
            }

            // Mirem títols de cada mòdul 
            if ((strpos($linia, "modul") > 0) || (strpos($linia, "mòdul") > 0)) {
                $inici = strpos($linia, "|");
                $fi = strrpos($linia, "]]") - 1;

                if ($inici > 0) {
                    $info[] = substr($linia, $inici + 1, $fi - $inici);
                }
            }

            // Mirem data versió
            if (strpos($linia, "Descàrrega del curs") > 0 and substr($linia, 0, 6) != '<html>') {
                preg_match('/\((.+)\)/', $linia, $coincidencies);
                $info['dataversio'] = $coincidencies[0];
            } elseif (preg_match("/\s([0-9]{4})\s/", $linia, $coincidencies2) and ( strpos($linia, "2003") == 0)) {
                $info['dataversio'] .= " / " . rtrim($linia);
            }

            if (substr($linia, 0, 6) == "{{tag>") {
                $tags = rtrim(substr($linia, 6));
                $info['tags'] = substr($tags, 0, -2);
            }
        }

        /*    if (!$existeix) {
          if ($no_concorda)
          $fitxer = "DISCORDANÇA: " . $fitxer;
          else
          $fitxer = "SENSE TÍTOL: " . $fitxer;
          write_error('errors.log', "\n" . $fitxer, 'a');
          }
         * 
         */
        return $info;
    }

    /**
     * esPrincipal
     * Comprova si el directori és el directori principal d'un curs o material
     * 
     * @global type $conf
     * @param type $url
     * @return int
     */
    function esPrincipal($url) {
        global $conf;


        if (strpos($url, ':') !== false) {
            $separador = ":";
        } else {
            $separador = "/";
        }

        $items = explode("$separador", $url);
        //echo $url.": " . $items[count($items) - 2]." --> ";
        // llegim  array amb els directoris "contenidors"
        $directoris = $conf['directoris'];

        if (in_array($items[count($items) - 2], $directoris)) {
            $principal = 1;
        } else {
            $principal = 0;
        }
        //echo $principal."<br>";
        return $principal;
    }

    /**
     * get_menu
     * 
     * Genera un menú automatitzat per a template wikiexport
     * 
     * Si no hi ha definit fitxer ini a l'arrel del curs o material,
     * genrea el menú "clàssic"
     * 
     * @global type $conf
     * @param type $data
     * @return int
     */
    function get_menu($ID, $idnav = 'navcontainer') {
        global $conf;
        //   echo "pagina--> " . $ID . "<br>";
        //     echo "\$ID ".$ID."<br>";

        $pagina = getNS($ID);
        // echo $pagina;
        $prefixlen = strlen($pagina) + 1;
        $first = true;

        $base_inici = $this->get_base($_SERVER["REQUEST_URI"]);

        // Nom de la pàgina
        $pageName = substr($ID, $prefixlen);

        $NS = cleanID($pagina);
        $namespace = utf8_encodeFN(str_replace(':', '/', $NS));

        $inBase = $NS == $base_inici;

        /*
         * es crea array amb les pàgines del NS
         */
        $opts = array('depth' => '4', 'skipacl' => true);
        $data = array();
        require_once (DOKU_INC . 'inc/search.php');
        search($data, $conf['datadir'], 'search_index', $opts, $namespace);

        $ini = $this->get_ini($ID);
        // Si l'array ini té com a mínim un element (està ple)
        if (count($ini) > 0) {
            // Si està definit $ini i $ini['menu'], preparem per omplir array $data
            if (isset($ini['menu'])) {
                $datanou = array();

                foreach ($ini['menu'] as $clau => $menu) {
                    if (substr($clau, -1) == '*') {
                        foreach ($data as $site) {
                            $iden = substr($site['id'], $prefixlen);
                            if ($this->treu_numero($iden) == substr($clau, 0, strlen($clau) - 1)) {
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
            $data = $datanou;
        } else {
            /*
             * Ordenem els items: Guia - Competències - Mòduls - Exercicis - Projecte
             */
            $data_moduls = array();
            $data_exer = array();
            $data_comp = array();
            $data_guia = array();
            $data_proj = array();
            $data_gloss = array();
            // afegit per a l'índex quan ha de sortir
            $data_ind = array();
            $data_ref = array(); // Guia ràpida
            // afegit per al cas que es tracti d'un curs curricular
            $data_curri = array();

            // afegit per al cas que es tracti d'un curs curricular
            $data_annex = array();

            foreach ($data as $site) {
                if (strpos($site[id], 'exercici') !== false)
                    array_push($data_exer, $site);
                else if (strpos($site[id], 'competenci') !== false)
                    array_push($data_comp, $site);
                else if (strpos($site[id], 'index') !== false)
                    array_push($data_ind, $site);
                else if (strpos($site[id], 'guia') !== false)
                    array_push($data_guia, $site);
                else if (strpos($site[id], 'projecte') !== false)
                    array_push($data_proj, $site);
                else if (strpos($site[id], 'g_rapida') !== false)
                    array_push($data_ref, $site);
                else if (strpos($site[id], 'annex') !== false)
                    array_push($data_annex, $site);
                else if ((strpos($site[id], 'glossari') !== false) or strpos($site[id], 'lectures'))
                    array_push($data_gloss, $site);
                else if (strpos($site[id], 'ppartida') !== false or
                        strpos($site[id], 'ppartidae') !== false or
                        strpos($site[id], 'ppartidai') !== false)
                    array_push($data_curri, $site);
                else
                    array_push($data_moduls, $site);
            }

            /*
             * podem controlar número de pràctiques i mòduls i escurçar-los si volem
             */
            if (strpos(substr($data_moduls[0]['id'], -11), 'modul') !== false) {
                $num_mod = count($data_moduls) + count($data_curri) + count($data_ref) + count($data_gloss) + count($data_annex);
            }

            if (strpos(substr($data_moduls[0]['id'], -11), 'practica') !== false) {
                $num_prac = count($data_moduls);
            }

            /*
             * ordenem arrays parcials segons es vulgui i el desem en array final
             */
            $data = array_merge($data_ind, $data_guia, $data_proj, $data_curri, $data_comp, $data_moduls, $data_exer, $data_ref, $data_gloss, $data_annex);
        }

        // comprovem si el títol = nomenu -> no s'ha demostrar el menú ni el títol
        if ($this->get_titol($ID) == 'nomenu')
            $nomenu = 1;

        if (trim($base_inici) == "index"
                or trim($base_inici) == ""
                or trim($base_inici) == "cursos:index"
                or trim($base_inici) == "cursos:presentacio"
                or in_array($abase_ini[count($abase_ini) - 1], $conf['directoris'])) {
            $nomenu = 1;
        }


        /*
          Inici generació menú
         */
       // $menu = '<div id="' . $idnav . '">';
       // $menu .= '<ul id="navlist">';

        if (!$inBase and $nomenu != 1) {
            $menu .= '<li><a href="' . $this->getFullLink("../index") . '" title="Inici">&lt;&lt;</a></li>';
            $first = false;
        }

        foreach ($data as $site) {

            $link = substr($site['id'], $prefixlen);
            $link = str_replace(':', '/', $link);
            $name = $link;

            if ($nomenu == 1) {
                continue;
            }

            global $conf;
            $amaga = $conf['amaga'];

            if (in_array($name, $amaga)) {
                continue;
            }

            if ($name == "index" && $pageName == $name)
                continue;

            if ($site['type'] == 'd')
                $link.='/index';

            if (!$first)
                print('  ');
            else
                $first = false;

            if ($site['id'] == $ID) {
                if (isset($ini) and trim($ini['mostramenu']) == "1") {
                    $menu .= '<li id="active"><a id="current" href="#">' . $this->getCleanName2($name, $num_mod, $num_prac, $ini['menu']) . '</a></li>';
                } elseif (!isset($ini)) {
                    $menu .= '<li id="active"><a id="current" href="#">' . $this->getCleanName($name, $num_mod, $num_prac) . '</a></li>';
                }
            } else {
                if (isset($ini) and trim($ini['mostramenu']) == "1") {
                    $menu .= '<li><a href="' . $this->getFullLink($link) . $ext . '">' . $this->getCleanName2($name, $num_mod, $num_prac, $ini['menu']) . '</a></li>';
                } elseif (!isset($ini)) {
                    $menu .= '<li><a href="' . $this->getFullLink($link) . $ext . '">' . $this->getCleanName($name, $num_mod, $num_prac) . '</a></li>';
                }
            }
        }

       // $menu .= '</ul>';
       // $menu .= '</div>';
        /*
         * fi menú
         */


        return $menu;
    }

    function getFullLink($link) {
        global $conf;
        $base_inici = $GLOBALS['base_curs'];

        $link = $link;
        if ($conf['template.follow']) {
            $link .= strpos($lnk, '?') === false ? "?" : "&";
            $link.="tpl=" . $conf['template'];

            if (isset($base_inici))
                $link.="&docbase=" . $base_inici;
            if (isset($conf['template.title']))
                $link.="&title=" . $conf['template.title'];
        }
        return $link;
    }

    /**
     *  Funció get_base()
     * 
     * Recupera camí base del curs
     * Substitueix el que es feia amb 'prepara.php' pel que fa al camí base del curs sol·licitat
     * No cal cap crida ni a un fitxer extern ni cal cap procés previ
     * Això permetrà accés simultani al servidor des de diferents cursos
     * 
     */
    function get_base($url) {
        global $conf;

        if ($url != '/wikiform/wikiexport/') {
            $curs = $this->get_curs($url);

            // Recuperem base inici a la variable $cami_curs 
            $cami_curs = str_replace("/wikiform/wikiexport/", " ", $url);
            $cami_curs = substr($cami_curs, 0, strpos($cami_curs, $curs) + strlen($curs));
            $cami_curs = str_replace("/", ":", $cami_curs);

            // Definim el camí com a global
            $GLOBALS['base_curs'] = trim($cami_curs);
        }
        return trim($cami_curs);
    }

    /**
     * Funció per a la generació del nom per als subtítols als menús custom
     * 
     * 
     */
    function getCleanNameSub($name, $num_mod, $num_prac, $menuini) {
        $name = str_replace(':', ' - ', $name);

        // Si seriat,traiem el número d'ordre
        $elements = explode(" - ", $name);

        foreach ($elements as $e) {
            if ($this->seriat($e)) {
                $name = $this->treu_numero($e);
            }
            if (array_key_exists($e, $menuini)) {
                $name = str_replace($e, $menuini[$e], $e);
            } elseif (array_key_exists($name . '*', $menuini)) {
                $name = str_replace($name, $menuini[$name . '*'], $e);
                $name = str_replace('_', ' ', $name);
            }
            $nom .= $name . " - ";
        }
        $name = substr($nom, 0, -3);

        return $name;
    }

    function getCleanName($name, $num_mod, $num_prac) {
        global $conf;

        $menuops = $conf['menus'];

        $name = str_replace('_', ' ', $name);
        $name = str_replace(':', ' - ', $name);

        if ($num_prac > 9) {
            $name = str_replace('practica', 'Pr&agrave;c.', $name);
        } else {
            $name = str_replace('practica', 'Pr&agrave;ctica', $name);
        }

        if ($num_mod > 9) {
            $name = str_replace('modul', 'M&ograve;d.', $name);
        } else {
            $name = str_replace('modul', 'M&ograve;dul', $name);
        }

        if (array_key_exists($name, $menuops)) {
            $name = str_replace($name, $menuops[$name], $name);
            //$name = $menuops[$name];  
        } else {
            $name = ucfirst($name);
        }

        $elements = explode(" - ", $name);
        foreach ($elements as $e)
            $nom .= ucfirst($e) . " - ";

        $name = substr($nom, 0, -3);

        return $name;
    }

    /**
     * Funció per a la generació del nom en menús custom
     * 
     * 
     */
    function getCleanName2($name, $num_mod, $num_prac, $menuini) {
        // global $conf;
        // Si seriat,traiem el número d'ordre
        if ($this->seriat($name)) {
            $nom = $name;
            $name = $this->treu_numero($name);
        }

        if (array_key_exists($name, $menuini)) {
            $name = str_replace($name, $menuini[$name], $name);
        } elseif (array_key_exists($name . '*', $menuini)) {
            $name = str_replace($name, $menuini[$name . '*'], $nom);
            $name = str_replace('_', ' ', $name);
        } else {
            return;
        }
        return $name;
    }

    /**
     *  Funció get_curs()
     *  Recupera el curs a exportar
     *  Passem la url i ens torna el curs a exportar
     * 
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

    /**
     * Funció get_ini()
     * Recuperem definicions inicials per al curs des de ini.txt
     *
     * @global array $conf
     * @param str $fileini ($ID)
     * @param int $tipus
     * @return array
     *  
     */
    function get_ini($fileini) {
        global $conf;

        $noufile = str_replace(":", "/", $fileini);
        $base = $this->get_base($noufile);
        $fileini = str_replace(":", "/", $this->arrel . trim($base) . "/ini.txt");

        if (file_exists($fileini)) {
            $torna = array();
            $linies = file($fileini);

            foreach ($linies as $linia) {
                $trans = explode('|', $linia);
                if (trim($trans[3]) == '') {
                    $torna[trim($trans[1])] = $trans[2];
                } else {
                    $torna['menu'][trim($trans[2])] = trim($trans[3]);
                }
            }
        } else {
            return;
        }

        return $torna;
    }

    /**
     *  Funció treu_numero()
     * 
     *  Treu el número d'un element seriat
     */
    function treu_numero($nom) {
        $tros = explode("_", $nom);

        /* $seriat = false;
          if (is_numeric($tros[count($tros) - 1])) {
          $seriat = true;
          }
         * 
         */
        $seriat = $this->seriat($nom);

        //$tros = explode("_", $nom);
        if ($seriat) {
            for ($i = 0; $i < count($tros) - 1; $i++)
                $torna .= $tros[$i];
        }

        return $torna;
    }

    /**
     *  Funció seriat()
     * 
     * Torna si l'element forma part d'una sèrie (s'acaba en número: ex. modul_1, modul_2, etc)
     */
    function seriat($nom) {
        $tros = explode("_", $nom);

        $seriat = false;
        if (is_numeric($tros[count($tros) - 1])) {
            $seriat = true;
        }

        return $seriat;
    }

}

// vim:ts=4:sw=4:et:












    