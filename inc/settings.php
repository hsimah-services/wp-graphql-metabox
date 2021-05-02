<?php

/**
 * The settings class.
 * This is where settings fields and types are registered
 *
 * @package  WPGraphQL_MetaBox
 */

/**
 * The WPGraphQL_MetaBox_Settings class.
 */
final class WPGraphQL_MetaBox_Settings
{
  /**
   * Registers the plugin settings
   * 
   * @access  public
   * @since   0.5.0
   * @return  array
   */
  public static function init()
  {
    // add GraphQL settings to CPT UI
    add_filter('mbcpt_advanced_fields', ['WPGraphQL_MetaBox_Settings', 'register_settings'], 10, 3);
    add_filter('mbcpt_advanced_taxonomy_fields', ['WPGraphQL_MetaBox_Settings', 'register_settings'], 10, 3);
  }
  /**
   * Handler for Meta Box CPT filter:
   * `mbcpt_advanced_fields`
   * `mbcpt_advanced_taxonomy_fields`
   * 
   * @param   array   $advanced_fields  The existing field config
   * @param   string  $_label_prefix    The label prefix
   * @param   string  $args_prefix      The args prefix
   * @access  public
   * @since   0.5.0
   * @return  array
   */
  public static function register_settings($advanced_fields, $_label_prefix, $args_prefix)
  {
    return array_merge($advanced_fields, self::get_settings_field_config($args_prefix));
  }

  /**
   * The advanced field settings config
   * 
   * @param   string  $prefix
   * @access  private
   * @since   0.5.0
   * @return  array
   */
  private static function get_settings_field_config($prefix)
  {
    return [
      [
        'name' => __('WPGraphQL Integration', 'wpgraphql-metabox'),
        'type' => 'heading',
      ],
      [
        'name' => __('Show in GraphQL?', 'wpgraphql-metabox'),
        'id'   => $prefix . 'show_in_graphql',
        'type' => 'checkbox',
        'std'  => 0,
        'desc' => __('Add this type to the GraphQL schema.', 'wpgraphql-metabox'),
      ],
      [
        'name'  => __('GraphQL single name', 'wpgraphql-metabox'),
        'id'    => $prefix . 'graphql_single_name',
        'type'  => 'text',
        'desc'  => __('Required if Show in GraphQL checked', 'wpgraphql-metabox'),
      ],
      [
        'name'  => __('GraphQL plural name', 'wpgraphql-metabox'),
        'id'    => $prefix . 'graphql_plural_name',
        'type'  => 'text',
        'desc'  => __('Required if Show in GraphQL checked', 'wpgraphql-metabox'),
      ],
    ];
  }
}
