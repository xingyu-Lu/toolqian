#!/usr/bin/env php
<?php

if (!$loader = include __DIR__ . '/../vendor/autoload.php') {
    die('You must set up the project dependencies.');
}

$app = new \Cilex\Application('Cilex');

$app->command(new \Cilex\Command\GreetCommand());
$app->command(new \Cilex\Command\DemoInfoCommand());

$app->command(new \Cilex\Command\RequestCommand());
$app->command(new \Cilex\Command\SearchLogCommand());
$app->command(new \Cilex\Command\MobileVerifyCommand());
$app->command(new \Cilex\Command\MobileNumberSegment());
$app->command(new \Cilex\Command\DecodeCommand());
$app->command(new \Cilex\Command\NotSameRegionCommand());
$app->command(new \Cilex\Command\YflingCommand());

$app->command('foo', function ($input, $output) {
    $output->writeln('Example output');
});

$app->run();
