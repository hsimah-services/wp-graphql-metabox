<?php

final class WPGraphQL_MetaBox
{

    /**
     * Stores the instance of the WPGraphQL_MetaBox class
     *
     * @var WPGraphQL_MetaBox The one true WPGraphQL_MetaBox
     * @since  0.0.1
     * @access private
     */
    private static $instance;

    /**
     * The instance of the WPGraphQL_MetaBox object
     *
     * @return object|WPGraphQL_MetaBox - The one true WPGraphQL_MetaBox
     * @since  0.0.1
     * @access public
     */
    public static function instance()
    {

        if (!isset(self::$instance) && !(self::$instance instanceof WPGraphQL_MetaBox)) {
            self::$instance = new WPGraphQL_MetaBox();
            self::$instance->init();
        }

        /**
         * Return the WPGraphQL_MetaBox Instance
         */
        return self::$instance;
    }

    /**
     * Throw error on object clone.
     * The whole idea of the singleton design pattern is that there is a single object
     * therefore, we don't want the object to be cloned.
     *
     * @since  0.0.1
     * @access public
     * @return void
     */
    public function __clone()
    {

        // Cloning instances of the class is forbidden.
        _doing_it_wrong(__FUNCTION__, esc_html__('The WPGraphQL_MetaBox class should not be cloned.', 'wpgraphql-metabox'), '0.0.1');
    }

    /**
     * Disable unserializing of the class.
     *
     * @since  0.0.1
     * @access protected
     * @return void
     */
    public function __wakeup()
    {

        // De-serializing instances of the class is forbidden.
        _doing_it_wrong(__FUNCTION__, esc_html__('De-serializing instances of the WPGraphQL_MetaBox class is not allowed', 'wpgraphql-metabox'), '0.0.1');
    }

    /**
     * Initialise plugin.
     *
     * @access private
     * @since  0.0.1
     * @return void
     */
    private function init()
    {
        // Register any necessary settings
        WPGraphQL_MetaBox_Settings::init();

        // Register built-in Meta Box types
        WPGraphQL_MetaBox_Types::init();

        // Register post type handlers
        WPGraphQL_MetaBox_Posts::init();

        // Register user type handlers
        WPGraphQL_MetaBox_Users::init();
    }
}

add_action('init', function () {

    $files = glob(plugin_dir_path(__FILE__) . '/inc/*.php');

    foreach ($files as $file) {
        require_once $file;
    }

    /**
     * Return an instance of the action
     */
    return \WPGraphQL_MetaBox::instance();
});
