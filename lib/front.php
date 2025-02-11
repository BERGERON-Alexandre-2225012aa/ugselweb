<?php
function style_html() {
    global $action, $taille, $tailleinf, $COULEUR, $Couleurs, $ENTRER;
    $CTexte        = $Couleurs[$COULEUR][1];
    $CTexteLien	   = $Couleurs[$COULEUR][2];
    $CTexteLienOver= $Couleurs[$COULEUR][3];
    $CFond         = $Couleurs[$COULEUR][4];
    $CFondTableTh  = $Couleurs[$COULEUR][5];
    $CFondTable1   = $Couleurs[$COULEUR][6];
    $CFondTable2   = $Couleurs[$COULEUR][7];
    $CFondFiltre   = $Couleurs[$COULEUR][8];
    $CFondEdit	   = $Couleurs[$COULEUR][9];
    $CFondSuppr	   = $Couleurs[$COULEUR][10];
    $CFondLienOver = $Couleurs[$COULEUR][11];
    $CMessInfo     = $Couleurs[$COULEUR][12];
    $CMessAlerte   = $Couleurs[$COULEUR][13];
    $CBouton       = $Couleurs[$COULEUR][14];
    $CTexteCompet  = $Couleurs[$COULEUR][15];
    $CFondCompet   = $Couleurs[$COULEUR][16];

    echo "<style type='text/css'>\n";
    if (( ($action == "logon") || ($action == "logout") ) && (!(isset($ENTRER))) ){
        echo "body {margin:8%; font-family: verdana, arial; font-size: $taille; color: $CTexte;}\n";
    } else {
        if ( (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6') !== FALSE) || (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 5') !== FALSE) ) {
            echo "body {font-family: verdana, arial; font-size: $taille; color: $CTexte; background-color:$CFond;}\n";
            echo "div#entete  {left:8px; right:8px; font-family: verdana, arial; font-size: $taille; color: $CTexte; background-color:$CFond;}\n";
            echo "div#contenu {left:8px; right:8px; font-family: verdana, arial; font-size: $taille; color: $CTexte; background-color:$CFond;}\n";
            echo "div#pied    {left:8px; right:8px; font-family: verdana, arial; font-size: $taille; color: $CTexte; background-color:$CFond;}\n";
        } else {
            echo "body {font-family: verdana, arial; font-size: $taille; color: $CTexte; background-color:$CFond;}\n";
            echo "div#entete  {left:8px; right:8px; top:0; position:fixed; background-color:$CFond;padding-top:0.5em; padding-bottom:0.5em;}\n";
            echo "div#contenu {left:8px; right:8px; padding-top:3.5em; padding-bottom:2em;}\n";
            echo "div#pied    {left:8px; right:8px; bottom:0; position:fixed; background-color:$CFond;padding-top:0.5em;padding-bottom:0.5em;}\n";
        }
    }

    echo "form {margin-top: 0; margin-bottom: 0;} \n";
    echo "table {border-collapse:separate; font-size: $taille;} \n";
    echo "table th {padding:5px;background-color: $CFondTableTh; font-weight:normal;} \n";
    echo "table td {padding:4px;} \n";
    echo ".tabledeb,.tablefin,.tableopt,.tableconopt,.tablemenu{width: 100%; border-collapse: collapse;} \n";
    echo ".tablemenu {width: 100%;border-collapse: collapse;} \n";
    echo ".tablemenu td {background-color:$CFondTable1;padding:4px;}\n";
    echo ".tablesousmenu {width: 100%;} \n";
    echo ".tablesousmenu td {padding:2px;} \n";
    echo ".tablecompet {width: 100%;} \n";
    echo ".tablecompet td {padding:6px; font-size:$taille; background-color:$CFondCompet; color:$CTexteCompet;} \n";
    echo ".tablecompet1 {width: 100%;} \n";
    echo ".tablecompet2 {width: 100%; font-size: $tailleinf;border-collapse: collapse;} \n";
    echo ".tablecompet2 td {padding:2px;} \n";
    echo ".tablecompets {background-color: $CFondTable2;font-size: $tailleinf;} \n";
    echo ".tablecompets td {padding:5px 2px;} \n";
    echo ".tableselecteur {font-size:$tailleinf; margin-left:15pt;} \n";
    echo ".tableselecteurEtab {margin: 10px 2px 10px 2px; background-color: $CFondTable2; Color:$CMessInfo; Width:100%; font-size:$tailleinf;border: 1px solid $CFondTableTh;} \n";
    echo ".tableselecteurEtab td {padding:7px 7px;} \n";
    echo ".tablemessage {margin-top: 10px; background-color: $CFondTable2; Color:$CMessInfo; Width:100%; font-size:$tailleinf;border: 4px double $CMessInfo;} \n";
    echo ".tablemessageerreur{margin-top: 10px; background-color: $CFondTable2; Color:$CMessAlerte; Width:100%; font-size:$tailleinf;border: 4px double $CMessAlerte;}\n";
    echo ".thfiltre,.thdercolfiltre {background-color: $CFondFiltre;} \n";
    echo ".thdercol {background-color: $CFondTableTh;} \n";
    echo ".trdeb,.trfin {background-color: $CTexte; color:white; font-size: $tailleinf;} \n";
    echo ".trcompet1,.tr1 {background-color: $CFondTable1;} \n";
    echo ".trcompet2,.tr2 {background-color: $CFondTable2;} \n";
    echo ".trsel {background-color: $CFondEdit;} \n";
    echo ".tredit {background-color: $CFondEdit;} \n";
    echo ".tredit td {padding:1px 2px;} \n";
    echo ".trimpexp {background-color: $CFondEdit;} \n";
    echo ".trimpexp td {padding:4px 2px;} \n";
    echo ".trsuppr {background-color: $CFondSuppr;} \n";
    echo ".trtotal {background-color: $CFondTableTh;} \n";
    echo ".tddercol {text-align: center;}\n";
    echo ".hr1,.hr2 {color: $CTexte; height: 1px; border:1px; width: 100%; background-color: $CTexte;} \n";
    echo "input {font-family: verdana, arial; font-size: $tailleinf;}";
    echo ".listederoulante {font-family: verdana, arial; font-size: $tailleinf; border-width:1px;}";
    echo "textarea {font-family : verdana, arial; width: 100%; font-size: $tailleinf; margin-top: 5px; margin-bottom:5px;}";
    echo "a {text-decoration:none; color: $CTexte;} \n";
    echo "a:link {text-decoration:none;} \n";
    echo "a:visited {text-decoration:none;} \n";
    echo "a:hover {text-decoration:none; background-color:$CFondLienOver; color:$CTexteLienOver;} \n";
    echo ".adecon {text-decoration:none; color:$CTexteLien} \n";
    echo ".tailleur {text-decoration:none; color:$CTexte} \n";
    echo ".inv {text-decoration:none; background-color:$CTexte; color:$CTexteLien;} \n";
    echo ".navig {text-decoration:none; color:$CTexte;} \n";
    echo ".bouton {border-color:$CBouton;font-size:$tailleinf;text-decoration:none;color:white;background-color:$CBouton;padding:0px;}\n";
    echo ".boutongrand{border-color:$CBouton;font-size:$tailleinf;text-decoration:none;color:white;background-color:$CBouton;padding:0px;}\n";
    echo ".boutonmoyen{border-color:$CBouton;font-size:$tailleinf;text-decoration:none;color:white;background-color:$CBouton;padding:0px;}\n";
    echo ".boutonpetit{border-color:$CBouton;font-size:$tailleinf;text-decoration:none;color:white;background-color:$CBouton;padding:0px;}\n";

    echo "@media screen {";
    echo ".filmenu, .hr2{display:none} \n";
    echo "}";

    echo "@media print{";
    echo "body{font-family: verdana,arial;font-size:$taille; color:black; border:1px solid #cccccc;padding:3px;}";
    echo "div#contenu {padding-top:0; padding-bottom:0;}";
    echo "div#entete, div#pied, .tabledeb,.tablefin,.tablemenu,.tablesousmenu,.tableopt,.thdercol,.thdercolfiltre,.trcompet2,.tredit,.tddercol,.bouton,.boutongrand,.boutonmoyen,.boutonpetit,.pasimprimer,.navig{display :none}";
    echo "a,.hr2 {text-decoration:none; color: black;} \n";
    echo ".tablecompet td {text-decoration:none; color:black;} \n";
    echo ".tablecompets {border-collapse: collapse;border-spacing:0pt; margin-left:auto;margin-right:auto;width:100%;font-size: $tailleinf;background-color: #EEEEEE;} \n";
    echo ".tablecompets th {padding: 2pt; color:black; font-weight:normal;} \n";
    echo ".tablecompets td {padding: 2pt; color:black; border-bottom-style: solid;border-bottom-width: 1pt;} \n";
    echo ".tableselecteur{margin-left:2pt;margin-right:2pt;width: 100%; margin-top: 0pt; margin-bottom: 0pt; font-size:$tailleinf; Color: black;} \n";
    echo ".tableselecteurEtab {color:black; font-weight:bold; border: 0px;} \n";
    echo ".trcompet1 {font-size:12pt;border-style:double;border-width:4pt;} \n";
    echo "th {font-size: $tailleinf; border-bottom-style: double;border-bottom-width: 2pt;} \n";
    echo ".filmenu {font-size:7pt;} \n";
    echo ".tablemessage, .tablemessageerreur{display:none} \n";
    echo "}";
    echo "</style>\n";
}

function fin_html($AffDeconnexion = false) {
    global $PHP_SELF, $VERSION, $UGSELNOM, $UGSELNOMDEP, $Adm, $action, $CONSULTATION, $ADRSITE;
    echo "</DIV>";
    echo "<DIV id = 'pied'>";
    echo "<TABLE class = 'tablefin'>";
    echo "<TR CLASS = 'trfin'>";
    echo "<TD Width = '90%' >&nbsp; ";
    if ($Adm) echo "Page g�n�r�e par le serveur le ".date("d/m/y � H:i:s ")." en ".getTime()."s" ; else echo "UGSEL Web &nbsp;&nbsp;&nbsp;$UGSELNOM&nbsp;&nbsp;$UGSELNOMDEP&nbsp;&nbsp;&nbsp;";
    echo "</TD>";
    echo "<TD Width = '10%' Align ='Center' >";
    if (($AffDeconnexion)) {
        echo "<a href='$PHP_SELF?action=logout' CLASS = 'adecon'>D�connexion</a>";
    } else {
        if ($CONSULTATION == "Non") {
            $MonUg = split('/', $_SERVER['SCRIPT_NAME']);
            echo "<a CLASS = 'adecon'; href='".$ADRSITE."/".$MonUg[1]."'>&nbsp Retour &nbsp</a>";
        }
    }
    echo "</TD>";
    echo "</TR>";
    echo "</TABLE>\n";
    echo "</DIV>";
    echo "</body>\n";
    echo "</html>\n";
}

function debut_html($AffDeconnexion = false) {
    Global $PHP_SELF, $VERSION, $UGSELNOM, $UGSELNOMDEP, $action, $Etab, $Adm, $menu, $sousmenu, $CONSULTATION, $BDD, $ADRSITE;
    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    if (($action == "logon") || (($action == "logout")) ) {
        echo "<!DOCTYPE html PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>\n";
    } else {
        echo "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.0//EN'>\n";
    }
    echo "<html>\n";
    echo "<head>\n";
    echo "<title>UGSEL Web</title>\n";
    echo "<link rel='shortcut icon' type='image/x-icon' href='".$ADRSITE."/favicon.ico' />\n";
    echo "<link rel='icon' type='image/png' href='".$ADRSITE."/favicon.png' />\n";
    style_html();
    echo "</head>\n";
    if (($action == "logon") || (($action == "logout")) ) {
        echo "<body onLoad='document.forms[\"formlogon\"].elements[\"login\"].focus()'>\n";
    } else {
        if (($sousmenu == "individuels") && ($action == "ajoutedata")) echo "<body onLoad='document.forms[\"formaffichelignes\"].elements[\"ParLicCode\"].focus()'>\n"; else echo "<body>\n";
    }
    echo "<DIV id = 'entete'>";
    echo "<TABLE class = 'tabledeb'>";
    echo "<TR CLASS = 'trdeb'>";
    echo "<TD Width = '80%'>&nbsp; UGSEL Web�&nbsp;&nbsp;&nbsp;$UGSELNOM&nbsp;&nbsp;$UGSELNOMDEP&nbsp;&nbsp;&nbsp;&nbsp;";

    if ($Adm) {
        $req = bf_mysql_query("SELECT COUNT(Session) AS Nbre FROM Connexions");
        if (!(!($req))) {
            $res = mysql_fetch_array($req);
            echo $res['Nbre']." connect�";
            if ($res['Nbre'] > 1) echo "s";
        }
    }

    if (($action != "logon") && ($action != "logout") && (!($Adm)) && ($CONSULTATION == "Non"))  {
        if ($res = mysql_fetch_array(bf_mysql_query("SELECT * FROM Etablissements WHERE EtabNum = ".$Etab)))
            echo sprintf('%06s',$res["EtabNum"])." - ".$res["EtabNomCourt"]." - ".$res["EtabNom"]." - ".$res["EtabVille"];
        else echo "Aucun �tablissement trouv� !";
    }
    echo "</TD>";

    echo "<TD Width = '10%' align = 'right'>";
    if (($AffDeconnexion)) {
        $pageAide = 1;
        if ($Adm) $ficAideAdm = "-Admin"; else $ficAideAdm = "";
        if ($Adm) {
            if (($menu == 'parametres') && ($sousmenu == 'sports'))        $pageAide = 8;
            if (($menu == 'parametres') && ($sousmenu == 'categories'))    $pageAide = 9;
            if (($menu == 'parametres') && ($sousmenu == 'epreuves'))      $pageAide = 10;
            if ($menu == 'etablissements')                                 $pageAide = 11;
            if ($menu == 'licencies')                                      $pageAide = 12;
            if (($menu == 'competitions') && ($sousmenu == 'references'))  $pageAide = 13;
            if (($menu == 'competitions') && ($sousmenu == 'individuels')) $pageAide = 15;
            if (($menu == 'competitions') && ($sousmenu == 'equipes'))     $pageAide = 16;
            if ($menu == 'options')                                        $pageAide = 17;
            if ($menu == 'outils')                                         $pageAide = 18;
            if ($menu == 'connexions')                                     $pageAide = 20;
            if ($menu == 'apropos')                                        $pageAide = 20;
        } else {
            if (($menu == 'competitions') && ($sousmenu == 'individuels')) $pageAide = 4;
            if (($menu == 'competitions') && ($sousmenu == 'equipes'))     $pageAide = 7;
        }
        echo "<a TARGET='_blank' href='".$ADRSITE."/UgselWeb-Documentation$ficAideAdm.pdf#page=$pageAide&pagemode=bookmarks' CLASS = 'adecon' >  Aide  </a>";
    }
    echo "</TD>";

    echo "<TD Width = '10%' align = 'center'>";
    if (($AffDeconnexion)) {
        echo "<a href='$PHP_SELF?action=logout' CLASS = 'adecon'>D�connexion</a>";
    } else {
        if ($CONSULTATION == "Non") {
            $MonUg = split('/', $_SERVER['SCRIPT_NAME']);
            echo "<a CLASS = 'adecon'; href='".$ADRSITE."/".$MonUg[1]."'>&nbsp Retour &nbsp</a>";
        }
    }
    echo "</TD>";
    echo "</TR>";
    echo "</TABLE>\n";
}

function listederoulante($ListeNom, $ListePrompt, $ListeSql, $ListeChampsAff, $ListeChampsFormat, $ListeCl�, $ListeInit, $Largeur="100%") {
    If ($Largeur == "") $Largeur="100%"; else $Largeur = $Largeur."px";
    if (!(is_array($ListeSql))) {
        echo "<select size=1 name='$ListeNom' CLASS ='listederoulante' style='width: $Largeur;'>";
        if ($ListePrompt <> "") echo "<option value=''>$ListePrompt</option>";
        $req = bf_mysql_query($ListeSql);
        while ($res = mysql_fetch_array($req)) {
            echo $res;
            echo "<option value='$res[$ListeCl�]'";
            if (isset($ListeInit)) {if($res["$ListeCl�"] == "$ListeInit") echo " selected";}
            $option = "";
            for ($i = 0; $i < count($ListeChampsAff); $i++) {
                if ($ListeChampsAff[$i] == "-") $option = $option."- ";
                else if ($ListeChampsFormat[$i] == "") $option = $option.$res[$ListeChampsAff[$i]]." "; else $option = $option.sprintf($ListeChampsFormat[$i], $res[$ListeChampsAff[$i]])." ";
            }
            echo ">$option</option>\n";
        }
        echo "</select>";
    } else {
        echo "<select size=1 name='$ListeNom' CLASS ='listederoulante' style='width: $Largeur;'>";
        if ($ListePrompt <> "") echo "<option value=''>$ListePrompt</option>";
        $MonTab = array_values($ListeSql);
        for( $i = 0; $i < count($ListeSql); $i++ ) {
            echo "<option value='";
            if ( (!(isset($ListeCl�))) || $ListeCl� == "") echo array_search($MonTab[$i],$ListeSql); else echo $ListeCl�[$i];
            echo "'";
            if (isset($ListeInit)) if($MonTab[$i] == $ListeInit) echo " selected";
            echo "> $MonTab[$i] </option>\n";
        }
        echo "</select>";
    }
}

function ConstruitZone($zone) {
    for ($i = 0; $i < count($zone); $i++) {
        echo "<input type='hidden' name=".$zone[$i][0]." value='".$zone[$i][1]."'>\n";
    }
}