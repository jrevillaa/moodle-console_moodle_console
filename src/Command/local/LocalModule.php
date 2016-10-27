<?php
namespace Atypax\Command\local;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class LocalModule
{

	function __construct(){
		
	}

	function templateModule($route,$name,$type){
    $fs = new Filesystem();

    $target = $route . '/' . $type . '/' . $name;
    //if(file_exists($target)){
    if($fs->exists($target)){
      return "Ya existe el módulo";
    }
    try{

      shell_exec("cp -r " . dirname(dirname(__DIR__)) . "/assets/". $type ." $target");
      $fs->chmod($target,0755,0000,true);


    }catch(IOExceptionInterface $e){
      echo "An error occurred while creating your directory at ".$e->getPath();
      //dump($e);
    }

    if(!$this->create_db($target,$name,$type)){
      return 'Error creando db';
    }

    if(!$this->create_lang($target,$name,$type)){
      return 'Error creando lang';
    }

    if(!$this->create_version_settings($target,$name,$type)){
      return 'Error creando version y settings';
    }

    if(!$this->create_index($target,$name,$type,$route)){
      return 'Error creando el index';
    }


    return "Módulo " . $name . " creado";
  }

  function create_index($target,$name,$type,$route){
    if($type == 'blocks'){
      $newtype = 'block';
    }else{
      $newtype = $type;
    }

    $data = file_get_contents($target. '/index.php');
    $data = str_replace('(?)',$name,$data);
    if (file_put_contents($target. '/index.php', $data) &&
        file_put_contents($target. '/index.php', str_replace('(??)',$route,$data))) {
      return true;
    }
    else {
      return false;
    }

  }

  function create_version_settings($target,$name,$type){
    if($type == 'blocks'){
      $newtype = 'block';
    }else{
      $newtype = $type;
    }

    $data = file_get_contents($target. '/version.php');

    if (!file_put_contents($target.'/version.php', str_replace('(?)',$name,$data))) {
      return false;
    }

    $data = file_get_contents($target. '/settings.php');

    if (!file_put_contents($target.'/settings.php', str_replace('(?)',$name,$data))) {
      return false;
    }

    return true;
  }

  function create_lang($target,$name,$type){
    
    if($type == 'blocks'){
      $newtype = 'block';
    }else{
      $newtype = $type;
    }
    $data = file_get_contents($target. '/lang/en/' . $newtype . '_holamundo.php');

    if (file_put_contents($target. '/lang/en/' . $newtype . '_holamundo.php', str_replace('(?)',$name,$data)) && 
        rename($target. '/lang/en/' . $newtype . '_holamundo.php',$target. '/lang/en/' . $newtype . '_' . $name . '.php')) {
      return true;
    }
    else {
      return false;
    }
  }

  function create_db($target,$name,$type){
    $data = file_get_contents($target.'/db/install.xml');

    if (file_put_contents($target.'/db/install.xml', str_replace('(?)',$name,$data))) {
      return true;
    }
    else {
      return false;
    }


  }

}