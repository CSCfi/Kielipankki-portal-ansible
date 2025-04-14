<head>
<meta charset="UTF-8">
<title><?php wp_title( '|', true, 'right' ) . bloginfo('name'); ?></title>
<link href='https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic' rel='stylesheet' type='text/css'>
<link href="<?php echo get_template_directory_uri(); ?>/tyylit.css" rel="stylesheet" type="text/css">
<?php if( WP_DEBUG === true ) { echo '<link href="'.get_template_directory_uri().'/tyylit_debug.css" rel="stylesheet" type="text/css">'; } ?>
<script src="<?php echo get_template_directory_uri(); ?>/js/jquery-3.7.1.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/SimpleCssParser.js" type="text/javascript"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/polycalc.js" type="text/javascript"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/koodit.js"></script>
<link href="<?php echo get_template_directory_uri(); ?>/calc.css" rel="stylesheet" type="text/css" data-PolyCalc="1">

<!--[if lte IE 8]>
<script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/respond.min.js"></script>
<![endif]-->

<link rel="apple-touch-icon" sizes="57x57" href="<?php echo get_template_directory_uri(); ?>/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="<?php echo get_template_directory_uri(); ?>/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="<?php echo get_template_directory_uri(); ?>/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="<?php echo get_template_directory_uri(); ?>/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="<?php echo get_template_directory_uri(); ?>/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="<?php echo get_template_directory_uri(); ?>/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="<?php echo get_template_directory_uri(); ?>/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="<?php echo get_template_directory_uri(); ?>/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_template_directory_uri(); ?>/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192"  href="<?php echo get_template_directory_uri(); ?>/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo get_template_directory_uri(); ?>/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="<?php echo get_template_directory_uri(); ?>/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo get_template_directory_uri(); ?>/favicon-16x16.png">
<!-- <link rel="manifest" href="<?php echo get_template_directory_uri(); ?>/manifest.json"> -->
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">
<?php wp_head(); ?>
</head>
