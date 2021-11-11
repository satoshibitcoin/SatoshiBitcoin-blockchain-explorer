<?php

function rpc_get($post_json) {
	
	$result = false; // se va schimba pe parcurs

	$RPCusername = '<username>';
	$RPCpassword = '<password>';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_USERPWD, $RPCusername.':'.$RPCpassword);
	curl_setopt($ch, CURLOPT_URL, 'http://server2.satoshibitcoin.app:5332'); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	#curl_setopt($ch, CURLOPT_POSTFIELDS, '{"id":"0","method":"getblock","params":{"address":"00000002f563882865e73dd81e80a37016943d1d403785e65d6080d44903289e"}}'); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);

	curl_setopt($ch, CURLOPT_POST, 1);

	$headers = array(); 
	$headers[] = "Content-Type: application/x-www-form-urlencoded"; 
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$result = curl_exec($ch); 
	if (curl_errno($ch)) { 
		echo 'Error:'.curl_error($ch).PHP_EOL; 
	} 
	else {
//		echo $result;
		$result =json_decode($result,true)["result"]; // decodam json-ul primit de la server direct in array (si copiem informatia din ['result'])

	}
	curl_close($ch);
	
	return $result;
}

function convert_hashes_to_links($input) { /*convertim ID-urile la blockuri/adrese in linkuri*/
   $pattern = '@\b\w{64}\b@';
   return $output = preg_replace($pattern, '<a rel="nofollow" href="/explorer/?search=$0">$0</a>', $input);
}	
	
function print_r_table($arr) {  // afisam array-ul pe ecran
	
	// stergem caracterele din fata si din spatele la rezultatul functiei print_r()
	$str = print_r($arr, true);

	// stergem Array-ul
	$str = substr($str, 8);   // stergem cuvantul 'Array ()' din fata
	$str = substr($str, 0, -3);   // stergem paranteza de la sfarsit ')' din fata

	// stergem array-ul de prin interior subnivele
//	$str = str_replace("Array\n\r(", '', $str);
//	$str = preg_replace("/Array[\s\n\r\(]/i", '', $str);
//	$str = str_replace("    )\n", '', $str);

	// stergem la toate liniile patru spatii de la inceput daca exista
	$arr_lines = explode("\n",$str);
	foreach($arr_lines as &$line) {    
		if (substr($line, 0, 4)=='    ') $line = substr($line, 4); // stergem primele 4 caracrere din linie daca sunt 4 spatii
	}
	$str=implode("\n", $arr_lines);
	
	$str = str_replace("[tx] => Array", '[tx] => <b>Transactions:</b>', $str);
	$str = str_replace("[", '', $str);
	$str = str_replace("]", '', $str);
	$str = str_replace("=>", '=', $str);
	$str = str_replace("Array", '', $str);	

//	$str = str_replace("(", '', $str);
//	$str = str_replace(")", '', $str);

	
	
	

	// Afisem print_r() nostru
	echo '<div style="width:700px; display:block; margin-left:auto; margin-right:auto;">';
	echo '<pre>';
	echo  convert_hashes_to_links($str);
	echo '</pre>';
	echo '</div>';
}
?>
