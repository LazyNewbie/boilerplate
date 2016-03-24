<?php namespace LazyNewbie\Boilerplate\Traits;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait FilesTrait {


  /**
   * Gives unique file name, for uploaded file and target directory
   *
   * @param string|UploadedFile $file
   * @param string $dirPath
   * @param bool $basePath
   * @param string $prefix
   * @return string
   */
  protected function getUniqueFileName($file, $dirPath, $basePath = false, $prefix=''){

    if($file instanceof UploadedFile){
      $imgFullName = $this->clearFileName($file->getClientOriginalName());
    }else{
      $imgFullName = $this->clearFileName($file);
    }

    $img_name = $imgFullName = explode(".", $imgFullName);
    $img_ext = '';

    if(count($imgFullName) > 0){
      $img_ext = array_pop($img_name);
      $img_name = implode('.', $img_name);
    }

    $img_duplicate = $prefix . "$img_name.$img_ext";

    $dirPath = rtrim($dirPath, '/').'/';

    $filecounter = 1;
    while(file_exists($dirPath . $img_duplicate)){
      $img_duplicate = $img_name . '_' . $filecounter++ . '.'. $img_ext;
    }
    return $basePath? $dirPath.$img_duplicate : $img_duplicate;
  }


  /**
   * Clear file name from special symbols
   *
   * @param string $name
   * @return string
  */
  protected function clearFileName($name){
    $name = trim(rawurldecode($name));
    $arr = explode('.', $name);
    $mime = array_pop($arr);
    $name = implode('', $arr);
    $name = preg_replace("/[^a-zA-Z0-9]+/", "-", $name);
    if(empty($name)) $name = time();
    $name = $name.'.'.$mime;
    return $name;
  }


  /**
   * Save uploaded file without validation
   *
   * @param File $file
   * @param string $newLocation
   * @param string $newName
   * @throws FileException
   * @return File
  */
  protected function saveFile(File $file, $newLocation, $newName = ""){
    $name = $file->getClientOriginalName();
    if($newName) $name = $newName;

    return $file->move(
      $this->createFolderIfNotExist($newLocation),
      $this->getUniqueFileName($name, $newLocation)
    );
  }


  /**
   * Save uploaded file with validation
   *
   * @param string|\Symfony\Component\HttpFoundation\File\UploadedFile $name
   * @param string $saveDir
   * @param null|array $validatorRules
   * @param string $namePrefix
   * @return object
   */
  protected function fileLoader($name, $saveDir, $validatorRules = null, $namePrefix = '', $newName = ''){
    $imgName = $dirPath = $errorMsg ='';

    if($name instanceof \Symfony\Component\HttpFoundation\File\UploadedFile){
      $file = $name;
      $name = 'image';
    }else{
      $file = \Input::file($name);
    }

    if(!$validatorRules){
      $validatorRules = [$name => 'mimes:jpeg,jpg,gif,png,bmp'];
    }

    $validator = \Validator::make([$name => $file], $validatorRules);
    $validator->fails();

    if($file){
      $fileOriginal = $file->getClientOriginalName();

      if(!$validator->failed()){

        $saveDir .='/';
        if($newName){
          $imgName = $newName.'.'.$file->getClientOriginalExtension();
        }else{
          $imgName = $this->getUniqueFileName($file,$saveDir, $namePrefix);
        }

        $file->move($saveDir, $imgName);
      }else{
        $errorMsg = $validator->messages()->all();
      }
    }else{
      $fileOriginal = '';
    }


    return (object)[
      'originalName'=> $fileOriginal,
      'imgName'     => $imgName,
      'pathName'    => $dirPath . $imgName,
      'hasFile'     => !is_null($file),
      'error'       => $validator->failed(),
      'errorMsg'    => $errorMsg
    ];
  }


  /**
   * Delete file
   *
   * @param string $file
   * @return bool
  */
  protected function deleteFile($file){
    if(\File::exists($file) && !\File::isDirectory($file)){
      return unlink($file);
    }
    return false;
  }


  /**
   * Create folder if not exist
   *
   * @param string $path
   * @param int $chmod
   * @return string
  */
  protected function createFolderIfNotExist($path, $chmod = 0777){
    if( !\File::exists($path) || !\File::isDirectory($path)){
      \File::makeDirectory($path, $chmod, true);
    }
    return $path;
  }


}