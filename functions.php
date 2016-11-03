<?php 

define( 'AW_ROOT_PATH', dirname(__FILE__) ); // Path in file directory to the root of Compare Responsive theme

// Theme's defaults
require_once('functions/defaults.php');

// Theme's custom walkerss
require_once('functions/walkers.php');

// Inctallation
require_once('functions/theme-installation.php');

// Theme options
require_once('functions/theme-options.php');

// Enqueue CSS
require_once('functions/enqueue-css.php');

// Enqueue JS
require_once('functions/enqueue-js.php');

// Enqueue Fonts
require_once('functions/enqueue-fonts.php');

// Register Thumbnails
require_once('functions/thumbnails.php');

// Register Menus
require_once('functions/menus.php');

// Register Widget Areas
require_once('functions/widget-areas.php');

// Register Widgets
require_once('functions/widgets.php');

// Shortcodes
require_once('functions/shortcodes.php');

// Register Custom Post Types
require_once('functions/custom-post-types.php');

// Register Custom Taxonomies
require_once('functions/custom-taxonomies.php');

// Register Custom Metas
require_once('functions/custom-meta-fields.php');

// Product Merchant Management Specific Functions
require_once('functions/products-merchant-management.php');

// Helpers
require_once('functions/helpers.php');

// Other
require_once('functions/other.php');

?>