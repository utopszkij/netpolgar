<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use HasFactory;
    
    protected $maxSize = 2000000;
    
    public function setMaxSize(int $value) {
        $this->maxSize = $value;
        $w = (int)(str_replace('M', '', ini_get('post_max_size')) * 1024 * 1024);
        if ($w < $value) {
            echo 'Fatal error server post_max_size = '.ini_get('post_max_size'); exit();
        }
        return $this;
    }
    
    /**
     * file upload kezelése (ha már létezik felülirja)
     * @param string $inputName input form control name
     * @param string $targetDir '/' -el a végén
     * @param string $targetName kiterjesztés nélkül
     * @param array $enabledExtensions
     * @param array $disabledExtensions
     * @return string 'no upload'|targetFile|'ERROR .....'
     */
    public function process(string $inputName,
        string $targetDir,
        string $targetName,
        array $enabledExtensions = ['jpg','png'],
        array $disabledExtensions =['php','htm','js']): string {
            if (!is_dir($targetDir)) {
                mkdir($targetDir,0777,true);
            }
            if (!isset($_FILES[$inputName])) {
                return 'no upload';
            }
            if ($_FILES[$inputName]['name'] == '') {
                return 'no upload';
            }
            $targetFile = $targetDir . basename($_FILES[$inputName]["name"]);
            $imageFileType = strtolower(pathinfo($targetFile,PATHINFO_EXTENSION));
            $targetFile = $targetDir.$targetName.'.'.$imageFileType;
            $_FILES[$inputName]['name'] = $targetName.'.'.$imageFileType;
            
            $uploadOk = true;
            $uploadMsg = '';
            if ($uploadOk) {
                if ($_FILES[$inputName]["size"] > $this->maxSize) {
                    $uploadMsg =  "ERROR ".__('FileToLarge').' (max.'.$this->maxSize.')';
                    $uploadOk = false;
                }
            }
            $ext = strtolower(substr($imageFileType,0,3));
            if (($uploadOk) & (count($enabledExtensions) > 0)) {
                if (!in_array($ext, $enabledExtensions)) {
                    $uploadMsg = "ERROR ".__('FileExtensionDisabled');
                    $uploadOk = false;
                }
            }
            if (($uploadOk) & (count($disabledExtensions) > 0)) {
                if (in_array($ext, $disabledExtensions)) {
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
            if ($uploadMsg == '') {
                $result = $targetFile;
            } else {
                $result = $uploadMsg;
            }
            return $result;
    }
    
    /**
     * file upload kezelése (ha már létezik felülirja)
     * @param string $inputName input form control name
     * @param string $targetDir '/' -el a végén
     * @param string $targetName kiterjesztés nélkül
     * @param array $enabledExtensions
     * @param array $disabledExtensions
     * @return string 'no upload'|targetFile|'ERROR .....'
     */
    public static function processUpload(string $inputName,
        string $targetDir,
        string $targetName,
        array $enabledExtensions = ['jpg','png'],
        array $disabledExtensions =['php','htm','js']): string {
        
        $model = new Upload();
        return $model->process($inputName, $targetDir, $targetName, 
                $enabledExtensions, $disabledExtensions);
    }
    
    
}
