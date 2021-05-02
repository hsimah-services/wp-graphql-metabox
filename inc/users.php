<?php

/**
 * The users class.
 * This contains logic associated with registering user fields 
 *
 * @package  WPGraphQL_MetaBox
 */

/**
 * The WPGraphQL_MetaBox_Users class.
 */
final class WPGraphQL_MetaBox_Users
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
    self::check_dependencies();

    // Register Meta Box user fields in the GraphQL schema
    add_action('rwmb_field_registered', ['WPGraphQL_MetaBox_Users', 'handle_registered_field'], 10, 3);
  }

  /**
   * Register Meta Box field with WPGraphQL
   * 
   * @param array  $field       Meta Box field configuration.
   * @param string $_type        Post type|Taxonomy|'user'|Setting page which the field belongs to.
   * @param string $object_type Object type which the field belongs to.
   * 
   * @access  public
   * @since   0.5.0
   * @return  void
   */
  public static function handle_registered_field($field, $_type, $object_type)
  {
    if ($object_type !== 'user') {
      return;
    }

    if (empty($field['graphql_name'])) {
      return;
    }

    $graphql_type_name = WPGraphQL_MetaBox_Util::resolve_graphql_type($field);

    // check if this field type is registered in the schema
    if (!$graphql_type_name) {
      return;
    }


    $resolve_field_callback = WPGraphQL_MetaBox_Util::resolve_graphql_resolver($field, ['object_type' => 'user']);
    $graphql_field_args = WPGraphQL_MetaBox_Util::resolve_graphql_args($graphql_type_name);

    register_graphql_field(
      'User',
      $field['graphql_name'],
      [
        'type'          => $graphql_type_name,
        'description'   => $field['name'],
        'resolve'       => $resolve_field_callback,
        'args'          => $graphql_field_args,
      ]
    );
  }

  /**
   * Check user field dependencies
   * 
   * @access  private
   * @since   0.5.0
   * @return  void
   */
  private static function check_dependencies()
  {
    // Without this MB extension this will fail
    if (!class_exists('RWMB_User_Storage')) {
      throw new Error('MB User Meta plugin required to register user fields');
    }
  }
}
