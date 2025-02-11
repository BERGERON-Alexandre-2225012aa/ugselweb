<?php
Function VoirMenu() {
    Global $Adm, $ValideLignesParPage, $ValideSelecteurEtab, $LignesParPage, $menu, $sousmenu, $action, $PHP_SELF, $tablename, $Compet, $Etab, $Lic, $Filtre, $Inscrire, $FiltreSuppr, $orderby, $Nav, $where, $affcompet, $optionexport, $optionsuppr, $aj, $modif, $ListeSport, $ListeSportImport, $plusunan, $moinsunan, $majcat, $importcat, $Bimportcat, $VERSION;
    Global $suppr, $supprtout, $MaKey, $filter, $page, $filtre1, $fi, $TAILLE, $ADMINLOGIN, $Couleurs;
    Global $BDD, $optionmaintenance, $validemaintenance, $validemodifierbase, $listebases, $upload, $presupprimer, $optionsuppr, $preimporter, $fileName, $tmpName, $fileSize, $fileType;
    Global $accueil, $valideaccueil, $supprimer, $requete, $validerequete;
    Global $Sport;
    Global $basesexternes, $valideBasesExternes;
    Global $Consult;
    Global $BAjouter, $SelecteurEtab;
    Global $exp, $ListeSauvegardes, $fichier, $actionfichier;
    Global $TriFic, $QUOTA;
    Global $stat, $horscat, $ParLicCode, $ugselimp;
    Global $exporter, $clicbouton;
    Global $licence;
    Global $valid;
    Global $valideimpressionlic, $optionimpressionlic, $valideinscriptionlic, $optioninscriptionlic, $optionimpressionlicAss;
    Global $selectionner;
    Global $LICENCES, $REQUETES, $ADRSITE;

    PurgeTables();

    $montableau = array(
        "menu" => $menu, "sousmenu" => $sousmenu,"action" => $action,
        "tablename" => $tablename,
        "MaKey" => $MaKey, "orderby" => $orderby, "filter" => $filter,
        "ListeSport" => $ListeSport,
        "modif" => $modif, "suppr" => $suppr,"supprtout" => $supprtout,
        "fi" => $fi, "aj" => $aj,
        "affcompet" => $affcompet,
        "page" => $page,"filtre1" => $filtre1,
        "TriFic" => $TriFic,
        "stat" => $stat,
        "horscat" => $horscat,
        "licence" => $licence
    );

    if ((isset($BAjouter)) && ($menu == "competitions") && ($sousmenu == "references")) $Compet = TrouveMax("SELECT MAX(CompetCode) AS Max FROM Comp�titions") + 1;
    if (isset($Compet)) $montableau["Compet"] = $Compet;
    if (!(isset($stat))) $stat = 0;
    if (!(isset($horscat))) $horscat = 0;
    if ($Consult) $licence = 0;

    $par = EcritParam(serialize($montableau));

    if (isset($ValideSelecteurEtab)) $Etab = $SelecteurEtab;

    if (!(isset($menu))) {if (($Adm) && (date("Ymd") < 20111101)) $menu = "apropos"; else $menu = "competitions";}

    if (!(isset($sousmenu))) {
        if ($menu == "competitions") {if ($Adm) $sousmenu = "references"; else $sousmenu = "individuels";}
        if ($menu == "parametres") $sousmenu = "sports";
    }

    if (($menu == "competitions") && (!(in_array($sousmenu, array("references","individuels","individuels(2)","equipes","licences"))))) if ($Adm) $sousmenu = "references"; else $sousmenu = "individuels";
    if (($menu == "parametres") && (!(in_array($sousmenu, array("sports","categories","epreuves"))))) $sousmenu = "sports";

    if ((!(isset($aj))) && (!(($menu == "competitions") && (($sousmenu == "individuels") || ($sousmenu == "equipes")) && ($Adm)))) $aj = false;
    if (!(isset($modif)))$modif = false;
    if (!(isset($fi)))   $fi    = false;
    $Where = "";

    if ($Adm) $s = "s"; else $s="";
    echo "<TABLE CLASS = 'tablemenu'>\n<TR>\n<TD>\n";
    if ($Adm) {echo  "<a "; if ($menu =="parametres") echo "CLASS = 'inv'"; echo "href='$PHP_SELF?action=VoirMenu&menu=parametres&sousmenu=$sousmenu&Compet=$Compet'> &nbsp;Param�tres&nbsp; </a>\n";}
    if (!($Consult)) { if ($Adm) echo "|"; echo "<a "; if ($menu =="etablissements") echo "CLASS = 'inv'"; echo "href='$PHP_SELF?action=VoirMenu&menu=etablissements&sousmenu=$sousmenu&Compet=$Compet' > &nbsp;Etablissement$s&nbsp; </a>\n";}
    if (!($Consult)) {echo "|<a "; if ($menu =="licencies") echo "CLASS = 'inv'"; echo "href='$PHP_SELF?action=VoirMenu&menu=licencies&sousmenu=$sousmenu&Compet=$Compet&licence=0'> &nbsp;Licenci�s&nbsp; </a>\n";}
    if (!($Consult)) echo "|"; echo "<a "; if ($menu =="competitions") echo "CLASS = 'inv'"; echo "href='$PHP_SELF?action=VoirMenu&menu=competitions&sousmenu=$sousmenu&Compet=$Compet&Etab=$Etab&licence=0'> &nbsp;Comp�titions&nbsp; </a>\n";
    echo "|<a "; if ($menu =="options") echo "CLASS = 'inv'"; echo "href='$PHP_SELF?action=VoirMenu&menu=options&sousmenu=$sousmenu&Compet=$Compet&Etab=$Etab'> &nbsp;Options&nbsp; </a>\n";
    if ($Adm) {echo "|<a "; if ($menu =="outils") echo "CLASS = 'inv'"; echo "href='$PHP_SELF?action=VoirMenu&menu=outils&sousmenu=$sousmenu&Compet=$Compet'> &nbsp;Outils&nbsp; </a>\n";}
    if ($Adm) {echo "|<a "; if ($menu =="connexions") echo "CLASS = 'inv'"; echo "href='$PHP_SELF?action=VoirMenu&menu=connexions&sousmenu=$sousmenu&Compet=$Compet'> &nbsp;En ligne&nbsp; </a>\n";}
    echo "|<a "; if ($menu =="apropos") echo "CLASS = 'inv'"; echo "href='$PHP_SELF?action=VoirMenu&menu=apropos&sousmenu=$sousmenu&Compet=$Compet&Etab=$Etab'> &nbsp;A propos&nbsp; </a>\n";
    if (!($Consult)) {echo "|<a href='$PHP_SELF?action=logout'> &nbsp;D�connexion&nbsp; </a>\n";}

    echo "</TD>\n<TD align = 'right'>";
    echo "<a href='$PHP_SELF?".$par."action=VoirMenu&modiftaille=".($TAILLE - 1)."&sousmenu=$sousmenu&Compet=$Compet&Etab=$Etab' CLASS = 'tailleur'>&nbsp;-&nbsp;</a>";
    echo "<a href='$PHP_SELF?".$par."action=VoirMenu&modiftaille=3&sousmenu=$sousmenu&Compet=$Compet&Etab=$Etab'                 CLASS = 'tailleur'> Taille </a>";
    echo "<a href='$PHP_SELF?".$par."action=VoirMenu&modiftaille=".($TAILLE + 1)."&sousmenu=$sousmenu&Compet=$Compet&Etab=$Etab' CLASS = 'tailleur'>&nbsp;+&nbsp;</a>&nbsp";
    echo "</TD>\n</TR>\n</TABLE>\n";

    echo "</DIV>";
    echo "<DIV id = 'contenu'>";

    echo "<SPAN CLASS='filmenu'> &nbsp;&nbsp;&nbsp;".date("d/m/y  H:i:s ")."&nbsp;&nbsp;(".$menu;
    if ( ($menu == "parametres") || ($menu == "competitions") ) echo " / ".$sousmenu;
    echo ") </SPAN>\n";

    if (($menu == "parametres")  && ($Adm)){
        echo "<TABLE CLASS = 'tablesousmenu'><TR><TD>";
        echo" &nbsp;";
        echo "<a "; if ($sousmenu == "sports") echo "CLASS = 'inv'"; echo "href='$PHP_SELF?action=VoirMenu&menu=parametres&sousmenu=sports&Compet=$Compet&ListeSport=$ListeSport'  > &nbsp; Sports  &nbsp; </a>"; echo"|";
        echo "<a "; if ($sousmenu == "categories") echo "CLASS = 'inv'"; echo "href='$PHP_SELF?action=VoirMenu&menu=parametres&sousmenu=categories&Compet=$Compet&ListeSport=$ListeSport'  > &nbsp; Cat�gories &nbsp; </a>"; echo"|";
        echo "<a "; if ($sousmenu == "epreuves") echo "CLASS = 'inv'"; echo "href='$PHP_SELF?action=VoirMenu&menu=parametres&sousmenu=epreuves&Compet=$Compet&ListeSport=$ListeSport'  > &nbsp; Epreuves &nbsp; </a>";
        echo "</TD></TR></TABLE>";

        if ($sousmenu == "sports") {
            $tablename     = "Sports";
            $queryStr      = "SELECT `SpoCode`,`SpoLibelCourt`, `SpoLibell�`, `Ordre`, `SpoGestionPerf` 
						  FROM `Sports`";
            if(!isset($orderby)) $orderby = "Ordre";
            $MaKey         = "SpoCode";
            $NomsColonnes  = array('Code','Code','Libell�','Ordre','Gestion');
            $ChampsTri     = array('/', '/', '/', '/','/');
            $ChampsAli     = array('center','center','','center','center');
            $ChampsFor     = array('','','','','');
            $ChampsAff     = array(false,true,true,false,false);
            $Choix 		   = array("importer","exporter","ajout","modifier","supprimer","monter","descendre");
            $ChampsType    = array("Texte","Texte","Texte","Texte","Texte");
            $ChampsTypeExt = array("","","","","");
            $ChampsFiltre  = array(true,true,true,true,true);
            $ChampsNomFil  = array("","","","","");
            $ChampsValide  = array('','','','','');
            $ChampsEdit      = array(
                array("Texte" ,false,"","",true),
                array("Texte" ,true ,"","",true),
                array("Texte" ,true ,"","",true),
                array("Texte" ,false ,"","",true),
                array("Texte" ,true ,"","",true)
            );
            $ChampsInsert    = array(
                array("Texte" ,true ,"","",Array("Max","SpoCode"),true,false,false),
                array("Texte" ,true ,"","","",true,true,false),
                array("Texte" ,true ,"","","",true,true,false),
                array("Texte" ,true ,"","", Array("Max","Ordre"), true,false,false),
                array("Texte" ,true ,"","","-99",true,true,false)
            );
            $ChampsRacFiltre = array(false,false,false,false,false);
            $ChampsRacParam  = array(array(2,'parametres','categories','ListeSport',0,'->'));
            $sousqueryStr    = "";
            $messagedel      = "Attention ! la suppression d'un sport entra�ne la suppression des cat�gories, des �preuves et des comp�titions dans ce sport.";
            $MajChpOrdre     = array(array("Cat�gories","CatSpoCode"),array("Epreuves","EprSpoCode"));
            GereData($tablename, $queryStr, $MaKey, $NomsColonnes, $ChampsTri, $ChampsAli, $ChampsFor, $ChampsAff, $action, $orderby, $Choix, $ChampsEdit, $ChampsInsert, $ChampsType, $ChampsTypeExt, $ChampsFiltre, $where, $ChampsNomFil, $ChampsRacFiltre, $ChampsRacParam, $sousqueryStr, $messagedel, $MajChpOrdre);
        }

        if (($sousmenu == "categories")) {
            if (!(isset($ListeSport)) || empty($ListeSport) )  {
                $req = bf_mysql_query("SELECT * FROM Sports ORDER BY Ordre");
                if (!(!$req)) {
                    $res = mysql_fetch_array($req);
                    if (!(!$res)) $ListeSport = $res["SpoCode"];
                }
            }
            $req = bf_mysql_query("SELECT * FROM Sports WHERE SpoCode = $ListeSport");
            if (!(!$req)) {
                echo "<SPAN CLASS='pasimprimer'>";
                $res = mysql_fetch_array($req);
                if ($res) $SpoGestion = $res["SpoGestionPerf"];
                echo "<form action='$PHP_SELF' method=post>\n";
                echo "<TABLE>";
                echo "<TR>";
                echo "<TD>";
                echo "&nbsp;Sport &nbsp;";
                listederoulante("ListeSport", "", "SELECT SpoCode, SpoLibelCourt, SpoLibell� FROM Sports ORDER BY Ordre ", array("SpoLibelCourt","-","SpoLibell�"), array("","",""), "SpoCode" ,$ListeSport, 350);
                echo "&nbsp;";
                echo "<input type='submit' name='action' value='Ok' class ='bouton'>";
                echo "</TD>";
                echo "</TR>";
                echo "<TR>";
                echo "<TD>";
                if (($majcat != true) && ($importcat != true)) {
                    echo "<a href='$PHP_SELF?action=VoirMenu&menu=parametres&sousmenu=categories&majcat=true&importcat=&ListeSport=$ListeSport'>&nbsp;Mettre � jour les ann�es des cat�gories de tous les sports&nbsp;</a>";
                }
                if ($majcat == true) {
                    echo "Mettre � jour les cat�gories de tous les sports ";
                    echo "<input type='submit' name='moinsunan' value='-1 an' class ='bouton'>";
                    echo "&nbsp;";
                    echo "<input type='submit' name='plusunan'  value='+1 an' class ='bouton'>";
                    echo "&nbsp;";
                    echo "<a href='$PHP_SELF?action=VoirMenu&menu=parametres&sousmenu=categories&majcat=&importcat=&ListeSport=$ListeSport'>&nbsp;Terminer&nbsp;</a>";
                }
                if ( ($SpoGestion == -99) && ($importcat != true) && ($majcat != true)) {
                    echo " - <a href='$PHP_SELF?action=VoirMenu&menu=parametres&sousmenu=categories&majcat=&importcat=true&ListeSport=$ListeSport'>&nbsp;Importer les cat�gories&nbsp;</a>";
                }
                if ($importcat == true) {
                    echo "Importer les cat�gories du sport ";
                    listederoulante("ListeSportImport", "", "SELECT SpoCode, SpoLibelCourt, SpoLibell� FROM Sports WHERE SpoCode <> $ListeSport ORDER BY Ordre ", array("SpoLibelCourt","-","SpoLibell�"), array("","",""), "SpoCode" ,$ListeSportImport, 300);
                    echo "&nbsp;";
                    echo "<input type='submit' name='Bimportcat' value='Ok' class ='bouton'>";
                    echo "&nbsp;";
                    echo "<a href='$PHP_SELF?action=VoirMenu&menu=parametres&sousmenu=categories&majcat=&importcat=&ListeSport=$ListeSport'>&nbsp;Terminer&nbsp;</a>";
                }
                echo "</TD>";
                echo "</TR>";
                echo "</TABLE>";
                ConstruitZone(array(array("menu",$menu),array("sousmenu",$sousmenu),array("action",$action)));
                ConstruitZone(array(array("Compet",$Compet),));
                ConstruitZone(array(array("affcompet",$affcompet)));
                ConstruitZone(array(array("majcat",$majcat)));
                echo "</FORM>";
                echo "</SPAN>";

                if (isset($plusunan)) {
                    bf_mysql_query("UPDATE Cat�gories SET CatDateD�b = DATE_ADD( CatDateD�b, INTERVAL 1 YEAR ), CatDateFin = DATE_ADD( CatDateFin, INTERVAL 1 YEAR ) WHERE YEAR( CatDateD�b ) <> 1970",0,"`Cat�gories`");
                    bf_mysql_query("UPDATE Cat�gories SET CatDateFin = DATE_ADD( CatDateFin, INTERVAL 1 YEAR ) WHERE YEAR( CatDateD�b ) = 1970",0,"`Cat�gories`");
                    unset($plusunan);
                    bf_mysql_query("UNLOCK TABLES");
                }
                if (isset($moinsunan)) {
                    bf_mysql_query("UPDATE Cat�gories SET CatDateD�b = DATE_ADD( CatDateD�b, INTERVAL -1 YEAR ), CatDateFin = DATE_ADD( CatDateFin, INTERVAL -1 YEAR ) WHERE YEAR( CatDateD�b ) <> 1970",0,"`Cat�gories`");
                    bf_mysql_query("UPDATE Cat�gories SET CatDateFin = DATE_ADD( CatDateFin, INTERVAL -1 YEAR ) WHERE YEAR( CatDateD�b ) = 1970",0,"`Cat�gories`");
                    unset($moinsunan);
                    bf_mysql_query("UNLOCK TABLES");
                }

                if (isset($Bimportcat)) {
                    $reqImport = bf_mysql_query("SELECT Cat�gories.* FROM `Cat�gories` WHERE Cat�gories.CatSpoCode = $ListeSportImport");
                    if (!(!($reqImport))) {
                        while ($resImport = mysql_fetch_array($reqImport)) {
                            Maj(1,"Cat�gories", $resImport, Array("CatSpoCode" => "SELECT SpoCode FROM Sports WHERE SpoCode = $ListeSport"));
                        }
                    }
                    unset($Bimportcat);
                    $Res = bf_mysql_query("SELECT * FROM Sports INNER JOIN Cat�gories ON Sports.SpoCode = Cat�gories.CatSpoCode ORDER BY Sports.Ordre, Cat�gories.Ordre");
                    $cpte = 0;
                    while ($res = mysql_fetch_array($Res)) {
                        $cpte = $cpte + 1;
                        bf_mysql_query("UPDATE Cat�gories SET Ordre = $cpte WHERE CatCode = ".$res['CatCode'],0,"`Cat�gories`");
                    }
                    bf_mysql_query("UNLOCK TABLES");
                }
            } else $SpoGestion = -99;

            $tablename     = "Cat�gories";
            $queryStr      = "SELECT `CatCode`, `SpoLibelCourt`, `CatLibelCourt`, `CatLibell�`, `CatDateD�b`, `CatDateFin`, `CatSexCode`, `CatSpoCode`, `CatPrim`, Cat�gories.`Ordre`, `SpoGestionPerf` 
						  FROM `Cat�gories` INNER JOIN `Sports` ON Cat�gories.CatSpoCode = Sports.SpoCode";
            if (!(!$req)) $where = "(CatSpoCode = $ListeSport)";
            if(!isset($orderby)) $orderby = "Cat�gories.Ordre";
            $MaKey         = "CatCode";
            $NomsColonnes  = array('Code','Sport', 'Code', 'Libell�','D�but','Fin', 'Sexe', 'Sport', 'Prim', 'Ordre', 'Gestion');
            $ChampsTri     = array('/', '/', '/', '/', '/', '/', '/', '/', '/', '/', '/');
            $ChampsAli     = array('center', 'center', 'center','','center','center','center','center','center','center','center');
            $ChampsFor     = array('','','','','','','','','','','');
            $ChampsAff     = array(false,true,true,true,true,true,true,false,false,false,false);
            if ($SpoGestion == -99 ) $Choix = array("ajout","modifier","supprimer","monter","descendre"); else $Choix = "";
            $ChampsType    = array("Texte","Texte","Texte","Texte","Texte","Texte","ListeS","Texte","Texte","Texte","Texte");
            $ChampsTypeExt = array("","","","","","",array("1"=>'G',"2"=>'F',"3"=>'M'),"","","","");
            $ChampsFiltre  = array(true,true,true,true,true,true,true,true,true,true,true);
            $ChampsNomFil  = array("","","", "","","","","","","","");
            $ChampsValide  = array('','','','','','','','','','','');
            $ChampsRacFiltre = array(false,false,false,false,false,false,false,false,false,false,false);
            $ChampsRacParam  = "";
            $sousqueryStr    = "";
            $ChampsEdit      = array(
                array("Texte" ,false,"","",true),
                array("Texte" ,false,"","",true),
                array("Texte" ,true ,"","",true),
                array("Texte" ,true ,"","",true),
                array("Texte" ,true ,"","",true),
                array("Texte" ,true ,"","",true),
                array("ListeS",true ,"",array("LicSexCode", "Sexe..."       ,array('1'=>'G','2'=>'F')    , "", "", "", "", "", "35"),true),
                array("Texte" ,false,"","",true),
                array("Texte" ,true ,"","",true),
                array("Texte" ,true ,"","",true),
                array("Texte" ,true ,"","",true)
            );
            $ChampsInsert    = array(
                array("Texte" ,true,"","",Array("Max","CatCode"),true,true,false),
                array("Texte" ,false,"","","",true,true,false),
                array("Texte" ,true ,"","","",true,true,false),
                array("Texte" ,true ,"","","",true,true,false),
                array("Texte" ,true ,"","","",true,true,false),
                array("Texte" ,true ,"","","",true,true,false),
                array("ListeS",true ,"",array("LicSexCode", "Sexe..."       ,array('1'=>'G','2'=>'F')    , "", "", "", "", "", "35"),"",true,true,false),
                array("Texte" ,true ,"","",$ListeSport,true,true,false),
                array("Texte" ,true ,"","",'1',true,true,false),
                array("Texte" ,true ,"","", Array("Max","Ordre"), true,true,false),
                array("Texte" ,false ,"","","",true,true,false)
            );
            $messagedel      = "Attention ! la suppression d'une cat�gorie entra�ne la suppression des �preuves et des participations dans cette cat�gorie.";
            $MajChpOrdre     = array(array("Cat�gories","CatSpoCode"),array("Epreuves","EprSpoCode"));
            GereData($tablename, $queryStr, $MaKey, $NomsColonnes, $ChampsTri, $ChampsAli, $ChampsFor, $ChampsAff, $action, $orderby, $Choix, $ChampsEdit, $ChampsInsert, $ChampsType, $ChampsTypeExt, $ChampsFiltre, $where, $ChampsNomFil, $ChampsRacFiltre, $ChampsRacParam, $sousqueryStr, $messagedel, $MajChpOrdre);
        }

        if (($sousmenu == "epreuves")) {

            if (!(isset($ListeSport)) || empty($ListeSport))  {
                $req = bf_mysql_query("SELECT * FROM Sports WHERE SpoGestionPerf <= 0 ORDER BY Ordre");
                if (!(!$req)) {
                    $res = mysql_fetch_array($req);
                    if (!(!$res)) $ListeSport = $res["SpoCode"];
                }
            }

            $req = bf_mysql_query("SELECT * FROM Sports WHERE SpoCode = $ListeSport");
            if (!(!$req)) {
                echo "<SPAN CLASS='pasimprimer'>";
                echo "<form action='$PHP_SELF' method=post>\n";
                echo "<TABLE>";
                echo "<TR>";
                echo "<TD>";
                $res = mysql_fetch_array($req);
                if ($res) $SpoGestion = $res["SpoGestionPerf"];
                if ($SpoGestion > 0) $ListeSport = 2;
                echo "&nbsp;Sport &nbsp;";
                listederoulante("ListeSport", "", "SELECT SpoCode, SpoLibelCourt, SpoLibell� FROM Sports WHERE SpoGestionPerf <= 0 ORDER BY Ordre", array("SpoLibelCourt","-","SpoLibell�"), array("","",""), "SpoCode" ,$ListeSport, 350);
                echo "&nbsp;";
                echo "<input type='submit' name='action' value='Ok' class ='bouton'>";
                echo "</TD>";
                echo "</TR>";
                echo "</TABLE>";
                ConstruitZone(array(array("menu",$menu),array("sousmenu",$sousmenu),array("action",$action)));
                ConstruitZone(array(array("Compet",$Compet),));
                ConstruitZone(array(array("affcompet",$affcompet)));
                echo "</FORM>";
                echo "</SPAN>";
            } else $SpoGestion = -99;

            $tablename     = "Epreuves";
            $queryStr      = "SELECT `EprCode`, `SpoLibelCourt`, `EprLibelCourt`, `EprLibell�`, `CatLibelCourt`, `EprSpoCode`, Epreuves.`Ordre`, `SpoGestionPerf`
						  FROM (Sports INNER JOIN Epreuves ON Sports.SpoCode = Epreuves.EprSpoCode) INNER JOIN Cat�gories ON Epreuves.EprCatCode = Cat�gories.CatCode" ;
            if (!(!$req)) $where = "(EprSpoCode = $ListeSport)";
            if(!isset($orderby)) $orderby = "Epreuves.Ordre";
            $MaKey         = "EprCode";
            $NomsColonnes  = array('Code','Sport','Code','Libell�','Cat', 'Sport', 'Ordre', 'Gestion');
            $ChampsTri     = array('/', '/', '/', '/', '/', '/', '/', '/');
            $ChampsAli     = array('center','center', 'center','','center','center','center','center');
            $ChampsFor     = array('','','','','','','','');
            $ChampsAff     = array(false,true,true,true,true,false,false,false);
            if ($SpoGestion == -99 ) $Choix = array("ajout","modifier","supprimer","monter","descendre"); else $Choix = "";
            $ChampsType    = array("Texte","Texte","Texte","Texte","ListeD","Texte","Texte","Texte");
            $ChampsTypeExt = array("","","","","","","","");
            $ChampsFiltre  = array(true,true,true,true,true,true,true,true);
            $ChampsNomFil  = array("","","","","","","","");
            $ChampsValide  = array('','','','','','','','');
            $ChampsRacFiltre = array(false,false,false,false,false,false,false,false);
            $ChampsRacParam  = "";
            $sousqueryStr    = "";
            $ChampsEdit      = array(
                array("Texte" ,false,"","",true),
                array("Texte" ,false,"","",true),
                array("Texte" ,true ,"","",true),
                array("Texte" ,true ,"","",true),
                array("ListeD",true ,"EprCatCode",array("selcat", "Cat...","SELECT CatLibelCourt, CatLibell� FROM Cat�gories WHERE CatSpoCode = $ListeSport ORDER BY Ordre", Array("CatLibelCourt", "-", "CatLibell�"), Array("", "", ""), "CatLibelCourt","","SELECT CatCode FROM Cat�gories WHERE CatSpoCode = $ListeSport AND CatLibelCourt = ","150"),true),
                array("Texte" ,false,"","",true),
                array("Texte" ,true ,"","",true),
                array("Texte" ,false,"","",true)
            );
            $ChampsInsert    = array(
                array("Texte" ,true,"","",Array("Max","EprCode"),true,true,false),
                array("Texte" ,false,"","","",true,true,false),
                array("Texte" ,true ,"","","",true,true,false),
                array("Texte" ,true ,"","","",true,true,false),
                array("ListeD",true,"EprCatCode",array("selcat", "Cat...","SELECT CatLibelCourt, CatLibell� FROM Cat�gories WHERE CatSpoCode = $ListeSport ORDER BY Ordre", Array("CatLibelCourt", "-", "CatLibell�"), Array("", "", ""), "CatLibelCourt","","SELECT CatCode FROM Cat�gories WHERE CatSpoCode = $ListeSport AND CatLibelCourt = ","150"),"", true,true,false),
                array("Texte" ,true ,"","",$ListeSport,true,true,false),
                array("Texte" ,true ,"","", Array("Max","Ordre"), true,true,false),
                array("Texte" ,false,"","","",true,true,false)
            );
            $messagedel      = "Attention ! la suppression d'une �preuve entra�ne la suppression des participations dans cette �preuve.";
            $MajChpOrdre     = array(array("Epreuves","EprSpoCode"));
            GereData($tablename, $queryStr, $MaKey, $NomsColonnes, $ChampsTri, $ChampsAli, $ChampsFor, $ChampsAff, $action, $orderby, $Choix, $ChampsEdit, $ChampsInsert, $ChampsType, $ChampsTypeExt, $ChampsFiltre, $where, $ChampsNomFil, $ChampsRacFiltre, $ChampsRacParam, $sousqueryStr, $messagedel, $MajChpOrdre);
        }
    }

    if ($menu == "etablissements"){
        If ($stat == 0) {

            bf_mysql_query("UPDATE Etablissements INNER JOIN Secteurs ON Etablissements.EtabSecCode = Secteurs.SecCode SET EtabMemo3 = IF(RAND() > 0.33, IF(RAND() > 0.66, CONCAT(SecLibel, LOWER(EtabNomCourt), FLOOR(RAND()*100)), CONCAT(LOWER(EtabNomCourt), SecLibel, FLOOR(RAND()*100))), CONCAT(FLOOR(RAND()*100), LOWER(EtabNomCourt), SecLibel)) WHERE EtabMemo3 = '' OR EtabMemo3 IS NULL");

            $tablename     = "Etablissements";
            $queryStr      = "SELECT `EtabCode`,`EtabNum`, `EtabNomCourt`, `EtabNom`, `EtabAS`, `EtabAdresse1`, `EtabAdresse2`, `EtabCP`, `EtabVille`, `EtabT�l`, `EtabFax`, `EtabMail`, `EtabT�lEps`, `EtabMemo3`, `SecLibel`,
						  (SELECT COUNT(LicEtabCode) FROM `Licenci�s` where LicEtabCode = EtabCode) AS Lic
						  FROM `Etablissements` INNER JOIN `Secteurs` ON Etablissements.EtabSecCode = Secteurs.SecCode";
            if (!$Adm) $where = "(EtabNum = $Etab)".RetAS($Etab);
            if ((!isset($orderby)) || ($orderby == "")) $orderby = "Secteurs.Ordre, EtabNomCourt";
            $MaKey         = "EtabCode";
            $NomsColonnes  = array('Code','Num�ro','Code','Nom','A.S.','Adresse1','Adresse2','CP','Ville','T�l','Fax','Mail','T�l Eps','Passe','Secteur','Nbre');
            $ChampsTri     = array('','Secteurs.Ordre, EtabNum', 'Secteurs.Ordre, EtabNomCourt', 'EtabNom','','','','EtabCP, Secteurs.Ordre, EtabNomCourt','EtabVille, Secteurs.Ordre, EtabNomCourt','','','','','','','');
            $ChampsAli     = array('','center','center','','','','','center','','center','center','','','center','center','center');
            $ChampsFor     = array('','%06d','','','','','','','','','','','','','','');
            $ChampsAff     = array(false,true,true,true,true,true,((($aj) || ($modif)) && ($Adm)),true,true,true,true,false,false,$Adm,((($aj) || ($modif)) && ($Adm)),true);
            if ($Adm) $Choix = array("importer","exporter","ajout","modifier","filtrage","supprimer","stat");else $Choix = "";
            $ChampsType    = array("Texte","Texte","Texte","Texte","Texte","Texte","Texte","Texte","Texte","Texte","Texte","Texte","Texte","Texte","Texte","Texte");
            $ChampsTypeExt = array("","","","","","","","","","","","","","","","");
            $ChampsFiltre  = array(true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,false);
            $ChampsNomFil  = array("","","","","","","","","","","","","","","","");
            $ChampsRacFiltre = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false,true,false);
            if ($Adm) $ChampsRacParam = array(array(1,'licencies','','EtabNomCourt',2,'->')); else $ChampsRacParam = "";
            $sousqueryStr    = "";
            $ChampsValide  = array('','','','','','','','','','','','','','','','','','','','');
            $ChampsEdit      = array(
                array("Texte" ,false,"","",true),
                array("Texte" ,$Adm ,"","",true),
                array("Texte" ,$Adm ,"","",true),
                array("Texte" ,$Adm ,"","",true),
                array("Texte" ,$Adm ,"","",false),
                array("Texte" ,$Adm ,"","",false),
                array("Texte" ,$Adm ,"","",false),
                array("Texte" ,$Adm ,"","",true),
                array("Texte" ,$Adm ,"","",true),
                array("Texte" ,$Adm ,"","",false),
                array("Texte" ,$Adm ,"","",false),
                array("Texte" ,True ,"","",false),
                array("Texte" ,$Adm ,"","",false),
                array("Texte" ,$Adm ,"","",false),
                array("ListeD",$Adm,"EtabSecCode",array("selsect", "Secteur...","SELECT SecLibel, SecLibell� FROM Secteurs WHERE SecLibel <> '0' ORDER BY Ordre", Array("SecLibel", "SecLibell�"), Array("", ""), "SecLibel","","SELECT SecCode FROM Secteurs WHERE SecLibel = ","75"),true),
                array("Texte" ,False ,"","",false)
            );
            $ChampsInsert    = array(
                array("Texte" ,false,"","","",true,true ,false),
                array("Texte" ,true ,"","","",true,true ,false),
                array("Texte" ,true ,"","","",true,true ,false),
                array("Texte" ,true ,"","","",true,true ,false),
                array("Texte" ,true ,"","","",true,false,false),
                array("Texte" ,true ,"","","",true,false,false),
                array("Texte" ,true ,"","","",true,false,false),
                array("Texte" ,true ,"","","",true,true ,false),
                array("Texte" ,true ,"","","",true,true ,false),
                array("Texte" ,true ,"","","",true,false,false),
                array("Texte" ,true ,"","","",true,false,false),
                array("Texte" ,true ,"","","",true,false,false),
                array("Texte" ,true ,"","","",true,false,false),
                array("Texte" ,true ,"","","",true,false,false),
                array("ListeD",true,"EtabSecCode",array("selsect", "Secteur...","SELECT SecLibel, SecLibell� FROM Secteurs WHERE SecLibel <> '0' ORDER BY Ordre", Array("SecLibel", "SecLibell�"), Array("", ""), "SecLibel","","SELECT SecCode FROM Secteurs WHERE SecLibel = ","75"),"",true,true,false),
                array("Texte" ,false,"","","",false,false,false),
            );
            $messagedel      = "Attention ! la suppression d'un �tablissement entra�ne la suppression des licenci�s et des participations de cet �tablissement.";
            GereData($tablename, $queryStr, $MaKey, $NomsColonnes, $ChampsTri, $ChampsAli, $ChampsFor, $ChampsAff, $action, $orderby, $Choix, $ChampsEdit, $ChampsInsert, $ChampsType, $ChampsTypeExt, $ChampsFiltre, $where, $ChampsNomFil, $ChampsRacFiltre, $ChampsRacParam,$sousqueryStr,$messagedel );
        } else {
            ConstruitStat(-2, 1, $queryStr, $NomsColonnes, $ChampsAli, $ChampsFor, $ChampsAff, $ChampsType, $Choix);
            GereData("", $queryStr, "", $NomsColonnes, "", $ChampsAli, $ChampsFor, $ChampsAff, $action, "", $Choix, "", "", $ChampsType, "", "", "", "", "", "", "", "", "", 1);
        }
    }

    if ($menu == "licencies"){

        if ($stat == 0) {

            $tablename     = "Licenci�s";
            if ($horscat == 0) {

                if ($licence != 1)
                    $queryStr      = "SELECT EtabNomCourt, Licenci�s.LicCode, Licenci�s.LicInscrit, Licenci�s.LicNumLicence, Licenci�s.LicNom, Licenci�s.LicPr�nom, Licenci�s.LicNaissance, Licenci�s.LicSexCode, CatLibelCourt, EtabNomCourt, EtabNom, EtabVille
							  FROM Cat�gories, Etablissements INNER JOIN Licenci�s ON Etablissements.EtabCode = Licenci�s.LicEtabCode";
                else
                    $queryStr      = "SELECT EtabNomCourt, Licenci�s.LicCode, Licenci�s.LicInscrit, Licenci�s.LicNumLicence, Licenci�s.LicNom, Licenci�s.LicPr�nom, Licenci�s.LicNaissance, Licenci�s.LicSexCode, CatLibelCourt, EtabNomCourt, EtabNom, EtabVille, LicAss, LicNomAss, LicDateAss, LicDateDem, LicDateValid
							  FROM Cat�gories, Etablissements INNER JOIN Licenci�s ON Etablissements.EtabCode = Licenci�s.LicEtabCode";

                $where = "( CatSpoCode = 1 AND LicSexCode = CatSexCode AND (Licenci�s.LicNaissance >= CatDateD�b And Licenci�s.LicNaissance <= CatDateFin) ";
                if (!$Adm) $where .= " And (EtabNum = $Etab".RetAS($Etab)."))"; else $where.=")";
                if ($Adm) $Choix = array("importer","exporter","ajout","modifier","filtrage","supprimer","stat","selectionner"); else $Choix = array("exporter","filtrage","selectionner");
                if ( ( ((!($Adm)) && (TrouveParamweb("ImpressionLic") == 1)) || (($Adm) && (TrouveParamweb("ImpressionLic") > 0)) ) || (TrouveParamweb("InscriptionLic") > 0)) array_push($Choix,"licence");
            } else {
                $queryStr      = "SELECT EtabNomCourt, Licenci�s.LicCode, Licenci�s.LicInscrit, Licenci�s.LicNumLicence, Licenci�s.LicNom, Licenci�s.LicPr�nom, Licenci�s.LicNaissance, Licenci�s.LicSexCode, '' AS Cat, EtabNomCourt, EtabNom, EtabVille, LicAss, LicNomAss, LicDateAss, LicDateDem, LicDateValid
						      FROM Etablissements INNER JOIN Licenci�s ON Etablissements.EtabCode = Licenci�s.LicEtabCode";
                $where = " ( LicNaissance < (SELECT CatDateD�b FROM Cat�gories WHERE CatSpoCode = 1 ORDER BY CatDateD�b LIMIT 1) OR LicNaissance > (SELECT CatDateFin FROM Cat�gories WHERE CatSpoCode = 1 ORDER BY CatDateFin DESC LIMIT 1)";
                if (!$Adm) $where .= " And EtabNum = $Etab)"; else $where.=")";
                if ($Adm) $Choix = array("modifier","filtrage","supprimer","liste"); else $Choix = array("filtrage");
            }
            if ((!isset($orderby)) || ($orderby == "")) $orderby = "LicNom, LicPr�nom";
            $MaKey         = "LicCode";
            $NomsColonnes  = array('Etab','Code','Inscrit','Num�ro','Nom','Pr�nom','Naissance','Sexe','Cat','Etab','Etab Nom','Etab Ville','Ass','Ass Nom','Ass Date','Demande','Validation');
            $ChampsTri     = array('EtabNomCourt, LicNom, LicPr�nom','', 'LicInscrit DESC, LicNom, LicPr�nom', 'LicNumLicence', 'LicNom, LicPr�nom', 'LicPr�nom, LicNom', 'LicNaissance, LicNom, LicPr�nom', 'LicSexCode DESC, LicNom, LicPr�nom', 'LicSexCode DESC, LicNaissance DESC, LicNom, LicPr�nom', 'EtabNomCourt, LicNom, LicPr�nom', 'EtabNom, LicNom, LicPr�nom','EtabVille, EtabNomCourt, LicNom, LicPr�nom','LicAss Desc, LicNom, LicPr�nom','LicNomAss, LicNom, LicPr�nom', 'LicDateAss DESC, LicNom, LicPr�nom','LicDateDem DESC, LicNom, LicPr�nom','LicDateValid DESC, LicNom, LicPr�nom');
            $ChampsAli     = array('center','','center','center','','','center','center','center','center','','','center','center','center','center','center');
            $ChampsFor     = array('','','','%010s','','','','','','','','','','','','','');
            $ChampsAff     = array(!$Adm,false,true,true,true,true,true,true,true,$Adm,$Adm && (!($licence)),$Adm && (!($licence)),$licence,false,$licence,$licence,$licence);
            $ChampsType    = array("Texte","Texte","ListeS","ListeD","Texte","Texte","Date","ListeS","Texte","Texte","Texte","Texte","ListeS","Texte","Date","Date","Date");
            $ChampsTypeExt = array("","",array("0"=>"Non","1"=>"Oui"),"","","","",array("1"=>'G',"2"=>'F'),"","","","",array("0"=>"Non","1"=>"Oui"),"","","","");
            $ChampsFiltre  = array(true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true);
            $ChampsNomFil  = array("","","","","","","","","","","","","","","","","");
            $ChampsRacFiltre = array(true,false,true,false,false,false,false,true,true,true,false,false,true,true,true,true,true);
            $ChampsRacParam  = "";
            $sousqueryStr    = "";
            $ChampsEdit      = array(
                array("Texte" ,false,"","",true),
                array("Texte" ,false,"","",true),
                array("ListeS",$Adm ,"",array("LicInscrit", "Inscrit..."    ,array("0"=>"Non","1"=>"Oui"), "", "", "", "", "","45"),true),
                array("Texte" ,$Adm,"","",true),
                array("Texte" ,$Adm ,"","",true),
                array("Texte" ,$Adm ,"","",true),
                array("Date"  ,$Adm ,"","",true),
                array("ListeS",$Adm ,"",array("LicSexCode", "Sexe..."       ,array('1'=>'G','2'=>'F')    , "", "", "", "", "", "35"),true),
                array("Texte" ,false,"","",true),
                array("ListeD",$Adm,"LicEtabCode",array("seletab", "Etab...","SELECT EtabNum, EtabNomCourt, EtabNom, EtabVille FROM Etablissements ORDER BY EtabNum", Array("EtabNum", "EtabNomCourt", "EtabNom", "EtabVille"), Array("%06d", "", "", ""), "EtabNum","SELECT EtabNum FROM Etablissements WHERE EtabNomCourt = ","SELECT EtabCode FROM Etablissements WHERE EtabNum = ","200"),true),
                array("Texte" ,false,"","",false),
                array("Texte" ,false,"","",false),
                array("ListeS",$licence,"",array("LicAss", "Ass..."    ,array("0"=>"Non","1"=>"Oui"), "", "", "", "", "","45"),false),
                array("Texte" ,$licence,"","",false),
                array("Date"  ,$Adm && $licence,"","",false),
                array("Date"  ,$Adm && $licence,"","",false),
                array("Date"  ,$Adm && $licence,"","",false)
            );
            $ChampsInsert    = array(
                array("Texte" ,false,""             ,"","",true,false,false),
                array("Texte" ,false,""             ,"","",true,true,false),
                array("ListeS",true ,"",array("LicInscrit", "Inscrit..."    ,array('0'=>'Non','1'=>'Oui'), "", "", "", "","","45"),"",true,true,false),
                array("Texte" ,$Adm ,""             ,"","",true,true,false),
                array("Texte" ,true ,""             ,"","",true,true,false),
                array("Texte" ,true ,""             ,"","",true,true,false),
                array("Date"  ,true ,""             ,"","",true,true,false),
                array("ListeS",true ,"",array("LicSexCode", "Sexe..."       ,array('1'=>'G','2'=>'F')    , "", "", "", "", "", "35"),"",true,true,false),
                array("Texte" ,false,""             ,"","",true,true,false),
                array("ListeD",true,"LicEtabCode",array("seletab", "Etab...","SELECT EtabNum, EtabNomCourt, EtabNom, EtabVille FROM Etablissements ORDER BY EtabNum", Array("EtabNum", "EtabNomCourt", "EtabNom", "EtabVille"), Array("%06d", "", "", ""), "EtabNum","SELECT EtabNum FROM Etablissements WHERE EtabNomCourt = ","SELECT EtabCode FROM Etablissements WHERE EtabNum = ","200"),"$Etab",$Adm,true,false),
                array("Texte" ,false,""             ,"","",true,false,false),
                array("Texte" ,false,""             ,"","",true,false,false),
                array("ListeS",true ,"",array("LicAss", "Ass..."            ,array('0'=>'Non','1'=>'Oui'), "", "", "", "", "","45"),"",true,true,false),
                array("Texte" ,true ,""             ,"","",true,false,false),
                array("Date"  ,true ,""             ,"","NULL",true,false,false),
                array("Date"  ,true ,""             ,"","NULL",true,false,false),
                array("Date"  ,true ,""             ,"","NULL",true,false,false)
            );
            if ($licence == 1) $messagedel = ""; else $messagedel      = "Attention ! la suppression d'un licenci� entra�ne la suppression des participations de ce licenci�.";
            if ( (!($licence == 1)) && (isset($filter)) ) {
                $tabfilter = explode(" AND ", $filter);
                $counttab = count($tabfilter);
                for ($i = 0; $i < $counttab; $i++) {if ( is_int(strpos($tabfilter[$i],"LicAss")) || is_int(strpos($tabfilter[$i],"LicNomAss")) || is_int(strpos($tabfilter[$i],"LicDateAss")) || is_int(strpos($tabfilter[$i],"LicDateDem")) || is_int(strpos($tabfilter[$i],"LicDateValid")) ) unset($tabfilter[$i]);}
                $filter = implode(" AND ", $tabfilter);
            }
            GereData($tablename, $queryStr, $MaKey, $NomsColonnes, $ChampsTri, $ChampsAli, $ChampsFor, $ChampsAff, $action, $orderby, $Choix, $ChampsEdit, $ChampsInsert, $ChampsType, $ChampsTypeExt, $ChampsFiltre, $where, $ChampsNomFil, $ChampsRacFiltre, $ChampsRacParam,$sousqueryStr,$messagedel);

        } else {
            ConstruitStat(-2, 1, $queryStr, $NomsColonnes, $ChampsAli, $ChampsFor, $ChampsAff, $ChampsType, $Choix);
            GereData("", $queryStr, "", $NomsColonnes, "", $ChampsAli, $ChampsFor, $ChampsAff, $action, "", $Choix, "", "", $ChampsType, "", "", "", "", "", "", "", "", "", 1);
        }
    }

    if ($menu == "competitions") {

        if((!isset($Compet)) || (empty($Compet))) $Compet = 0;
        if((!isset($CompetEqu)) || (empty($CompetEqu))) $CompetEqu = 0;
        if(!isset($Lic)) $Lic = 0;
        if ($Compet == 0) $Where = "WHERE CompetEtat = '1'" ; else $Where = "WHERE CompetEtat = '1' And CompetCode = $Compet ";
        if (($licence == 0) && ($sousmenu == "licences")) {if ($Adm) $sousmenu = "references"; else $sousmenu = "individuels";}
        if ($licence == 1) $sousmenu = "licences";
        if ( (!isset($affcompet)) || (empty($affcompet)) ) $affcompet = "oui";
        if ($affcompet == "oui") $affcompettexte = " &nbsp; Masquer les autres comp�titions disponibles&nbsp;"; else $affcompettexte = " &nbsp; Afficher les autres comp�titions disponibles &nbsp;";
        if ($sousmenu == "equipes") $WhereComp = "AND CompetEqu = 1"; else $WhereComp = "";
        if ($Adm) { if ($affcompet == "nonfermees") $WhereEtat = " CompetEtat = 1 "; else $WhereEtat = " CompetEtat >= 0 ";} else $WhereEtat = " CompetEtat = 1 ";
        $CompetStatut ="Inscriptions ferm�es";
        $affgrillecompet = "non";
        $queryCompet = bf_mysql_query("SELECT CompetCode, CompetLibell�, DATE_FORMAT(CompetDateD�b,'%d/%m/%Y') AS CompetDateD�but, CompetLieu, CompetCalculAutoEqu, SpoLibell�, SpoCode, CompetEqu, CompetStatut, CompetEtat, CompetObs, Comp�titions.Ordre FROM Sports INNER JOIN Comp�titions ON Sports.SpoCode = Comp�titions.CompetSpoCode WHERE $WhereEtat $WhereComp ORDER BY Comp�titions.Ordre, CompetDateD�b DESC");
        if (!(!($queryCompet))) {
            $res = mysql_fetch_array($queryCompet);
            if ($res) {
                $affgrillecompet = "oui";
                $req = bf_mysql_query("SELECT CompetCode, CompetLibell�, DATE_FORMAT(CompetDateD�b,'%d/%m/%Y') AS CompetDateD�but, CompetLieu, CompetCalculAutoEqu, SpoLibell�, SpoCode, CompetEqu, CompetStatut, CompetEtat, CompetObs, Comp�titions.Ordre FROM Sports INNER JOIN Comp�titions ON Sports.SpoCode = Comp�titions.CompetSpoCode WHERE CompetCode = $Compet AND $WhereEtat $WhereComp ORDER BY Comp�titions.Ordre, CompetDateD�b DESC");
                $res1 = mysql_fetch_array($req);
                if ($res1) $res = $res1;
                if (!( (isset($BAjouter)) && ($menu == "competitions") && ($sousmenu == "references") )) $Compet = $res["CompetCode"];
                if (mysql_num_rows($queryCompet) > 1) $monnum = "1 -"; else $monnum = "";
                $TexteCompet = "<B>".$monnum."<BLINK>&nbsp;".$res["SpoLibell�"]." ->&nbsp; </BLINK> ".$res["CompetLibell�"]." - ".$res["CompetDateD�but"]." - ".$res["CompetLieu"]
                    ."<span class='pasimprimer'>";
                if (!($Consult)) $TexteCompet.= " - ".$res["CompetStatut"];
                if (!($res["CompetObs"]) == "") $TexteCompet.= " - ".$res["CompetObs"];
                $TexteCompet.= "</span></B>";
                $Req = bf_mysql_query("SELECT CompetCode, CompetLibell�, DATE_FORMAT(CompetDateD�b,'%d/%m/%Y') AS CompetDateD�but, CompetLieu, SpoLibell�, SpoCode, CompetEqu, CompetStatut, CompetEtat, CompetObs, Comp�titions.Ordre FROM Sports INNER JOIN Comp�titions ON Sports.SpoCode = Comp�titions.CompetSpoCode WHERE CompetCode <> $Compet AND $WhereEtat $WhereComp ORDER BY Comp�titions.Ordre, CompetDateD�b DESC");
                if (mysql_num_rows($Req) > 0) {
                    $TexteCompet .= "<span class='pasimprimer'><BR>&nbsp;&nbsp;&nbsp;<FONT Size=1> <a href=$PHP_SELF?action=VoirMenu&sousmenu=$sousmenu&affcompet=";
                    if ($affcompet == "oui") $TexteCompet .="non"; else $TexteCompet .="oui";
                    $TexteCompet .= "&Compet=$Compet&licence=$licence> $affcompettexte </a></FONT></span><HR CLASS = 'hr2'>";
                    if ( ($Adm) && ($affcompet == "oui") ) {
                        $TexteCompet .= "<span class='pasimprimer'>&nbsp;<FONT Size=1> <a href=$PHP_SELF?action=VoirMenu&menu=competitions&sousmenu=$sousmenu&affcompet=";
                        if ($affcompet == "oui") $TexteCompet .="nonfermees"; else $TexteCompet .="oui";
                        $TexteCompet .= "&Compet=$Compet&licence=$licence> &nbsp; Masquer les comp�titions non visibles par les utilisateurs &nbsp; </a></FONT></span><HR CLASS = 'hr2'>";
                    }
                } else $affcompet = "non";
                $Sport               = $res["SpoCode"];
                $CompetEqu			 = $res["CompetEqu"];
                $CompetCalculAutoEqu = $res["CompetCalculAutoEqu"];
                $CompetStatut        = $res["CompetStatut"];
            } else {
                $TexteCompet = "&nbsp; Aucune comp�tition disponible pour l'instant !";
                $affcompet = "non";
            }
        }

        $SpoGestion = TrouveSport($Compet, "SpoGestionPerf");
        $TxtSM = "";

        if (((!$Adm) && ($CompetEqu != 0)) || ($Adm) ) {
            $TxtSM.="<TABLE CLASS = 'tablesousmenu'><TR><TD>";
            $TxtSM.=" &nbsp;";
            if ($Adm) {$TxtSM.= "<a "; if ($sousmenu =="references") $TxtSM.= "CLASS = 'inv'"; $TxtSM.= "href='$PHP_SELF?action=VoirMenu&menu=competitions&sousmenu=references&Compet=$Compet&affcompet=$affcompet'  > &nbsp; R�f�rences  &nbsp; </a>"; $TxtSM.="|";}
            $TxtSM.= "<a "; if (($sousmenu == "individuels") || ($sousmenu =="individuels(2)")) $TxtSM.="CLASS = 'inv'"; $TxtSM.="href='$PHP_SELF?action=VoirMenu&menu=competitions&sousmenu=individuels&Compet=$Compet&affcompet=$affcompet&Etab=$Etab' > &nbsp; Individuels &nbsp; </a>";
            $montexteequipe = "";
            if (($SpoGestion ==  0) || ($SpoGestion == -2) || ($SpoGestion == -3) || ($SpoGestion == -4) || ($SpoGestion == -99)) $montexteequipe = "Equipes";
            if (($SpoGestion == -5)) $montexteequipe = "Relais";
            if (($SpoGestion == -1) || ($SpoGestion == -7)) $montexteequipe = "Relais - Equipes";
            if (($montexteequipe != "") && ($CompetEqu != 0) ){ $TxtSM.="|<a "; if ($sousmenu =="equipes") $TxtSM.="CLASS = 'inv'"; $TxtSM.="href='$PHP_SELF?action=VoirMenu&menu=competitions&sousmenu=equipes&Compet=$Compet&affcompet=$affcompet&Etab=$Etab'     > &nbsp; ".$montexteequipe." &nbsp; </a>";}
            $TxtSM.="</TD></TR></TABLE>";
        }

        if ($Consult) {
            if (isset($Etab)) $compEtab = "AND EtabNum = ".$Etab." "; else $compEtab = "";
            $selectEt1 = "SELECT DISTINCT Etablissements.* FROM Etablissements INNER JOIN Licenci�s ON Etablissements.EtabCode = Licenci�s.LicEtabCode INNER JOIN Participations ON Licenci�s.LicNumLicence = Participations.ParLicCode
					     WHERE Participations.ParCompetCode = $Compet $compEtab
						 UNION SELECT Etablissements.* FROM Etablissements INNER JOIN Equipes ON Etablissements.EtabCode = Equipes.EquEtabCode
						 WHERE Equipes.EquCompetCode = $Compet $compEtab
						 ORDER BY EtabNum";
            $selectEt2 = "SELECT DISTINCT Etablissements.* FROM Etablissements INNER JOIN Licenci�s ON Etablissements.EtabCode = Licenci�s.LicEtabCode INNER JOIN Participations ON Licenci�s.LicNumLicence = Participations.ParLicCode
					     WHERE Participations.ParCompetCode = $Compet
						 UNION SELECT Etablissements.* FROM Etablissements INNER JOIN Equipes ON Etablissements.EtabCode = Equipes.EquEtabCode
						 WHERE Equipes.EquCompetCode = $Compet
						 ORDER BY EtabNum";
            $querySelEtab = bf_mysql_query($selectEt1);
            if ((!($querySelEtab)) || (mysql_num_rows($querySelEtab) == 0) ) $querySelEtab = bf_mysql_query($selectEt2);
            if (!(!($querySelEtab))) {
                $resSelEtab = mysql_fetch_array($querySelEtab);
                if ($resSelEtab) {
                    $Etab = $resSelEtab['EtabNum'];
                    $sousR = "(".$selectEt2.") AS TempEtab";
                    $prem = mysql_fetch_array(bf_mysql_query("SELECT DISTINCT MIN(EtabNum) FROM ". $sousR ));$prem = $prem[0];
                    $prec = mysql_fetch_array(bf_mysql_query("SELECT DISTINCT MAX(EtabNum) FROM ". $sousR ." WHERE EtabNum <".$Etab));$prec = $prec[0];
                    $suiv = mysql_fetch_array(bf_mysql_query("SELECT DISTINCT MIN(EtabNum) FROM ". $sousR ." WHERE EtabNum >".$Etab));$suiv = $suiv[0];
                    $dern = mysql_fetch_array(bf_mysql_query("SELECT DISTINCT MAX(EtabNum) FROM ". $sousR .""));$dern = $dern[0];
                    echo "<span class='pasimprimer' ><BR><i>&nbsp;&nbsp;Choisissez un �tablissement dans la liste et cliquez sur <B>S�lectionner</B> :";
                    echo "</i></SPAN>";
                    echo "<form action='$PHP_SELF' method=post>\n";
                    echo "<TABLE CLASS ='tableselecteurEtab'>";
                    echo "<TR><TD>";
                    if( $Etab > $prem ) {
                        echo "<a Class='navig' href='$PHP_SELF?".$par."action=VoirMenu&Consult=true&sousmenu=$sousmenu&Compet=$Compet&Etab=$prem'><< </a>\n";
                        echo "<a Class='navig' href='$PHP_SELF?".$par."action=VoirMenu&Consult=true&sousmenu=$sousmenu&Compet=$Compet&Etab=$prec'>< </a>\n";
                    } else echo "<span class='pasimprimer'> << < </span>\n";
                    echo "&nbsp;&nbsp;";
                    listederoulante("SelecteurEtab", "", $selectEt2, array("EtabNum","-","EtabNomCourt","-","EtabNom","-","EtabVille"), array("%06s","","","","","",""), "EtabNum", $Etab, 400);
                    echo "&nbsp;&nbsp;<INPUT TYPE='submit' NAME='ValideSelecteurEtab' VALUE='S�lectionner' class='bouton'>&nbsp;&nbsp;";
                    if( $Etab < $dern) {
                        echo "<a Class='navig' href='$PHP_SELF?".$par."action=VoirMenu&Consult=true&sousmenu=$sousmenu&&Compet=$Compet&Etab=$suiv'>> </a>\n";
                        echo "<a Class='navig' href='$PHP_SELF?".$par."action=VoirMenu&Consult=true&sousmenu=$sousmenu&&Compet=$Compet&Etab=$dern'>>> </a>\n";
                    } else echo "<span class='pasimprimer' >> >> </span>\n";
                    echo "</TD></TR>";
                    echo "</TABLE></FORM>\n";
                }
            }
        }

        if ($Adm) echo $TxtSM;

        if (($sousmenu == "references") && ($Adm)) {
            If ($stat == 0) {
                $tablename     = "Comp�titions";
                $queryStr      = "SELECT CompetCode, CompetLibell�, CompetDateD�b, CompetLieu, SpoLibelCourt, CompetEqu, CompetCalculAutoEqu, CompetChpSup, CompetEtat, CompetStatut, CompetObs,
						  (SELECT COUNT(ParCode) FROM `Participations` where ParCompetCode = CompetCode) AS Ind, 
						  (SELECT COUNT(EquCode) FROM `Equipes` where EquCompetCode = CompetCode) AS Equ, 
						  Comp�titions.Ordre
						  FROM Sports INNER JOIN Comp�titions ON Sports.SpoCode = Comp�titions.CompetSpoCode";
                $where		   = "";
                if ((!isset($orderby)) || ($orderby == "")) $orderby = "Comp�titions.Ordre, CompetDateD�b DESC";
                $MaKey         = "CompetCode";
                $NomsColonnes  = array('Code','Libell�','Date','Lieu','Sport','Equ','Equ Auto','Options Colonnes','Etat','Statut','Obs','Ind','Equ','Num');
                $ChampsTri     = array('/','/','/','/','/','/','/','/','/','/','/','/','/','/');
                $ChampsAli     = array('center','','center','','','center','center','','center','center','center','center', 'center', 'center');
                $ChampsFor     = array('','', '', '','' , '', '', '', '','','','','','');
                $ChampsAff     = array(false,true,true,true,true,true,false,true,true,true,true,true,true,false);
                $Choix         = array("importer","exporter","ajout","modifier","filtrage","supprimer","monter","descendre","stat");
                $ChampsType    = array("Texte","Texte","Texte","Texte","Texte", "ListeS", "ListeS","Texte","ListeS","ListeS","Texte","Texte","Texte","Texte");
                $ChampsTypeExt = array('', '', '', '' , '', array("0"=>"Non","1"=>"Oui"), array("0"=>"Non","1"=>"Oui"),'',array("0"=>"Masquer","1"=>"Afficher"), array('Inscriptions ferm�es'=>'Inscriptions ferm�es','Inscriptions ouvertes'=>'Inscriptions ouvertes'),'','','','');
                $ChampsFiltre  = array(true,true,true,true,true,true,true,false,true,true,true,false,false,false,false);
                $ChampsNomFil  = array('', '', '', '', '' , '', '', '', '','','','','','');
                $ChampsRacFiltre = array(false,false,false,false,true,true,true,false,true,true,false,false,false,false);
                $ChampsRacParam  = array(array(2,'competitions','individuels','Compet',0,'->'));
                $sousqueryStr    = "";
                $ChampsEdit      = array(
                    array("Texte" ,true ,"","",true),
                    array("Texte" ,true ,"","",true),
                    array("Texte" ,true ,"","",true),
                    array("Texte" ,true ,"","",true),
                    array("ListeD",true ,"CompetSpoCode",array("selsport", "Sport...", "SELECT SpoLibelCourt FROM Sports WHERE SpoGestionPerf <= 0 ORDER BY Ordre", Array("SpoLibelCourt"), Array(""), "SpoLibelCourt", "","Select SpoCode From Sports WHERE SpoLibelCourt =","100"),true),
                    array("ListeS",true ,"",array("CompetEqu", "Equ ..."       ,array('0'=>'Non','1'=>'Oui')    , "", "", "", "", "", "65"),true),
                    array("ListeS",false ,"",array("CompetCalculAutoEqu", "Composition Auto ...",array('0'=>'Non','1'=>'Oui')    , "", "", "", "", "", "160"),true),
                    array("Texte" ,true,"","",false),
                    array("ListeS",true ,"",array("CompetEtat"  , "Etat..."       ,array('0'=>'Masquer','1'=>'Afficher')    , "", "", "", "", "", "65"),true),
                    array("ListeS",true ,"",array("CompetStatut", "Statut..."     ,array('Inscriptions ferm�es'=>'Inscriptions ferm�es','Inscriptions ouvertes'=>'Inscriptions ouvertes')    , "", "", "", "", "", "120"),true),
                    array("Texte" ,true ,"","",false),
                    array("Texte" ,false,"","",false),
                    array("Texte" ,false,"","",false),
                    array("Texte" ,true,"","",true)
                );
                $ChampsInsert    = array(
                    array("Texte" ,true ,"","", array("Max","CompetCode"), true,true,false),
                    array("Texte" ,true ,"","","",true,true,false),
                    array("Texte" ,true ,"","","",true,true,false),
                    array("Texte" ,true ,"","","",true,true,false),
                    array("ListeD",true ,"CompetSpoCode",array("selsport", "Sport...", "SELECT SpoLibelCourt FROM Sports WHERE SpoGestionPerf <= 0 ORDER BY Ordre", Array("SpoLibelCourt"), Array(""), "SpoLibelCourt", "","Select SpoCode From Sports WHERE SpoLibelCourt =","100"),"",true,true,false),
                    array("ListeS",true ,"",array("CompetEqu", "Equ ..."       ,array('0'=>'Non','1'=>'Oui'), "", "", "", "", "", "65"),"",true,true,false),
                    array("ListeS",false ,"",array("CompetCalculAutoEqu", "Composition Auto ..."       ,array('0'=>'Non','1'=>'Oui')    , "", "", "", "", "", "160"),"",true,true,false),
                    array("Texte" ,true,"","","",true,false,false),
                    array("ListeS",true ,"",array("CompetEtat"  , "Etat..."       ,array('0'=>'Masquer','1'=>'Afficher')    , "", "", "", "", "", "65"),"",true,true,false),
                    array("ListeS",true ,"",array("CompetStatut", "Statut..."     ,array('Inscriptions ferm�es'=>'Inscriptions ferm�es','Inscriptions ouvertes'=>'Inscriptions ouvertes')    , "", "", "", "", "", "120"),"",true,true,false),
                    array("Texte" ,true ,"","","",true,false,false),
                    array("Texte" ,false,"","","",false,false,false),
                    array("Texte" ,false,"","","",false,false,false),
                    array("Texte" ,true ,"","","0",true,true,false)
                );
                $messagedel      = "Attention ! la suppression d'une comp�tition entra�ne la suppression des participations de cette comp�tition.";
                $MajChpOrdre     = array(array("Comp�titions","Ordre"));
                GereData($tablename, $queryStr, $MaKey, $NomsColonnes, $ChampsTri, $ChampsAli, $ChampsFor, $ChampsAff, $action, $orderby, $Choix, $ChampsEdit, $ChampsInsert, $ChampsType, $ChampsTypeExt, $ChampsFiltre, $where, $ChampsNomFil, $ChampsRacFiltre, $ChampsRacParam,$sousqueryStr,$messagedel,$MajChpOrdre);
            } else {
                ConstruitStat(-1, 1, $queryStr, $NomsColonnes, $ChampsAli, $ChampsFor, $ChampsAff, $ChampsType, $Choix);
                GereData("", $queryStr, "", $NomsColonnes, "", $ChampsAli, $ChampsFor, $ChampsAff, $action, "", $Choix, "", "", $ChampsType, "", "", "", "", "", "", "", "", "", 1);
            }
        }

        if ((!(!($queryCompet))) && (($sousmenu == "individuels") || ($sousmenu == "equipes") || ($sousmenu == "individuels(2)") || ($sousmenu == "licences"))) {
            echo "<TABLE CLASS = 'tablecompet'>";
            echo"<TR CLASS = 'trcompet'><TD>";
            echo $TexteCompet;
            echo"</TD></TR>";
            echo"</TABLE>";
            if ($affgrillecompet == "non") echo "<BR><BR>";
            if (($affcompet == "oui") || ($affcompet == "nonfermees")){
                echo "<TABLE CLASS = 'tablecompet2'>";
                if (!($Adm)) echo "<TR CLASS = 'trcompet2'><TD><I>&nbsp; Autres comp�titions disponibles (cliquez sur la comp�tition de votre choix) : </I></TD></TR><TR>";
                if ($res = mysql_fetch_array($Req)) {
                    $monnum = 1;
                    do {
                        $monnum = $monnum + 1;
                        echo "<TR CLASS = 'trcompet2'>";
                        echo "<TD>";
                        echo "<a href=$PHP_SELF?action=VoirMenu&tablename=$tablename&selection=0&sousmenu=individuels&Compet=".$res["CompetCode"]."&Etab=$Etab&Lic=$Lic> &nbsp; ".$monnum." - ".$res["SpoLibell�"]." - ".$res["CompetLibell�"]." - ".$res["CompetDateD�but"]." - ".$res["CompetLieu"];
                        if (!($Consult)) echo " - ".$res["CompetStatut"];
                        if (!($res["CompetObs"]) == "") echo " - ".$res["CompetObs"];
                        echo "&nbsp;&nbsp;&nbsp; </a></TD>";
                        echo "</TR>";
                    } while ($res = mysql_fetch_array($Req));
                }
                echo "</TABLE>";
            }
        }

        if (!($Adm)) echo $TxtSM;

        $tabChp = array_merge(explode("//", TrouveSport($Compet, "CompetChpSup")), array('',''));

        if ( ($sousmenu == "individuels(2)") && (in_array("-Epr", array_merge(explode("/", $tabChp[0]), array('','','','','')))) ) $sousmenu = "individuels";

        if (($sousmenu == "individuels") && ($affgrillecompet == "oui")) {
            If ($stat == 0) {
                $tabChpSup = array_merge(explode("/", $tabChp[0]), array('','','','','','','','',''));
                for ($i = 0; $i < count($tabChpSup); $i++) $tabChpSup[$i] = trim($tabChpSup[$i]);
                $affQualif = false;
                if (in_array("Qualif", $tabChpSup)) {
                    $affQualif = true;
                    unset($tabChpSup[array_search("Qualif", $tabChpSup)]);
                    $tabChpSup = array_values($tabChpSup);
                }
                $affEpr = true;
                if (in_array("-Epr", $tabChpSup)) {
                    $affEpr = false;
                    unset($tabChpSup[array_search("-Epr", $tabChpSup)]);
                    $tabChpSup = array_values($tabChpSup);
                }
                $affQuad = false;
                if (in_array("+Quad", $tabChpSup)) {
                    $affQuad = true;
                    unset($tabChpSup[array_search("-Quad", $tabChpSup)]);
                    $tabChpSup = array_values($tabChpSup);
                }

                $strCatEpr = "";
                for ($i = 0; $i < count($tabChpSup); $i++) {
                    $monChpSup = explode("#", $tabChpSup[$i]);
                    if (in_array("CatEpr", $monChpSup)) {
                        unset($tabChpSup[$i]);
                        $tabChpSup = array_values($tabChpSup);
                        if (array_key_exists('1', $monChpSup)) {
                            $Valeurs = explode(";", $monChpSup[1]);
                            if (count($Valeurs) > 0) {
                                for ($j = 0; $j < count($Valeurs); $j++) {
                                    if (!($strCatEpr == "")) $strCatEpr = $strCatEpr." OR ";
                                    switch($Valeurs[$j]) {
                                        case "Open": $strCatEpr = $strCatEpr."EprLibelCourt LIKE '%Open%'"; Break;
                                        case "Pr�" : $strCatEpr = $strCatEpr."EprLibelCourt LIKE '%Pr� inscrit%'"; Break;
                                        default    : $strCatEpr = $strCatEpr."CatLibelCourt = '".$Valeurs[$j] ."'";
                                    }
                                }
                                if (!($strCatEpr == "")) $strCatEpr = " AND (".$strCatEpr.")";
                            }
                        }
                    }
                }

                $maxInsc = 9999;
                for ($i = 0; $i < count($tabChpSup); $i++) {
                    $monChpSup = explode("#", $tabChpSup[$i]);
                    if (in_array("Max", $monChpSup)) {
                        unset($tabChpSup[$i]);
                        $tabChpSup = array_values($tabChpSup);
                        if (array_key_exists('1', $monChpSup)) $maxInsc = $monChpSup[1];
                        if ($monChpSup[1] == 0) $maxInsc = 9999;
                    }
                }

                $CEprU = "";
                if ($affEpr == false) {
                    $reqU = bf_mysql_query("SELECT CatLibelCourt, COUNT(EprLibelCourt) AS Nbre FROM Cat�gories LEFT JOIN Epreuves ON CatCode = EprCatCode Where CatSpoCode = ".TrouveSport($Compet, "SpoCode")." GROUP BY CatLibelCourt HAVING Nbre > 1 ");
                    if (!(!$reqU)) {
                        $resU = mysql_fetch_array($reqU);
                        if (!(!$resU)) {
                            $reqeprU = bf_mysql_query("SELECT EprCompetCode, EprLibelCourt FROM Epreuves INNER JOIN Cat�gories ON Cat�gories.CatCode = Epreuves.EprCatCode INNER JOIN `Epreuves Comp�titions` ON Epreuves.EprCode = `Epreuves Comp�titions`.EprCompetEprCode LEFT JOIN Groupes ON Groupes.GrCode = Epreuves.EprGrCode WHERE `Epreuves Comp�titions`.EprCompetCompetCode = $Compet
										       AND EprLibelCourt LIKE 'Pr� Inscrit%' AND IF(CatSpoCode = 10, TRUE, Cat�gories.CatSexCode = 
										     ( SELECT LicSexCode FROM Licenci�s WHERE LicNumLicence = $ParLicCode LIMIT 1))  
										       ORDER BY Epreuves.Ordre");
                        } else {
                            $reqeprU = bf_mysql_query("SELECT EprCompetCode, EprLibelCourt FROM Epreuves INNER JOIN Cat�gories ON Cat�gories.CatCode = Epreuves.EprCatCode INNER JOIN `Epreuves Comp�titions` ON Epreuves.EprCode = `Epreuves Comp�titions`.EprCompetEprCode LEFT JOIN Groupes ON Groupes.GrCode = Epreuves.EprGrCode WHERE `Epreuves Comp�titions`.EprCompetCompetCode = $Compet
											   AND Cat�gories.CatLibelCourt =
											 ( SELECT CatLibelCourt FROM Cat�gories, Licenci�s WHERE ( (Licenci�s.LicNaissance Between CatDateD�b And CatDateFin) And LicSexCode = CatSexCode And CatSpoCode = $Sport) AND LicNumLicence = $ParLicCode ORDER BY Cat�gories.Ordre LIMIT 1) 
										       ORDER BY Epreuves.Ordre");
                        }
                        if (!(!$reqeprU)) {
                            $reseprU = mysql_fetch_array($reqeprU);
                            if (!(!$reseprU)) $CEprU = $reseprU['EprCompetCode'];
                        }
                    }
                }

                for ($i = 1; $i <= 5; $i++) {$NomChp{$i} = ""; $Type{$i} = "Texte"; $List{$i} = ""; $PasVide{$i} = false;}
                for ($i = 0; $i < count($tabChpSup); $i++) {
                    $Sup = explode("#", $tabChpSup[$i]);
                    $num = $i + 1;
                    $NomChpSup{$num} = trim($Sup[0]);
                    if (strpos($NomChpSup{$num},"!") === 0) {
                        $PasVide{$num} = true;
                        $NomChpSup{$num} = substr($NomChpSup{$num},1) ;
                    }
                    if (array_key_exists('2', $Sup)) { $Sup[2] = trim($Sup[2]); if (!(is_numeric($Sup[2]))) $Sup[2] = ""; else if ($Sup[2] < 100) $Sup[2] = "";} else $Sup[2] = "";
                    if (array_key_exists('1', $Sup)) {
                        $Valeurs = explode(";", $Sup[1]);
                        if (count($Valeurs) > 0) {
                            $ListeValeurs = "";
                            for ($j = 0; $j < count($Valeurs); $j++) {
                                if (strpos($Valeurs[$j],"@") !== false) {
                                    $ValeursTO = explode("@", $Valeurs[$j]);

                                    $prefixe = '';
                                    $deb = strpos($ValeursTO[0],"[");
                                    if ($deb !== false) {
                                        $fin = strpos($ValeursTO[0],"]",$deb);
                                        if ($fin !== false) {
                                            $prefixe = substr($ValeursTO[0],$deb+1,$fin-1);
                                            $ValeursTO[0] = substr($ValeursTO[0],$fin+1,strlen($ValeursTO[0]));
                                        }
                                    }
                                    $suffixe = '';
                                    $deb = strpos($ValeursTO[1],"[");
                                    if ($deb !== false) {
                                        $fin = strpos($ValeursTO[1],"]",$deb);
                                        if ($fin !== false) {
                                            $suffixe = substr($ValeursTO[1],$deb+1,$fin-3);
                                            $ValeursTO[1] = substr($ValeursTO[1],0,$deb-1);
                                        }
                                    }

                                    $ValeursTO[0] = trim($ValeursTO[0]); $ValeursTO[1] = trim($ValeursTO[1]);
                                    if ( (is_numeric($ValeursTO[0])) && (is_numeric($ValeursTO[1])) ) {
                                        for ($k = $ValeursTO[0]; $k <= $ValeursTO[1]; $k++) {
                                            $ListeValeurs[$prefixe.$k.$suffixe] = $prefixe.$k.$suffixe;
                                            if (array_key_exists('2', $ValeursTO)) { $ValeursTO[2] = trim($ValeursTO[2]); if (is_numeric($ValeursTO[2])) $k = $k + $ValeursTO[2]-1; }
                                        }
                                    }
                                } else if ($Valeurs[$j] != "") $ListeValeurs[$Valeurs[$j]] = $Valeurs[$j];
                            }
                            if ($ListeValeurs != "") { $List{$num} = array("", $NomChpSup{$num}."...", $ListeValeurs, "", "", "", "", "", $Sup[2]); $Type{$num} = "ListeD";}
                        }
                    }
                }

                if ( (!($Consult)) && isset($_GET['ParLicCode']) && ($aj == True) && ($selectionner == true) && (strlen($_POST['ParLicCode'])==0) ) $_POST['ParLicCode'] = $_GET['ParLicCode'];

                $tablename  = "Participations";
                $queryStr        ="SELECT EtabNomCourt, ParCode, ParLicCode, Licenci�s.LicNom, Licenci�s.LicPr�nom, Licenci�s.LicNaissance, Licenci�s.LicSexCode, CatLibelCourt, EprLibelCourt, ParCompetCode, ParEprCode, ParQuadra, EquNum, ParPerfQualif, ParObs1, ParObs2, ParObs3, ParObs4, ParObs5 
						   FROM Cat�gories, Etablissements INNER JOIN Licenci�s ON Etablissements.EtabCode = Licenci�s.LicEtabCode INNER JOIN Participations ON Licenci�s.LicNumLicence = Participations.ParLicCode INNER JOIN `Epreuves Comp�titions` ON `Epreuves Comp�titions`.EprCompetCode = Participations.ParEprCode INNER JOIN Epreuves ON Epreuves.EprCode = `Epreuves Comp�titions`.EprCompetEprCode LEFT JOIN Equipes ON Equipes.EquCode = Participations.ParEquCode ";
                $where = "(((Licenci�s.LicNaissance Between CatDateD�b And CatDateFin) And LicSexCode = CatSexCode And CatSpoCode = 1)";
                if (!$Adm) $where .= " AND ParCompetCode = $Compet AND (EtabNum = $Etab".RetAS($Etab)."))"; else $where .= " AND ParCompetCode = $Compet)";
                if ((!isset($orderby)) || ($orderby == "")) $orderby = "LicNom, LicPr�nom, Epreuves.Ordre";
                $MaKey           = "ParCode";
                $NomsColonnes    = array('Etab','Code','Num�ro','Nom','Pr�nom','Naissance','Sexe','Cat','Epreuve','Compet','Epr','Quad','Equ','Qualif', $NomChpSup{1},$NomChpSup{2},$NomChpSup{3}, $NomChpSup{4}, $NomChpSup{5} );
                $ChampsTri       = array('EtabNomCourt, LicNom, LicPr�nom, Epreuves.Ordre','ParCode','LicNumLicence, Epreuves.Ordre','LicNom, LicPr�nom, Epreuves.Ordre','LicPr�nom, LicNom, Epreuves.Ordre','LicNaissance, LicNom, LicPr�nom, Epreuves.Ordre','LicSexCode, LicNom, LicPr�nom, Epreuves.Ordre','LicSexCode DESC, LicNaissance DESC, LicNom, LicPr�nom, Epreuves.Ordre', 'Epreuves.Ordre, LicNom, LicPr�nom','','','ParQuadra, Epreuves.Ordre, LicNom, LicPr�nom' ,'EquNum, LicNom, LicPr�nom','ParPerfQualif, LicNumLicence, LicNom, LicPr�nom', 'ParObs1, LicNumLicence, LicNom, LicPr�nom','ParObs2, LicNumLicence, LicNom, LicPr�nom','ParObs3, LicNumLicence, LicNom, LicPr�nom','ParObs4, LicNumLicence, LicNom, LicPr�nom','ParObs5, LicNumLicence, LicNom, LicPr�nom');
                $ChampsAli       = array('center','','center','','','center','center','center','center','','','center','center','right','center','center','center','center','center');
                $ChampsFor       = array('','','%010s','','','','','','','','','','','','','','','','');
                $ChampsAff       = array(true,false,true,true,true,true,true,true,$affEpr,false,false,(($SpoGestion == -1) && ($affQuad) ),(($SpoGestion != -5) && ($CompetEqu)),$affQualif,($tabChpSup[0] != ""),($tabChpSup[1] != ""),($tabChpSup[2] != ""),($tabChpSup[3] != ""),($tabChpSup[4] != ""));
                $ChampsType      = array("Texte","Texte","ListeD","Texte","Texte","Date","ListeS","Texte","ListeD","Texte",'Texte',"ListeS",'Texte','Perf','Texte','Texte','Texte','Texte','Texte');
                $ChampsTypeExt   = array("","","","","","",array('1'=>'G','2'=>'F'),"","","","",array('0'=>'Non','1'=>'Oui'),"","","","","","","");
                $ChampsFiltre    = array(true,true,true,true,true,true,true,true,true,false,false,($SpoGestion == -1),(($SpoGestion != -5) && ($CompetEqu)),true,true,true,true,true,true);
                $ChampsNomFil    = array("","","","","","","","","","","","","","","","","","","");
                $ChampsRacFiltre = array(true,false,true,false,false,false,true,true,true,false,false,true,true,false,false,false,false,false,false);
                $ChampsRacParam  = "";
                $sousqueryStr    = "";
                if (($Adm) || ($CompetStatut == "Inscriptions ouvertes")) {
                    $Choix = array("ajout","supprimer","filtrage","exporter");
                    if ( ( ((!($Adm)) && (TrouveParamweb("ImpressionLic") == 1)) || (($Adm) && (TrouveParamweb("ImpressionLic") > 0)) ) || (TrouveParamweb("InscriptionLic") > 0)) array_push($Choix,"licence");
                    if ($ChampsAff[8] || $ChampsAff[11] || $ChampsAff[12] || $ChampsAff[13] || $ChampsAff[14] || $ChampsAff[15] || $ChampsAff[16]) array_push($Choix, "modifier");
                } else $Choix = array("filtrage","exporter");
                if ($Adm) { array_push($Choix, "suppressiontout"); array_push($Choix, "stat");}
                if ($Consult) $Choix = array("consultation");
                if ($Adm) { $Clause1 = ""; $Clause2 = ""; } else { $Clause1 = "WHERE EtabNum = $Etab"; $Clause2 = "AND EtabNum = $Etab";}
                if ($SpoGestion == -4) $chpcat = "Chall"; else $chpcat = "CatLibelCourt";
                $ChampsEdit      = array(
                    array("Texte" ,false,"","",false),
                    array("Texte" ,false,"","",false),
                    array("Texte" ,false,"","",false),
                    array("Texte" ,false,"","",false),
                    array("Texte" ,false,"","",false),
                    array("Date"  ,false,"","",false),
                    array("Texte" ,false,"","",false),
                    array("Texte" ,false,"","",false),
                    array("ListeD", $affEpr, "ParEprCode",
                        array("selepr",
                            "Epreuve...",
                            "SELECT EprCompetCode, EprLibelCourt, EprLibell� 
									  FROM Epreuves INNER JOIN Cat�gories ON Cat�gories.CatCode = Epreuves.EprCatCode INNER JOIN `Epreuves Comp�titions` ON Epreuves.EprCode = `Epreuves Comp�titions`.EprCompetEprCode LEFT JOIN Groupes ON Groupes.GrCode = Epreuves.EprGrCode
									  WHERE (Groupes.GrLibelCourt < 9 OR Groupes.GrLibelCourt IS NULL) AND `Epreuves Comp�titions`.EprCompetCompetCode = ".$Compet.$strCatEpr." ORDER BY Epreuves.Ordre",
                            Array("EprLibelCourt", "-", "EprLibell�"),
                            Array("", "", ""),
                            "EprCompetCode",
                            "SELECT EprCompetCode FROM Epreuves INNER JOIN `Epreuves Comp�titions` ON Epreuves.EprCode = `Epreuves Comp�titions`.EprCompetEprCode WHERE EprCompetCompetCode = $Compet AND EprLibelCourt=",
                            "",
                            "150"),
                        true),
                    array("Texte" ,true ,"ParCompetCode","",true),
                    array("Texte" ,false,"","",false),
                    array("ListeS", ($SpoGestion == -1), "",array("ParQuadra", "Quad...",array('0'=>'Non','1'=>'Oui'), "", "", "", "","","45"),false),
                    array("ListeD",(($SpoGestion != -5) && ($CompetEqu)) ,"ParEquCode",
                        array("selequ",
                            "Equipe...",
                            "SELECT EquCode, EquNum, EquCompl�ment, EtabNum, EtabNomCourt, EtabNom, EtabVille, CatLibelCourt, IF(EquChall=1, 'Courses', IF(EquChall = 2, 'Sauts', IF(EquChall = 3, 'Lancers',''))) AS Chall 
									  FROM Etablissements, Cat�gories INNER JOIN Equipes ON Cat�gories.CatCode = Equipes.EquCatCode LEFT JOIN `Epreuves Comp�titions` ON Equipes.EquEprCompetCode = `Epreuves Comp�titions`.EprCompetCode LEFT JOIN Epreuves ON `Epreuves Comp�titions`.EprCompetEprCode = Epreuves.EprCode 
									  WHERE Etablissements.EtabCode = Equipes.EquEtabCode AND EquCompetCode = $Compet $Clause2 ORDER BY EquNum",
                            Array("EquNum", "-", $chpcat, "-", "EtabNomCourt", "EquCompl�ment"),
                            Array("", "", "", "", "", ""),
                            "EquCode",
                            "SELECT EquCode FROM Equipes WHERE EquCompetCode = $Compet AND EquNum = ",
                            "",
                            "100"),
                        false),
                    array("Texte" ,true,"","",false),
                    array($Type{1}, true, "", $List{1}, $PasVide{1}),
                    array($Type{2}, true, "", $List{2}, $PasVide{2}),
                    array($Type{3}, true, "", $List{3}, $PasVide{3}),
                    array($Type{4}, true, "", $List{4}, $PasVide{4}),
                    array($Type{5}, true, "", $List{5}, $PasVide{5})
                );
                $ChampsInsert    = array(
                    array("Texte" ,false,""             ,"","",true,false,false),
                    array("Texte" ,false,""             ,"","",true,false,false),
                    array("Texte" ,true, ""             ,"","",true,false,true),
                    array("Texte" ,false,""             ,"","",true,false,false),
                    array("Texte" ,false,""             ,"","",true,false,false),
                    array("Date"  ,false,""             ,"","",true,false,false),
                    array("Texte" ,false,""             ,"","",true,false,false),
                    array("Texte" ,false,""             ,"","",true,false,false),
                    array("ListeD",true ,"ParEprCode"   ,
                        array(
                            "selepr",
                            "Epreuve...",
                            "SELECT EprCompetCode, EprLibelCourt, EprLibell� 
										FROM Epreuves INNER JOIN Cat�gories ON Cat�gories.CatCode = Epreuves.EprCatCode INNER JOIN `Epreuves Comp�titions` ON Epreuves.EprCode = `Epreuves Comp�titions`.EprCompetEprCode LEFT JOIN Groupes ON Groupes.GrCode = Epreuves.EprGrCode
										WHERE (Groupes.GrLibelCourt < 9 OR Groupes.GrLibelCourt IS NULL) AND `Epreuves Comp�titions`.EprCompetCompetCode = ".$Compet.$strCatEpr." ORDER BY Epreuves.Ordre",
                            Array("EprLibelCourt", "-", "EprLibell�"),
                            Array("", "", ""),
                            "EprCompetCode",
                            "",
                            "",
                            "150"
                        ),
                        $CEprU, $affEpr, true, false),
                    array("Texte" ,true ,"ParCompetCode","","$Compet",true, false,false),
                    array("Texte" ,false,""             ,"","",true, false,false),
                    array("ListeS",($SpoGestion == -1) ,"",array("ParQuadra", "Quad..."    ,array('0'=>'Non','1'=>'Oui'), "", "", "", "","","45"),"",($SpoGestion == -1), false,false),
                    array("ListeD",(($SpoGestion != -5) && ($CompetEqu)) ,"ParEquCode",
                        array("selequ",
                            "Equipe...",
                            "SELECT EquCode, EquNum, EquCompl�ment, EquChall, EtabNum, EtabNomCourt, EtabNom, EtabVille, CatLibelCourt, IF(EquChall = 1, 'Courses', IF(EquChall = 2, 'Sauts', IF(EquChall = 3, 'Lancers',''))) AS Chall 
									  FROM Etablissements, Cat�gories INNER JOIN Equipes ON Cat�gories.CatCode = Equipes.EquCatCode LEFT JOIN `Epreuves Comp�titions` ON Equipes.EquEprCompetCode = `Epreuves Comp�titions`.EprCompetCode LEFT JOIN Epreuves ON `Epreuves Comp�titions`.EprCompetEprCode = Epreuves.EprCode 
									  WHERE Etablissements.EtabCode = Equipes.EquEtabCode AND EquCompetCode = $Compet $Clause2 ORDER BY EquNum",
                            Array("EquNum", "-", $chpcat, "-", "EtabNomCourt", "EquCompl�ment"),
                            Array("", "", "", "", "", ""),
                            "EquCode",
                            "SELECT EquCode FROM Equipes WHERE EquCompetCode = $Compet AND EquNum = ",
                            "",
                            "100"),
                        "",true,false,false),
                    array("Texte" ,true,"","","",true,false,false),
                    array($Type{1},true,"",$List{1},"",true,$PasVide{1},false),
                    array($Type{2},true,"",$List{2},"",true,$PasVide{2},false),
                    array($Type{3},true,"",$List{3},"",true,$PasVide{3},false),
                    array($Type{4},true,"",$List{4},"",true,$PasVide{4},false),
                    array($Type{5},true,"",$List{5},"",true,$PasVide{5},false)
                );
                GereData($tablename, $queryStr, $MaKey, $NomsColonnes, $ChampsTri, $ChampsAli, $ChampsFor, $ChampsAff, $action, $orderby, $Choix, $ChampsEdit, $ChampsInsert, $ChampsType, $ChampsTypeExt, $ChampsFiltre, $where, $ChampsNomFil, $ChampsRacFiltre, $ChampsRacParam, $sousqueryStr,"","","",$strCatEpr, $maxInsc);
            } else {
                if ($stat == 1) $SportCat = 1; else $SportCat = $Sport;
                ConstruitStat($Compet, $SportCat, $queryStr, $NomsColonnes, $ChampsAli, $ChampsFor, $ChampsAff, $ChampsType, $Choix);
                GereData("", $queryStr, "", $NomsColonnes, "", $ChampsAli, $ChampsFor, $ChampsAff, $action, "", $Choix, "", "", $ChampsType, "", "", "", "", "", "", "", "", "", $stat);
            }
        };

        if (($sousmenu == "individuels(2)")) {
            $tablename  = "Participations";
            $queryStr        ="SELECT DISTINCT EtabNomCourt, ParLicCode, LicNom, LicPr�nom, LicNaissance, LicSexCode, CatLibelCourt, ParCompetCode
						   FROM Cat�gories, Etablissements INNER JOIN Licenci�s ON Etablissements.EtabCode = Licenci�s.LicEtabCode INNER JOIN Participations ON Licenci�s.LicNumLicence = Participations.ParLicCode ";
            $where = "(((Licenci�s.LicNaissance Between CatDateD�b And CatDateFin) And LicSexCode = CatSexCode And CatSpoCode = 1) ";
            if (!$Adm) $where .= " And ParCompetCode = $Compet AND (EtabNum = $Etab".RetAS($Etab)."))"; else $where .= " And ParCompetCode = $Compet)";
            if ((!isset($orderby)) || ($orderby == "")) $orderby = "LicNom, LicPr�nom";
            $MaKey           = "ParCode";
            $NomsColonnes    = array('Etab','Num�ro','Nom','Pr�nom','Naissance','Sexe','Cat','Compet');
            $ChampsTri       = array('EtabNomCourt, LicNom, LicPr�nom','LicNumLicence','LicNom, LicPr�nom','LicPr�nom, LicNom','LicNaissance, LicNom, LicPr�nom','LicSexCode, LicNom, LicPr�nom','LicSexCode DESC, LicNaissance DESC, LicNom, LicPr�nom','','ParObs1, LicNumLicence, LicNom, LicPr�nom','ParObs2, LicNumLicence, LicNom, LicPr�nom','ParObs3, LicNumLicence, LicNom, LicPr�nom');
            $ChampsAli       = array('center','center','','','center','center','center','center');
            $ChampsFor       = array('','%010s','','','','','','');
            $ChampsAff       = array((($Adm) || ($Consult)),true,true,true,true,true,true,false);
            $ChampsType      = array("Texte","ListeD","Texte","Texte","Date","ListeS","Texte","Texte");
            $ChampsTypeExt   = array("","","","","",array('1'=>'G','2'=>'F'),"","");
            $ChampsFiltre    = array(true,true,true,true,true,true,true,true);
            $ChampsNomFil    = array("","","","","","","","");
            $ChampsRacFiltre = array(true,true,false,false,false,true,true,true);
            $ChampsRacParam  = array(array(1,'competitions','individuels','ParLicCode',1,'->'));
            if ($Consult) $Choix = array("consultation"); else $Choix = array("filtrage");
            if ($Adm) { $Clause1 = ""; $Clause2 = ""; } else { $Clause1 = "WHERE EtabNum = $Etab"; $Clause2 = "AND EtabNum = $Etab";}
            $ChampsEdit      = "";
            $ChampsInsert    = "";
            $sousqueryStr    = array("SELECT EprLibelCourt, ParQuadra, EquNum, IF(EquNum > 0,CONCAT('Eq',INSERT(EquNum, 1, LENGTH(EtabNum), '')),NULL) AS EquNumero
						          FROM Etablissements INNER JOIN Licenci�s ON Etablissements.EtabCode = Licenci�s.LicEtabCode INNER JOIN Participations ON Licenci�s.LicNumLicence = Participations.ParLicCode INNER JOIN `Epreuves Comp�titions` ON `Epreuves Comp�titions`.EprCompetCode = Participations.ParEprCode INNER JOIN Epreuves ON Epreuves.EprCode = `Epreuves Comp�titions`.EprCompetEprCode LEFT JOIN Equipes ON Equipes.EquCode = Participations.ParEquCode
								  WHERE ParCompetCode = $Compet And ParLicCode = ",
                "Epreuves", "ParLicCode", Array(Array("EprLibelCourt","","","",False),array("ParQuadra","==",1,"Qd","False"),array("EquNum",">",0,"EquNumero",True)));
            if (isset($filter)) {
                $tabfilter = explode(" AND ", $filter);
                $counttab = count($tabfilter);
                for ($i = 0; $i < $counttab; $i++) {if ( is_int(strpos($tabfilter[$i],"EprLibelCourt")) || is_int(strpos($tabfilter[$i],"EquNum"))) unset($tabfilter[$i]);}
                $filter = implode(" AND ", $tabfilter);
            }
            if (isset($orderby)) {
                $taborderby = explode(",", $orderby);
                $counttab = count($taborderby);
                for ($i = 0; $i < $counttab; $i++) { if ( is_int(strpos($taborderby[$i],"Epreuves")) || is_int(strpos($taborderby[$i],"EquNum"))) unset($taborderby[$i]);}
                $orderby = implode(",", $taborderby);
            }
            GereData($tablename, $queryStr, $MaKey, $NomsColonnes, $ChampsTri, $ChampsAli, $ChampsFor, $ChampsAff, $action, $orderby, $Choix, $ChampsEdit, $ChampsInsert, $ChampsType, $ChampsTypeExt, $ChampsFiltre, $where, $ChampsNomFil, $ChampsRacFiltre, $ChampsRacParam, $sousqueryStr);
        }


        if (($sousmenu == "licences")) {

            if ($action == "deleteData") bf_mysql_query("UPDATE Comp�titions SET CompetDemLic = REPLACE(CompetDemLic,',".$_GET['Lic']."','') WHERE CompetCode = ".$Compet);

            $tablename     = "Licenci�s";
            $queryStr      = "SELECT EtabNomCourt, LicCode, LicInscrit, LicNumLicence, LicNom, LicPr�nom, LicNaissance, LicSexCode, CatLibelCourt, LicAss, LicNomAss, LicDateAss, LicDateDem, LicDateValid
							  FROM Cat�gories, Etablissements INNER JOIN Licenci�s ON Etablissements.EtabCode = Licenci�s.LicEtabCode 
							  WHERE LicNumLicence IN(0".TrouveSport($Compet, "CompetDemLic").") AND ( CatSpoCode = 1 AND LicSexCode = CatSexCode AND (Licenci�s.LicNaissance >= CatDateD�b And Licenci�s.LicNaissance <= CatDateFin)) ";
            if (!$Adm) $queryStr .= " AND (EtabNum = $Etab ".RetAS($Etab).") ";
            $queryStr     .= "UNION 
							  SELECT DISTINCT EtabNomCourt, LicCode, LicInscrit, LicNumLicence, LicNom, LicPr�nom, LicNaissance, LicSexCode, CatLibelCourt, LicAss, LicNomAss, LicDateAss, LicDateDem, LicDateValid
							  FROM Cat�gories, Etablissements INNER JOIN Licenci�s ON Etablissements.EtabCode = Licenci�s.LicEtabCode INNER JOIN Participations ON Licenci�s.LicNumLicence = Participations.ParLicCode";
            $where = "(ParCompetCode = $Compet) AND ( CatSpoCode = 1 AND LicSexCode = CatSexCode AND (Licenci�s.LicNaissance >= CatDateD�b And Licenci�s.LicNaissance <= CatDateFin) ";
            if (!$Adm) $where .= " And (EtabNum = $Etab ".RetAS($Etab)."))"; else $where.=")";
            $Choix = array("modifier","filtrage","licence");
            if ((!isset($orderby)) || ($orderby == "")) $orderby = "LicNom, LicPr�nom";
            $MaKey         = "LicCode";
            $NomsColonnes  = array('Etab','Code','Inscrit','Num�ro','Nom','Pr�nom','Naissance','Sexe','Cat','Ass','Ass Nom','Ass Date','Demande','Validation','Compet',);
            $ChampsTri     = array('EtabNomCourt, LicNom, LicPr�nom', '', 'LicInscrit DESC, LicNom, LicPr�nom', 'LicNumLicence', 'LicNom, LicPr�nom', 'LicPr�nom, LicNom', 'LicNaissance, LicNom, LicPr�nom', 'LicSexCode DESC, LicNom, LicPr�nom', 'LicSexCode DESC, LicNaissance DESC, LicNom, LicPr�nom', 'LicAss DESC, LicNom, LicPr�nom', 'LicNomAss, LicNom, LicPr�nom','LicDateAss DESC, LicNom, LicPr�nom', 'LicDateDem DESC, LicNom, LicPr�nom', 'LicDateValid DESC, LicNom, LicPr�nom');
            $ChampsAli     = array('center','','center','center','','','center','center','center','center','','center','center','center');
            $ChampsFor     = array('','','','%010s','','','','','','','','','','','');
            $ChampsAff     = array((($Adm) || ($Consult)),false,true,true,true,true,true,true,true,true,false,true,(TrouveParamweb("InscriptionLic") > 0),(TrouveParamweb("InscriptionLic") > 0),false);
            $ChampsType    = array("Texte","Texte","ListeS","Texte","Texte","Texte","Date","ListeS","Texte","ListeS","Texte","Date","Date","Date","Texte");
            $ChampsTypeExt = array("","",array("0"=>"Non","1"=>"Oui"),"","","","",array("1"=>'G',"2"=>'F'),"",array("0"=>"Non","1"=>"Oui"),"","","","","");
            $ChampsFiltre  = array(true,false,true,true,true,true,true,true,true,true,true,true,true,true,false);
            $ChampsNomFil  = array("","","","","","","","","","","","","","","");
            $ChampsRacFiltre = array(true,false,true,true,false,false,false,true,true,true,true,true,true,true,false);
            $ChampsRacParam  = "";
            $sousqueryStr    = "";
            $ChampsEdit      = array(
                array("Texte" ,false,"","",true),
                array("Texte" ,false ,"","",true),
                array("ListeS",($Adm && (!(isset($valid)))),"",array("LicInscrit", "Inscrit..."    ,array("0"=>"Non","1"=>"Oui"), "", "", "", "", "","45"),true),
                array("Texte" ,false,"","",true),
                array("Texte" ,$Adm ,"","",true),
                array("Texte" ,$Adm ,"","",true),
                array("Date"  ,$Adm ,"","",true),
                array("ListeS",$Adm ,"",array("LicSexCode", "Sexe..."       ,array('1'=>'G','2'=>'F')    , "", "", "", "", "", "35"),true),
                array("Texte" ,false,"","",true),
                array("ListeS",true,"",array("LicAss", "Ass..."    ,array("0"=>"Non","1"=>"Oui"), "", "", "", "", "","45"),false),
                array("Texte" ,true,"","",false),
                array("Date"  ,$Adm,"","",false),
                array("Date"  ,$Adm,"","",false),
                array("Date"  ,($Adm && (!(isset($valid)))),"","",false),
                array("Texte" ,false,"","",false)
            );
            $messagedel = "";
            GereData($tablename, $queryStr, $MaKey, $NomsColonnes, $ChampsTri, $ChampsAli, $ChampsFor, $ChampsAff, $action, $orderby, $Choix, $ChampsEdit, $ChampsInsert, $ChampsType, $ChampsTypeExt, $ChampsFiltre, $where, $ChampsNomFil, $ChampsRacFiltre, $ChampsRacParam,$sousqueryStr,$messagedel);
        }

        if (($sousmenu == "equipes") && ($affgrillecompet == "oui")) {
            $tabChpSup = array_merge(explode("/", $tabChp[1]), array('','','','','','',''));
            for ($i = 0; $i < count($tabChpSup); $i++) $tabChpSup[$i] = trim($tabChpSup[$i]);

            $affQualif = false;
            if (in_array("Qualif", $tabChpSup)) {
                $affQualif = true;
                unset($tabChpSup[array_search("Qualif", $tabChpSup)]);
                $tabChpSup = array_values($tabChpSup);
            }

            $relOblige = false;
            if (in_array("Relayeurs", $tabChpSup)) {
                $relOblige = true;
                unset($tabChpSup[array_search("Relayeurs", $tabChpSup)]);
                $tabChpSup = array_values($tabChpSup);
            }

            $strCatEpr = "";
            for ($i = 0; $i < count($tabChpSup); $i++) {
                $monChpSup = explode("#", $tabChpSup[$i]);
                if (in_array("CatEpr", $monChpSup)) {
                    unset($tabChpSup[$i]);
                    $tabChpSup = array_values($tabChpSup);
                    $Valeurs = explode(";", $monChpSup[1]);
                    if (count($Valeurs) > 0) {
                        for ($j = 0; $j < count($Valeurs); $j++) {
                            if (!($strCatEpr == "")) $strCatEpr = $strCatEpr." OR ";
                            $strCatEpr = $strCatEpr."CatLibelCourt = '".$Valeurs[$j] ."'";
                        }
                        if (!($strCatEpr == "")) $strCatEpr = " AND (".$strCatEpr." OR EprLibelCourt LIKE '%Open%')";
                    }
                }
            }

            for ($i = 1; $i <= 5; $i++) {$NomChp{$i} = ""; $Type{$i} = "Texte"; $List{$i} = ""; $PasVide{$i} = false;}
            for ($i = 0; $i < count($tabChpSup); $i++) {
                $Sup = explode("#", $tabChpSup[$i]);
                $num = $i + 1;
                $NomChpSup{$num} = trim($Sup[0]);
                if (strpos($NomChpSup{$num},"!") === 0) {
                    $PasVide{$num} = true;
                    $NomChpSup{$num} = substr($NomChpSup{$num},1) ;
                }
                if (array_key_exists('2', $Sup)) { $Sup[2] = trim($Sup[2]); if (!(is_numeric($Sup[2]))) $Sup[2] = ""; else if ($Sup[2] < 100) $Sup[2] = "";} else $Sup[2] = "";
                if (array_key_exists('1', $Sup)) {
                    $Valeurs = explode(";", $Sup[1]);
                    if (count($Valeurs) > 0) {
                        $ListeValeurs = "";
                        for ($j = 0; $j < count($Valeurs); $j++) {
                            if (strpos($Valeurs[$j],"@") !== false) {
                                $ValeursTO = explode("@", $Valeurs[$j]);
                                $ValeursTO[0] = trim($ValeursTO[0]); $ValeursTO[1] = trim($ValeursTO[1]);
                                if ( (is_numeric($ValeursTO[0])) && (is_numeric($ValeursTO[1])) ) {
                                    for ($k = $ValeursTO[0]; $k <= $ValeursTO[1]; $k++) {
                                        $ListeValeurs[$k] = $k;
                                        if (array_key_exists('2', $ValeursTO)) { $ValeursTO[2] = trim($ValeursTO[2]); if (is_numeric($ValeursTO[2])) $k = $k + $ValeursTO[2]-1; }
                                    }
                                }
                            } else if ($Valeurs[$j] != "") $ListeValeurs[$Valeurs[$j]] = $Valeurs[$j];
                        }
                        if ($ListeValeurs != "") { $List{$num} = array("", $NomChpSup{$num}."...", $ListeValeurs, "", "", "", "", "", $Sup[2]); $Type{$num} = "ListeD";}
                    }
                }
            }

            $tablename  = "Equipes";
            $queryStr        ="SELECT EtabNomCourt, EtabNom, EtabVille, EquCode, EquNum, EquCompl�ment, CatLibelCourt, EprLibelCourt, EquCompetCode, EquPromo, EquChall, EquRelayeurs, EquPerfRelaisQualif, EquObs1, EquObs2, EquObs3, EquObs4, EquObs5
						   FROM Etablissements, Cat�gories INNER JOIN Equipes ON Cat�gories.CatCode = Equipes.EquCatCode LEFT JOIN `Epreuves Comp�titions` ON Equipes.EquEprCompetCode = `Epreuves Comp�titions`.EprCompetCode LEFT JOIN Epreuves ON `Epreuves Comp�titions`.EprCompetEprCode = Epreuves.EprCode";
            if (!$Adm) $where =" Etablissements.EtabCode = Equipes.EquEtabCode AND EquCompetCode = $Compet AND (EtabNum = $Etab ".RetAS($Etab).")"; else $where ="Etablissements.EtabCode = Equipes.EquEtabCode AND EquCompetCode = $Compet";
            if ((!isset($orderby)) || ($orderby == "")) $orderby = "EtabNomCourt, EquNum, EquCompl�ment, Epreuves.Ordre";
            $ArrayChall = array();
            if ($SpoGestion == -4) $ArrayChall = array("1"=>'Courses', "2"=>'Sauts', "3"=>'Lancers');
            if ($SpoGestion == -7) $ArrayChall = array("1"=>'Col F', "2"=>'Col G', "3"=>'Lyc Promo', "4"=>'Lyc Elite');

            $MaKey           = "EquCode";
            $NomsColonnes    = array('Etab','Nom','Ville','Code','Num�ro','Compl�ment','Cat','Epreuve','Compet','Crit-Chal','Chall','Relayeurs','Qualif', $NomChpSup{1},$NomChpSup{2},$NomChpSup{3}, $NomChpSup{4}, $NomChpSup{5});
            $ChampsTri       = array("EtabNomCourt, EquNum","EtabNom, EquNum","EtabVille, EquNum","EquCode","EtabNomCourt, EquNum","","","","","","","","EquPerfQualif, EtabNomCourt, EquNum","","","","","");
            $ChampsAli       = array('center','','','center','center','','center','center','center','center','center','',"right",'center','center','center','center','center');
            $ChampsFor       = array('','','','','','','','','','','','','','','','','','');
            $ChampsAff       = array((($Adm) || ($Consult)),$Adm, $Adm, false,true,true,($SpoGestion != -4 && $SpoGestion != -5 ),(($SpoGestion == -1) || ($SpoGestion == -5) || ($SpoGestion == -7)),false,($SpoGestion == -1),(($SpoGestion == -4) || ($SpoGestion == -7)),(($SpoGestion == -1) || ($SpoGestion == -5) || ($SpoGestion == -7)),$affQualif ,($tabChpSup[0] != ""), ($tabChpSup[1] != ""), ($tabChpSup[2] != ""), ($tabChpSup[3] != ""), ($tabChpSup[4] != "") );
            $ChampsType      = array("Texte","Texte","Texte","Texte","Texte","Texte","Texte","Texte","Texte","ListeS","ListeS","Texte","Perf","Texte","Texte","Texte","Texte","texte");
            $ChampsTypeExt   = array("","","","","","","","","",array('0'=>'Non','1'=>'Oui'),$ArrayChall,"","","","","","","");
            $ChampsFiltre    = array(true,true, true,true,true,true,true,true,true,($SpoGestion == -1),(($SpoGestion == -4) || ($SpoGestion == -7)),true,true,true,true,true,true,true);
            $ChampsNomFil    = array("","","","","","","","","","","","","","","","","","");
            $ChampsRacFiltre = array(true,false,false,false,false,false,true,true,false,true,true,false,false,false,false,false,false,false);
            $ChampsRacParam  = array(array(1,'competitions','individuels','EquNum',4,'->'));
            $sousqueryStr    = "";
            if (($Adm) || ($CompetStatut == "Inscriptions ouvertes")) $Choix = array("ajout","supprimer","filtrage","modifier","exporter"); else $Choix = array("filtrage","exporter");
            if ($Adm) {array_push($Choix, "suppressiontout");}
            if ($Consult) $Choix = array("");
            if ($SpoGestion != -7) {
                $CatPrim    = "SELECT EprCatCode, CatLibelCourt, CatLibell�, CatPrim, CatType, EprCompetCompetCode FROM Cat�gories INNER JOIN Epreuves ON Cat�gories.CatCode = Epreuves.EprCatCode INNER JOIN `Epreuves Comp�titions` ON Epreuves.EprCode = `Epreuves Comp�titions`.EprCompetEprCode GROUP BY Epreuves.EprCatCode, Cat�gories.CatLibelCourt, Cat�gories.CatLibell�, Cat�gories.Ordre, `Epreuves Comp�titions`.EprCompetCompetCode, Cat�gories.CatPrim HAVING `Epreuves Comp�titions`.EprCompetCompetCode = $Compet And Cat�gories.CatPrim = 1 ORDER BY Cat�gories.Ordre";
                $CatPrimTrue = " AND CatPrim = TRUE";
                $CatChp = "EprCatCode";
                $CatPrimBis = "SELECT CatCode FROM Cat�gories INNER JOIN Epreuves ON Cat�gories.CatCode = Epreuves.EprCatCode INNER JOIN `Epreuves Comp�titions` ON Epreuves.EprCode = `Epreuves Comp�titions`.EprCompetEprCode WHERE EprCompetCompetCode = $Compet AND CatLibelCourt=";
                $CatInit = "";
                $EpreInit = "";
                if ($SpoGestion == -4) {
                    $resCatInit = mysql_fetch_array( bf_mysql_query("SELECT * FROM Comp�titions INNER JOIN Cat�gories ON Comp�titions.CompetSpoCode = Cat�gories.CatSpoCode WHERE CatLibelCourt = 'JF' AND CompetCode = ".$Compet));
                    if ($resCatInit) $CatInit = $resCatInit["CatCode"];

                    $resEpreInit = mysql_fetch_array( bf_mysql_query("SELECT * FROM Groupes INNER JOIN ((Cat�gories INNER JOIN Epreuves ON Cat�gories.CatCode = Epreuves.EprCatCode) INNER JOIN `Epreuves Comp�titions` ON Epreuves.EprCode = `Epreuves Comp�titions`.EprCompetEprCode) ON Groupes.GrCode = Epreuves.EprGrCode WHERE GrLibelCourt = 9 AND CatLibelCourt = 'JF' AND EprCompetCompetCode = ".$Compet));
                    $EpreInit = $resEpreInit["EprCompetCode"];

                }
            } else {
                $CatPrim    = "SELECT CatCode, CatLibelCourt, CatLibell�, CatPrim, CatType FROM Cat�gories INNER JOIN Sports On Cat�gories.CatSpoCode = Sports.SpoCode WHERE Sports.SpoGestionPerf = -7 AND Cat�gories.CatType = 99 ORDER BY Cat�gories.Ordre";
                $CatPrimTrue = "";
                $CatChp = "CatCode";
                $CatPrimBis = "SELECT CatCode FROM Cat�gories INNER JOIN Sports On Cat�gories.CatSpoCode = Sports.SpoCode WHERE Sports.SpoGestionPerf = -7 AND Cat�gories.CatType = 99 AND Cat�gories.CatLibelCourt=";
                $CatInit = "";
                $EpreInit = "";
            }
            $ChampsEdit      = array(
                array("ListeD",$Adm,"EquEtabCode",array("seletabequ", "Etab...","SELECT EtabNum, EtabNomCourt, EtabNom, EtabVille FROM Etablissements ORDER BY EtabNum", Array("EtabNum", "EtabNomCourt", "EtabNom", "EtabVille"), Array("%06d", "", "", ""), "EtabNum","SELECT EtabNum FROM Etablissements WHERE EtabNomCourt = ","SELECT EtabCode FROM Etablissements WHERE EtabNum = ","100"),true),
                array("Texte" ,false,"","",false),
                array("Texte" ,false,"","",false),
                array("Texte" ,false,"","",false),
                array("Texte" ,false,"","",false),
                array("Texte" ,false,"","",false),
                array("ListeD",true ,"EquCatCode",array("selcatequ", "Cat�gorie...",$CatPrim, Array("CatLibelCourt", "-", "CatLibell�"), Array("", "", ""), $CatChp, $CatPrimBis,"","130"),true),
                array("ListeD",(($SpoGestion == -1) || ($SpoGestion == -5) || ($SpoGestion == -7)),"EquEprCompetCode",array(
                    "seleprequ",
                    "Epreuve...",
                    "SELECT EprCompetCode, EprLibelCourt, EprLibell� 
						   FROM Epreuves INNER JOIN Cat�gories ON Cat�gories.CatCode = Epreuves.EprCatCode INNER JOIN `Epreuves Comp�titions` ON Epreuves.EprCode = `Epreuves Comp�titions`.EprCompetEprCode RIGHT JOIN Groupes ON Groupes.GrCode = Epreuves.EprGrCode
						   WHERE Groupes.GrLibelCourt >= 9 $CatPrimTrue AND `Epreuves Comp�titions`.EprCompetCompetCode = ".$Compet.$strCatEpr." ORDER BY Epreuves.Ordre",
                    Array("EprLibelCourt", "-","EprLibell�"),
                    Array("", "", ""),
                    "EprCompetCode",
                    "SELECT EprCompetCode FROM Epreuves INNER JOIN `Epreuves Comp�titions` ON Epreuves.EprCode = `Epreuves Comp�titions`.EprCompetEprCode WHERE EprCompetCompetCode = ".$Compet." AND EprLibelCourt = ",
                    "",
                    "230"),
                    (($SpoGestion == -1) || ($SpoGestion == -5) || ($SpoGestion == -7)) ),
                array("Texte" ,false,"","",false),
                array("ListeS",($SpoGestion == -1) ,"",array("EquPromo", "Crit-Chal...",array('0'=>'Non','1'=>'Oui'), "", "", "", "","","60"),"",true,false),
                array("ListeS",(($SpoGestion == -4)||($SpoGestion == -7)),"",array("EquChall", "Chall...",$ArrayChall, "", "", "", "","","70"),"",true,false),
                array("Texte" ,(($SpoGestion == -1) || ($SpoGestion == -5) || ($SpoGestion == -7)),"","",$relOblige),
                array("Texte" ,true,"","",false),
                array($Type{1}, true, "", $List{1}, $PasVide{1}),
                array($Type{2}, true, "", $List{2}, $PasVide{2}),
                array($Type{3}, true, "", $List{3}, $PasVide{3}),
                array($Type{4}, true, "", $List{4}, $PasVide{4}),
                array($Type{5}, true, "", $List{5}, $PasVide{5})
            );
            $ChampsInsert    = array(
                array("ListeD",true ,"EquEtabCode",array("seletabequ", "Etab...","SELECT EtabNum, EtabNomCourt, EtabNom, EtabVille FROM Etablissements ORDER BY EtabNum", Array("EtabNum", "-", "EtabNomCourt", "-", "EtabNom", "EtabVille"), Array("%06d", "", "", "","",""), "EtabNum","SELECT EtabNum FROM Etablissements WHERE EtabNomCourt = ","SELECT EtabCode FROM Etablissements WHERE EtabNum = ","200"),"$Etab",$Adm,true,false),
                array("Texte" ,false,""             ,"","",false,false,false),
                array("Texte" ,false,""             ,"","",false,false,false),
                array("Texte" ,false,""             ,"","",true, false,false),
                array("Texte" ,false,""             ,"","",false,false,false),
                array("Texte" ,false,""             ,"","",false,false,false),
                array("ListeD",$SpoGestion != -5,"EquCatCode",array("selcatequ", "Cat�gorie...",$CatPrim, Array("CatLibelCourt", "-", "CatLibell�"), Array("", "", ""), $CatChp, $CatPrimBis, "","130"), $CatInit, $SpoGestion != -4,true,false),
                array("ListeD",(($SpoGestion == -1) || ($SpoGestion == -5) || ($SpoGestion == -7) || ($SpoGestion == -4)),"EquEprCompetCode",array(
                    "seleprequ",
                    "Epreuve...",
                    "SELECT EprCompetCode, EprLibelCourt, EprLibell� 
						    FROM Epreuves INNER JOIN Cat�gories ON Cat�gories.CatCode = Epreuves.EprCatCode INNER JOIN `Epreuves Comp�titions` ON Epreuves.EprCode = `Epreuves Comp�titions`.EprCompetEprCode RIGHT JOIN Groupes ON Groupes.GrCode = Epreuves.EprGrCode
							WHERE Groupes.GrLibelCourt >= 9 $CatPrimTrue AND `Epreuves Comp�titions`.EprCompetCompetCode = ".$Compet.$strCatEpr." ORDER BY Epreuves.Ordre",
                    Array("EprLibelCourt", "-", "EprLibell�"),
                    Array("", "", ""),
                    "EprCompetCode",
                    "",
                    "",
                    "230"),
                    $EpreInit, (($SpoGestion == -1) || ($SpoGestion == -5) || ($SpoGestion == -7)),(($SpoGestion == -1) || ($SpoGestion == -5) || ($SpoGestion == -7)),False),
                array("Texte"  ,true ,"EquCompetCode","","$Compet",true,true,false),
                array("ListeS",($SpoGestion == -1) ,"",array("EquPromo", "Crit-Chal..."    ,array('0'=>'Non','1'=>'Oui'), "", "", "", "","","60"),"",($SpoGestion == -1),false,false),
                array("ListeS",(($SpoGestion == -4)||($SpoGestion == -7)) ,"",array("EquChall", "Chall...", $ArrayChall, "", "", "", "","","70"),"",(($SpoGestion == -4)||($SpoGestion == -7)),true,false),
                array("Texte" ,(($SpoGestion == -1) || ($SpoGestion == -5) || ($SpoGestion == -7)),"","","",true,$relOblige,false),
                array("Texte" ,true,"","","",true,false,false),
                array($Type{1},true,"",$List{1},"",true,$PasVide{1},false),
                array($Type{2},true,"",$List{2},"",true,$PasVide{2},false),
                array($Type{3},true,"",$List{3},"",true,$PasVide{3},false),
                array($Type{4},true,"",$List{4},"",true,$PasVide{4},false),
                array($Type{5},true,"",$List{5},"",true,$PasVide{5},false)
            );
            GereData($tablename, $queryStr, $MaKey, $NomsColonnes, $ChampsTri, $ChampsAli, $ChampsFor, $ChampsAff, $action, $orderby, $Choix, $ChampsEdit, $ChampsInsert, $ChampsType, $ChampsTypeExt, $ChampsFiltre, $where, $ChampsNomFil, $ChampsRacFiltre, $ChampsRacParam, $sousqueryStr);
            if ($SpoGestion == -3) { bf_mysql_query("UPDATE (Cat�gories INNER JOIN (Groupes INNER JOIN (Sports INNER JOIN (Equipes INNER JOIN Epreuves ON Equipes.EquCatCode = Epreuves.EprCatCode) ON Sports.SpoCode = Epreuves.EprSpoCode) ON Groupes.GrCode = Epreuves.EprGrCode) ON Cat�gories.CatCode = Epreuves.EprCatCode) INNER JOIN `Epreuves Comp�titions` ON Epreuves.EprCode = `Epreuves Comp�titions`.EprCompetEprCode SET Equipes.EquEprCompetCode = EprCompetCode WHERE Groupes.GrLibelCourt = 9 AND Sports.SpoGestionPerf = -3 AND Equipes.EquCompetCode = ".$Compet); }
        }
    }

    if (($menu == "connexions") && ($Adm)) {
        $tablename  = "Connexions";
        $queryStr  = "SELECT DATE_FORMAT(Depart,'%d/%m/%Y %T') As Debut, Ip, IF(EtabNomCourt IS NULL,Id,EtabNomCourt) AS Utilisateur, TIMEDIFF(Now(),Depart) AS Duree, Session 
					  FROM `Connexions` LEFT JOIN `Etablissements` ON Connexions.Id = Etablissements.EtabNum";
        $where = "";
        if ((!isset($orderby)) || ($orderby == "")) $orderby = "Debut";
        $MaKey           = "Session";
        $NomsColonnes    = array('D�but','Adresse IP','Utilisateur','Dur�e','Session');
        $ChampsTri       = array('Debut','','(IF(EtabNomCourt IS NULL,"Admin",EtabNomCourt))','','');
        $ChampsAli       = array('center','center','center','center','center');
        $ChampsFor       = array('','','','','');
        $ChampsAff       = array(true,true,true,true,false);
        $ChampsType      = array("Texte","Texte","Texte","Texte","Texte");
        $ChampsTypeExt   = array("","","","","");
        $ChampsFiltre    = array(false,true,false,false,false);
        $ChampsNomFil    = array("","","","","");
        $ChampsRacFiltre = array(false,true,false,false,false);
        $ChampsRacParam  = "";
        $Choix = array("filtrage");
        $Clause1 = ""; $Clause2 = "";
        $ChampsEdit      = "";
        $ChampsInsert    = "";
        $sousqueryStr    = "";
        GereData($tablename, $queryStr, $MaKey, $NomsColonnes, $ChampsTri, $ChampsAli, $ChampsFor, $ChampsAff, $action, $orderby, $Choix, $ChampsEdit, $ChampsInsert, $ChampsType, $ChampsTypeExt, $ChampsFiltre, $where, $ChampsNomFil, $ChampsRacFiltre, $ChampsRacParam, $sousqueryStr);
    }

    if ($menu == "options"){

        echo "<BR>";
        echo "<FORM ACTION='$PHP_SELF' METHOD='POST'>";

        if ($Adm) {
            echo "<TABLE CLASS = 'tableconopt'>";
            echo "<TR>";
            echo "<TD>";
            echo "&nbsp;&nbsp;&nbsp;Nombre de lignes par page : ";
            echo "<select size=1 name='LignesParPage' CLASS='listederoulante'>";
            for( $i = 1; $i < 1000; $i++ ) {
                if ($i > 50) $i = $i + 10-1;
                if ($i > 100) $i = $i + 100-10;
                echo "<option value='$i'";
                if ($_SESSION['LignesParPage'] == $i) echo " selected";
                echo "> $i </option>\n";
            }
            echo "<option value='999999'";
            if ($_SESSION['LignesParPage'] == 999999) echo " selected";
            echo "> Tout (...peut ralentir) </option>\n";
            echo "</select>";
            echo "&nbsp;<INPUT TYPE='submit' NAME='ValideLignesParPage' VALUE='Valider' class='bouton'>";
            echo "</TD>";
            echo "</TR>";
            echo "</TABLE>";
            echo "<BR>";
        }
        echo "<TABLE CLASS = 'tableconopt'>";
        echo "<TR>";
        echo "<TD>";
        echo "&nbsp;&nbsp;&nbsp;Mod�le de couleurs : ";
        echo "<select size=1 name='Couleur' CLASS='listederoulante'>";
        for( $i = 0; $i < count($Couleurs); $i++ ) {
            echo "<option value = '$i'";
            if ($_SESSION['Couleur'] == $i) echo " selected";
            echo "> ".$Couleurs[$i][0]."&nbsp; </option>\n";
        }
        echo "</select>";
        echo "&nbsp;<INPUT TYPE='submit' NAME='ValideCouleur' VALUE='Valider' class='bouton'>";
        echo "</TD>";
        echo "</TR>";
        echo "</TABLE>";
        echo "<BR>";

    }

    if (($menu == "outils")  && ($Adm)) {

        $clicbouton = false; if ( ($valideaccueil == "Valider") || ($validemaintenance == "Valider") || ($presupprimer == "Supprimer") || ($supprimer == "Supprimer") || ($valideBasesExternes == "Valider") || ($upload == "Restaurer") || ($exporter == "Sauvegarder") || ($validerequete == "Valider") || ($valideinscriptionlic == "Valider") || ($valideimpressionlic == "Valider")) $clicbouton = true;

        if (isset($valideaccueil)) {
            bf_mysql_query("UPDATE `Paramweb` SET `Accueil` = '".urlencode($accueil)."'");
        }
        $req = bf_mysql_query("SELECT `Accueil` FROM `Paramweb`");
        if ((!(!$req)) && (mysql_num_rows($req) > 0)) {$data = mysql_fetch_assoc($req); $data = urldecode($data["Accueil"]);} else $data = "";
        echo "<BR>";
        echo "<FORM method='post'>";
        echo "<TABLE CLASS = 'tableconopt'>";
        echo "<TR>";
        echo "<TD>";
        echo "> CONNEXION &nbsp;&nbsp;";
        echo "Message d'accueil &nbsp;&nbsp;";
        echo "<input name='valideaccueil' type='submit' id='valideaccueil' value='Valider' class='bouton'>";
        echo "<TEXTAREA name='accueil' rows='2'>".$data."</textarea><BR>";
        echo "</TD>";
        echo "</TR>";
        echo "</TABLE>";
        echo "</FORM>";

        echo "<HR CLASS = 'hr1'>\n";

        if (isset($validemaintenance)) {
            if ($optionmaintenance == "oui") $optionmain = 1; else $optionmain = 0;
            bf_mysql_query("UPDATE `Paramweb` SET `Maintenance` = '$optionmain'");
        }
        $req = bf_mysql_query("SELECT `Maintenance` FROM `Paramweb`");
        if ((!(!$req)) && (mysql_num_rows($req) > 0)) {$data = mysql_fetch_assoc($req); $data = $data["Maintenance"];} else $data = 0;

        echo "<FORM method='post'>";
        echo "<TABLE CLASS = 'tableconopt'>";
        echo "<TR>";
        echo "<TD>";
        echo "> MAINTENANCE &nbsp;&nbsp;";
        echo "Fermer le site&nbsp;&nbsp;";
        echo "<input type='radio' name='optionmaintenance' value='oui'";
        if (($data['Maintenance']) == 1) echo " checked='checked'";
        echo ">Oui&nbsp";
        echo "<input type='radio' name='optionmaintenance' value='non'";
        if (($data['Maintenance']) != 1) echo " checked='checked'";
        echo ">Non &nbsp;&nbsp;";
        echo "<input name='validemaintenance' type='submit' id='validemaintenance' value='Valider' class='bouton'>";
        echo "</TD>";
        echo "</TR>";
        echo "</TABLE>";
        echo "</FORM>";

        echo "<HR CLASS = 'hr1'>\n";

        echo "<FORM method='post'>";
        echo "<TABLE CLASS = 'tableconopt'>";
        echo "<TR>";
        echo "<TD>";
        echo "> EXPORTATION &nbsp;&nbsp;";
        echo "Exporter au format (pour Excel, testez csv1 et csv2) &nbsp;&nbsp;";
        echo "<input type='hidden' name='optionexport' value='exptout'>";
        echo "<input type='radio' name='optionexporttype' value='expcsv' >csv1 (Excel avec ,) &nbsp;";
        echo "<input type='radio' name='optionexporttype' value='expcs2' >csv2 (Excel avec ;) &nbsp;";
        echo "<input type='radio' name='optionexporttype' value='expsql' checked='checked'>ugw (Ug20xx)&nbsp;&nbsp;";
        echo "<input type='radio' name='optionexporttype' value='exptex' >txt (Texte) &nbsp;";
        echo "<input name='' type='submit' id='exporter' value='Exporter' class='bouton'>";
        echo "</TD>";
        echo "</TR>";
        echo "</TABLE>";
        echo "</FORM>";

        echo "<HR CLASS = 'hr1'>\n";

        if ($actionfichier == "confirmesupprimer") {
            if (strrchr($fichier,".") == '.ugw') @unlink($fichier);
            $actionfichier = "";
            $fichier = "";
        }

        echo "<FORM method='post'>";
        echo "<TABLE CLASS = 'tableconopt'>";
        echo "<TR>";
        echo "<TD>";
        echo "> SAUVEGARDE &nbsp;&nbsp;";
        echo "Sauvegarder sur le serveur &nbsp;&nbsp;";
        echo "<input type='hidden' name='optionexporttype' value='expser' checked='checked'>";
        echo "<input type='hidden' name='optionexport' value='exptout'>";
        if (TailleRep(".") > $QUOTA) {
            echo "(Pour sauvegarder, supprimez les sauvegardes inutiles pour lib�rer de l'espace sur le serveur) <BR>";
        } else {
            echo "<input name='exporter' type='submit' id='exporter' value='Sauvegarder' class='bouton'>";
        }
        echo "</TD>";
        echo "</TR>";
        echo "</TABLE>";
        echo "</FORM>";

        if ($actionfichier == "modifier") {
            if (strrchr($fichier,".") == '.ugw') {
                $journow = date('w', @filemtime($fichier));
                $fichiertab = explode(".",$fichier);
                if (strstr($fichier, 'Auto') == True) @rename($fichier, str_replace(" Auto".$journow." ", "", $fichier)); else @rename($fichier, $fichiertab[0]." Auto".$journow." .".$fichiertab[1]);
            }
            $actionfichier = "";
            $fichier = "";
        }

        $tabfile = RetourneFic (".", "Tout", "Auto", $TriFic);
        if (!($tabfile == 0)) {
            echo "<TABLE CLASS = 'tablecompets' style = 'Margin-top:0px;Margin-left:40px;Margin-bottom:10px;'>";
            for($i=0; $i < count($tabfile); $i++) {
                if ($i == 0) {
                    echo "<CAPTION style='text-align:left; padding-bottom:4px;'>Sauvegardes trouv�es sur le serveur : </CAPTION>";
                    echo "<TR><TH>N�</TH>";
                    echo "<TH>Type</TH>";
                    echo "<TH><a href=$PHP_SELF?action=VoirMenu&menu=outils&TriFic=Date>Date</a></TH>";
                    echo "<TH>Heure</TH>";
                    echo "<TH><a href=$PHP_SELF?action=VoirMenu&menu=outils&TriFic=Taille>Taille</a></TH>";
                    echo "<TH>Age</TH>";
                    echo "<TH></TH></TR>";
                }
                echo "<TR CLASS='";
                if (($actionfichier == "supprimer") && ($tabfile[$i]["Nom"] == $fichier)) echo "trsuppr"; else if ( (round($i / 2) - ($i / 2)) == "0" ) echo "tr1"; else echo "tr2";
                echo "'>";
                echo "<TD ALIGN = 'center'>".($i+1)."</TD>";
                echo "<TD ALIGN = 'center'>".$tabfile[$i]["Type"]."</TD>";
                echo "<TD ALIGN = 'right'>".$tabfile[$i]["Date"]."</TD>";
                echo "<TD>".$tabfile[$i]["Heure"]."</TD>";
                echo "<TD>".$tabfile[$i]["Taille"]."</TD>";
                echo "<TD ALIGN = 'center'>".$tabfile[$i]["Age"]." Jr</TD>";
                echo "<TD>";
                if (($actionfichier == "supprimer") && ($tabfile[$i]["Nom"] == $fichier)) {
                    echo " <a href=$PHP_SELF?action=VoirMenu&menu=outils&fichier=".stripslashes(rawurlencode($fichier))."&actionfichier=confirmesupprimer&TriFic=$TriFic>&nbsp;Confirmer la suppression&nbsp;</a>";
                    echo "<a href=$PHP_SELF?action=VoirMenu&menu=outils&TriFic=$TriFic>&nbsp;Annuler&nbsp;</a><BR>";
                } else {
                    echo " <a href=$PHP_SELF?action=VoirMenu&menu=outils&fichier=".stripslashes(rawurlencode($tabfile[$i]["Nom"]))."&actionfichier=supprimer&TriFic=$TriFic>&nbsp;Supprimer&nbsp;</a>";
                    echo "<a href=$PHP_SELF?action=VoirMenu&menu=outils&fichier=".stripslashes(rawurlencode($tabfile[$i]["Nom"]))."&actionfichier=modifier&TriFic=$TriFic>&nbsp;Modifier&nbsp;</a>";
                    echo "<a href=".stripslashes(rawurlencode($tabfile[$i]["Nom"])).">&nbsp;T�l�charger&nbsp;</a><BR>";
                }
                echo "</TD>";
                echo "</TR>";
                $filenoms[$i] = $tabfile[$i]["Nom"];
                $filedes[$i]  = ($i+1)." - ".$tabfile[$i]["Date"]." � ".$tabfile[$i]["Heure"]." (".$tabfile[$i]["Taille"].")";
            }
            echo "</TABLE>";
        }

        echo "<HR CLASS = 'hr1'>\n";

        echo "<form method='post' enctype='multipart/form-data'>";
        echo "<table CLASS = 'tableconopt'>";
        echo "<TR>";
        echo "<TD>";
        echo "> RESTAURATION &nbsp;&nbsp;";
        if (!($tabfile == 0)) {
            echo " Restaurer du serveur &nbsp;";
            listederoulante("ListeSauvegardes", "Sauvegardes...", $filedes, "", "", $filenoms, "", 230);
            echo " &nbsp; Ou &nbsp;";
        }
        echo " Restaurer le fichier &nbsp;";
        echo "<input type='hidden' name='MAX_FILE_SIZE' value='50000000'>";
        echo "<input name='userfile' type='file' id='userfile' size ='30'>";
        echo " &nbsp; &nbsp;";
        echo "<input name='upload' type='submit' id='upload' value='Restaurer' class='bouton'>";
        echo "</TD>";
        echo "</TR>";
        echo "</TABLE>";
        echo "</FORM>";

        $tmpName = "";
        if(isset($_POST['upload']) && ($ListeSauvegardes != "")) {
            $tmpName  = "./$ListeSauvegardes";
            $fileSize = filesize("./$ListeSauvegardes");
        }
        if(isset($_POST['upload']) && $_FILES['userfile']['size'] > 0) {
            $tmpName  = $_FILES['userfile']['tmp_name'];
            $fileSize = $_FILES['userfile']['size'];
        }

        echo "<HR CLASS = 'hr1'>\n";

        echo "<FORM method='POST'>";
        echo "<TABLE CLASS = 'tableconopt'>";
        echo "<TR>";
        echo "<TD>";
        echo "> SUPPRESSION &nbsp;&nbsp; Supprimer &nbsp;&nbsp;";

        if ((($action == "supprime") || (isset($supprimer)))  ) {
            if ($optionsuppr == "supprtout"   ) $listetable = Array("Sports", "Etablissements", "Secteurs", "Licenci�s", "Cat�gories", "Comp�titions", "Epreuves", "Epreuves Comp�titions", "Groupes", "Participations", "Equipes", "Tours Epreuves Comp�titions", "Param�tres");
            if ($optionsuppr == "suppretab"   ) $listetable = array("Etablissements", "Secteurs", "Licenci�s", "Participations", "Equipes");
            if ($optionsuppr == "supprlic"    ) $listetable = array("Licenci�s", "Participations", "Equipes");
            if ($optionsuppr == "supprcompet" ) $listetable = Array("Comp�titions", "Epreuves Comp�titions", "Participations", "Equipes", "Tours Epreuves Comp�titions");
            if ($optionsuppr == "supprpartic" ) $listetable = Array("Participations", "Equipes");
            $maconnec = bf_mysql_connect();
            $pTable = mysql_list_tables($BDD,$maconnec);
            if (is_array($listetable)) {
                $num    = count($listetable);
            } else {
                $pTable  = mysql_list_tables($BDD);
                $num     = mysql_num_rows($pTable);
            }
            for($t = 0; $t < $num; $t++ ) {
                if (is_array($listetable)) {
                    $tablename = $listetable[$t];
                    if (is_array($tablename)) {$tablename =	$listetable[$t][0];}
                } else $tablename = mysql_tablename($pTable, $t);
                if (is_array($listetable)) {
                    $req = $listetable[$t];
                    if (is_array($req)) $req = $listetable[$t][1]; else $req = "DELETE FROM `$tablename`";
                } else $req = "DELETE FROM `$tablename`";
                bf_mysql_query($req, 0 ,"`$tablename`");
                bf_mysql_query("ALTER TABLE `$tablename` AUTO_INCREMENT = 0", 0 ,"`$tablename`");
            }
        }

        if ( (isset($presupprimer)) && (isset($optionsuppr)) ){
            $options = array('de tout'=>'supprtout','des �tablissements'=>'suppretab','des licenci�s'=>'supprlic','des comp�titions'=>'supprcompet');
            echo "Confirmer la suppression ".array_search($optionsuppr, $options)."&nbsp;&nbsp;";
            ConstruitZone(array(array("optionsuppr",$optionsuppr)));
            echo "<input name='supprimer' type='submit' id='supprimer' value='Supprimer' class='bouton'>";
        } else {
            echo "<input type='radio' name='optionsuppr' value='supprtout'   >Tout&nbsp;&nbsp;&nbsp;";
            echo "<input type='radio' name='optionsuppr' value='suppretab'   >Etablissements&nbsp;&nbsp;&nbsp;";
            echo "<input type='radio' name='optionsuppr' value='supprlic'    >Licenci�s&nbsp;&nbsp;&nbsp;";
            echo "<input type='radio' name='optionsuppr' value='supprcompet' >Comp�titions&nbsp;&nbsp;&nbsp;";
            echo "<input type='radio' name='optionsuppr' value='supprpartic' >Participations&nbsp;&nbsp;&nbsp;";
            echo "<input name='presupprimer' type='submit' id='presupprimer' value='Supprimer' class='bouton'>";
        }
        echo "</TD>";
        echo "</TR>";
        echo "</TABLE>";
        echo "</FORM>";

        echo "<HR CLASS = 'hr1'>\n";

        $tabfile = RetourneFic (".", "Comp�tition", "Comp", $TriFic);
        if (!($tabfile == 0)) {

            echo "<TABLE CLASS = 'tableconopt' ID = 'enattente'>";
            echo "<TR>";
            echo "<TD>";
            echo "> COMPETITIONS EN ATTENTE &nbsp;&nbsp;";
            echo "</TD>";
            echo "</TR>";
            echo "</TABLE>";

            echo "<TABLE CLASS = 'tablecompets' style = 'Margin-top:0px;Margin-left:40px;Margin-bottom:10px;'>";
            for($i=0; $i < count($tabfile); $i++) {
                if ($i == 0) {
                    echo "<CAPTION style='text-align:left; padding-bottom:4px;'>Comp�titions en attente trouv�es sur le serveur : </CAPTION>";
                    echo "<TR><TH>N�</TH>";
                    echo "<TH>Type</TH>";
                    echo "<TH><a href=$PHP_SELF?action=VoirMenu&menu=outils&TriFic=Date#enattente>Date</a></TH>";
                    echo "<TH>Heure</TH>";
                    echo "<TH><a href=$PHP_SELF?action=VoirMenu&menu=outils&TriFic=Taille#enattente>Taille</a></TH>";
                    echo "<TH>Age</TH>";
                    echo "<TH><a href=$PHP_SELF?action=VoirMenu&menu=outils&TriFic=Ugsel#enattente>Ugsel</a></TH>";
                    echo "<TH><a href=$PHP_SELF?action=VoirMenu&menu=outils&TriFic=Sport#enattente>Sport</a></TH>";
                    echo "<TH>Description</TH>";
                    echo "<TH>Obs</TH>";
                    echo "<TH></TH></TR>";
                }
                echo "<TR CLASS='";
                if (($actionfichier == "supprimer") && ($tabfile[$i]["Nom"] == $fichier)) echo "trsuppr"; else if ( (round($i / 2) - ($i / 2)) == "0" ) echo "tr1"; else echo "tr2";
                echo "'>";
                echo "<TD ALIGN = 'center'>".($i+1)."</TD>";
                echo "<TD ALIGN = 'center'>".$tabfile[$i]["Type"]."</TD>";
                echo "<TD ALIGN = 'right'>".$tabfile[$i]["Date"]."</TD>";
                echo "<TD>".$tabfile[$i]["Heure"]."</TD>";
                echo "<TD>".$tabfile[$i]["Taille"]."</TD>";
                echo "<TD ALIGN = 'center'>".$tabfile[$i]["Age"]." Jr</TD>";
                echo "<TD ALIGN = 'center'>".$tabfile[$i]["Ugsel"]."</TD>";
                echo "<TD>".$tabfile[$i]["Sport"]."</TD>";
                echo "<TD>".$tabfile[$i]["Description"]."</TD>";
                echo "<TD>".$tabfile[$i]["Obs"]."</TD>";
                echo "<TD>";
                if (($actionfichier == "supprimer") && ($tabfile[$i]["Nom"] == $fichier)) {
                    echo " <a href=$PHP_SELF?action=VoirMenu&menu=outils&fichier=".stripslashes(rawurlencode($fichier))."&actionfichier=confirmesupprimer&TriFic=$TriFic#enattente>&nbsp;Confirmer la suppression&nbsp;</a>";
                    echo "<a href=$PHP_SELF?action=VoirMenu&menu=outils&TriFic=$TriFic#enattente>&nbsp;Annuler&nbsp;</a><BR>";
                } else {
                    echo " <a href=$PHP_SELF?action=VoirMenu&menu=outils&fichier=".stripslashes(rawurlencode($tabfile[$i]["Nom"]))."&actionfichier=supprimer&TriFic=$TriFic#enattente>&nbsp;Supprimer&nbsp;</a>";
                    echo "<a href=".stripslashes(rawurlencode($tabfile[$i]["Nom"])).">&nbsp;T�l�charger&nbsp;</a><BR>";
                }
                echo "</TD>";
                echo "</TR>";
                $filenoms[$i] = $tabfile[$i]["Nom"];
                $filedes[$i]  = $tabfile[$i]["Date"]." � ".$tabfile[$i]["Heure"]." (".$tabfile[$i]["Taille"].")";
            }
            echo "</TABLE>";

            echo "<HR CLASS = 'hr1'>\n";

        }
        if (isset($valideBasesExternes)) {
            bf_mysql_query("UPDATE `Paramweb` SET `BasesExternes` = '$basesexternes'");
        }

        $tabrep = RetourneRep ("../", "ud");
        if (!($tabrep == 0)) {
            $req = bf_mysql_query("SELECT `BasesExternes` FROM `Paramweb`");
            if ((!(!$req)) && (mysql_num_rows($req) > 0)) {$data = mysql_fetch_assoc($req); $data = $data["BasesExternes"];} else $data = "";
            echo "<FORM method='post'>";
            echo "<TABLE CLASS = 'tableconopt'>";
            echo "<TR>";
            echo "<TD>";
            echo "> INSCRIPTION &nbsp;&nbsp;";
            echo "Rechercher les licenci�s introuvables dans les bases externes (noms s�par�s par des points virgules) &nbsp;&nbsp;";
            echo "<input name='valideBasesExternes' type='submit' id='valideBasesExternes' value='Valider' class='bouton'>";
            echo "<TEXTAREA name='basesexternes' rows='1'>".$data."</textarea><BR>";
            echo "</TD>";
            echo "</TR>";
            echo "</TABLE>";
            echo "</FORM>";

            echo "<HR CLASS = 'hr1'>\n";

            echo "<TABLE CLASS = 'tableconopt' ID = 'miseajour'>";
            echo "<TR>";
            echo "<TD>";
            echo "> MISE A JOUR &nbsp;&nbsp;";
            echo "</TD>";
            echo "</TR>";
            echo "</TABLE>";
            echo "<TABLE CLASS = 'tablecompets' style = 'Margin-top:0px;Margin-left:40px;Margin-bottom:10px;'>";
            for($i=0; $i < count($tabrep); $i++) {
                if ($i == 0) {
                    echo "<CAPTION style='text-align:left; padding-bottom:4px;'>Ugsel affili�es trouv�es sur le serveur : </CAPTION>";
                    echo "<TR><TH>N�</TH>";
                    echo "<TH>Ugsel</TH>";
                    echo "<TH>Licenci�s</TH>";
                    echo "<TH>En interne</TH>";
                    echo "<TH>";
                    if (($actionfichier == "importerugsel") && ($ugselimp == 'tout') ) {
                        echo " <a href=$PHP_SELF?action=VoirMenu&menu=outils&ugselimp=tout&actionfichier=confirmeimporterugsel#miseajour>&nbsp;Confirmer l'importation&nbsp;</a>";
                        echo "<a href=$PHP_SELF?action=VoirMenu&menu=outils#miseajour>&nbsp;Annuler&nbsp;</a><BR>";
                    } else echo "<a href=$PHP_SELF?action=VoirMenu&menu=outils&ugselimp=tout&actionfichier=importerugsel#miseajour>&nbsp;Importer tout&nbsp;</a>";
                    echo "</TH></TR>";
                }
                echo "<TR CLASS='";
                if (($actionfichier == "importerugsel") && (($tabrep[$i]["Nom"] == $ugselimp) || ($ugselimp == 'tout'))) echo "trsuppr"; else if ( (round($i / 2) - ($i / 2)) == "0" ) echo "tr1"; else echo "tr2";
                echo "'>";
                if ($tabrep[$i]["Bdd"] != "*") {
                    echo "<TD ALIGN = 'center'>".($i+1)."</TD>";
                    echo "<TD ALIGN = 'center'>".$tabrep[$i]["Nom"]."</TD>";
                    echo "<TD ALIGN = 'center'>".$tabrep[$i]["Lic Externe"]."</TD>";
                    echo "<TD ALIGN = 'center'>".$tabrep[$i]["Lic Interne"]."</TD>";
                    echo "<TD ALIGN = 'center'>";
                    if (($actionfichier == "importerugsel") && (($tabrep[$i]["Nom"] == $ugselimp)) ) {
                        echo " <a href=$PHP_SELF?action=VoirMenu&menu=outils&ugselimp=".stripslashes(rawurlencode($tabrep[$i]["Nom"]))."&actionfichier=confirmeimporterugsel#miseajour>&nbsp;Confirmer l'importation&nbsp;</a>";
                        echo "<a href=$PHP_SELF?action=VoirMenu&menu=outils#miseajour>&nbsp;Annuler&nbsp;</a><BR>";
                    } else {
                        echo " <a href=$PHP_SELF?action=VoirMenu&menu=outils&ugselimp=".stripslashes(rawurlencode($tabrep[$i]["Nom"]))."&actionfichier=importerugsel#miseajour>&nbsp;Importer&nbsp;</a>";
                    }
                    echo "</TD>";
                }

                echo "</TR>";
            }
            echo "</TABLE>";

            echo "<HR CLASS = 'hr1'>\n";
        }

        if ($LICENCES == "Oui") {
            if (isset($valideimpressionlic)) {
                bf_mysql_query("UPDATE `Paramweb` SET `ImpressionLic` = '$optionimpressionlic', `AssUgsel` = '$optionimpressionlicAss'");
            }
            $req = bf_mysql_query("SELECT `ImpressionLic`, `AssUgsel` FROM `Paramweb`");
            if ((!(!$req)) && (mysql_num_rows($req) > 0)) {$data = mysql_fetch_assoc($req); $data1 = $data["ImpressionLic"];$data2 = $data["AssUgsel"];} else { $data1 = 0; $data2 = "";}
            echo "<FORM method='post'>";
            echo "<TABLE CLASS = 'tableconopt'>";
            echo "<TR><TD>";
            echo "> IMPRESSION EN LIGNE DES LICENCES &nbsp;&nbsp;";
            echo "<input type='radio' name='optionimpressionlic' value='0'";
            if (($data['ImpressionLic']) == 0) echo " checked='checked'";
            echo ">Ne pas autoriser &nbsp&nbsp;";
            echo "<input type='radio' name='optionimpressionlic' value='1'";
            if (($data['ImpressionLic']) == 1) echo " checked='checked'";
            echo ">Autoriser tout le monde &nbsp&nbsp;";
            echo "<input type='radio' name='optionimpressionlic' value='2'";
            if (($data['ImpressionLic']) == 2) echo " checked='checked'";
            echo ">Autoriser seulement les administrateurs &nbsp&nbsp;";
            echo "Assurance Ugsel <input type='text' size='8' name='optionimpressionlicAss' value='".$data2."'> &nbsp; ";
            echo "<input name='valideimpressionlic' type='submit' id='valideimpressionlic' value='Valider' class='bouton'>";
            echo "</TD></TR></TABLE></FORM>";

            echo "<HR CLASS = 'hr1'>\n";
        }

        if (isset($valideinscriptionlic)) {
            bf_mysql_query("UPDATE `Paramweb` SET `InscriptionLic` = '$optioninscriptionlic'");
        }
        $req = bf_mysql_query("SELECT `InscriptionLic` FROM `Paramweb`");
        if ((!(!$req)) && (mysql_num_rows($req) > 0)) {$data = mysql_fetch_assoc($req); $data = $data["InscriptionLic"];} else $data = 0;
        echo "<FORM method='post'>";
        echo "<TABLE CLASS = 'tableconopt'>";
        echo "<TR><TD>";
        echo "> INSCRIPTION EN LIGNE DES 'Non Inscrits' &nbsp;&nbsp;";
        echo "<input type='radio' name='optioninscriptionlic' value='0'";
        if (($data['InscriptionLic']) == 0) echo " checked='checked'";
        echo ">Ne pas autoriser &nbsp&nbsp;";
        echo "<input type='radio' name='optioninscriptionlic' value='2'";
        if (($data['InscriptionLic']) == 2) echo " checked='checked'";
        echo ">Autoriser avec validation automatique &nbsp;&nbsp;";
        echo "<input type='radio' name='optioninscriptionlic' value='1'";
        if (($data['InscriptionLic']) == 1) echo " checked='checked'";
        echo ">Autoriser avec validation des administrateurs &nbsp;&nbsp;";
        echo "<input name='valideinscriptionlic' type='submit' id='valideinscriptionlic' value='Valider' class='bouton'>";
        echo "</TD></TR></TABLE></FORM>";

        echo "<HR CLASS = 'hr1'>\n";


        echo "<TABLE CLASS = 'tableconopt'>";
        echo "<TR><TD>";
        echo "> INFO SITE &nbsp;&nbsp;";
        echo $BDD." : &nbsp;";

        PurgeTables();

        $mesinfo = array("Secteurs","Etablissements","Licenci�s","Sports","Cat�gories","Epreuves","Comp�titions","Participations","Equipes");
        for ($i = 0; $i < count($mesinfo); $i++) {
            if (is_array($mesinfo[$i])) $manomtable = $mesinfo[$i][0]; else $manomtable = $mesinfo[$i];
            if (is_array($mesinfo[$i])) $matable = $mesinfo[$i][1]; else $matable = $mesinfo[$i];
            echo CompteEnr($matable) . " ugselweb.php" .$manomtable;
            if ($i < count($mesinfo)-1) echo " / ";
        }
        echo "</TD></TR></TABLE>";

        $taillerep = TailleRep(".");
        if ($taillerep > 0) {
            echo "<HR CLASS = 'hr1'>\n";
            echo "<TABLE CLASS = 'tableconopt'>";
            echo "<TR><TD>";
            echo "> ESPACE SUR LE SERVEUR &nbsp;&nbsp;";
            if ($taillerep > $QUOTA) echo " <BLINK> Supprimez des fichiers ! </BLINK>";
            echo ConvertTaille($taillerep)." d'espace occup� sur un total disponible de ".ConvertTaille($QUOTA);
            echo " (". sprintf('%.2f ',$taillerep / $QUOTA * 100) ."%).";
            echo "</TD></TR></TABLE>";
        }

        if ( ($REQUETES == "Oui") ) {
            if (isset($validerequete)) bf_mysql_query($requete);
            echo "<HR CLASS = 'hr1'>\n";
            echo "<FORM method='post'>";
            echo "<TABLE CLASS = 'tableconopt'>";
            echo "<TR>";
            echo "<TD>";
            echo "> REQUETE &nbsp;&nbsp;";
            echo "Requ�te &nbsp;&nbsp;";
            echo "<input name='validerequete' type='submit' id='validerequete' value='Valider' class='bouton'>";
            echo "<TEXTAREA name='requete' rows='3'>";
            if (!(isset($requete))) echo "UPDATE Etablissements INNER JOIN Secteurs ON Etablissements.EtabSecCode = Secteurs.SecCode SET EtabMemo3 = IF(RAND() > 0.33, IF(RAND() > 0.66, CONCAT(SecLibel, LOWER(EtabNomCourt), FLOOR(RAND()*100)), CONCAT(LOWER(EtabNomCourt), SecLibel, FLOOR(RAND()*100))), CONCAT(FLOOR(RAND()*100), LOWER(EtabNomCourt), SecLibel)) WHERE EtabMemo3 = '' OR EtabMemo3 IS NULL"; else echo $requete;
            echo "</textarea><BR>";
            echo "</TD>";
            echo "</TR>";
            echo "</TABLE>";
            echo "</FORM>";
        }

        echo "<BR>";
    }

    if ($menu == "apropos"){
        echo "<BR>";
        echo "<TABLE CLASS = 'tableconopt'>";
        echo "<TR><TD><B> &nbsp; &nbsp UGSEL Web </B></TD></TR></TABLE>";
        echo "<TABLE CLASS = 'tableconopt'>";
        echo "<TR><TD> &nbsp; &nbsp; &nbsp;  Version  : $VERSION </TD></TR>";
        echo "<TR><TD> &nbsp; &nbsp; &nbsp;  Propulsion : <a TARGET='_blank' href=http://www.ugsel.org> Ugsel Nationale</a></TD></TR>";
        echo "<TR><TD> &nbsp; &nbsp; &nbsp;  Optimisation de la navigation : <a TARGET='_blank' href=http://www.mozilla-europe.org/fr/firefox> FireFox</a></TD></TR>";
        if (!($Consult)) echo "<TR><TD> &nbsp; &nbsp; &nbsp;  Documentation : <a TARGET='_blank' href='".$ADRSITE."/UgselWeb-Documentation.pdf#pagemode=bookmarks&zoom=100'> Cliquez ici </a></TD></TR>";
        if ($Adm) echo "<TR><TD> &nbsp; &nbsp; &nbsp;  Documentation Administrateurs : <a TARGET='_blank' href='".$ADRSITE."/UgselWeb-Documentation-Admin.pdf#pagemode=bookmarks&zoom=100'> Cliquez ici </a></TD></TR>";
        echo "<TR><TD></TD></TR>";
        echo "</TABLE>";
        echo "<BR>";

        if ($Adm) {
            echo "<TABLE CLASS = 'tableconopt'><TR><TD><B><BLINK> &nbsp; &nbsp INFORMATION </BLINK></B></TD></TR></TABLE>";
            echo "<HR CLASS = 'hr1'>\n";
            echo "<TABLE CLASS = 'tableconopt'>";
            echo "<TR><TD ALIGN = 'Center' VALIGN = 'Top' style ='white-space:nowrap;'>1 Sept 2012</TD><TD>L'adresse du serveur a chang� : <B>ugselweb.org</B> (et non plus : ugsel-bretagne.org). <BR>Pensez � modifier les liens de vos sites internet.</TD></TR>";
            echo "</TABLE>";
            echo "<HR CLASS = 'hr1'>\n";
        }
    }
}