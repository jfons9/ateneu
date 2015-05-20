<?php

/**
 * DokuWiki Plugin ateneu (Helper Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Jordi Fons <jfons@xtec.cat>
 */
// must be run within Dokuwiki
if (!defined('DOKU_INC'))
    die();

class helper_plugin_ateneu extends DokuWiki_Plugin {
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

    /**
     *  getCurs
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

    /**
     *  getTitolCurs
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

        if (strpos($url, ':') == false) {
            $separador = ":";
            $fitxer = str_replace(":", "/", $pagina);
        }

        $codi = explode('/', $fitxer);
        $codi_curs = trim($codi[count($codi) - 2]);

        $existeix = false;
        // Llegeix l'arxiu index.txt on ha d'haver-hi el títol del curs    
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
     *  indexArray
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

    /**
     * _has_subdirs
     * 
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

    /**
     * llegeixIndexCurs()
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
        
        // llegim  array amb els directoris "contenidors"
        $directoris = $conf['directoris'];

        if (in_array($items[count($items) - 2], $directoris)) {
            $principal = 1;
        } else {
            $principal = 0;
        }

        return $principal;
    }

}






    
