<?php
function TailleRep ($chemin = ".", $ext = ".ugw") {
    $handle  = @opendir($chemin);
    $taille = 0;
    while ($file = @readdir($handle)) {
        if  ( (!(is_dir("$chemin/$file"))) &&  (strtolower(strrchr($file,".")) == strtolower($ext)) ){
            $taille = $taille + filesize("$chemin/$file");
        }
    }
    @closedir($handle);
    return $taille;
}

function TrouveDansFic($Fic, $Atrouver) {
    $HTheFile = fopen($Fic, 'r');
    while (!feof($HTheFile)){
        $Ligne = fgets($HTheFile,1024);
        $MaPos = strpos($Ligne, $Atrouver);
        if (!(is_bool($MaPos))) {
            $arraydata = explode('"', $Ligne);
            return $arraydata[1];
        }
    }
    fclose($HTheFile);
}

function ConvertTaille($Taille) {
    $symbols = array('o', 'Ko', 'Mo', 'Go', 'To', 'Po', 'Eo', 'Zo', 'Yo');
    $exp = $Taille ? floor(log($Taille) / log(1024)) : 0;
    return sprintf('%.1f '.$symbols[$exp], ($Taille/pow(1024, floor($exp))));
}

function FormatPerf($Nbre) {
    $Nbre = floor(round($Nbre*100))/100;
    $Nb = explode(".",$Nbre);
    $Perf = "";
    for ($i = 0 ; $i < strlen($Nb[0]) ; $i++) {
        $Perf .= $Nb[0][$i];
        if ( ($i%2) != (strlen($Nb[0])%2) ) $Perf .= " ";
    }
    $Perf = rtrim($Perf);
    if($Nb[1] > 0) $Perf.= ".".$Nb[1];
    Return $Perf;
}

function RetourneFic ($chemin = ".", $Masque = "", $Type = "", $TriFic = "") {
    $chemin  = ".";
    $handle  = @opendir($chemin);
    $now= time();
    $datenow = mktime(0,0,0, date('m', $now), date('d', $now), date('Y', $now));
    $arrayjours = array('dim' => 0, 'lun' => 1, 'Mar' => 2, 'Mer' => 3, 'Jeu' => 4, 'Ven' => 5, 'Sam' => 6,);
    $fileind = 0;
    while ($file = @readdir($handle)) {
        if( (!(is_dir("$chemin/$file"))) && (!(!(strstr($file, $Masque)))) ){
            $tabfile[$fileind]["Nom"]       = $file;
            $tabfile[$fileind]["Taille"]    = sprintf ("%.1f K%s",(filesize("$chemin/$file")/1024),"o");
            $tabfile[$fileind]["Date"]      = array_search(Date("w", filemtime("$chemin/$file")), $arrayjours) . " ugselweb.php" .Date("d/m/Y", filemtime("$chemin/$file"));
            $tabfile[$fileind]["Heure"]     = Date("H:i:s", filemtime("$chemin/$file"));
            $tabfile[$fileind]["DateTri"]   = filemtime("$chemin/$file");
            $tabfile[$fileind]["TailleTri"] = filesize("$chemin/$file");
            $tabfile[$fileind]["Age"]       = ceil((($datenow - mktime(0,0,0, date('m', filemtime("$chemin/$file")), date('d', filemtime("$chemin/$file")), date('Y', filemtime("$chemin/$file")))) / 86400));
            if (strstr($file, $Type) == True) $tabfile[$fileind]["Type"] = $Type; else $tabfile[$fileind]["Type"] = "Man";

            if ($Masque == "Comp�tition") {
                $tabfile[$fileind]["Ugsel"] = "";
                $tabfile[$fileind]["Sport"] = "";
                $tabfile[$fileind]["Description"] = "";
                $tabfile[$fileind]["Obs"] = "";
                $TheFile=gzopen($file, 'rb');
                while (!gzeof($TheFile)){
                    $ligne=trim(gzgets($TheFile,65535));
                    if (strlen($ligne) > 0) {
                        if(substr($ligne, 0, 4) == "-- #") {
                            $tab = explode("#", $ligne);
                            if($tab[1] != "") $tabfile[$fileind]["Ugsel"] = $tab[2];
                            if($tab[2] != "") $tabfile[$fileind]["Sport"] = $tab[3];
                            if($tab[3] != "") $tabfile[$fileind]["Description"] = $tab[4];
                            if($tab[4] != "") $tabfile[$fileind]["Obs"] = $tab[5];
                            $tabfile[$fileind]["R�sum�"] = $tabfile[$fileind]["Sport"]." - ".$tabfile[$fileind]["Ugsel"]." -> ".$tabfile[$fileind]["Date"]." � ".$tabfile[$fileind]["Heure"]." (".$tabfile[$fileind]["Taille"].") ".$tabfile[$fileind]["Description"];
                        }
                    }
                }
                gzclose($TheFile);
            }

            $fileind++;
        }
    }
    @closedir($handle);
    if ($fileind > 0) {
        if ($Masque == "Comp�tition") {
            switch($TriFic) {
                case "Date"   : $tabfile = TriTableau($tabfile, "DateTri","DATE","ASC", "Ugsel","STR","ASC", "Sport","STR","ASC"); Break;
                case "Taille" : $tabfile = TriTableau($tabfile, "TailleTri","NB","ASC", "Ugsel","STR","ASC", "Sport","STR","ASC") ; Break;
                case "Ugsel"  : $tabfile = TriTableau($tabfile, "Ugsel","STR","ASC", "Sport","STR","ASC", "DateTri","DATE","ASC"); Break;
                case "Sport"  : $tabfile = TriTableau($tabfile, "Sport","STR","ASC", "Ugsel","STR","ASC", "DateTri","DATE","ASC"); Break;
                default       : $tabfile = TriTableau($tabfile, "Sport","STR","ASC", "Ugsel","STR","ASC", "DateTri","DATE","ASC");
            }
        } else {
            switch($TriFic) {
                case "Date"   : $tabfile = TriTableau($tabfile, "DateTri","DATE","ASC"); Break;
                case "Taille" : $tabfile = TriTableau($tabfile, "TailleTri","NB","ASC", "DateTri","DATE","ASC"); Break;
                default       : $tabfile = TriTableau($tabfile, "DateTri","DATE","ASC");
            }
        }
        return $tabfile;
    } else return 0;
}

function TriTableau() {
    $args   = func_get_args();
    $arrays = $args[0];
    for ($i = (count($args)-1); $i > 0; $i--) {
        if (in_array($args[$i], array("ASC" , "DESC", "STR" , "DATE" , "NB"))) continue;
        $compstr = create_function('$a,$b','return strcasecmp($a["'.$args[$i].'"], $b["'.$args[$i].'"]);');
        $compnb = create_function('$a,$b','return $a["'.$args[$i].'"] <= $b["'.$args[$i].'"];');
        if ($args[$i+1] == "STR") usort($arrays, $compstr); else usort($arrays, $compnb);
        if ($args[$i+2] == "DESC") $arrays = array_reverse($arrays);
    }
    return $arrays;
}

function TrouveMax($Sql) {
    $req = bf_mysql_query($Sql);
    $res = mysql_fetch_array($req);
    if ($res) Return($res[0]); Else Return("0");
}

function getTime() {
    static $chrono = false, $deb;
    if ($chrono === false) {
        $deb = array_sum(explode(' ',microtime()));
        $chrono = true;
        return NULL;
    } else {
        $chrono = false;
        $fin = array_sum(explode(' ',microtime()));
        return round(($fin - $deb), 3);
    }
}

Function JoueSon($Son) {
    Global $SON;
    if ($SON == "Oui") {if (file_exists($Son)) echo "<EMBED width='0' height='0' src='$Son' loop='false' autostart='true' hidden='true'>";}
}