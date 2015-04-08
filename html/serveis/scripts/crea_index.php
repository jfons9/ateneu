<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ca"
      lang="ca" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>
            Creació índex de cursos de formació
        </title>
    </head>

    <pre>

        <?php
// INFORMACIONS SOBRE L'ÚS DE LA VARIABLE $tot
// $tot = 1 permet de generar un fitxer amb TOTS els cursos, estiguin inactius o no
// $tot = 0 permet de generar un fitxer NOMÉS amb els cursos que  volem que apareguin a la portada   
// per defecte $tot = 0
// Podem passar-li el paràmetre des del formulari gestio_cursos.php (via GET) o bé des de l'adreça (via POST)
// Per tal que ens accepti toes dues vies, rebem la variable amb REQUEST
// Cal fer córrer el procés amb tot=1 quan vulguem que es torni a generar el fitxer base 'index_gestio_cursos' 

        if (isset($_REQUEST['tot'])) {
            $tot = $_REQUEST['tot'];
        } else {
            $tot = 0;
        }


//// TRANSPORTAT DES DE GESTIO_CURSOS
        // Comprovem que venim del formulari mitjançant la variable oculta  'set'
        // si no venim del formulari no toquem el contingut de 'infocursos.txt' 
        // en cas contrari esborraríem el contingut del fitxer
        if ($_POST['set'] == "yes") {
            $cami = "/dades/wikiform/data/pages/z_gestio/aux/";
            $fitxer_cursos = "infocursos.txt";
            $fitxer_marcats = $cami . $fitxer_cursos;

            // Creem array amb les etiquetes de cada opció: etiqueta programa|etiqueta array 
            $marques = array("actiu|inactius", "nou|novetat", "elaboracio|elaboracio", "actualitzat|actualitzats");
            $cursos_marcats = array();
            $desti = array();

            foreach ($marques as $marca) {
                $separa = explode("|", $marca);
                $desti[] = "[" . $separa[1] . "] \n\n";
                $torna = "";
                //echo $separa[1].": <br>";
                for ($i = 0; $i < count($_POST[$separa[0]]); $i++) {
                    $torna .= $_POST[$separa[0]][$i] . "$";
                    $cursos_marcats[$separa[1]][] = $_POST[$separa[0]][$i];
                    if (in_array($_POST[$separa[0]][$i], $cursos_marcats['inactius']) and in_array($_POST[$separa[0]][$i], $cursos_marcats['novetat'])) {
                        $alerta .= "<br />ALERTA: Hi pot haver alguna incongruència(Inactiu/Novetat) Curs: " . $_POST[$separa[0]][$i];
                    }
                    if (in_array($_POST[$separa[0]][$i], $cursos_marcats['actualitzats']) and in_array($_POST[$separa[0]][$i], $cursos_marcats['novetat'])) {
                        $alerta .= "<br />ALERTA: Hi pot haver alguna incongruència(Nou/Actualitzat) Curs: " . $_POST[$separa[0]][$i];
                    }
                }

                $desti[] = strtolower($torna);
                $desti[] = "\n\n\n";
            }

            copy($fitxer_marcats, $fitxer_marcats . ".bak");

            if (file_put_contents($cami . "temp.txt", $desti) === false) {
                echo "No s'ha pogut crear el fitxer temporal";
            } else {
                unlink($fitxer_marcats);
                rename($cami . "temp.txt", $fitxer_marcats);

                //  echo "<script languaje='javascript'> alert('Canvis desats'".$alerta."); </script>";
                //	echo "<script languaje='javascript'> alert('Canvis desats ".$alerta."'); </script>";
                //  echo "Les dades s'han desat correctament a: '$fitxer_marcats' <br /><br />";
            }
        }
/// FI TRANSPORTAT	

        echo '<a href="/wikiform/wikiform/z_gestio/gestio_cursos">Torna a gestió de cursos (Wiki)</a>';
        echo '  |  <a href="/wikiform/serveis/scripts/crea_index.php?tot=1" title="Cal fer-ho només quan haguem afegit cursos nous">Refés llista base de cursos</a>';
        echo '  |  <a href="/wikiform/serveis/scripts/gestio_cursos.php">Gestió de cursos</a>';
        echo '  |  <a href="/wikiform/wikiexport/cursos/index?purge=true">Purga index wikiexport</a>';
        echo '  |  <a href="/wikiform/wikiform/cursos/index?purge=true">Purga index wikiform</a>';


        if ($tot == 1) {
            $afegit = "(inclou TOTS els cursos, inclosos inactius)";
        } else {
            $afegit = "(destinat a pàgina inicial)";
        }

        echo "<h2>Creació índex de cursos de formació " . $afegit . " </h2>";


        /*
         * Generació automàtica de l'índex de cursos 
         * 
         * 
         */

        /*
          // Inici per a depuració: recuperació de missatges d'error
          ini_set('display_errors', 1);
          ini_set('log_errors', 1);
          ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
          //error_reporting(E_ALL);
          error_reporting(E_ALL & ~E_NOTICE);
         */
        include_once("funcions.php");

        // buida l'arxiu d'errors
        write_error('errors.log', "", 'w');

        $fitxer_marcats = "/dades/wikiform/data/pages/z_gestio/aux/infocursos.txt";
        $marcats = getcursosmarcats($fitxer_marcats);
        $novetat = $marcats['novetat'];
        $inactiu = $marcats['inactius'];
        $elaboracio = $marcats['elaboracio'];
        $actualitzat = $marcats['actualitzats'];

        $fitxer = "/dades/wikiform/data/pages/z_gestio/aux/base_index_cursos.txt";
        $afitxer = file($fitxer);

        $fitxerCursosUnitats = "/dades/wikiform/data/pages/z_gestio/aux/base_cursos_unitats.txt";
        $aunitats = file($fitxerCursosUnitats);
        $aNomUnitats = file($fitxerUnitats);
        $unitats = array();
        foreach ($aunitats as $lin) {
            $tros = explode("|", $lin);
            $unitats[trim($tros[1])] = trim($tros[2]);
        }
        array_shift($unitats);

        $fitxerUnitats = "/dades/wikiform/data/pages/z_gestio/aux/base_unitats.txt";
        $nomUnitats = array();
        foreach ($aNomUnitats as $nom) {
            $tros = explode("|", $nom);
            $nomUnitats[trim($tros[1])] = trim($tros[2]);
        }
        array_unshift($nomUnitats);

        //  $acl = permisos();
        //  $users = usuaris();

        echo "Llegint carpetes... <br /><br />";

        if (isset($_GET["dir"])) {
            $directory = $_GET["dir"];
            $f = index_array($directory);
        } else {
            $f = index_array();
        }
        $resultat = array();
        $tots = array();

        echo "S'ha iniciat el procés <br />";

        $fullcalcul = "Codi;Curs;Unitat;Grup-permís;Usuaris grup;Versions" . chr(13) . chr(10);

        // afegim un enllaç al document .csv que es crearà
        $resultat_permisos[] = "\\\ ";
        $resultat_permisos[] = "{{:permisoswiki.csv|Descarrega fitxer permisos en format .csv}} ";

        //print_r($afitxer);
        foreach ($afitxer as $linia) {

            $linia1 = rtrim($linia);
            // traiem el primer caracter separador |
            $linia2 = substr($linia1, 1);
            $linia2 = $linia2;

            $alinia = explode("|", $linia2);

            // print_r($alinia);
            $dirs_index = explode("/", trim($alinia[0]));
            //   print_r($dirs_index);
            if (count($dirs_index) < 2) {
                $marca_titol = "=====";
            } else {
                $marca_titol = "====";
            }

            $nl = str_replace("\\", '', $alinia[2]);
            $apartat = rtrim($nl);

            if ($apartat != "" and trim($alinia[1]) > -1) {

                //              echo "|".trim($alinia[3])."|";
                if (trim($alinia[3]) != '') {
                    $responsable = " (" . trim($alinia[3]) . ") ";
                } else {
                    $responsable = "";
                }
                $resultat[] = "\n" . $marca_titol . " " . $apartat . $responsable . $marca_titol . "\n";
                $resultat_permisos[] = "\n" . $marca_titol . " " . $apartat . $responsable . $marca_titol . "\n";
            }

            if (trim($alinia[1]) == 1 or trim($alinia[1]) == 2) {

                foreach ($f as $path) {
                    $tira_permis = '';
                    $tira_autoritzats = '';
                    $tira_autoritzats_impres = '';
                    $dirs_arxius = explode("/", $path);

                    if ((strpos($path, trim($alinia[0])) !== false) and ($dirs_index[count($dirs_index) - 1] == $dirs_arxius[count($dirs_arxius) - 3]) and ((!in_array($dirs_arxius[count($dirs_arxius) - 2], $inactiu) or $tot == 1))) {

                        if (in_array($dirs_arxius[count($dirs_arxius) - 2], $inactiu) and $tot == 1) {
                            //		echo "%%% " .$dirs_arxius[count($dirs_arxius)-2]."<br>";
                            //$resultat[] =  "  * [[cursos:".$desti." |".$titolcurs[0]."]]".$img_nou.$img_elaboracio.$img_actualitzat."\n";	
                        }
                        $titolcurs = llegeixtitols("/dades/wikiform/data/pages/" . $path);
                        // print_r($titolcurs);
                        $img_nou = "";
                        if (in_array($dirs_arxius[count($dirs_arxius) - 2], $novetat))
                            $img_nou = "{{cursos:nou.gif|}}";

                        $img_elaboracio = "";
                        if (in_array($dirs_arxius[count($dirs_arxius) - 2], $elaboracio))
                            $img_elaboracio = "{{cursos:elaboracio.gif|}}";

                        $img_actualitzat = "";
                        if (in_array($dirs_arxius[count($dirs_arxius) - 2], $actualitzat))
                            $img_actualitzat = "{{cursos:actua.gif|}}";

                        $desti = str_replace('/', ':', $path);
                        $desti_wiki = str_replace('.txt', '', $desti);
                        $desti_permisos = str_replace('index.txt', '', $desti);

                        $item = explode("/", $path);
                        $curs = $item[count($item) - 2];

                        $permis = tepermis($curs, $desti_permisos);

                        $nopermis = false;
                        if (count($permis) > 0) {
                            foreach ($permis as $p) {
                                $nivell = nivellpermis(rtrim($p['permis']));
                                $tira_permis .= $p['grup'] . "-" . $nivell . " / ";
                            }
                        } else {
                            $nopermis = true;
                        }

                        $tira_permis = substr($tira_permis, 0, -2);

                        $autoritzats = usuaris_grup($curs);
                        //                     print_r($autoritzats);
                        if (count($autoritzats) > 0) {
                            foreach ($autoritzats as $a) {
                                $tira_autoritzats .= "[[?do=admin&page=permissioninfo&show=userpermissions&user={$a['usuari']}|{$a['usuari']} ]]" . " - ";
                                $tira_autoritzats_impres .= $a['usuari'] . " - ";
                            }
                            $tira_autoritzats = substr($tira_autoritzats, 0, -2);
                            $tira_autoritzats_impres = substr($tira_autoritzats_impres, 0, -2);
                        }

                        //$resultat[] = "  * [[:" . $desti_wiki . " |" . $titolcurs[0] . "]]" .$titolcurs['dataversio']." ". $img_nou . $img_elaboracio . $img_actualitzat . " " . $tira_permis . $tira_autoritzats . "\n";
                        $resultat[] = "[[:" . $desti_wiki . " |" . $titolcurs[0] . "]] " . $titolcurs['dataversio'] . " " . $img_nou . " " . $img_elaboracio . " " . $img_actualitzat . chr(13) . chr(10) . chr(13) . chr(10);
                        //"| " . rtrim($tira_permis) . "|" . $tira_autoritzats . "| " . chr(13) . chr(10) . chr(13) . chr(10);

                        if (trim($titolcurs['dataversio']) != '') {
                            $final = "| Data i mida |" . trim($titolcurs['dataversio']) . "| " . chr(13) . chr(10); //. chr(13) . chr(10);
                        } else {
                            $final = ''; // chr(13) . chr(10);
                        }
                        if (trim($titolcurs['tags']) != '') {
                            $tags = "| Tags        | " . $titolcurs['tags'] . " |" . chr(13) . chr(10);
                        } else {
                            $tags = '';
                        }

                        if (trim($tira_autoritzats) != '') {
                            $autors = "| Usuaris     | " . $tira_autoritzats . "| " . chr(13) . chr(10);
                        } else {
                            $autors = '';
                        }

                        if (trim($tira_permis) != '') {
                            $grups = "| Grup-permís | " . rtrim($tira_permis) . "|" . chr(13) . chr(10);
                        } else {
                            $grups = '';
                        }

                        $tira_unitats = '';
                        $unitat = '';
                        if (isset($unitats[$curs])) {
                            $tira_unitats = $unitats[$curs];
                            $unitat = "| Unitat | **" . $unitats[$curs] . "** (" . $nomUnitats[$unitats[$curs]] . ") |" . chr(13) . chr(10);
                            // echo $curs." ".$unitats[$curs].$tira_unitats."<br>";
                        }

                        $resultat_permisos[] = "^ $curs ^[[:" . $desti_wiki . " |" . $titolcurs[0] . "]] " . $img_nou . " " . $img_elaboracio . " " . $img_actualitzat . " ^  " . chr(13) . chr(10) .
                                $unitat .
                                $grups .
                                $autors .
                                $tags .
                                $final;
                        /*
                          if ($nopermis) {
                          $nopermisos[] = "^ $curs ^[[:" . $desti_wiki . " |" . $titolcurs[0] . "]] " . $img_nou . $img_elaboracio . $img_actualitzat . " ^  " . chr(13) . chr(10) .
                          "| " . rtrim($tira_permis) . "|" . $tira_autoritzats . "| " . chr(13) . chr(10) . chr(13) . chr(10);
                          } else {
                          $ambpermisos[] = "^ $curs ^[[:" . $desti_wiki . " |" . $titolcurs[0] . "]] " . $img_nou . $img_elaboracio . $img_actualitzat . " ^  " . chr(13) . chr(10) .
                          "| " . rtrim($tira_permis) . "|" . $tira_autoritzats . "| " . chr(13) . chr(10) . chr(13) . chr(10);
                          }
                         */
                        //   echo $unitats[$curs];
                        $fullcalcul .= $curs . ";" . trim($titolcurs[0]) . ";" . $unitats[$curs] . ";" . rtrim($tira_permis) . ";" . $tira_autoritzats_impres . ";" . $titolcurs['dataversio'] . ";" . $titolcurs['tags'] . chr(13) . chr(10);

                        $tots[] = array($path,
                            "  * [[:" . $desti_wiki . " |" . $titolcurs[0] . "]]" . $img_nou . " " . $img_elaboracio . " " . $img_actualitzat . "\n",
                            $permis,
                            $autoritzats);
                    }
                }
            }
        }

        //        print_r($nopermisos);
//        print_r($ambpermisos);
        //      $resultat = array_merge($nopermisos, $ambpermisos);

        if ($tot != 1) {
            echo "<br />El fitxer creat inclou només  els cursos actius (apte per a ser publicat a la pàgina inicial)";

            $fitxer = "/dades/wikiform/data/pages/z_gestio/aux/index_cursos.txt";
        } else {
            echo "<br />El fitxer inclou tots els cursos, inclosos els inactius (destinat a la gestió)";

            $fitxer = "/dades/wikiform/data/pages/z_gestio/aux/index_cursos_gestio.txt";
        }
        $cami2 = "/dades/wikiform/data/media/";
        $fitxer2 = $cami2 . "permisoswiki.csv";
        //$fitxer2 = "/dades/wikiform/data/pages/z_gestio/aux/permisoswiki.csv";
        $fitxer3 = "/dades/wikiform/data/pages/z_gestio/aux/permisoswiki.txt";

        write_error('errors.log', "\n" . $alerta, 'a');
        write_error('errors.log', "\n\n" . "Data de creació de l'índex de cursos: " . date("d/m/Y  G:i:s"), 'a');

        if (file_put_contents("/dades/wikiform/data/pages/z_gestio/aux/temp.txt", $resultat) === false) {
            if ($tot != 1)
                echo "No s'ha pogur crear el fitxer temporal";
        }else {
            unlink($fitxer);
            rename("/dades/wikiform/data/pages/z_gestio/aux/temp.txt", $fitxer);
            echo "<br />Fitxer '$fitxer' creat amb èxit <br /><br />";
            echo "<br />Cursos sense títol: <br />";
            include('errors.log');
        }

        if (file_put_contents($cami2 . "temp2.txt", $fullcalcul) === false) {
            echo "No s'ha pogur crear el fitxer temporal";
        } else {
            unlink($fitxer2);
            rename($cami2 . "temp2.txt", $fitxer2);
            echo "<br />Fitxer '{$cami}{$fitxer2}' creat amb èxit <br /><br />";
        }

        if (file_put_contents("/dades/wikiform/data/pages/z_gestio/aux/temp3.txt", $resultat_permisos) === false) {
            if ($tot != 1)
                echo "No s'ha pogur crear el fitxer temporal";
        }else {
            unlink($fitxer3);
            rename("/dades/wikiform/data/pages/z_gestio/aux/temp3.txt", $fitxer3);
            echo "<br />Fitxer '{$fitxer3}' creat amb èxit <br /><br />";
        }

        echo '<br /> <br /><a href="/wikiform/wikiform/z_gestio/gestio_cursos">Torna a gestió de cursos (Wiki)</a>';
        //echo '  |  <a href="/wikiform/serveis/scripts/crea_index.php">Crea índex de materials</a>';
        echo '  |  <a href="/wikiform/serveis/scripts/crea_index.php?tot=1" title="Cal fer-ho només quan haguem afegit cursos nous">Refés llista base de cursos</a>';
        echo '  |  <a href="/wikiform/serveis/scripts/gestio_cursos.php">Gestió de cursos</a>';
        echo '  |  <a href="/wikiform/wikiexport/cursos/index?purge=true">Purga index wikiexport</a>';
        echo '  |  <a href="/wikiform/wikiform/cursos/index?purge=true">Purga index wikiform</a>';
        ?>
    </pre>
</html>