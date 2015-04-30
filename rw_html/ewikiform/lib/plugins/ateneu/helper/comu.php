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

class helper_plugin_ateneu_comu extends DokuWiki_Plugin {

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

    function getCurs($url, $tipus = 1) {
        global $conf;
        $separador = ":";
        if ($tipus == 2)
            $separador = "/";
        $items = explode("$separador", $url);

        $fet = 0;
        // llegim  array amb els directoris "contenidors"
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

        $fitxer = $pagina;
        if ($tipus == 1)
            $fitxer = str_replace(":", "/", $pagina);

        $codi = explode('/', $fitxer);
        $codi_curs = trim($codi[count($codi) - 2]);

        $existeix = false;
        // Llegeix l'arxiu index.txt on ha d'haver el títol del curs    
        //$linies = file('/dades/wikiform/data/pages/' . $fitxer . ".txt");
        $linies = file($conf['datadir'] . $fitxer . ".txt");

        $titols = array();
        foreach ($linies as $linia) {
            // Mirem el títol del curs
            $inici = strpos($linia, "<html><!--");    // primer
            $fi = strrpos($linia, "-->");

            if ($fi > 0) { //Hi ha títol
                $titol = trim(substr($linia, $inici + 10, $fi - 10));
            }
        }
        return $titol;
    }

}

// vim:ts=4:sw=4:et:

    