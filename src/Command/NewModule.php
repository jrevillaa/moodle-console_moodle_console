<?php
namespace Atypax\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
/**
 * Creacion de archivo via consola.
 */
class NewModule extends Command
{
  public function configure()
  {
    $this
    ->setName("new")
    ->setDescription("Comando para la creacion de nuevo módulo para Moodle")
    ->addArgument(
      'type',
      InputArgument::REQUIRED,
      "Se necesita saber el tipo de módulo que se va a crear"
      )
    ->addArgument(
      'name',
      InputArgument::REQUIRED,
      "Se necesita saber el nombre del módulo que se va a crear"
      )
    ;
  }

  public function execute(InputInterface $input, OutputInterface $output)
  {

    $namePlugins = $input->getArgument("name");
    $typePlugins = $input->getArgument("type");


    $route = explode('/', getcwd());
    unset($route[0]);
    //dump($route);
    $count = count($route);
    $validate = false;
    $validateRoute = '';
    while($count > 0){

      $newroute = '';
      foreach ($route as $key => $value) {
        if($key <= $count){
          $newroute .= '/' .$value;
        }
      }
      if(file_exists($newroute . '/config.php')){
        $validate = true;
        $validateRoute = $newroute;
      }
      //$output->writeln($newroute);
      $count--;

    }

    if($validate){
      //$output->writeln($validateRoute);
      $output->writeln($this->templateModule($validateRoute,$namePlugins,$typePlugins));
      //$output->writeln("Se acaba de crear el plugin del tipo " . $typePlugins . " con el nombre " . $namePlugins);
    }else{
      $output->writeln("Debes estar dentro del directorio de un moodle");
    }
    //$output->writeln(getcwd());
  }



  function templateModule($route,$name,$type){

    if($type != 'blocks' && $type != 'local' && $type != 'mod'){
      return 'Sólo se puede crear módulos blocks, local, mod en esta version';
    }

    $target = $route . '/' . $type . '/' . $name;
    if(file_exists($target)){
      return "Ya existe el módulo";  
    }

    mkdir($target, 0755, true);

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
    $data_dummy = '
<?php

require_once("' . $route . '/config.php");

global $DB, $CFG, $PAGE, $OUTPUT;
require_once($CFG->libdir."/adminlib.php");

require_login();
require_capability("moodle/site:config", context_system::instance());
admin_externalpage_setup("generateDummy", "", null);
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_heading($SITE->fullname);
$main_url = new moodle_url("/' . $type . '/' . $name . '/index.php");
$PAGE->set_url($main_url);
$title = get_string("titlesite","' . $newtype . '_' . $name . '");
$PAGE->set_title($title);
$PAGE->set_heading($title);
print $OUTPUT->header();

echo html_writer::tag("h2","Creando Cursos..");


print $OUTPUT->footer();';
  
  if (!file_put_contents($target.'/index.php', $data_dummy, FILE_APPEND | LOCK_EX)) {
      return false;
    }
      
    return true;


  }

  function create_version_settings($target,$name,$type){
    if($type == 'blocks'){
      $newtype = 'block';
    }else{
      $newtype = $type;
    }
    $data_version= '<?php

defined("MOODLE_INTERNAL") || die;

$plugin->version   = 2015041700;
$plugin->release   = "1.0";
$plugin->requires  = 2013082100; 
$plugin->component = "' . $newtype . '_' . $name . '";';

    $data_settings= '<?php 

$settings = null;

defined("MOODLE_INTERNAL") || die;

if ($hassiteconfig) {
  //Add link in modules
  $ADMIN->add("modules", new admin_category("categorydummy", get_string("exampletitle","' . $newtype . '_' . $name . '")));
  
  //generate link to Admin
  $ADMIN->add("categorydummy", new admin_externalpage("generateDummy", 
                get_string("menutitleexample", "' . $newtype . '_' . $name . '"), 
                new moodle_url("' . $type . '/' . $name . '/index.php")));

}';

    if (!file_put_contents($target.'/version.php', $data_version, FILE_APPEND | LOCK_EX)) {
      return false;
    }

    if (!file_put_contents($target.'/settings.php', $data_settings, FILE_APPEND | LOCK_EX)) {
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
    $data_dummy = '<?php
    defined("MOODLE_INTERNAL") || die();
   $string["pluginmane"] = "' . $name . '";
   $string["exampletitle"] = "Link Example";
   $string["menutitleexample"] = "Page Example";
   $string["modulenameplural"] = "' . $name . 's";';

    mkdir($target .'/lang', 0755, true);
    mkdir($target .'/lang/en', 0755, true);

    if (file_put_contents($target.'/lang/en/' . $newtype . '_' . $name . '.php', $data_dummy, FILE_APPEND | LOCK_EX)) {
      return true;
    }
    else {
      return false;
    }

  }

  function create_db($target,$name,$type){
    if($type == 'blocks'){
      $newtype = 'block';
    }else{
      $newtype = $type;
    }
    $data_dummy = '<?xml version="1.0" encoding="UTF-8" ?>
      <XMLDB PATH="' . $type . '/' . $name . '/db" VERSION="2015041700" COMMENT="XMLDB file for Moodle ' . $type . '/' . $name . '"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:noNamespaceSchemaLocation="../../../lib/xmldb/xmldb.xsd">
      <TABLES>
        <TABLE NAME="' . $name . '" COMMENT="Default comment for ' . $name . ', please edit me">
          <FIELDS>
            <FIELD NAME="id" TYPE="int" LENGTH="10" NOTNULL="true" UNSIGNED="true" SEQUENCE="true"/>
            <FIELD NAME="course" TYPE="int" LENGTH="10" NOTNULL="true" UNSIGNED="true" SEQUENCE="false" COMMENT="Course ' . $name . ' activity belongs to"/>
            <FIELD NAME="name" TYPE="char" LENGTH="255" NOTNULL="true" SEQUENCE="false" COMMENT="name field for moodle instances"/>
            <FIELD NAME="intro" TYPE="text" NOTNULL="true" SEQUENCE="false" COMMENT="General introduction of the ' . $name . ' activity"/>
            <FIELD NAME="introformat" TYPE="int" LENGTH="4" NOTNULL="true" UNSIGNED="true" DEFAULT="0" SEQUENCE="false" COMMENT="Format of the intro field (MOODLE, HTML, MARKDOWN...)"/>
            <FIELD NAME="timecreated" TYPE="int" LENGTH="10" NOTNULL="true" UNSIGNED="true" SEQUENCE="false"/>
            <FIELD NAME="timemodified" TYPE="int" LENGTH="10" NOTNULL="true" UNSIGNED="true" DEFAULT="0" SEQUENCE="false"/>
            <FIELD NAME="grade" TYPE="int" LENGTH="10" NOTNULL="true" DEFAULT="100" SEQUENCE="false" COMMENT="The maximum grade. Can be negative to indicate the use of a scale."/>
          </FIELDS>
          <KEYS>
            <KEY NAME="primary" TYPE="primary" FIELDS="id"/>
          </KEYS>
          <INDEXES>
            <INDEX NAME="course" UNIQUE="false" FIELDS="course"/>
          </INDEXES>
        </TABLE>
      </TABLES>
    </XMLDB>';

    mkdir($target .'/db', 0755, true);
    if (file_put_contents($target.'/db/install.xml', $data_dummy, FILE_APPEND | LOCK_EX)) {
      return true;
    }
    else {
      return false;
    }


  }
}
