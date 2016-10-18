<?php
namespace Atypax\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
/**
 * Comando de prueba.
 */
class TestCommand extends Command
{
  public function configure()
  {
    $this
      ->setName("test:test")
      ->setDescription("Comando de prueba para la aplicacion.")
      ->addArgument(
        'name',
        InputArgument::OPTIONAL,
        "Ingrese un nombre para mostrar"
      )
      ->addOption(
        'mayus',
        null,
        InputOption::VALUE_NONE,
        "Ingrese un valor opcional"
      )
    ;
  }

  public function execute(InputInterface $input, OutputInterface $output)
  {
    $nombre = $input->getArgument("name");
    if ($nombre) {
      $text = 'Hola ' . $nombre;
    }
    else {
      $text = "Hola desconocido";
    }
    if ($input->getOption("mayus")) {
      $text = strtoupper($text);
    }
    $output->write($text . "\n");
  }
}
