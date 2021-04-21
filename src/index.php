<?php

define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/WeatherFetcher/src/config/database.php'); 
require_once(__ROOT__.'/WeatherFetcher/src/FetchWeather.php'); 

$fw = new FetchWeather();

$fw->fetchAndSave($dbh,"London");
