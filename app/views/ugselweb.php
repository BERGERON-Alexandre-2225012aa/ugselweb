<?php

global $PHP_SELF;
global $BDD, $HOSTNAME, $UTILISATEUR, $MDP, $CONSULTATION, $Consult, $ENTRER, $UGSELNOM;
global $optionexporttype;

session_start();
getTime();

if(!empty($_GET)) extract($_GET);
if(!empty($_POST)) extract($_POST);
if ((isset($_GET['Adm'])) || (isset($_POST['Adm']))) logout();

if (isset($valideEditCouleurs)) {
    for( $i = 1; $i < 17; $i++ ) {
        $Couleurs[3][$i] = $_POST['nomCouleur'.$i];
    }
}

if (isset($_COOKIE["ugselweb"])){
    if (isset($_COOKIE["ugselweb"]['LIGNES_PAR_PAGE'])) $LIGNES_PAR_PAGE = $_COOKIE['ugselweb']['LIGNES_PAR_PAGE'];
    if (isset($_COOKIE["ugselweb"]['TAILLE']))          $TAILLE          = $_COOKIE['ugselweb']['TAILLE'];
    if (isset($_COOKIE["ugselweb"]['COULEUR']))         $COULEUR         = $_COOKIE['ugselweb']['COULEUR'];
    if (isset($_COOKIE["ugselweb"]['SON']))             $SON             = $_COOKIE['ugselweb']['SON'];
} else {
    setcookie("ugselweb[LIGNES_PAR_PAGE]", "$LIGNES_PAR_PAGE", time() + 3600*24*365);
    setcookie("ugselweb[TAILLE]",          "$TAILLE"         , time() + 3600*24*365);
    setcookie("ugselweb[COULEUR]",         "$COULEUR"        , time() + 3600*24*365);
    setcookie("ugselweb[SON]",             "$SON"            , time() + 3600*24*365);
}

if (isset($_SESSION['LignesParPage'])) $LIGNES_PAR_PAGE = $_SESSION['LignesParPage'];
if (isset($_SESSION['Son'])) $SON = $_SESSION['Son'];

if (isset($ValideLignesParPage)) {
    $LIGNES_PAR_PAGE = $LignesParPage;
    $_SESSION['LignesParPage'] = $LIGNES_PAR_PAGE;
    if (isset($_COOKIE["ugselweb"]['LIGNES_PAR_PAGE'])) setcookie("ugselweb[LIGNES_PAR_PAGE]", "$LignesParPage", time() + 3600*24*365);
}
if (isset($ValideCouleur)) {
    $COULEUR = $Couleur;
    $_SESSION['Couleur'] = $COULEUR;
    if (isset($_COOKIE["ugselweb"]['COULEUR'])) setcookie("ugselweb[COULEUR]", "$Couleur", time() + 3600*24*365);
}

if (isset($ValideSon)) {
    $SON = $Son;
    $_SESSION['Son'] = $SON;
    if (isset($_COOKIE["ugselweb"]['SON'])) setcookie("ugselweb[SON]", "$Son", time() + 3600*24*365);
    JoueSon("sonok.wav");
}

$tailles = array("6pt","7pt","8pt","9pt","10pt","11pt","12pt","13pt","14pt","15pt","16pt","17pt");
if (isset($_SESSION['Taille'])) $TAILLE  = $_SESSION['Taille'];
if (isset($modiftaille)) {
    $TAILLE = $modiftaille;
    if ($TAILLE == -1) $TAILLE = intval(count($tailles)/2);
    if ($TAILLE < 1) $TAILLE = 1;
    if ($TAILLE > (count($tailles)-1)) $TAILLE = count($tailles)-1;
    $_SESSION['Taille'] = $TAILLE;
    if (isset($_COOKIE["ugselweb"]['TAILLE'])) setcookie("ugselweb[TAILLE]", "$TAILLE", time() + 3600*24*365);
}
$taille    = $tailles[$TAILLE];
$tailleinf = $tailles[($TAILLE - 1)];

if (isset($_SESSION['Couleur'])) $COULEUR  = $_SESSION['Couleur'];

if ($CONSULTATION != "Non") {
    $_SESSION['login']  = "Consultant";
    $_SESSION['log  ']  = $BDD;
    $_SESSION['LignesParPage']  = $LIGNES_PAR_PAGE;
    $_SESSION['Couleur']= $COULEUR;
    $_SESSION['Son']    = $SON;
    if ($ENTRER == " Entrer ") $action = "VoirMenu";
    if (!(isset($action)) || empty($action) || $action == "") $action = "";
} else {
    if (!(isset($action)) || empty($action) || $action == "") $action = "logon";
}

if ($action == "logon") logon();
else if ($action == "logout") {
    bf_mysql_query('DELETE FROM Connexions where Session = "'.Session_id().'"');
    logout();
}
else if ( $action == "Connexion" ) logon_submit();
else if (( isset($_SESSION['login']) && ($_SESSION['log  '] == $BDD)) || ($CONSULTATION != "Non")) {
    if ($CONSULTATION != "Non") {
        $Adm = false;
        $Consult = true;
        $Etab = 0;
        MajConnexions("Consultant");
    } else {
        $Consult = false;
        if ($_SESSION['login'] == "Admin") {
            $Adm = true;
            $Etab = "";
            MajConnexions("Admin");
        } else {
            $Adm = false;
            $Etab = $_SESSION['login'];
            MajConnexions($Etab);
        }
    }

    if (($TRANSFERT_DONNEES == "Url") && (isset($par))) extract(unserialize(urldecode(stripslashes($par))));
    if (($TRANSFERT_DONNEES == "Bdd") && (isset($par))){
        $req = bf_mysql_query('SELECT Param FROM Connexions WHERE Session = "'.session_id().'"');
        if (!(!$req)) {
            $data = mysql_fetch_array($req);
            extract(unserialize(urldecode(stripslashes($data["Param"])))) ;
        }
    }

    if (!empty($_GET)) extract($_GET);
    if (!empty($_POST['ListeSport'])) $ListeSport = $_POST['ListeSport'];
    if ($Consult) $message = "";

    if ( ($CONSULTATION == "Non") && (($_GET["action"] == "exporte") || (isset($exporter)))) {
        if (($optionexporttype == "expser") || ($optionexporttype == "expsqlser") ) {
            debut_html(($CONSULTATION == "Non"));
            VoirMenu();
            fin_html(($CONSULTATION == "Non"));
        }
    } else {
        debut_html(($CONSULTATION == "Non"));
        VoirMenu();
        fin_html(($CONSULTATION == "Non"));
    }

} else logout("Veuillez vous reconnecter.");
?>