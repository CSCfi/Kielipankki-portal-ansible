<?php /**
 * Adds News_Widget_ENG widget.
 */
class News_Widget_ENG extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'News_Widget_ENG', // Base ID
			__( 'News widget ENG', 'text_domain' ), // Name
			array( 'description' => __( 'List latest news, ENG', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
	echo '<div class="lightbox">
    <h3>News</h3>
    <ul>
	';
	$my_args = array(
	'posts_per_page'   => 5,
	'offset'           => 0,
	'category'         => '',
	'category_name'    => '',
	'orderby'          => 'date',
	'order'            => 'DESC',
	'post_type'        => 'news',
	'post_status'      => 'publish',
	'suppress_filters' => true );
	$news_array = get_posts( $my_args );
	  foreach($news_array as $news){
		  $perm = get_permalink( $news->ID );
	  echo '<li><a href="'.$perm.'">'.$news->post_title.' ('.mysql2date('j.n.Y', $news->post_date).')</a></li>';
	  }
	echo ' </ul>
    <p><a href="'.site_url().'/news">More news</a></p>
    </div>';
	
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( '', 'text_domain' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

} // class News_Widget_ENG