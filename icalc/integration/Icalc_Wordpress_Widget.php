<?php

function icalc_register_custom_widget() {
	register_widget('Icalc_Wordpress_Widget');
}
add_theme_support('widgets-block-editor');
add_action('widgets_init', 'icalc_register_custom_widget');


class Icalc_Wordpress_Widget extends WP_Widget {

	public function __construct() {
		$widget_ops = array(
			'classname' => 'icalc_widget',
			'description' => __("Icalc Widget for interactive calculations"),
		);
		parent::__construct( 'icalc_widget', 'Icalc Calculations Widget', $widget_ops );
	}


	public function widget($args, $instance) {
		echo $args['before_widget'];
		// Display your custom content here
		$calculation = new \icalc\fe\Calculation( $instance["calculationId"] );
		echo $calculation->render();

		echo $args['after_widget'];
	}


	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'ICalc Custom Widget', 'icalc' );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'text_domain' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php

	}

	public function update( $new_instance, $old_instance ) {
		$instance          = [];
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

}

?>