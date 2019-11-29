<?php

use WPGraphQL\Data\DataSource;
use WPGraphQL\Data\Connection\PostObjectConnectionResolver;

final class WPGraphQL_MetaBox {

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
    public static function instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPGraphQL_MetaBox ) ) {
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
    public function __clone() {

        // Cloning instances of the class is forbidden.
        _doing_it_wrong( __FUNCTION__, esc_html__( 'The WPGraphQL_MetaBox class should not be cloned.', 'wpgraphiql-mb-relationships' ), '0.0.1' );

    }

    /**
     * Disable unserializing of the class.
     *
     * @since  0.0.1
     * @access protected
     * @return void
     */
    public function __wakeup() {

        // De-serializing instances of the class is forbidden.
        _doing_it_wrong( __FUNCTION__, esc_html__( 'De-serializing instances of the WPGraphQL_MetaBox class is not allowed', 'wpgraphiql-mb-relationships' ), '0.0.1' );

    }

    /**
     * Register Meta Box field with WPGraphQL
     *
     * @since  0.0.1
     * @access protected
     * @return void
     */
    public static function register_field( $field, $type, $object_type ) {
        // TODO support other types
        if ( 'post' !== $object_type ) return;
        
        $post_type_object = get_post_type_object( $type );
    
        // check this type and field should be exposed in the schema
        if ( $post_type_object->show_in_graphql && $field['graphql_name'] ) {
            $graphql_type = WPGraphQL_MetaBox_Util::resolve_graphql_type( $field['type'] );

            if ( ! $graphql_type ) {
                // not implemented
                return;
            }

            $graphql_resolver = WPGraphQL_MetaBox_Util::resolve_graphql_resolver( $graphql_type, $field['id'] );
            $graphql_args = WPGraphQL_MetaBox_Util::resolve_graphql_args( $graphql_type );

            register_graphql_fields( $post_type_object->graphql_single_name, [
                $field['graphql_name']  => [
                    'type'          => $graphql_type,
                    'description'   => $field['name'],
                    'resolve'       => $graphql_resolver,
                    'args'          => $graphql_args,
                ]
            ] );
        }
    }

    /**
     * Initialise plugin.
     *
     * @access private
     * @since  0.0.1
     * @return void
     */
    private function init() {

      add_action( 'rwmb_field_registered', [ 'WPGraphQL_MetaBox', 'register_connection' ], 10, 3 );

    }

}

// TODO find appropriate action to hook into
add_action( 'init', 'WPGraphQL_MetaBox_init' );

if ( ! function_exists( 'WPGraphQL_MetaBox_init' ) ) {
    /**
     * Function that instantiates the plugins main class
     *
     * @since 0.0.1
     */
    function WPGraphQL_MetaBox_init() {

        $files = glob( plugin_dir_path( __FILE__ ) . '/src/*.php' );

        foreach ( $files as $file ) {
            require_once $file; 
        }

        /**
         * Return an instance of the action
         */
        return \WPGraphQL_MetaBox::instance();
    }
}
