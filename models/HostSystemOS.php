<?php

namespace app\models;

class HostSystemOS extends \yii\db\ActiveRecord
{
	public static function getUsers() {
		$sys_exec = array();
		
		exec("../SystemScripts/getUsers.sh", $sys_exec);
		
		return $sys_exec;
	}
	public static function getCurrentUser() {
		$sys_exec = array();
		
		exec("../SystemScripts/getCurrentUser.sh", $sys_exec);
		
		
		return $sys_exec;
	}
	public static function getCurrentFolder() {
		$sys_exec = array();
		
		exec("../SystemScripts/getCurrentFolder.sh", $sys_exec);
		
		return $sys_exec[0];
	}
	public static function getCheckSum($file) {
		$sys_exec = array();
		
		exec("../SystemScripts/getCheckSum.sh ".$file, $sys_exec);
		
		$check_sum = preg_split("/[\s]+/", $sys_exec[0])[0];
		return $check_sum;
	}
	public static function createFolder($folder_name, $mode = 0755) {
		$status = mkdir($folder_name, $mode);
		
		return $status;
	}
	public static function removeFolder($folder_name) {
		if (is_dir($folder_name)) {
			$status = rmdir($folder_name);
		}
		else {
			$status = false;
		}

		return $status;
	}
	public static function copyFolder($folder_src, $folder_dst) {
		/*$sys_exec = array();

		$command = "../SystemScripts/copyFolder.sh $folder_src $folder_dst";
		
		exec($command, $sys_exec, $error);
		
		//return $sys_exec;
		return [is_dir($folder_dst), $sys_exec, $error];*/
		$status = copy ($folder_src, $folder_dst);
		
		return $status;
	}
	public static function getFolderList($folder = "") {
		$sys_exec = array();
		$filesArray = array();
		
		exec("../SystemScripts/getFolderList.sh ".$folder, $sys_exec);
		
		if(empty($folder)) {
			$folder = HostSystemOS::getCurrentFolder()."/";
		}
		
		foreach($sys_exec as $file) {
			$fArr = array();
			
			$attrArr = preg_split("/[\s]+/", $file);
			
			if($attrArr[0][0] == "d") {
				$fArr["FILE_TYPE"] = 'folder';
			}
			elseif ($attrArr[0][0] == "l") {
				$fArr["FILE_TYPE"] = 'link';
			}
			else {
				$fArr["FILE_TYPE"] = 'file';
			}
			
			$fArr["FILE_NAME"] = $attrArr[6];
			$fArr["ACCESS_MARK"] = $attrArr[0];
			$fArr["OWNER_NAME"] = $attrArr[2];
			$fArr["FILE_SIZE"] = $attrArr[3];
			$fArr["FILE_DATE"] = $attrArr[4];
			$fArr["FILE_TIME"] = $attrArr[5];
			$fArr["FILE_FULL_PATH"] = $folder.$attrArr[6];
			if($fArr["FILE_TYPE"] == 'file') {
				$fArr["FILE_CHECK_SUM"] = HostSystemOS::getCheckSum($fArr["FILE_FULL_PATH"]);
			}
						
			$filesArray[] = $fArr;
		}
		
		return $filesArray;
	}
}
