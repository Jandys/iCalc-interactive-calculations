<?php

function icalc_register_custom_widget() {
	register_widget('IcalcWordpressWidget');
}

add_action('widgets_init', 'icalc_register_custom_widget');


class IcalcWordpressWidget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'icalc_widget', // Base ID
			__( 'ICalc Custom Widget', 'icalc' ), // Name
			[
				'description' => __( 'A custom widget that displays data from my plugin.', 'icalc' ),
			]
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		// Get the data from your plugin
//		$plugin_data = my_plugin_get_data();

		// Generate the HTML output using the plugin data
		echo '<div class="my-custom-widget">';
		echo '<ul>';
//		foreach ( $plugin_data as $item ) {
//			echo '<li>' . esc_html( $item ) . '</li>';
//		}
		echo '<p>This is my custom widget and i rock and roll.</p>';
		echo '</ul>';
		echo '</div>';

		echo $args['after_widget'];
	}


	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'ICalc Custom Widget', 'icalc' );
		echo '<p>THis is custom formmmmm</p>';
	}

	public function update( $new_instance, $old_instance ) {
		$instance          = [];
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

}

?>