<?php
/**
 * Actualisation des fiches de frais
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

if(isset($_GET['id']) && isset($_GET['mois'])){
    $fiche = getFicheFraisForfaitise($_GET['id'], $_GET['mois'], $idConnexion);
}
?>
<?php
if(isset($_GET['id']) && isset($_GET['mois']) && !isset($_POST['idVisiteur']) && !isset($_POST['mois'])){
?>
<div id="contenu" class="marginAuto">
    <h2>Actualiser fiche de  du visiteur <?=$_GET['id']?> pour <?=$_GET['mois']?></h2>
    <form action="" method="post">
        <div class="corpsForm">
            <input type="hidden" name="idVisiteur" value="<?=$_GET['id']?>">
            <input type="hidden" name="mois" value="<?=$_GET['mois']?>">

            <?php
                foreach ( $fiche as $frais) {
                    ?>
                    <label><?=$frais['id'].' - '.$frais['libelle']. '('.$frais['montant'].' euros/u)'?></label>
                    <input type="text" name="<?=$frais['id']?>" value="<?=$frais['quantite']?>">
                    <br>
                    <?php
                }
            ?>
            <input type="submit" value="Actualiser" class="button">
        </div>
    </form>
</div>
<?php
}
if(isset($_POST['idVisiteur']) && isset($_POST['mois'])){

    if(isset($_POST['ETP'])){
        $actionETP = editFicheFraisForfaitise($_POST['idVisiteur'], $_POST['mois'], 'ETP', $_POST['ETP'], $idConnexion);
    }
    if(isset($_POST['KM'])){
        $actionKM = editFicheFraisForfaitise($_POST['idVisiteur'], $_POST['mois'],'KM', $_POST['KM'], $idConnexion);
    }
    if(isset($_POST['NUI'])){
        $actionNUI = editFicheFraisForfaitise($_POST['idVisiteur'], $_POST['mois'], 'NUI', $_POST['NUI'], $idConnexion);
    }
    if(isset($_POST['REP'])){
        $actionREP = editFicheFraisForfaitise($_POST['idVisiteur'], $_POST['mois'], 'REP', $_POST['REP'], $idConnexion);
    }

    ?>
    <div id="contenu" class="marginAuto">
        <h2>Actualiser fiche de  du visiteur <?=$_GET['id']?> pour <?=$_GET['mois']?> terminé</h2>
        <h3>Nouvelle fiche forfait :</h3>
        <form action="cValiderFicheFrais" method="post">
            <?php
            if( $actionETP == true){
                ?>
                <label>ETP - Forfait étape : <?=$_POST['ETP']?></label><br>
                <?php
            }
            if( $actionKM == true ){
                ?>
                <label>KM - Frais kilométrique : <?=$_POST['KM']?></label><br>
                <?php
            }
            if( $actionKM == true ){
                ?>
                <label>NUI - Nuitée hôtel : <?=$_POST['NUI']?></label><br>
                <?php
            }
            if( $actionKM == true ){
                ?>
                <label>REP - Repas restaurant : <?=$_POST['REP']?></label><br>
                <?php
            }
            ?>
            <input type="hidden" name="lstUser" value="<?=$_GET['id']?>">
            <input type="submit" value="Confirmer" class="button">
        </form>
    </div>
    <?php
}
?>
<?php
require($repInclude . "_pied.inc.html");
require($repInclude . "_fin.inc.php");
?>