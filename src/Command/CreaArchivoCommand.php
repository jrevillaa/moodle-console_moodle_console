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
class CreaArchivoCommand extends Command
{
  public function configure()
  {
    $this
      ->setName("archivo:crear")
      ->setDescription("Comando para la creacion de un archivo.")
      ->addOption(
        'msg',
        null,
        InputOption::VALUE_REQUIRED,
        "Ingrese un dato para guardar en el archivo."
      )
    ;
  }

  public function execute(InputInterface $input, OutputInterface $output)
  {
    if ($mensaje = $input->getOption("msg")) {
      // echo $mensaje;exit;
      $target = "test/test.txt";
      if (!is_dir(dirname($target))) {
        mkdir(dirname($target), 0777, true);
      }
      if (file_put_contents($target, $mensaje, FILE_APPEND | LOCK_EX)) {
        $msg = "Archivo creado!";
      }
      else {
        $msg = "Error al crear el archivo.";
      }
    }
    else {
      $msg = "Nada que escribir";
    }
    $output->write($msg . "\n");
  }
}
