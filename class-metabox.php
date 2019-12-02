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
     * Register Meta Box field with WPGraphQL
     *
     * @since  0.0.1
     * @access protected
     * @return void
     */
    public static function register_field($field, $type, $object_type)
    {
        switch ($object_type) {
            case 'post':
                return self::register_post_field($field, $type);
            case 'user':
                return self::register_user_field($field, $type, $object_type);
        }
    }

    private static function register_post_field($field, $type)
    {
        $post_type_object = get_post_type_object($type);

        if (!$post_type_object) {
            return;
        }

        // check this type and field should be exposed in the schema
        if (
            property_exists($post_type_object, 'show_in_graphql') &&
            $post_type_object->show_in_graphql && !empty($field['graphql_name'])
        ) {
            $post_type = array_key_exists('post_type', $field) ? $field['post_type'] : null;
            $graphql_type = WPGraphQL_MetaBox_Util::resolve_graphql_type($field['type'], $field['multiple'], $post_type);

            if (!$graphql_type) {
                // not implemented
                return;
            }

            $graphql_resolver = WPGraphQL_MetaBox_Util::resolve_graphql_resolver($field['type'], $field['id']);
            $graphql_args = WPGraphQL_MetaBox_Util::resolve_graphql_args($graphql_type);

            register_graphql_fields($post_type_object->graphql_single_name, [
                $field['graphql_name']  => [
                    'type'          => $graphql_type,
                    'description'   => $field['name'],
                    'resolve'       => $graphql_resolver,
                    'args'          => $graphql_args,
                ]
            ]);
        }
    }

    private static function register_user_field($field, $type, $object_type)
    {
        // TODO resolve fields on users
    }

    public static function add_field_settings($advanced_fields, $label_prefix, $args_prefix)
    {
        return array_merge($advanced_fields, [
            [
                'name' => __('WPGraphQL Integration', 'wpgraphql-metabox'),
                'type' => 'heading',
            ],
            [
                'name' => __('Show in GraphQL?', 'wpgraphql-metabox'),
                'id'   => $args_prefix . 'show_in_graphql',
                'type' => 'checkbox',
                'std'  => 0,
                'desc' => __('Add this type to the GraphQL schema.', 'wpgraphql-metabox'),
            ],
            [
                'name'  => __('GraphQL single name', 'wpgraphql-metabox'),
                'id'    => $args_prefix . 'graphql_single_name',
                'type'  => 'text',
                'desc'  => __('Required if Show in GraphQL checked', 'wpgraphql-metabox'),
            ],
            [
                'name'  => __('GraphQL plural name', 'wpgraphql-metabox'),
                'id'    => $args_prefix . 'graphql_plural_name',
                'type'  => 'text',
                'desc'  => __('Required if Show in GraphQL checked', 'wpgraphql-metabox'),
            ],
        ]);
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
        add_filter('rwmb_advanced_field_settings', ['WPGraphQL_MetaBox', 'add_field_settings'], 10, 3);
        WPGraphQL_MetaBox_Types::register_builtin_types();
        add_action('rwmb_field_registered', ['WPGraphQL_MetaBox', 'register_field'], 10, 3);
    }
}

// TODO find appropriate action to hook into
add_action('graphql_init', 'WPGraphQL_MetaBox_init');

if (!function_exists('WPGraphQL_MetaBox_init')) {
    /**
     * Function that instantiates the plugins main class
     *
     * @since 0.0.1
     */
    function WPGraphQL_MetaBox_init()
    {

        $files = glob(plugin_dir_path(__FILE__) . '/src/*.php');

        foreach ($files as $file) {
            require_once $file;
        }

        /**
         * Return an instance of the action
         */
        return \WPGraphQL_MetaBox::instance();
    }
}
