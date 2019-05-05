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

if(isset($_GET['id']) && isset($_GET['mois']) && !isset($_POST['idVisiteur']) && !isset($_POST['mois'])){
    $fiche = getHorsForfait($_GET['id'], $_GET['mois'], $idConnexion);

?>
<div id="contenu" class="marginAuto">
    <h2>Suppression de la ligne hors-forfait de l'utilisateur <?=$_GET['id']?> pour <?=$_GET['mois']?></h2>
    <h3>Le montant hors-forfait sera reporté sur le mois suivant.</h3>
    <form action="" method="post">
        <div class="corpsForm">
            <input type="hidden" name="id" value="<?=$_GET['id']?>">
            <input type="hidden" name="mois" value="<?=$_GET['mois']?>">
            <?php
            foreach ( $fiche as $frais) {
                ?>
                <label> le <?=$frais['date'].' - '.$frais['libelle']. ' : '.$frais['montant'].' euros'?> </label>
                <input type="hidden" name="montant" value="<?=$frais['montant']?>">
                <input type="hidden" name="date" value="<?=$frais['date']?>">
                <input type="hidden" name="libelle" value="<?=$frais['libelle']?>">
                <br>
                <?php
            }
            ?>
            <input type="submit" value="Supprimer" class="button">
        </div>
    </form>
</div>
<?php
}
if(isset($_POST['id']) && isset($_POST['mois'])){
    $deleted = deleteHorsForfait($_POST['id'], $_POST['mois'], $_POST['montant'],$_POST['date'],  $_POST['libelle'], $idConnexion)
    ?>
<div id="contenu" class="marginAuto">
    <h2>Suppression effectuée</h2>
    <form action="" metho="POST">
        <input type="hidden" name="lstUser" value="<?=$_POST['id']?>">
        <input type="submit" value="Retour à la fiche utilisateur" class="button">
    </form>
</div>
    <?php
}
?>
<?php
require($repInclude . "_pied.inc.html");
require($repInclude . "_fin.inc.php");
?>
