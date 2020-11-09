<?php

use WPGraphQL\Data\DataSource;
use WPGraphQL\Model\User;

final class WPGraphQL_MetaBox_Util
{

    /**
     * Resolve Meta Box type to GraphQL type
     *
     * @since  0.0.1
     * @access public
     * @return string
     */
    public static function resolve_graphql_type($field)
    {
        if (!is_array($field)) {
            return null;
        }
        $type = $field['type'];
        $multiple = $field['multiple'];
        $clone = $field['clone'];

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
            case 'video':
                error_log(__("Unsupported Meta Box type supplied to wpgraphql-metabox: $type", 'wpgraphql-metabox'));
                return;
            case 'taxonomy':
            case 'taxonomy_advanced':
                $name = $field['taxonomy'][0];
                $taxonomy = get_taxonomy($name);

                if (empty($taxonomy) || $taxonomy->show_in_graphql === false) {
                    error_log("wp-graphql-metabox: $name is not in the schema.");
                    return null;
                }

                return $multiple ? ['list_of' => $taxonomy->graphql_single_name] : $taxonomy->graphql_single_name;
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
            case 'email':
            case 'tel':
            case 'text':
            case 'url':
            case 'wysiwyg':
                return $multiple ?  ['list_of' => 'String'] : 'String';
            case 'fieldset_text':
            case 'select_advanced':
                return ['list_of' => 'String'];
            case 'text_list':
                return $clone ? ['list_of' => ['list_of' => 'String']] : ['list_of' => 'String'];
            case 'key_value':
                return ['list_of' => 'MBKeyValue'];
            case 'number':
            case 'range':
                return $multiple ?  ['list_of' => 'Float'] : 'Float';
            case 'single_image':
                return 'MBSingleImage';
            case 'user':
                return $multiple ? ['list_of' => 'User'] : 'User';
            case 'post':
                $post_type = true === is_array($field['post_type']) ? $field['post_type'][0] : $field['post_type'];
                $post_type_object = get_post_type_object($post_type);

                if (!$post_type_object || !$post_type_object->show_in_graphql) {
                    error_log(__("Unknown Meta Box type supplied to wpgraphql-metabox: $post_type", 'wpgraphql-metabox'));
                    return;
                }

                $graphql_type =  $post_type_object->graphql_single_name;
                return $multiple ? ['list_of' => $graphql_type] : $graphql_type;
            default:
                error_log(__("Unknown Meta Box type supplied to wpgraphql-metabox: $type", 'wpgraphql-metabox'));
        }
    }

    /**
     * Resolves metabox union type for post-to-post with multiple type fields
     *
     * @var mixed           The metabox field configuration
     * @return string|mixed The GraphQL type
     * @since  0.2.1
     * @access public
     */
    public static function resolve_graphql_union_type($field)
    {
        $union_names = array_reduce($field['post_type'], function ($a, $c) {
            $post_type_object = get_post_type_object($c);
            if (true === $post_type_object->show_in_graphql) {
                array_push($a, ucfirst($post_type_object->graphql_single_name));
            }
            return $a;
        }, []);

        $union_name = ucfirst($field['graphql_name']) . 'To' . join('And', $union_names) . 'Union';
        register_graphql_union_type($union_name, [
            'typeNames'       => $union_names,
            'resolveType' => function ($union) {
                return DataSource::resolve_node_type($union);
            }
        ]);

        return $field['multiple'] ? ['list_of' => $union_name] : $union_name;
    }

    public static function resolve_graphql_resolver($type, $field_id, $meta_args = null)
    {
        switch ($type) {
            case 'number':
            case 'range':
                return function ($node) use ($field_id, $meta_args) {
                    $field = self::get_field($node, $field_id, $meta_args);
                    $resolve_field = function ($field_data) {
                        return is_numeric($field_data) ? $field_data : null;
                    };
                    return is_array($field) ? array_map($resolve_field, $field) : $resolve_field($field);
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
            case 'email':
            case 'tel':
            case 'text':
            case 'fieldset_text':
            case 'text_list':
            case 'key_value':
            case 'select_advanced':
            case 'url':
            case 'wysiwyg':
                return function ($node) use ($field_id, $meta_args) {
                    $field = self::get_field($node, $field_id, $meta_args);
                    $resolve_field = function ($field_data) {
                        return isset($field_data) ? $field_data : null;
                    };
                    return is_array($field) ? array_map($resolve_field, $field) : $resolve_field($field);
                };
            case 'single_image':
                return function ($node, $args) use ($field_id, $meta_args) {
                    $size = !isset($args['size']) ? 'thumbnail' : $args['size'];
                    $merged_args = array_merge($meta_args, ['size' => $size]);
                    $field = rwmb_meta($field_id, $merged_args, $node->ID);
                    $resolve_field = function ($field_data) {
                        return isset($field_data) ? $field_data : null;
                    };

                    return is_array($field) ? array_map($resolve_field, $field) : $resolve_field($field);
                };
            case 'user':
                return function ($node, $args, $context) use ($field_id, $meta_args) {
                    $field = self::get_field($node, $field_id, $meta_args);
                    $resolve_field = function ($field_data) use ($context) {
                        $user = DataSource::resolve_user($field_data, $context);
                        return isset($user) ? $user : null;
                    };

                    return is_array($field) ? array_map($resolve_field, $field) : $resolve_field($field);
                };
            case 'post':
                return function ($node, $args, $context) use ($field_id, $meta_args) {
                    $field = self::get_field($node, $field_id, $meta_args);
                    $resolve_field = function ($field_data) use ($context) {
                        $node = DataSource::resolve_post_object($field_data, $context);
                        return isset($node) ? $node : null;
                    };

                    return is_array($field) ? array_map($resolve_field, $field) : $resolve_field($field);
                };
            case 'taxonomy':
            case 'taxonomy_advanced':
                return function ($node, $args, $context) use ($field_id, $meta_args) {
                    $field = self::get_field($node, $field_id, $meta_args);
                    $resolve_field = function ($field_data) use ($context) {
                        $taxonomy = DataSource::resolve_term_object($field_data->term_id, $context);
                        return isset($taxonomy) ? $taxonomy : null;
                    };
                    return is_array($field) ? array_map($resolve_field, $field) : $resolve_field($field);
                };
            default:
                return function () {
                    return null;
                };
        }
    }

    /**
     * Resolves GraphQL input args for a given GraphQL type
     *
     * @var string          The GraphQL type name
     * @return  array|null  The GraphQL arg config
     * @since  0.1.0
     * @access public
     */
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

    /**
     * Resolves metabox field data by entity type
     *
     * @var \WPGraphQL\Model    The node to resolve
     * @var string              The metabox field id
     * @var array               The metabox field args
     * @return  mixed           The field contents
     * @since  0.2.0
     * @access private
     */
    private static function get_field($node, $field_id, $args = null)
    {
        if ($node instanceof User) {
            return rwmb_meta($field_id, $args, $node->userId);
        }
        return rwmb_meta($field_id, $args, $node->ID);
    }
}
