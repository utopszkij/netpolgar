<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use HasFactory;
    
		  /**
		   * file upload kezelése (ha már létezik felülirja)
		   * @param string $inputName input form control name
		   * @param string $targetDir '/' -el a végén
		   * @param string $targetName kiterjesztés nélkül
		   * @param array $enabledExtensions
		   * @return string 'no upload'|targetFile|'ERROR .....'
		   */ 
		  public static function processUpload(string $inputName, 
		    string $targetDir, 
		    string $targetName,
		    array $enabledExtensions = ['jpg','png']): string {
			if (!is_dir($targetDir)) {	
				mkdir($targetDir,0777,true);
			}		
			if (!isset($_FILES[$inputName])) {
			    return 'no upload';
			}
		    if (file_exists($_FILES[$inputName]['tmp_name']) & 
		        is_uploaded_file($_FILES[$inputName]['tmp_name'])) {
				$targetFile = $targetDir . basename($_FILES[$inputName]["name"]);
				$imageFileType = strtolower(pathinfo($targetFile,PATHINFO_EXTENSION));
				$targetFile = $targetDir.$targetName.'.'.$imageFileType;
				
				$uploadOk = true;
				$uploadMsg = '';
				/*
				$check = getimagesize($_FILES[$inputName]["tmp_name"]);
				if($check !== false) {
					$uploadOk = true;
				} else {
					$uploadMsg = "ERROR ".__('FileNotImage');
					$uploadOk = false;
				}
				*/
				if ($uploadOk) {	    
					if ($_FILES[$inputName]["size"] > 200000) {
						$uploadMsg =  "ERROR ".__('FileToLarge').' (max.2M)';
						$uploadOk = false;
					}
				}
				if ($uploadOk) {	
					if (!in_array($imageFileType, $enabledExtensions)) {
					  $uploadMsg = "ERROR ".__('FileExtensionDisabled');
					  $uploadOk = false;
					}			
				}
				if (file_exists($targetFile)) {
					unlink($targetFile);
				}
				if (move_uploaded_file($_FILES[$inputName]["tmp_name"], $targetFile)) {
					// sikeres upload
					$uploadMsg = '';		
				} else {
					$uploadMsg = "ERROR ".__('FileUploadError');
				}			
			} else {
				$uploadMsg = 'no upload';
			}
			if ($uploadMsg == '') {
				$result = $targetFile;
			} else {
				$result = $uploadMsg;
			}
			return $result;	
		}		
    
}
