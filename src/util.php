<?php

final class WPGraphQL_MetaBox_Util {

    /**
     * Resolve Meta Box type to GraphQL type
     *
     * @since  0.0.1
     * @access protected
     * @return string
     */
    public static function resolve_graphql_type( $type ) {
        switch ( $type ) {
            case 'autocomplete':
            case 'button':
            case 'button_group':
            case 'divider':
            case 'file':
            case 'file_advanced':
            case 'file_input':
            case 'file_upload':
            case 'hidden':
            case 'image':
            case 'image_advanced':
            case 'image_select':
            case 'image_upload':
            case 'map':
            case 'plupload_image':
            case 'post':
            case 'slider':
            case 'taxonomy':
            case 'taxonomy_advanced':
            case 'user':
            case 'video':
            case 'wysiwyg':
                error_log( __( "Unsupported Meta Box type supplied to wp-graphql-metabox: $type" );
                return;
            case 'switch':
            case 'checkbox':
                return 'Boolean';
            case 'checkbox_list':
                return [
                    'list_of' => 'Boolean',
                ];
            case 'background':
            case 'color':
            case 'custom_html':
            case 'date':
            case 'heading':
            case 'datetime':
            case 'oembed':
            case 'password':
            case 'radio':
            case 'textarea':
            case 'time':
            case 'select':
            case 'text':
                return 'String';
            case 'fieldset_text':
            case 'text_list':
            case 'select_advanced':
                return [
                    'list_of' => 'String',
                ];
            case 'key_value':
                return [
                    'list_of' => 'MBKeyValue',
                ];
            case 'number':
            case 'range':
                return 'Float';
            case 'single_image':
                return 'SingleImage';
            default:
                error_log( __( "Unknown Meta Box type supplied to wp-graphql-metabox: $type" );
                return;
        }
    }

    public static function resolve_graphql_resolver( $type, $field_id ) {
        switch ( $type ) {
            case 'switch':
            case 'checkbox':
            case 'checkbox_list':
            case 'background':
            case 'color':
            case 'custom_html':
            case 'date':
            case 'heading':
            case 'datetime':
            case 'oembed':
            case 'password':
            case 'radio':
            case 'textarea':
            case 'time':
            case 'select':
            case 'text':
            case 'fieldset_text':
            case 'number':
            case 'range':
            case 'text_list':
            case 'key_value':
            case 'select_advanced':
                return function( $post ) use ( $field_id ) {
                    return rwmb_meta( $field, null, $post->ID );
                };
            case 'single_image':
                return function( $post ) {
                    // TODO resolve this type
                    return function( $post, $args ) use ( $field_id ) {
                        return rwmb_meta( $field, [ 'size' => $args['size'] ], $post->ID );
                    };
                };
        }
    }

    public static function resolve_graphql_args( $type ) {
        switch ( $type ) {
            case 'SingleImage':
                return [
                    'size' => [
                        'type'        => 'MediaItemSizeEnum',
                        'description' => __( 'Simple Image size', 'wpgraphql-metabox' ),
                    ],
                ];
            default:
                return null;
        }
    }
}