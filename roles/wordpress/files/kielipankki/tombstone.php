<?php
/*
	Template Name: Tombstone
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


if (has_post_thumbnail()){
	echo get_the_post_thumbnail( $post->ID, 'full' );
} else {
/*
no post_thumbnail
check if page has parent, if it does, check if parent does have post_thumbnail
*/
if(intval($post->post_parent) > 0){
	$ppthumb = get_the_post_thumbnail( intval($post->post_parent), 'full' );
	if($ppthumb){
		echo $ppthumb;
	} else {
/* parent has no post_thumbnail set, show default image */
		echo '<img src="'.get_template_directory_uri().'/images/Kielipankki_Kielipankki.png" alt="' . the_title_attribute( array( 'echo' => 0 ) ) . '"/>';
	}
} else {
	/* no post parent and no post_thumbnail set -> show default image */
	echo '<img src="'.get_template_directory_uri().'/images/Kielipankki_Kielipankki.png" alt="' . the_title_attribute( array( 'echo' => 0 ) ) . '"/>';
}
	}
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

	/* the loop */

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		the_content();
	} // end while
} // end if

if ($show_last_modified) {
    make_last_modified($lang);
}

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
