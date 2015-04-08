<?php

///////////////////////////////////////////////////////////////////////////////
// Funció getcursosmarcats()
// Recupera  un array amb 3 subarrays corresponents als cursos: inactius, novetat i en eleboració
// Torna un array amb:
//  [inactius] = array amb codis de cursos inactius
//  [novetat] = array amb codis de cursos que són novetat
//  [elaboracio] = array amb codis de cursos que estan en elaboració
//  [actualitzats] = array amb codis de cursos que s'han actulizat de fa poc
// Paràmetres: $fitxer = str (path i nom del fitxer que conté la informació)
function getcursosmarcats($fitxer) {

    if (!file_exists($fitxer))
        return;

    $linies = file($fitxer);
    $torna = array();

    foreach ($linies as $linia) {
        if (strpos($linia, 'inactius') !== false) {
            $marca = "inactius";
        } elseif (strpos($linia, 'novetat') !== false) {
            $marca = "novetat";
        } elseif (strpos($linia, 'elaboracio') !== false) {
            $marca = "elaboracio";
        } elseif (strpos($linia, 'actualitzats') !== false) {
            $marca = "actualitzats";
        }

        if (strpos($linia, '$') !== false) {
            $codis = explode("$", $linia);
            foreach ($codis as $codi) {
                $torna[$marca][] = trim($codi);
            }
        }
    }

    return $torna;
}

///////////////////////////////////////////////////////////////////////////////
// Funció escriucursosmarcats()
// Escriu fitxer amb cursos marcats, a partir 
// Escriu array amb subarrays quwe contenen els codis:
//  [inactius] = array amb codis de cursos inactius
//  [novetat] = array amb codis de cursos que són novetat
//  [elaboracio] = array amb codis de cursos que estan en elaboració
//  [actualitzats] = array amb codis de cursos que s'han actulizat de fa poc
// Paràmetres: $fitxer = str (path i nom del fitxer que conté la informació)
function escriucursosmarcats($fitxer) {
    /*
      if (!file_exists($fitxer))
      return;

      $linies = file($fitxer);
      $torna = array();

      foreach($linies as $linia) {
      if(strpos($linia, 'inactius')!==false){
      $marca = "inactius";
      }elseif(strpos($linia, 'novetat')!==false){
      $marca = "novetat";
      }elseif(strpos($linia, 'elaboracio')!==false){
      $marca = "elaboracio";
      }elseif(strpos($linia, 'actualitzats')!==false){
      $marca = "actualitzats";
      }

      if (strpos($linia, '$')!==false){
      $codis = explode ("$",$linia);
      foreach($codis as $codi){
      $torna[$marca][] = trim($codi);
      }
      }
      }

      return $torna; */
}

///////////////////////////////////////////////////////////////////////////////
// Funció llegeixtitols()
// Recupera títols del curs i de cada mòdul
// Torna un array amb [0] = títol del curs i, la resta, títols de mòduls
// Paràmetres: $fitxer = str (path i nom del fitxer)
// Jordi
function llegeixtitols($fitxer) {

    if (!file_exists($fitxer))
        return;

    $codi = explode('/', $fitxer);
    $codi_curs = trim($codi[count($codi) - 2]);

    $existeix = false;
    // Llegeix l'arxiu index.txt on ha d'haver el títol del curs    
    $linies = file($fitxer);

    $titols = array();
    foreach ($linies as $linia) {
        // echo $linia;
        // Mirem el títol del curs
        $inici = strpos($linia, "<html><!--");    // primer
        $fi = strrpos($linia, "-->");

        if ($fi > 0) { //Hi ha títol
            // Mirar si el codi del curs coincideix amb el directori
            $titols[] = substr($linia, $inici + 10, $fi - 10);
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
                $titols[] = substr($linia, $inici + 1, $fi - $inici);
            }
        }

        // Mirem data versió
        if (strpos($linia, "Descàrrega del curs") > 0 and substr($linia, 0, 6) != '<html>') {
            preg_match('/\((.+)\)/', $linia, $coincidencies);
            $titols['dataversio'] = $coincidencies[0];
        } elseif (preg_match("/\s([0-9]{4})\s/", $linia, $coincidencies2) and (strpos($linia, "2003") == 0)) {
            $titols['dataversio'] .= " / " . rtrim($linia);
        }

        if (substr($linia, 0, 6) == "{{tag>") {
            $tags = rtrim(substr($linia, 6));
            $titols['tags'] = substr($tags, 0, -2);
        }
    }

    if (!$existeix) {
        if ($no_concorda)
            $fitxer = "DISCORDANÇA: " . $fitxer;
        else
            $fitxer = "SENSE TÍTOL: " . $fitxer;
        write_error('errors.log', "\n" . $fitxer, 'a');
    }
    return $titols;
}

/*
 * function index_array: 
 * Recorregut d'un arbre de directoris buscant arxius que coincidexin parcialment o total amb el paràmetre pattern
 * @ $directory 
 * @ $pattern: nom de l'arxiu o extensió a incloure a la llista
 */

function index_array($directory = "../../../data/pages/", $pattern = 'index.txt', $recursive = true) {

    $array_items = array();
    if ($handle = opendir($directory)) {

        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                if (is_dir($directory . "/" . $file)) {

                    if ($recursive) {
                        if (substr($file, 0, 4) != ".tmp" and substr($file, 0, 2) != "z_") {
                            $array_items = array_merge($array_items, index_array($directory . "/" . $file, $pattern, $recursive));
                        }
                    }
// No és un directori
                } else {
// mirar si s'inclou a la llista
                    $newdir = str_replace("//", "/", $directory) . '/';
                    $subdirs = has_subdirs($newdir);
                    $ext = substr(strtolower($file), -strlen($pattern));
                    if (!is_dir($file) && ($ext == $pattern) && $subdirs) {
                        $file = $directory . "/" . $file;
                        $array_items[] = preg_replace("/\/\//si", "/", $file);
                    }
                }
            }
        }
        closedir($handle);
    }
    foreach ($array_items as &$item) {
        $item = str_replace("../../../data/pages/", "", $item);
    }
    deleteFromArray($array_items, $pattern, $useOldKeys = FALSE);
    $result = natsort($array_items);
    return $array_items;
}

/* write_error: s'utilitza per recollir els cursos que no disposen de t�tol
 * mode: 'w' obre el fitxer buit i per escriptura 
 *       'a' afegeix al final del fitxer. Si no existeix el crea.
 */

function write_error($filename = 'errors.log', $stringData, $mode = 'w') {
    $fh = fopen($filename, $mode) or die("No es pot crear/obrir l\'arxiu: " . $filename);
    fwrite($fh, $stringData);
    fclose($fh);
}

/*
 * Funci�: has_subdirs
 * Verificar si un directori donat cont� subdirectoris
 * $path : el directori a comprovar.
 * 
 * Retorna true si el cam� indicat ($path) cont� altres directoris
 */

function has_subdirs($path) {
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

function deleteFromArray(&$array, $deleteIt, $useOldKeys = FALSE) {
    $key = array_search($deleteIt, $array, TRUE);
    if ($key === FALSE)
        return FALSE;
    unset($array[$key]);
    if (!$useOldKeys)
        $array = array_values($array);
    return TRUE;
}

/*
 * Busca usuaris de la wiki
 * 
 * Torna un array que conté un array per a cada  usuari, amb dades extretes del fitxer users.auth.php
 * 
 */

function usuaris() {

    $users = file("/dades/wikiform/ewikiform/conf/users.auth.php");
    $usuaris = array();

    for ($i = 0; $i < 10; $i++) {
        unset($users[$i]);
    }
    foreach ($users as $user) {
        $usuaris[] = explode(":", $user);
    }

    return $usuaris;
}

/*
 * Busca permisos de la wiki
 * 
 * Torna un array  que conté una array per a cada permís, extrets del fitxer acl.auth.php
 * 
 */

function permisos() {

    $acl = file("/dades/wikiform/ewikiform/conf/acl.auth.php");

    for ($i = 0; $i < 8; $i++) {
        unset($acl[$i]);
    }

    $permisos = array();
    foreach ($acl as $a) {
        $permisos[] = explode("	", $a); //alerta! caràcter de tabulació a l'explode
    }
    return $permisos;
}

/*
 * Busca permisos per a un camí passat
 * 
 * Torna un array  que conté una array per a cada permís, extrets del fitxer acl.auth.php
 * 
 */

function tepermis($grup, $cami) {
    $permisos = permisos();

    $grup = '@' . $grup;
    $cami = $cami . "*";
    //mirem si el grup es troba en algun lloc de l'array de permisos
    $torna = '';


    if (in_array_r($grup, $permisos)) {
        // si true, recorrem l'array de permisos
        foreach ($permisos as $permis) {
            if ($cami == $permis[0]) {
                $torna[] = array('path' => $permis[0], 'grup' => $permis[1], 'permis' => $permis[2]);
            }
        }
    }

    return $torna;
}

function usuaris_grup($grup) {
    $usuaris = usuaris();

    $torna = false;
    foreach ($usuaris as $usuari) {
        // Amb rtrim traiem el salt de línia final per tal que el darrer element del'array es construeixi bé 
        $grups_user = explode(",", rtrim($usuari[4]));
        if (in_array($grup, $grups_user, true)) {
            $torna[] = array('grup' => $grup, 'usuari' => $usuari[0], 'nom' => $usuari[2], 'email' => $usuari[3], 'grups' => $usuari['4']);
        }
    }

    return $torna;
}

/*
 * Equivalent per a in_array per a un array multidimensional
 * 
 */

function in_array_r($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
}

function exporta($fullcalcul) {
    //flush;
    header("Content-Description: File Transfer");
    header("Content-Type: application/force-download");
    header("Content-Disposition: attachment; filename=permisoswiki-" . date("Ymd") . ".csv");
    //echo $fullcalcul;
    return $fullcalcul;
}

function nivellpermis($nivell) {

    $nivells = array(
        '0' => 'Denegat',
        '1' => 'Lectura',
        '2' => 'Edició',
        '4' => 'Creació',
        '8' => 'Penjar fitxers',
        '16' => 'Suprimir');

    return $nivells["$nivell"];
}

/*
 *   Funció get_curs()
 * 
 *   Recupera el curs a exportar
 *   Passem la url i ens torna el curs a exportar
 *   Jordi
 * 
 */

function get_curs($url) {
    //global $conf;
    $conf['directoris'] = array("llai", "cma", "cco", "cci", "prl", "epa", "tav", "tdi", "tax", "fic_orientacio", "fic", "lle", "bib", "ose", "jornades", "ace", "tac", "eso_btx", "inf_pri", "interniv", "actic", "tallers", "tic", "dirs", "tutorials", "escola_inclusiva", "equips_directius", "gestio_centres", "gestio", "cmd", "pas", "curriculum", "biblioteques", "altres_materials", "autoria", "cursos", "formgest", "materials", "z_gestio", "z_test", "z_test_public", "wikiexport");
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

?>
