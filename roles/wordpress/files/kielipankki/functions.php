<?php
add_action( 'wp_enqueue_scripts', 'jk_load_dashicons' );
function jk_load_dashicons() {
    wp_enqueue_style( 'dashicons' );
}
add_action( 'init', 'create_post_type' );

add_action('after_setup_theme', 'remove_admin_bar');


// Only show admin bar on top, if you are admin, not for normal users.
function remove_admin_bar() {
if (current_user_can('subscriber')) {
  show_admin_bar(false);
}
}



/*
 return set of laundry tags from custom fields.
*/

function create_post_type() {
  register_post_type( 'uutiset',
    array(
      'labels' => array(
        'name' => __( 'Uutiset' ),
        'singular_name' => __( 'uutiset' ),
	'menu_name' => __('Uutiset (fi)')
      ),
      'public' => true,
      'has_archive' => true,
	  'menu_position' => 5,
	  'menu_icon' => 'dashicons-media-text',
	  'taxonomies' => array('category'),
	  'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions' )
    )
  );

  register_post_type( 'news',
    array(
      'labels' => array(
        'name' => __( 'News' ),
        'singular_name' => __( 'news' ),
	'menu_name' => __('News (en)')
      ),
      'public' => true,
      'has_archive' => true,
	  'menu_position' => 6,
	  'menu_icon' => 'dashicons-media-text',
	  'taxonomies' => array('category'),
	  'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions' )
    )
  );

/* what is this? -- mma 4.7.19
$args = array( 
      'hierarchical' => true,
      'labels' => $labels,
      'show_ui' => true,
      'show_admin_column' => true,
      'query_var' => true, 
      );

  //  register_taxonomy_for_object_type( 'category', 'uutiset' );
  // register_taxonomy('category',array('news','uutiset'), $args);
*/

}



function custom_theme_setup() {
	add_theme_support( 'post-thumbnails', array( 'page', 'post', 'uutiset', 'news' ) );
	add_theme_support( 'menus');
	add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );    

	register_sidebar( array(
        'name' => __( 'Default Sidebar Fin', '' ),
        'id' => 'sidebar-1-fin',
        'description' => __( 'Default sidebar Finnish', 'theme-slug' ),
        'before_widget' => '',
	'after_widget'  => '',
	'before_title'  => '<div>',
	'after_title'   => '</div>',
    ) );
	register_sidebar( array(
        'name' => __( 'Default Sidebar Eng', '' ),
        'id' => 'sidebar-1-eng',
        'description' => __( 'Default sidebar English', 'theme-slug' ),
        'before_widget' => '',
	'after_widget'  => '',
	'before_title'  => '<div>',
	'after_title'   => '</div>',
    ) ); 
	register_sidebar( array(
        'name' => __( 'Default Sidebar Swe', '' ),
        'id' => 'sidebar-1-swe',
        'description' => __( 'Default sidebar Swedish', 'theme-slug' ),
        'before_widget' => '',
	'after_widget'  => '',
	'before_title'  => '<div>',
	'after_title'   => '</div>',
    ) ); 

    	register_sidebar( array(
        'name' => __( 'Footer Fin', '' ),
        'id' => 'sidebar-2-fin',
        'description' => __( 'Footer Finnish', 'theme-slug' ),
        'before_widget' => '',
	'after_widget'  => '',
	'before_title'  => '<div>',
	'after_title'   => '</div>',
    ) );
        register_sidebar( array(
        'name' => __( 'Footer Eng', '' ),
        'id' => 'sidebar-2-eng',
        'description' => __( 'Footer English', 'theme-slug' ),
        'before_widget' => '',
	'after_widget'  => '',
	'before_title'  => '<div>',
	'after_title'   => '</div>',
    ) );
        register_sidebar( array(
        'name' => __( 'Footer Swe', '' ),
        'id' => 'sidebar-2-swe',
        'description' => __( 'Footer Swedish', 'theme-slug' ),
        'before_widget' => '',
	'after_widget'  => '',
	'before_title'  => '<div>',
	'after_title'   => '</div>',
    ) );

	register_sidebar( array(
        'name' => __( 'News Sidebar Fin', '' ),
        'id' => 'sidebar-3-fin',
        'description' => __( 'News sidebar Finnish', 'theme-slug' ),
        'before_widget' => '',
	'after_widget'  => '',
	'before_title'  => '<div>',
	'after_title'   => '</div>',
    ) );
	register_sidebar( array(
        'name' => __( 'News Sidebar Eng', '' ),
        'id' => 'sidebar-3-eng',
        'description' => __( 'News sidebar English', 'theme-slug' ),
        'before_widget' => '',
	'after_widget'  => '',
	'before_title'  => '<div>',
	'after_title'   => '</div>',
    ) ); 

	register_sidebar( array(
        'name' => __( '404 Sidebar Fin', '' ),
        'id' => 'sidebar-4-fin',
        'description' => __( '404 sidebar Finnish', 'theme-slug' ),
        'before_widget' => '',
	'after_widget'  => '',
	'before_title'  => '<div>',
	'after_title'   => '</div>',
    ) );
	register_sidebar( array(
        'name' => __( '404 Sidebar Eng', '' ),
        'id' => 'sidebar-4-eng',
        'description' => __( '404 sidebar English', 'theme-slug' ),
        'before_widget' => '',
	'after_widget'  => '',
	'before_title'  => '<div>',
	'after_title'   => '</div>',
    ) );
	register_sidebar( array(
        'name' => __( '404 Sidebar Swe', '' ),
        'id' => 'sidebar-4-swe',
        'description' => __( '404 sidebar Swedish', 'theme-slug' ),
        'before_widget' => '',
	'after_widget'  => '',
	'before_title'  => '<div>',
	'after_title'   => '</div>',
    ) );
}

function wpb_list_child_pages() { 

global $post; 

if ( is_page() && $post->post_parent )

   $childpages = wp_list_pages( 'sort_column=post_title&title_li=&child_of=' . $post->post_parent . '&echo=0' );
else
	$childpages = wp_list_pages( 'sort_column=post_title&title_li=&child_of=' . $post->ID . '&echo=0' );

if ( $childpages ) {

   $string = '<ul>' . $childpages . '</ul>';
}

return $string;

}

add_shortcode('wpb_childpages', 'wpb_list_child_pages');

add_action( 'after_setup_theme', 'custom_theme_setup' );
function get_site_path(){
  return site_url();
}
add_shortcode('SITE_URL','get_site_path');
function get_url_by_id( $atts ){
	if(isset($atts['id'])){
		$pid = intval($atts['id']);
		$perm = get_permalink($pid);
	return $perm;
	}
}
add_shortcode( 'GET_PAGE_URL', 'get_url_by_id' );

//Test by Martin:
function create_reference_link(){
	 global $lang;
	 return "<b>".$lang."</b>";
}
add_shortcode('MAKE_KIELIPANKKI_REF', 'create_reference_link');


add_filter('widget_text', 'do_shortcode');
/* WIDGETS */
// register Foo_Widget widget
include('include/News_Widget_FIN.php');
include('include/Search_Widget_FIN.php');
include('include/News_Widget_ENG.php');
include('include/Kielipankki_text_widget.php');
function register_news_widget() {
    	register_widget( 'News_Widget_FIN' );
	register_widget( 'News_Widget_ENG' );
	register_widget( 'Search_Widget_FIN' );
	register_widget( 'kielipankki_text_widget' );
}
add_action( 'widgets_init', 'register_news_widget' );

/* Set From Email Address and Name */
add_filter('wp_mail_from', function ($addr) {
        return 'kielipankki@csc.fi';
     }
);
add_filter('wp_mail_from_name', function ($name) {
        return 'Kielipankki';
     }
);

/* Change sorting to ascending of the Events calendar*/
add_filter('tribe_events_views_v2_view_list_repository_args',
        function ( $args ) {
        $args['orderby'] = 'date_start';
        $args['order'] = 'ASC';
        return $args;
        }
);

?>
