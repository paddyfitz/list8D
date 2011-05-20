<?php

// Requires Hay Kranen's PHP Markov Chain generator: 
// http://www.haykranen.nl/projects/markov/
//
// Generates fake modules/book titles/author names for sample data
 

require 'markov.php';

$order  = 3;
$length = 300;


echo "Modules\n=======\n";
echo implode("\n", gen_markov('modules.data', $order, $length)) . "\n\n"; 
echo "Titles\n======\n";
echo implode("\n", gen_markov('titles.data', $order, $length)) . "\n\n"; 
echo "Authors\n=======\n";
echo implode("\n", gen_markov('authors.data', $order, $length)) . "\n"; 

function gen_markov ($file, $order, $length) {    
	$text = file_get_contents($file);
        
	$markov_table = generate_markov_table($text, $order);
	$markov = generate_markov_text($length, $markov_table, $order);

	$amarkov =  explode("\n", $markov) ;
	array_shift($amarkov);
	array_pop($amarkov);

	return $amarkov;
}
        
