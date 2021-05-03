<?php

/**
 * The posts class.
 * This contains logic associated with registering post types and fields 
 *
 * @package  WPGraphQL_MetaBox
 */

/**
 * The WPGraphQL_MetaBox_Posts class.
 */
final class WPGraphQL_MetaBox_Posts
{
  /**
   * Registers the plugin GraphQL types
   * 
   * @access  public
   * @since   0.5.0
   * @return  array
   */
  public static function init()
  {
    // Register Meta Box post fields in the GraphQL schema
    add_action('rwmb_field_registered', ['WPGraphQL_MetaBox_Posts', 'handle_registered_field'], 10, 3);
  }

  /**
   * Register Meta Box field with WPGraphQL
   * 
   * @param array  $field       Meta Box field configuration.
   * @param string $type        Post type|Taxonomy|'user'|Setting page which the field belongs to.
   * @param string $object_type Object type which the field belongs to.
   * 
   * @access  public
   * @since   0.5.0
   * @return  void
   */
  public static function handle_registered_field($field, $type, $object_type)
  {
    if ($object_type !== 'post') {
      return;
    }

    $post_type_object = get_post_type_object($type);

    // sanity check this type exists
    if (!$post_type_object) {
      return;
    }

    // check this type and field should be exposed in the schema
    if (
      !property_exists($post_type_object, 'show_in_graphql') ||
      $post_type_object->show_in_graphql != true ||
      empty($field['graphql_name'])
    ) {
      return;
    }

    $graphql_type_name =  WPGraphQL_MetaBox_Util::get_graphql_type($field);

    // check if this field type is registered in the schema
    if (!$graphql_type_name) {
      return;
    }

    $resolve_field_callback = WPGraphQL_MetaBox_Util::get_graphql_field_resolver_callback($field);
    $graphql_field_args = WPGraphQL_MetaBox_Util::get_graphql_field_args($graphql_type_name);

    register_graphql_fields($post_type_object->graphql_single_name, [
      $field['graphql_name']  => [
        'type'          => $graphql_type_name,
        'description'   => $field['name'],
        'resolve'       => $resolve_field_callback,
        'args'          => $graphql_field_args,
      ]
    ]);

    WPGraphQL_MetaBox_Util::register_mutation_input($post_type_object->graphql_single_name, $graphql_type_name, $field);
  }
}
