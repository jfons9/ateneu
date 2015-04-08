<?php
$lang['namespaces'] = 'Enter a semicolon-delimited list of namespaces. Any search done ' .
	'in these namespaces will be limited to the namespace. Lower-level namespaces override ' .
	'higher-level namespaces. For example, "myns;myns:sub" will cause searches in myns or ' .
	'myns:whatever to be limited to myns, while searches in myns:sub:other will be limited to ' .
	'myns:sub. The special value @all will limit searches in all namespaces (even sub namespaces). ' .
	'The special value @depth[n] will limit searches to a certain depth. For example, using @depth1, ' .
	'searches in myns:sub:sub2 will be limited to results from myns:sub. @base is equivalent to @depth0.';
