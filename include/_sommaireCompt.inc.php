<?php
/** 
 * Contient la division pour le sommaire, sujet à des variations suivant la 
 * connexion ou non d'un utilisateur, et dans l'avenir, suivant le type de cet utilisateur 
 * @todo  RAS
 */

?>
    <!-- Division pour le sommaire -->
    <div id="menuGauche" class="flex-column">
     <div id="infosUtil">
    <?php      
      if (estVisiteurConnecte() ) {
          $idUser = obtenirIdUserConnecte() ;
          $lgUser = obtenirDetailVisiteur($idConnexion, $idUser);
          $nom = $lgUser['nom'];
          $prenom = $lgUser['prenom'];            
    ?>
        <div>
    <?php  
            echo $nom . " " . $prenom ;
    ?>
        </div>
        <div>Comptable</div>
    <?php
       }
    ?>  
      </div>  
<?php      
  if (estVisiteurConnecte() ) {
?>
   <div class="centerAlign">
        <ul id="menuList" class="flex-row centerAlign">
           <li class="smenu">
              <a href="cAccueilComptable.php" title="Page d'accueil">Accueil</a>
           </li>
           <li class="smenu">
              <a href="cValiderFicheFrais.php" title="Saisie fiche de frais du mois courant">Valider fiche de frais</a>
           </li>
           <li class="smenu">
              <a href="cSuivrePaiementFicheFrais.php" title="Consultation de mes fiches de frais">Suivre paiement fiche de frais</a>
           </li>
           <li class="smenu">
              <a href="cSeDeconnecter.php" title="Se déconnecter">Se déconnecter</a>
           </li>
         </ul>
  </div>
        <?php
          // affichage des éventuelles erreurs déjà détectées
          if ( nbErreurs($tabErreurs) > 0 ) {
              echo toStringErreurs($tabErreurs) ;
          }
  }
        ?>
    </div>
    