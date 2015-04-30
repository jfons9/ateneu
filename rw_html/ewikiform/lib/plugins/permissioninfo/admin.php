<?php
/**
 * Permissioninfo: Displays group and user information and the permissions of users and groups
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Gabriel Birke <gb@birke-software.de>
 */
 
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'admin.php');
 
/**
 * All DokuWiki plugins to extend the admin function
 * need to inherit from this class
 */
class admin_plugin_permissioninfo extends DokuWiki_Admin_Plugin {

    /**
     * If information about which user is in which group is displayed
     */
    var $show_group_info = true;

    /**
     * return sort order for position in admin menu
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
        if($this->auth->canDo('getUsers'))
        {
            $getUserFunc  = array($this->auth, 'retrieveUsers');
        }
        else
        {
            // Can't determine user/group association from ACL
            $this->show_group_info = false;
            $getUserFunc = array($this, "_getUsersFromACL");
        }
        if($this->auth->canDo('getGroups'))
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
        $this->_aclUserPermissions();

        // Associate groups with users and set the data in $this->group2user
        $this->_group2user();
        
        
        // If we show permissions for an individual user, collect its permissions
        if($INPUT->has('show') && $INPUT->has('user'))
        {
            $this->_userPermissions($INPUT->str('user'));
        }
    }
 
    /**
     * output Overview page with groups or permissionpage for individual user, all
     * depending on $_REQUEST['show']
     */
    function html() {
        global $INPUT;
        switch($INPUT->str('show','overview'))
        {
            case 'userpermissions':
                $this->_showUserPermissions();
                break;
            case 'overview':
            default:
                $this->_groupOverview();
        }
    }

    /**
     * Shows an overview for users in groups and permissions assigned to groups
     */
    function _groupOverview()
    {
        $id = cleanID($this->getLang('menu'));
        ptln('<h1><a name="'.$id.'" id="'.$id.'">'.$this->getLang('menu')."</a></h1>");
        echo $this->locale_xhtml('help');         
        
        foreach($this->groups as $gname => $g)
        {
            // container for group information
            ptln('<section class="piContainer">');
            // print group header
            ptln('<header>', 2);
            ptln("<h2>$gname</h2>", 4);
            ptln('</header>', 2);
            
            ptln('<div class="content">', 2);

            // print acl settings for this group 
            ptln('<header>'.$this->getLang('permissions').'</header>', 4);
            $this->_permissionTable($this->aclGroupPermissions[$gname], "permissions".$gname);

            // print users in group
            if(!empty($this->group2user[$gname])) {
                ptln('<header>'.$this->getLang('users').'</header>', 4);
                ptln('<div class="users">', 4);
                foreach($this->group2user[$gname] as $u)
                {
                    $url = wl($ID, array(
                        'do' => "admin",
                        'page' => $this->getPluginName(),
                        'show' => 'userpermissions',
                        'user' => $u
                    ));
                    $u_enc = auth_nameencode($u);
                    $lnk = '<a href="'.$url.'"  title="'.$u.'" '.(!empty($this->aclUserPermissions[$u_enc])?'class="special"':"").'>';
                    ptln($lnk.$this->users[$u]['name'].'</a>', 6);
                }
                ptln('   </div>');    
            }

            // close content div
            ptln('</div>', 2); 

            //end container
            ptln('</section>');
        }
    }

    /**
     * Show permissions for individual user, highlight permissions that were 
     * assigned explicitly to this user.
     */
    function _showUserPermissions()
    {
        $head = sprintf($this->getLang('pi_permissionfor'), $this->username);
        $id = cleanID($head);
        ptln('<h1><a name="'.$id.'" id="'.$id.'">'.$head."</a></h1>");
        echo $this->locale_xhtml('help_userpermissions');
        
        // Link to Overview
        $url =wl($ID, array(
            'do' => "admin",
            'page' => $this->getPluginName(),
            'show' => 'overview'
        ));
        ptln('<p class="piToOverview"><a href="'.$url.'">'.$this->getLang('pi_to_overview')."</a></p>");

        ptln('<div class="piContainer">');
        $this->_permissionTable($this->userPermissions, 'Userpermissions');
        ptln('</div>');
    }

    /**
     * Print permissions for a user or group
     * @param array $acldata namespace/page_name=>permission pairs
     * @param string $id ID for the div that surrounds the table
     */
    function _permissionTable($acldata)
    {
        $displayed_permissions = array(
            AUTH_READ,
            AUTH_EDIT,
            AUTH_CREATE,
            AUTH_UPLOAD,
            AUTH_DELETE
        );
        ptln("   <div class='permissions'>");
        if(empty($acldata))
        {
            ptln("    <p>".$this->getLang('pi_no_permissions_found').'</p>');
            ptln("    </div>");
            return;
        }
        ptln("   <table>");
        $s = "<tr><th>".$this->getLang('pi_resource')."</th>";
        foreach($displayed_permissions as $p)
            $s .= "<th>".$this->getLang('acl_perm'.$p)."</td>";
        ptln($s."</tr>",6);

        
        $even = false;
        foreach($acldata as $item => $perm)
        {
            $additional_class = empty($this->explicitUserPermissions[$item]) ? "" : " explicitUserPermission";
            ptln('<tr class="'.($even?"even":"odd").$additional_class.'">', 6);
            if(preg_match('/\*\s*$/', $item))
                ptln('<td class="piItemNS">'.$item.'</td>', 9);
            else
                ptln('<td class="piItemPage">'.$item.'</td>',9);
            foreach($displayed_permissions as $p)
            {
                if($p & $perm)
                    ptln('<td class="piAllowed">X</td>', 9);
                else
                    ptln('<td class="piDenied">-</td>', 9);
            }
            $even = !$even;
        }
        ptln("   </table>");
        ptln("   </div>");
    }

 
    /**
     * This just gets a very rudimentary user and not very useful user list - 
     * only users who have special permissions in the ACL are listed. 
     * @return array This array is structured similar to the array returned by an auth class.
     */
    function _getUsersFromACL()
    {
        global $AUTH_ACL;
        $users = array();
        foreach($AUTH_ACL as $a)
        {
            // Don't parse comments
            if(preg_match('/^#/', $a))
                continue;
            if(preg_match('/^[^\s]+\s([^@\s]+)/', $a, $matches))
            {
                $usr_arr = array('name' => $matches[1], 'grps' => array());
                $users[$matches[1]] = $usr_arr;
            }
        }
        return $users;
    }

    /**
     * This function retrieves group names from the acl file.
     * Since none of the existing auth classes supports groups, I don't know 
     * what output to expect from them. I assume a two-dimensional hash similar 
     * to that from Auth->retrieveUsers.
     * @return array
     */
    function _getGroupsFromACL()
    {
        global $AUTH_ACL;
        $groups = array();
        foreach($AUTH_ACL as $a)
        {
            // Don't parse comments
            if(preg_match('/^#/', $a))
                continue;
            if(preg_match('/^[^\s]+\s@([^\s]+)/', $a, $matches))
            {
                $grp_arr = array('name' => $matches[1]);
                $groups[urldecode($matches[1])] = $grp_arr;
            }
        }
        return $groups;
    }

    /**
     * sets $this->aclGroupPermissions in the form of a[groupname][namespace/page_name]=permission
     */
    function _aclGroupPermissions()
    {
        $AUTH_ACL = $this->_auth_loadACL(); //without %USER% replacement
        $gp = array();
        foreach($AUTH_ACL as $a)
        {
            // Don't parse comments
            if(preg_match('/^#/', $a))
                continue;
            if(preg_match('/^([^\s]+)\s@([^\s]+)\s(\d+)/', $a, $matches))
            {
                $gp[$matches[2]][$matches[1]] = $matches[3];
            }
        }
        $this->aclGroupPermissions = array();
        foreach($gp as $grpname => $permissions)
        {
            ksort($permissions);
            $this->aclGroupPermissions[urldecode($grpname)] = $permissions;
        }
    }

    /**
     * sets $this->aclUserPermissions in the form of a[username][namespace/page_name]=permission
     */
    function _aclUserPermissions()
    {
        global $AUTH_ACL;
        $up = array();
        foreach($AUTH_ACL as $a)
        {
            // Don't parse comments
            if(preg_match('/^#/', $a))
                continue;
            if(preg_match('/^([^\s]+)\s([^@\s]+)\s(\d+)/', $a, $matches))
            {
                $up[$matches[2]][$matches[1]] = $matches[3];
            }
        }
        $this->aclUserPermissions = array();
        foreach($up as $usrname => $permissions)
        {
            ksort($permissions);
            $this->aclUserPermissions[$usrname] = $permissions;
        }
    }

    /**
     * Build an Array in $this->group2user that associates user names with users
     * The users are sorted by last name
     */
    function _group2user()
    {
        $g2u = array();
        foreach(array_keys($this->groups) as $g)
            $g2u[$g] = array();
        foreach($this->users as $username => $properties)
        {
            foreach($properties['grps'] as $grpname)
                $g2u[$grpname][$username] = array_pop(explode(' ', $properties['name'])); // Store last name of user here for Sorting
        }
        $this->group2user = array();
        foreach($g2u as $grpname => $users)
        {
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
    function _userPermissions($username)
    {
        // Build regular expression for the username an its groups
        $userdata = $this->auth->getUserData($username);
        $this->username = $userdata['name'];
        $search_string = preg_quote(auth_nameencode($username), '/');
        foreach($userdata['grps'] as $g)
            $search_string .= '|@'.preg_quote(auth_nameencode($g), '/');
        $perm_regex = '/^([^\s]+)\s('.$search_string.')\s(\d+)/';
        // Search through permissions
        $AUTH_ACL = $this->_auth_loadACL(); //without user replacement
        $up = array();
        // $for_user holds permissions that are assigned explicitly to the user
        $for_user = array(); 
        foreach($AUTH_ACL as $a)
        {
            // Don't parse comments
            if(preg_match('/^#/', $a))
                continue;
            if(preg_match($perm_regex, $a, $matches))
            {
                $ns = str_replace('%USER%',auth_nameencode($username),$matches[1]); //replace %USER% with username
                $up[$ns] = (empty($up[$matches[1]])?0:$up[$matches[1]]) | $matches[3];
                if(substr($matches[2], 0, 1) != "@")  
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
    
        if(!is_readable($config_cascade['acl']['default'])) return array();
    
        $acl = file($config_cascade['acl']['default']);

        $out = array();
        foreach($acl as $line) {
            $line = trim($line);
            if(empty($line) || ($line{0} == '#')) continue; // skip blank lines & comments
            list($id,$rest) = preg_split('/[ \t]+/',$line,2);
    
            // substitute group wildcard (its 1:m)
            if(strstr($line, '%GROUP%')){
                // if user is not logged in, grps is empty, no output will be added (i.e. skipped)
                foreach((array) $USERINFO['grps'] as $grp){
                    $nid   = str_replace('%GROUP%',cleanID($grp),$id);
                    $nrest = str_replace('%GROUP%','@'.auth_nameencode($grp),$rest);
                    $out[] = "$nid\t$nrest";
                }
            } else {
                $out[] = "$id\t$rest";
            }
        }
    
        return $out;
    }
}

