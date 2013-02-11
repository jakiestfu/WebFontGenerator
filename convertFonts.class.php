<?PHP 

class convertFonts {
	
	private $fontName;
	private $fileName;
	private $folder;
	private $theTime;
	
	private function generateFonts(){
		ini_set('max_execution_time', 5000);
		
		mkdir($this->folder.'/fonts', 0777, true);
		
		$handle = popen( './fontforge -script convert.sh '.$this->folder.' '.$this->fileName, "r" );
		$this->fontName = trim(fread($handle, 2096));
		pclose($handle);
	}
	private function generateDemo(){
		
		$fn = explode('.',$this->fileName);
		$fn = $fn[0];
		chmod($this->folder, 0777);
		
		$css = "@font-face {\n	font-family: '".$this->fontName."';\n	src: url('fonts/".$fn.".eot'); /* IE9 Compat Modes */\n	src: url('fonts/".$fn.".eot?#iefix') format('embedded-opentype'), /* IE6-IE8 */\n	     url('fonts/".$fn.".woff') format('woff'), /* Modern Browsers */\n	     url('fonts/".$fn.".ttf')  format('truetype'), /* Safari, Android, iOS */\n	     url('fonts/".$fn.".svg#".$this->fontName."') format('svg'); /* Legacy iOS */\n}";
		$cssFile = fopen($this->folder.'/font.css', "a"); 
		fputs($cssFile, $css); 
		fclose($cssFile);
		
		$html = "<!doctype html>\n<html lang=\"en\">\n<head>\n	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n	<title>@font-face Demo</title>\n	<link rel=\"stylesheet\" href=\"font.css\" type=\"text/css\" charset=\"utf-\" />\n	<style type=\"text/css\">\n	body {\n		background-color:white;\n		font-family:Verdana,Tahoma,Arial,Sans-Serif;\n		color:black;\n		font-size:12px;\n	}\n\n	.demo\n	{\n		font-family:'".$this->fontName."',Sans-Serif;\n		width:800px;\n		margin:10px auto;\n		text-align:left;\n		border:1px solid #666;\n		padding:10px;\n	}\n	</style>\n</head>\n<body>\n	<div class=\"demo\" style=\"font-size:25px\">\n		The quick brown fox jumps over the lazy dog.\n	</div>\n</body>\n</html>";
		$demoFile = fopen($this->folder.'/demo.html', "a"); 
		fputs($demoFile, $html); 
		fclose($demoFile);
		
	}
	private function createArchive(){
		
		$archiveName = $this->folder.'/'.$this->fontName.'.zip';
		
		$zip = new ZipArchive();

		if ($zip->open($archiveName, ZIPARCHIVE::CREATE) !== TRUE) {
		    die("Could not open archive");
		}
		
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->folder));
		
		$blacklist = array(
			'fonts/.','fonts/..','.','..'
		);
		
		foreach ($iterator as $key=>$value) {
			$rv = str_ireplace($this->folder.'/', '', $value);
			if(!in_array($rv, $blacklist)){
				$zip->addFile(realpath($key), $this->fontName.'-'.$this->theTime.'/'.$rv) or die ("ERROR: Could not add file: $key");
			}
		}
		
		$zip->close();
	}
	private function download(){
		
		$zipname = $this->fontName.'.zip';
		$zipPath = $this->folder.'/'.$zipname;
		
		header('Content-Type: application/zip');
		header('Content-disposition: attachment; filename='.$zipname);
		header('Content-Length: ' . filesize($zipPath));
		readfile($zipPath);
	}
	private function cleanUp($dir=false){
		$dir = $dir ? $dir : $this->folder;
		$files = array_diff(scandir($dir), array('.','..')); 
		foreach ($files as $file) { 
			(is_dir("$dir/$file")) ? $this->cleanUp("$dir/$file") : unlink("$dir/$file"); 
		} 
		return rmdir($dir); 
	}
	
	public function convert($name){
		$this->fileName = $name;
		$this->theTime = time();
		$this->folder = getcwd().'/temp/'.sha1( md5( $this->theTime ) );
		
		$this->generateFonts();
		$this->generateDemo();
		
		$this->createArchive();
		$this->download();
		$this->cleanUp();
	}
};