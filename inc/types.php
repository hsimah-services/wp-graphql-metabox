<?php

/**
 * The types class.
 * This is where GraphQL types are defined and registered
 *
 * @package  WPGraphQL_MetaBox
 */

/**
 * The WPGraphQL_MetaBox_Types class.
 */
final class WPGraphQL_MetaBox_Types
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
    register_graphql_object_type('MBKeyValue', self::get_key_value_config());
    register_graphql_object_type('MBSingleImage', self::get_single_image_config());
  }

  /**
   * KeyValue GraphQL type config
   * 
   * @access  private
   * @since   0.5.0
   * @return  array
   */
  private static function get_key_value_config()
  {
    return [
      'description'   => __('Meta Box Key Value type', 'wpgraphql-metabox'),
      'fields'        => [
        'key'   => [
          'type' => 'String',
          'description'   => __('Key', 'wpgraphql-metabox'),
        ],
        'value' => [
          'type' => 'String',
          'description'   => __('Value', 'wpgraphql-metabox'),
        ],
      ],
    ];
  }

  /**
   * SingleImage GraphQL type config
   * 
   * @access  private
   * @since   0.5.0
   * @return  array
   */
  private static function get_single_image_config()
  {
    return [
      'description'   => __('Meta Box single image type', 'wpgraphql-metabox'),
      'fields'        => [
        'name'          => [
          'type'          => 'String',
          'description'   => __('Single Image name', 'wpgraphql-metabox'),
        ],
        'path'          => [
          'type'          => 'String',
          'description'   => __('Single Image path', 'wpgraphql-metabox'),
        ],
        'url'           => [
          'type'          => 'String',
          'description'   => __('Single Image URL', 'wpgraphql-metabox'),
        ],
        'width'         => [
          'type'          => 'Int',
          'description'   => __('Single Image width', 'wpgraphql-metabox'),
        ],
        'height'        => [
          'type'          => 'Int',
          'description'   => __('Single Image height', 'wpgraphql-metabox'),
        ],
        'full_url'       => [
          'type'          => 'String',
          'description'   => __('Single Image full URL', 'wpgraphql-metabox'),
        ],
        'title'         => [
          'type'          => 'String',
          'description'   => __('Single Image title', 'wpgraphql-metabox'),
        ],
        'caption'       => [
          'type'          => 'String',
          'description'   => __('Single Image caption', 'wpgraphql-metabox'),
        ],
        'description'   => [
          'type'          => 'String',
          'description'   => __('Single Image description', 'wpgraphql-metabox'),
        ],
        'alt'           => [
          'type'          => 'String',
          'description'   => __('Single Image alt text', 'wpgraphql-metabox'),
        ],
        'srcset'        => [
          'type'          => 'String',
          'description'   => __('Single Image source set', 'wpgraphql-metabox'),
        ],
      ],
    ];
  }
}
