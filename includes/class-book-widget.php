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
 * @class WPBook_Widget
 */
class WPBook_Widget extends WP_Widget
{

    /**
     * Constructor.
     */
    function __construct()
    {
        parent::__construct(
            'WPBook_Widget',
            __('Books Widget', 'wp-book'),
            array('description' => __('WP Books Widget', 'wp-book'),
            )
        );
    }

    /**
     * @args array
     * @return mixed
     * Function to return widget output
     */
    public function widget($args, $instance)
    {
        
        $title = apply_filters('widget_title', $instance['title']);
        $bookCats = (array)$instance['bookCats'];
        $categories_list = implode(",", $bookCats);
       
        echo $args['before_widget'];

        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        $args = array('post_type' => 'book', 'taxonomy' => 'book_category', );
        
        echo do_shortcode("[book category='".$categories_list."']");

        echo $args['after_widget'];
    }

    /**
     * @args array
     * @return array
     * Function to manage widget form values
     */
    public function form($instance)
    {
        $title = isset($instance['title']) ? $instance['title'] : 'Default Title';
        $instance['bookCats'] = (array)$instance['bookCats'];

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('bookCats'); ?>"><?php _e('Select Categories you want to show:'); ?></label><br />
            <?php $args = array(
                'post_type' => 'book',
                'taxonomy' => 'book_category',
            );
            $terms = get_terms($args);
            foreach ($terms as $id => $name) {
                $checked = "";
                if (in_array($name->term_id, $instance['bookCats'])) {
                    $checked = "checked='checked'";
                }
                ?>
                <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('bookCats').'-'.$name->term_id; ?>"
                    name="<?php echo $this->get_field_name('bookCats'); ?>[]" value="<?php echo $name->term_id; ?>" <?php echo $checked; ?> />
                <label for="<?php echo $this->get_field_id('bookCats').'-'.$name->term_id; ?>"><?php echo $name->name; ?></label><br />
            <?php } ?>
        </p>
        <?php
    }

    /**
     * @args array
     * @return mixed
     * Function for update widget field
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['bookCats'] = (array)$new_instance['bookCats'];
        
        return $instance;
    }

}

new WPBook_Widget();

class WPBook_Widget_init
{

    public function __construct()
    {
        add_action('widgets_init', array($this, 'wp_book_widget_init'));
    }

    public function wp_book_widget_init()
    {
        register_widget('WPBook_Widget');
    }
}

new WPBook_Widget_init();