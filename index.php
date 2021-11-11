<?php
	ob_start("ob_gzhandler");


	if (isset($_GET['search'])) { // Daca suntem intr-un url (daca exista parametrul &search)
		// in orice alta pagina inafara de homepage (avem cache la pagina o ora)
		$seconds_to_cache = 86400; // 60*60*24 // 24 de ore
		$ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
		header("Expires: $ts");
		header("Pragma: cache");
		header("Cache-Control: max-age=$seconds_to_cache");

		header("HTTP/1.0 404 Not Found"); // daca exista parametrul &search in url facem 404 (page not found) sa nu se indexeze multe paginii aiurea
	} else { // daca suntem in homepage
		// in homepage avem header no cache
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1        // Nu tine chace pt pagini dinamice
		header("Expires: Mon, 27 Jul 2011 07:08:02 GMT");   // Date in the past  // Nu tine chace pt pagini dinamice
	}
	
	include('functions.php');
?>


<!doctype html>
<html>
<head>
<meta charset="utf-8">
<?php if (isset($_GET['search'])) echo '<meta name="robots" content="noindex">'; // daca exista parametrul &search in url facem noindex sa nu se indexeze multe paginii aiurea ?>
<title>SathosiBitcoin Block Explorer</title>
<link rel="canonical" href="<?php echo $_SERVER['SCRIPT_URI'] ?>" />
<link rel="shortcut icon" href="favicon.svg" />	
<link rel="stylesheet" href="style.css" type="text/css" />	
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Tangerine">

	
</head>

<body>
	
<center>

	<a href="https://satoshibitcoin.app" style="color:black; text-decoration: none; width: 500px; display:block" title="Homepage">
		<img style="vertical-align:bottom;" src="/assets/img/favicon.png" height="60">
	</a>
	
	
	<a href="https://satoshibitcoin.app/explorer/" style="color:black; text-decoration: none; width: 500px; display:block">
		<h1 style="margin-top:40px; margin-bottom:40px; "><img style="vertical-align:bottom;" src="favicon.svg" height="60"> SathosiBitcoin Block Explorer</h1>
	</a>

	<form method="get">
	  <label for="search"><input name="search" type="search" id="search" placeholder="Search hash or height" value="<?php echo htmlentities(trim($_GET['search'])) ?>"></label>
	  <input type="submit" id="search_button" value="Search">
	</form>

</center>
	
	
<?php



	  
	  
$search = trim($_GET['search']);
	
if ($search=='') { // Daca suntem in homepage (daca nu sa cautat nimic)
	echo '<br><br><center>Best Block</center>';
//	$getblockchaininfo_arr = rpc_get('{"jsonrpc": "1.0", "id":"curltest", "method": "getblockchaininfo" }');
	$bestblockhash = rpc_get('{"jsonrpc": "1.0", "id":"curltest", "method": "getbestblockhash" }');
	$block_arr             = rpc_get('{"jsonrpc": "1.0", "id":"curltest", "method": "getblock", "params": ["'.$bestblockhash.'"] }');
	if ($block_arr['hash']!='') { // daca am gasit block-ul
		print_r_table($block_arr);
		echo '<center>';
		if ($block_arr['nextblockhash']!='') echo '<a rel="nofollow" href="/explorer/?search='.$block_arr['nextblockhash'].'" style="color:black; text-decoration: none;">&lt;&lt;&lt; next</a> &nbsp; ';
		if ($block_arr['previousblockhash']!='') echo '<a rel="nofollow" href="/explorer/?search='.$block_arr['previousblockhash'].'" style="color:black; text-decoration: none;">previous &gt;&gt;&gt;</a>'; 
		echo '</center>';
	}
} else { // daca sa cautat ceva

	// cautam blocul
	$block_arr = rpc_get('{"jsonrpc": "1.0", "id":"curltest", "method": "getblock", "params": ["'.$search.'"] }');
	if ($block_arr['hash']!='') { // daca am gasit block-ul
		echo '<br><br><center>Block</center>';
		print_r_table($block_arr);
		echo '<center>';
		if ($block_arr['nextblockhash']!='') echo '<a rel="nofollow" href="/explorer/?search='.$block_arr['nextblockhash'].'" style="color:black; text-decoration: none;">&lt;&lt;&lt; next</a> &nbsp; ';
		if ($block_arr['previousblockhash']!='') echo '<a rel="nofollow" href="/explorer/?search='.$block_arr['previousblockhash'].'" style="color:black; text-decoration: none;">previous &gt;&gt;&gt;</a>'; 
		echo '</center>';

	}
	else { // Daca nu exista blocul cautam adresa tranzactiei
		$transaction_arr = rpc_get('{"jsonrpc": "1.0", "id":"curltest", "method": "getrawtransaction", "params": ["'.$search.'", true] }');
		if ($transaction_arr['txid']!='') { // daca am gasit tranzactia
			echo '<br><br><center>Transaction</center>';
			print_r_table($transaction_arr);
		}
	}

}

	  
  

?>
<br><br><br>
<center><a href="https://satoshibitcoin.app" target="_blank" style="color:black; text-decoration: none; display:block; font-size:13px;" title="Homepage">Copyright satoshibitcoin.app</a></center>
<br><br>
</body>
</html>
