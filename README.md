# 3D Virtual Tours Embedder

Tested on WordPress 4.9+.

## Requirements

1. PHP Zip (http://php.net/manual/en/book.zip.php)
2. Write permissions on $WORDPRESS/wp-content/uploads

## Usage in your theme

### Shortcode
After uploading your tour in WordPress, you may use the provided shortcode to directly 
embed the uploaded tour in your website. You'll still need to add CSS styling for it.

The shortcode syntax is as follows

```text
[tdvt_tour id=YOUR_TOUR_POST_ID_HERE]
```

### Direct embedding
If you don't want to use a shortcode, you can generate the `<iframe>` string buffer
directly using `tdvt_get_embed_tour_by_id()`.

```php
<?php
if (function_exists('tdvt_get_embed_tour_by_id'))
	echo tdvt_get_embed_tour_by_id(get_field('some_tour_id_field'));
```