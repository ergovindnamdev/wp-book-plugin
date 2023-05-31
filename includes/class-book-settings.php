<?php
/**
 * WP Book Plugin setup
 *
 * @package wp-book
 * @since   1.0.0
 */
defined('ABSPATH') || exit;
/**
 * Book plugin setting Class.
 *
 * @class BookSettingSubMenu
 */
class BookSettingSubMenu
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'book_submenu'));
        add_action('admin_init', array($this, 'book_settings_init'));

    }

    /**
     * @return mixed
     * Function for Book sub menu
     */
    public function book_submenu()
    {
        add_submenu_page(
            'edit.php?post_type=book',
            'Books Setting',
            'Settings',
            'manage_options',
            'book_setting',
            array($this, 'book_setting_fun')
        );
    }


    /**
     * @return mixed 
     * Function for Book Settings
     */
    public function book_setting_fun()
    {
        // check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }

        // check if the user have submitted the settings
        if (isset($_GET['settings-updated'])) {
            add_settings_error('book_group_messages', 'book_group_message', __('Settings Saved', 'simple-book-plugin'), 'updated');
        }
        ?>
        <div class="wrap">
            <h1>
                <?php echo esc_html(get_admin_page_title()); ?>
            </h1>
            <form action="options.php" method="post">
                <?php
                // output security fields for the registered setting "wporg"
                settings_fields('book_group');
                // output setting sections and their fields
                // (sections are registered for "wporg", each field is registered to a specific section)
                do_settings_sections('book_group');
                // output save settings button
                submit_button('Save Settings');
                ?>
            </form>
        </div>
        <?php
    }


    /**
     * Setting initialization.
     */
    public function book_settings_init()
    {
        // Regiter Settings for Book Setting.
        register_setting('book_group', 'book_options');

        // Add settings section
        add_settings_section(
            'book_plugin_setting_section',
            "",
            '',
            'book_group'
        );

        // Add settings section field
        add_settings_field(
            'book_plugin_display',
            __('Books pages show at most ', 'simple-book-plugin'),
            array($this, 'book_plugin_display_fun'),
            'book_group',
            'book_plugin_setting_section',
            array(
                'label_for' => 'book_display',
                'class' => 'book_display_row',
                'book_group_custom_data' => 'custom',
            )
        );

        // Add settings section field
        add_settings_field(
            'book_plugin_posts',
            __('Post Types', 'simple-book-plugin'),
            array($this, 'book_plugin_posts_fun'),
            'book_group',
            'book_plugin_setting_section',
            array(
                'label_for' => 'book_currency',
                'class' => 'book_currency_row',
                'book_group_custom_data' => 'custom',
            )
        );


    }

    /**
     * @args array
     * @return string
     * Function for book display section
     */
    public function book_plugin_display_fun($args)
    {
        $options = get_option('book_options');
        ?>
        <input type="number" id="<?php echo esc_attr($args['label_for']); ?>"
            name="book_options[<?php echo esc_attr($args['label_for']); ?>]"
            value="<?php (!empty($options) && $options[$args['label_for']] !== "") ? _e($options[$args['label_for']], 'simple-book-plugin') : "10" ?>">
        books
        <?php
    }

    /**
     * @args array
     * @return string
     * Functin for display post types
     */
    public function book_plugin_posts_fun($args)
    {

        $options = get_option('book_options');


        $countries_json = file_get_contents(dirname(WPBOOK_PLUGIN_FILE) . '/json/countries.json');

        // Decode the JSON file
        $countries = json_decode($countries_json, true);
        ?>
        <select id="<?php echo esc_attr($args['label_for']); ?>"
            name="book_options[<?php echo esc_attr($args['label_for']); ?>]">
            <option value="">Select Currency</option>
            <?php
            foreach ($countries['countries'] as $country) {
                $selected = "";
                if (!empty($options) && $options[$args['label_for']] != '' && $country['countryName'] . ' - ' . $country['currencyCode'] == $options[$args['label_for']]) {
                    $selected = "selected='selected'";
                }

                ?>
                <option <?php echo $selected; ?> value="<?php echo $country['countryName'] . ' - ' . $country['currencyCode']; ?>"><?php echo $country['countryName'] . ' - ' . $country['currencyCode']; ?></option>
                <?php
            }
            ?>
        </select>
        <?php

    }


}

new BookSettingSubMenu();