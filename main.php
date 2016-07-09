<?php
	error_reporting(E_ALL & ~E_NOTICE);
	set_time_limit(0);
	ini_set('display_errors', 'on');

	$pathIs = realpath(dirname(__FILE__));
	include $pathIs."/config.php";
	include $pathIs."/lib.php";
	updateList();

	$sock = fsockopen($server, $port, $errno, $errstr, 30);
	if (!$sock) {
		printf("errno: %s, errstr: %s", $errno, $errstr);
	} else {
		echo "\nSuccessful Connection.\n";
		echo "Included:\n";
		echo var_dump($coms);
	}
	if($sock) {
		fwrite($sock, "PASS ".$pass."\n");
		fwrite($sock, "USER ".$name."\n");
		fwrite($sock, "NICK ".$nick."\n");
		sleep(1);
		fwrite($sock, "JOIN ".$channel."\n");
		sleep(1);
		if ($showS) {
			fwrite($sock, "PRIVMSG ".$channel." :Up and running pajaHop\n");
		}
		unlink($pathIs."/log.txt");
		echo "=> RUNNING\n";
		$startTime = time();
		while(true) {
			$timeoutA = 0;
      $tick = 1;
			while($data = fgets($sock, 128)) {

				// Update lists
        if (checkC("admin", "update") || $tick % 60 == 0) {
					updateList();
        }

				// Commands
				$dataE = "<START>".nl2br($data);
				$genVars = explode(".".$host." PRIVMSG ".$channel." :", $dataE);
				$MSfrom = explode("@", explode("<br />", $genVars[0])[0])[1];
				$varsIN = explode(" ", explode($MSfrom.".".$host." PRIVMSG ".$channel." :", explode("<br />", $genVars[1])[0])[0]);
				foreach($coms as $file) {
					include $file;
				}

				if (isset($MSfrom) && !empty($MSfrom)) {
					file_put_contents($pathIs.'/log.txt', $MSfrom.": ".implode(" ", $varsIN)."\n", FILE_APPEND);
					echo $MSfrom.": ".implode(" ", $varsIN)."\n";
				} else {
					echo $data;
				}
				flush();

				// Separate all data
				$exData = explode(' ', $data);

				// Send PONG back to the server
				if($exData[0] == "PING") {
					fwrite($sock, "PONG ".$exData[1]."\n");
				}
        $tick++;
			}
		}
	} else {
		echo $eS . ": " . $eN;
	}
?>
