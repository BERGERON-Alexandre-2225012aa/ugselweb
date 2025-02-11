<?php


Function MajConnexions($id = "") {
    Global $Consult;
    $tpsAdm = 600; $tpsEtab = 300; $tpsConsult = 60;
    $temps_actuel = date("U");
    bf_mysql_query('UPDATE Connexions SET Ip = "Out" WHERE Id = "Admin" AND Temps < "'.($temps_actuel - $tpsAdm).'"');
    bf_mysql_query('UPDATE Connexions SET Ip = "Out" WHERE Id <> "Admin" AND Id <> "Consultant" AND Temps < "'.($temps_actuel - $tpsEtab).'"');
    bf_mysql_query('UPDATE Connexions SET Ip = "Out" WHERE Id = "Consultant" AND Temps < "'.($temps_actuel - $tpsConsult).'"');
    $req = bf_mysql_query('SELECT Session FROM Connexions WHERE Ip = "Out"');if (!(!$req)) { while ($data = mysql_fetch_array($req)) { unlink(session_save_path().'/sess_'.$data['Session']);} }
    bf_mysql_query('DELETE FROM Connexions WHERE Ip = "Out"');
    if ((!(file_exists(session_save_path().'/sess_'.session_id()))) && (!($Consult)) ) logout("Veuillez vous reconnecter.");
    $req = bf_mysql_query('SELECT Temps, Ip FROM Connexions WHERE Session = "'.session_id().'"');
    if (!(!$req)) {
        $data = mysql_fetch_array($req);
        if (!($data)) bf_mysql_query('INSERT INTO Connexions VALUES("'.$_SERVER['REMOTE_ADDR']. '","'.session_id(). '","'.$temps_actuel.'","'.$id.'",Now(),"")');
        else bf_mysql_query('UPDATE Connexions SET Id = "'.$id.'", Temps = "'.$temps_actuel.'", Depart = Depart WHERE Session = "'.session_id().'"');
    }
}

function bf_stop($message = "Echec de la connexion.") {
    logout($message);
}

function bf_mysql_connect($MonUtilisateur = "", $MonMdp = "") {
    global $HOSTNAME, $UTILISATEUR, $MDP;
    if ($MonUtilisateur == "") $MonUtilisateur = $UTILISATEUR;
    if ($MonMdp == "") $MonMdp = $MDP;
    $retour = @mysql_connect($HOSTNAME, $MonUtilisateur, $MonMdp);
    if (!(is_resource($retour))) bf_stop("Echec de la connexion avec le serveur.");
    return $retour;
    bf_mysql_close($retour);
}

function bf_mysql_select_db($MaBdd = "", $link_identifier = 0, $MonUtilisateur = "", $MonMdp = "" ){
    global $BDD;
    if ($MaBdd == "") {$MaBdd = $BDD; $ext = 0 ; $mysql_connect = bf_mysql_connect();} else { $ext = 1; $mysql_connect = bf_mysql_connect($MonUtilisateur, $MonMdp);}
    if ($link_identifier <> 0) $retour = @mysql_select_db($MaBdd, $link_identifier); else $retour = @mysql_select_db($MaBdd, $mysql_connect);
    if (!($retour) && ($ext == 0)) bf_stop("Echec de la connexion. <BR><BR>".urlencode("Le site est actuellement ferm�."));
    return $retour;
    bf_mysql_close($mysql_connect);
}

function bf_mysql_query ($query, $link_identifier = 0, $ALocker = "", $MaBdd = "", $MonUtilisateur = "", $MonMdp = "") {
    Global $Adm, $action, $BDD;

    if ($MaBdd == "") {
        $MaBdd = $BDD;
        $mysql_connect = bf_mysql_connect();
        bf_mysql_select_db($MaBdd);
    } else {
        $mysql_connect = bf_mysql_connect($MonUtilisateur, $MonMdp);
        bf_mysql_select_db($MaBdd, $link_identifier, $MonUtilisateur, $MonMdp);
    }

    if ( (!($Adm)) && ($action != "logout") ) {
        $req = @mysql_query("SELECT `Maintenance` FROM `Paramweb`", $mysql_connect);
        if ((!(!$req)) && (mysql_num_rows($req) > 0)) {
            $data = mysql_fetch_assoc($req);
            mysql_free_result($req);
            if (($data['Maintenance']) == 1) echo bf_stop("Echec de la connexion. <BR><BR>".urlencode("Le site est actuellement ferm�."));
        } else {
            bf_stop("Echec de la connexion. <BR><BR>".urlencode("Le site est actuellement ferm�."));
        }
    }

    if (($ALocker != "")) {
        if (!(is_array($ALocker))) $ALocker = array("$ALocker");
        $arrayLock = array("`Sports`", "`Cat�gories`", "`Epreuves`", "`Groupes`", "`Secteurs`", "`Etablissements`", "`Licenci�s`", "`Comp�titions`", "`Epreuves Comp�titions`", "`Tours Epreuves Comp�titions`", "`Participations`", "`Equipes`", "`Param�tres`","`Paramweb`");
        $rr = mysql_query("LOCK TABLES ".implode(" WRITE, ", $ALocker)." WRITE, " .implode(" READ, ", array_diff($arrayLock,$ALocker)). " READ");
    }

    if ($query != "") {
        if ($link_identifier <> 0) $retour = @mysql_query($query, $link_identifier); else $retour = @mysql_query($query, $mysql_connect);
    } else $retour = true;

    return $retour;
    bf_mysql_close($mysql_connect);

}

function bf_mysql_close($link_identifier = 0){
    if ($link_identifier <> 0) $retour = @mysql_close($link_identifier); else @mysql_close();
    return $retour;
}

function PurgeTables() {
    Global $PURGE, $Adm;
    if (($Adm) && ($PURGE != 0)) {
        bf_mysql_query("DELETE Participations FROM Participations INNER JOIN Licenci�s ON Participations.ParLicCode = Licenci�s.LicNumLicence INNER JOIN Etablissements ON Licenci�s.LicEtabCode = Etablissements.EtabCode INNER JOIN Secteurs ON Etablissements.EtabSecCode = Secteurs.SecCode WHERE SecLibel <> $PURGE");
        bf_mysql_query("DELETE Equipes FROM Equipes INNER JOIN Etablissements ON Equipes.EquEtabCode = Etablissements.EtabCode INNER JOIN Secteurs ON Etablissements.EtabSecCode = Secteurs.SecCode WHERE SecLibel <> $PURGE");
        bf_mysql_query("DELETE Licenci�s FROM Licenci�s INNER JOIN Etablissements ON Licenci�s.LicEtabCode = Etablissements.EtabCode INNER JOIN Secteurs ON Etablissements.EtabSecCode = Secteurs.SecCode WHERE SecLibel <> $PURGE");
        bf_mysql_query("DELETE Etablissements FROM Etablissements INNER JOIN Secteurs ON Etablissements.EtabSecCode = Secteurs.SecCode WHERE SecLibel <> $PURGE");
        bf_mysql_query("DELETE Secteurs FROM Secteurs WHERE SecLibel <> $PURGE");
    }
}

function RetAS($Etab) {
    $Ret = "";
    $res = bf_mysql_query("SELECT EtabAS FROM Etablissements WHERE EtabNum = ".$Etab);
    if (!(!$res)) {
        $row = mysql_fetch_array($res);
        if ($row[0] != "") $Ret = " OR (EtabAS = '".$row[0]."')";
    }
    Return $Ret;
}

function OptimizeTables() {
    Global $BDD;
    $table = mysql_list_tables($BDD);
    $sql = "";
    $req = mysql_query('SHOW TABLE STATUS');
    if (!(!($req))) {
        while($data = mysql_fetch_assoc($req)) {
            if($data['Data_free'] > 0) 	$sql .= '`'.$data['Name'].'`, ';
        }
        if ($sql != "")	bf_mysql_query("OPTIMIZE TABLE ".substr($sql, 0, (strlen($sql)-2)));
    }
}

function RenumeroteEquipes($Compet) {
    bf_mysql_query("UPDATE Sports INNER JOIN Comp�titions ON Sports.SpoCode = Comp�titions.CompetSpoCode INNER JOIN Equipes ON Comp�titions.CompetCode = Equipes.EquCompetCode INNER JOIN `Epreuves Comp�titions` ON Equipes.EquEprCompetCode = `Epreuves Comp�titions`.EprCompetCode INNER JOIN Epreuves ON `Epreuves Comp�titions`.EprCompetEprCode = Epreuves.EprCode SET EquCatCode = EprCatCode WHERE (ISNULL(EquCatCode) OR EquCatCode = 0) AND SpoGestionPerf = -5 AND EquCompetCode = $Compet", 0, "`Equipes`");
    bf_mysql_query("UPDATE Equipes SET EquNum = EquNum * -1, EquCompl�ment = Null Where EquCompetCode = $Compet", 0, "`Equipes`");
    $Res = bf_mysql_query("SELECT EquCode, EtabNum, EquTour, EquEtabCode, CatCode FROM Etablissements INNER JOIN Secteurs On Etablissements.EtabSecCode = Secteurs.SecCode, Cat�gories INNER JOIN Equipes ON Cat�gories.CatCode = Equipes.EquCatCode LEFT JOIN `Epreuves Comp�titions` ON Equipes.EquEprCompetCode = `Epreuves Comp�titions`.EprCompetCode LEFT JOIN Epreuves ON `Epreuves Comp�titions`.EprCompetEprCode = Epreuves.EprCode where Etablissements.EtabCode = Equipes.EquEtabCode AND EquCompetCode = $Compet ORDER BY Cat�gories.Ordre, Epreuves.Ordre, Secteurs.Ordre, Etablissements.EtabNum, EquNum Desc, EquTour");
    while ($res = mysql_fetch_array($Res)) {
        if ( ($res["EquTour"] == 1) || ($res["EquTour"] == Null) ) {
            $Maxnum = mysql_fetch_array(bf_mysql_query("SELECT MAX(EquNum) AS Max FROM Equipes INNER JOIN Etablissements ON Equipes.EquEtabCode = Etablissements.EtabCode WHERE EquNum > 0 AND EquCompetCode = $Compet AND EquEtabCode = ".$res["EquEtabCode"]));
            if ($Maxnum["Max"] != Null) $NumEqu = $Maxnum["Max"] + 1; else $NumEqu = ($res["EtabNum"] * 10) + 1;
            if (Floor($NumEqu / 10) - $res["EtabNum"] == 1) $NumEqu = (($NumEqu / 10) - 1) * 100 + 10;
            $Countcomp = mysql_fetch_array(bf_mysql_query("SELECT COUNT(EquCode) AS Nbre FROM Equipes INNER JOIN Etablissements ON Equipes.EquEtabCode = Etablissements.EtabCode WHERE EquTour = 1 AND EquNum > 0 AND EquCatCode = ".$res["CatCode"]." AND EquNum < $NumEqu AND EquCompetCode = $Compet AND EquEtabCode = ".$res["EquEtabCode"]));
            $NumComp = $Countcomp["Nbre"] + 1;
            bf_mysql_query("UPDATE Equipes SET EquNum = $NumEqu, EquCompl�ment = $NumComp Where EquCompetCode = $Compet AND EquCode = ".$res["EquCode"]);
            $SauveNumEqu = $NumEqu;
            $SauveCompEqu = $NumComp;
        } Else {
            bf_mysql_query("UPDATE Equipes SET EquNum = $SauveNumEqu, EquCompl�ment = $SauveCompEqu Where EquCompetCode = $Compet AND EquCode = ".$res["EquCode"]);
        }
    }
}

function ConstruitStat($TypeStat = 0, $MonSport = 1, &$queryStr, &$NomsColonnes, &$ChampsAli, &$ChampsFor, &$ChampsAff, &$ChampsType, &$Choix, $Etab = "") {
    $NomsColonnes  = array('','Code ','Num�ro','Code','Nom','Ville','Total','Ins','Non','F','G');
    $ChampsAli     = array('','','center','center','','','right','right','right','right','right');
    $ChampsFor     = array('','','%06d','','','','','','','','');
    $ChampsAff     = array(false,false,true,true,true,true,true,true,true,true,true);
    $ChampsType    = array("","Texte","Texte","Texte","Texte","Texte","Texte","Texte","Texte","Texte","Texte");
    $Choix = array("exporter", "liste");
    $MaStr = " SUM(1) AS Total, SUM(IF(LicInscrit,1,0)) AS Ins, SUM(IF(NOT LicInscrit,1,0)) AS Non, SUM(IF(LicSexCode=2,1,0)) AS F, SUM(IF(LicSexCode=1,1,0)) AS G,";
    $ResCat = bf_mysql_query("SELECT * FROM Sports INNER JOIN Cat�gories ON Sports.SpoCode = Cat�gories.CatSpoCode WHERE SpoCode = $MonSport AND CatPrim = TRUE ORDER BY Cat�gories.Ordre");
    while ($resCat = mysql_fetch_array($ResCat)) {
        $MaStr = $MaStr." SUM(IF(CatLibelCourt='".$resCat['CatLibelCourt']."',1,0)) AS ".$resCat['CatLibelCourt'].",";
        array_push ($NomsColonnes, $resCat['CatLibelCourt']);
        array_push ($ChampsAli,"right");
        array_push ($ChampsFor,"");
        array_push ($ChampsAff,true);
        array_push ($ChampsType,"Texte");
    }
    $MaStr = substr($MaStr,0,-1);
    $strRel = "";
    if ($TypeStat >= -1) {
        if ($TypeStat == -1) $reqRelay = bf_mysql_query("SELECT EquRelayeurs FROM Equipes WHERE NOT(EquRelayeurs = '')"); else $reqRelay = bf_mysql_query("SELECT EquRelayeurs FROM Equipes WHERE NOT(EquRelayeurs = '') AND EquCompetCode = ".$TypeStat);
        if ($reqRelay) {
            $strRel = "''";
            while ($resRel = mysql_fetch_array($reqRelay)) {
                $strRel .= ",".str_replace(" - ", ",", RetRelayeurs($resRel['EquRelayeurs'],1));
            }
            $strRel = " SELECT LicNumLicence AS ParLicCode, ".$TypeStat." AS ParCompetCode FROM Licenci�s WHERE LicNumLicence IN (".$strRel.") ";
        }
    }
    for( $i = 0; $i <= 3; $i++ ) {
        $Fin = "WHERE ( (Licenci�s.LicNaissance Between CatDateD�b And CatDateFin) And LicSexCode = CatSexCode And CatSpoCode = $MonSport And CatPrim = TRUE) ";
        if ($i == 0) $Fin = $Fin.$Etab;
        if ($TypeStat == -2) $MaStrTab[$i] = $MaStr." FROM Cat�gories, Etablissements INNER JOIN Licenci�s ON Etablissements.EtabCode = Licenci�s.LicEtabCode INNER JOIN Secteurs ON Secteurs.SecCode = Etablissements.EtabSecCode ".$Fin;

        $maUnionRelayeurs = "";
        if (($TypeStat >= -1) && ($strRel != "")) {
            bf_mysql_query("CREATE TEMPORARY TABLE TempParRelayeurs$i $strRel");
            if ($TypeStat > 0) $maUnionRelayeurs = " UNION (SELECT ParLicCode, ParCompetCode FROM TempParRelayeurs$i GROUP BY ParLicCode, ParCompetCode HAVING ParCompetCode = $TypeStat) "; else $maUnionRelayeurs = " UNION (SELECT ParLicCode FROM TempParRelayeurs$i GROUP BY ParLicCode) ";
        }

        if ($TypeStat > 0) $select = "(SELECT ParLicCode, ParCompetCode FROM Participations GROUP BY ParLicCode, ParCompetCode HAVING ParCompetCode = $TypeStat) $maUnionRelayeurs"; else $select = "(SELECT ParLicCode FROM Participations GROUP BY ParLicCode) $maUnionRelayeurs";
        if ($TypeStat >= -1) {
            bf_mysql_query("CREATE TEMPORARY TABLE TempPar$i $select");
            $MaStrTab[$i] = $MaStr." FROM Cat�gories, Licenci�s INNER JOIN Etablissements ON LicEtabCode = EtabCode INNER JOIN Secteurs ON SecCode = EtabSecCode INNER JOIN TempPar$i ON Licenci�s.LicNumLicence = TempPar$i.ParLicCode ".$Fin;
        }
    }
    $queryStr      = "SELECT '' AS ' ', CONCAT(SecR�gionCode, ' ',SecLibel,' ', EtabNomCourt) AS `Code `, EtabNum AS Num�ro, EtabNomCourt AS Code, EtabNom AS Nom, EtabVille AS Ville, ".$MaStrTab[0]." GROUP BY EtabNum ";
    If ($Etab == "") $queryStr = $queryStr."
					  UNION SELECT '�' AS F, CONCAT(SecR�gionCode, ' ', SecLibel, '��') AS `Code `,  '' AS Num�ro, '' AS Code, CONCAT(SecLibell�, ' (', COUNT(DISTINCT(EtabCode)),' Etab)') AS Nom, '' AS Ville, ".$MaStrTab[1]." GROUP BY SecCode 
					  UNION SELECT '��' AS F, CONCAT(SecR�gionCode, '���') AS `Code `,  '' AS Num�ro, '' AS Code, CONCAT(SecR�gionCode, ' (', COUNT(DISTINCT(EtabCode)),' Etab)') AS Nom, '' AS Ville, ".$MaStrTab[2]." GROUP BY SecR�gionCode
					  UNION SELECT '���' AS F, '���' AS `Code `,  '' AS Num�ro, '' AS Code, CONCAT('Ugsel', ' (', COUNT(DISTINCT(EtabCode)),' Etab)')  AS Nom, '' AS Ville, ".$MaStrTab[3]." GROUP BY `Code `";
    $queryStr = $queryStr." ORDER BY `Code `";
    Return($queryStr);
}

function RetourneRep ($chemin = ".", $Masque = "") {
    $handle  = @opendir($chemin);
    $fileind = 0;
    while ($file = @readdir($handle)) {
        if( (is_dir("$chemin/$file")) && (!(!(strstr($file, $Masque)))) ){
            $monIndex = "$chemin/$file/bzh.php";
            if (file_exists($monIndex)) {
                $tabfile[$fileind]["Nom"] = TrouveDansFic($monIndex,"UGSELNOM");
                $tabfile[$fileind]["Bdd"] = TrouveDansFic($monIndex,"BDD");
                $tabfile[$fileind]["Utilisateur"] = TrouveDansFic($monIndex,"UTILISATEUR");
                $tabfile[$fileind]["Mdp"] = TrouveDansFic($monIndex,"MDP");
                $tabfile[$fileind]["Lic Externe"] = "-";
                $tabfile[$fileind]["Lic Interne"] = "-";
                $reqlicExt = bf_mysql_query("SELECT COUNT(*) AS NB FROM Licenci�s", 0, "", $tabfile[$fileind]["Bdd"], $tabfile[$fileind]["Utilisateur"],$tabfile[$fileind]["Mdp"]);
                if (!(!($reqlicExt))) {
                    $reslicExt = mysql_fetch_array($reqlicExt);
                    if (!(!($reslicExt))) $tabfile[$fileind]["Lic Externe"] = $reslicExt["NB"];
                }
                $reqlicInt = bf_mysql_query("SELECT COUNT(*) AS NB FROM (Etablissements INNER JOIN Licenci�s ON Etablissements.EtabCode = Licenci�s.LicEtabCode) INNER JOIN Secteurs ON Etablissements.EtabSecCode = Secteurs.SecCode WHERE SecLibel = ".substr($tabfile[$fileind]["Utilisateur"],5,3) );
                if (!(!($reqlicInt))) {
                    $reslicInt = mysql_fetch_array($reqlicInt);
                    if (!(!($reslicInt))) $tabfile[$fileind]["Lic Interne"] = $reslicInt["NB"];
                }
                $fileind++;
            }
        }
    }
    @closedir($handle);
    if ($fileind > 0) {
        $tabfile = TriTableau($tabfile, "Nom","STR","ASC");
        return $tabfile;
    } else return 0;
}

Function RetRelayeurs($Relayeurs, $TypeInfo = 2, $SaisieOblige = False) {
    Global $PHP_SELF, $Adm, $Etab;
    $RetRelayeurs = "";
    if (!($Adm)) $mawhere = " AND (EtabNum = ".$Etab.RetAS($Etab).")"; else $mawhere = "";
    switch ($TypeInfo) {
        Case 0 : {
            $req = bf_mysql_query("SELECT Licenci�s.LicInscrit, Licenci�s.LicNumLicence, Licenci�s.LicNom, Licenci�s.LicPr�nom, CatLibelCourt FROM Cat�gories, Etablissements INNER JOIN Licenci�s ON Etablissements.EtabCode = Licenci�s.LicEtabCode WHERE (Licenci�s.LicNaissance Between CatDateD�b And CatDateFin) And LicSexCode = CatSexCode AND CatSpoCode = 1 AND LicNumLicence = ".$Relayeurs.$mawhere);
            if ($req) {
                $res = mysql_fetch_array($req);
                if ($res) $RetRelayeurs = "(".$res['CatLibelCourt'].") ".$res['LicNom']." ".$res['LicPr�nom']; else $RetRelayeurs = "Erreur";
            } else {
                if ($Relayeurs != "") $RetRelayeurs = "Erreur";
            }
            Break;
        }
        Case 1 :
        Case 2 :
        Case 3 :
        Case 4 : {
            $arrayres = array();
            $arrayrels1 = array();
            $arrayrels  = array();

            If ($TypeInfo >= 3) {
                for ($i = 0; $i < 4; $i++) {
                    if ((isset($_POST["EquRelayeurs$i"])) && ($_POST["EquRelayeurs$i"] != "") ) {
                        if ($Relayeurs != "") $Relayeurs = $Relayeurs."-";
                        $Relayeurs = $Relayeurs.$_POST["EquRelayeurs$i"];
                    }
                }
            }
            if ($Relayeurs != "") {
                $arrayrels1 = explode("-",$Relayeurs);
                $arrayrels  = array_unique($arrayrels1);
            }
            for ($i = 0; $i < count($arrayrels); $i++) {
                $arrayrel = explode(" ",trim($arrayrels[$i]));
                if (is_numeric($arrayrel[0])) {
                    If ($TypeInfo == 1) array_push($arrayres, $arrayrel[0]);
                    If (($TypeInfo == 2)||($TypeInfo == 3)||($TypeInfo == 4)) {
                        $req = bf_mysql_query("SELECT Licenci�s.LicInscrit, Licenci�s.LicNumLicence, Licenci�s.LicNom, Licenci�s.LicPr�nom, CatLibelCourt FROM Cat�gories, Etablissements INNER JOIN Licenci�s ON Etablissements.EtabCode = Licenci�s.LicEtabCode WHERE (Licenci�s.LicNaissance Between CatDateD�b And CatDateFin) And LicSexCode = CatSexCode And CatSpoCode = 1 AND LicNumLicence = ".$arrayrel[0].$mawhere);
                        $res = mysql_fetch_array($req);
                        if ($res) array_push($arrayres, $arrayrel[0]." (".$res['CatLibelCourt'].") ".$res['LicNom']." ".$res['LicPr�nom']);
                        if (isset($_POST['EprLibelCourt']) && ($_POST['EprLibelCourt'] != "")) {
                            $rescat = mysql_fetch_array(bf_mysql_query("SELECT EprCatCode, EprLibelCourt, SpoGestionPerf, SpoCode FROM Sports INNER JOIN Comp�titions ON Sports.SpoCode = Comp�titions.CompetSpoCode INNER JOIN `Epreuves Comp�titions` ON Comp�titions.CompetCode = `Epreuves Comp�titions`.EprCompetCompetCode INNER JOIN Epreuves ON `Epreuves Comp�titions`.EprCompetEprCode = Epreuves.EprCode INNER JOIN Cat�gories ON Epreuves.EprCatCode = Cat�gories.CatCode WHERE `Epreuves Comp�titions`.EprCompetCode = ".$_POST["EprLibelCourt"]));
                            if ($rescat['SpoGestionPerf'] == -5) {
                                $reslic =  mysql_fetch_array(bf_mysql_query("SELECT CatCode FROM Cat�gories, Etablissements INNER JOIN Licenci�s ON Etablissements.EtabCode = Licenci�s.LicEtabCode WHERE (Licenci�s.LicNaissance Between CatDateD�b And CatDateFin) And LicSexCode = CatSexCode And CatSpoCode = ".$rescat['SpoCode']." AND LicNumLicence = ".$arrayrel[0].$mawhere));
                                if ($rescat['EprCatCode'] != $reslic['CatCode']) $RetRelayeurs = "Erreur";
                            }
                        }
                        if ($SaisieOblige) {if (count($arrayrels) < 4) $RetRelayeurs = "Erreur";}
                    }
                }
            }
            if ($TypeInfo != 4) $RetRelayeurs = implode(" - ", $arrayres); else {
                if (count($arrayres) != count($arrayrels1)) $RetRelayeurs = "Erreur";
            }
            break;
        }
    }
    Return ($RetRelayeurs);
}

Function MajOrdre($TablesNames = "") {
    if ($TablesNames != "") {
        if (!(is_array($TablesNames))) $TablesNames = array("$TablesNames");
        for ($t = 0; $t < count($TablesNames); $t++) {
            $cpte = 0;
            $mastr = "SELECT * FROM ".$TablesNames[$t][0]." ORDER BY ";
            if (count($TablesNames[$t]) == 1) $mastr = $mastr."Ordre"; else $mastr = $mastr.$TablesNames[$t][1].", Ordre";
            $res = bf_mysql_query($mastr);
            $Chp = mysql_fetch_field($res, 0);
            if (!(!$res)) {
                while ($row = mysql_fetch_array($res)) {
                    $cpte = $cpte + 1;
                    bf_mysql_query("UPDATE ".$TablesNames[$t][0]." SET Ordre = $cpte WHERE $Chp->name = ".$row[0],0,"`".$TablesNames[$t][0]."`");
                }
            }
        }
    }
}

function Maj($MajType, $Source, $ResCible, $Exceptions = array(), $result = "") {
    if ($result == "") $result = bf_mysql_query("SELECT * FROM $Source");
    $i = 0;
    $strSource = array();
    $strCible = array();
    $strOnDuplicate = array();
    while ($i < mysql_num_fields($result)) {
        $champ = mysql_fetch_field($result, $i);
        $flags = explode(' ', mysql_field_flags($result, $i));
        if (!(in_array("auto_increment" ,$flags))) {
            $strSource[$i] = $champ->name;
            if (array_key_exists($champ->name, $Exceptions)) {
                $result1 = bf_mysql_query($Exceptions[$champ->name]);
                if (!(!($result1))) {
                    $row = mysql_fetch_row($result1);
                    $strCible[$i] = $row[0];
                } else $strCible[$i] = null;
            } else {
                if (in_array("primary_key" ,$flags)) {
                    $strCible[$i] = TrouveMax("SELECT MAX(".$champ->name.") FROM $Source") + 1 ;
                } else $strCible[$i] = $ResCible[$champ->name];
            }
            if (is_null($strCible[$i])) $strCible[$i] = 'NULL'; else $strCible[$i] = '"'.$strCible[$i].'"';
            if (!(in_array("primary_key" ,$flags))) $strOnDuplicate[$i] = $champ->name." = ".$strCible[$i];
        }
        $i++;
    }
    $strSource = implode(",", $strSource);
    $strCible = implode(",", $strCible);
    $strOnDuplicate = implode(",", $strOnDuplicate);
    if ($MajType == 1) bf_mysql_query("INSERT INTO $Source ($strSource) VALUES ($strCible) ON DUPLICATE KEY UPDATE $strOnDuplicate");
    return array($strSource, $strCible);
}

function SupprimeTables($Base, $Masque) {
    $reqtables = bf_mysql_query("SHOW TABLES FROM `$Base`");
    if (!(!$reqtables)) {
        while ($row = mysql_fetch_row($reqtables)) {
            if (substr($row[0],0,strlen($Masque)) == "trans ".session_id()) {
                bf_mysql_query("DROP TABLE `".$row[0]."`");
            }
        }
        PurgeTables();
    }
}

function EffaceTables($Base, $Masque) {
    $reqtables = bf_mysql_query("SHOW TABLES FROM `$Base`");
    if (!(!$reqtables)) {
        while ($row = mysql_fetch_row($reqtables)) {
            if (!($row[0] == "Paramweb" || $row[0] == "Connexions")) {
                if (substr($row[0],0,strlen($Masque)) != "trans ".session_id()) {
                    bf_mysql_query("DELETE FROM `".$row[0]."`");
                }
            }
        }
    }
}

function logon_submit() {
    global $Adm, $login, $password, $PHP_SELF, $ADMINLOGIN, $ADMINMDP, $ADMINREGLOGIN, $ADMINREGMDP, $LIGNES_PAR_PAGE, $COULEUR, $SON, $BDD, $UGSELNOM;
    $loginOK = false;
    $login = substr($login,0,15);
    $password = substr($password,0,15);
    if ( isset($_POST) && (!empty($_POST['login'])) && (!empty($_POST['password'])) ) {
        if ( (($login == $ADMINLOGIN) && ($password == $ADMINMDP)) || (($login == $ADMINREGLOGIN) && ($password == $ADMINREGMDP))){
            $loginOK 			= true;
            $_SESSION['login']  = "Admin";
            $_SESSION['log  ']  = $BDD;
            $_SESSION['LignesParPage']  = $LIGNES_PAR_PAGE;
            $_SESSION['Couleur'] = $COULEUR;
            $_SESSION['Son']  = $SON;
            $view = "VoirMenu";
            $Adm = true;
            bf_mysql_query("CREATE TABLE IF NOT EXISTS `Paramweb` (`Maintenance` INT DEFAULT '1' NOT NULL, `Accueil` TEXT, `BasesExternes` VARCHAR(100), `ImpressionLic` INT DEFAULT '0' NOT NULL, `InscriptionLic` INT DEFAULT '0' NOT NULL)");
            bf_mysql_query("ALTER TABLE `Paramweb` ADD `ImpressionLic` INT DEFAULT '0' NOT NULL");
            bf_mysql_query("ALTER TABLE `Paramweb` ADD `InscriptionLic` INT DEFAULT '0' NOT NULL");
            bf_mysql_query("ALTER TABLE `Paramweb` ADD `AssUgsel` VARCHAR(25) NULL");
            $req = bf_mysql_query("SELECT `Maintenance` FROM `Paramweb`");
            if ((!($req)) || ((mysql_num_rows($req)) == 0)) bf_mysql_query("INSERT INTO `Paramweb` (`Maintenance`,`Accueil`,`ImpressionLic`,`InscriptionLic`) VALUES ('1','Identification UGSEL','0','0')");
            bf_mysql_query("CREATE TABLE IF NOT EXISTS Connexions (Ip VARCHAR(15) NOT NULL, Session VARCHAR(50), Temps bigint (16) NOT NULL default '0', Id VARCHAR(15) NOT NULL, Depart TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',`Param` TEXT, PRIMARY KEY (`Session`))");
            bf_mysql_query("ALTER TABLE `Comp�titions` CHANGE `CompetCode` `CompetCode` INT(11)");
            bf_mysql_query("ALTER TABLE `Comp�titions` ADD `CompetEqu` VARCHAR(30)");
            bf_mysql_query("ALTER TABLE `Comp�titions` ADD `CompetEtat` VARCHAR(30)");
            bf_mysql_query("ALTER TABLE `Comp�titions` ADD `CompetStatut` VARCHAR(30)");
            bf_mysql_query("ALTER TABLE `Comp�titions` ADD `CompetObs` VARCHAR(50)");
            bf_mysql_query("ALTER TABLE `Comp�titions` ADD `CompetChpSup` VARCHAR(255)");
            bf_mysql_query("ALTER TABLE `Comp�titions` ADD `CompetDemLic` TEXT");
            bf_mysql_query("UPDATE `Comp�titions` SET `CompetEqu`  = '0' WHERE isnull(CompetEqu)", 0, "`Comp�titions`");
            bf_mysql_query("UPDATE `Comp�titions` SET `CompetEtat` = '0' WHERE isnull(CompetEtat)");
            bf_mysql_query("UPDATE `Comp�titions` SET `CompetStatut` = 'Inscriptions ferm�es' WHERE isnull(CompetStatut)");
            bf_mysql_query("ALTER TABLE `param�tres` ADD PRIMARY KEY (`ParVersion`, `ParSousVersion`)");
            bf_mysql_query("ALTER TABLE `Licenci�s` ADD `LicDateDem` DATE NULL");
            bf_mysql_query("ALTER TABLE `Licenci�s` ADD `LicDateValid` DATE NULL");
            bf_mysql_query("ALTER TABLE `Licenci�s` ADD INDEX `LicEtabCode` ( `LicEtabCode` )");
            bf_mysql_query("UNLOCK TABLES");
            $reqtables = bf_mysql_query("SHOW TABLES FROM `$BDD`");
            if (!(!$reqtables)) {
                while ($row = mysql_fetch_row($reqtables)) {
                    $arraytable = explode(' ', $row[0]);
                    if ($arraytable[0] == "trans") {
                        $reqtrans = bf_mysql_query('SELECT Session FROM Connexions WHERE Session = "'.$arraytable[1].'"');
                        if ((!(!($reqtrans))) && ((mysql_num_rows($reqtrans)) == 0)) {
                            bf_mysql_query("DROP TABLE `".$row[0]."`");
                        }
                    }
                }
            }

            bf_mysql_query("UPDATE Etablissements INNER JOIN Secteurs ON Etablissements.EtabSecCode = Secteurs.SecCode SET EtabMemo3 = IF(RAND() > 0.33, IF(RAND() > 0.66, CONCAT(SecLibel, LOWER(EtabNomCourt), FLOOR(RAND()*100)), CONCAT(LOWER(EtabNomCourt), SecLibel, FLOOR(RAND()*100))), CONCAT(FLOOR(RAND()*100), LOWER(EtabNomCourt), SecLibel)) WHERE EtabMemo3 = '' OR EtabMemo3 IS NULL");

            $now= time();
            $journow = date('w', $now);
            if ($journow >= 0) {
                $chemin  = ".";
                $handle  = @opendir($chemin);
                $datenow = mktime(0,0,0, date('m', $now), date('d', $now), date('Y', $now));
                $trouveauto = "non";
                while ($file = @readdir($handle)) {
                    if( (!(is_dir("$chemin/$file"))) && (strrchr($file,".") == '.ugw') && (strstr($file, 'Auto') == True) ){
                        if ((($datenow - mktime(0,0,0, date('m', filemtime("$chemin/$file")), date('d', filemtime("$chemin/$file")), date('Y', filemtime("$chemin/$file")))) / 86400) >= 7) {
                            @unlink($file);
                        } else {
                            if (strstr($file, 'Auto'.$journow) == True) $trouveauto = "oui";
                        }
                    }
                    if( (!(is_dir("$chemin/$file"))) && (strstr($file, "Temp") == True) ) {
                        if ((($datenow - mktime(0,0,0, date('m', filemtime("$chemin/$file")), date('d', filemtime("$chemin/$file")), date('Y', filemtime("$chemin/$file")))) / 86400) > 1) @unlink($file);
                    }
                }
                @closedir($handle);
            }

            OptimizeTables();

        } else {
            $req = bf_mysql_query("SELECT EtabNum, EtabNomCourt, EtabMemo3 FROM Etablissements WHERE EtabNum = ".addslashes($login));
            if ((!(!$req)) && (mysql_num_rows($req) > 0)) {
                $data = mysql_fetch_assoc($req);
                if ( (($data['EtabMemo3'] != "") && ($password == $data['EtabMemo3'])) || (($data['EtabMemo3'] == "") && ($password == $data['EtabNomCourt'])) ) {
                    $loginOK            = true;
                    $_SESSION['login']  = $data['EtabNum'];
                    $_SESSION['log  ']  = $BDD;
                    $_SESSION['LignesParPage'] = $LIGNES_PAR_PAGE;
                    $_SESSION['Couleur']= $COULEUR;
                    $_SESSION['Son']    = $SON;
                    $view               = "VoirMenu";
                    $Adm				= false;
                }
            }
        }
    }
    echo "<HTML>";
    echo "<head>";
    if ($loginOK) echo "<META HTTP-EQUIV=Refresh CONTENT='0; URL=$PHP_SELF?action=$view'>";
    else bf_stop("Echec de la connexion. <BR><BR> Verifiez vos identifiants (Utilisateur et Mot de passe).");
    echo "</head>";
    echo "</HTML>";
}

function logout($message = "") {
    Global $PHP_SELF, $HOSTNAME, $UTILISATEUR, $MDP, $BDD, $UGSELNOM, $Consult;
    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) @setcookie(session_name(),'', time() - 42000, '/');
    session_destroy();
    echo "<META HTTP-EQUIV=Refresh CONTENT='0; URL=$PHP_SELF?action=logon&message=$message'>";
    @mysql_close();
    die();
}

function logon() {
    global $PHP_SELF, $message, $Couleurs, $COULEUR, $HOSTNAME, $UTILISATEUR, $MDP, $BDD, $CONSULTATION, $UGSELNOM, $UGSELNOMDEP;
    if ($CONSULTATION == "Non") {
        $connect = @mysql_connect($HOSTNAME, $UTILISATEUR, $MDP);
        @mysql_select_db($BDD, $connect);
        $messageAccueil = "<B>UGSEL Web</B><BR><BR><BR>Bienvenue dans l'espace d'inscription aux comp�titions<BR><BR>";
        $req = @mysql_query("SELECT `Accueil` FROM `Paramweb`",$connect);
        if ((!(!$req)) && (mysql_num_rows($req) > 0)) {
            $data = mysql_fetch_assoc($req);
            $messageAccueil = urldecode($data["Accueil"]);
        }
    }
    debut_html(false);
    echo "<form method='post' name='formlogon' action='$PHP_SELF'>";
    echo "<table bgcolor='".$Couleurs[$COULEUR][4]."' bordercolor='".$Couleurs[$COULEUR][1]."' bordercolordark = '".$Couleurs[$COULEUR][1]."' bordercolorlight = '".$Couleurs[$COULEUR][1]."' border='1' cellpadding='0' cellspacing='0' width='100%' height='80%'>";
    echo " <tr>";
    echo "   <td>";
    if ($CONSULTATION == "Non") {
        echo "        <p align='center'<b>$messageAccueil</b></p>";
        echo "        <table CLASS = 'tableconopt'>";
        echo "            <tr>";
        echo "              <td width='50%' align='right'> Utilisateur &nbsp;</td>";
        echo "              <td width='50%' align='left'> <input type='text' name='login' value=''> </td>";
        echo "            </tr>";
        echo "            <tr><TD>&nbsp;</TD></tr>";
        echo "            <tr>";
        echo "              <td width='50%' align='right'> Mot de passe &nbsp;</td>";
        echo "              <td width='50%' align='left'> <input type='password' name='password' value = ''></td>";
        echo "            </tr>";
        echo "        </table>";
        echo "        <BR>";
        echo "        <p align='center'><input type='submit' name='action' value='Connexion' class='boutongrand'>";
        echo "        <p align='center'>$message";
    } else {
        echo "          <p align='center' <B>$UGSELNOM&nbsp;&nbsp;$UGSELNOMDEP</B></P>
						<BR>
						<p align='center' <B>Bienvenue dans l'espace d'inscription aux comp�titions</B></P>
						<BR><BR>
						<p align='center'<BLINK>$message</BLINK></p>
						<BR>
						<p align='center'><input type='submit' name='ENTRER' value=' Entrer ' class='boutongrand'>";
    }
    echo "   </td>";
    echo " </tr>";
    echo "</table>";
    echo " </form>";
    fin_html();
}

function TrouveParamweb($ChpRetour, $Valdef=0) {
    $ret = $Valdef;
    $req = bf_mysql_query("SELECT `$ChpRetour` FROM `Paramweb`");
    if ((!(!$req)) && (mysql_num_rows($req) > 0)) {
        $res = mysql_fetch_assoc($req);
        if (!(!$res)) $ret = $res["$ChpRetour"];
    }
    Return $ret;
}

function TrouveSport($Compet, $ChpRetour) {
    $req = bf_mysql_query("SELECT * FROM Sports INNER JOIN Comp�titions ON Sports.SpoCode = Comp�titions.CompetSpoCode WHERE CompetCode = $Compet");
    if (!(!$req)) {
        $res = mysql_fetch_array($req);
        if (!(!$res)) Return($res["$ChpRetour"]); Else Return("?");
    }
}

function CalculCat($ChpRetour, $DateNaiss, $Sexe, $Sport) {
    $req = bf_mysql_query("SELECT CatLibelCourt FROM Cat�gories WHERE (STR_TO_DATE('$DateNaiss','%Y-%m-%d') BETWEEN CatDateD�b AND CatDateFin) AND CatSexCode =".$Sexe." And CatSpoCode = ".$Sport." order by Ordre");
    $res = mysql_fetch_array($req);
    if ($res) Return($res[0]); Else Return("?");
}

function CompteEnr($matable, $maclausewhere = "") {
    $mastr = "SELECT COUNT(*) FROM `$matable`";
    if ($maclausewhere != "") $mastr = $mastr." WHERE $maclausewhere";
    $req = bf_mysql_query($mastr);
    if (!(!($req))) {
        $res = mysql_fetch_array($req);
        if ($res) Return($res[0]); Else Return("?");
    }
    else return(-1);
}

Function EcritParam($par) {
    Global $TRANSFERT_DONNEES;
    if ($TRANSFERT_DONNEES == "Url") return "par=".addslashes(urlencode($par))."&";
    if ($TRANSFERT_DONNEES == "Bdd") {
        bf_mysql_query("UPDATE Connexions SET Param = '".addslashes(urlencode($par))."' WHERE Session = '".session_id()."'");
        return "par=0&";
    }
}