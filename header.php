<?php global $aw_theme_options; ?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 9]>    <html class="no-js ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js" <?php language_attributes(); ?> itemscope="" itemtype="http://schema.org/Product"> <!--<![endif]-->
<head>
        <?php do_action('aw_show_compare_header_version_numbers'); ?>
    
	<meta charset="<?php bloginfo('charset'); ?>">

	<meta http-equiv="X-UA-Compatible" content="IE=edge">
        
<?php  if(isset($_GET['s'])){ ?>
        <!-- SEARCH PAGE NOINDEX -->
        <meta name="robots" content="noindex, nofollow" />
<?php } ?>
        
        <title><?php if(is_home() || is_search() || is_front_page()) { bloginfo('name'); echo ' | '; bloginfo('description'); } else { wp_title(''); echo ' | '; bloginfo('name'); } ?></title>
        <link rel="alternate" type="application/rss+xml" title="<?php bloginfo( 'name' ); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
        <link rel="alternate" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
        <link rel="alternate" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />

        
<?php if( trim($aw_theme_options['tz_favicon_url']) != "" ): ?>        
        <link rel="shortcut icon" href="<?php echo $aw_theme_options['tz_favicon_url']; ?>" type="image/x-icon" />
<?php else: ?>
        <link rel="shortcut icon" href="<?php bloginfo('template_directory'); ?>/favicon.png" type="image/x-icon" />
<?php endif; ?>
        
<?php if( trim($aw_theme_options['tz_apple_touch_icon_url']) != "" ): ?>
        <link rel="apple-touch-icon" href="<?php echo $aw_theme_options['tz_apple_touch_icon_url']; ?>" />
<?php else: ?>
        <link rel="apple-touch-icon" href="<?php bloginfo('template_directory'); ?>/apple-touch-icon.png" />
<?php endif; ?>

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">        
              
	<?php wp_head(); ?>
        
        <style type="text/css">
<?php
$tz_colorset5_c1 = $aw_theme_options['tz_colorset5_c1'];
$tz_colorset4_c3 = $aw_theme_options['tz_colorset4_c3'];
$tz_colorset4_c2 = $aw_theme_options['tz_colorset4_c2'];
$tz_colorset4_c1 = $aw_theme_options['tz_colorset4_c1'];
$tz_colorset3_c1 = $aw_theme_options['tz_colorset3_c1'];
$tz_colorset3_c2 = $aw_theme_options['tz_colorset3_c2'];
$tz_colorset2_c2 = $aw_theme_options['tz_colorset2_c2'];
$tz_colorset2_c1 = $aw_theme_options['tz_colorset2_c1'];
$tz_colorset1_c2 = $aw_theme_options['tz_colorset1_c2'];
$tz_colorset1_c1 = $aw_theme_options['tz_colorset1_c1'];
?>
            /* Body BG-colour */
            body { background-color: <?php echo $tz_colorset5_c1; ?>; }
            

            /* Main menu element */
            @media only screen and (max-width: 768px) {
                    .navbar ul { background: <?php echo $tz_colorset1_c1; ?>; }
            }

            /* Blue */
            a { color: <?php echo $tz_colorset2_c1; ?>; }
            @media only screen and (max-width: 767px) { 
                    .navbar ul li.active .dropdown ul li a:hover { color: <?php echo $tz_colorset2_c1; ?>; }
                    .navbar li .dropdown.active .dropdown ul li a:hover { color: <?php echo $tz_colorset2_c1; ?>; }
                    .gumby-no-touch .navbar ul li:hover .dropdown ul li a:hover { color: <?php echo $tz_colorset2_c1; ?>; }
                    .navbar ul li > a + .dropdown ul a { background: <?php echo $tz_colorset1_c2; ?>; border-bottom-color: <?php echo $tz_colorset1_c1; ?> !important; }
                    .gumby-no-touch .navbar ul li:hover > a, .gumby-touch .navbar ul li.active > a { background: <?php echo $tz_colorset1_c2; ?>; }
                    .gumby-no-touch .navbar ul li:hover .dropdown, .gumby-touch .navbar ul li.active .dropdown { border-top-color: <?php echo $tz_colorset1_c1; ?> !important;}
            }
            .navbar li .dropdown ul > li a { color: <?php echo $tz_colorset2_c1; ?>; }
            .btn.secondary, .skiplink.secondary { background: <?php echo $tz_colorset2_c1; ?>; border-color: <?php echo $tz_colorset2_c1; ?>; }
            .badge.secondary, .label.secondary { background: <?php echo $tz_colorset2_c1; ?>; border-color: <?php echo $tz_colorset2_c1; ?>; }
            .badge.light a, .label.light a { color: <?php echo $tz_colorset2_c1; ?>; }
            .alert.secondary { border-color: <?php echo $tz_colorset2_c1; ?>; }
            .homepage-slider #sequence h2 a:hover, .homepage-slider #sequence h2 a:focus { color: <?php echo $tz_colorset2_c1; ?>; }
            .pager ul li a { border-color: <?php echo $tz_colorset2_c1; ?>; }
            .tophead a:hover, .tophead a:focus { color: <?php echo $tz_colorset2_c1; ?>; }
            .topmain > div { background-color: <?php echo $tz_colorset2_c1; ?>; }
            .row-homepage-featured > div > a h3, .row-homepage-featured > div > a h4 { color: <?php echo $tz_colorset2_c1; ?>; }
            .paginate .page-numbers, .paginate .buttons a { border-color: <?php echo $tz_colorset2_c1; ?>; }
            .nav-brands .skiplink > a:hover, .nav-brands .skiplink > a:focus { color: <?php echo $tz_colorset2_c1; ?>; }

            /* Light Blue */
            .topmain > div .logo p { color: <?php echo $tz_colorset2_c2; ?>; }

            /* $light-gray: #f8f8f8;
            $gray: #888;
            $dark-gray: #4c4c4c;
            $border-gray: #e9e9e9; */

            /* orange*/
            .btn.primary, .skiplink.primary { background: <?php echo $tz_colorset3_c1; ?>; border-color: <?php echo $tz_colorset3_c1; ?>; }
            .badge.primary, .label.primary { background: <?php echo $tz_colorset3_c1; ?>; border-color: <?php echo $tz_colorset3_c1; ?>; }
            .alert.primary { border-color: <?php echo $tz_colorset3_c1; ?>; color: #ba5a00; }
            .ttip:after { background: <?php echo $tz_colorset3_c1; ?>; border-color: <?php echo $tz_colorset3_c1; ?>; }
            .ttip:before { border-top-color: <?php echo $tz_colorset3_c1; ?> !important; }
            .pager .short .btn { background: <?php echo $tz_colorset3_c1; ?>; border-color: <?php echo $tz_colorset3_c1; ?>; }
            .pager ul li a:hover, .pager ul li a:focus { border-color: <?php echo $tz_colorset3_c1; ?>; }
            .bypostauthor .comment-body { border-left-color: <?php echo $tz_colorset3_c1; ?>; }
            a:hover, a:focus { color: <?php echo $tz_colorset3_c1; ?>; }
            .links-dark-gray:hover, .links-gray:hover, .sidebar .widget a:hover, .links-body-color:hover, .product-listing .listing-params .btn-form i:hover, .blog-column .meta-tags + h2 a:hover, .links-dark-gray:focus, .links-gray:focus, .sidebar .widget a:focus, .links-body-color:focus, .product-listing .listing-params .btn-form i:focus, .blog-column .meta-tags + h2 a:focus { color: <?php echo $tz_colorset3_c1; ?>; }
            .orange-button, .comment-list .comment-reply-link, .form-submit input[type="submit"], .sidebar input[type="submit"], .tooltip .button, .merchant-button { background: <?php echo $tz_colorset3_c1; ?>; border-color: <?php echo $tz_colorset3_c1; ?>; }
            .checkbox .icon-check { color: <?php echo $tz_colorset3_c1; ?>; }
            .topmain > div .logo .plain-text:hover { color: <?php echo $tz_colorset3_c1; ?>; }
            @media only screen and (max-width: 768px) {
                    .primary.btn { background-color: <?php echo $tz_colorset3_c1; ?>; border-color: <?php echo $tz_colorset3_c1; ?>; }
            }
            @media only screen and (min-width: 768px) {
                    .gumby-no-touch .main-menu-container .main-menu > li:hover > a, .gumby-no-touch .main-menu-container .main-menu > li:focus > a, .gumby-touch .main-menu-container .main-menu > li:hover > a, .gumby-touch .main-menu-container .main-menu > li:focus > a { color: <?php echo $tz_colorset3_c1; ?>; }
                    .gumby-no-touch .main-menu-container .main-menu > li .dropdown li:hover a, .gumby-no-touch .main-menu-container .main-menu > li .dropdown li:focus a, .gumby-touch .main-menu-container .main-menu > li .dropdown li:hover a, .gumby-touch .main-menu-container .main-menu > li .dropdown li:focus a { color: <?php echo $tz_colorset3_c1; ?>; }
            }
            .master-footer a:hover, .master-footer a:focus { color: <?php echo $tz_colorset3_c1; ?>; }
            .row-homepage-featured > div > a:hover h3, .row-homepage-featured > div > a:focus h3 { color: <?php echo $tz_colorset3_c1; ?>; }
            .homepage-products > div a:hover h4, .homepage-products > div a:focus h4 { color: <?php echo $tz_colorset3_c1; ?>; }
            .homepage-products > div a .from span { color: <?php echo $tz_colorset3_c1; ?>; }
            .paginate .page-numbers:hover, .paginate .page-numbers:focus, .paginate .buttons a:hover, .paginate .buttons a:focus { border-color: <?php echo $tz_colorset3_c1; ?>; }
            .product-listing-container.grid-view .product .product-view .price span { color: <?php echo $tz_colorset3_c1; ?>; }
            .required, .row-write-review-head label + span, .row-write-comment-head label + span { color: <?php echo $tz_colorset3_c1; ?>; }
            .retailers-table table .sort-head.headerSortDown i { color: <?php echo $tz_colorset3_c1; ?>; }
            .retailers-table table .sort-head.headerSortUp i { color: <?php echo $tz_colorset3_c1; ?>; }
            .dropcap { color: <?php echo $tz_colorset3_c1; ?>; }
            .highlight { background: <?php echo $tz_colorset3_c1; ?>; }

            .gumby-no-touch .navbar li .dropdown ul li a:hover, .gumby-touch .navbar li .dropdown ul li a.active { color: <?php echo $tz_colorset3_c1; ?>; }
            .gumby-no-touch .navbar ul li:hover > a, .gumby-touch .navbar ul li.active > a { color: <?php echo $tz_colorset3_c1; ?>; }
            .product-listing-container .product .product-desc h2 a:hover, .product-listing-container .product .product-desc h2 a:focus { color: <?php echo $tz_colorset3_c1; ?>; }

            /* Orange Hover */
            .pager .short .btn:hover, .pager .short .btn:focus { background: <?php echo $tz_colorset3_c2; ?>; }
            .orange-button-hover, .comment-list .comment-reply-link:hover, .form-submit input[type="submit"]:hover, .form-submit input[type="submit"]:focus, .sidebar input[type="submit"]:hover, .sidebar input[type="submit"]:focus, .tooltip .button:hover, .tooltip .button:focus, a:hover .merchant-button, a:focus .merchant-button { background: <?php echo $tz_colorset3_c2; ?>; border-color: <?php echo $tz_colorset3_c2; ?>; }
            .primary.btn:hover, .primary.btn:focus { background-color: <?php echo $tz_colorset3_c2; ?>; }

            /* Footer background */
            .master-footer, .master-footer p { background: <?php echo $tz_colorset4_c1; ?>; }

            /* $footer-header: #83929B; */
            .master-footer .widget-title { color: <?php echo $tz_colorset4_c3; ?>; }

            /* $footer-text: #c8ced2; */
            .master-footer .row { color: <?php echo $tz_colorset4_c3; ?>; }
        </style>
        <?php
        if(is_singular('product') && $aw_theme_options['tz_enable_adsense'] == 'true')
            ?>
            <!-- ad sense start -->
            <script type="text/javascript" charset="utf-8">
            (function(G,o,O,g,L,e){G[g]=G[g]||function(){(G[g]['q']=G[g]['q']||[]).push(
            arguments)},G[g]['t']=1*new Date;L=o.createElement(O),e=o.getElementsByTagName(
            O)[0];L.async=1;L.src='//www.google.com/adsense/search/async-ads.js';
            e.parentNode.insertBefore(L,e)})(window,document,'script','_googCsa');
            </script>
            <!-- ad sense end   -->
            <?php
        ?>

</head>

<body <?php body_class(); ?>>

<header class="masterheader">
	<div class="row tophead">
		<div class="twelve columns">
			
			<?php wp_nav_menu(array('theme_location' => 'top-nav', 'container' => '', 'items_wrap' => '<ul id="%1$s" class="topnav %2$s">%3$s</ul>')); ?>
                        <?php do_action('aw_show_header_social_links'); ?>
                    
		</div>
	</div>

	<div class="row topmain">
		<div class="twelve columns">
			<div class="four columns top-search push_eight">
                            <form action="<?php echo home_url(); ?>/" id="primary-search-form">
				<ul>
					<li class="append field">
						<input name="s" id="primary-search-input" class="wide input" type="search" value="<?php if(isset($_GET['s']) && !isset($_GET['product'])) { echo sanitize_text_field(stripslashes(trim($_GET['s']))); } ?>" placeholder="<?php _e('I am shopping for...', 'framework')?>" />
						<div id="primary-search-btn" class="medium primary btn"><a href="#"><i class="icon-search"></i></a></div>
					</li>
				</ul>
                            </form>
			</div>

			<div class="eight columns logo pull_four">
<?php if ($aw_theme_options['tz_plain_logo'] == 'true'): ?>
                                <a class="plain-text" href="<?php echo home_url();  ?>"><?php bloginfo( 'name' ); ?></a> <p><?php bloginfo('description'); ?></p>
<?php elseif($aw_theme_options['tz_logo_url'] != ''): ?>
                                <a href="<?php echo home_url() ?>"><img src="<?php echo $aw_theme_options['tz_logo_url']; ?>" alt="<?php bloginfo( 'name' ); ?>" /></a> <p><?php bloginfo('description'); ?></p>
<?php else: ?>
                                <a class="plain-text" href="<?php echo home_url();  ?>"><?php bloginfo( 'name' ); ?></a> <p><?php bloginfo('description'); ?></p>
<?php endif; ?>				
			</div>
		</div>
	</div>

	<nav class="row navbar main-menu-container" id="nav1">
		<!-- Toggle for mobile navigation, targeting the <ul> -->
		<a class="toggle" gumby-trigger="#nav1 > ul" href="#"><i class="icon-menu"></i></a>
                <?php
                if (has_nav_menu('main-nav')) {
                    wp_nav_menu(array('theme_location' => 'main-nav', 'container' => '', 'items_wrap' => '<ul id="%1$s" class="twelve columns main-menu %2$s">%3$s</ul>', 'walker' => new aw_Walker_Page_Gumby() ));
                }
                ?>
	</nav>

	<?php 
		if( ! is_page_template('template-homepage.php') && ! is_front_page()) :
			echo '<section class="row breadcrumbs"><div class="twelve columns">';
                        get_template_part('functions/views/frontend/content', 'breadcrumb');
			echo '</div></section>';
		endif;
	?>

</header>

<div class="row master-row">