<?php
/**
 * M1 header, included in the main and detail files.  Also includes all menu information.
 */

// must be run from within DokuWiki
if (!defined('DOKU_INC')) die();
?>

<div id="dokuwiki__header">
 
        <div id="m1-header-left"> <!--Left hand mobile menu -->
        	<a id="m1-menu-ltrig" href="#m1-menu-left"><i class="fa fa-lg fa-bars"></i></a>
        </div>
        
        <div id="m1-header-center"> <!--Logo and main center image -->
            	<?php
        		echo "<h1><a href=\"".DOKU_BASE."\" class=\"m1-logo\" accesskey=\"h\"";
       			 if (file_exists(DOKU_TPLINC."images/logo.png")){
            	//user defined PNG
            	echo "><img src=\"".DOKU_TPL."images/logo.png\" class=\"m1-imglogo\" alt=\"PaddlingABC\"/></a></h1>\n";
        		}elseif (file_exists(DOKU_TPLINC."images/logo.gif")){
            	//user defined GIF
            	echo "><img src=\"".DOKU_TPL."images/logo.gif\" class=\"m1-imglogo\" alt=\"PaddlingABC\"/></a></h1>\n";
        		}elseif (file_exists(DOKU_TPLINC."images/logo.jpg")){
            	//user defined JPG
            	echo "><img src=\"".DOKU_TPL."images/logo.jpg\" class=\"m1-imglogo\" alt=\"PaddlingABC\"/></a></h1>\n";
        		}else{
            	//default
            	echo " class=\"m1-txtlogo\">".hsc($conf["title"])."</a></h1>\n";
        		}
        		?>
        	</div>
  	  		
        <div id="m1-header-right"> <!--Right hand mobile menu -->
        	<a id="m1-menu-strig" href="#"><i class="fa fa-lg fa-search"></i></a>
        	<a id="m1-menu-rtrig" href="#m1-menu-right"><i class="fa fa-lg fa-gear"></i></a>
        </div>
    
        
        <!--NON-MOBILE MENU-->
        <div id="m1-menu"> <!--Menu displayed for tablet and desktop viewing, hidden on phone size -->
      		<ul>
     			<li><a href='#'>Portfolio</a></li>
     			<li class='has-sub m1-desktop'><a href='#'>Products</a> <!--Hidden on tablet due to m1-desktop class -->
       				 <ul>
                 		<li><a href='#'>Sub 1</a></li>
                 		<li><a href='#'>Sub 2</a></li>
                 		<li><a href='#'>Sub 3</a></li>
              		</ul>
           		</li>
     			<li><a href='#'>Contact</a></li>
    		 	<li><a href='#'>About</a></li>
    			<li><a href='#'>Help</a></li>
  			</ul>
  	  	</div>
  	  	
  	  	<div id="m1-search">
  	    <?php tpl_searchform() ?>
		</div>
		
		 <!-- BREADCRUMBS -->
    <?php if($conf['breadcrumbs'] || $conf['youarehere']): ?>
        <div class="breadcrumbs">
            <?php if($conf['youarehere']): ?>
                <div class="youarehere"><?php tpl_youarehere() ?></div>
            <?php endif ?>
            <?php if($conf['breadcrumbs']): ?>
                <div class="trace"><?php tpl_breadcrumbs() ?></div>
            <?php endif ?>
        </div>
    <?php endif ?>
  	  	
	  		
</div><!-- /dokuwiki__header -->