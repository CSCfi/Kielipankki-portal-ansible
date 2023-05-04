<footer class="footer">
<div class="container">
<!-- <div class="copyright">© 2015 Kielipankki, FIN-CLARIN ja CSC – Tieteen Tietotekniikan keskus</div>
<div class="footerlinks"><a href="#">Yhteys tekniseen tukeen</a><a href="#">Legal Statement</a></div>
-->


<?php
if ( function_exists('dynamic_sidebar')){
global $lang;
if(isset($lang)){
switch($lang){
case 'fi':
dynamic_sidebar('sidebar-2-fin');
break;
case 'en':
dynamic_sidebar('sidebar-2-eng');
break;
case 'sv':
dynamic_sidebar('sidebar-2-swe');
break;
default:
dynamic_sidebar('sidebar-2-fin');
break;
}
} else{
/* no lang */
}
}
?>
<div class="spacer"></div>
</div>
</footer>
<?php wp_footer(); ?>