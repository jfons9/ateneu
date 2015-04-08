<?php
/**
 * Options for the tagentry Plugin
 */
$conf['tagsrc']       = 'All tags'; // where to get the tags from
$conf['namespace']    = 'tag';      // scan here for tags
$conf['table']        = false;      // arrange tags in a table
$conf['limit']        = 0;          // 0: unlimited
$conf['blacklist']    = 'tag-syntax llenguees lleguees llegues insert';    // space separated list
$conf['height']       = '';         // float(em|px|pt) ; <0: scale with entries, >0 fixed , 0 or empty: use CSS
$conf['tablerowcnt']  = 5;          // <td>'s per <tr>

//Setup VIM: ex: et ts=2 enc=utf-8 :
