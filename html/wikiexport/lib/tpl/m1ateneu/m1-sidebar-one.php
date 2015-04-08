<?php
/**
 * M1 Sidebar One, farthest right sidebar and first one to display with media queries
 */

// must be run from within DokuWiki
if (!defined('DOKU_INC')) die();
?>

		<div class="m1-sidebox m1-side-register">
			<ul>
			   <?php _tpl_toolsevent('usertools', array(
                    'admin'     => tpl_action('admin', 1, 'li', 1),
                    'profile'   => tpl_action('profile', 1, 'li', 1),
                    'register'  => tpl_action('register', 1, 'li', 1),
                    'login'     => tpl_action('login', 1, 'li', 1),
                )); ?>
                        
        	</ul>
        </div>
        
		<!--Uncomment this to include a wiki page in your sidebar
		<div class="m1-sidebox">
        <h3>Sidebar Title</h3>
        <?php tpl_include_page("wiki:somepage",1, 1); ?>
        </div>
        -->
        
        <div class="m1-sidebox">
  		<h3>Sidebar Title</h3>
  		<p>Content here...Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce augue augue, tristique a leo sit amet, laoreet malesuada purus. Etiam est dui, adipiscing at ultricies vel, gravida sit amet turpis. Duis erat metus, bibendum ut molestie quis, tempor quis ligula. Phasellus mollis auctor tellus, et imperdiet sem tincidunt eu. In quis odio nec nulla vestibulum semper a sed ipsum. Interdum et malesuada fames ac ante ipsum primis in faucibus. Integer et turpis augue. Pellentesque mattis, tortor sed consectetur fringilla, sapien velit sagittis libero, auctor mollis quam eros id lectus. Nulla id aliquam dolor.</p>
  		</div>
        

  		<div class="m1-sidebox m1-side-tools">
   	    <h3>Tools</h3>
   	    <ul>          
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
  		
  		
  		