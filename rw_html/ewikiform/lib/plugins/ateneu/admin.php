<?php

/**
 * ateneu: Gestiona informació sobre cursos i materials
 * 
 * @author     Jordi Fons <jfons@xtec.cat>
 */
if (!defined('DOKU_INC'))
    define('DOKU_INC', realpath(dirname(__FILE__) . '/../../') . '/');
if (!defined('DOKU_PLUGIN'))
    define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
require_once(DOKU_PLUGIN . 'admin.php');

/**
 * All DokuWiki plugins to extend the admin function
 * need to inherit from this class
 */
class admin_plugin_ateneu extends DokuWiki_Admin_Plugin {

    /**
     * If information about which user is in which group is displayed
     */
    var $show_group_info = true;

    /**
     * Definim camí principal 
     */
    var $arrel = "/dades/wikiform/data/pages/";

    /**
     * Definim títol a mostrar
     */
    var $titol = "Ateneu - Assistent per a la gestió de cursos";

    /**
     * retorna ordre per a la posició al menú admin
     */
    function getMenuSort() {
        return 145;
    }

    /**
     * handle user request
     */
    function handle() {
        global $conf;
        global $auth;
        global $INPUT;
        $this->auth = $auth;

        // If the auth class can't list users or groups, retrieve user and group information from ACL
        if ($this->auth->canDo('getUsers')) {
            $getUserFunc = array($this->auth, 'retrieveUsers');
        } else {
            // Can't determine user/group association from ACL
            $this->show_group_info = false;
        }

        if ($this->auth->canDo('getGroups'))
            $getGroupFunc = array($this->auth, 'retrieveGroups');
        else
            $getGroupFunc = array($this, '_getGroupsFromACL');
        // Collect user and group names
        $this->users = call_user_func($getUserFunc);
        $this->groups = call_user_func($getGroupFunc);
        ksort($this->groups);

        // Get permissions for each group and set the data in $this->aclGroupPermissions
        $this->_aclGroupPermissions();

        // Get explicit user permissions from ACL and set the data in $this->aclUserPermissions
        $this->_aclUserPermissions('jfons');

        // Associate groups with users and set the data in $this->group2user
        $this->_group2user();

        // If we show permissions for an individual user, collect its permissions
        if ($INPUT->has('show') && $INPUT->has('user')) {
            $this->_userPermissions($INPUT->str('user'));
        }
    }

    /**
     * output pàgina triada segons opció
     * depenent de $_REQUEST['show']
     */
    function html() {
        global $INPUT;

        switch ($INPUT->str('show', 'inici')) {

            case 'infocursos':
                $this->_cursosOverview();
                break;
            case 'gestiona':
                $this->_gestiona();
                break;
            case 'infosistema':
                $this->_info();
                break;
            default:
                $this->_inici();
        }
    }

    function _info() {
        ptln('<h1>' . $this->titol . '</h1>');
        ptln('<h2>Info sistema</h2>');
        ptln($this->menu());

        ob_start();
        phpinfo();
        $pinfo = ob_get_contents();
        ob_end_clean();

        $pinfo = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $pinfo);
        echo $pinfo;
    }

    function _inici() {
        ptln('<h1>' . $this->titol . '</h1>');
        ptln('<h2>Inici </h2>');
        ptln($this->menu());
    }

    /**
     * Mostra els cursos amb selector d'unitat i checkbox per a actiu, etc. 
     */
    function _gestiona() {
        global $INPUT;

        $id = cleanID($this->getLang('menu'));

        $fitxer_marcats = $this->arrel . "z_gestio/aux/infocursos.txt";
        $fitxer_unitats = $this->arrel . "z_gestio/aux/base_cursos_unitats.txt";

        // Si  hem enviat el formulari omplim fitxer infocursos
        if ($INPUT->str('envia') == 'Envia') {

            $recollida = array('novetat' => $INPUT->arr('nou'),
                'inactius' => $INPUT->arr('inact'),
                'elaboracio' => $INPUT->arr('elab'),
                'actualitzats' => $INPUT->arr('actu')
            );

            foreach ($recollida as $tipus => $item) {
                $contingut .= "\n\n[" . $tipus . "]\n";
                foreach ($item as $r) {

                    $contingut .= $r . "$";
                }
            }

            copy($fitxer_marcats, $fitxer_marcats . ".bak");
            if (file_put_contents($this->arrel . "z_gestio/aux/" . "temp.txt", $contingut) === false) {
                echo "No s'ha pogut crear el fitxer temporal";
            } else {
                unlink($fitxer_marcats);
                rename($this->arrel . "z_gestio/aux/temp.txt", $fitxer_marcats);
            }

            $escriu = "^ Codi                ^ Unitat  ^\n";
            foreach ($INPUT->arr('unitats') as $curs => $uni) {
                $escriu .= "| " . $curs . " | " . $uni . " |\n";
            }
            if (file_put_contents($this->arrel . "z_gestio/aux/" . "temp.txt", $escriu) === false) {
                echo "No s'ha pogut crear el fitxer temporal";
            } else {
                unlink($fitxer_unitats);
                rename($this->arrel . "z_gestio/aux/temp.txt", $fitxer_unitats);
            }
        }

        $uniCurs = $this->unitatsCursos();
        $blanc = 1;
        $uni = $this->unitats($blanc);
        ksort($uni);

        $marcats = $this->getCursosMarcats($fitxer_marcats);

        $helper = $this->loadHelper('ateneu'); // or $this->loadHelper('tag', true);
        $esquelet = $helper->indexArray();
        $cursos = $helper->getCursos($esquelet);
        $compta = 0;

        ptln('<h1>' . $this->titol . '</h1>');
        ptln('<h2>Unitat i estats dels cursos </h2>');
        ptln($this->menu());

        ptln('<form action="index?do=admin&page=ateneu&show=gestiona" method="post">');
        ptln('<div class="no"><input type="hidden" name="id" value="' . $ID . '" /></div>');
        formSecurityToken();

        ptln('<table>');
        ptln('<tr><th>Codi</th>'
                . '<th>Títol</th>'
                . '<th>Unitat</th>'
                . '<th>Inact</th>'
                . '<th>Nove</th>'
                . '<th>Elab</th>'
                . '<th>Actu</th>'
                . '<th>Tags</th></tr>');

        foreach ($cursos as $c) {
            
            if (isset($uniCurs[$c['codi']]) and ! empty($uniCurs[$c['codi']])) {
                $unitat = '<span style="cursor: help;" title="' . $uni[$uniCurs[$c['codi']]] . '">' . $uniCurs[$c['codi']] . '</span>';
            } else {
                $unitat = '';
            }
            $tasco = '';
            if (strlen($titol) > 40)
                $tasco = "...";
            $abre = substr($c['titol'], 0, 40) . $tasco;

            $tags = '';
            $infocurs = $helper->llegeixIndexCurs($this->arrel . $c['cami']);      
            
            
            if ($infocurs['tags'] != ''){
                $tags = "Sí";            
                $llistaTags = str_replace(" ", "&#13;", $infocurs['tags']);
            }
            
            $inact_check = "";
            if (count($marcats['inactius']) > 0 and in_array(strtolower($c['cami']), $marcats['inactius']))
                $inact_check = "checked";

            $novet_check = "";
            if (count($marcats['novetat']) > 0 and in_array(strtolower($c['cami']), $marcats['novetat']))
                $novet_check = "checked";

            $elabo_check = "";
            if (count($marcats['elaboracio']) > 0 and in_array(strtolower($c['cami']), $marcats['elaboracio']))
                $elabo_check = "checked";

            $actua_check = "";
            if (count($marcats['actualitzats']) > 0 and in_array(strtolower($c['cami']), $marcats['actualitzats']))
                $actua_check = "checked";

            ptln('<tr><td>');
            ptln('<div style="width: 60px;  white-space: nowrap;  overflow: hidden;  text-overflow: ellipsis; cursor: help;"> <span title="' . $c['titol'] . '&#13; ' . $c['cami'] . '">' . $c['codi'] . '</span> </div>');
            ptln('</td><td>');

            ptln('<div style="width: 300px;  white-space: nowrap;  overflow: hidden;  text-overflow: ellipsis;"> <a href="/wikiform/wikiform/' . substr($c['cami'], 0, -4) . '" title="'.$c['titol'].' ">' . $c['titol'] . '</a></div>');
            ptln('</td><td>');

            // creem select per a mostrar/triar unitat
            ptln('<select name="unitats[' . $c['cami'] . ']">');
            foreach ($uni as $codi => $desc) {
                $seleccionat = '';
                if ($codi == $uniCurs[$c['cami']])
                    $seleccionat = 'selected';

                $tasco = '';
                if (strlen($desc) > 20)
                    $tasco = '...';

                ptln(' <option value ="' . $codi . '" title="' . $desc . '" ' . $seleccionat . '>' . $codi . '</option>');
            }
            ptln('</select>');

            ptln('</td><td style="text-align:center" >');

            ptln('<input type="checkbox" name="inact[]" ' . $inact_check . ' value="' . $c['cami'] . '" title="Inactiu">');
            ptln('</td><td style="text-align:center" >');

            ptln('<input type="checkbox" name="nou[]" ' . $novet_check . ' value="' . $c['cami'] . '" title="Novetat">');
            ptln('</td><td style="text-align:center">');

            ptln('<input type="checkbox" name="elab[]" ' . $elabo_check . ' value="' . $c['cami'] . '" title="En elaboració">');
            ptln('</td><td style="text-align:center">');

            ptln('<input type="checkbox" name="actu[]" ' . $actua_check . ' value="' . $c['cami'] . '" title="Actualitzat">');          
            
            ptln( '</td><td style="text-align:center;"><span title = "' . $llistaTags . '" style="cursor:help;">'.$tags .'</span>');

            ptln('</td></tr>');
        }

        ptln("</table>");

        ptln('<input type="submit" class="button" name="envia" value="Envia" />');
        ptln('</form>');
    }

    /**
     * Mostra la informació sobre tot els cursos
     */
    function _cursosOverview() {
        global $conf;

        $id = cleanID($this->getLang('menu'));

        $cami_media = "/dades/wikiform/data/media/";
        $fitxer_graella = "permisoswiki2.csv";
        $contingut = 'Codi' . '|' . 'Títol' . '|' . 'Camí' . '|' . 'Permisos' . '|' . 'Unitat' . '|' . 'Tags' . '|' . 'Marques' . '|' . 'Data i versió' . "|" . chr(13) . chr(10);

        ptln('<h1>' . $this->titol . '</h1>');
        ptln('<h2>Info dels cursos</h2>');
        ptln($this->menu());

        $fitxer_marcats = $this->arrel . "z_gestio/aux/infocursos.txt";
        $marcats = $this->getCursosMarcats($fitxer_marcats);
        $novetat = $marcats['novetat'];
        $inactiu = $marcats['inactius'];
        $elaboracio = $marcats['elaboracio'];
        $actualitzat = $marcats['actualitzats'];

        $helper = $this->loadHelper('ateneu'); // or $this->loadHelper('tag', true);
        $esquelet = $helper->indexArray();
        $cursos = $helper->getCursos($esquelet);

        $compta = 0;
        $uniCurs = $this->unitatsCursos();
        $unisInfo = $this->unitatsTot();
        $uni = $this->unitats();
  
        $img = DOKU_BASE.'lib/images/info.png';      
  
        ptln('<table>');
        foreach ($cursos as $curs) {
            $compta++;
            $infocurs = $helper->llegeixIndexCurs($this->arrel . $curs['cami']);

            $permis = $this->permisNS($curs['cami']);
            $permis_csv = str_replace("%5f", "_", $this->permisNS($curs['cami'], 1));

            $infoUni = $unisInfo[$uniCurs[$curs['cami']]];
           // print_r($infoUni);

            $contacte = '';
              if ($infoUni['responsables'] != ''){
                $contacte = " <span style='cursor: help;' title=' Email: " . str_replace("\\", "",$infoUni['emails']) . '&#13; Ext.: ' . str_replace("\\", "",$infoUni['telefons']) . "'>". '<img src="'.$img.'" width="16" height="16" style="vertical-align: center" class="'.$link['class'].'" /> </span> ';
            }

            if (isset($uniCurs[$curs['cami']]) and ! empty($uniCurs[$curs['cami']])) {
                $unitat = $uniCurs[$curs['cami']] . " (" . $uni[$uniCurs[$curs['cami']]] . ")";
            } else {
                $unitat = '';
            }
            $nove = '';
            if (in_array($curs['cami'], $novetat))
                $nove = "Novetat | ";

            $inac = '';
            if (in_array($curs['cami'], $inactiu))
                $inac = "Inactiu | ";

            $elab = '';
            if (in_array($curs['cami'], $elaboracio))
                $elab = "En elaboració | ";

            $actu = '';
            if (in_array($curs['cami'], $actualitzat))
                $actu = "Actualitzat";

            $tags = explode(" ", $infocurs['tags']);
            $tiraTags = '';
            foreach ($tags as $t) {
                $tiraTags .= '<a href="tag/' . $t . '?do=showtag&tag=' . $t . '">' . $t . '</a> ';
            }

            // mirem si és un directori 'contenidor' de cursos i el maquem i mostrem
            $parts = explode("/", $curs['cami']);
            $dir = $parts[count($parts) - 2];
            $directori = '';
            //$colorfons = "style='background: #fff;' ";
            $colorfons = "";
            if (in_array($dir, $conf['directoris'])) {
                $directori = "[[" . $dir . "]]";
                $colorfons = " style='background: #fdd;' ";
            }
            //ptln('<div class="piContainer">');
            ptln('<tr>');
            //  ptln("<th>----------</th><th>-------------------------------------</th>");            
            //  ptln('</tr><tr>');
            ptln("<th>" . $compta . "</th><th>" . $curs['codi'] . "</th><th {$colorfons}><a href='/wikiform/wikiform/" . substr($curs['cami'], 0, -4) . "'>" . $directori . " " . $curs['titol'] . '</a></th>');
            ptln('</tr><tr>');
            ptln("<td></td><td> Camí </td><td>" . "  " . $curs['cami'] . '</td>');

            ptln('</tr><tr>');
            ptln("<td></td><td> Grup-permís </td><td>" . urldecode($permis) . '</td>');

            ptln('</tr><tr>');
            ptln("<td></td><td> Unitat </td><td>" . $unitat . " ". str_replace("\\", "", $infoUni['responsables']) .  $contacte    .'</td>');

            ptln('</tr><tr>');
            ptln("<td></td><td> Data i mida </td><td>" . $infocurs['dataversio'] . '</td>');

            ptln('</tr><tr>');

            //ptln("<td></td><td> Etiquetes </td><td>" . $infocurs['tags'] . '</td>');
            ptln("<td></td><td> Etiquetes </td><td>" . $tiraTags . '</td>');

            ptln('</tr><tr>');
            ptln("<td></td><td> Marques </td><td>" . $nove . " " . $inac . " " . $elab . "  " . $actu . '</td>');
            ptln('</tr>');
            // ptln('</div>');

            $contingut .= $curs['codi'] . "|" . $curs['titol'] . "|" . $curs['cami'] . "|" . $permis_csv . "|" . $uniCurs[$curs['cami']] . "|" . $infocurs['tags'] . "|" . substr($nove, 0, -3) . " " . substr($inac, 0, -3) . " " . substr($elab, 0, -3) . "  " . $actu . "|" . $infocurs['dataversio'] . chr(13) . chr(10);
        }
        ptln('</table>');

        if (file_put_contents($cami_media . "temp2.txt", $contingut) === false) {
            echo "No s'ha pogut crear el fitxer temporal: " . $cami_media . $fitxer_graella;
        } else {
            unlink($cami_media . $fitxer_graella);
            rename($cami_media . "temp2.txt", $cami_media . $fitxer_graella);
            echo "<br />Fitxer '{$cami_media}{$fitxer_graella}' creat amb èxit <br /><br />";
        }
    }

    /**
     * This function retrieves group names from the acl file.
     * Since none of the existing auth classes supports groups, I don't know 
     * what output to expect from them. I assume a two-dimensional hash similar 
     * to that from Auth->retrieveUsers.
     * @return array
     */
    function _getGroupsFromACL() {
        global $AUTH_ACL;
        $groups = array();
        foreach ($AUTH_ACL as $a) {
            // Don't parse comments
            if (preg_match('/^#/', $a))
                continue;
            if (preg_match('/^[^\s]+\s@([^\s]+)/', $a, $matches)) {
                $grp_arr = array('name' => $matches[1]);
                $groups[urldecode($matches[1])] = $grp_arr;
            }
        }
        return $groups;
    }

    
    /**
     * sets $this->aclGroupPermissions in the form of a[groupname][namespace/page_name]=permission
     */
    function _aclGroupPermissions() {
        $AUTH_ACL = $this->_auth_loadACL(); //without %USER% replacement
        $gp = array();
        $pm = array();
        foreach ($AUTH_ACL as $a) {
            // Don't parse comments
            if (preg_match('/^#/', $a))
                continue;
            if (preg_match('/^([^\s]+)\s@([^\s]+)\s(\d+)/', $a, $matches)) {
                $gp[$matches[2]][$matches[1]] = $matches[3];
                $pm[] = array('cami' => $matches[1],
                    'grup' => $matches[2],
                    'permis' => $matches[3]);
            }
        }

        $this->aclGroupPermissions = array();
        foreach ($gp as $grpname => $permissions) {
            ksort($permissions);
            $this->aclGroupPermissions[urldecode($grpname)] = $permissions;
        }
        $this->permisos = $pm;
    }

    /**
     * sets $this->aclUserPermissions in the form of a[username][namespace/page_name]=permission
     */
    function _aclUserPermissions() {
        global $AUTH_ACL;
        $up = array();
        foreach ($AUTH_ACL as $a) {
            // Don't parse comments
            if (preg_match('/^#/', $a))
                continue;
            if (preg_match('/^([^\s]+)\s([^@\s]+)\s(\d+)/', $a, $matches)) {
                $up[$matches[2]][$matches[1]] = $matches[3];
            }
        }
        $this->aclUserPermissions = array();
        foreach ($up as $usrname => $permissions) {
            ksort($permissions);
            $this->aclUserPermissions[$usrname] = $permissions;
        }
    }

    /**
     * Build an Array in $this->group2user that associates user names with users
     * The users are sorted by last name
     */
    function _group2user() {
        $g2u = array();
        foreach (array_keys($this->groups) as $g)
            $g2u[$g] = array();
        foreach ($this->users as $username => $properties) {
            foreach ($properties['grps'] as $grpname)
                $g2u[$grpname][$username] = array_pop(explode(' ', $properties['name'])); // Store last name of user here for Sorting
        }
        $this->group2user = array();
        foreach ($g2u as $grpname => $users) {
            // Sort users in each group by last name
            asort($users);
            $this->group2user[$grpname] = array_keys($users);
        }
    }

    /**
     * Torna una tira amb els permisos de grup i els usuaris corresponents amb permís
     * Els usuaris són ordents per nom
     * 
     * $this->group2user
     */
    function _nsPermisos() {
        $g2u = array();
        foreach (array_keys($this->groups) as $g)
            $g2u[$g] = array();
        foreach ($this->users as $username => $properties) {
            foreach ($properties['grps'] as $grpname)
                $g2u[$grpname][$username] = array_pop(explode(' ', $properties['name']));
        }
        $this->group2user = array();
        foreach ($g2u as $grpname => $users) {
            // Sort users in each group by last name
            asort($users);
            $this->group2user[$grpname] = array_keys($users);
        }
    }

    /**
     * Collects permission data for an individual user from the ACL. It 
     * collects permission data from the groups of the user and from the 
     * explicitly assigned permissions for the user. 
     * The data is stored in the form of two arrays:
     * $this->userPermissions Namespace/Page => Permission pairs
     * $this->explicitUserPermissions Namespace/Page => Permission pairs
     */
    function _userPermissions($username) {
        // Build regular expression for the username an its groups
        $userdata = $this->auth->getUserData($username);
        $this->username = $userdata['name'];
        $search_string = preg_quote(auth_nameencode($username), '/');
        foreach ($userdata['grps'] as $g)
            $search_string .= '|@' . preg_quote(auth_nameencode($g), '/');
        $perm_regex = '/^([^\s]+)\s(' . $search_string . ')\s(\d+)/';
        // Search through permissions
        $AUTH_ACL = $this->_auth_loadACL(); //without user replacement
        $up = array();
        // $for_user holds permissions that are assigned explicitly to the user
        $for_user = array();
        foreach ($AUTH_ACL as $a) {
            // Don't parse comments
            if (preg_match('/^#/', $a))
                continue;
            if (preg_match($perm_regex, $a, $matches)) {
                $ns = str_replace('%USER%', auth_nameencode($username), $matches[1]); //replace %USER% with username
                $up[$ns] = (empty($up[$matches[1]]) ? 0 : $up[$matches[1]]) | $matches[3];
                if (substr($matches[2], 0, 1) != "@")
                    $for_user[$ns] = $matches[3];
            }
        }
        ksort($up);
        ksort($for_user);
        $this->userPermissions = $up;
        $this->explicitUserPermissions = $for_user;
    }

    /**
     * Loads the ACL setup 
     * 
     * copyed from inc/auth -> auth_loadACL()
     *  - removed substitute of user wildcard
     */
    function _auth_loadACL() {
        global $config_cascade;
        global $USERINFO;

        if (!is_readable($config_cascade['acl']['default']))
            return array();

        $acl = file($config_cascade['acl']['default']);

        $out = array();
        foreach ($acl as $line) {
            $line = trim($line);
            if (empty($line) || ($line{0} == '#'))
                continue; // skip blank lines & comments
            list($id, $rest) = preg_split('/[ \t]+/', $line, 2);

            // substitute group wildcard (its 1:m)
            if (strstr($line, '%GROUP%')) {
                // if user is not logged in, grps is empty, no output will be added (i.e. skipped)
                foreach ((array) $USERINFO['grps'] as $grp) {
                    $nid = str_replace('%GROUP%', cleanID($grp), $id);
                    $nrest = str_replace('%GROUP%', '@' . auth_nameencode($grp), $rest);
                    $out[] = "$nid\t$nrest";
                }
            } else {
                $out[] = "$id\t$rest";
            }
        }

        return $out;
    }

    /**
     * 
     * @param type $blanc
     * @return type
     */
    function unitats($blanc = 0) {

        $fitxerUnitats = $this->arrel . "z_gestio/aux/base_unitats.txt";
        //  echo $fitxerUnitats;
        $aNomUnitats = file($fitxerUnitats);
        $nomUnitats = array();

        foreach ($aNomUnitats as $nom) {
            $tros = explode("|", $nom);
            $nomUnitats[trim($tros[1])] = trim($tros[2]);
        }
        if ($blanc != 1)
            array_shift($nomUnitats);

        /* $fitxerCursosUnitats = $this->arrel . "z_gestio/aux/base_cursos_unitats.txt";
          $aunitats = file($fitxerCursosUnitats);
          $aNomUnitats = file($fitxerUnitats);
          $unitats = array();


          foreach ($aunitats as $lin) {
          $tros = explode("|", $lin);
          $unitats[trim($tros[1])] = trim($tros[2]);
          }

          array_shift($unitats);
          print_r($nomUnitats);
         * 
         */
        return $nomUnitats;
    }

    /**
     * 
     * @return type
     */
    function unitatsCursos() {
        $fitxerCursosUnitats = $this->arrel . "z_gestio/aux/base_cursos_unitats.txt";
        $aunitats = file($fitxerCursosUnitats);
        $unitats = array();
        foreach ($aunitats as $lin) {
            $tros = explode("|", $lin);
            $unitats[trim($tros[1])] = trim($tros[2]);
        }
        array_shift($unitats);

        return $unitats;
    }

    /**
     * 
     * @return type
     */
    function unitatsTot() {
        $fitxerCursosUnitats = $this->arrel . "z_gestio/aux/base_unitats.txt";
        $aunitats = file($fitxerCursosUnitats);
        //print_r($aunitats);
        $unitats = array();
        foreach ($aunitats as $lin) {
            $tros = explode("|", $lin);
            $unitats[trim($tros[1])] = array('nom' => trim($tros[2]),
                'responsables' => trim($tros[3]),
                'emails' => trim($tros[4]),
                'telefons' => trim($tros[5]));
        }
        array_shift($unitats);

        return $unitats;
    }

    /**
     * 
     * @param type $ns
     * @param type $csv
     * @return string
     */
    function permisNS($ns, $csv = 0) {
        $cami_modi = str_replace('index.txt', '', $ns);
        $cami_modi = str_replace('/', ':', $cami_modi);
        foreach ($this->permisos as $p) {
            //echo $cami_modi. $p['cami']."<br>"; 
            if ($cami_modi . '*' == $p['cami']) {
                //echo $cami_modi. $p['cami']."<br>";
                $usuaris = '';
                foreach ($this->group2user[$p['grup']] as $u) {
                    if ($csv == 1) {
                        $final = ''; //chr(10);
                        $usuaris .= $u . " ";
                    } else {
                        $final = "<br>";
                        $usuaris .= "<a href='?do=admin&page=permissioninfo&show=userpermissions&user=" . $u . "'>$u</a> ";
                    }
                }

                $permis .= "@" . $p['grup'] . "-" . $this->_permis($p['permis']) . ": " . $usuaris . $final;
            }
        }
        return $permis;
    }

    /**
     * Funció getCursosMarcats()
     * 
     * Recupera  un array amb 4 subarrays corresponents als cursos: inactius, novetat, en eleboració i actualitzats
     * Torna un array amb:
     * [inactius] = array amb codis de cursos inactius
     * [novetat] = array amb codis de cursos que són novetat
     * [elaboracio] = array amb codis de cursos que estan en elaboració
     * [actualitzats] = array amb codis de cursos que s'han actulizat de fa poc
     * Paràmetres: $fitxer = str (path i nom del fitxer que conté la informació) 
     * 
     */
    function getCursosMarcats($fitxer) {
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
                $camins = explode("$", $linia);
                // print_r($camins);
                foreach ($camins as $cami) {
                    if (trim($cami) != '') {
                        $torna[$marca][] = trim($cami);
                    }
                }
            }
        }
        //$darrer = array_pop($torna);
        return $torna;
    }

    function menu() {
        $separador = " | ";
        $menu = "<a href='/wikiform/wikiform/?do=admin&page=ateneu'>Inici</a>" . $separador .
                "<a href='/wikiform/wikiform/?do=admin&page=ateneu&show=infocursos'>Info cursos</a>" . $separador .
                "<a href='/wikiform/wikiform/?do=admin&page=ateneu&show=gestiona'>Estats i unitats cursos</a>" . $separador .
                "<a href='/wikiform/wikiform/?do=admin&page=ateneu&show=infosistema'>Sistema</a>" . $separador .
                "<a href='/wikiform/wikiform/z_gestio/aux/base_unitats'>Unitats</a>" . $separador .
                "<a href='/wikiform/wikiform/_media/permisoswiki2.csv'>Fitxer permisos .csv</a>" .
                "<br><br>";

        return $menu;
    }

    function _permis($permis) {
        $permisos = array(
            '0' => '',
            '1' => 'Lectura',
            '2' => 'Edició',
            '4' => 'Creació',
            '8' => 'Pujar fitxers',
            '16' => 'Suprimir',
            '255' => 'Admin'
        );

        return $permisos[$permis];
    }

}
