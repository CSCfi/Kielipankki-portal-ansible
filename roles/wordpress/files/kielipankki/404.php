<?php
/*
	Template Name: 404 error page
*/

include 'init_page.php';
?>

<body>

<header class="header" role="banner">
<?php 
global $post;


$i18n = array(
    'MAIN_MENU' => array (
        'en' => 'Main menu - English',
        'fi' => 'Main menu - Finnish',
        'sv' => 'Main menu - Swedish'
    ),
    'SIDEBAR' => array (
        'en' => 'sidebar-4-eng',
        'fi' => 'sidebar-4-fin',
        'sv' => 'sidebar-4-swe'
    )
);


echo '<img src="'.get_template_directory_uri().'/images/Kielipankki_Kielipankki.png" alt="' . the_title_attribute( array( 'echo' => 0 ) ) . '"/>';

?>
</header>

<div class="wrapper nav-wrapper">
  <nav class="page-nav" role="navigation">
  <?php

$men = array(
	'theme_location'  => '',
	'menu'            => $i18n['MAIN_MENU'][$lang],
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
<div class="content lbluebg">
  <div class="container">
    <div class="leftcol">
    <?php

echo "<h1 class=\"first\">Sivua ei löydy. Page not found. Sidan hittas ej.</h1>";
	
	?>
	<div class="ccomme">
	<?php comments_template(); ?> 
	</div>
    </div>
    <div class="rightcol">
    <?php
	/* the sidebar */
	if ( function_exists('dynamic_sidebar')){
		dynamic_sidebar($i18n['SIDEBAR'][$lang]);
	}
	?>
    </div>
</div>
<?php
get_footer();
?>
</body>
</html>
