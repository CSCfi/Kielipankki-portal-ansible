<?php
/**
 * Template Name: custom Vanilla Forum Template FI
 *
 * A custom page template for a Vanilla Forum.
 *
 * The "Template Name:" bit above allows this to be selectable
 * from a dropdown menu on the edit page screen.
 *
 */

include 'init_page.php';

?>

<body>
<header class="header" role="banner">
<?php 
global $post;
if (has_post_thumbnail()){
	echo get_the_post_thumbnail( $post->ID, 'full' );
} else {
	echo '<img src="'.get_template_directory_uri().'/images/Kielipankki_Kielipankki.png" alt="' . the_title_attribute( array( 'echo' => 0 ) ) . '"/>';
	}
?>
</header>
<div class="wrapper nav-wrapper">
  <nav class="page-nav" role="navigation">
  <?php

$men = array(
	'theme_location'  => '',
	'menu'            => 'Main menu - Finnish',
	'container'       => 'div',
	'container_class' => '',
	'container_id'    => '',
	'menu_class'      => 'menu',
	'menu_id'         => '',
	'echo'            => true,
	'fallback_cb'     => 'wp_page_menu',
	'before'          => '',
	'after'           => '',
	'link_before'     => '',
	'link_after'      => '',
	'items_wrap'      => '<ul id="%1$s" class="nav clearfix">%3$s</ul>',
	'depth'           => 0,
	'walker'          => ''
);

wp_nav_menu( $men );
  
  ?>
    <div class="nav-mobile" id="mobile-nav"> <a href="#mobile-nav" class="mobile-nav-trigger"><span class="fontawesome-reorder"></span></a> </div>
  </nav>
</div>
<?php
the_content();

get_footer();
?>
</body>
