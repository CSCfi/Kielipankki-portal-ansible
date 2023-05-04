<?php
/*
	Template Name: Reference Popup

Create reference instructions from raw data from Tablepress.
So far we provide 3 variants: Human readable, bibtex, zotero.

*/

$lang ="en";

if (isset ($_GET['lang'])) {
   $lang = $_GET['lang'];
}


// Only fi/en supportet so far, sv=en.
if($lang == "sv") {
  $lang="en";
}

$key = $_GET['key'];

// This funtion maps the corpus short name in column1 to the row number in a given table_id
// FIXME: This code is highly ineffiecent, but I did not manage to figure out how to pre-compile a name->row mapping.
// So for know, we use brute force. --matthies 9/2016.
// This function searches first the production table and then the upcoming table. Assuming the corpus Ids are unique. Which they should be.

function get_row_table ($corpus_shortname_or_urn) {

    // The production table
    $table_id=14;
    // $table_id=22; // from "preview"
    $table = TablePress::load_model( 'table' )->load($table_id);

    $max = count($table['data']);
    for($i = 1; $i < $max;$i++)
    // echo $table['data'][$i][0].":";
    {
      if ($table['data'][$i][0] == $corpus_shortname_or_urn or $table['data'][$i][17] == $corpus_shortname_or_urn ) {
        return Array($table, $i);
      }
    }

    // The Upcoming resources table
    $table_id=17;
    // $table_id=23; // from "preview"
    $table = TablePress::$controller->model_table->load($table_id);

    $max = count($table['data']);
    for($i = 1; $i < $max;$i++)
    {
      //echo $table['data'][$i][0];
      if ($table['data'][$i][0] == $corpus_shortname_or_urn or $table['data'][$i][17] == $corpus_shortname_or_urn ) {
        return Array($table, $i);
      }
    }
    return Array (-1,-1);

}

function get_cell($table, $row, $column) {
  return $table['data'][$row][$column];
}

/*
Helper function to provide localised texts. If the language cannot be found, the result defaults to English. If the localisation cannot be found the value
is the key with a "LOCALIZE" warning in front."
 */

function localize($lang,$key) {

 static $l18n = array(
    "fi" => array(
         "T" => "tekstikorpus",
         "P" => "puhekorpus",
         "M" => "multimodaalinen korpus",
         "K" => "korpus", /* generic */
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
         "T" => "text corpus",
         "P" => "speech corpus",
         "M" => "multimodal corpus",
         "K" => "corpus", /* generic */
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
    } else {
       // Fallback: Return key with warning.
       return "LOCALIZE:".$key;
    }

}

/*
 Renders author(s) and date. A missing author will not render a date
*/


function render_author_date($lang, $table, $row) {
  $authors=get_authors($lang,$table,$row);
  $date=render_date($table,$row);
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
function get_authors($lang, $table, $row) {
  $column_fi=27;
  $column_en=28;
  /* Take "fi" if defined, "en" in all other cases. As fallback, if there is no info in the desired language, try the other language. */
  if ($lang == "fi") {
      $authors = get_cell($table,$row,$column_fi);
      if (! $authors) $authors = get_cell($table,$row,$column_en);
  } else {
      $authors = get_cell($table,$row,$column_en);
      if (! $authors) $authors = get_cell($table,$row,$column_fi);
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

function render_date($table, $row) {
  $date=get_date($table, $row);
  if ($date) {
    return " (".$date.")";
  } else {
    return "";
  }
}

function get_date($table, $row) {
  return get_cell($table,$row,29);
}

/*
 Put the title in italics.
 */

function render_title($lang, $table, $row) {
  return "<I>".get_title($lang, $table, $row)."</I>";
}

/*
 Get the title depending on the language
 */

function get_title($lang, $table, $row) {
  if ($lang == "fi") {
    return get_cell($table,$row,15);
  } else {
    return get_cell($table,$row,16);
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

function render_type($lang, $table, $row) {
 return "[".get_type($lang, $table, $row)."]";
}

/*
 Get the type code and translate (eg. T > text corpus).
 */

function get_type($lang, $table, $row) {
 $type = get_cell($table, $row, 22);
 return localize($lang,$type);
}

/*
 Turn the URN into a link.
 */

function render_urn($lang, $table, $row) {
  $urn=get_urn($table,$row);
  $available="";
  if ($urn) {
    $available = "http://urn.fi/".$urn;
  }
  return $available;
}

function get_shortname($table, $row) {
    return get_cell($table,$row,0);
}

function get_urn($table, $row) {
    return get_cell($table,$row,17);
}

/*
 Render the whole reference.
 */

function render_reference($lang,$table,$row) {
    return render_author_date($lang,$table,$row) .
           render_title($lang, $table, $row) . " " .
           render_type($lang,$table, $row) . ". ".get_publisher(). ". " .
           localize($lang,"available_at")." ".render_urn($lang,$table,$row) ;
}

/*
 Render bibtex code to be used with bibtex.
 */

function render_bibtex($lang,$table,$row, $key) {
    $authors="";
    $bibtex="";
    $author_list = get_authors($lang,$table,$row);
    if ($author_list) $authors = str_replace("  "," ",implode(" and ", $author_list));
    $year   = get_date($table,$row);
    $type   = get_type($lang,$table,$row);
    $bibtex .= "@misc{".get_shortname($table, $row)."_".$lang.",\n";
     if ($authors) {
      $authors = str_replace("_"," ",$authors);
      $bibtex .= " author={". ltrim($authors) ."},\n";
     }
     if ($year) $bibtex .= " year={".$year."},\n";
     $bibtex .= " title={{".get_title($lang,$table,$row)."}},\n";
     $bibtex .= " publisher={".get_publisher()."},\n";
     if ($type) $bibtex .= " type={".$type."},\n";
     $bibtex .= " url={".render_urn($lang,$table,$row)."},\n";
    $bibtex .= "}\n";
    return $bibtex;
}

/*
 Render bibtex code to be used with Zotero.
 */


function render_zotero($lang,$table,$row, $key) {
    $authors="";
    $bibtex="";
    $author_list = get_authors($lang,$table,$row);
    if ($author_list) $authors = str_replace("  "," ",implode(" and ", $author_list));
    $year   = get_date($table,$row);
    $type   = get_type($lang,$table,$row);
    $bibtex .= "@techreport{".get_shortname($table, $row)."_".$lang.",\n";
     if ($authors) {
      $authors = str_replace("_"," ",$authors);
      $bibtex .= " author={". ltrim($authors) ."},\n";
     }
     if ($year) $bibtex .= " year={".$year."},\n";
     $bibtex .= " title={{".get_title($lang,$table,$row)."}},\n";
     $bibtex .= " publisher={".get_publisher()."},\n";
     if ($type) $bibtex .= " type={".$type."},\n";
     $bibtex .= " url={".render_urn($lang,$table,$row)."},\n";
    $bibtex .= "}\n";
    return $bibtex;
}


?>

<!doctype html>
<!--[if lt IE 7 ]><html class="ie ie6" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html  style="margin-top: 1em !important" lang="<?php echo $lang; ?>">

<!--<![endif]-->
<?php
get_header();
?>

<body style="margin:1em; margin-top: 1em">
  <script type="text/javascript">
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
    <?php
	/* the loop */

function my_content($content) {
	 global $post, $key, $lang;
         $original = $content;

	 list ($table, $row) = get_row_table($key);
	 $content .= '<div class="onecol">';
         if ($table != -1) {
	     $content .= '<div align="right"><a href="/viittaus/?key='.urlencode($key).'&lang=fi">[suomeksi]</a> <a href="/viittaus/?key='.urlencode($key).'&lang=en">[in English]</a></div>';
	     $content .= '<h2 class="first">'.localize($lang,"ref_heading").": ".get_shortname($table, $row).'</h2>';

	     $content .= localize($lang,"ref_intro");
	     $content .= "<p class='light'>".render_reference($lang,$table,$row)."</p>";

	     $content .= localize($lang,"show").'<a href="#" id="show_bibtex">[Bibtex]</a>';
	     $content .=' <a href="#" id="show_zotero">[Zotero]</a>';
	     $content .='<div id="bibtex" style="display:none">';
	     $content .='<h3>Bibtex</h3>';
	     $content .= localize($lang,"bibtex_intro_text");
	     $content .='<pre class="light">'.render_bibtex($lang,$table,$row,$key).'</pre>';
	     $content .='</div>';
	     $content .='<div id="zotero" style="display:none">';
	     $content .='<h3>Zotero</h3>';
	     $content .= localize($lang,"zotero_intro_text");
	     $content .='<pre class="light">'.render_zotero($lang,$table,$row,$key).'</pre>';
	     $content .='</div>';
	     $content .='<p><a href="https://scholar.google.com/scholar?q='.urlencode(get_urn($table, $row).' OR "'.get_title("fi", $table, $row).'" OR "'.get_title("en", $table, $row).'"').'" target="_parent">'.localize($lang,"search_scholar").'</a></p>';
	 } else {
	     $content .= localize($lang,"ref_heading")." <b>".$key."</b> ".localize($lang,"not_found").".<br>";
	     $content .= localize($lang,"lb_notified");

	     $to = 'matthies@csc.fi'; //for now to test
	     $subject = 'Key error in reference instructions: '.$key;
	     $body = 'The reference instructions were called with an unknown key.<br>Please fix on the page where it came from.<p>';
	     $body .= 'Key: '.$key."<br>";
	     $body .= 'Language: '.$lang."<br>";
	     $body .= 'Referer: '. wp_get_referer();
	     $headers = array('Content-Type: text/html; charset=UTF-8');

             wp_mail( $to, $subject, $body, $headers );
	     http_response_code(404);
	 }
	 $content .='</div>';
	 return $content;
}


add_filter( 'the_content', 'my_content' );

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		the_content();
	} // end while
} // end if
?>
</div>
</body>
</html>
