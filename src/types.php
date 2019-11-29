<?php

final class WPGraphQL_MetaBox_Types {

    public static function register_builtin_types() {

        register_graphql_object_type( 'MBKeyValue', [
            'description'   => __( 'Meta Box Key Value type', 'wpgraphql-metabox' ),
            'fields'        => [
                'key'   => 'String',
                'value' => 'String',
            ],
        ] );

        register_graphql_object_type( 'MBSingleImage', [
            'description'   => __( 'Meta Box single image type', 'wpgraphql-metabox' ),
            'fields'        => [
                'name'          => [
                    'type'          => 'String',
                    'description'   => __( 'Single Image name', 'wpgraphql-metabox' ),
                ],
                'path'          => [
                    'type'          => 'String',
                    'description'   => __( 'Single Image path', 'wpgraphql-metabox' ),
                ],
                'url'           => [
                    'type'          => 'String',
                    'description'   => __( 'Single Image URL', 'wpgraphql-metabox' ),
                ],
                'width'         => [
                    'type'          => 'Int',
                    'description'   => __( 'Single Image width', 'wpgraphql-metabox' ),
                ],
                'height'        => [
                    'type'          => 'Int',
                    'description'   => __( 'Single Image height', 'wpgraphql-metabox' ),
                ],
                'fullUrl'       => [
                    'type'          => 'String',
                    'description'   => __( 'Single Image full URL', 'wpgraphql-metabox' ),
                ],
                'title'         => [
                    'type'          => 'String',
                    'description'   => __( 'Single Image title', 'wpgraphql-metabox' ),
                ],
                'caption'       => [
                    'type'          => 'String',
                    'description'   => __( 'Single Image caption', 'wpgraphql-metabox' ),
                ],
                'description'   => [
                    'type'          => 'String',
                    'description'   => __( 'Single Image description', 'wpgraphql-metabox' ),
                ],
                'alt'           => [
                    'type'          => 'String',
                    'description'   => __( 'Single Image alt text', 'wpgraphql-metabox' ),
                ],
                'srcset'        => [
                    'type'          => 'String',
                    'description'   => __( 'Single Image source set', 'wpgraphql-metabox' ),
                ],
            ],
        ] );

    }
}