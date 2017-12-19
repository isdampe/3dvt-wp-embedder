<?php defined('ABSPATH') or die();
/*
 * Plugin Name:  3D Virtual Tours Embedder
 * Plugin URI:   https://www.3dvt.com.au/
 * Description:  Embed your 3D virtual tour from 3D Virtual Tours Pty Ltd
 * Version:      0.1.0
 * Author:       isdampe
 * Author URI:   https://github.com/isdampe/3dvt-wordpress-plugin
 * License:      MIT
 * Text Domain:  3dvt
*/

require_once 'lib/post-type.php';
require_once 'lib/meta-box.php';
require_once 'lib/ajax.php';
require_once 'lib/embed.php';
require_once 'lib/style.php';
require_once 'lib/shortcode.php';