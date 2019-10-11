<?php
    /**
     * Skript zum Auslesen von Kostal Piko Wechselrichter PIKO 10.1
     */
      
        $user = 'pvserver';         // User-Name i.d.R. pvserver
        $pwd = 'xxx';              // Passwort des WR
        $wr = '192.168.178.253';    // Name oder IP-Adresse des Wechselrichters
        $db = 'piko';               // Datenbank-Name MySQL
        $dbhost = 'localhost';      // MySQL Datenbankserver
        $dbuser = 'root';           // MySQL User
        $dbpw = 'xxx';              // MySQL Passwort

//-------------------------------------------------------------------

		$url = "http://$user:$pwd@$wr/index.fhtml";        
     
        $contents = '';
        $handle = fopen ($url, "r");        
     
        while (!feof($handle)) {
            $contents .= fread($handle, 8192);
        }
        fclose($handle);
        $searchtext = array("\r","\n","&nbsp");
        $contents = str_replace($searchtext, "",$contents); //lösche Return u. so
        $full = explode("</tr>",$contents);    // erzeugt ein array mit </tr> als Trennung
        foreach ( $full as $line) {
            $line = strip_tags(html_entity_decode($line));  //lösche HTML Tags
            //echo $line;
     
            if ( preg_match("/aktuell/",$line))
                {$line = str_replace(" x x x","0",trim($line));
                unset($line2);
    	    $line2 = array_filter(explode(" ",$line)); // durch Leerzeichen trennen u. leere Felder löschen
    	    $line2 = array_values($line2); // array Index neu sortieren
    	    //print_r($line2);
    	    if ($line2[1] == "W") {
    		//unterschiedliche Anzeige Status An und Aus
    		//add array 1 mit 0 Watt
    		$line2[5] = $line2[4];
    		$line2[4] = $line2[3];
    		$line2[3] = $line2[2];
    		$line2[2] = $line2[1];
    		$line2[1] = 0;
    	    }
    	    //print_r($line2);
                $aktuell = $line2[1];
    	    $aktuellE = $line2[2];       //Einheit von aktuell
                $Gesamtenergie = $line2[4];
    	    $GesamtenergieE = $line2[5]; //Einheit von Gesamtenergie
                //echo "aktuell: $aktuell $aktuellE \nGesamtenergie: $Gesamtenergie $GesamtenergieE \n";
            }
    	if ( preg_match("/Tagesenergie/",$line)) {
                //echo "$line \n";
    	    unset($line2);
    	    $line2 = array_filter(explode(" ",$line)); // durch Leerzeichen trennen u. leere Felder löschen
    	    $line2 = array_values($line2); // array Index neu sortieren
    	    //print_r($line2);
     
                $tleistung = $line2[1];  // str_replace("Tagesenergie","",$line);
    	    $tleistungE = $line2[2]; // Einheit von tleistung
                //echo "Tagesenergie: $tleistung $tleistungE \n";
    	    //print_r($line2);
            }
            if ( preg_match("/Status/",$line))
                { $status = trim(str_replace("Status","",$line));
                //echo "Status: $status \n";
            }
     
        } // foreach Ende

        echo date("d.m.Y H:i:s"), " Erzeugung aktuell: $aktuell $aktuellE, Tagesenergie: $tleistung $tleistungE, Gesamtenergie: $Gesamtenergie $GesamtenergieE, Status: $status \n";

        //mysql stuff
        $link = mysqli_connect($dbhost, $dbuser, $dbpw, $db);
        if (!$link) {
            echo "<PRE>Fehler: konnte nicht mit MySQL verbinden." . PHP_EOL;
            echo "\nDebug-Fehlernummer: " . mysqli_connect_errno() . PHP_EOL;
            echo "\nDebug-Fehlermeldung: " . mysqli_connect_error() . PHP_EOL;
            exit;
        }

        $mysqldate = date("Y-m-d H:i:s");
        $sql = "INSERT INTO log (Datum, `Erzeugung Aktuell`, Tagesenergie, Gesamtenergie, Status) VALUES('$mysqldate', $aktuell, $tleistung, $Gesamtenergie, '$status')";
        if (!mysqli_query($link, $sql)) {
             echo "<PRE>Fehler: MySQL" . PHP_EOL;
             echo "\nDebug-Fehlernummer: " . mysqli_errno($link) . PHP_EOL;
             echo "\nDebug-Fehlermeldung: " . mysqli_error($link) . PHP_EOL;
             exit;
        }
        mysqli_close($link);