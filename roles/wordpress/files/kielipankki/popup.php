<?php
/*
   Template Name: Reference Popup

   Create reference instructions from kielipankki_db corpus table.
   So far we provide 3 variants: Human readable, bibtex, zotero.

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

$key = $_GET['key'];

function get_mysqli_object() {
    /* Function that first gets required username and password details needed to access WordPress database
       and then, with those, returns a mysqli object if no errors occur. */
    require_once('/var/www/html/wp-config.php');
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if (!is_null($mysqli->connect_error)) {
	die();
    }
    return $mysqli;
}

function get_corpus($mysqli, $input) {
    /* Function that returns the first row matching a given (unique) metadata urn
       (or shortname, deprecated) from the corpus table.
       returns false if no matching row is found
     */
    $escaped_input = $mysqli->real_escape_string($input);
    $query = "SELECT * FROM corpus WHERE metadata_urn = '$escaped_input' OR shortname = '$escaped_input'";
    $result = $mysqli->query($query);
    if(! empty( $mysqli->error ) ) {
	echo $mysqli->error;
    }
    $row = $result->fetch_assoc();
    return $row;
}

/*
   Helper function to provide localised texts. If the language cannot be found, the result defaults to English. If the localisation cannot be found the value
   is the key with a "LOCALIZE" warning in front."
 */

function localize($lang,$key) {
    static $l18n = array(
	"fi" => array(
	    "data set" => "korpus", /* generic */
	    "available_at" => "Saatavilla",
	    "ref_heading" => "Viittausohje",
	    "ref_intro" => "Viittaa kielivaraan näin:",
	    "bibtex_intro_text" => "Kopioi koodi alla bibtex-kirjastoosi. Koodi on testattu apacite.sty-tyyillä. Kenttä <code>url</code> ei toimi kaikilla bibtex-tyylillä, kokeile silloin <code>note</code>-kenttää <code>url</code>:n sijaan.",
	    "zotero_intro_text" => "Kopioi koodi alla leikepöydälle. Luo Zoterossa 'Report'-tietueen valitsemalla 'Actions > Import from clipboard'. Zoteron 'Report'-tietue on parhaiten yhteensopiva tyyli.",
	    "show" => "Näytä: ",
	    "search_scholar" => "Etsi viittauksia aineistoon Google Scholar -palvelusta.",
	    "not_found" => "ei löytynyt",
	    "lb_notified" => "Ilmoitus on lähetetty Kielipankin ylläpidolle.",
	),
	"en" => array(
	    "data set" => "data set", /* generic */
	    "ref_heading" => "Reference instructions",
	    "ref_intro" => "Please cite the language resource as follows:",
	    "available_at" => "Retrieved from",
	    "bibtex_intro_text" => "Copy the code below to your bibtex bibliography file. The code has been tested using the style apacite.sty. The field <code>url</code> does not work in all styles. In case of problems try changing it to <code>note</code> instead.",
	    "zotero_intro_text" => "Copy the code below to the clipboard. In Zotero, create a 'Report' item by clicking 'Actions > Import from clipboard'. Zotero's 'Report' item is the most compatible style.",
	    "show" => "Show: ",
	    "search_scholar" => "Search for references to the language resource in Google Scholar",
	    "not_found" => "not found",
	    "lb_notified" => "The Language Bank administrators will be notified.",
	)
    );

    if (! $l18n[$lang]) $lang="en";
    $localized_string=$l18n[$lang][$key];
    if ($localized_string) {
	return $localized_string;
    }
    else {
	// Fallback: Return key with warning.
	return "LOCALIZE:".$key;
    }
}

/*
   Renders author(s) and date. A missing author will not render a date
 */
function render_author_date($lang, $row) {
    $authors=get_authors($lang,$row);
    $date=render_date($row);
    $author_date="";
    $author_count=count($authors);
    if ($authors) {
	/* For one author, just return the rendered author. Otherwise separate with commas and add & for the last entry. */
	if ($author_count == 1) {
	    $author_date .= render_author($authors[0]);
	} else {
	    /* list the authors, put an & in front of the last.*/
	    for ($i = 0; $i < $author_count-1 ; $i++) {
		$author_date .= render_author($authors[$i]).", ";
	    }
	    $author_date .= "&amp; " . render_author($authors[$author_count-1]);
	}
	/* Add date if known */
	if ($date) {
	    $author_date.=$date;
	}
	$author_date .= ". ";
    }
    return $author_date;
}

/* render the author.
   Firstname Lastname => Lastname, F.
   Firstname {van_der_Lastname} => van der Lastname, F.
   {Firstname Lastname} => Firstname Lastname  (for institutions)
 */

function render_author($author) {
    $author = ltrim($author);
    $len_author=strlen($author);
    /* Check for brackets and return the name unchanged. Otherwise return Lastname, F. format */
    if ( ($author[0] == '{') AND ($author[$len_author-1] == '}')) {
	$result = substr($author,1,$len_author-2);
    } else {
	/* Split name at spaces */
	$name_parts=explode(" ", $author);
	$part_count=count($name_parts);

	/* Put Lastname first */
	$result = $name_parts[$part_count-1];
	if ($part_count > 1) {
	    $result .= ",";
	    /* iterate through firstnames and add inital*/
	    for ($I = 0; $I < $part_count-1 ; $I++) {
		$result .= " ".mb_substr($name_parts[$I], 0, 1,'UTF8').".";
	    }
	}
    }
    /* Strip bibtex code and underscores before returning the author */
    return str_replace(Array("{","}","_"),Array("",""," "),$result);
}

/*
   get semicolon (;) separated authors. Returns Array
 */
function get_authors($lang, $row) {
    /* Take "fi" if defined, "en" in all other cases. As fallback, if there is no info in the desired language, try the other language. */
    if ($lang == "fi") {
	$authors = $row['authors_fi'];
	if (! $authors) $authors = $row['authors_en'];
    } else {
	$authors = $row['authors_en'];
	if (! $authors) $authors = $row['authors_fi'];
    }
    /* return array of authors or null, if both columns empty.*/
    if ($authors) {
	return explode(';', $authors );
    } else {
	return [];
    }
}

/*
   Render the date as (date).
 */

function render_date($row) {
    $date=get_date($row);
    if ($date) {
	$date_parts=explode("-", $date);
	return " (".$date_parts[0].")";
    } else {
	return "";
    }
}

function get_date($row) {
    return $row['first_publication_date'];
}

/*
   Put the title in italics.
 */

function render_title($lang, $row) {
    return "<I>".get_title($lang, $row)."</I>";
}

/*
   Get the title depending on the language
 */


function get_title($lang, $row) {
    if ($lang == "fi") {
	return $row['name_fi'];
    } else {
	return $row['name_en'];
    }
}

/*
   The publisher is always us.
 */

function get_publisher() {
    return "Kielipankki";
}

/*
   Render the type as [type].
 */

function render_type($lang, $row) {
    return "[".get_type($lang, $row)."]";
}

/*
   Get the type code and translate (eg. T > text corpus).
 */

function get_type($lang, $row) {
    $type = $row['type'];
    return localize($lang,$type);
}

/*
   Turn the URN into a link.
 */

function render_urn($lang, $row) {
    $urn=get_urn($row);
    $available="";
    if ($urn) {
	$available = "http://urn.fi/".$urn;
    }
    return $available;
}

function get_shortname($row) {
    return $row['shortname'];
}

function get_urn($row) {
    return $row['metadata_urn'];
}

/*
   Render the whole reference.
 */

function render_reference($lang,$row) {
    return render_author_date($lang,$row) .
           render_title($lang, $row) . " " .
           render_type($lang, $row) . ". ".get_publisher(). ". " .
           localize($lang,"available_at")." ".render_urn($lang,$row) ;
}

/*
   Render bibtex code to be used with bibtex.
 */

function render_bibtex($lang,$row, $key) {
    $authors="";
    $bibtex="";
    $author_list = get_authors($lang,$row);
    if ($author_list) $authors = str_replace("  "," ",implode(" and ", $author_list));
    $year   = get_date($row);
    $type   = get_type($lang,$row);
    $bibtex .= "@misc{".get_shortname($row)."_".$lang.",\n";
    if ($authors) {
	$authors = str_replace("_"," ",$authors);
	$bibtex .= " author={". ltrim($authors) ."},\n";
    }
    if ($year) $bibtex .= " year={".$year."},\n";
    $bibtex .= " title={{".get_title($lang,$row)."}},\n";
    $bibtex .= " publisher={".get_publisher()."},\n";
    if ($type) $bibtex .= " type={".$type."},\n";
    $bibtex .= " url={".render_urn($lang, $row)."},\n";
    $bibtex .= "}\n";
    return $bibtex;
}

/*
   Render bibtex code to be used with Zotero.
 */


function render_zotero($lang,$row, $key) {
    $authors="";
    $bibtex="";
    $author_list = get_authors($lang,$row);
    if ($author_list) $authors = str_replace("  "," ",implode(" and ", $author_list));
    $year   = get_date($row);
    $type   = get_type($lang,$row);
    $bibtex .= "@techreport{".get_shortname($row)."_".$lang.",\n";
    if ($authors) {
	$authors = str_replace("_"," ",$authors);
	$bibtex .= " author={". ltrim($authors) ."},\n";
    }
    if ($year) $bibtex .= " year={".$year."},\n";
    $bibtex .= " title={{".get_title($lang, $row)."}},\n";
    $bibtex .= " publisher={".get_publisher()."},\n";
    if ($type) $bibtex .= " type={".$type."},\n";
    $bibtex .= " url={".render_urn($lang, $row)."},\n";
    $bibtex .= "}\n";
    return $bibtex;
}

?>
<!doctype html>
<!--[if lt IE 7 ]><html class="ie ie6" <?php echo $lang_locale; ?>> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" <?php echo $lang_locale; ?>> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" <?php echo $lang_locale; ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html style="margin-top: 1em !important" <?php $echo lang_locale; ?>>
<!--<![endif]-->

    <?php
    get_header();
    ?>

    <body style="margin:1em; margin-top: 1em">
	<script type="text/javascript">
	 // javascript code to show/hide zotero/bibtex code
	 $(document).ready(function() {
	     $('#show_bibtex').click(function() {
		 $('#zotero').hide();
		 $('#bibtex').slideToggle("fast");
             });
             $('#show_zotero').click(function() {
		 $('#bibtex').hide();
		 $('#zotero').slideToggle("fast");
             });
	 });
	</script>


	<div class="content lbluebg">

	    /* the loop */

	    function my_content($content) {
		global $post, $key, $lang;
		$original = $content;
		$mysqli_connection = get_mysqli_object();
		/* change character set to utf8mb4 */
		$mysqli_connection->set_charset("utf8mb4");
		$row = get_corpus($mysqli_connection, $key);
		$content .= '<div class="onecol">';
		if ($row) {
		    // Offer easy language switch
		    $content .= '<div align="right"><a href="/viittaus/?key='.urlencode($key).
				'&lang=fi">[suomeksi]</a> <a href="/viittaus/?key='.urlencode($key).
				'&lang=en">[in English]</a></div>';
		    $content .= '<h2 class="first">'.localize($lang,"ref_heading").": ".get_shortname($row).'</h2>';
		    $content .= localize($lang,"ref_intro");
		    $content .= "<p class='light'>".render_reference($lang, $row)."</p>";

		    // hide Zotero/Bibtex by default, show if clicked
		    $content .= localize($lang,"show").'<a href="#" id="show_bibtex">[Bibtex]</a>';
		    $content .=' <a href="#" id="show_zotero">[Zotero]</a>';
		    $content .='<div id="bibtex" style="display:none">';

		    $content .='<h3>Bibtex</h3>';
		    $content .= localize($lang,"bibtex_intro_text");
		    $content .='<pre class="light">'.render_bibtex($lang,$row,$key).'</pre>';
		    $content .='</div>';
		    $content .='<div id="zotero" style="display:none">';

		    $content .='<h3>Zotero</h3>';
		    $content .= localize($lang,"zotero_intro_text");
		    $content .='<pre class="light">'.render_zotero($lang,$row,$key).'</pre>';
		    $content .='</div>';

		    // search for URN or titles (fi/en) in Google Scholar (not exact, but decent)
		    $content .='<p><a href="https://scholar.google.com/scholar?q='.
			       urlencode(
				   get_urn($row).
				   ' OR "'.get_title("fi", $row).
				   '" OR "'.get_title("en", $row).'"'
			       ).'" target="_parent">'.
			       localize($lang,"search_scholar").'</a></p>';
		} else {
		    $content .= localize($lang,"ref_heading")." <b>".$key."</b> ".localize($lang,"not_found").".<br>";
		    /* waning mail disabled for now, referer is not set properly
		    $content .= localize($lang,"lb_notified");
		    $to = 'matthies@csc.fi'; //for now to test
		    $subject = 'Key error in reference instructions: '.$key;
		    $body = 'The reference instructions were called with an unknown key.<br>Please fix on the page where it came from.<p>';
		    $body .= 'Key: '.$key."<br>";
		    $body .= 'Language: '.$lang."<br>";
		    $body .= 'Referer: '. wp_get_referer();
		    $headers = array('Content-Type: text/html; charset=UTF-8');
		    wp_mail( $to, $subject, $body, $headers );
		    */
		    // status_header(404); // does not seem to work --mma
		}
		$content .='</div>';
		$mysqli_connection->close();
		return $content;
	    }

	    add_filter( 'the_content', 'my_content' );

	    if ( have_posts() ) {
		while ( have_posts() ) {
		    the_post();
		    the_content();
		} // end while
	    } // end if
	    <?php
        if ($show_last_modified) {
        make_last_modified($lang);
    }

	    ?>
	</div>
    </body>
</html>
