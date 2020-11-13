# svgFontReader

## About
with svgFontReader you can easy read out the glyphs of a svgFont. Especially for icon-fonts this showed up to be very useful

## Usage
use of the class as followed

	include 'svgFontReader.class.php';
    $svgFontReader = new SVG_Font_Reader; // initiate the class
    $svg = dirname(__FILE__) . '/font-icons.svg'; // define the svg font-file
    $glyphsAll = $svgFontReader->get_glyphs($svg); // get glyphs as array
    $glyphsList = $svgFontReader->list_glyphs($svg); //get glyphs as comma separated list

some rules you have to follow:

 - charset must be unicode/utf8
 - you have to include the font in css
 - for the relevant area you have to define the font-family

## Timeline
 * 23/07/2015: testrun with bootstraps glyphicons done
 * 23/07/2015: some glyphs which actually arent glyphs in the font but other attributes are now sorted out 