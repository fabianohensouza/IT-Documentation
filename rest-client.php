<?php
$location = 'http://localhost/IT-Documentation/rest.php';
$parameters = [];
$parameters['class']  = 'ManutencoesServices';
$parameters['method'] = 'getData';
$parameters['id']     = '1';

$url = $location . '?' . http_build_query($parameters);
echo '<pre>';
print_r($url);
print_r(json_decode( file_get_contents($url)));
echo '<hr>';
var_dump( json_decode( file_get_contents($url) ) );
