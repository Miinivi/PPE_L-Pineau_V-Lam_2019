<?php
/**
 * Script de contrôle et d'affichage du cas d'utilisation "Valider une fiche de frais"
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

  if(isset($_POST['lstUser'])){
      $lstFiches = obtenirFichesPourVisiteur($_POST['lstUser'],$idConnexion);
  }
?>
  <!-- Division principale -->
  <div id="contenu" class="marginAuto">
      <h2>Fiches de frais</h2>
      <form action="" method="post">
          <div class="corpsForm">
              <label for="lstUser">Utilisateur concerné : </label>
              <select id="lstUser" name="lstUser" title="Sélectionnez l'utilisateur souhaité pour la fiche de frais">
                  <?php
                  // on propose tous les utilisateurs à choisir
                  $req = obtenirVisiteur();
                  $listVis = mysqli_query($idConnexion,$req);
                  $lgVis = mysqli_fetch_assoc($listVis);
                  while ( is_array($lgVis) ) {
                      $id  = $lgVis["id"];
                      $nom = $lgVis["nom"];
                      $prenom = $lgVis["prenom"];
                      $login = $lgVis["login"];
                      ?>
                      <option value="<?php echo $id; ?>" <?php if (isset($_POST['lstUser']) && $_POST['lstUser'] == $id ) { ?> selected="selected"<?php } ?> ><?php echo $nom . ' ' . $prenom . ' - ' . $login ?></option>
                      <?php
                      $lgVis = mysqli_fetch_assoc($listVis);
                  }
                  mysqli_free_result($listVis);
                  ?>
              </select>
          </div>
          <div class="piedForm">
              <p>
                  <input type="submit" value="Rechercher" name="submit" class="button">
              </p>
          </div>
      </form>

      <!-- si il y a une visiteur sélectionné, on affiche sa liste de fiche de frais -->
      <?php
      if(isset($_POST['lstUser']))
      {
      ?>
      <table class="listeLegere">
          <caption>Fiches de frais pour le le visiteur <?=$_POST['lstUser']?>
          </caption>
          <tr>
              <th class="date">Mois</th>
              <th class="libelle">Etat</th>
              <th class="libelle">Libellé des frais</th>
              <th class="libelle">Somme totale des frais du forfait</th>
              <th class="libelle">Date du hors-forfait</th>
              <th class="libelle">Libellé du hors-forfait</th>
              <th class="libelle">Somme total du hors-forfait</th>
              <th class="action">Actions</th>
          </tr>
          <?php
          // liste de fiches
          foreach ( $lstFiches as $lst) {
              $etat = getEtat($lst["idEtat"], $idConnexion);
              $frais = getFraisForfait($lst["idFraisForfait"], $idConnexion);
              ?>
              <tr>
                  <td><?php echo $lst["mois"] ; ?></td>
                  <td><?php echo $etat["libelle"] ; ?></td>
                  <td><?php echo $frais["libelle"] ; ?></td>
                  <td><?php echo $frais["montant"] * $lst["quantite"]; ?></td>
                  <td><?php echo $lst["date"] ; ?></td>
                  <td><?php echo $lst['libelle']?></td>
                  <td><?php echo $lst['montant']?></td>
                  <td class="centerAlign">
                      <a href="cEditFicheFrais.php?id=<?=$lst["idVisiteur"]?>&mois=<?=$lst["mois"]?>" class="centerAlign" t1itle="Actualiser informations des frais forfaitisés"><img style="height: 25px;padding: 2px;" src="images/edit.svg"> </a>
                      <a href="cDeleteHorsForfait.php?id=<?=$lst["idVisiteur"]?>&mois=<?=$lst["mois"]?>" class="centerAlign" title="Suppression des lignes hors-forfait non valides"><img style="height: 22px;padding: 4px;" src="images/delete.svg"></a>
                  </td>
              </tr>
          <?php
          }
          ?>
      </table>
      <?php
      }
      ?>
      <!--<h3>Mois à sélectionner : </h3>
      <form action="" method="post">
      <div class="corpsForm">
          <input type="hidden" name="etape" value="validerConsult" />
      <p>
        <label for="lstMois">Mois : </label>
        <select id="lstMois" name="lstMois" title="Sélectionnez le mois souhaité pour la fiche de frais">
            <?php
/*                // on propose tous les mois pour lesquels le visiteur a une fiche de frais
                $req = obtenirReqMoisFicheFrais(obtenirIdUserConnecte());
                $idJeuMois = mysqli_query($idConnexion,$req);
                $lgMois = mysqli_fetch_assoc($idJeuMois);
                while ( is_array($lgMois) ) {
                    $mois = $lgMois["mois"];
                    $noMois = intval(substr($mois, 4, 2));
                    $annee = intval(substr($mois, 0, 4));
            */?>
            <option value="<?php /*echo $mois; */?>"<?php /*if ($moisSaisi == $mois) { */?> selected="selected"<?php /*} */?>><?php /*echo obtenirLibelleMois($noMois) . " " . $annee; */?></option>
            <?php
/*                    $lgMois = mysqli_fetch_assoc($idJeuMois);
                }
                mysqli_free_result($idJeuMois);
            */?>
        </select>
      </p>
      </div>
      <div class="piedForm">
      <p>
        <input id="ok" type="submit" value="Valider" size="20"
               title="Demandez à consulter cette fiche de frais" class="button"/>
        <input id="annuler" type="reset" value="Effacer" size="20" class="button"/>
      </p> 
      </div>
        
      </form>-->

  </div>
<?php        
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
?> 