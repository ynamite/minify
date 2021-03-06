<?php
	class minify {
		private $files = [];
		
		public function __construct() {
			
		}
		
		public function addFile($file, $set = 'default') {
			$this->files[$set][] = $file;
		}
		
		public static function isSCSS($file) {
			$pos = strrpos($file, ".");
			if ($pos !== false) {
				return substr($file, $pos+1) == 'scss';
			}
			
			return false;
		}
		
		public static function compileFile($file, $type = 'scss') {
			$compiledFilename = rex_path::addonAssets(__CLASS__, 'cache'.'/compiled.scss.'.str_replace('.scss', '.css', basename($file)));
			
			$compiler = new rex_scss_compiler();
			$compiler->setScssFile(self::absolutePath($file));
			$compiler->setCssFile($compiledFilename);
			$compiler->compile();
			
			return $compiledFilename;
		}
		
		public static function absolutePath($file) {
			return rex_path::base($file);
		}
		
		public static function relativePath($file) {
			return substr($file,strlen(rex_path::base(''))-1);
		}
		
		public function minify($type, $set = 'default', $output = 'file') {
			if (!in_array($type, ['css','js'])) {
				return false;
			}
			
			$minify = false;
			$oldCache = [];
			$newCache = [];
			
			if (file_exists(rex_path::addonAssets(__CLASS__, 'cache'.'/'.$type.'_'.$set.'.json'))) {
				$string = file_get_contents(rex_path::addonAssets(__CLASS__, 'cache'.'/'.$type.'_'.$set.'.json'));
				$oldCache = json_decode($string, true);
			}
			
			if (!empty($this->files[$set])) {
				foreach ($this->files[$set] as $file) {
					//Start - get timestamp of the file
						$newCache[$file] = filemtime(trim(rex_path::base(substr($file,1))));
					//End - get timestamp of the file
					
					if (empty($oldCache[$file])) {
						$minify = true;
					} else {
						if ($newCache[$file] > $oldCache[$file]) {
							$minify = true;
						}
					}
				}
				
				//Start - save path into cachefile
					if (!$minify) {
						$path = $oldCache['path'];
					}
				//Ebd - save path into cachefile
				
				if ($minify) {
					$path = rex_path::addonAssets(__CLASS__, 'cache'.'/'.md5($set.'_'.implode(',',$newCache).'_'.time()).'.'.$type);
					$newCache['path'] = $path;
					
					switch($type) {
						case 'css':
							$minifier = new MatthiasMullie\Minify\CSS();
						break;
						case 'js':
							$minifier = new MatthiasMullie\Minify\JS();
						break;
					}
					
					if (!rex_file::put(rex_path::addonAssets(__CLASS__, 'cache'.'/'.$type.'_'.$set.'.json'), json_encode($newCache))) {
						echo 'Cachefile für '.$type.' konnte nicht geschrieben werden!';
					}
					
					foreach ($this->files[$set] as $file) {
						$file = trim($file);
						
						if (self::isSCSS($file)) {
							$compiledFilename = self::compileFile($file, 'scss');
							$minifier->add($compiledFilename);
						} else {
							$minifier->add(rex_path::base(substr($file,1)));
						}
					}
					
					$minifier->minify($path);
				}
				
				switch ($output) {
					case 'file':
						return self::relativePath($path);
					break;
					case 'inline':
						return rex_file::get($path);
					break;
				}
			}
			
			return false;
		}
	}
?>