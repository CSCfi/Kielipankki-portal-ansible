<?php
// All pages should include this, setting useful ACF fields and rendering appropriate html attributes
$lang = get_field('lang');
$show_last_modified = get_field('show_last_modified');

if (!$lang) {
  $lang='fi';
}

if ($lang=='fi') {
    $lang_locale = 'lang="fi"';
}

if ($lang=='en') {
    $lang_locale = 'lang="en-GB"';
}

if ($lang=='sv') {
    $lang_locale = 'lang="sv-FI"';
}
?>

<!doctype html>
<!--[if lt IE 7 ]><html class="ie ie6" <?php echo $lang_locale; ?>> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" <?php echo $lang_locale; ?>> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" <?php echo $lang_locale; ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html <?php echo $lang_locale; ?>>
<!--<![endif]-->

<?php get_header(); ?>
