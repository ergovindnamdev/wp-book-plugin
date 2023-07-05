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
 * @class WPBook_Shortcode
 */
class WPBook_Shortcode {


	/**
	 * Constructor.
	 */
	public function __construct() {
		add_shortcode( 'book', array( $this, 'book_shortcode_fun' ) );
	}

	/**
	 * Shortcode Callback
	 *
	 * @param array  $atts Attributes array.
	 * @param string $content Shortcode Content.
	 *
	 * @return mixed
	 */
	public function book_shortcode_fun( $atts, $content = '' ) {
		$atts = shortcode_atts(
			array(
				'id'          => '',
				'author_name' => '',
				'year'        => '',
				'category'    => '',
				'tag'         => '',
				'publisher'   => '',
			),
			$atts
		);

		$args = array(
			'post_type' => 'book',
		);

		if ( ! empty( $atts['author_name'] ) && '' !== $atts['author_name'] ) {
			$auth_meta_query = array(
				'key'     => 'book_info_author_name',
				'compare' => '=',
				'value'   => $atts['author_name'],
			);
		}

		if ( ! empty( $atts['year'] ) && '' !== $atts['year'] ) {
			$year_meta_query = array(
				'key'     => 'book_info_year',
				'compare' => '=',
				'value'   => $atts['year'],
			);
		}

		if ( ! empty( $atts['publisher'] ) && '' !== $atts['publisher'] ) {
			$pub_meta_query = array(
				'key'     => 'book_info_publisher',
				'compare' => '=',
				'value'   => $atts['publisher'],
			);
		}

		if ( ! empty( $meta_query ) ) {
			$meta_query = array_merge( $meta_query, array( 'relation' => 'OR' ) );
		}

		if ( ! empty( $atts['category'] ) && '' !== $atts['category'] ) {
			$args = array_merge(
				$args,
				array(
					'tax_query' => array(
						'relation' => 'OR',
						array(
							'taxonomy' => 'book_category',
							'field'    => 'term_id',
							'terms'    => explode( ',', $atts['category'] ),
							'operator' => 'IN',
						),
					),
				)
			);
		}

		if ( ! empty( $atts['tag'] ) && '' !== $atts['tag'] ) {
			$args = array_merge(
				$args,
				array(
					'tax_query' => array(
						'relation' => 'OR',
						array(
							'taxonomy' => 'book_tag',
							'field'    => 'slug',
							'terms'    => explode( ',', $atts['tag'] ),
							'operator' => 'IN',
						),
					),
				)
			);
		}

		$bookids = $this->get_bookids_from_meta( $atts['author_name'], $atts['year'], $atts['publisher'] );
		if ( ! empty( $bookids ) ) {
			$book_ids = explode( ',', implode( ', ', $bookids ) . ', ' . $atts['id'] );
		} else {
			$book_ids = explode( ',', $atts['id'] );
		}

		if ( ! empty( $book_ids ) ) {
			$args = array_merge( $args, array( 'post__in' => $book_ids ) );
		}

		$book_query = new WP_Query( $args );

		$short_html = '';
		if ( $book_query->have_posts() ) {
			while ( $book_query->have_posts() ) {
				$book_query->the_post();

				$short_html .= "<div class='book-container'>";
				$short_html .= get_the_post_thumbnail();
				$short_html .= "<h2 class='wp-block-post-title'><a href='" . get_the_permalink() . "' target='_self'>" . get_the_title() . "</a></h2>
			<div class='wp-block-post-excerpt'>
                <p class='wp-block-post-excerpt__excerpt'>" . get_the_excerpt() . '</p>
            </div>
            </div>';

			}
		} else {
			$short_html .= '<div>No Data Available</div>';
		}
		wp_reset_postdata();

		return $short_html;
	}

	/**
	 * Function to get book ids by author name, publisher & year
	 *
	 * @param string $author_name Author name.
	 * @param string $publisher Publisher name.
	 * @param string $year Year.
	 *
	 * @return array
	 */
	public function get_bookids_from_meta( $author_name, $publisher, $year ) {
		global $wpdb;
		$where = '';
		if ( '' !== $author_name ) {
			$where .= $wpdb->prepare( ' OR author_name = %s', $author_name );
		}

		if ( '' !== $publisher ) {
			$where .= $wpdb->prepare( ' OR publisher = %s', $publisher );
		}

		if ( '' !== $year ) {
			$where .= $wpdb->prepare( ' OR year = %s', $year );
		}

		$book_ids = $wpdb->get_col( $wpdb->prepare( 'SELECT book_id FROM ' . $wpdb->prefix . 'book_meta WHERE 1=1 ' . $where, 1 ) );
		return $book_ids;
	}
}

$cp_mb = new WPBook_Shortcode();
