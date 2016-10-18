#!/usr/bin/env php
<?php
require __DIR__ . "/../vendor/autoload.php";

use Symfony\Component\Console\Application;
use Atypax\Command\TestCommand;
use Atypax\Command\CreaArchivoCommand;
use Atypax\Command\NewModule;

$app = new Application();

$app->add(new TestCommand());
$app->add(new CreaArchivoCommand());
$app->add(new NewModule);

$app->run();
