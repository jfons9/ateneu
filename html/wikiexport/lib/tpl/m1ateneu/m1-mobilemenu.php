<?php
/**
 * M1 Mobile Menu, included in the main and detail files.  Also includes all mobile menu information.
 */

// must be run from within DokuWiki
if (!defined('DOKU_INC')) die();
?>

	 <!-- ********** Mobile-Left ********** -->   

<!--
         <div id="m1-menu-left">
   	  <ul>
      <li><a href="#">Menu Item 1</a></li>
      <li><a href="#">Menu Item 2</a></li>
      <li><a href="#">Menu Item 3</a></li>
      <li><a href="#">Menu Item 4</a></li>
  	  </ul>
	</div> -->
         <?php
            $NS = getNS($ID); 
            //echo "<br><br><br>";
            $helper = plugin_load('helper', 'ateneuplus');        
            $menuleft = $helper->get_menu($ID, 'm1-menu-left');
          //  $titol = $helper->getTitolCurs($ID);  
            print('<div id="m1-menu-left"><ul>');
            print ( $menuleft);
            print('</ul></div>');
            //echo "<br><br>";          
	 ?>
         
        
         
	 <!-- ********** Mobile-Right ********** -->   
         
      <div id="m1-menu-right">
   	    <ul>       
            <?php _tpl_toolsevent('usertools', array(
                'admin'     => tpl_action('admin', 1, 'li', 1),
                'profile'   => tpl_action('profile', 1, 'li', 1),
                'register'  => tpl_action('register', 1, 'li', 1),
                'login'     => tpl_action('login', 1, 'li', 1),
                )); ?>  
            <?php _tpl_toolsevent('pagetools', array(
                'edit'      => tpl_action('edit', 1, 'li', 1),
                'revisions' => tpl_action('revisions', 1, 'li', 1),
                'revert'    => tpl_action('revert', 1, 'li', 1),
                )); ?>  
            <?php _tpl_toolsevent('sitetools', array(
                'recent'    => tpl_action('recent', 1, 'li', 1),
                'media'     => tpl_action('media', 1, 'li', 1),
                'index'     => tpl_action('index', 1, 'li', 1),
                )); ?>
        </ul>
	  </div>
