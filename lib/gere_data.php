<?php

function GereData($tablename, $queryStr, $MaKey="", $NomsColonnes="", $ChampsTri="", $ChampsAli="", $ChampsFor="", $ChampsAff="", $action="GereData", $orderby="", $Choix="", $ChampsEdit="", $ChampsInsert="", $ChampsType="", $ChampsTypeExt="", $ChampsFiltre="", $where="", $ChampsNomFil="", $ChampsRacFiltre = "", $ChampsRacParam = "", $sousqueryStr, $messagedel = "", $MajChpOrdre = "", $stat = 0, $strCatEpr = "", $maxInsc = 99999 ) {
    global $Adm, $filter, $filtre1, $BFiltrer, $BAjouter, $BModifier, $tablename, $PHP_SELF, $errMsg, $page, $rowperpage, $coul, $code, $codewhere,  $order, $selection, $menu, $sousmenu, $Etab, $Compet, $Lic, $Epr, $ColNom, $Tri, $suppr, $modif, $aj, $fi, $supprtout, $ListeSport, $BValidernumlicence, $ParLicCode, $affcompet, $BSupprimerTout, $ADMINREGLOGIN;
    global $message;
    global $racnom, $racval;
    global $TAILLE, $tailleinf, $taille;
    global $exportegrille, $changeordre;
    global $dataext;
    global $imp, $exp;
    global $valideimportcompet, $valideimportsport;
    global $ListeImportCompet, $ListeImportSport;
    global $Sport, $BDD, $optionexporttype, $exporttype;
    global $optionexport;
    global $Consult;
    global $ListeImportCompetInterne, $EtabExport, $EtabImport, $horscat;
    Global $SurClass, $BAjouterSurclassement, $BModifierSurclassement ;
    Global $licence, $seltous;
    Global $selectionner;
    Global $optioninslic, $valideinslic, $reflicins;

    $MaReqErr = "";
    $TabErr   = array();
    if (isset($BAjouterSurclassement)) $BAjouter = "Inscrire";
    if (isset($BModifierSurclassement)) $BModifier = "Valider";

    if ((isset($BAjouter)) || (isset($BModifier)) || (isset($BFiltrer)) || (isset($racnom)) ) {
        $queryStr = stripslashes($queryStr);
        $pResult = bf_mysql_query($queryStr." LIMIT 0");
        $col = mysql_num_fields($pResult);
    }

    if (isset($BAjouter)) {
        $MaReq1="INSERT INTO `$tablename` (";
        $MaReq2="VALUES(";
        for( $j = 0; $j < $col; $j++ ) {
            $field = mysql_fetch_field( $pResult, $j );

            if ((isset($_POST[$field->name]) && ($ChampsInsert[$j][1])) || ($field->name == "EquRelayeurs")) {
                if ($field->name == "EquRelayeurs") {
                    $Data = addslashes(RetRelayeurs("",3));
                    if (RetRelayeurs("",4,$ChampsInsert[$j][6]) == "Erreur") $TabErr[$j] = $NomsColonnes[$j];
                } else $Data = addslashes($_POST[$field->name]);
                if (is_array($ChampsInsert[$j][4])) {
                    if ($ChampsInsert[$j][4][0] == "Max") {
                        $Data = TrouveMax("SELECT ".$ChampsInsert[$j][4][0]."(".$ChampsInsert[$j][4][1].") AS Result FROM $tablename") + 1;
                        $_POST[$field->name] = $Data;
                    }
                }
                if ($ChampsType[$j] == "Perf") {
                    $Data = str_replace(" ","",$Data);
                    $Data = str_replace(",",".",$Data);
                }
                if ($field->type == 'date') {
                    $MaErrDate = true;
                    $maDate = explode("/", $Data);
                    if (count($maDate) == 3) {
                        $jour  = sprintf('%02s',$maDate [0]);
                        $mois  = sprintf('%02s',$maDate [1]);
                        if ($maDate[2] < 50) $monPrefix = "%2002s"; Else $monPrefix = "%1902s";
                        $annee = sprintf($monPrefix,$maDate[2]);
                        if ((is_numeric($jour)) && (is_numeric($mois)) && (is_numeric($annee))) {
                            if (checkdate($mois,$jour,$annee)) {
                                if ($field->name == "LicNaissance") {
                                    $datesaisie = $annee.$mois.$jour;
                                    $dateinf  = date("19700101"); $datesup  = date("20301231");
                                    if (($datesaisie <= $datesup) && ($datesaisie >= $dateinf)) {
                                        $Data = $annee."-".$mois."-".$jour;
                                        $MaErrDate = false;
                                    }
                                } else {
                                    $Data = $annee."-".$mois."-".$jour;
                                    $MaErrDate = false;
                                }
                            }
                        }
                    }
                    if ((!($ChampsInsert[$j][6])) && ($MData == "")) {
                        $MData = "Null";
                        $MaErrDate = false;
                    }
                }
                if (($ChampsInsert[$j][0]=="ListeD") && ($ChampsInsert[$j][3][7] != "")) {
                    $req = bf_mysql_query($ChampsInsert[$j][3][7]."'$Data'");
                    if ($req) {
                        $res = mysql_fetch_array($req);
                        $Data =$res[0];
                    }
                }
                if (($ChampsInsert[$j][0]=="ListeS") && (!(array_key_exists($Data,$ChampsInsert[$j][3][2])))) {
                    $tabkeys = array_keys($ChampsInsert[$j][3][2]);
                    $Data = $tabkeys[0];
                }
                if ($ChampsInsert!= "") {
                    if ($ChampsInsert[$j][2] != "") {
                        $MaReq1 = $MaReq1."`".$ChampsInsert[$j][2]."`,";
                        $result1 = mysql_query("SELECT ". $ChampsInsert[$j][2]. " FROM $tablename LIMIT 1");
                        $field1 = mysql_fetch_field( $result1, 0);
                    } else {
                        $MaReq1 = $MaReq1."`".$field->name."`,";
                        $field1 = $field;
                    }
                }
                if ($field1->numeric) {
                    if ((empty($Data)) && ($Data != "0")) $MaReq2 = $MaReq2."'',"; else $MaReq2 = $MaReq2."$Data,";
                } else {
                    if ($Data == "Null") $MaReq2 = $MaReq2."$Data,"; else $MaReq2 = $MaReq2."'$Data',";
                }
                if ((($field1->not_null) || ($ChampsInsert[$j][6]) || ($field1->primary_key)) && ((empty($Data)) && ($Data != "0")) ){
                    $TabErr[$j] = $NomsColonnes[$j];
                }
                if (!(empty($Data))) {
                    if (($field1->numeric) && !is_numeric($Data)) {
                        $TabErr[$j] = $NomsColonnes[$j];
                    }
                    if (($field1->type == "date") && ($MaErrDate)) {
                        $TabErr[$j] = $NomsColonnes[$j];
                    }
                }
            }
        }

        if (Count($TabErr) > 0) $MaReqErr = "Impossible de valider !  Erreur sur : ". implode(", ", $TabErr).".";

        $MaReq = substr( $MaReq1, 0, strlen($MaReq1)-1 ).") ". substr( $MaReq2, 0, strlen($MaReq2)-1 ).")";

        if ($MaReqErr == "") {
            if (($menu == "competitions") && ($sousmenu == "individuels")) {
                if ((!(isset($BValidernumlicence))) || (empty($BValidernumlicence))) {
                    $MaReqErr = "Cliquez sur le bouton Ok pour rechercher le licenci�.";
                } else {
                    $reqlicstr = "SELECT LicInscrit, LicNumLicence, LicNom, LicPr�nom, LicNaissance, LicSexCode, LicAss, LicDateDem FROM Licenci�s INNER JOIN Etablissements ON Licenci�s.LicEtabCode = Etablissements.EtabCode WHERE LicNumLicence = ".$_POST["ParLicCode"];
                    if (!($Adm)) $reqlicstr .= " And (EtabNum = ".$Etab.RetAS($Etab).")";
                    $reqlic = bf_mysql_query($reqlicstr);
                    if ($reqlic) {
                        $reslic = mysql_fetch_array($reqlic);
                        if ($reslic) {
                            $reflic = sprintf('%010s', $reslic['LicNumLicence']) . " ugselweb.php" .$reslic['LicNom']." ".$reslic['LicPr�nom'];
                            if ($reslic["LicInscrit"] != 1) {
                                $MaReqErr  = "Aucune licence n'a �t� �tablie pour ".$reflic.".";
                                $optionIns = TrouveParamweb("InscriptionLic");
                                if ($optionIns == 0) {
                                    $MaReqErr .= " Contactez votre Ugsel pour �tablir la licence.";
                                } else {
                                    if ( ($optionIns == 1) && (!(is_null($reslic['LicDateDem']))) && (CompteEnr("Comp�titions", $reslic['LicNumLicence']." IN(0".TrouveSport($Compet, "CompetDemLic").")") > 0) ) {
                                        $MaReqErr .= "<BR><BR> Une demande de licence a d�j� �t� effectu�e pour ".$reslic['LicPr�nom']." ".$reslic['LicNom'].". Vous pourrez l'inscrire dans la comp�tition une fois la demande valid�e par l'Ugsel.";
                                    } else {
                                        $MaReqErr .= "<BR><BR> &nbsp; Voulez-vous demander une licence pour ".$reslic['LicPr�nom']." ".$reslic['LicNom']." ?";
                                        $MaReqErr .= "
										<BR><BR>
										<FORM method='post'>
										&nbsp;&nbsp; <input type='radio' name='optioninslic' value='0' checked='checked'> Non";
                                        if ($reslic['LicAss'] == 1) {
                                            $MaReqErr .= "&nbsp;&nbsp; <input type='radio' name='optioninslic' value='2'> Oui";
                                        } else {
                                            $trouveAss = TrouveParamweb("AssUgsel", "");
                                            $MaReqErr .= "&nbsp;&nbsp; <input type='radio' name='optioninslic' value='2'> Oui AVEC Assurance $trouveAss Ugsel
											&nbsp;&nbsp; <input type='radio' name='optioninslic' value='1'> Oui SANS Assurance $trouveAss Ugsel";
                                        }
                                        $MaReqErr .= "&nbsp;&nbsp; <input name='valideinslic' type='submit' id='valideinslic' value='Valider' class='bouton'>
										&nbsp;&nbsp; <input name='ParLicCode' type='hidden' id='ParLicCode' value='".$reslic['LicNumLicence']."'>
										&nbsp;&nbsp; <input name='reflicins' type='hidden' id='reflicins' value='".$reflic."'>									
										</FORM>";
                                    }
                                }
                            } else {
                                $resins = mysql_fetch_array(bf_mysql_query("SELECT ParCode FROM Participations WHERE ParEprCode = ".$_POST["EprLibelCourt"]." AND ParLicCode = ".$_POST["ParLicCode"]));
                                $resepr = mysql_fetch_array(bf_mysql_query("SELECT EprLibelCourt, CatDateD�b, CatDateFin, CatSexCode FROM Cat�gories INNER JOIN Epreuves ON Cat�gories.CatCode = Epreuves.EprCatCode INNER JOIN `Epreuves Comp�titions` ON Epreuves.EprCode = `Epreuves Comp�titions`.EprCompetEprCode WHERE EprCompetCode = ".$_POST["EprLibelCourt"]));
                                if ( ($Sport == 13) || ($Sport == 15) || ($Sport == 16) || ($Sport == 17) || ($Sport == 18) || ($Sport == 19)) {
                                    $resuni = mysql_fetch_array(bf_mysql_query("SELECT ParCode FROM Participations WHERE ParCompetCode = ".$Compet." AND ParLicCode = ".$_POST["ParLicCode"]));
                                    if ($resuni) $MaReqErr = "Le licenci� ".$reflic." est d�j� inscrit dans la comp�tition (vous pouvez modifier son inscription)";
                                }
                                if ($resins) {
                                    $MaReqErr = "Le licenci� ".$reflic." est d�j� inscrit dans l'�preuve ".$resepr['EprLibelCourt'];
                                } else {
                                    if ($resepr["CatSexCode"] <3) {
                                        if (stristr($resepr['EprLibelCourt'], "OPEN") === false) {
                                            if (!(($reslic["LicSexCode"] == $resepr["CatSexCode"]) && ($reslic["LicNaissance"] >= $resepr["CatDateD�b"]) && ($reslic["LicNaissance"] <= $resepr["CatDateFin"]))) {
                                                if (isset($_POST["EprLibelCourt"])) $MaReqErr = "La cat�gorie du licenci� ".$reflic." est diff�rente de celle de l'�preuve ".$resepr['EprLibelCourt'];
                                                else $MaReqErr = "Saisissez un N� de licence puis cliquez sur le bouton Ok";

                                                if ( ($Sport == 7) || ($Sport == 11) || ($Sport == 12) || ($Sport == 20) ) {
                                                    if ((($reslic["LicSexCode"] == $resepr["CatSexCode"]) && ($reslic["LicNaissance"] >= $resepr["CatDateD�b"]) && ($reslic["LicNaissance"] <= date('Y/m/d', strtotime('+1 year',strtotime($resepr["CatDateFin"])))  ))) {
                                                        if (!(isset($BAjouterSurclassement))) {
                                                            $MaReqErr .= "<BR><BR> &nbsp; < Vous pouvez cliquer sur le bouton 'SurClasser' pour inscrire ce participant (v�rifiez au pralable si le r�glement vous l'autorise) >";
                                                            $SurClass = 1;
                                                        } else {
                                                            $MaReqErr = "";
                                                            $SurClass = 0;
                                                        }
                                                    }
                                                }
                                            }
                                        } else {
                                            if (!(($reslic["LicSexCode"] == $resepr["CatSexCode"]))) {
                                                if ($MaReqErr == "") $MaReqErr = "La cat�gorie du licenci� ".$reflic." est diff�rente de celle de l'�preuve ".$resepr['EprLibelCourt'];
                                            }
                                        }
                                    }
                                }

                                $nbTot = 0; $nbInsc = 0;
                                while (isset($_POST['Epr'.$nbTot])) {
                                    if (isset($_POST['EprLibelCourt'.$_POST['Epr'.$nbTot]])) {$nbInsc++;}
                                    $nbTot++;
                                }
                                if ($nbInsc > $maxInsc) {
                                    $MaReqErr = "Vous ne pouvez pas inscrire un participant dans plus de $maxInsc �preuve";
                                    if ($maxInsc > 1) $MaReqErr .= "s";
                                    $MaReqErr .= ".";
                                } else {

                                    $l=0; $messageAj = ""; $messageSu = ""; $messageMo = "";
                                    while (isset($_POST['Epr'.$l])) {
                                        $MaReq = ""; $strPost = ""; $MaReqErrLigne = false;
                                        if ($l == 0) $MaReqErr = "";
                                        if ($_POST['Lic'.$l] != $_POST['ParLicCode']) {$MaReqErr = "Cliquez sur le bouton 'Ok' apr�s avoir modifi� le num�ro de licence."; break;}
                                        $postParQuadra = "Null"; $postEquNum = "Null"; $postParPerfQualif = "0";
                                        if ($_POST['ParQuadra'.$_POST['Epr'.$l]] > 0) $postParQuadra = "True";
                                        if ($_POST['EquNum'.$_POST['Epr'.$l]] > 0) $postEquNum = $_POST['EquNum'.$_POST['Epr'.$l]];
                                        if ($_POST['ParPerfQualif'.$_POST['Epr'.$l]] > 0){
                                            $_POST['ParPerfQualif'.$_POST['Epr'.$l]] = str_replace(" ","",$_POST['ParPerfQualif'.$_POST['Epr'.$l]]);
                                            $_POST['ParPerfQualif'.$_POST['Epr'.$l]] = str_replace(",",".",$_POST['ParPerfQualif'.$_POST['Epr'.$l]]);
                                            $postParPerfQualif = $_POST['ParPerfQualif'.$_POST['Epr'.$l]];
                                        }
                                        for ( $k = 1; $k < 6; $k++ ) {
                                            $postParObs{$k}= "Null";
                                            if (isset($_POST['ParObs'.$k.$_POST['Epr'.$l]])) {
                                                if ($_POST['ParObs'.$k.$_POST['Epr'.$l]] != "") $postParObs{$k} = "'".$_POST['ParObs'.$k.$_POST['Epr'.$l]]."'";
                                                if ( (isset($_POST['EprLibelCourt'.$_POST['Epr'.$l]])) && ( $postParObs{$k} == "Null" ) && ( $ChampsInsert[(13+$k)][6] ) ) {
                                                    $TabErr[(13+$k)] = $NomsColonnes[(13+$k)] ;
                                                    $MaReqErr = "Impossible de valider !  Erreur sur : ". implode(", ", $TabErr).".";
                                                    $MaReqErrLigne = true;
                                                }
                                            } else {
                                                if ( (isset($_POST['EprLibelCourt'.$_POST['Epr'.$l]])) && ($ChampsInsert[(13+$k)][6]) ) {
                                                    $TabErr[(13+$k)] = $NomsColonnes[(13+$k)] ;
                                                    $MaReqErr = "Impossible de valider !  Erreur sur : ". implode(", ", $TabErr).".";
                                                    $MaReqErrLigne = true;
                                                }
                                            }
                                        }
                                        if (isset($_POST['EprLibelCourt'.$_POST['Epr'.$l]])) {
                                            $strPost = "ParQuadra = ".$postParQuadra.", ParEquCode = ".$postEquNum.", ParPerfQualif = ".$postParPerfQualif.", ParObs1 = ".$postParObs{1}.", ParObs2 = ".$postParObs{2}.", ParObs3 = ".$postParObs{3}.", ParObs4 = ".$postParObs{4}.", ParObs5 = ".$postParObs{5};
                                            if ($_POST['Par'.$l] == "") {
                                                $strPost = "INSERT INTO Participations SET ParCompetCode = ".$Compet.", ParEprCode = ".$_POST['Epr'.$l].", ParLicCode = ".$_POST['Lic'.$l].", ".$strPost;
                                                if ($messageAj != "") $messageAj = $messageAj.", "; else $messageAj = " <BR> &nbsp;&nbsp; Ajout en : "; $messageAj = $messageAj.$_POST['Lib'.$l];
                                            } else {
                                                $strPost = "UPDATE Participations SET ".$strPost." WHERE ParCode = ".$_POST['Par'.$l];
                                                if ($messageMo != "") $messageMo = $messageMo.", "; else $messageMo = " <BR> &nbsp;&nbsp; Mise � jour en : "; $messageMo = $messageMo.$_POST['Lib'.$l];
                                            }
                                        } else {
                                            if ($_POST['Par'.$l] != "")	{
                                                $strPost = "DELETE FROM Participations WHERE ParCode = ".$_POST['Par'.$l];
                                                if ($messageSu != "") $messageSu = $messageSu.", "; else $messageSu = " <BR> &nbsp;&nbsp; Suppression en : "; $messageSu = $messageSu.$_POST['Lib'.$l];
                                            }
                                        }
                                        if (!($MaReqErrLigne)) bf_mysql_query($strPost);
                                        $l++;
                                    }

                                }
                            }
                        } else {
                            $MaReqErr = "Le licenci� ".$_POST["ParLicCode"]." est introuvable.";
                        }
                    } else {
                        $MaReqErr = "Le licenci� ".$_POST["ParLicCode"]." est introuvable.";
                    }
                }
            }

            if (($menu == "competitions") && ($sousmenu == "equipes") && isset($_POST["EprLibelCourt"])) {
                $reqetabcat = bf_mysql_query("SELECT EprCatCode, EprLibelCourt, SpoGestionPerf FROM Sports INNER JOIN Comp�titions ON Sports.SpoCode = Comp�titions.CompetSpoCode INNER JOIN `Epreuves Comp�titions` ON Comp�titions.CompetCode = `Epreuves Comp�titions`.EprCompetCompetCode INNER JOIN Epreuves ON `Epreuves Comp�titions`.EprCompetEprCode = Epreuves.EprCode INNER JOIN Cat�gories ON Epreuves.EprCatCode = Cat�gories.CatCode WHERE `Epreuves Comp�titions`.EprCompetCode = ".$_POST["EprLibelCourt"]);
                if (!(!($reqetabcat))) {
                    $resetabcat = mysql_fetch_array($reqetabcat);
                    if (!(!($resetabcat))) {
                        if ($resetabcat['SpoGestionPerf'] == -1) {
                            if ($resetabcat['EprCatCode'] != $_POST["CatLibelCourt"]) {
                                $rescat = mysql_fetch_array(bf_mysql_query("SELECT CatLibelCourt FROM Cat�gories WHERE CatCode = ".$_POST["CatLibelCourt"]));
                                $MaReqErr = "La cat�gorie de l'�quipe ".$rescat['CatLibelCourt'] ." est diff�rente de celle de l'�preuve ".$resetabcat['EprLibelCourt'];
                            }
                        }
                    }
                }
            }

            if ($MaReqErr == "") {
                bf_mysql_query($MaReq, 0, "`$tablename`");
                if (!($MajChpOrdre == "")) MajOrdre($MajChpOrdre);
                if ($tablename != "Participations") $aj= "";
            }

        }

        if ($MaReqErr == ""){

            if (($menu == "parametres") && ($sousmenu == "epreuves")) {
                $Res = bf_mysql_query("SELECT CompetCode FROM Comp�titions WHERE CompetSpoCode = $ListeSport");
                while ($res = mysql_fetch_array($Res)) {
                    bf_mysql_query("INSERT INTO `Epreuves Comp�titions` (`EprCompetEprCode`, `EprCompetCompetCode`) SELECT `EprCode`, ".$res['CompetCode']." AS Compet FROM `Epreuves` WHERE `EprCode` = ".$_POST['EprCode'],0 , "`Epreuves Comp�titions`");
                }
            }

            if (($menu == "competitions") && ($sousmenu == "references")) {
                $res = mysql_fetch_array(bf_mysql_query("SELECT SpoCode FROM Sports WHERE SpoLibelCourt = '".$_POST['SpoLibelCourt']."'"));
                $resepr = bf_mysql_query("SELECT `EprCode` FROM `Epreuves` WHERE `EprSpoCode` = ".$res["SpoCode"]);
                while ($res = mysql_fetch_array($resepr)) {
                    bf_mysql_query("INSERT INTO `Epreuves Comp�titions` (`EprCompetEprCode`, `EprCompetCompetCode`) VALUES(".$res["EprCode"].",".$_POST["CompetCode"].")",0 , "`Epreuves Comp�titions`");
                }
            }

            if (($menu == "competitions") && ($sousmenu == "equipes")) {
                RenumeroteEquipes($Compet);
            }

            if (($menu == "competitions") && ($sousmenu == "individuels")) {
                $message = "Inscription de ".$reflic;
                if (!(isset($_POST['Epr1']))) {
                    if ($ChampsAff[8]) $message = $message." en ".$resepr['EprLibelCourt']." effectu�e."; else $message = $message." effectu�e.";
                } else {
                    if (($messageAj != "") || ($messageMo != "") || ($messageSu != "")) $message = $message." : ".$messageAj.$messageMo.$messageSu; else $message = "";
                }
                $BValidernumlicence = '';
                $_POST['ParLicCode'] = substr(sprintf('%010s',$_POST['ParLicCode']),0,6);
                unset($selectionner);
            }

            if ($menu == "etablissements") bf_mysql_query("UPDATE Etablissements INNER JOIN Secteurs ON Etablissements.EtabSecCode = Secteurs.SecCode SET EtabMemo3 = IF(RAND() > 0.33, IF(RAND() > 0.66, CONCAT(SecLibel, LOWER(EtabNomCourt), FLOOR(RAND()*100)), CONCAT(LOWER(EtabNomCourt), SecLibel, FLOOR(RAND()*100))), CONCAT(FLOOR(RAND()*100), LOWER(EtabNomCourt), SecLibel)) WHERE EtabMemo3 = '' OR EtabMemo3 IS NULL");

            for( $j = 0; $j < $col; $j++ ) {
                $field = mysql_fetch_field( $pResult, $j );
                if (isset($_POST[$field->name]) && ($ChampsInsert[$j][7] == false)) unset($_POST[$field->name]);
            }
        }
        bf_mysql_query("UNLOCK TABLES");
    }

    if (isset($BModifier)) {
        $MaReq1="UPDATE `$tablename` SET ";
        for( $j = 0; $j < $col; $j++ ) {
            $field = mysql_fetch_field( $pResult, $j );
            if ((isset($_POST[$field->name]) && ($ChampsEdit[$j][1])) || ($field->name == "EquRelayeurs")) {
                if ($field->name == "EquRelayeurs") {
                    $MData = addslashes(RetRelayeurs("",3));
                    if (RetRelayeurs("",4,$ChampsEdit[$j][4]) == "Erreur") $TabErr[$j] = $NomsColonnes[$j];
                } else $MData = addslashes($_POST[$field->name]);
                if ($ChampsType[$j] == "Perf") {
                    $MData = str_replace(" ","",$MData);
                    $MData = str_replace(",",".",$MData);
                }
                if ($field->type == 'date') {
                    $MaErrDate = true;
                    $maDate = explode("/", $MData);
                    if (count($maDate) == 3) {
                        $jour  = sprintf('%02s',$maDate [0]);
                        $mois  = sprintf('%02s',$maDate [1]);
                        if ($maDate[2] < 50) $monPrefix = "%2002s"; Else $monPrefix = "%1902s";
                        $annee = sprintf($monPrefix,$maDate[2]);
                        if ((is_numeric($jour)) && (is_numeric($mois)) && (is_numeric($annee))) {
                            if (checkdate($mois,$jour,$annee)) {
                                if ($field->name == "LicNaissance") {
                                    $datesaisie = $annee.$mois.$jour;
                                    $dateinf = date("19700101"); $datesup  = date("20301231");
                                    if (($datesaisie <= $datesup) && ($datesaisie >= $dateinf)) {
                                        $MData = $annee."-".$mois."-".$jour;
                                        $MaErrDate = false;
                                    }
                                } else {
                                    $MData = $annee."-".$mois."-".$jour;
                                    $MaErrDate = false;
                                }
                            }
                        }
                    }
                    if ((!($ChampsEdit[$j][4])) && ($MData == "")) {
                        $MData = "Null";
                        $MaErrDate = false;
                    }
                }
                if ($ChampsEdit[$j][0] == "ListeD") {
                    if ($ChampsEdit[$j][3][7] != "") {
                        $req = bf_mysql_query($ChampsEdit[$j][3][7]."'$MData'");
                        $res = mysql_fetch_array($req);
                        $MData =$res[0];
                    }
                }
                if (($ChampsEdit[$j][0]=="ListeS") && (!(array_key_exists($MData,$ChampsEdit[$j][3][2])))) {
                    $tabkeys = array_keys($ChampsEdit[$j][3][2]);
                    $MData = $tabkeys[0];
                }
                if ($ChampsEdit != ""){
                    if (($ChampsEdit[$j][2] != "")) {
                        $MaReq1 = $MaReq1."`".$ChampsEdit[$j][2]."`=";
                        $result1 = mysql_query("SELECT ". $ChampsEdit[$j][2]. " FROM $tablename LIMIT 1");
                        $field1 = mysql_fetch_field( $result1, 0);
                    } else {
                        $MaReq1 = $MaReq1."`".$field->name."`=";
                        $field1 = $field;
                    }
                }
                if ($field1->numeric) {
                    if ((empty($MData)) && ($MData != "0")) $MaReq1 = $MaReq1."'',"; else $MaReq1 = $MaReq1."$MData,";
                } else {
                    if ($MData == "Null") $MaReq1 = $MaReq1."$MData,"; else $MaReq1 = $MaReq1."'$MData',";
                }
                if ((($field1->not_null) || ($ChampsEdit[$j][4]) || ($field1->primary_key)) && ((empty($MData)) && ($MData != "0")) ){
                    $TabErr[$j] = $NomsColonnes[$j];
                }
                if (!(empty($MData))) {
                    if (($field1->numeric) && !is_numeric($MData)) {
                        $TabErr[$j] = $NomsColonnes[$j];
                    }
                    if (($field1->type == "date") && ($MaErrDate)) {
                        $TabErr[$j] = $NomsColonnes[$j];
                    }
                }
            }
        }

        if (Count($TabErr) > 0) $MaReqErr = "Impossible de valider !  Erreur sur : ". implode(", ", $TabErr).".";

        if ($MaReqErr == "") {
            if (($menu == "competitions") && ($sousmenu == "individuels") && isset($_POST["EprLibelCourt"]) ) {
                $reslic = mysql_fetch_array(bf_mysql_query("SELECT LicNumLicence, LicNom, LicPr�nom, LicNaissance, LicSexCode FROM Licenci�s INNER JOIN Participations ON Licenci�s.LicNumLicence = Participations.ParLicCode WHERE ParCode = ".$modif));
                if ($reslic) {
                    $reflic = sprintf('%010s', $reslic['LicNumLicence']) . " ugselweb.php" .$reslic['LicNom']." ".$reslic['LicPr�nom'];
                    $resins = mysql_fetch_array(bf_mysql_query("SELECT ParCode FROM Participations WHERE ParCode <> ".$modif." AND ParEprCode = ".$_POST["EprLibelCourt"]." AND ParLicCode = ".$reslic["LicNumLicence"]));
                    $resepr = mysql_fetch_array(bf_mysql_query("SELECT EprLibelCourt, CatDateD�b, CatDateFin, CatSexCode, CatLibelCourt FROM  Cat�gories INNER JOIN Epreuves ON Cat�gories.CatCode = Epreuves.EprCatCode INNER JOIN `Epreuves Comp�titions` ON Epreuves.EprCode = `Epreuves Comp�titions`.EprCompetEprCode WHERE EprCompetCode = ".$_POST["EprLibelCourt"]));
                    if ($resins) {
                        $MaReqErr = "Le licenci� $reflic est d�j� inscrit dans l'�preuve ".$resepr['EprLibelCourt'];
                    } else {
                        if (stristr($resepr['EprLibelCourt'], "OPEN") === false) {
                            if (!(($reslic["LicSexCode"] == $resepr["CatSexCode"]) && ($reslic["LicNaissance"] >= $resepr["CatDateD�b"]) && ($reslic["LicNaissance"] <= $resepr["CatDateFin"]))) {
                                if (!(((CalculCat("", $reslic["LicNaissance"], $reslic["LicSexCode"], 1) == "PF") && ($resepr["CatLibelCourt"] == "BF")) || ((CalculCat("", $reslic["LicNaissance"], $reslic["LicSexCode"], 1) == "PG") && ($resepr["CatLibelCourt"] == "BG")))) {
                                    $MaReqErr = "La cat�gorie du licenci� $reflic est diff�rente de celle de l'�preuve ".$resepr['EprLibelCourt'];
                                }
                                if ( ($Sport == 7) || ($Sport == 11) || ($Sport == 12) || ($Sport == 20) ) {
                                    if ((($reslic["LicSexCode"] == $resepr["CatSexCode"]) && ($reslic["LicNaissance"] >= $resepr["CatDateD�b"]) && ($reslic["LicNaissance"] <= date('Y/m/d', strtotime('+1 year',strtotime($resepr["CatDateFin"])))  ))) {
                                        if (!(isset($BModifierSurclassement))) {
                                            $MaReqErr .= "<BR><BR> &nbsp; < Vous pouvez cliquer sur le bouton 'SurClasser' pour inscrire ce participant < Cliquez sur le bouton 'SurClasser' pour inscrire ce participant (v�rifiez au pralable si le r�glement l'autorise) >>";
                                            $SurClass = 1;
                                        } else {
                                            $MaReqErr = "";
                                            $SurClass = 0;
                                        }
                                    }
                                }
                            }
                        } else {
                            if (!(($reslic["LicSexCode"] == $resepr["CatSexCode"]))) {
                                $MaReqErr = "La cat�gorie du licenci� ".$reflic." est diff�rente de celle de l'�preuve ".$resepr['EprLibelCourt'];
                            }
                        }
                    }
                } else {
                    $MaReqErr = "Le licenci� ".$_POST["ParLicCode"]." est introuvable.";
                }
            }

            if (($menu == "competitions") && ($sousmenu == "equipes") && isset($_POST["EprLibelCourt"])) {
                $reqetabcat = bf_mysql_query("SELECT EprCatCode, EprLibelCourt, SpoGestionPerf FROM Sports INNER JOIN Comp�titions ON Sports.SpoCode = Comp�titions.CompetSpoCode INNER JOIN `Epreuves Comp�titions` ON Comp�titions.CompetCode = `Epreuves Comp�titions`.EprCompetCompetCode INNER JOIN Epreuves ON `Epreuves Comp�titions`.EprCompetEprCode = Epreuves.EprCode INNER JOIN Cat�gories ON Epreuves.EprCatCode = Cat�gories.CatCode WHERE `Epreuves Comp�titions`.EprCompetCode = ".$_POST["EprLibelCourt"]);
                if (!(!($reqetabcat))) {
                    $resetabcat = mysql_fetch_array($reqetabcat);
                    if (!(!($resetabcat))) {
                        if ($resetabcat['SpoGestionPerf'] == -1) {
                            if ($resetabcat['EprCatCode'] != $_POST["CatLibelCourt"]) {
                                $rescat = mysql_fetch_array(bf_mysql_query("SELECT CatLibelCourt FROM Cat�gories WHERE CatCode = ".$_POST["CatLibelCourt"]));
                                $MaReqErr = "La cat�gorie de l'�quipe ".$rescat['CatLibelCourt'] ." est diff�rente de celle de l'�preuve ".$resetabcat['EprLibelCourt'];
                            }
                        }
                    }
                }
            }

            if (($menu == "competitions") && ($sousmenu == "references")) {
                $MaCompet = $_POST["modif"];
                $ressport = mysql_fetch_array(bf_mysql_query("SELECT SpoLibelCourt FROM Sports INNER JOIN Comp�titions ON Sports.SpoCode = Comp�titions.CompetSpoCode WHERE CompetCode = ".$MaCompet));
                if ($_POST['SpoLibelCourt'] <> $ressport['SpoLibelCourt']) {
                    $respar = mysql_fetch_array(bf_mysql_query("SELECT COUNT(ParCode) AS NbPar FROM Participations WHERE ParCompetCode = ".$MaCompet));
                    $resequ = mysql_fetch_array(bf_mysql_query("SELECT COUNT(EquCode) AS NbEqu FROM Equipes WHERE EquCompetCode = ".$MaCompet));
                    if ( ($respar) && ($resequ) ) {
                        if (($respar['NbPar'] > 0) || ($resequ['NbEqu'] > 0) ) {
                            $MaReqErr = "Impossible de changer de sport car des participations existent dans la comp�tition.";
                        } else {
                            bf_mysql_query("DELETE FROM `Epreuves Comp�titions` WHERE EprCompetCompetCode = $MaCompet",0,"`Epreuves Comp�titions`");
                            $resspo = mysql_fetch_array(bf_mysql_query("SELECT SpoCode FROM Sports WHERE SpoLibelCourt = '".$_POST['SpoLibelCourt']."'"));
                            bf_mysql_query("INSERT INTO `Epreuves Comp�titions` (`EprCompetEprCode`, `EprCompetCompetCode`) SELECT `EprCode`, $MaCompet AS Compet FROM `Epreuves` WHERE `EprSpoCode` = ".$resspo["SpoCode"]);
                        }
                    }
                }

                $rescompetequ = mysql_fetch_array(bf_mysql_query("SELECT CompetEqu FROM Comp�titions WHERE CompetCode = ".$MaCompet));
                if ( ($_POST['CompetEqu'] == 0) && ($rescompetequ['CompetEqu'] == 1) ) {
                    $resequ = mysql_fetch_array(bf_mysql_query("SELECT COUNT(EquCode) AS NbEqu FROM Equipes WHERE EquCompetCode = ".$MaCompet));
                    if ( $resequ ) {
                        if ($resequ['NbEqu'] > 0) {
                            if ($MaReqErr == "") $MaReqErr = "Impossible de changer 'Equ' car des �quipes existent dans la comp�tition.";
                        }
                    }
                }

            }

        }

        if ($MaReqErr == "") {
            $MaReq = substr( $MaReq1, 0, strlen($MaReq1)-1 );
            $MaReq = $MaReq. " Where $MaKey = $modif ";
            if ($MaReqErr == "") {
                bf_mysql_query($MaReq, 0, "`$tablename`");
                if (($menu == "competitions") && ($sousmenu == "equipes")) {
                    bf_mysql_query("UPDATE Sports INNER JOIN Comp�titions ON Sports.SpoCode = Comp�titions.CompetSpoCode INNER JOIN Equipes ON Comp�titions.CompetCode = Equipes.EquCompetCode INNER JOIN `Epreuves Comp�titions` ON Equipes.EquEprCompetCode = `Epreuves Comp�titions`.EprCompetCode INNER JOIN Epreuves ON `Epreuves Comp�titions`.EprCompetEprCode = Epreuves.EprCode SET EquCatCode = EprCatCode WHERE SpoGestionPerf = -5 AND EquCompetCode = $Compet", 0, "`Equipes`");
                    RenumeroteEquipes($Compet);
                }
                if ($licence == 1) {
                    if (isset($_GET['valid'])) {
                        bf_mysql_query("UPDATE Licenci�s SET LicInscrit = TRUE, LicDateValid = CURDATE() WHERE $MaKey = $modif");
                        $message = "La demande de licence de ".$_POST["LicNom"]." ".$_POST["LicPr�nom"]." a �t� valid�e.";
                    }
                    if (!($Adm)) bf_mysql_query("UPDATE Licenci�s SET LicDateAss = CURDATE() WHERE LicAss = TRUE AND $MaKey = $modif");
                }
                if ($menu == "etablissements") bf_mysql_query("UPDATE Etablissements INNER JOIN Secteurs ON Etablissements.EtabSecCode = Secteurs.SecCode SET EtabMemo3 = IF(RAND() > 0.33, IF(RAND() > 0.66, CONCAT(SecLibel, LOWER(EtabNomCourt), FLOOR(RAND()*100)), CONCAT(LOWER(EtabNomCourt), SecLibel, FLOOR(RAND()*100))), CONCAT(FLOOR(RAND()*100), LOWER(EtabNomCourt), SecLibel)) WHERE EtabMemo3 = '' OR EtabMemo3 IS NULL");
                $modif = "";
            }
        }
        bf_mysql_query("UNLOCK TABLES");
    }

    if ($action == "deleteData") {

        $queryStrDelete = "DELETE FROM `$tablename` WHERE $MaKey = $suppr";
        $mesTables = "`$tablename`";

        if ( ($menu == "parametres") && ($sousmenu == "sports") ) {
            $mesTables = array("`Sports`", "`Participations`", "`Equipes`", "`Comp�titions`","`Epreuves Comp�titions`", "`Tours Epreuves Comp�titions`", "`Epreuves`", "`Cat�gories`", "`Groupes`");
            bf_mysql_query("DELETE `Participations` FROM Participations INNER JOIN Comp�titions ON Participations.ParCompetCode = Comp�titions.CompetCode WHERE CompetSpoCode = $suppr",0,$mesTables);
            bf_mysql_query("DELETE `Equipes` FROM Equipes INNER JOIN Comp�titions ON Equipes.EquCompetCode = Comp�titions.CompetCode WHERE CompetSpoCode = $suppr");
            bf_mysql_query("DELETE `Tours Epreuves Comp�titions` FROM Comp�titions INNER JOIN `Epreuves Comp�titions` ON Comp�titions.CompetCode = `Epreuves Comp�titions`.EprCompetCompetCode INNER JOIN `Tours Epreuves Comp�titions` ON `Epreuves Comp�titions`.EprCompetCode = `Tours Epreuves Comp�titions`.TouEprCompetEprCompetCode WHERE CompetSpoCode = $suppr");
            bf_mysql_query("DELETE `Epreuves Comp�titions` FROM `Epreuves Comp�titions` INNER JOIN Comp�titions  ON `Epreuves Comp�titions`.EprCompetCompetCode = Comp�titions.CompetCode WHERE CompetSpoCode = $suppr");
            bf_mysql_query("DELETE `Comp�titions` FROM Comp�titions WHERE CompetSpoCode = $suppr");
            bf_mysql_query("DELETE `Epreuves` FROM `Epreuves` WHERE EprSpoCode = $suppr");
            bf_mysql_query("DELETE `Cat�gories` FROM `Cat�gories` WHERE CatSpoCode = $suppr");
            bf_mysql_query("DELETE `Groupes` FROM `Groupes` WHERE GrSpoCode = $suppr");
        }

        if ( ($menu == "parametres") && ($sousmenu == "categories") ) {
            $mesTables = array("`Cat�gories`", "`Participations`", "`Equipes`", "`Epreuves Comp�titions`", "`Tours Epreuves Comp�titions`", "`Epreuves`");
            bf_mysql_query("DELETE `Participations` FROM `Epreuves` INNER JOIN `Epreuves Comp�titions` ON Epreuves.EprCode = `Epreuves Comp�titions`. EprCompetEprCode INNER JOIN Participations ON `Epreuves Comp�titions`.EprCompetCode = Participations.ParEprCode WHERE EprCatCode = $suppr",0,$mesTables);
            bf_mysql_query("DELETE `Equipes` FROM Equipes WHERE EquCatCode = $suppr");
            bf_mysql_query("DELETE `Tours Epreuves Comp�titions` FROM `Epreuves` INNER JOIN `Epreuves Comp�titions` ON Epreuves.EprCode = `Epreuves Comp�titions`. EprCompetEprCode INNER JOIN `Tours Epreuves Comp�titions` ON `Epreuves Comp�titions`.EprCompetCode = `Tours Epreuves Comp�titions`.TouEprCompetEprCompetCode WHERE EprCatCode = $suppr");
            bf_mysql_query("DELETE `Epreuves Comp�titions` FROM Epreuves INNER JOIN `Epreuves Comp�titions` ON Epreuves.EprCode = `Epreuves Comp�titions`.EprCompetEprCode WHERE EprCatCode = $suppr");
            bf_mysql_query("DELETE `Epreuves` FROM `Epreuves` WHERE EprCatCode = $suppr");
        }

        if ( ($menu == "parametres") && ($sousmenu == "epreuves") ) {
            $mesTables = array("`Epreuves`", "`Participations`", "`Equipes`", "`Epreuves Comp�titions`", "`Tours Epreuves Comp�titions`");
            bf_mysql_query("DELETE `Participations` FROM `Epreuves Comp�titions` INNER JOIN Participations ON `Epreuves Comp�titions`.EprCompetCode = Participations.ParEprCode WHERE EprCompetEprCode = $suppr",0,$mesTables);
            bf_mysql_query("DELETE `Equipes` FROM `Epreuves Comp�titions` INNER JOIN Equipes ON `Epreuves Comp�titions`.EprCompetCode = Equipes.EquEprCompetCode WHERE EprCompetEprCode = $suppr");
            bf_mysql_query("DELETE `Tours Epreuves Comp�titions` FROM `Epreuves Comp�titions` INNER JOIN `Tours Epreuves Comp�titions` ON `Epreuves Comp�titions`.EprCompetCode = `Tours Epreuves Comp�titions`.TouEprCompetEprCompetCode WHERE EprCompetEprCode = $suppr");
            bf_mysql_query("DELETE `Epreuves Comp�titions` FROM `Epreuves Comp�titions` WHERE EprCompetEprCode = $suppr");
        }

        if ( ($menu == "etablissements") ) {
            $mesTables = array("`Etablissements`", "`Participations`", "`Equipes`", "`Licenci�s`");
            bf_mysql_query("DELETE Participations FROM Participations INNER JOIN Licenci�s ON Participations.ParLicCode = Licenci�s.LicNumLicence WHERE LicEtabCode = $suppr",0,$mesTables);
            bf_mysql_query("DELETE Equipes FROM Equipes WHERE EquEtabCode = $suppr");
            bf_mysql_query("DELETE Licenci�s FROM Licenci�s INNER JOIN Etablissements ON Licenci�s.LicEtabCode = Etablissements.EtabCode WHERE LicEtabCode = $suppr");
        }

        if ( ($menu == "licencies") && (!($licence == 1)) ) {
            $mesTables = array("`Licenci�s`", "`Participations`");
            bf_mysql_query("DELETE Participations FROM Participations INNER JOIN Licenci�s ON Participations.ParLicCode = Licenci�s.LicNumLicence WHERE LicCode = $suppr");
        }

        if ( ($menu == "competitions") && ($sousmenu == "references") ) {
            $mesTables = array("`Comp�titions`", "`Participations`", "`Equipes`", "`Epreuves Comp�titions`", "`Tours Epreuves Comp�titions`");
            bf_mysql_query("DELETE `Epreuves Comp�titions` FROM `Epreuves Comp�titions` WHERE EprCompetCompetCode = $suppr",0,$mesTables);
            bf_mysql_query("DELETE Participations FROM Participations WHERE ParCompetCode = $suppr");
            bf_mysql_query("DELETE Equipes FROM Equipes WHERE EquCompetCode = $suppr");
            bf_mysql_query("DELETE `Tours Epreuves Comp�titions` FROM `Tours Epreuves Comp�titions` WHERE EprCompetCompetCode = $suppr");
        }

        if ($licence == 1) {
            $queryStrDelete = "";
            $nbDem = 0;
            $req = bf_mysql_query("SELECT COUNT(*) FROM Comp�titions WHERE CompetDemLic LIKE '%".$_GET['Lic']."%'");
            if ( (!(!($req))) && ($menu == "competitions") ) {
                $res = mysql_fetch_array($req);
                if ($res) $nbDem = $res[0];
            }
            if ($nbDem == 0) bf_mysql_query("UPDATE Licenci�s SET LicInscrit = FALSE, LicAss = FALSE, LicNomAss = NULL, LicDateAss = NULL, LicDateDem = NULL, LicDateValid = NULL WHERE $MaKey = $suppr");
            $message = "La demande de licence de ".$_GET["LicNom"]." ".$_GET["LicPr�nom"]." a �t� annul�e.";
        }

        bf_mysql_query($queryStrDelete);
        if (!($MajChpOrdre == "")) MajOrdre($MajChpOrdre);

        if (($menu == "competitions") && ($sousmenu == "equipes")) {RenumeroteEquipes($Compet);}

        bf_mysql_query("UNLOCK TABLES");
        $action = "VoirMenu";
    }

    if (($action == "monter") || ($action == "descendre")) {
        $WhereCompOrdre = "";
        if ($tablename == "Cat�gories") $WhereCompOrdre = "AND CatSpoCode = $ListeSport";
        if ($tablename == "Epreuves")   $WhereCompOrdre = "AND EprSpoCode = $ListeSport";
        $ResOrdre = mysql_fetch_array(bf_mysql_query("SELECT $MaKey, Ordre From $tablename Where $MaKey = $changeordre"));
        if ($ResOrdre) {
            if( $action == "descendre" ) {
                $ResMax = mysql_fetch_array(bf_mysql_query("SELECT MAX(Ordre) AS Max FROM $tablename WHERE Ordre < ".$ResOrdre["Ordre"]." $WhereCompOrdre ORDER BY Ordre"));
                $ResPrec = mysql_fetch_array(bf_mysql_query("SELECT $MaKey, Ordre From $tablename Where Ordre = ".$ResMax["Max"]));
                if ($ResPrec) {
                    $ResChange = bf_mysql_query("UPDATE $tablename SET Ordre = ".$ResPrec["Ordre"] ." WHERE $MaKey = ".$ResOrdre["$MaKey"],0 , "`$tablename`");
                    $ResChange = bf_mysql_query("UPDATE $tablename SET Ordre = ".$ResOrdre["Ordre"] ." WHERE $MaKey = ".$ResPrec["$MaKey"]);
                }
            } else {
                $ResMin = mysql_fetch_array(bf_mysql_query("SELECT MIN(Ordre) AS Min FROM $tablename WHERE Ordre > ".$ResOrdre["Ordre"]." $WhereCompOrdre ORDER BY Ordre"));
                $ResSuiv = mysql_fetch_array(bf_mysql_query("SELECT $MaKey, Ordre From $tablename Where Ordre = ".$ResMin["Min"]));
                if ($ResSuiv) {
                    $ResChange = bf_mysql_query("UPDATE $tablename SET Ordre = ".$ResSuiv["Ordre"] ." WHERE $MaKey = ".$ResOrdre["$MaKey"],0 , "`$tablename`");
                    $ResChange = bf_mysql_query("UPDATE $tablename SET Ordre = ".$ResOrdre["Ordre"]." WHERE $MaKey = ".$ResSuiv["$MaKey"]);
                }
            }
        }
        $action = "VoirMenu";
        bf_mysql_query("UNLOCK TABLES");
    }

    if ((isset($BValidernumlicence)) && (!(empty($BValidernumlicence))) && (!(empty($_POST["ParLicCode"])))) {
        $monsport = 1;
        $reqsport = bf_mysql_query("SELECT CompetSpoCode FROM Comp�titions WHERE CompetCode = $Compet");
        if ($reqsport) {
            $ressport = mysql_fetch_array($reqsport);
            if ($ressport) $monsport = $ressport["CompetSpoCode"];
        }
        $reqlicstr = "SELECT Secteurs.*, Etablissements.*, Licenci�s.*, CatLibelCourt FROM Cat�gories, Licenci�s INNER JOIN Etablissements On Licenci�s.LicEtabCode = Etablissements.EtabCode INNER JOIN Secteurs ON Etablissements.EtabSecCode = Secteurs.SecCode WHERE (Licenci�s.LicNaissance Between CatDateD�b And CatDateFin) And LicSexCode = CatSexCode And CatSpoCode = "."1"." And LicNumLicence = ".$_POST["ParLicCode"];
        if (!($Adm)) $reqlicstr .= " And (EtabNum = ".$Etab.RetAS($Etab).")";

        $reqlic = bf_mysql_query($reqlicstr);

        if (!(!($reqlic))) {
            $reslic = mysql_fetch_array($reqlic);
            if (!($reslic)) {
                $baseext = bf_mysql_query("SELECT `BasesExternes` FROM `Paramweb`");
                if ((!(!$baseext)) && (mysql_num_rows($baseext) > 0)) {
                    $dataext = mysql_fetch_assoc($baseext);
                    $tabdataext = explode(";", $dataext["BasesExternes"]);
                    for ($i = 0; $i < count($tabdataext); $i++) {
                        $ficext = "../$tabdataext[$i]/inscriptions/bzh.php";
                        if (file_exists($ficext)) {
                            $reslic = mysql_fetch_array(bf_mysql_query($reqlicstr, 0, "", TrouveDansFic($ficext,"BDD"), TrouveDansFic($ficext,"UTILISATEUR"),TrouveDansFic($ficext,"MDP")));
                            if (!(!($reslic))) {
                                Maj(1,"Secteurs", $reslic);
                                Maj(1,"Etablissements", $reslic, Array("EtabSecCode" => "SELECT SecCode From Secteurs WHERE SecLibel = ".$reslic["SecLibel"]));
                                Maj(1,"Licenci�s", $reslic, Array("LicEtabCode" => "SELECT EtabCode From Etablissements WHERE EtabNum = ".$reslic["EtabNum"]));
                                break;
                            }
                        }
                    }
                }
            }
        }
        if ( (!($reqlic)) || ((!(!($reqlic))) && (!($reslic)))) $MaReqErr = "Le licenci� ".$_POST["ParLicCode"]." est introuvable.";
    }

    if ( ($menu == "competitions") && ($sousmenu == "individuels") ) {
        if (  ((isset($BValidernumlicence)) && (!(empty($BValidernumlicence))) && (empty($_POST["ParLicCode"]))) || ((!(isset($message))) && (isset($BAjouter)) && ( (empty($BValidernumlicence)) || (empty($_POST["ParLicCode"])) || (strlen($_POST["ParLicCode"]) < 9)))) {
            $MaReqErr = "Saisissez un N� de licence puis cliquez sur le bouton Ok";
        }
    }
    if ((isset($BFiltrer)) || (isset($racnom))) {
        $MaReq1="";

        if (isset($BFiltrer)) {
            if (isset($filtre1[$racnom])) unset ($filtre1[$racnom]);
            if (isset($racnom)) unset($racnon);
            if (isset($racval)) unset ($racval);
        }

        for( $j = 0; $j < $col; $j++ ) {
            $field = mysql_fetch_field( $pResult, $j );
            if (isset($_POST["filtre".$field->name])) {
            } else {
                if ( (isset($racnom)) && ($racnom == $field->name) ) {
                    $_POST["filtre".$field->name] = $racval;
                }
                if (is_array($filtre1)) {
                    if (array_key_exists($field->name, $filtre1)) {
                        if ( (isset($racnom)) && ($racnom == $field->name) ) {
                            if (empty($filtre1[$field->name])) {
                                $_POST["filtre".$field->name] = $filtre1[$field->name];
                            } else {
                                unset($filtre1[$field->name]);
                                $_POST["filtre".$field->name] = "";
                            }
                        } else {
                            $_POST["filtre".$field->name] = $filtre1[$field->name];
                        }
                    }
                }
            }

            if (isset($_POST["filtre".$field->name]) && ($ChampsFiltre[$j]) && ($_POST["filtre".$field->name] != "")) {
                $Data = $_POST["filtre".$field->name];
                $filtre1["$field->name"] = $Data;
                if ($field->type == 'date') {
                    $jour=substr($Data,0,2);
                    $mois=substr($Data,3,2);
                    $annee=substr($Data,-4);
                    if ((strlen($Data) == 10) && (is_numeric($jour)) && (is_numeric($mois)) && (is_numeric($annee))) {
                        if (checkdate($mois,$jour,$annee)) {
                            $Data = $annee."-".$mois."-".$jour;
                            $MaErrDate = false;
                        }
                    }
                }
                if ($ChampsType[$j] == 'ListeS') {
                    if (array_search($Data, $ChampsTypeExt[$j]) !== false) {
                        $MonArray = Array_Keys($ChampsTypeExt[$j], $Data);
                        $Data = $MonArray[0];
                    }
                }
                if ($ChampsNomFil[$j] != "") $monchamp = $ChampsNomFil[$j]; else $monchamp = "`".$field->name."`";
                if (($field->type == 'numeric') || ($field->type == 'real')) $MaReq1 = $MaReq1."(".$monchamp." = '$Data') AND "; else {
                    $moncrit = ' LIKE "'.$Data.'"';
                    if (strtolower($Data) == "vide") $moncrit = ' IS NULL';
                    if (strtolower($Data) == "pas vide") $moncrit = ' IS NOT NULL';
                    $MaReq1 = $MaReq1.'('.$monchamp.$moncrit.' ) AND ';
                }
            }
        }

        $filter = substr($MaReq1, 0, strlen($MaReq1) - 4);
        $page=1;

    }

    if ($where != "") {
        $queryStr = $queryStr." WHERE ".$where;
        if ($filter != "")  {
            $queryUnion = explode('UNION',$queryStr);
            if (count($queryUnion) == 1) {
                $queryStr = $queryStr." AND $filter";
            } else {
                $queryStr = "";
                for( $f = 0; $f < count($queryUnion); $f++ ) {
                    $queryStr .= $queryUnion[$f]." HAVING $filter";
                    if ($f < (count($queryUnion)) - 1) $queryStr .= " UNION ";
                }
            }
        }
    } else if ($filter != "") {
        $queryStr = $queryStr." WHERE $filter";
    }

    if ($orderby != "") $queryStr = $queryStr." ORDER BY ".$orderby;

    if (isset($BSupprimerTout)) {
        if ($menu == "competitions") {
            if ($sousmenu == "individuels") {
                bf_mysql_query("DELETE FROM Participations WHERE ParCompetCode = $Compet");
            }
            if ($sousmenu == "equipes") {
                bf_mysql_query("DELETE FROM Equipes WHERE EquCompetCode = $Compet");
                bf_mysql_query("UPDATE Participations SET ParEquCode = NULL WHERE ParCompetCode = $Compet");
            }
            $supprtout = false;
        }
    }

    if (isset($valideinslic)) {
        if ($optioninslic > 0) {
            $message = " Une demande de licence";
            $trouveAss = TrouveParamweb("AssUgsel", "");
            if ($optioninslic == 1) {
                $messageAss.= " SANS assurance $trouveAss Ugsel";
            } else {
                $messageAss.= " AVEC assurance $trouveAss Ugsel";
                bf_mysql_query("UPDATE Licenci�s SET LicAss = 1 WHERE LicNumLicence = $ParLicCode");
                bf_mysql_query("UPDATE Licenci�s SET LicDateAss = CURDATE() WHERE LicNumLicence = $ParLicCode AND LicDateAss IS NULL");
            }
            bf_mysql_query("UPDATE Comp�titions SET CompetDemLic = IF(CompetDemLic IS NULL,',".$ParLicCode."',CONCAT(CompetDemLic,',".$ParLicCode."')) WHERE CompetCode = ".$Compet." AND NOT '".$ParLicCode."' IN(0".TrouveSport($Compet, "CompetDemLic").")");
            $optionIns = TrouveParamweb("InscriptionLic");
            if ($optionIns == 1) {
                bf_mysql_query("UPDATE Licenci�s SET LicDateDem = CURDATE() WHERE LicNumLicence = $ParLicCode AND LicDateDem IS NULL");
                $message.= $messageAss." a �t� effectu�e pour $reflicins. Vous pourrez l'inscrire dans la comp�tition une fois la demande valid�e par l'Ugsel.";
            }
            if ($optionIns == 2) {
                bf_mysql_query("UPDATE Licenci�s SET LicInscrit = TRUE WHERE LicNumLicence = $ParLicCode");
                bf_mysql_query("UPDATE Licenci�s SET LicDateDem = CURDATE() WHERE LicNumLicence = $ParLicCode AND LicDateDem IS NULL");
                bf_mysql_query("UPDATE Licenci�s SET LicDateValid = CURDATE() WHERE LicNumLicence = $ParLicCode AND LicDateValid IS NULL");
                $message.= $messageAss." a �t� effectu�e pour $reflicins. Vous pouvez maintenant l'inscrire dans la comp�tition.";
            }
            $BValidernumlicence = "Ok";
        }
    };

    if ($MaReqErr != "") {
        echo "<TABLE CLASS='tablemessageerreur'> <TR> <TD> <BLINK><B> Attention ! </B></BLINK> $MaReqErr </TD> </TR> </TABLE>";
        JoueSon('sonpb.wav');
    }
    if (($messagedel != "") && ($action == "confirmedeletedata") ) echo "<TABLE CLASS='tablemessageerreur'> <TR> <TD> $messagedel </TD> </TR> </TABLE>";
    if ($message != "") {
        if(stristr($message, 'Erreur') === FALSE) {
            echo "<TABLE CLASS='tablemessage'> <TR> <TD> $message </TD> </TR> </TABLE>";
            JoueSon('sonok.wav');
        } else {
            echo "<TABLE CLASS='tablemessageerreur'> <TR> <TD> $message </TD> </TR> </TABLE>";
            JoueSon('sonpb.wav');
        }
    }

    if (($Adm) && (($action == "importer"))&& ($imp == true)) {

        if (!(  (($menu == "parametres") || ($menu == "competitions") || ($menu == "outils") )&& (isset($_POST['upload']) && (($_FILES['userfile']['size'] > 0) || ($ListeImportCompet != "") || ($ListeImportCompetInterne != "") )))) {

            echo "<form method='post' enctype='multipart/form-data'>";
            echo "<table CLASS = 'tableopt'>";
            echo "<TR>";
            echo "<TD>";

            if (($menu == "competitions") && ($sousmenu == "references")) {
                $req = bf_mysql_query("SELECT SpoLibelCourt FROM Sports WHERE SpoCode = $Sport");
                if (!(!$req)) {
                    $res = mysql_fetch_array($req);
                    if (!(!$res)) $MonSportLibel = $res["SpoLibelCourt"];
                }
                $req = bf_mysql_query("SELECT CompetCode, CompetLibell�, DATE_FORMAT(CompetDateD�b,'%d/%m/%Y') AS CompetDateD�b, CompetLieu, SpoLibelCourt FROM `Sports` INNER JOIN `Comp�titions` ON `Sports`.SpoCode = `Comp�titions`.CompetSpoCode WHERE CompetCode <> ". $Compet ." AND SpoLibelCourt = '".$MonSportLibel."'");
                if ((!(!$req)) && (mysql_num_rows($req) > 0)) {
                    echo " Importer la comp�tition ";
                    listederoulante("ListeImportCompetInterne", "Comp�tition...", "SELECT CompetCode, CompetLibell�, DATE_FORMAT(CompetDateD�b,'%d/%m/%Y') AS CompetDateD�b, CompetLieu, SpoLibelCourt FROM `Sports` INNER JOIN `Comp�titions` ON `Sports`.SpoCode = `Comp�titions`.CompetSpoCode WHERE CompetCode <> ". $Compet ." AND SpoLibelCourt = '".$MonSportLibel."'", array("SpoLibelCourt","-","CompetLibell�","-","CompetDateD�b","-","CompetLieu"), array("","","","","","",""),"CompetCode" , 0,  350);
                    echo " &nbsp; Ou &nbsp; <BR><BR>";
                }
                $tabImport = RetourneFic(".", "Comp�tition","Comp", "");
                $listeImport = array();
                if (count($tabImport) > 0) {
                    for( $i = 0; $i < count($tabImport); $i++ ) {
                        if ( ($MonSportLibel) == ($tabImport[$i]["Sport"]) ) {
                            $listeImport[$i] = $tabImport[$i]["R�sum�"];
                            $listeImportCl�[$i] = $tabImport[$i]["Nom"];
                        }
                    }
                }
                if (count($listeImport) > 0) {
                    echo "Importer du serveur une comp�tition en attente ";
                    listederoulante("ListeImportCompet", "Fichiers...", $listeImport, "", "", $listeImportCl�, $ListeImportCompet, 230);
                    echo " &nbsp; Ou &nbsp; <BR><BR>";
                }
            }

            echo " Importer le fichier ";
            echo "<input type='hidden' name='MAX_FILE_SIZE' value='50000000'>";
            echo "<input name='userfile' type='file' id='userfile' >&nbsp;";
            echo "<input name='upload' type='submit' id='upload' value='Importer' class='bouton'>";
            if (($menu == "etablissements") || ($menu == "licencies")) echo " <B>&nbsp; Attention ! Cette op�ration peut durer plusieurs minutes...</B>";
            echo "</TD>";
            echo "</TR>";
            echo "</TABLE>";
            echo "</FORM>";
        }
    }

    $pResult = bf_mysql_query($queryStr);

    if (!$pResult) {
        echo "<TABLE CLASS='tablemessageerreur'> <TR> <TD> Impossible de lire les donn�es pour l'instant.</TD> </TR> </TABLE> <BR>";
    } else {

        $row = mysql_num_rows( $pResult );
        $col = mysql_num_fields( $pResult );

        echo "<form name='formaffichelignes' id='formaffichelignes' action='$PHP_SELF' method=post>\n";

        ConstruitZone(array(array("menu",$menu),array("sousmenu",$sousmenu),array("action",$action)));
        ConstruitZone(array(array("tablename",$tablename),array("MaKey",$MaKey),array("orderby",$orderby),array("filter",$filter)));
        ConstruitZone(array(array("Compet",$Compet),array("ListeSport",$ListeSport)));
        ConstruitZone(array(array("modif",$modif),array("suppr",$suppr),array("supprtout",$supprtout)));
        ConstruitZone(array(array("fi",$fi),array("aj",$aj)));
        ConstruitZone(array(array("BValidernumlicence",$BValidernumlicence)));
        ConstruitZone(array(array("affcompet",$affcompet)));
        ConstruitZone(array(array("licence",$licence)));
        ConstruitZone(array(array("selectionner",$selectionner)));

        $montableau = array(
            "menu" => $menu, "sousmenu" => $sousmenu,"action" => $action,
            "tablename" => $tablename,"MaKey" => $MaKey, "orderby" => $orderby, "filter" => $filter,
            "ListeSport" => $ListeSport,
            "modif" => $modif, "suppr" => $suppr,"supprtout" => $supprtout,
            "fi" => $fi, "aj" => $aj,
            "affcompet" => $affcompet,
            "page" => $page,"filtre1" => $filtre1,
            "stat" => $stat,
            "horscat" => $horscat,
            "licence" => $licence,
            "selectionner" => $selectionner
        );
        if (isset($Compet)) $montableau["Compet"] = $Compet;
        $par = EcritParam(serialize($montableau));
        $alea = Rand(1,9999);

        if ($rowperpage == "") $rowperpage = $_SESSION['LignesParPage'];
        if (!($Adm)) $rowperpage = 999999;
        if (!(isset($page))) $page = 0; else $page--;
        if (is_int($row/$rowperpage)) $max = $row/$rowperpage; else $max = (int)($row/$rowperpage) + 1;
        if ($page * $rowperpage >= $row) $page = $max - 1;
        if ($row == 0) $nbaffiche = 0; else if ($page < $max - 1) $nbaffiche = $rowperpage; else $nbaffiche = $row - ($page * $rowperpage);
        if ($row > 0) mysql_data_seek( $pResult, $page * $rowperpage );

        if ($selection == 0) {
            $selecteur = "<TABLE CLASS ='tableselecteur'><TR><TD>";
            if ($Adm) {
                if( $page > 0 ) {
                    $selecteur .= "<a Class='navig' href='$PHP_SELF?".$par."action=VoirMenu&modif=&suppr=&page=1&Etab=$Etab'><< </a>";
                    $selecteur .=  "<a Class='navig' href='$PHP_SELF?".$par."action=VoirMenu&modif=&suppr=&page=$page&Etab=$Etab'>< </a>";
                } else $selecteur .=  "<span class='pasimprimer'> << < </span>";
                $selecteur .=  " Page ".($page+1)."/".$max;
                if( $page < $max-1) {
                    $selecteur .=  "<a Class='navig' href='$PHP_SELF?".$par."action=VoirMenu&modif=&suppr=&page=".($page+2)."&Etab=$Etab'> ></a>";
                    $selecteur .=  "<a Class='navig' href='$PHP_SELF?".$par."action=VoirMenu&modif=&suppr=&page=$max&Etab=$Etab'> >></a>";
                } else $selecteur .=  "<span class='pasimprimer'> > >> </span>";
            }
            if ($filter != "") $tfiltre = "(filtre actif)"; else $tfiltre = "";
            if ($nbaffiche > 1) $s="s"; else $s="";
            $selecteur .= "&nbsp;&nbsp;$nbaffiche ligne$s affich�e$s";
            if ($Adm) $selecteur .= " sur un total de $row";
            $selecteur .= "  $tfiltre";
            if ( ($Adm) && ($menu == "licencies") && ($horscat == 0) && ($stat == 0) && ($fi == 0)) {
                $pResulttot = bf_mysql_query("SELECT * FROM Licenci�s");
                $rowtot = mysql_num_rows($pResulttot);
                if ($row != $rowtot) $selecteur .=  "&nbsp; &nbsp; <BLINK> Attention ! </BLINK><a href='$PHP_SELF?".$par."action=GereData&horscat=1&aj=0'>Afficher la liste des licenci�s Hors Cat�gories</a>\n";
            }
            $selecteur .=  "</TD></TR></TABLE>\n";
        }

        echo $selecteur;

        echo "<table CLASS = 'tablecompets'>\n";

        echo "<tr>\n";
        for( $i = 0; $i < $col; $i++ ) {
            $field = mysql_fetch_field( $pResult, $i );
            if ($NomsColonnes !== "") $ColNom = $NomsColonnes[$i]; else $ColNom = $field->name;
            if ($ChampsTri !== "")  {
                $Tri = $field->name;
                if ($ChampsTri[$i] != "") $Tri = $ChampsTri[$i];
                if ($ChampsTri[$i] == "/") $Tri = "";
            }
            if (($ChampsAff !== "") && ($ChampsAff[$i])){
                echo "<th";
                if ($fi) echo " CLASS = 'thfiltre' ";
                echo ">";
                if (($Tri != "") && ($row > 1)) echo "<a href='$PHP_SELF?".$par."action=VoirMenu&orderby=$Tri&Etab=$Etab&sousmenu=$sousmenu&Compet=$Compet'>".$ColNom."</a>"; else echo $ColNom;
                echo "</th>\n";
            }
        }

        if ($sousqueryStr != "") {
            echo "<th";
            if ($fi) echo " CLASS = 'thfiltre' ";
            echo ">";
            echo $sousqueryStr[1];
            echo "</th>\n";
        }

        if ($Choix != "") {

            echo "<TH CLASS = '";
            if ($fi) echo "thdercolfiltre"; else echo "thdercol";
            echo "'>";

            if ( (in_array("ajout",$Choix)) || (in_array("filtrage",$Choix)) || (in_array("exporter",$Choix)) || ((in_array("suppressiontout",$Choix)) && ($row > 0)) || (in_array("consultation",$Choix)) || ((in_array("stat",$Choix)) && ($row > 0)) || ((in_array("licence",$Choix)) && ($row > 0)) ) {

                if ( (in_array("licence",$Choix)) && ($licence == 1)  ) {

                    $req = bf_mysql_query("SELECT `ImpressionLic` FROM `Paramweb`");
                    if ((!(!$req)) && (mysql_num_rows($req) > 0)) {$data = mysql_fetch_assoc($req); $data = $data["ImpressionLic"];} else $data = 0;

                    if ( ((!($Adm))&&($data == '1')) || ((($Adm))&&($data > 0)) ) echo "<input name='exporter' type='submit' id='exporter' value='Imprimer Licence(s)' class='bouton'> ";

                    echo "<input type='hidden' name='optionexporttype' value='exppdf' checked='checked'>";
                    if ((in_array("filtrage",$Choix)) && ($supprtout==false) && ($row > 0) ) {
                        if ($fi==true) {
                            $invfi=false;
                            $annulefiltre="&filter=&filtre1=";
                        } else {
                            $invfi=true;
                            $annulefiltre="";
                        }
                        echo "<a href='$PHP_SELF?".$par."action=filtredata&fi=$invfi".$annulefiltre."'>Filtrage</a>\n";
                    }

                    echo "<a href='$PHP_SELF?".$par."action=GereData&licence=0&aj=0";
                    if ($menu == "competitions") echo "&sousmenu=individuels&fi=0&filter=&filtre1=&orderby=";
                    echo "'>Fermer</a>\n";

                    if ( ((!($Adm))&&($data == '1')) || ((($Adm))&&($data > 0)) ) {
                        echo "<BR>S�lection : ";
                        echo " <a href='$PHP_SELF?".$par."action=GereData&licence=1&aj=0&seltous=1'>Tous</a>\n";
                        echo "/";
                        echo " <a href='$PHP_SELF?".$par."action=GereData&licence=1&aj=0&selaucun=1'>Aucun</a>\n";
                    }

                } else {

                    if (in_array("ajout",$Choix)  && ($supprtout==false) ) {
                        if ($aj==true) $invaj=false; else $invaj=true;
                        echo "<a href='$PHP_SELF?".$par."action=ajoutedata&aj=$invaj";
                        if (!($Adm)) {;echo "&fi=&filter=";}
                        echo "'>";
                        if (($menu == "competitions") && (($sousmenu == "individuels") || ($sousmenu == "equipes"))) {
                            if ($aj == false) echo "<BLINK>";
                            echo "<B>Inscription</B>";
                            if ($aj == false) echo "</BLINK>";
                        } else echo "Ajout";
                        echo "</a>\n";
                    }

                    if ( (in_array("licence",$Choix)) && (!($licence == 1)) && ($supprtout==false) && ( ($row > 0) || ($Adm) ) ) {
                        echo " <a href='$PHP_SELF?".$par."action=GereData&licence=1&aj=0";
                        if ($menu == "competitions") echo "&fi=0&filter=&filtre1=&orderby=";
                        echo "'>Licences</a>\n";
                    }

                    if ((in_array("suppressiontout",$Choix)) && ($row > 0) ) {
                        if ($supprtout==true) $invsupprtout=false; else $invsupprtout=true;
                        if ($supprtout==false) {
                            echo "<a href='$PHP_SELF?".$par."action=supprdatatout&aj=&supprtout=$invsupprtout&filter=&filtre1=&fi='>Suppression</a>\n";
                        } else {
                            echo "<INPUT TYPE='submit' NAME='BSupprimerTout' VALUE='Supprimer";
                            echo " tout !";
                            echo "' class='boutongrand'>&nbsp;";
                            echo "<a href='$PHP_SELF?".$par."action=VoirMenu&supprtout='>&nbsp;Annuler&nbsp;</a>";
                        }
                    }

                    if ((in_array("filtrage",$Choix)) && ($supprtout==false) && ($row > 0) ) {
                        if ($fi==true) {
                            $invfi=false;
                            $annulefiltre="&filter=&filtre1=";
                        } else {
                            $invfi=true;
                            $annulefiltre="";
                        }
                        echo "<a href='$PHP_SELF?".$par."action=filtredata&fi=$invfi".$annulefiltre."'>Filtrage</a>\n";
                    }

                    if ((in_array("importer",$Choix)) && ($menu != "parametres") && ($menu != "competitions")) {
                        if ($imp == true) $invimp =false; else 	$invimp =true;
                        echo "<a href='$PHP_SELF?".$par."action=importer&imp=$invimp'>Import</a>\n";
                    }

                    if (($stat >= 1) && ($menu == "competitions") && ($sousmenu == "individuels")) {
                        echo " <a "; if ($stat == 1) echo "CLASS = 'inv'"; echo "href='$PHP_SELF?".$par."action=GereData&stat=1'>Licence</a>\n"." / ";
                        echo " <a "; if ($stat == 2) echo "CLASS = 'inv'" ; echo "href='$PHP_SELF?".$par."action=GereData&stat=2'>Sport</a>\n";
                    }

                    if (in_array("exporter",$Choix) && ($supprtout==false) && ($row > 0) && (($menu == "etablissements") || ($menu == "licencies") || (($menu == "competitions") && ( ($sousmenu != "references") || ($stat == 1) )))) {
                        if ($exp == True) $invexp = False; else $invexp = True;
                        if ($menu == "etablissements") {echo "<a href='$PHP_SELF?".$par."action=exporter&exp=$invexp&exporttype=$exporttype&optionexport=expetab'>Export</a>";}
                        if ($menu == "licencies") 	   {echo "<a href='$PHP_SELF?".$par."action=exporter&exp=$invexp&exporttype=$exporttype&optionexport=explic'>Export</a>";}
                        if ($menu == "competitions")   {echo "<a href='$PHP_SELF?".$par."action=exporter&exp=$invexp&exporttype=$exporttype&optionexport=expcompet&Compet=$Compet'>Export</a> \n";}
                    }

                    if ((in_array("stat",$Choix)) && ($supprtout==false) && ($fi == false) && ($row > 0) ){
                        if ($stat == 0) echo " <a href='$PHP_SELF?".$par."action=GereData&stat=1&aj=0&fi=0'>Stat</a>\n"; else echo " <a href='$PHP_SELF?".$par."stat=0'>Liste</a>\n";
                    }

                    if (in_array("liste",$Choix)) {
                        echo " <a href='$PHP_SELF?".$par."action=GereData&stat=0&horscat=0&page=1'>Fermer</a>\n";
                    }

                    if (($menu == "competitions") && ($supprtout==false) && ($stat == 0) && ($row > 0) && ((($sousmenu == "individuels") && ($ChampsAff[8])) || ($sousmenu == "individuels(2)")) ) {
                        echo "<a "; if ($sousmenu =="individuels")    echo "CLASS = 'inv'"; echo "href='$PHP_SELF?".$par."action=VoirMenu&menu=competitions&sousmenu=individuels&Etab=$Etab&Compet=$Compet'    > &nbsp;1 </a>";
                        echo "/";
                        echo "<a "; if ($sousmenu =="individuels(2)") echo "CLASS = 'inv'"; echo "href='$PHP_SELF?".$par."action=VoirMenu&menu=competitions&sousmenu=individuels(2)&Etab=$Etab&Compet=$Compet&aj=0' > 2&nbsp</a>";
                    }

                }
            }

            echo "</TH>\n";
            echo "</tr>\n";

        }

        if ( (!($Adm)) && ($aj) && ($menu == "competitions") ) {

            if ($sousmenu == "individuels")  {
                echo "<TR><TD COLSPAN='$col'><SPAN CLASS='pasimprimer'> 
			&nbsp;&nbsp;Pour inscrire un participant : Saisissez son <a CLASS = 'inv'; href='$PHP_SELF?action=VoirMenu&menu=licencies&sousmenu=$sousmenu&Compet=$Compet&licence=0&affcompet=$affcompet&selectionner=true';>&nbsp;n� de Licence&nbsp;</a> puis cliquez sur 'Ok'. &nbsp;Compl�tez si besoin les autres champs (Epreuve...) et cliquez sur 'Inscrire'.<BR>";
                if ($ChampsAff[12])
                    echo "<BLINK><B>&nbsp;&nbsp; -> Attention ! </B></BLINK> Si le participant appartient � une �quipe qui ne figure pas dans la liste 'Equipe...', proc�dez au pr�alable � l'
			<a CLASS = 'inv'; href='$PHP_SELF?action=VoirMenu&menu=competitions&sousmenu=equipes&Compet=$Compet&affcompet=$affcompet'>&nbsp;inscription d'une �quipe&nbsp;</a>
			</SPAN></TD></TR>";
            }
            if ($sousmenu == "equipes")  {
                echo "<TR><TD COLSPAN='$col'><SPAN CLASS='pasimprimer'>
			&nbsp;&nbsp;Remarque : La num�rotation des relais est automatique et les num�ros d�j� attribu�s peuvent �tre modifi�s suite � l'inscription d'un nouveau relais. &nbsp;
			<BR> &nbsp; Pour les relayeurs, saisissez les num�ros de Licence.";
                echo "</SPAN></TD></TR>";
            }
        }

        for( $i = -2; $i < $rowperpage; $i++ ) {
            if ($i>=0) {

                $rowArray = mysql_fetch_array( $pResult );
                if( $rowArray == false ) break;
            }

            $key = "";
            for( $j = 0; $j < $col; $j++ ) {
                $field = mysql_fetch_field( $pResult, $j );
                if ($i>=0) {
                    $data = $rowArray[$j];
                    if ($MaKey != "" && $field->name == $MaKey) $key = $data;
                }
            }

            if (($i >= 0 ) || (($i == -2) && (($Choix != "") && in_array("filtrage",$Choix)) && ($fi == true)) || (($i == -1) && (($Choix != "") && in_array("ajout",$Choix)) && ($aj == true))) {
                echo "<TR CLASS = '";
                if ($i == -1) echo "tredit";
                if ($i >=  0) {
                    if ( (($action == "modifiedata") && ($key == $modif))
                        || ( ($menu == "parametres") && ($action == "importer") && ($key == $Sport)  && ($imp == True) )
                        || ( ($menu == "competitions") && ($action == "importer") && ($key == $Compet)  && ($imp == True) )
                        || ( ($menu == "etablissements") && ($action == "importer") && ($key == $EtabImport)  && ($imp == True) )
                        || ( ($menu == "parametres") && ($action == "exporter") && ($key == $Sport)  && ($exp == True) )
                        || ( ($menu == "competitions") && ($action == "exporter") && ($key == $Compet)  && ($exp == True) )
                        || ( ($menu == "etablissements") && ($action == "exporter") && ($key == $EtabExport)  && ($exp == True) &&($stat == 0) )
                    )
                    {
                        echo "trimpexp";
                    } else {
                        if ((($action == "confirmedeletedata") && ($key == $suppr)) || ($supprtout == true) ) {
                            echo "trsuppr";
                        } else {
                            if ($rowArray[0] == "�") echo "trtotal"; else {
                                if ($rowArray[0] == "��") echo "trimpexp"; else {
                                    if ($rowArray[0] == "���") echo "trsuppr"; else {
                                        if (($menu == "licencies") && (isset($selectionner)) && ($selectionner == true)) {
                                            if ( (round($i / 2) - ($i / 2)) == "0" ) echo "tr1' onmouseover='this.className=\"trsel\"' onmouseout='this.className=\"tr1\""; else echo "tr2' onmouseover='this.className=\"trsel\"' onmouseout='this.className=\"tr2\"";
                                        } else {
                                            if ( (round($i / 2) - ($i / 2)) == "0" ) echo "tr1"; else echo "tr2";
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                echo "'>\n";
            }

            for( $j = 0; $j < $col; $j++ ) {

                $field = mysql_fetch_field( $pResult, $j );

                if ($i >= 0) {
                    $data = $rowArray[$j];

                    if ($sousqueryStr != "") {
                        if ($field->name == $sousqueryStr[2]) $sousqueryStrATrouver = $data;
                    }

                    if ( (strlen( $data ) > 255 ) && ($field->name != 'EquRelayeurs') )$data = substr( $data, 0, 255 ) . "...";
                    $data = htmlspecialchars($data);
                    if ($field->type == "date") $data = eregi_replace("([0-9].*)-([0-9].*)-([0-9].*)" ,"\\3/\\2/\\1",$data);
                    if (($ChampsType[$j] == "ListeS") && (array_key_exists($data,$ChampsTypeExt[$j])) && ($data != "")) {
                        $data = $ChampsTypeExt[$j][$data];
                    }

                }

                $testinsert = false;
                $testinsert = is_array($ChampsInsert);
                if ($testinsert) $testinsert = $ChampsInsert[$j][1];

                if ((($ChampsAff!="") && ($ChampsAff[$j]==true)) || (($ChampsAff!="") && ($ChampsAff[$j]==false) && ($testinsert==true) && ($aj==true)) ) {
                    if ($ChampsAli!=='') if ($ChampsAli[$j]=='') $Ali = 'Left'; else $Ali = $ChampsAli[$j];
                    if ($ChampsFor!=='') if ($ChampsFor[$j]=='') $Fom = '%s'  ; else $Fom = $ChampsFor[$j];
                    if (($i == -2) && ($fi == true)) {
                        if (($ChampsAff[$j] == true) && ($ChampsFiltre[$j] == true)) {

                            $browser = "";
                            if (ereg("MSIE", $_SERVER["HTTP_USER_AGENT"])) $browser = "ie";
                            echo "<td Align = Center ";
                            if ($browser == "") echo " WIDTH = '30px' ";
                            echo "><input type='text' maxlength = '255' name='filtre$field->name' style='border-width : 0px; width:";
                            if ($browser == "") echo "100"; else echo "95";
                            echo "%; text-align:";
                            if ($ChampsAli[$j] == '') echo 'Center'; else echo $ChampsAli[$j];
                            echo";'";

                            $maval="";
                            if (is_array($filtre1)) { if (array_key_exists($field->name, $filtre1)) $maval = $filtre1[$field->name];}
                            if (isset($_POST["filtre".$field->name])) $maval = $_POST["filtre".$field->name];
                            if ($maval != "") echo " value = ".'"'.$maval.'"'."";

                            echo "</td>\n";
                        } else if ($ChampsAff[$j] == true) echo "<td></td>";
                    };

                    if (($i >= 0) && ($action == "modifiedata") && ($key == $modif))  {
                        if ($ChampsEdit[$j][1]==true) {
                            if (isset($_POST["$field->name"]))  $data = $_POST["$field->name"];
                            if (($ChampsEdit[$j][0]=="Texte")  || ($ChampsEdit[$j][0]=="Date")) {

                                if ($field->name == 'EquRelayeurs') {
                                    $arraydata = explode("-",RetRelayeurs($data,1));
                                    $numre = 1;
                                    echo "<td>";
                                    for( $re = 0; $re < 4; $re++ ) {
                                        if (isset($_POST["EquRelayeurs".$re])) {
                                            $monre = $_POST["EquRelayeurs".$re];
                                        } else {
                                            if ($re < count($arraydata)) $monre = htmlspecialchars(trim($arraydata[$re])); else $monre = "";
                                        }
                                        echo $numre++.". <input type='text' maxlength='255' name='".$field->name.$re."' value='".$monre."' style='border-width : 1px; text-align:".$ChampsAli[$j]."; width:85%';>";
                                        echo "<BR>&nbsp;&nbsp;".RetRelayeurs($monre,0);
                                        if ($re < 3) echo "<BR>";
                                    }
                                } else {
                                    if (($ChampsType[$j] == "Perf") && ($data == 0))  $data = "";
                                    echo "<td><input type='text' maxlength='255' name='$field->name' value= ".'"'.htmlspecialchars($data).'"'." style='border-width : 0px; text-align:".$ChampsAli[$j]."; ";
                                    if (($menu == "competitions") && ($sousmenu == "individuels") && ($field->name == 'ParLicCode') ) {
                                        echo "width:80';>"."&nbsp;"."<input type='button' name='BValidernumlicence' value='Ok' class ='boutonpetit'>";
                                    } else {
                                        echo "width:100";
                                        if (($row > 0) || ($data != "")) echo "%"; else echo "px";
                                        echo ";'>";
                                    }
                                }
                                echo "</td>\n";
                            }
                            if (($ChampsEdit[$j][0]=="ListeD") || ($ChampsEdit[$j][0]=="ListeS")) {
                                echo "<td>";
                                if (($ChampsEdit[$j][0]=="ListeD") && ($ChampsEdit[$j][3][6] != "") && (!(isset($BModifier))) ){
                                    $req = bf_mysql_query($ChampsEdit[$j][3][6]."'$data'");
                                    $res = mysql_fetch_array($req);
                                    $data =$res[0];
                                }
                                if (($ChampsEdit[$j][0]=="ListeS") && (isset($BModifier))) {
                                    $TabFlip = array_flip($ChampsEdit[$j][3][2]);
                                    $data = array_search("$data",$TabFlip);
                                }
                                listederoulante($field->name,$ChampsEdit[$j][3][1],$ChampsEdit[$j][3][2],$ChampsEdit[$j][3][3],$ChampsEdit[$j][3][4],$ChampsEdit[$j][3][5], $data, $ChampsEdit[$j][3][8]);
                                echo "</td>";
                            }
                        }
                        else echo "<td align=$Ali>".sprintf($Fom, $data)."</td>\n";

                    } else if (($i == -1) && ($aj == true)) {

                        if ($ChampsInsert[$j][1] == true) {
                            if ($ChampsAff[$j] != false)  echo "<td style ='white-space:nowrap;'>";
                            if ((($ChampsInsert[$j][0]=="ListeD") || ($ChampsInsert[$j][0]=="ListeS")) && ($ChampsInsert[$j][5] == true)) {
                                if (isset($_POST["$field->name"])) $data = $_POST["$field->name"]; else $data="";
                                if (($ChampsEdit[$j][0]=="ListeS") && (isset($BAjouter))) {
                                    $TabFlip = array_flip($ChampsInsert[$j][3][2]);
                                    if (!empty($data)) {
                                        $data = array_search("$data",$TabFlip);
                                    } else {
                                        $TabKeys = array_keys($TabFlip);
                                    }
                                }
                                if (($menu == "competitions") && ($sousmenu == "individuels") && ($field->name == "EprLibelCourt") && (isset($_POST["ParLicCode"]))) {
                                    $reqlicstrinit = "SELECT LicNaissance, LicSexCode FROM Licenci�s INNER JOIN Etablissements ON Licenci�s.LicEtabCode = Etablissements.EtabCode WHERE LicNumLicence = ".$_POST["ParLicCode"];
                                    if (!($Adm)) $reqlicstrinit .= " And (EtabNum = ".$Etab.RetAS($Etab).")";
                                    $reqlicinit = bf_mysql_query($reqlicstrinit);
                                    if (!(!($reqlicinit))) {
                                        $reslicinit = mysql_fetch_array($reqlicinit);
                                        if (!(!($reslicinit))) {
                                            $reqeprinit = bf_mysql_query("SELECT EprCompetCode
																	  FROM Epreuves INNER JOIN Cat�gories ON Cat�gories.CatCode = Epreuves.EprCatCode INNER JOIN `Epreuves Comp�titions` ON Epreuves.EprCode = `Epreuves Comp�titions`.EprCompetEprCode LEFT JOIN Groupes ON Groupes.GrCode = Epreuves.EprGrCode
																	  WHERE (Groupes.GrLibelCourt < 9 OR Groupes.GrLibelCourt IS NULL) AND `Epreuves Comp�titions`.EprCompetCompetCode = ".$Compet." AND (Cat�gories.CatSexCode = ".$reslicinit["LicSexCode"]." AND Cat�gories.CatDateD�b <= Date('".$reslicinit["LicNaissance"]."') AND Cat�gories.CatDateFin >= Date('".$reslicinit["LicNaissance"]."')) ORDER BY Epreuves.Ordre LIMIT 1");
                                            if (!(!($reqeprinit))) {
                                                $reseprinit = mysql_fetch_array($reqeprinit);
                                                if ($MaReqErr == "") {if (!(!($reseprinit))) $data = $reseprinit["EprCompetCode"];} else $data = $_POST["EprLibelCourt"];
                                            }
                                            $reqeprinitBIS = bf_mysql_query("SELECT EprCompetCode, EprLibelCourt 
																	     FROM Epreuves INNER JOIN Cat�gories ON Cat�gories.CatCode = Epreuves.EprCatCode INNER JOIN `Epreuves Comp�titions` ON Epreuves.EprCode = `Epreuves Comp�titions`.EprCompetEprCode LEFT JOIN Groupes ON Groupes.GrCode = Epreuves.EprGrCode
																	     WHERE Cat�gories.CatPrim = TRUE $strCatEpr AND (Groupes.GrLibelCourt < 9 OR Groupes.GrLibelCourt IS NULL) AND `Epreuves Comp�titions`.EprCompetCompetCode = ".$Compet." AND (Cat�gories.CatSexCode = ".$reslicinit["LicSexCode"]." AND ( (Cat�gories.CatDateD�b <= Date('".$reslicinit["LicNaissance"]."') AND Cat�gories.CatDateFin >= Date('".$reslicinit["LicNaissance"]."')) OR (EprLibelCourt LIKE '%Open%') )) 
																		 ORDER BY Epreuves.Ordre");
                                            $nbLignesEpr = 0;
                                            $nbLignesEpr = mysql_num_rows($reqeprinitBIS);
                                            if ($nbLignesEpr > 1) $testNbLignesEpr = true;
                                            $reqeprinitTER = bf_mysql_query("SELECT EprCompetCode, EprLibelCourt 
																	     FROM Epreuves INNER JOIN Cat�gories ON Cat�gories.CatCode = Epreuves.EprCatCode INNER JOIN `Epreuves Comp�titions` ON Epreuves.EprCode = `Epreuves Comp�titions`.EprCompetEprCode LEFT JOIN Groupes ON Groupes.GrCode = Epreuves.EprGrCode
																	     WHERE Cat�gories.CatPrim = TRUE $strCatEpr AND (Groupes.GrLibelCourt < 9 OR Groupes.GrLibelCourt IS NULL) AND `Epreuves Comp�titions`.EprCompetCompetCode = ".$Compet." AND (Cat�gories.CatSexCode = ".$reslicinit["LicSexCode"]." AND ( (Cat�gories.CatDateD�b <= Date('".$reslicinit["LicNaissance"]."') AND Cat�gories.CatDateFin >= Date('".$reslicinit["LicNaissance"]."')) )) 
																		 ORDER BY Epreuves.Ordre");
                                            $nbLignesEprSansOpen = 0;
                                            $nbLignesEprSansOpen = mysql_num_rows($reqeprinitTER);
                                            if ($nbLignesEprSansOpen == 0) {
                                                $catInitBIS = CalculCat("", $reslicinit["LicNaissance"], $reslicinit["LicSexCode"], 1);
                                                if (($catInitBIS == "PF") || ($catInitBIS == "PG")) {
                                                    $reqeprinitBIS = bf_mysql_query("SELECT EprCompetCode, EprLibelCourt 
																				 FROM Epreuves INNER JOIN Cat�gories ON Cat�gories.CatCode = Epreuves.EprCatCode INNER JOIN `Epreuves Comp�titions` ON Epreuves.EprCode = `Epreuves Comp�titions`.EprCompetEprCode LEFT JOIN Groupes ON Groupes.GrCode = Epreuves.EprGrCode
																				 WHERE Cat�gories.CatPrim = TRUE AND (Groupes.GrLibelCourt < 9 OR Groupes.GrLibelCourt IS NULL) AND `Epreuves Comp�titions`.EprCompetCompetCode = ".$Compet." AND Cat�gories.CatSexCode = ".$reslicinit["LicSexCode"]." AND (Cat�gories.CatLibelCourt ='BF' OR Cat�gories.CatLibelCourt ='BG' OR EprLibelCourt LIKE '%Open%' OR EprLibelCourt LIKE '%Col %')
																				 ORDER BY Epreuves.Ordre");

                                                    $nbLignesEpr = mysql_num_rows($reqeprinitBIS);
                                                    if ($nbLignesEpr > 1) $testNbLignesEpr = true;
                                                }
                                            }
                                        }
                                    }
                                }
                                if (!(isset($testNbLignesEpr))) {
                                    if ($ChampsAff[$j] == true) listederoulante($field->name,$ChampsInsert[$j][3][1],$ChampsInsert[$j][3][2],$ChampsInsert[$j][3][3],$ChampsInsert[$j][3][4],$ChampsInsert[$j][3][5], $data, $ChampsInsert[$j][3][8]);
                                }
                            } else {
                                if ($field->name == 'EquRelayeurs') {
                                    $numre = 1;
                                    for( $re = 0; $re < 4; $re++ ) {
                                        if (isset($_POST["EquRelayeurs".$re])) {
                                            $maval = $_POST["EquRelayeurs".$re];
                                        } else {
                                            $maval = "";
                                        }
                                        echo $numre++.". <input type='text' maxlength='255' name='"."EquRelayeurs".$re."' value='".$maval."' style='border-width : 1px; text-align:".$ChampsAli[$j]."; width:85%';>";
                                        if (RetRelayeurs($maval,0) != "") echo "<BR> &nbsp; ";
                                        echo RetRelayeurs($maval,0);
                                        if ($re < 3) echo "<BR>";
                                    }
                                } else {

                                    if (!(isset($testNbLignesEpr))) {

                                        $montxt = "";
                                        echo "<input ";
                                        if (($ChampsAff[$j] == false) || ($ChampsInsert[$j][5] == false)) echo "Type = 'Hidden' ";
                                        if (($ChampsInsert != "") && ($ChampsInsert[$j][4] != "")) {
                                            echo "style='text-align:".$ChampsAli[$j].";' value = '";
                                            if (!(is_array($ChampsInsert[$j][4]))) $montxt = $ChampsInsert[$j][4]; else $montxt = "Max";
                                            echo "$montxt' ";
                                        }
                                        if (isset($_POST["$field->name"])) $maval = $_POST["$field->name"];
                                        else {if (($field->name == "ParLicCode") && (isset($Etab)) && ($Etab > 0)) $maval = sprintf('%06s',$Etab); else $maval = "";}
                                        echo "type='text' maxlength='255' name='$field->name' value='$maval' style='border-width:0px;";
                                        if (($menu == "competitions") && ($sousmenu == "individuels") && ($field->name == 'ParLicCode') ) {
                                            echo "width:".(70 + $TAILLE * 7)."px; text-align:center; '>"."&nbsp;";
                                            echo" <a CLASS = 'inv' href='$PHP_SELF?action=VoirMenu&menu=licencies&sousmenu=$sousmenu&Compet=$Compet&licence=0&affcompet=$affcompet&selectionner=true'>&nbsp;?&nbsp;</a>\n";
                                            echo "<input type='submit' name='BValidernumlicence' value='Ok' class ='boutonpetit'>";
                                        } else {
                                            echo "width:100";
                                            if (($row > 0) || ($montxt != "")) echo "%"; else echo "px";
                                            echo ";'>";
                                        }
                                        echo "\n";

                                    }

                                }
                            }
                            if ($ChampsAff[$j] != false) echo "</td>";
                        }
                        else {
                            echo "<td align=$Ali>";

                            if ((isset($BValidernumlicence)) && (!(empty($BValidernumlicence)) )) {
                                $monsport = 1;
                                $reqsport = bf_mysql_query("SELECT CompetSpoCode FROM Comp�titions WHERE CompetCode = $Compet");
                                if ($reqsport) {
                                    $ressport = mysql_fetch_array($reqsport);
                                    if ($ressport) $monsport = $ressport["CompetSpoCode"];
                                }
                                $reqlicstr = "SELECT EtabNum, EtabNomCourt, LicEtabCode, LicInscrit, LicNumLicence, LicNom, LicPr�nom, LicSexCode, LicNaissance, LicDateEnr, LicDateLic, LicAss, LicNomAss, LicObs, CatLibelCourt FROM Cat�gories, Licenci�s INNER JOIN Etablissements On Licenci�s.LicEtabCode = Etablissements.EtabCode WHERE (Licenci�s.LicNaissance Between CatDateD�b And CatDateFin) And LicSexCode = CatSexCode And CatSpoCode = 1 And LicNumLicence = ".$_POST["ParLicCode"];
                                if (!($Adm)) $reqlicstr .= " And (EtabNum = ".$Etab.RetAS($Etab).")";
                                $reqlic = bf_mysql_query($reqlicstr);

                                if (!(!($reqlic))) {
                                    $reslic = mysql_fetch_array($reqlic);
                                    if ($reslic) {
                                        if ($field->name == "LicNaissance") echo eregi_replace("([0-9].*)-([0-9].*)-([0-9].*)" ,"\\3/\\2/\\1",$reslic["$field->name"]);
                                        else if ($field->name == "LicSexCode") { if ($reslic["$field->name"] == 1) echo "G"; else echo "F";}
                                        else echo $reslic["$field->name"];
                                    } else echo " ? ";
                                } else echo " ? ";
                            }

                            echo "</td>\n";
                        }
                    } else if (($i>=0) && ($ChampsAff[$j] == true)) {
                        echo "<td align=$Ali";
                        if (strlen($data) < 30) echo " NOWRAP";
                        echo ">";
                        if ($ChampsRacFiltre == "") {
                            if ($data != "" ) echo sprintf($Fom, $data);
                        } else {
                            if (($ChampsRacFiltre[$j] == true)  && (!($Consult)) ){
                                $MaVal = sprintf($Fom,$data);
                                if ( (isset($racnom)) && (isset($racval)) && ($racnom == $field->name) && ($racval == sprintf($Fom,$data)) && ($data != "") ) $MaVal = "";
                                echo "<a href='$PHP_SELF?".$par."action=VoirMenu&fi=1&racnom=".$field->name."&racval=".$MaVal."'>".sprintf($Fom,$data)."</a>";
                            } else {
                                if ($field->name == 'EquRelayeurs') $data = str_replace(" - ","<BR>",$data);

                                if ($field->name == 'CompetLibell�') {
                                    $SpoG = TrouveSport($rowArray[0], "SpoGestionPerf") * -1;
                                    $SpoL = $rowArray["SpoLibelCourt"];
                                    if ($SpoG == 99) $SpoG = 10;
                                    if (is_int(strpos($SpoL,"Bad")) || is_int(strpos($SpoL,"TTable"))) $SpoG = 7;
                                    if (is_int(strpos($SpoL,"Judo")) || is_int(strpos($SpoL,"Escri"))|| is_int(strpos($SpoL,"Combat"))) $SpoG = 8;
                                    if (is_int(strpos($SpoL,"Vtt")) || is_int(strpos($SpoL,"CO"))|| is_int(strpos($SpoL,"Esca"))|| is_int(strpos($SpoL,"Raid"))) $SpoG = 9;
                                    $tabCoul = array("green","darkorange","yellow","darkorange","darkorange","Cyan","darkorange","magenta","black","green","white");
                                    echo " <b style='background:".$tabCoul[$SpoG]."'>&nbsp; &nbsp; </b>&nbsp;";
                                }

                                if ($ChampsType[$j] == "Perf") {
                                    if ($data != 0) echo FormatPerf($data);
                                } else {
                                    echo sprintf($Fom,$data);
                                }
                            }
                            echo "</td>\n";
                        }
                    }
                }
            }

            $mapage = $page + 1;

            if (($sousqueryStr != "") ) {
                if ($i >= 0) {
                    $Resu = bf_mysql_query($sousqueryStr[0].$sousqueryStrATrouver);
                    $Tt ="";
                    while ($resu = mysql_fetch_array($Resu)) {
                        for ($ii = 0; $ii < count($sousqueryStr[3]); $ii++) {
                            $Tt1 = "";
                            if ($sousqueryStr[3][$ii][1] != "") {
                                if ($sousqueryStr[3][$ii][1] == "==") {if ($resu[$sousqueryStr[3][$ii][0]] == $sousqueryStr[3][$ii][2]) $Tt1 = $sousqueryStr[3][$ii][3];}
                                if ($sousqueryStr[3][$ii][1] == "!=") {if ($resu[$sousqueryStr[3][$ii][0]] != $sousqueryStr[3][$ii][2]) $Tt1 = $sousqueryStr[3][$ii][3];}
                                if ($sousqueryStr[3][$ii][1] == ">" ) {if ($resu[$sousqueryStr[3][$ii][0]] >  $sousqueryStr[3][$ii][2]) { if ($sousqueryStr[3][$ii][4]) $Tt1 = $resu[$sousqueryStr[3][$ii][3]]; else $Tt1 = $sousqueryStr[3][$ii][3];}}
                                if ($sousqueryStr[3][$ii][1] == "<" ) {if ($resu[$sousqueryStr[3][$ii][0]] <  $sousqueryStr[3][$ii][2]) $Tt1 = $sousqueryStr[3][$ii][3];}
                            } else {
                                $Tt1 = $resu[$sousqueryStr[3][$ii][0]];
                            }
                            if ($Tt1 != "") {
                                if ($Tt != "") $Tt .= " ";
                                $Tt .= $Tt1;
                            }
                        }
                        $Tt .= " / ";
                    }
                    $Tt = substr($Tt, 0, strlen($Tt) - 3);
                    echo "<TD>".$Tt."</TD>";
                }
            }

            if ($Choix != "") {

                if ((($i == -2) && in_array("filtrage",$Choix) && ($fi==true)) || (($i == -1) && in_array("ajout",$Choix) && ($aj==true))) echo "<TD CLASS = 'tddercol'>\n";

                if (($i == -2) && in_array("filtrage",$Choix) && ($fi==true)) {
                    echo "<INPUT TYPE='submit' NAME='BFiltrer' VALUE='Valider' class='boutonmoyen'>&nbsp;\n";
                    echo "<a href='$PHP_SELF?".$par."filter=&filtre1=&action=VoirMenu'>&nbsp;Effacer&nbsp;</a>\n";
                    echo "<a href='$PHP_SELF?".$par."filter=&filtre1=&fi=&action=VoirMenu'>&nbsp;Annuler&nbsp;</a>\n";
                }
                if (($i == -1) && in_array("ajout",$Choix) && ($aj==true)) {
                    echo "<INPUT TYPE='submit' NAME='BAjouter' VALUE='";
                    if ( ($menu == "competitions") && (($sousmenu == "individuels") || ($sousmenu == "equipes"))) echo "Inscrire"; else echo "Valider";
                    echo "' class='bouton'>&nbsp;\n";
                    if ($SurClass == 1)	echo " &nbsp;"."<input type='submit' name='BAjouterSurclassement' value='SurClasser' class ='boutonpetit'> ";
                    echo "<a href='$PHP_SELF?".$par."action=VoirMenu&aj='>&nbsp;Annuler&nbsp;</a>\n";
                }

                if ((($i == -2) && in_array("filtrage",$Choix) && ($fi==true)) || (($i == -1) && in_array("ajout",$Choix) && ($aj==true))) echo "</TD>";

                if ($i >= 0) {
                    if (($action == "modifiedata") && ($key == $modif)) {
                        echo "<TD CLASS = 'tddercol'>\n";
                        echo "<INPUT TYPE='submit' NAME='BModifier' VALUE='Valider' class='bouton'>&nbsp;\n";
                        if ($SurClass == 1)	echo " &nbsp;"."<input type='submit' name='BModifierSurclassement' value='SurClasser' class ='boutonpetit'> ";
                        echo "<a href='$PHP_SELF?".$par."action=VoirMenu&modif='>&nbsp;Annuler&nbsp;</a>\n";
                        echo "</TD>";
                    }
                    else {
                        if (($action == "confirmedeletedata") && ($key == $suppr)) {
                            echo "<TD CLASS = 'tddercol'>\n";
                            echo "<a href='$PHP_SELF?".$par."action=deleteData&suppr=$key&LicNom=".$rowArray['LicNom']."&LicPr�nom=".$rowArray['LicPr�nom']."&Lic=".$rowArray['LicNumLicence']."'>&nbsp;Supprimer&nbsp;</a>\n";
                            echo "<a href='$PHP_SELF?".$par."action=VoirMenu'>&nbsp;Annuler&nbsp;</a>\n";
                            echo "</TD>\n";
                        } else {
                            if (count ($Choix) > 0) {
                                echo "<TD CLASS = 'tddercol'>\n";
                                if (in_array("monter"   ,$Choix)) {if ((($i > 0) || ( ($i == 0) && ($page > 0))) && ($row > 1 ) && ($filter == "") && (in_array("descendre",$Choix))) echo "<a href='$PHP_SELF?".$par."action=descendre&changeordre=$key&aj='>&nbsp;-&nbsp;</a>"; else echo "<a>&nbsp;-&nbsp;</a>";}
                                if (in_array("descendre",$Choix)) {if ((($page*$rowperpage+$i) < ($row - 1)) && ($row > 1 ) && ($filter == "") && (in_array("monter",$Choix))) echo "<a href='$PHP_SELF?".$par."action=monter&changeordre=$key&aj='>&nbsp;+&nbsp;</a>"; else echo "<a>&nbsp;+&nbsp;</a>";}
                                $protect = false; if (($field->name == "SpoGestionPerf") && ($data != -99)) $protect = true;

                                if ((in_array("modifier" ,$Choix)) && (!($licence == 1)) ) {
                                    if (!($protect)) {
                                        $testNbLignesEpr = false;
                                        if (($menu == "competitions") && ($sousmenu == "individuels")) {
                                            $reqlicstrinit = "SELECT LicNaissance, LicSexCode FROM Licenci�s INNER JOIN Etablissements ON Licenci�s.LicEtabCode = Etablissements.EtabCode WHERE LicNumLicence = ".$rowArray[2];
                                            if (!($Adm)) $reqlicstrinit .= " And (EtabNum = ".$Etab.RetAS($Etab).")";
                                            $reqlicinit = bf_mysql_query($reqlicstrinit);
                                            if (!(!($reqlicinit))) {
                                                $reslicinit = mysql_fetch_array($reqlicinit);
                                                if (!(!($reslicinit))) {
                                                    $reqeprinitBIS = bf_mysql_query("SELECT EprCompetCode, EprLibelCourt 
																	     FROM Epreuves INNER JOIN Cat�gories ON Cat�gories.CatCode = Epreuves.EprCatCode INNER JOIN `Epreuves Comp�titions` ON Epreuves.EprCode = `Epreuves Comp�titions`.EprCompetEprCode LEFT JOIN Groupes ON Groupes.GrCode = Epreuves.EprGrCode
																	     WHERE Cat�gories.CatPrim = TRUE AND (Groupes.GrLibelCourt < 9 OR Groupes.GrLibelCourt IS NULL) AND `Epreuves Comp�titions`.EprCompetCompetCode = ".$Compet." AND (Cat�gories.CatSexCode = ".$reslicinit["LicSexCode"]." AND Cat�gories.CatDateD�b <= Date('".$reslicinit["LicNaissance"]."') AND Cat�gories.CatDateFin >= Date('".$reslicinit["LicNaissance"]."')) ORDER BY Epreuves.Ordre");
                                                    $nbLignesEpr = 0;
                                                    $nbLignesEpr = mysql_num_rows($reqeprinitBIS);
                                                    if ($nbLignesEpr > 1) $testNbLignesEpr = true;
                                                    if ($nbLignesEpr == 0) {
                                                        $catInitBIS = CalculCat("", $reslicinit["LicNaissance"], $reslicinit["LicSexCode"], 1);
                                                        if (($catInitBIS == "PF") || ($catInitBIS == "PG")) {
                                                            $reqeprinitBIS = bf_mysql_query("SELECT EprCompetCode, EprLibelCourt 
																				 FROM Epreuves INNER JOIN Cat�gories ON Cat�gories.CatCode = Epreuves.EprCatCode INNER JOIN `Epreuves Comp�titions` ON Epreuves.EprCode = `Epreuves Comp�titions`.EprCompetEprCode LEFT JOIN Groupes ON Groupes.GrCode = Epreuves.EprGrCode
																				 WHERE Cat�gories.CatPrim = TRUE AND (Groupes.GrLibelCourt < 9 OR Groupes.GrLibelCourt IS NULL) AND `Epreuves Comp�titions`.EprCompetCompetCode = ".$Compet." AND Cat�gories.CatSexCode = ".$reslicinit["LicSexCode"]." AND (Cat�gories.CatLibelCourt ='BF' OR Cat�gories.CatLibelCourt ='BG') ORDER BY Epreuves.Ordre");
                                                            $nbLignesEpr = mysql_num_rows($reqeprinitBIS);
                                                            if ($nbLignesEpr > 1) $testNbLignesEpr = true;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        if ( $testNbLignesEpr) {
                                            echo "<a href='$PHP_SELF?".$par."supprtout=&BValidernumlicence=Ok&action=ajoutedata&aj=1&selectionner=true&ParLicCode=".$rowArray['ParLicCode']."'>&nbsp;Modifier&nbsp;</a>";
                                        } else {
                                            echo "<a id='$i' href='$PHP_SELF?".$par."al=".$alea."&action=modifiedata&modif=$key&aj=&supprtout=#".($i-10)."'>&nbsp;Modifier&nbsp;</a>";
                                        }
                                    } else {
                                        echo "&nbsp;Modifier&nbsp;";
                                    }
                                }

                                if ((in_array("supprimer",$Choix)) && (!($licence == 1)) ) {if (!($protect)) echo "<a id='$i' href='$PHP_SELF?".$par."action=confirmedeletedata&suppr=$key&aj=&supprtout=#".($i-4)."'>&nbsp;Supprimer&nbsp;</a>"; else echo "&nbsp;Supprimer&nbsp;";}

                                if ((in_array("importer",$Choix)) && (($menu == "etablissements") || ($menu == "competitions") || ($menu == "parametres"))) {
                                    if (($imp == True) && ( ($Compet == $key) || ($Sport == $key) )) $invimp = False; else $invimp = True;
                                    if ($menu == "etablissements") echo "<a href='$PHP_SELF?".$par."action=importer&imp=$invimp&EtabImport=$key'> Import </a>\n";
                                    if ($menu == "competitions") echo "<a href='$PHP_SELF?".$par."action=importer&imp=$invimp&Compet=$key'> Import </a>\n";
                                    if ($menu == "parametres") {if (!($protect)) echo "<a href='$PHP_SELF?".$par."action=importer&imp=$invimp&Sport=$key'> Import </a>\n";else echo "&nbsp;Import&nbsp;";}
                                }

                                if ( ($stat == 0) && (in_array("exporter",$Choix)) && (($menu == "etablissements") || ( ($menu == "competitions")  && ($sousmenu == "references") ) || ($menu == "parametres"))) {
                                    if (($exp == True) && ( ($EtabExport == $key) || ($Compet == $key) || ($Sport == $key) )) $invexp = False; else $invexp = True;
                                    if ($menu == "etablissements") echo "<a href='$PHP_SELF?".$par."action=exporter&exp=$invexp&EtabExport=$key&exporttype=$exporttype&optionexport=expetab'>&nbsp;Export </a>\n";
                                    if ($menu == "competitions")   echo "<a href='$PHP_SELF?".$par."action=exporter&exp=$invexp&Compet=$key&exporttype=$exporttype&optionexport=expcompet'>&nbsp;Export </a>\n";
                                    if ($menu == "parametres")     echo "<a href='$PHP_SELF?".$par."action=exporter&exp=$invexp&Sport=$key&exporttype=$exporttype&optionexport=expsport'>&nbsp;Export </a>\n";
                                }

                                if ( in_array("licence",$Choix) && ($licence == 1) ) {

                                    $req = bf_mysql_query("SELECT `ImpressionLic` FROM `Paramweb`");
                                    if ((!(!$req)) && (mysql_num_rows($req) > 0)) {$data = mysql_fetch_assoc($req); $data = $data["ImpressionLic"];} else $data = 0;
                                    $check = "";
                                    if (isset($_POST['licence'.$key])) $check = "checked" ;
                                    if ($seltous == 1) $check = "checked";
                                    if ($selaucun == 1) $check = "";

                                    echo "<INPUT type='hidden' name='licenceLigne".$i."' value='".$key."'>";
                                    if ( ($rowArray['LicInscrit'] == 1) && ( $data > 0) ) {
                                        if ( ($Adm) || ( (!($Adm)) && ($data == 1)) ) {
                                            echo "<p style ='margin:0px; text-align:left;'> &nbsp;";
                                            echo "<INPUT type='checkbox' name='licence".$key."' value='".$rowArray['LicNumLicence']."'".$check."> ".strtoupper($rowArray['LicNom'])." ".strtoupper(substr($rowArray['LicPr�nom'],0,1)).".";
                                            if ($Adm) echo " &nbsp;-&nbsp; <a id='$i' href='$PHP_SELF?".$par."action=modifiedata&modif=$key#".($i-10)."'>&nbsp;Modifier&nbsp;</a>";
                                            echo "</p>\n";
                                        }
                                    } else {
                                        if ( (!($rowArray['LicInscrit'])) && (!(is_null($rowArray['LicDateDem']))) && (is_null($rowArray['LicDateValid'])) ) echo "<FONT color='red'><BLINK> Demande de licence en cours </BLINK></FONT><BR>";

                                        if ( (!($Adm)) && ($rowArray['LicAss']) ) echo "&nbsp;Modifier&nbsp"; else echo "<a id='$i' href='$PHP_SELF?".$par."action=modifiedata&modif=$key#".($i-10)."'>&nbsp;Modifier&nbsp;</a>";

                                        if ( (!($rowArray['LicInscrit'])) && (!(is_null($rowArray['LicDateDem']))) && (is_null($rowArray['LicDateValid'])) ) {
                                            echo "<a id='$i' href='$PHP_SELF?".$par."action=confirmedeletedata&suppr=$key&aj=&supprtout=#".($i-10)."'>&nbsp;Supprimer&nbsp;</a>";
                                            if ($Adm) echo "<a id='$i' href='$PHP_SELF?".$par."action=modifiedata&modif=$key&valid=$key#".($i-10)."'>&nbsp;Valider&nbsp;</a>";
                                        }
                                        if ( ($menu == "competitions") && (!($rowArray['LicInscrit'])) && ((is_null($rowArray['LicDateDem']))) && (is_null($rowArray['LicDateValid'])) ) {
                                            if ($Adm) echo "<a id='$i' href='$PHP_SELF?".$par."action=confirmedeletedata&suppr=$key&aj=&supprtout=#".($i-10)."'>&nbsp;Supprimer&nbsp;</a>";
                                        }
                                    }
                                }

                                if ( in_array("selectionner",$Choix) ) {
                                    if ( (isset($Compet)) && (!(empty($Compet))) && ($Compet > 0) && ($selectionner == true) )
                                        if ($sousmenu == "individuels")	echo "<a href='$PHP_SELF?".$par."action=VoirMenu&menu=competitions&sousmenu=individuels&aj=1&ParLicCode=".$rowArray['LicNumLicence']."&BValidernumlicence=Ok&affcompet=$affcompet&filter=&filtre1=&fi=0'>&nbsp;S�lectionner&nbsp;</a>";

                                }

                                if (($ChampsRacParam != "") && (!($Consult)) ) {
                                    for ($b = 0; $b < count($ChampsRacParam); $b++) {
                                        if ($ChampsRacParam[$b][0] == 1) echo "<a href='$PHP_SELF?".$par."action=VoirMenu&menu=".$ChampsRacParam[$b][1]."&sousmenu=".$ChampsRacParam[$b][2]."&filtre1=&fi=1&orderby=&racnom=".$ChampsRacParam[$b][3]."&racval=".$rowArray[$ChampsRacParam[$b][4]]."'>&nbsp;".$ChampsRacParam[$b][5]."&nbsp;</a>";
                                        if ($ChampsRacParam[$b][0] == 2) echo "<a href='$PHP_SELF?".$par."action=VoirMenu&menu=".$ChampsRacParam[$b][1]."&sousmenu=".$ChampsRacParam[$b][2]."&".$ChampsRacParam[$b][3]."=".$rowArray[$ChampsRacParam[$b][4]]."&filtre1=&fi=&orderby=&racnom=&racval=&orderby='>&nbsp;".$ChampsRacParam[$b][5]."&nbsp;</a>";
                                    }
                                }
                            }
                        }
                    }
                }

                if (($i >= 0 ) || (($i == -2) && in_array("filtrage",$Choix) && ($fi==true)) || (($i == -1) && in_array("ajout",$Choix) && ($aj==true))) echo "</tr>\n";

                if (($menu == "competitions") && ($sousmenu == "individuels") && ($nbLignesEpr > 1) && (!(strstr($MaReqErr, "bouton") == True))) {
                    if  (($i == -1) && ($aj == true) && (isset($_POST["ParLicCode"]))) {
                        for( $l = 0; $l < $nbLignesEpr; $l++ ) {
                            echo "<TR class='tredit'>";
                            for( $j = 0; $j < ($col-1); $j++ ) {
                                if ($ChampsAff[$j] == true) {
                                    echo "<TD style='padding:2px;'>\n";
                                    $field = mysql_fetch_field( $pResult, $j );
                                    $check = ""; $valeur = "";
                                    if ($j == 2) {
                                        $rowz = mysql_fetch_array($reqeprinitBIS);
                                        $reqeprz = bf_mysql_query("SELECT * FROM Participations WHERE ParEprCode = ".$rowz[0]." AND ParCompetCode = ".$Compet." AND ParLicCode = ".$_POST["ParLicCode"]);
                                        $rowzz = mysql_fetch_array($reqeprz);
                                    }
                                    if ($field->name == "EprLibelCourt") {
                                        if (!(!($rowzz))) $check = "checked"; else if (isset($_POST['EprLibelCourt'.$rowz[0]])) $check = "checked";
                                        echo "<p style ='margin: 0px; white-space:nowrap;'>";
                                        echo "<INPUT type='hidden' name='Lic".$l."' value='".$_POST["ParLicCode"]."'> ";
                                        echo "<INPUT type='hidden' name='Par".$l."' value='".$rowzz['ParCode']."'> ";
                                        echo "<INPUT type='hidden' name='Epr".$l."' value='".$rowz[0]."'> ";
                                        echo "<INPUT type='hidden' name='Lib".$l."' value='".$rowz[1]."'> ";
                                        echo "<INPUT type='checkbox' name='EprLibelCourt".$rowz[0]."' value='".$rowz[0]."'".$check."> ";
                                        if ($check == "checked") echo "<B><I>";
                                        echo $rowz[1];
                                        if ($check == "checked") echo "</B></I>";
                                        echo "</p>\n";
                                    }
                                    if ($field->name == "ParQuadra") {
                                        if (!(!($rowzz))) if ($rowzz['ParQuadra']) $check = "checked";else if (isset($_POST['ParQuadra'.$rowz[0]])) $check = "checked";
                                        echo "<p style ='margin: 0px; text-align:center;'>";
                                        echo "<INPUT type='checkbox' name='ParQuadra".$rowz[0]."' value='".$rowz[0]."'".$check.">";
                                        echo "</p>\n";
                                    }
                                    if ($field->name == "EquNum") {
                                        if (!(!($rowzz))) if ($rowzz['ParEquCode'] > 0) $valeur = $rowzz['ParEquCode'];else $valeur = $_POST['ParEquCode'.$rowz[0]];
                                        echo "<p style ='margin: 0px;'>";
                                        listederoulante($field->name.$rowz[0]," ",$ChampsInsert[$j][3][2],$ChampsInsert[$j][3][3],$ChampsInsert[$j][3][4],$ChampsInsert[$j][3][5], $valeur, $ChampsInsert[$j][3][8]);
                                        echo "</p>\n";
                                    }
                                    if ($field->name == "ParPerfQualif") {
                                        if (!(!($rowzz))) if ($rowzz['ParPerfQualif'] > 0) $valeur = $rowzz['ParPerfQualif'];else $valeur = $_POST['ParPerfQualif'.$rowz[0]];
                                        echo "<p style ='margin: 0px;'>";
                                        echo "<INPUT type='text' name='ParPerfQualif".$rowz[0]."' value='".$valeur."' style='width : 75px; border-width : 0px; text-align : right;'>";
                                        echo "</p>\n";
                                    }
                                    for ( $k = 1; $k < 6; $k++ ) {
                                        if ($field->name == "ParObs".$k) {
                                            $valeur = "";
                                            if (!(!($rowzz))) $valeur = $rowzz['ParObs'.$k];else $valeur = $_POST['ParObs'.$k.$rowz[0]];
                                            echo "<p style ='margin: 0px;'>";
                                            if ((($ChampsInsert[$j][0]=="ListeD") || ($ChampsInsert[$j][0]=="ListeS")) && ($ChampsInsert[$j][5] == true)) {
                                                listederoulante($field->name.$rowz[0],$ChampsInsert[$j][3][1],$ChampsInsert[$j][3][2],$ChampsInsert[$j][3][3],$ChampsInsert[$j][3][4],$ChampsInsert[$j][3][5], $valeur, $ChampsInsert[$j][3][8]);
                                            } else {
                                                echo "<INPUT type='text' name='ParObs".$k.$rowz[0]."' value='".$valeur."' style='border-width : 0px; text-align:center;'>";
                                            }
                                            echo "</p>\n";
                                        }
                                    }
                                    echo "</TD>\n";
                                }
                            }
                            echo "<TD></TD>";
                            echo "</TR>";
                        }
                    }
                }

            }

        }

        if( $row == 0 ) {
            echo "<TR><TD Align='center' COLSPAN='". (count(array_keys($ChampsAff, true)) + 1) ."'> - La liste est vide - </TD></TR>";
        }

        echo "</table>\n";

        echo "</form>\n";

        echo $selecteur;

    }

}

