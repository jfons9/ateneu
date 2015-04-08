<?php $diagResults = array (
  'Client' => 'Mozilla/5.0 (X11; U; Linux i686; ca; rv:1.9.2.3) Gecko/20100401 SUSE/3.6.3-1.2 Firefox/3.6.3',
  'Magic Quotes Enabled' => 'Yes',
  'GD Enabled' => 'Yes',
  'Upload Max Size' => '10M',
  'Memory Limit' => '64M',
  'Max execution time' => '1000',
  'Safe Mode' => '0',
  'Safe Mode GID' => '0',
  'Xml parser enabled' => '1',
  'MCrypt Enabled' => 'No',
  'Serveur OS' => 'SunOS',
  'Session Save Path' => '',
  'Session Save Path Writeable' => false,
  'PHP Version' => '5.2.4',
  'Locale' => 'es_MX.ISO8859-1',
  'Directory Separator' => '/',
  'Upload Tmp Dir Writeable' => true,
  'PHP Upload Max Size' => 10485760,
  'PHP Post Max Size' => 10485760,
  'AJXP Upload Max Size' => '0',
  'Users enabled' => 1,
  'Guest enabled' => 0,
  '[Server, logs, conf]' => '[1,1,1]',
  'Zlib Enabled' => 'Yes',
);$outputArray = array (
  0 => 
  array (
    'name' => 'AjaXplorer version',
    'result' => false,
    'level' => 'info',
    'info' => 'AJXP version : 2.6',
  ),
  1 => 
  array (
    'name' => 'Client Browser',
    'result' => false,
    'level' => 'info',
    'info' => 'Current client Mozilla/5.0 (X11; U; Linux i686; ca; rv:1.9.2.3) Gecko/20100401 SUSE/3.6.3-1.2 Firefox/3.6.3',
  ),
  2 => 
  array (
    'name' => 'Magic quotes',
    'result' => false,
    'level' => 'info',
    'info' => 'Magic quotes enabled : 1',
  ),
  3 => 
  array (
    'name' => 'PHP error level',
    'result' => false,
    'level' => 'info',
    'info' => 'E_ERROR | E_WARNING | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE',
  ),
  4 => 
  array (
    'name' => 'PHP GD version',
    'result' => true,
    'level' => 'warning',
    'info' => 'GD is required for generating thumbnails',
  ),
  5 => 
  array (
    'name' => 'PHP Limits variables',
    'result' => false,
    'level' => 'info',
    'info' => '<b>Testing configs</b>
Upload Max Size=10M
Memory Limit=64M
Max execution time=1000
Safe Mode=0
Safe Mode GID=0
Xml parser enabled=1',
  ),
  6 => 
  array (
    'name' => 'MCrypt enabled',
    'result' => false,
    'level' => 'warning',
    'info' => 'MCrypt is required for generating publiclets',
  ),
  7 => 
  array (
    'name' => 'PHP operating system',
    'result' => false,
    'level' => 'info',
    'info' => 'Current operating system SunOS',
  ),
  8 => 
  array (
    'name' => 'PHP Session',
    'result' => false,
    'level' => 'error',
    'info' => 'The temporary folder used by PHP to save the session data is either incorrect or not writeable! Please check : ',
  ),
  9 => 
  array (
    'name' => 'PHP version',
    'result' => true,
    'level' => 'error',
    'info' => 'Minimum required version is PHP 5.0.0, PHP 5.2 or higher recommended when using foreign language',
  ),
  10 => 
  array (
    'name' => 'Server charset encoding',
    'result' => true,
    'level' => 'error',
    'info' => 'You must set a correct charset encoding in your locale definition in the form: en_us.UTF-8. Please refer to setlocale man page. If your detected locale is C, please check <a href="http://www.ajaxplorer.info/documentation/chapter-8-faq/#c87">http://www.ajaxplorer.info/documentation/chapter-8-faq/#c87</a>. ',
  ),
  11 => 
  array (
    'name' => 'Upload particularities',
    'result' => false,
    'level' => 'info',
    'info' => '<b>Testing configs</b>
Upload Tmp Dir Writeable=1
PHP Upload Max Size=10485760
PHP Post Max Size=10485760
AJXP Upload Max Size=0',
  ),
  12 => 
  array (
    'name' => 'Users Configuration',
    'result' => false,
    'level' => 'info',
    'info' => 'Current config for users',
  ),
  13 => 
  array (
    'name' => 'Required writeable folder',
    'result' => false,
    'level' => 'info',
    'info' => '[1,1,1]',
  ),
  14 => 
  array (
    'name' => 'Zlib extension (ZIP)',
    'result' => false,
    'level' => 'info',
    'info' => 'Extension enabled : 1',
  ),
  15 => 
  array (
    'name' => 'Filesystem Plugin
 Testing repository : Default Files',
    'result' => true,
    'level' => 'error',
    'info' => '',
  ),
  16 => 
  array (
    'name' => 'Filesystem Plugin
 Testing repository : data',
    'result' => true,
    'level' => 'error',
    'info' => '',
  ),
  17 => 
  array (
    'name' => 'Filesystem Plugin
 Testing repository : Wikiform root',
    'result' => true,
    'level' => 'error',
    'info' => '',
  ),
  18 => 
  array (
    'name' => 'Filesystem Plugin
 Testing repository : Root',
    'result' => true,
    'level' => 'error',
    'info' => '',
  ),
); ?>