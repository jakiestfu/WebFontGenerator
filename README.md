WebFontGenerator
================

This is a class to generate CSS3 web fonts with Font Forge and PHP

## Requirements
* Command Line Access
* <a href="http://fontforge.org/" target="_blank">FontForge</a> (and dependencies)
* PHP

## Installation
Source: <a href="http://openfontlibrary.org/en/guidebook/how_to_install_fontforge">Open Font Library</a>

## Usage

```php
<?PHP

require('convertFonts.class.php');

$converter = new convertFonts();
$converter->convert( 'fuck_yeah_fonts.otf' );
```

The convert function will ultimately output a zip file which has the following structure:

<img src="http://i.imgur.com/y1x3Nmy.png">

## Current Limitations

* Doesn't fully work. HAHA....
