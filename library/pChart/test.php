<?php
include('C:\\wamp\\www\\git\\mailingsystem\\library\\My\\ReportGraph.php');

            $file = 'D:\\trash\\repo1.txt';
//            $plik = fopen($file, "w");
//            fwrite($plik, serialize($reportdb->fetchAllReport6($table["campaign"], $table["send"])));
//$test = array('cityName' => 'Leszno', 'ilosc' => 12);
$datatmp = fread(fopen($file, "r"), filesize($file));
//$data = array(unserialize($datatmp)/*, unserialize($datatmp)*/);
//$data[0][0]['cityName'] = 'Moskwa';
//array_push($data[1], $test);


print_r($data);
    //        fclose($plik);





$data = array(
    array(
	    array(
		    'cityName' => 'Konin',
			'ilosc' => 12
		)
	)
);


$addiational = array(
    'seriesNames' => array(
//	    'Wysyłka nr 1: 2010-11-20 18:54',
	    'Wysyłka nr 2: 2010-11-20 23:14'),
	'graphName' => 'Porównanie 2 wysyłek',
	'savePath' => 'C:\\wamp\\www\\git\\mailingsystem\\library\\pChart\\test.png'
);

$test = new ReportGraph($data, null, 'cityName',$addiational);
?>