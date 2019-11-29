# WPGraphQL-MetaBox: WPGraphQL provider for Meta Box

## Quick Install
Download and install like any WordPress plugin.

## Documentation
The WPGraphQL documentation can be found [here](https://docs.wpgraphql.com).

- Requires PHP 5.5+
- Requires WordPress 4.7+
- Requires WPGraphQL 0.3.8+
- Requires Meta Box 5.2.1+

## Overview
This plugin provides an integration for [Meta Box](https://metabox.io/) and [WPGraphQL](https://www.wpgraphql.com/).

By simply adding an extra `graphql_name` property to the field registration the field will be exposed in the GraphQL schema.

## Usage:
**This assume you know how to expose custom post types in WPGraphQL - read their documentation for further info.**

Using Meta Box, define a custom field. Copy and paste the generated code to your `functions.php` (or where ever you store your custom code).

Add in the `graphql_name` to the field definition:

```
$meta_boxes[] = [
    'title' => 'Extra Fields',
    'id' => 'extra-fields',
    'post_types' => [
        0 => 'post',
    ],
    'context' => 'after_title',
    'priority' => 'high',
    'autosave' => true,
    'fields' => [
        [
            'id' => 'a_random_number',
            'name' => 'A Random Number',
            'type' => 'number',
            'std' => 5,
            'columns' => 2,
            'size' => 3,
            'graphql_name' => 'randomNumber',
        ],
    ],
];
```

That's it. The field `randomNumber` will be exposed on the type `post`. This will work for any custom post types you may create.

**NB**: You must expose custom types by adding `show_in_graphql` to the configuration of the CPT.

A simple query might look like this:
```
query {
    posts {
        nodes {
            title
            content
            randomNumber
        }
    }
}
```

## Limitations
Currently the plugin only supports custom fields on `post` types (ie no User or Settings Pages).

Currently the plugin only supports using the following Meta Box types:
- `switch`
- `checkbox`
- `checkbox_list`
- `background`
- `color`
- `custom_html`
- `date`
- `heading`
- `datetime`
- `oembed`
- `password`
- `radio`
- `textarea`
- `time`
- `select`
- `text`
- `fieldset_text`
- `number`
- `range`
- `text_list`
- `key_value`
- `select_advanced`
- `url`
- `single_image`
