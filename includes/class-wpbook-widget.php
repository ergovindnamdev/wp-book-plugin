<?php
/**
 * WP Book Plugin setup
 *
 * @package wp-book
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main WPBook Plugin Class.
 *
 * @class WPBook_Widget
 */
class WPBook_Widget extends WP_Widget {


	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'WPBook_Widget',
			__( 'Books Widget', 'wp-book' ),
			array(
				'description' => __( 'WP Books Widget', 'wp-book' ),
			)
		);
	}

	/**
	 * Function to return widget output.
	 *
	 * @param array $args Arguments for widget.
	 * @param array $instance Instance array of widget.
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {

		$title           = apply_filters( 'widget_title', $instance['title'] );
		$book_cats       = (array) $instance['bookCats'];
		$categories_list = implode( ',', $book_cats );

		echo esc_html( $args['before_widget'] );

		if ( $title ) {
			echo esc_html( $args['before_title'] . $title . $args['after_title'] );
		}

		$args = array(
			'post_type' => 'book',
			'taxonomy'  => 'book_category',
		);

		echo do_shortcode( "[book category='" . $categories_list . "']" );

		echo esc_html( $args['after_widget'] );
	}

	/**
	 * Function to manage widget form values.
	 *
	 * @param array $instance Instance of widget.
	 *
	 * @return void
	 */
	public function form( $instance ) {
		$title                = isset( $instance['title'] ) ? $instance['title'] : 'Default Title';
		$instance['bookCats'] = (array) $instance['bookCats'];

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'bookCats' ) ); ?>"><?php esc_html_e( 'Select Categories you want to show:' ); ?></label><br />
			<?php
			$args  = array(
				'post_type' => 'book',
				'taxonomy'  => 'book_category',
			);
			$terms = get_terms( $args );
			foreach ( $terms as $id => $name ) {
				$checked = '';
				if ( in_array( $name->term_id, $instance['bookCats'], true ) ) {
					$checked = "checked='checked'";
				}
				?>
				<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'bookCats' ) . '-' . $name->term_id ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'bookCats' ) ); ?>[]" value="<?php echo esc_attr( $name->term_id ); ?>" <?php echo esc_html_e( $checked ); ?> />
				<label for="<?php echo esc_attr( $this->get_field_id( 'bookCats' ) . '-' . $name->term_id ); ?>"><?php echo esc_html_e( $name->name ); ?></label><br />
						<?php } ?>
		</p>
		<?php
	}

	/**
	 * Function for update widget field.
	 *
	 * @param array $new_instance The new instance of the widget.
	 * @param array $old_instance The old instance of the widget.
	 *
	 * @return array The updated instance of the widget.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance             = array();
		$instance['title']    = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['bookCats'] = (array) $new_instance['bookCats'];

		return $instance;
	}

}

new WPBook_Widget();

/**
 * Main WPBook Widget Class.
 *
 * @class WPBook_Widget_Init
 */
class WPBook_Widget_Init {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'widgets_init', array( $this, 'wp_book_widget_init' ) );
	}

	/**
	 * Function to register widget.
	 */
	public function wp_book_widget_init() {
		register_widget( 'WPBook_Widget' );
	}
}

new WPBook_Widget_Init();
