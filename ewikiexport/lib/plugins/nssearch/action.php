<?php
/**
 * nssearch Plugin for DokuWiki / action.php
 *
 * @license None. This is free with no conditions.
 * @author  Eli Fenton
 */

if (!defined('DOKU_INC')) {die();}
if (!defined('DOKU_PLUGIN')) {define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');}
require_once DOKU_PLUGIN . 'action.php';

class action_plugin_nssearch extends DokuWiki_Action_Plugin
{
	function getInfo() {return array('author' => 'Eli Fenton', 'name' => 'Namespace Search Plugin', 'url' => 'http://dokuwiki.org/plugin:nssearch');}

	function register(&$controller)
	{
		// Edit the query before doing a string search.
		$controller->register_hook('SEARCH_QUERY_FULLPAGE', 'BEFORE', $this, 'handleQuery');
		// The page results don't have an edittable query for some reason, so filter invalid results after the search.
		$controller->register_hook('SEARCH_QUERY_PAGELOOKUP', 'AFTER', $this, 'filterPages');
	}

	function getLastCrumb()
	{
		$br = breadcrumbs();
		$lastcrumb = '';
		foreach ($br as $a=>$b) $lastcrumb=$a;
		return $lastcrumb;
	}
	function getBaseNs($id)
	{
		return preg_replace('/:.*$/', '', getNS(cleanID($id)));
	}
	function getDepthNs($id, $depth)
	{

		$a = explode(':', getNS(cleanID($id)));
		array_splice($a, 1+(int)$depth);
		return implode(':', $a);
	}

	function handleQuery(&$event, $param)
	{          
		$ns = $this->getLimitNs($this->getLastCrumb());                
           
                $this->helper = plugin_load('helper', 'ateneuplus');
                $cami =  str_replace(":", "/", $ns);
                $titol = $this->helper->get_titol($cami,2);
               print 'Trobat a: <b>'. $titol.'</b>';
             
               //echo " fffff ".$event->data['query'];
		if ($ns && $event->data['query'])
        		$event->data['query'] = 'ns:' . $ns . ' ' . $event->data['query'];
	}

	// You can't edit the query before a page search, so instead we have to filter the results after the search.
	function filterPages(&$event, $param)
	{
		$ns = $this->getLimitNs($this->getLastCrumb());
		if (!$ns)
			return;
		$newresult = array();
		foreach ($event->result as $a=>$b)
		{
			if ($ns.':' == substr(cleanID($a), 0, strlen($ns)+1))
				$newresult[$a] = $b;
		}
		$event->result = $newresult;
	}

	function getLimitNs($id)
	{
		$nslist = explode(';', $this->getConf('namespaces'));
		rsort($nslist);

		if ($nslist[0] == '@all')
			return getNS($id);
		if ($nslist[0] == '@base')
                            $cami =  str_replace(":", "/", $id);
                            $this->helper = plugin_load('helper', 'ateneuplus');
                            $base = $this->helper->get_base($cami);  
                            echo $base."<br>";
			//return $this->getBaseNs($id);
			return $base;
        
		if (substr($nslist[0],0,6) == '@depth')
			return $this->getDepthNs($id, substr($nslist[0],6));

		foreach ($nslist as $nstest)
		{
			if ($nstest.':' == substr($id, 0, strlen($nstest)+1))
				return $nstest;
		}
	}
}
