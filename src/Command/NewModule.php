<?php
namespace Atypax\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

use Atypax\Command\local\LocalModule;

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

    $module = null;

    switch ($typePlugins) {
      case 'local':
        $module = new LocalModule();
        break;
    }


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
      $count--;

    }

    if($validate){
      //$output->writeln($validateRoute);
      $output->writeln($module->templateModule($validateRoute,$namePlugins,$typePlugins));
    }else{
      $output->writeln("Debes estar dentro del directorio de un moodle");
    }
    //$output->writeln(getcwd());
  }

}
