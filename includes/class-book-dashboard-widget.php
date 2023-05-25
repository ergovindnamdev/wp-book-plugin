<?php
/**
 * WP Book Plugin setup
 *
 * @package wp-book
 * @since   1.0.0
 */
defined('ABSPATH') || exit;
/**
 * Main WPBook Plugin Class.
 *
 * @class WPBook_Dashboard_Widget_init
 */

class WPBook_Dashboard_Widget_init
{

    public function __construct()
    {
        add_action('wp_dashboard_setup', array($this, 'wp_book_dashboard_widget_init'));
    }

    public function wp_book_dashboard_widget_init()
    {
        wp_add_dashboard_widget(
            'wp_book_dashboard_widget',
            esc_html__('WP Book Categories', 'wp-book'),
            array($this, 'wp_book_dashboard_widget_render')
        );
    }

    public function wp_book_dashboard_widget_render()
    {
        ?>
        <ul>
            <?php wp_list_categories(
                array(
                    'orderby' => 'name',
                    'show_count' => true,
                    'taxonomy' => 'book_category',
                    'number' => 5,
                    'hide_title_if_empty'=>true,
                    'title_li' => 'Book Categories'
                )); ?>
        </ul>
        <?php
    }
}

new WPBook_Dashboard_Widget_init();