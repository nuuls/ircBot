<?php
	if (checkC("admin", "!-banlist")) {
		unset($varsIN[0]);
		$stringB = implode(" ", $varsIN);
		$varsIN[0] = $MSfrom;
		echo "=> Removed from banlist: ".$stringB."\n";
		if(($key = array_search($stringB, $blacklist)) !== false) {
			unset($blacklist[$key]);
		}
		file_put_contents($pathIs.'/blacklist.txt', implode("\n", $blacklist)."\n");
		if ($showS) {
			fwrite($sock, "PRIVMSG ".$channel." :Removed \"".$stringB."\" from the blacklist.\n");
		}
	}
?>