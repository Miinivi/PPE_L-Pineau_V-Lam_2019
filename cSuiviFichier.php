<?php
/**
 * Affiche la fiche de frais et permet de changer le statut
 * @package default
 * @todo  RAS
 */
$repInclude = './include/';
require($repInclude . "_init.inc.php");

// page inaccessible si visiteur non connecté
if ( ! estVisiteurConnecte() ) {
    header("Location: cSeConnecter.php");
}
require($repInclude . "_entete.inc.html");
require($repInclude . "_sommaireCompt.inc.php");

$fiche = getAllInfosFiche($idConnexion, $_GET['id'], $_GET['mois']);
//var_dump($fiche);
$frais = getFraisForfait($fiche['idFraisForfait'], $idConnexion);
?>
<!-- Division principale -->
<div id="contenu" class="marginAuto">
    <h2>Fiche de frais du visiteur <?=$_GET['id']?> pour <?=$_GET['mois']?></h2>
    <div class="encadre">
        Visiteur : <?=$fiche['nom'].' '.$fiche['prenom']?><br>
        Mois : <?=$fiche['mois']?><br>
        Nombre de justificatifs : <?=$fiche['nbJustificatifs']?><br>
        Frais forfaitisés :<br>
        - <?=$frais['libelle'] ?> : <?=$frais['montant']*$fiche['quantite'] ?> euros <br>
        Frais Hors forfait : <?=$fiche['montant']?> euros<br>
        Libellé hors-forfait : <?=$fiche['libelle']?><br><br>

        <a href="cSuiviOperationSuivi.php?op=MP&id=<?=$fiche["idVisiteur"]?>&mois=<?=$fiche["mois"]?>" class="button">Mettre en paiement</a>
        <a href="cSuiviOperationSuivi.php?op=RB&id=<?=$fiche["idVisiteur"]?>&mois=<?=$fiche["mois"]?>" class="button">Hors forfait remboursé</a><br><br>
    </div>
</div>
