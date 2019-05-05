<?php
/**
 * Script de contrôle et d'affichage du cas d'utilisation "Consulter une fiche de frais"
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

  // on récupère toutes les fiches au statut VA
  $fiches = getAllFichesVA($idConnexion);
?>
  <!-- Division principale -->
  <div id="contenu" class="marginAuto">
    <h2>Fiche de frais VA</h2>
    <div class="encadre">
        <table class="listeLegere" style="width: 100%;">
            <caption>Quantités des éléments forfaitisés</caption>
            <tr>
                <th>Visiteur</th>
                <th>Mois</th>
                <th>Date modification</th>
                <th>Actions</th>
            </tr>
            <?php
            foreach ( $fiches as $fiche) {
            ?>
            <tr>
                <td class="qteForfait">
                    <?=$fiche['prenom'].' '.$fiche['nom'];?>
                </td>
                <td class="qteForfait">
                    <?=$fiche['mois'];?>
                </td>
                <td class="qteForfait">
                    <?=$fiche['dateModif'];?>
                </td>
                <td class="qteForfait">
                    <a href="cSuiviFichier.php?id=<?=$fiche["idVisiteur"]?>&mois=<?=$fiche["mois"]?>"><img style="height: 25px;padding: 2px;" src="images/see.png"></td></a>
            </tr>
            <?php
            }
            ?>
        </table>
    </div>
  </div>
<?php        
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
?> 