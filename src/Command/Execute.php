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
class Execute extends Command
{
    public function configure()
    {
        $this
            ->setName("execute")
            ->setAliases(['exec', 'exe'])
            ->setDescription("Comando para ejecución de helpers")
            ->addArgument(
                'type',
                InputArgument::REQUIRED,
                "Se necesita saber el tipo helper"
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {

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
            $count--;

        }

        if($validate){
            //$output->writeln($validateRoute);
            //$output->writeln("Hola mundo!!");
            $this->exe_helpers($typePlugins,$validateRoute);
            $output->writeln('sss');
        }else{
            $output->writeln("Debes estar dentro del directorio de un moodle");
        }
        //$output->writeln(getcwd());
    }

    function exe_helpers($name,$path){

        switch ($name){
            case 'restart':
                shell_exec('/usr/bin/php  ' . $path . '/admin/cli/reset_password.php') ;
            break;
            default:
                return 'no se puede ejecutar';
            break;
        }

    }

}