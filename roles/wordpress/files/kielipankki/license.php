<?php
/*
    Template Name: License template – Generic
*/

$supported_languages = array('fi', 'en');

$lang ="en";

if (isset ($_GET['lang'])) {
    $lang = $_GET['lang'];
}

if (! in_array($lang, $supported_languages)) {
    $lang='en';
}

if ($lang=='fi') {
    $lang_locale = 'lang="fi"';
}

if ($lang=='en') {
    $lang_locale = 'lang="en-GB"';
}

$show_last_modified = get_field('show_last_modified');

// if this date is newer than the license's last change date,
// it will be used instead of the date of the license
$licence_template_last_updated = '24.5.2022';

?>
<!doctype html>
<!--[if lt IE 7 ]><html class="ie ie6" <?php echo $lang_locale; ?>> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" <?php echo $lang_locale; ?>> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" <?php echo $lang_locale; ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html <?php echo $lang_locale; ?>>
<!--<![endif]-->
<?php
get_header();
?>

<body>
<header class="header" role="banner">
<?php

$i18n = array(
    'MAIN_MENU' => array (
        'en' => 'Main menu - English',
        'sv' => 'Main menu - Swedish',
        'fi' => 'Main menu - Finnish'
    ),

    'LANG_SWITCH' => array (
        'en' => '<a href="?lang=fi">Näytä lisenssiteksti suomeksi</a>',
        'sv' => '<a href="?lang=fi">Näytä lisenssiteksti suomeksi</a>',
        'fi' => '<a href="?lang=en">Show this license text in English</a>'
    ),



    'END_USER_LICENSE' => array (
        'en' => 'end-user license',
        'sv' => 'end-user license',
        'fi' => 'loppukäyttäjän lisenssisopimus'
    ),

    'RESOURCE' => array (
        'en' => 'Resource',
        'sv' => 'Resource',
        'fi' => 'Aineisto'
    ),
    'COPYRIGHT_HOLDER' => array (
        'en' => 'Rightholder',
        'sv' => 'Rightholder',
        'fi' => 'Oikeudenhaltija'
    ),

    'PREAMBLE_PUB' => array(
        'en' => 'The Rightholder grants the End-User a free, non-exclusive and perpetual (for the duration of the copyright) right to use and make copies of the Resource, distribute copies and present the Resource in public as such, as modified, or as part of a compilation or derived work. The permission applies to all known or future modes and means of communication and includes a right to make modifications enabling the use of the Resource on other devices and in other formats.',
        'sv' => 'The Rightholder grants the End-User a free, non-exclusive and perpetual (for the duration of the copyright) right to use and make copies of the Resource, distribute copies and present the Resource in public as such, as modified, or as part of a compilation or derived work. The permission applies to all known or future modes and means of communication and includes a right to make modifications enabling the use of the Resource on other devices and in other formats.',
        'fi' => 'Oikeudenhaltija myöntää Käyttäjälle maksuttoman, ei-yksinomaisen ja pysyvän (tekijänoikeuden voimassaoloajan kestävän) luvan käyttää ja kopioida Aineistoa, levittää Aineiston kopioita sekä esittää Aineistoa julkisesti muutettuna, muuttamattomana tai osana yhteenliitettyä teosta. Mainitut oikeudet koskevat kaikkia tunnettuja viestintävälineitä ja muotoja ja sisältävät oikeuden tehdä sellaisia muutoksia, jotka mahdollistavat Käyttäjälle Aineiston käyttämisen toisissa laitteissa ja formaateissa.'
    ),

    'PREAMBLE_ACA' => array (
        'en' => 'The Rightholder grants the End-User a free, non-exclusive and perpetual (for the duration of the copyright) right to use and make copies of the Resource for educational, teaching or research purposes in the End-User’s organization as such, as modified, or as part of a compilation or derived work. The permission applies to all known or future modes and means of communication and includes a right to make modifications enabling the use of the Resource on other devices and in other formats.',
        'sv' => 'The Rightholder grants the End-User a free, non-exclusive and perpetual (for the duration of the copyright) right to use and make copies of the Resource for educational, teaching or research purposes in the End-User’s organization as such, as modified, or as part of a compilation or derived work. The permission applies to all known or future modes and means of communication and includes a right to make modifications enabling the use of the Resource on other devices and in other formats.',
        'fi' => 'Oikeudenhaltija myöntää Käyttäjälle maksuttoman, ei-yksinomaisen ja pysyvän (tekijänoikeuden voimassaoloajan kestävän) oikeuden käyttää ja kopioida Aineistoa opiskelu-, opetus- tai tutkimuskäyttöön Käyttäjän oman organisaation sisällä muutettuna, muuttamattomana tai osana yhteenliitettyä teosta. Mainitut oikeudet koskevat kaikkia tunnettuja viestintävälineitä ja muotoja ja sisältävät oikeuden tehdä sellaisia muutoksia, jotka mahdollistavat Käyttäjälle Aineiston käyttämisen toisissa laitteissa ja formaateissa.'
    ),

    'PREAMBLE_RES' => array (
        'en' => 'The Rightholder grants the End-User a personal free, non-exclusive and perpetual (for the duration of the copyright) right to use and make copies of the Resource for the purpose agreed with the End-User as such, as modified, or as part of a compilation or derived work. The permission applies to all known or future modes and means of communication and includes a right to make modifications enabling the use of the Resource on other devices and in other formats.',
        'sv' => 'The Rightholder grants the End-User a personal free, non-exclusive and perpetual (for the duration of the copyright) right to use and make copies of the Resource for the purpose agreed with the End-User as such, as modified, or as part of a compilation or derived work. The permission applies to all known or future modes and means of communication and includes a right to make modifications enabling the use of the Resource on other devices and in other formats.',
        'fi' => 'Oikeudenhaltija myöntää Käyttäjälle henkilökohtaisen maksuttoman, ei-yksinomaisen ja pysyvän (tekijänoikeuden voimassaoloajan kestävän) oikeuden käyttää ja kopioida Aineistoa Käyttäjän kanssa sovittuun tarkoitukseen muutettuna, muuttamattomana tai osana yhteenliitettyä teosta. Mainitut oikeudet koskevat kaikkia tunnettuja viestintävälineitä ja muotoja ja sisältävät oikeuden tehdä sellaisia muutoksia, jotka mahdollistavat Käyttäjälle Aineiston käyttämisen toisissa laitteissa ja formaateissa.'
    ),

    'HEADING_ADDITIONAL_TERMS' => array (
        'en' => 'Additional license terms as defined in the Terms of Service Agreement',
        'sv' => 'Additional license terms as defined in the Terms of Service Agreement',
        'fi' => 'Palveluehtojen mukaiset lisenssiehdot'
    ),

    'HEADING_ID_ACCESS' => array (
        'en' => 'Identification and Access Conditions',
        'sv' => 'Identification and Access Conditions',
        'fi' => 'Tunnistamiseen ja pääsyyn liittyvät ehdot'
    ),

    'ID' => array (
        'en' => 'The End-User must be authenticated or identified.',
        'sv' => 'The End-User must be authenticated or identified.',
        'fi' => 'Käyttäjän on tunnistauduttava.'
    ),
    'AFFIL=FIN-CLARIN' => array (
        'en' => 'The user must be affiliated with a member organization of the <a href="https://www.kielipankki.fi/organization/fin-clarin/fin-clarin-members/">FIN-CLARIN</a> consortium.',
        'sv' => 'The user must be affiliated with a member organization of the <a href="https://www.kielipankki.fi/organization/fin-clarin/fin-clarin-members/">FIN-CLARIN</a> consortium.',
        'fi' => 'Käyttäjän on kuuluttava johonkin <a href="https://www.kielipankki.fi/organization/fin-clarin/fin-clarin-members/">FIN-CLARIN-konsortion</a> jäsenorganisaatioon.'
    ),
    'AFFIL=EDU' => array (
        'en' => 'The End-User must be affiliated with a community of researchers through a university or research institution.',
        'sv' => 'The End-User must be affiliated with a community of researchers through a university or research institution.',
        'fi' => 'Käyttäjän on kuuluttava yliopistojen tai korkeakoulujen tutkimushenkilöstöön päästäkseen käyttämään Aineistoa.'
    ),
    'AFFIL=META' => array (
        'en' => 'The End-User needs to be affiliated with the general community of language research and technology researchers.',
        'sv' => 'The End-User needs to be affiliated with the general community of language research and technology researchers.',
        'fi' => 'Käyttäjän on kuuluttava yleiseen kielen tai kieliteknologian tutkijoiden yhteisöön (META) päästäkseen käyttämään Aineistoa.'
    ),
    'FF' => array (
        'en' => 'A fee will be charged for getting access to the Resource.',
        'sv' => 'A fee will be charged for getting access to the Resource.',
        'fi' => 'Aineiston käyttämisestä peritään maksu.'
    ),
    'PLAN' => array (
        'en' => 'The End-User must present a research plan before the license can be granted. The End-User may only use the Resource for the purpose described in the research plan.',
        'sv' => 'The End-User must present a research plan before the license can be granted. The End-User may only use the Resource for the purpose described in the research plan.',
        'fi' => 'Käyttäjän on esitettävä tutkimussuunnitelma ennen lisenssin myöntämistä. Käyttäjä saa käyttää Aineistoa vain tutkimussuunnitelman mukaiseen tarkoitukseen.'
    ),
    'HEADING_USAGE' => array (
        'en' => 'General conditions of use',
        'sv' => 'General conditions of use',
        'fi' => 'Käyttöön liittyvät ehdot'
    ),
    'BY' => array (
        'en' => 'The Author(s) of the Resource must be mentioned in connection with use.',
        'sv' => 'The Author(s) of the Resource must be mentioned in connection with use.',
        'fi' => 'Aineiston tekijä(t) on mainittava käytön yhteydessä.'
    ),
    'NC' => array (
        'en' => 'The Resource may not be used for profit-making purposes. Research projects involving Business Finland, the Academy of Finland or other similar parties are not considered to involve profit-making purposes, even if some of the funding comes from companies.',
        'sv' => 'The Resource may not be used for profit-making purposes. Research projects involving Business Finland, the Academy of Finland or other similar parties are not considered to involve profit-making purposes, even if some of the funding comes from companies.',
        'fi' => 'Aineistoa ei saa käyttää ansiotarkoituksessa. Business Finlandin, Suomen Akatemian tai muut vastaavat tutkimushankkeet eivät ole ansiokäyttöä, vaikka rahoituksessa olisi mukana myös yrityksiltä saatava rahoitusosuus.'
    ),
    'INF' => array (
        'en' => 'The End-User is required to inform the Rightholder in case a publication is created on the basis of the Resource.',
        'sv' => 'The End-User is required to inform the Rightholder in case a publication is created on the basis of the Resource.',
        'fi' => 'Mikäli Aineistosta syntyy julkaisu, Käyttäjän on ilmoitettava siitä Aineiston Oikeudenhaltijalle.'
    ),
    'LOC' => array (
        'en' => 'The Resource may only be used in a specific location, centre or service.',
        'sv' => 'The Resource may only be used in a specific location, centre or service.',
        'fi' => 'Aineiston käyttö on sallittu ainoastaan tietyssä sijaintipaikassa, keskuksessa tai palvelussa. '
    ),
    'LRT' => array (
        'en' => 'The Resource may only be used for language research and technology development.',
        'fi' => 'Aineistoa saa käyttää ainoastaan kielentutkimukseen ja kieliteknologiseen kehitystyöhön.'
    ),
    'PRIV' => array (
        'en' => 'There are personal data in the Resource. The End-User must comply with the data processing terms and conditions of this Resource. The End-User may process the personal data in the Resource only as long as there is a legal purpose for the processing, after which the personal data must be deleted.',
        'sv' => 'There are personal data in the Resource. The End-User must comply with the data processing terms and conditions of this Resource. The End-User may process the personal data in the Resource only as long as there is a legal purpose for the processing, after which the personal data must be deleted.',
        'fi' => 'Aineisto sisältää henkilötietoja, joiden käsittelyssä Käyttäjän on noudatettava aineistokohtaisia tietosuojaehtoja. Käyttäjä saa käsitellä Aineiston sisältämiä henkilötietoja vain niin kauan kuin sillä on käsittelyyn lainmukainen peruste, minkä jälkeen henkilötiedot on poistettava.'
    ),
    'HEADING_DISTRIBUTION' => array (
        'en' => 'Distribution conditions',
        'sv' => 'Distribution conditions',
        'fi' => 'Välittämiseen liittyvät ehdot'
    ),
    'NORED' => array (
        'en' => 'The Resource may not be disclosed to third parties. However, the Resource may be disclosed to subcontractors with whom appropriate agreements have been made concerning the subcontracted service and the processing of personal data. Subcontractors are only allowed to use the Resource to perform the service.',
        'sv' => 'The Resource may not be disclosed to third parties. However, the Resource may be disclosed to subcontractors with whom appropriate agreements have been made concerning the subcontracted service and the processing of personal data. Subcontractors are only allowed to use the Resource to perform the service.',
        'fi' => 'Aineistoa ei saa luovuttaa kolmansille osapuolille. Aineistoa saa kuitenkin luovuttaa alihankkijoille, joiden kanssa on tehty asianmukaiset sopimukset alihankittavasta palvelusta sekä henkilötietojen käsittelystä. Alihankkijat saavat käyttää Aineistoa ainoastaan palvelun suorittamista varten.'
    ),
    'ND' => array (
        'en' => 'It is not permitted to distribute new works derived from the Resource or containing parts of the original Resource.',
        'sv' => 'It is not permitted to distribute new works derived from the Resource or containing parts of the original Resource.',
        'fi' => 'Aineistosta tehtyjen johdannaisten tai alkuperäisen Aineiston osia sisältävien uusien teosten välittäminen on kielletty.'
    ),
    'SA' => array (
        'en' => 'Derivative works can be redistributed under similar conditions, i.e. the license is reciprocal.',
        'sv' => 'Derivative works can be redistributed under similar conditions, i.e. the license is reciprocal.',
        'fi' => 'Käyttäjä saa levittää muutettua tai yhteenliitettyä Aineistoa ainoastaan samoilla ehdoilla kuin alkuperäistä Aineistoa.'
    ),
    'DEP' => array (
        'en' => 'Modified versions of the Resource can be made available through the CLARIN service by separate agreement with CLARIN.',
        'sv' => 'Modified versions of the Resource can be made available through the CLARIN service by separate agreement with CLARIN.',
        'fi' => 'Aineiston muokattuja versioita voidaan saattaa saataviin CLARIN-palvelun kautta sopimalla asiasta erikseen CLARINin kanssa.'
    ),
    'HEADING_OTHER' => array (
        'en' => 'Other terms of use',
        'sv' => 'Other terms of use',
        'fi' => 'Muut käyttöehdot'
    ),
    'PLURAL' => array (
        'en' => 's',
        'sv' => 's',
        'fi' => 't'
    ),
    'UNKNOWN_ERROR' => array (
        'en' => 'unknown error',
        'sv' => 'unknown error',
        'fi' => 'tuntematon virhe'
    ),

    'OTHER' => array (
        'en' => 'There are other non-standard conditions in the license that the End-User should pay attention to.',
        'sv' => 'There are other non-standard conditions in the license that the End-User should pay attention to.',
        'fi' => 'Aineistoon liittyy muita erikoisehtoja, joihin Käyttäjän on kiinnitettävä huomiota.'
    ),


    'ENDING' => array (
        'en' => 'This license has been made in compliance with copyright agreements by WIPO - the World Intellectual Property Organization. The rights granted in this license shall be so interpreted that in case applicable intellectual property laws grant rights not mentioned in this license, they are also regarded as part of the rights to be licensed; the purpose of this license is not to restrict any rights intended to be licensed within different legal systems. Additional rights to the Resource may be agreed separately in writing.<p>The Language Bank has the right to amend these terms on legitimate grounds, which may relate to, including but not limited to, instructions given by authorities, best practice, or changes in data protection laws or other applicable laws. A notification of any changes made shall be provided sixty (60) days before they take effect on the Language Bank’s website, as well as by email to the addresses included in the User’s application for access to the Resource.',
        'fi' => 'Käyttölupa on laadittu vastaamaan WIPOn tekijänoikeussopimuksia. Käyttöluvan myöntämiä oikeuksia tulee tulkita siten, että mikäli soveltuvassa tekijänoikeuslaissa myönnetään oikeuksia, joita tässä käyttöluvassa ei ole mainittu, myös ne katsotaan kuuluvaksi myönnettyihin oikeuksiin; tätä käyttölupaa ei ole tarkoitettu rajoittamaan eri oikeusjärjestyksissä myönnetyiksi tarkoitettuja oikeuksia. Aineiston käyttämisestä tätä laajemmin voidaan sopia erikseen kirjallisesti. <p>Kielipankilla on oikeus tehdä näihin ehtoihin muutoksia perustelluista syistä, jotka voivat liittyä esimerkiksi viranomaisen antamiin ohjeisiin, hyviin käytäntöihin tai tietosuojalainsäädännön tai muun soveltuvan lainsäädännön muutoksiin. Muutoksista ilmoitetaan 60 päivää ennen niiden voimaantuloa Kielipankin verkkosivuilla sekä sähköpostitse niihin osoitteisiin, jotka on ilmoitettu Kielipankille Aineiston käyttöoikeuden hakemisen yhteydessä.'
    ),

    'SELFLINK_URN' => array (
        'en' => 'Persistent Identifier of this license',
        'sv' => 'Persistent Identifier of this license',
        'fi' => 'Tämän lisenssin pysyvä tunniste'
    ),

    'LAST_UPDATED' => array (
        'en' => 'Last updated',
        'sv' => 'Last updated',
        'fi' => 'Viimeksi päivitetty'
    )

);

function lic_laundry_tag_list($filter_out) {
    $lic_id_access    = get_field('field_569388a5a6fe1');
    if (! $lic_id_access) {
        $lic_id_access = array();
    }
    $lic_usage        = get_field('field_5693b54a4348c');
    if (! $lic_usage) {
        $lic_usage = array();
    }
    $lic_distribution = get_field('field_5693b5854348d');
    if (! $lic_distribution) {
        $lic_distribution = array();
    }
    $tags = array_merge($lic_id_access, $lic_usage, $lic_distribution);

    $tags = array_diff($tags, $filter_out);
    $tag_list= '';
    foreach ($tags as $tag) {
        $tag_list = $tag_list . ' +' . $tag;
    }
    return $tag_list;
}

function get_urn_href($urn,$lang) {
    global $i18n;
    if ($urn) {
        return '<a href="http://urn.fi/'.$urn.'">'.$urn.'</a>';
    } else {
        return '<b>'.$i18n['UNKNOWN_ERROR'][$lang].'</b>';
    }
}

function union_of_values($array1, $array2) {
    return array_unique(array_merge($array1, $array2));
}

global $post;

if (has_post_thumbnail()){
    echo get_the_post_thumbnail( $post->ID, 'full' );
} else {
/*
no post_thumbnail
check if page has parent, if it does, check if parent does have post_thumbnail
 */
    if(intval($post->post_parent) > 0){
        $ppthumb = get_the_post_thumbnail( intval($post->post_parent), 'full' );
        if($ppthumb){
            echo $ppthumb;
        } else {
            /* parent has no post_thumbnail set, show default image */
            echo '<img src="'.get_template_directory_uri().'/images/Kielipankki_Kielipankki.png" alt="' . the_title_attribute( array( 'echo' => 0 ) ) . '"/>';
        }
    } else {
        /* no post parent and no post_thumbnail set -> show default image */
        echo '<img src="'.get_template_directory_uri().'/images/Kielipankki_Kielipankki.png" alt="' . the_title_attribute( array( 'echo' => 0 ) ) . '"/>';
    }
}
?>
</header>
<div class="wrapper nav-wrapper">
  <nav class="page-nav" role="navigation">
<?php

$men = array(
    'theme_location'  => '',
    'menu'            => $i18n['MAIN_MENU'][$lang],
    'container'       => 'div',
    'container_class' => '',
    'container_id'    => '',
    'menu_class'      => 'menu',
    'menu_id'         => '',
    'echo'            => true,
    'fallback_cb'     => 'wp_page_menu',
    'before'          => '',
    'after'           => '',
    'link_before'     => '',
    'link_after'      => '',
    'items_wrap'      => '<ul id="%1$s" class="nav clearfix">%3$s</ul>',
    'depth'           => 0,
    'walker'          => ''
);

wp_nav_menu( $men );

?>
    <div class="nav-mobile" id="mobile-nav"> <a href="#mobile-nav" class="mobile-nav-trigger"><span class="fontawesome-reorder"></span></a> </div>
  </nav>
</div>
<div class="content lbluebg">
  <div class="container">
    <div class="onecol">
<?php



$lic_version='v2.1';

$lic_type = get_field('lic_type');

/*
foreach($pages as $page){
    echo $page->post_title.'<br />';
}
 */

$additional_cond_text="-/-";

echo '<header>';

switch ($lic_type) {
    case 'CLARIN PUB':
        echo '<h1 style="display: inline-block;" class="first">CLARIN PUB ' . $i18n['END_USER_LICENSE'][$lang] . lic_laundry_tag_list(array()) . ' ' . $lic_version . '</h1>';
        $preamble = $i18n['PREAMBLE_PUB'][$lang];
        break;

    case 'CLARIN ACA':

        $id_access_default = array('ID');
        $lic_id_access = get_field('field_569388a5a6fe1');
        if ($lic_id_access) {
            update_field('field_569388a5a6fe1', union_of_values($id_access_default, $lic_id_access)); // ID ACCESS
        } else {
            update_field('field_569388a5a6fe1', $id_access_default); // ID ACCESS
        }

        $usage_default=array('BY');
        $lic_usage = get_field('field_5693b54a4348c');
        if ($lic_usage) {
            update_field('field_5693b54a4348c', union_of_values($usage_default, $lic_usage));
        } else {
            update_field('field_5693b54a4348c', $usage_default);
        }

        $distribution_default=array('NORED');
        $lic_distribution = get_field('field_5693b5854348d');

        if ($lic_distribution) {
            update_field('field_5693b5854348d', union_of_values($distribution_default, $lic_distribution));
        } else {
            update_field('field_5693b5854348d', $distribution_default);
        }

        $filter_out = array_merge($id_access_default, $usage_default, $distribution_default);

        echo '<h1 style="display: inline-block;" class="first">CLARIN ACA ' . $i18n['END_USER_LICENSE'][$lang] . lic_laundry_tag_list($filter_out) . ' ' . $lic_version . '</h1>';
        $preamble =  $i18n['PREAMBLE_ACA'][$lang];
        break;

    case 'CLARIN RES':
        // var_dump($values);
        //Set +PLAN as default only if +FF is not set
        $field = get_field_object('lic_id_access');
        $values = $field['value'];
        if (in_array('FF', $values)) {
            $id_access_default=array('ID');
        } else {
            $id_access_default=array('ID', 'PLAN');
        }

        $lic_id_access = get_field('field_569388a5a6fe1');
        if ($lic_id_access) {
            update_field('field_569388a5a6fe1', union_of_values($id_access_default, $lic_id_access)); // ID ACCESS
        } else {
            update_field('field_569388a5a6fe1', $id_access_default); // ID ACCESS
        }

        $lic_usage = get_field('field_5693b54a4348c');
        $usage_default=array('BY');
        if ($lic_usage) {
            update_field('field_5693b54a4348c', union_of_values($usage_default, $lic_usage));
        } else {
            update_field('field_5693b54a4348c', $usage_default);
        }

        $distribution_default=array('NORED');
        $lic_distribution = get_field('field_5693b5854348d');

        if ($lic_distribution) {
            update_field('field_5693b5854348d', union_of_values($distribution_default, $lic_distribution));
        } else {
            update_field('field_5693b5854348d', $distribution_default);
        }

        $filter_out = array_merge($id_access_default, $usage_default, $distribution_default);

        echo '<h1 style="display: inline-block;" class="first">CLARIN RES ' . $i18n['END_USER_LICENSE'][$lang] . lic_laundry_tag_list($filter_out) . ' ' . $lic_version . '</h1>';

        $preamble =  $i18n['PREAMBLE_RES'][$lang];
        break;
}
echo '  <span style="float: right; vertical-align: top"><b>'.$i18n['LANG_SWITCH'][$lang].'</b></span>';
echo '</header>';
/* Allow for multiple names/urns split with semicolons. */
$urn_list=explode(";", get_field('lic_resource_urn'));
$urn_list_count=count($urn_list);
$name_list=explode(";", get_field('lic_'.$lang.'_resource_name'));
$name_list_count=count($name_list);

/* One resource: one line of output */
if ($name_list_count == 1) {
    echo '<p><b>' . $i18n['RESOURCE'][$lang]  . '</b>: ' . $name_list[0] . ' (URN: '.get_urn_href($urn_list[0],$lang). ')</p>';
} else { /* multiple resources: bullet list*/
    echo '<p><b>' . $i18n['RESOURCE'][$lang].$i18n['PLURAL'][$lang]  . '</b>:<br>';
    echo '<ul>';
    for ($I = 0; $I < $name_list_count ; $I++) {
        $urn="";
        if ($I < $urn_list_count) {
            $urn = $urn_list[$I];
        }
        echo '<li>' . $name_list[$I] . ' (URN: '.get_urn_href($urn,$lang). ')</li>';
    }
    echo '</ul>';
}

echo '<p><b>' . $i18n['COPYRIGHT_HOLDER'][$lang] . '</b>: ' . get_field('lic_copyright_holder') . '</p>';

echo $preamble;

echo '<h2>'.$i18n['HEADING_ADDITIONAL_TERMS'][$lang].':</h2>';

$field = get_field_object('lic_id_access');
$values = $field['value'];


if ($values) {
    echo '<h3>'.$i18n['HEADING_ID_ACCESS'][$lang].'</h3>';
    $additional_cond_text = "";
    echo '<ul>';

    foreach($values as $v) {
        echo '<li><b>'.$v.'</b>: '.$i18n[$v][$lang] . '</li>';
    }

    echo '</ul>';
}


$field = get_field_object('lic_usage');
$values = $field['value'];
$choices = $field['choices'];


if ($values) {
    echo '<h3>' . $i18n['HEADING_USAGE'][$lang] . '</h3>';
    $additional_cond_text = "";
    echo '<ul>';

    foreach($values as $v) {
        echo '<li><b>'.$v.'</b>: '.$i18n[$v][$lang] . '</li>';
    }

    echo '</ul>';
}


/*

OTHER is technically part of "Distribution" (to keep things a little bit simpler elsewhere). It is put under its own heading below.

 */

$field = get_field_object('field_5693b5854348d');
$values = $field['value'];
$choices = $field['choices'];

$dist_conditions_list="";

if ($values) {
    foreach($values as $v) {
        if ($v != 'OTHER') {
            $dist_conditions_list= $dist_conditions_list . '<li><b>'.$v.'</b>: '.$i18n[$v][$lang] . '</li>';
        }
    }
}

if ($dist_conditions_list) {
    $additional_cond_text = "";
    echo '<h3>'.$i18n['HEADING_DISTRIBUTION'][$lang].'</h3>';
    echo '<ul>';
    echo $dist_conditions_list;
    echo '</ul>';
}

if (in_array('OTHER', $values)) {
    $additional_cond_text = "";
    echo '<h3>'.$i18n['HEADING_OTHER'][$lang].'</h3>';
    echo '<ul>';
    echo  '<li><b>OTHER</b>: '.$i18n['OTHER'][$lang] . '</li>';
    echo '<p><i>' . get_field('lic_'.$lang.'_other') . '</i></p>';
    echo '</ul>';

}
echo '<p>'.$additional_cond_text.'</p>';


echo '<p>'.$i18n['ENDING'][$lang].'</p>';

$lic_last_updated = get_field('lic_page_last_updated');

// if no last update date given or tempate updated more recently, use date of template update
if (empty($lic_last_updated) || (strtotime($lic_last_updated) < strtotime($licence_template_last_updated)) ) {
    $lic_last_updated = $licence_template_last_updated ;
}


if ($lic_last_updated) {
    echo '<p style="margin-bottom: 3em;"><b>'.$i18n['LAST_UPDATED'][$lang].'</b> : '.$lic_last_updated.'</p>';
}

$lic_selflink_urn = get_field('lic_'.$lang.'_selflink_urn');
if ($lic_selflink_urn) {
    echo '<p><small>'.$i18n['SELFLINK_URN'][$lang].': '.get_urn_href($lic_selflink_urn, $lang).'</small></p>';
}

/* the loop */
if ( have_posts() ) {
    while ( have_posts() ) {
        the_post();
        the_content();
    } // end while
} // end if
?>
</div>
</div>
<?php
get_footer();
?>
</body>
</html>
