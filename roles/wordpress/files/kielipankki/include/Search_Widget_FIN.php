<?php /**
 * Adds Search_Widget_FIN widget.
 */
class Search_Widget_FIN extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'Search_Widget_FIN', // Base ID
			__( 'Search widget FIN', 'text_domain' ), // Name
			array( 'description' => __( 'Search widget, FIN', 'text_domain' ), ) // Args
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
    <h3>'.$instance['title'].'</h3>
	<form role="search" method="get" class="search-form" action="https://www.kielipankki.fi/">
    <input type="text" placeholder="Hae sivustolta..." name="s">
    </form>
<!--	<div class="openerbox">
	<p class="infoicon"><span class="question-mark"></span><a href="#">Usein haetut termit</a></p>
	<div class="openercontent">';
	sm_list_popular_searches();
	echo '</div>
	</div>
    <p class="infoicon"><span class="question-mark"></span><a href="#">Yleistä Kielipankista</a></p>
-->
    </div>
	';
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

} // class Search_Widget_FIN