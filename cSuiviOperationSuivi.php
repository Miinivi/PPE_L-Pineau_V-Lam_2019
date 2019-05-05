<?php
$repInclude = './include/';
require($repInclude . "_init.inc.php");

// page inaccessible si visiteur non connecté
if ( ! estVisiteurConnecte() ) {
    header("Location: cSeConnecter.php");
}
require($repInclude . "_entete.inc.html");
require($repInclude . "_sommaireCompt.inc.php");

//  scipt des boutons de suiviFichier
editEtatFicheFrais($idConnexion, $_GET['id'], $_GET['mois'], $_GET['op']);
?>


<!-- Division principale -->
<div id="contenu" class="marginAuto">
    <h2>Modification effectué</h2>

    <a href="cSuivrePaiementFicheFrais.php" class="button">Retour à la liste</a>
</div>