<?php
namespace app\components;

use yii\base\Widget;
const WRONG_DIRECTORY = "<span style='color:red'>Некорректная директория</span>";
const PERMISSION_DENIED = "<span style='color:red'>Невозможно отобразить содержимое каталога</span>";
/**
* 
*/
class FolderViewWidget extends Widget
{
	public $folder;

	public function init() {
		parent::init();
		if($this->folder === null) {
			$this->folder = "";
		}
	}
	public function run() {
		return $this->render('folderview',[
			"folder" => $this->showTree($this->folder,"")
		]);
	}
	private function getTree($folder) {
		if(!$folder) return WRONG_DIRECTORY;
		try {
			$files = scandir($folder);
		}
		catch(\Exception $e) {
			return PERMISSION_DENIED;
		}
		$fileArray = array();

		foreach($files as $file) {
			if (($file == '.') || ($file == '..')) continue;

			$f0 = $folder.'/'.$file;

			if (is_dir($f0)) {
				$dir["name"] = $file;				
				$dir["files"] = $this->getTree($f0);

				$fileArray[] = $dir;
			}
			else $fileArray[] = $file;
		}
		return $fileArray;
	}
	private function showTree($folder) {
		if(!$folder) return WRONG_DIRECTORY;
		try {
			$files = scandir($folder);
		}
		catch(\Exception $e) {
			return PERMISSION_DENIED;
		}
					
		$fileString = '<a href="#" class="dir">'.end(explode("/",$folder)).'</a><ul>';				
		foreach($files as $file) {
			if (($file == '.') || ($file == '..')) continue;
			$fileString .= '<li>';
			
			$f0 = $folder.'/'.$file;

			if (is_dir($f0)) {
				$fileString .= $this->showTree($f0);
			}
			else $fileString .= '<a href="#" data-file="'.$f0.'" class="file">'.$file.'</a>';
			$fileString .= '</li>';
		}
		$fileString .= '</ul>';
		
		return $fileString;
	}
}
