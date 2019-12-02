<?php

use WPGraphQL\Data\DataSource;

final class WPGraphQL_MetaBox_Util
{

    /**
     * Resolve Meta Box type to GraphQL type
     *
     * @since  0.0.1
     * @access protected
     * @return string
     */
    public static function resolve_graphql_type($type, $multiple, $post_type)
    {
        switch ($type) {
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
            case 'slider':
            case 'taxonomy':
            case 'taxonomy_advanced':
            case 'video':
            case 'wysiwyg':
                error_log(__("Unsupported Meta Box type supplied to wp-graphql-metabox: $type"));
                return;
            case 'switch':
            case 'checkbox':
                return $multiple ?  ['list_of' => 'Boolean'] : 'Boolean';
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
            case 'url':
                return $multiple ?  ['list_of' => 'String'] : 'String';
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
                return $multiple ?  ['list_of' => 'Float'] : 'Float';
            case 'single_image':
                return 'MBSingleImage';
            case 'user':
                return $multiple ? ['list_of' => 'User'] : 'User';
            case 'post':
                if (is_array($post_type) && count($post_type) > 0) {
                    // TODO maybe make unions for multiple post types
                    // $union_types = [];
                    // foreach ($post_type as $type) {
                    //     $graphql_type = self::get_graphql_type_name($type);
                    //     if ($graphql_type) {
                    //         array_push($union_types, $graphql_type);
                    //     }
                    // }
                    // register_graphql_union_type('PetUnion', [
                    //     'types'       => array_map(function ($union_type) {
                    //         return \WPGraphQL\TypeRegistry::get_type($union_type);
                    //     }, $union_types),
                    //     'resolveType' => function ($post) {
                    //         return \WPGraphQL\TypeRegistry::get_type($post['type']);
                    //     }
                    // ]);
                    $post_type = $post_type[0];
                }
                $post_type_object = get_post_type_object($post_type);
                if (!$post_type_object || !$post_type_object->show_in_graphql) {
                    error_log(__("Unknown Meta Box type supplied to wp-graphql-metabox: $post_type"));
                    return;
                }
                return $multiple ? ['list_of' => $post_type_object->graphql_single_name] : $post_type_object->graphql_single_name;
            default:
                error_log(__("Unknown Meta Box type supplied to wp-graphql-metabox: $type"));
        }
    }

    public static function resolve_graphql_resolver($type, $field_id)
    {
        switch ($type) {
            case 'number':
            case 'range':
                return function ($post) use ($field_id) {
                    $field = rwmb_meta($field_id, null, $post->ID);
                    $resolve_field = function ($field_data) {
                        return is_numeric($field_data) ? $field_data : null;
                    };
                    if (is_array($field)) {
                        return array_map($resolve_field, $field);
                    }
                    return $resolve_field($field);
                };
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
            case 'text_list':
            case 'key_value':
            case 'select_advanced':
            case 'url':
                return function ($post) use ($field_id) {
                    $field = rwmb_meta($field_id, null, $post->ID);
                    $resolve_field = function ($field_data) {
                        return isset($field_data) ? $field_data : null;
                    };
                    if (is_array($field)) {
                        return array_map($resolve_field, $field);
                    }
                    return $resolve_field($field);
                };
            case 'single_image':
                return function ($post, $args) use ($field_id) {
                    $size = !isset($args['size']) ? 'thumbnail' : $args['size'];
                    $field = rwmb_meta($field_id, ['size' => $size], $post->ID);
                    $resolve_field = function ($field_data) {
                        return isset($field_data) ? $field_data : null;
                    };
                    if (is_array($field)) {
                        return array_map($resolve_field, $field);
                    }
                    return $resolve_field($field);
                };
            case 'user':
                return function ($post, $args, $context) use ($field_id) {
                    $field = rwmb_meta($field_id, null, $post->ID);
                    $resolve_field = function ($field_data) use ($context) {
                        $user = DataSource::resolve_user($field_data, $context);
                        return isset($user) ? $user : null;
                    };
                    if (is_array($field)) {
                        return array_map($resolve_field, $field);
                    }
                    return $resolve_field($field);
                };
            case 'post':
                return function ($post, $args, $context) use ($field_id) {
                    $field = rwmb_meta($field_id, null, $post->ID);
                    $resolve_field = function ($field_data) use ($context) {
                        $post = DataSource::resolve_post_object($field_data, $context);
                        return isset($post) ? $post : null;
                    };
                    if (is_array($field)) {
                        return array_map($resolve_field, $field);
                    }
                    return $resolve_field($field);
                };
        }
    }

    public static function resolve_graphql_args($type)
    {
        switch ($type) {
            case 'MBSingleImage':
                return [
                    'size' => [
                        'type'        => 'MediaItemSizeEnum',
                        'description' => __('Simple Image size', 'wpgraphql-metabox'),
                    ],
                ];
            default:
                return null;
        }
    }
}
