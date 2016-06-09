<?php

namespace app\models;
use Yii;

class vHost {
	public $name = "";
	public $value = "";
	public $params = array();

	function __construct($lines) {
		//preg_match("/^<(?P<key>\w+)\s+(?P<value>.*)>$/", htmlentities(trim($l)), $matches)	- открывающий тег хоста
		//preg_match("/^<\/$this->name>$/", htmlentities(trim($l)), $matches)					- закрывающий тег хоста
		//preg_match("/^(?P<key>\w+)\s+(?P<value>.*)/", htmlentities(trim($l)), $matches)		- получение параметра
    	$config = array();
    	$subtag_opened = false;
    	$current_key = "";

    	if(preg_match("/^<(?P<key>\w+)\s+(?P<value>.*)>$/", trim($lines[0]), $matches)) {
    		$this->name = $matches["key"];
    		$this->value = $matches["value"];

			if(preg_match("/^<\/$this->name>$/", trim(end($lines)), $matches)) {
    			for($i = 1; $i<count($lines)-1; $i++) {
    				if($subtag_opened) {
    					if(preg_match("/^<\/$current_key>$/", trim($lines[$i]), $matches)) {
    						$subtag_opened = false;
    					}
    					elseif(preg_match("/^(?P<key>\w+)\s+(?P<value>.*)/", trim($lines[$i]), $matches)) {
    						if (isset($matches['key'])) {
    							$config[$current_key]["params"][$matches['key']] = $matches['value'];
    						}
    					}
    				}
    				else {
						if(preg_match("/^(?P<key>\w+)\s+(?P<value>.*)/", trim($lines[$i]), $matches)) {
							if (isset($matches['key'])) {
								$config[$matches['key']] = $matches['value'];
							}
						}
						elseif(preg_match("/^<(?P<key>\w+)\s+(?P<value>.*)>$/", trim($lines[$i]), $matches)) {
							$subtag_opened = true;
							$current_key = $matches['key'];

							$config[$current_key] = array(
									"value" => $matches['value'],
									"params" => array()
								);
							
						}
    				}
				}
				$this->params = $config;
			}
			else throw new \Exception('Ошибка синтаксиса: не хватает закрывающего тега');
		}
		else throw new \Exception('Ошибка синтаксиса: не хватает открывающего тега');
	}
    public static function getParams($lines) {
		/*$config = array();

    	foreach ($lines as $key => $l) {
    		if(preg_match("/^<(?P<key>\w+)\s+(?P<value>.*)>$/", htmlentities(trim($l)), $matches)) {
    			$this->name = $matches["key"];
    			$this->value = $matches["value"];
    		}
    		elseif(preg_match("/^<\/$this->name>$/", htmlentities(trim($l)), $matches);) {
    			return $this;
    		}
    		else{
    			preg_match("/^(?P<key>\w+)\s+(?P<value>.*)/", htmlentities(trim($l)), $matches);
			    if (isset($matches['key'])) {
			        $config[$matches['key']] = $matches['value'];
			    }
    		}    
		}*/

		
		/*$config["test"] = preg_match("/^<(?P<key>\w+)\s+(?P<value>.*)>$/", "<VirtualHost *:8080>", $matches);
		$qs = $matches['key'];
		$config["close_tag"] = */

		//return $config;
    }
}
class HostSystemOS
{
	public static function getParams($lines) {
		/*$config = array();
		$name = "";

    	foreach ($lines as $key => $l) {
    		if(preg_match("/^<(?P<key>\w+)\s+(?P<value>.*)>$/", htmlentities(trim($l)), $matches)) {
    			$config["Name"] = $matches["key"];
    			$config["Value"] = $matches["value"];
    			$name = $config["Name"];
    		}
    		elseif(preg_match("/^<\/$name>$/", htmlentities(trim($l)), $matches);) {
    			return $config;
    		}
    		else{
    			preg_match("/^(?P<key>\w+)\s+(?P<value>.*)/", htmlentities(trim($l)), $matches);
			    if (isset($matches['key'])) {
			        $config["Params"][$matches['key']] = $matches['value'];
			    }
    		}    
		}*/

		
		/*$config["test"] = preg_match("/^<(?P<key>\w+)\s+(?P<value>.*)>$/", "<VirtualHost *:8080>", $matches);
		$qs = $matches['key'];
		$config["close_tag"] = */

		//return $config;
    }
	public static function getUsers() {
		$sys_exec = array();
		
		exec("../SystemScripts/getUsers.sh", $sys_exec);
		
		return $sys_exec;
	}
	public static function getCurrentUser() {
		$sys_exec = array();
		
		exec("../SystemScripts/getCurrentUser.sh", $sys_exec);
		
		
		return $sys_exec[0];
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
	public static function reloadApache() {
		$sys_exec = array();
		//$passfile = "pass";

		//file_put_contents($passfile, Yii::$app->params['adminPasswd']);
		
		exec("sudo -g hostsystem -u root '../SystemScripts/reloadApache.sh'", $sys_exec);
		//sudo -u root -g hostsystem -S service apache2 restart < $passfile
		//unlink($passfile);
		
		return $sys_exec;
	}
	public static function createFolder($folder_name, $mode = 0755) {
		$status = mkdir($folder_name, $mode);
		
		return $status;
	}
	public static function createSymLink($target, $link) {
		$status = symlink ($target , $link);
		
		return $status;
	}
	public static function removeFolder($dir) {
		if (is_dir($dir)) { 
			$objects = scandir($dir); 
			foreach ($objects as $object) { 
				if ($object != "." && $object != "..") { 
					if (is_dir($dir."/".$object))
						HostSystemOS::removeFolder($dir."/".$object);
					else
						unlink($dir."/".$object); 
				} 
			}
			rmdir($dir); 
		}
		else return false;
		return true;
	}
	public static function copyFolder($folder_src, $folder_dst) {
		$sys_exec = array();

		$command = "../SystemScripts/copyFolder.sh $folder_src $folder_dst";
		
		exec($command, $sys_exec, $status);
		
		return is_dir($folder_dst);
	}
	public static function copyFile($file_src, $file_dst) {
		$status = copy ($folder_src, $folder_dst);
		
		return $status;
	}
	public static function getFilesList($folder = "", $short = false) {
		$sys_exec = array();
		$filesArray = array();

		if(empty($folder)) {
			$folder = HostSystemOS::getCurrentFolder()."/";
		}

		if($short) {
			return scandir($folder);
		}
		
		exec("../SystemScripts/getFolderList.sh ".$folder, $sys_exec);

		if(!empty($sys_exec)) {
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
			
			return $sys_exec;
		}
		else throw new \Exception('Невозможно отобразить содержимое каталога.');
	}
	public static function addVHost($hostfile,$servername,$serveradmim,$port,$docroot,$user_id,$user_group,$host_ip = "*") {
		
		$vhost = 
"<VirtualHost $host_ip:$port>
	ServerName $servername
	ServerAdmin $serveradmim
	DocumentRoot $docroot

	ErrorLog \${APACHE_LOG_DIR}/error.log
	CustomLog \${APACHE_LOG_DIR}/access.log combined

	<Directory \"$docroot\">
		AllowOverride All
		Require all granted
	</Directory>

	<IfModule mod_ruid2.c>
		RUidGid $user_id $user_group
	</IfModule>
</VirtualHost>";


		$fp = fopen($hostfile, 'aw');
		$test = fwrite($fp, $vhost);
		fclose($fp);

		return $test;
	}
	public static function getVHosts($filename) {
		$fp = fopen($filename, 'r');//'/etc/apache2/sites-available/hostsystem.conf'
		$contents = fread($fp, filesize($filename));
		fclose($fp);
		$result = preg_split("/(?<=VirtualHost>)/s", $contents);
		$vhosts = array();

		foreach($result as $vhost) {
			if(preg_match_all("/(?<=<VirtualHost)(.*)(?=<\/VirtualHost>)/s", $vhost, $matches)) {
				$vhosts[] = new vHost(explode("\n",trim($vhost)));
			}
		}

		return $vhosts;
	}
	public static function deleteUser($username, $port, $delete_homedir = false) {
		/*$sys_exec = array();

		exec("sudo -g hostsystem -u root '../SystemScripts/createUser.sh' $username $password $port", $sys_exec);
		
		return $sys_exec;*/
	}
	public static function createUser($username, $password, $port) {
		$sys_exec = array();

		exec("sudo -g hostsystem -u root '../SystemScripts/createUser.sh' $username $password $port", $sys_exec);

		return $sys_exec;
	}
}