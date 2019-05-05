<?php
/** 
 * Regroupe les fonctions d'acc�s aux donn�es.
 * @package default
 * @author Arthur Martin
 * @todo Fonctions retournant plusieurs lignes sont � r��crire.
 */

/** 
 * Se connecte au serveur de donn�es mysqli.                      
 * Se connecte au serveur de donn�es mysqli � partir de valeurs
 * pr�d�finies de connexion (h�te, compte utilisateur et mot de passe). 
 * Retourne l'identifiant de connexion si succ�s obtenu, le bool�en false 
 * si probl�me de connexion.
 * @return resource identifiant de connexion
 */
function connecterServeurBD() {
    $hote = "localhost";
    $login = "root";
    $mdp = "";
    $conn =  mysqli_connect($hote, $login, $mdp);
    if (mysqli_connect_errno())
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    return $conn;
}

/**
 * S�lectionne (rend active) la base de donn�es.
 * S�lectionne (rend active) la BD pr�d�finie gsb_frais sur la connexion
 * identifi�e par $idCnx. Retourne true si succ�s, false sinon.
 * @param resource $idCnx identifiant de connexion
 * @return boolean succ�s ou �chec de s�lection BD 
 */
function activerBD($idCnx) {
    $bd = "gsb_valide";
    $query = "SET CHARACTER SET utf-8 ; ";
    // Modification du jeu de caract�res de la connexion
    $res = mysqli_query($idCnx,$query); 
    $ok = mysqli_select_db($idCnx, $bd);
    
    return $ok;
}

/** 
 * Ferme la connexion au serveur de donn�es.
 * Ferme la connexion au serveur de donn�es identifi�e par l'identifiant de 
 * connexion $idCnx.
 * @param resource $idCnx identifiant de connexion
 * @return void  
 */
function deconnecterServeurBD($idCnx) {
    mysqli_close($idCnx);
}

/**
 * Echappe les caract�res sp�ciaux d'une cha�ne.
 * Envoie la cha�ne $str �chapp�e, c�d avec les caract�res consid�r�s sp�ciaux
 * par mysqli (tq la quote simple) pr�c�d�s d'un \, ce qui annule leur effet sp�cial
 * @param string $str cha�ne � �chapper
 * @return string cha�ne �chapp�e 
 */    
function filtrerChainePourBD($str,$idCnx) {
    activerBD($idCnx);
    if ( ! get_magic_quotes_gpc() ) { 
        // si la directive de configuration magic_quotes_gpc est activ�e dans php.ini,
        // toute cha�ne re�ue par get, post ou cookie est d�j� �chapp�e 
        // par cons�quent, il ne faut pas �chapper la cha�ne une seconde fois                              
        $str = mysqli_real_escape_string($idCnx,$str);
    }
    return $str;
}

/** 
 * Fournit les informations sur un visiteur demand�. 
 * Retourne les informations du visiteur d'id $unId sous la forme d'un tableau
 * associatif dont les cl�s sont les noms des colonnes(id, nom, prenom).
 * @param resource $idCnx identifiant de connexion
 * @param string $unId id de l'utilisateur
 * @return array  tableau associatif du visiteur
 */
function obtenirDetailVisiteur($idCnx, $unId) {
    $id = filtrerChainePourBD($unId,$idCnx);
    $requete = "select id, nom, prenom from visiteur where id='" . $unId . "'";
    $idJeuRes = mysqli_query($idCnx,$requete);  
    $ligne = false;     
    if ( $idJeuRes ) {
        $ligne = mysqli_fetch_assoc($idJeuRes);
        mysqli_free_result($idJeuRes);
    }
    return $ligne ;
}

/** 
 * Fournit les informations d'une fiche de frais. 
 * Retourne les informations de la fiche de frais du mois de $unMois (MMAAAA)
 * sous la forme d'un tableau associatif dont les cl�s sont les noms des colonnes
 * (nbJustitificatifs, idEtat, libelleEtat, dateModif, montantValide).
 * @param resource $idCnx identifiant de connexion
 * @param string $unMois mois demand� (MMAAAA)
 * @param string $unIdVisiteur id visiteur  
 * @return array tableau associatif de la fiche de frais
 */
function obtenirDetailFicheFrais($idCnx, $unMois, $unIdVisiteur) {
    $unMois = filtrerChainePourBD($unMois,$idCnx);
    $ligne = false;
    $requete="select IFNULL(nbJustificatifs,0) as nbJustificatifs, Etat.id as idEtat, libelle as libelleEtat, dateModif, montantValide 
    from FicheFrais inner join Etat on idEtat = Etat.id 
    where idVisiteur='" . $unIdVisiteur . "' and mois='" . $unMois . "'";
    $idJeuRes = mysqli_query($idCnx,$requete);  
    if ( $idJeuRes ) {
        $ligne = mysqli_fetch_assoc($idJeuRes);
    }        
    mysqli_free_result($idJeuRes);
    
    return $ligne ;
}
              
/** 
 * V�rifie si une fiche de frais existe ou non. 
 * Retourne true si la fiche de frais du mois de $unMois (MMAAAA) du visiteur 
 * $idVisiteur existe, false sinon. 
 * @param resource $idCnx identifiant de connexion
 * @param string $unMois mois demand� (MMAAAA)
 * @param string $unIdVisiteur id visiteur  
 * @return bool�en existence ou non de la fiche de frais
 */
function existeFicheFrais($idCnx, $unMois, $unIdVisiteur) {
    $unMois = filtrerChainePourBD($unMois,$idCnx);
    $requete = "select idVisiteur from fichefrais where idVisiteur='" . $unIdVisiteur ."' and mois='" . $unMois . "'";
    $idJeuRes = mysqli_query($idCnx,$requete);  
    $ligne = false ;
    if ( $idJeuRes ) {
        $ligne = mysqli_fetch_assoc($idJeuRes);
        mysqli_free_result($idJeuRes);
    }        
    
    // si $ligne est un tableau, la fiche de frais existe, sinon elle n'exsite pas
    return is_array($ligne) ;
}

/** 
 * Fournit le mois de la derni�re fiche de frais d'un visiteur.
 * Retourne le mois de la derni�re fiche de frais du visiteur d'id $unIdVisiteur.
 * @param resource $idCnx identifiant de connexion
 * @param string $unIdVisiteur id visiteur  
 * @return string dernier mois sous la forme AAAAMM
 */
function obtenirDernierMoisSaisi($idCnx, $unIdVisiteur) {
	$requete = "select max(mois) as dernierMois from FicheFrais where idVisiteur='" .
            $unIdVisiteur . "'";
	$idJeuRes = mysqli_query($idCnx,$requete);
    $dernierMois = false ;
    if ( $idJeuRes ) {
        $ligne = mysqli_fetch_assoc($idJeuRes);
        $dernierMois = $ligne["dernierMois"];
        mysqli_free_result($idJeuRes);
    }        
	return $dernierMois;
}

/** 
 * Ajoute une nouvelle fiche de frais et les �l�ments forfaitis�s associ�s, 
 * Ajoute la fiche de frais du mois de $unMois (MMAAAA) du visiteur 
 * $idVisiteur, avec les �l�ments forfaitis�s associ�s dont la quantit� initiale
 * est affect�e � 0. Cl�t �ventuellement la fiche de frais pr�c�dente du visiteur. 
 * @param resource $idCnx identifiant de connexion'r
 * @param string $unMois mois demand� (MMAAAA)
 * @param string $unIdVisiteur id visiteur  
 * @return void
 */
function ajouterFicheFrais($idCnx, $unMois, $unIdVisiteur) {
    $unMois = filtrerChainePourBD($unMois,$idCnx);
    // modification de la derni�re fiche de frais du visiteur
    $dernierMois = obtenirDernierMoisSaisi($idCnx, $unIdVisiteur);
	$laDerniereFiche = obtenirDetailFicheFrais($idCnx, $dernierMois, $unIdVisiteur);
	if ( is_array($laDerniereFiche) && $laDerniereFiche['idEtat']=='CR'){
		modifierEtatFicheFrais($idCnx, $dernierMois, $unIdVisiteur, 'CL');
	}
    
    // ajout de la fiche de frais � l'�tat Cr��
    $requete = "insert into FicheFrais (idVisiteur, mois, nbJustificatifs, montantValide, idEtat, dateModif) values ('" 
              . $unIdVisiteur 
              . "','" . $unMois . "',0,NULL, 'CR', '" . date("Y-m-d") . "')";
    mysqli_query($idCnx,$requete);
    
    // ajout des �l�ments forfaitis�s
    $requete = "select id from FraisForfait";
    $idJeuRes = mysqli_query($idCnx,$requete);
    if ( $idJeuRes ) {
        $ligne = mysqli_fetch_assoc($idJeuRes);
        while ( is_array($ligne) ) {
            $idFraisForfait = $ligne["id"];
            // insertion d'une ligne frais forfait dans la base
            $requete = "insert into LigneFraisForfait (idVisiteur, mois, idFraisForfait, quantite)
                        values ('" . $unIdVisiteur . "','" . $unMois . "','" . $idFraisForfait . "',0)";
            mysqli_query($idCnx,$requete);
            // passage au frais forfait suivant
            $ligne = mysqli_fetch_assoc ($idJeuRes);
        }
        mysqli_free_result($idJeuRes);       
    }        
}

/** $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
 * Retourne la liste des tous les visiteurs
 * @param aucun
 * @return liste de visiteurs
*/
function obtenirVisiteur(){
    $req = "select * from visiteur";
    return $req;
}

/** $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
 * Retourne les fiches de frais disponibles des mois précédents pour le visiteur sélectionné
 * @param id visiteur
 * @return liste des fiches de frais
 */
function obtenirFichesPourVisiteur($idVisiteur, $idCnx){
    $currentDate = new DateTime('now');
    $moisCourant = $currentDate->format('Y'). '' . $currentDate->format('m');
    $req = "select f.*, ff.*, hf.*  from fichefrais f, lignefraisforfait ff, lignefraishorsforfait hf where f.idVisiteur = '". $idVisiteur ."' and f.mois < ". $moisCourant . " GROUP BY f.idVisiteur";
    $lstFiche = mysqli_query($idCnx,$req);
    $ligne = false;

    if ( $lstFiche ) {
        $ligne = [];
        while($row = mysqli_fetch_assoc($lstFiche)) {
            array_push($ligne, $row);
        }
    }
    return $ligne;
}

/** $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
 * retourne l'état de la fiche de frais
 * @param idEtat
 * @return toutes les informations de l'état
 */
function getEtat($idEtat, $idCnx){
    $req = "select * from etat where id = '". $idEtat . "'";
    $etat = mysqli_query($idCnx,$req);
    $etatL = false;
    if ( $etat ) {
        $etatL = mysqli_fetch_assoc($etat);
        mysqli_free_result($etat);
    }
    return $etatL;
}
/** $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
 * retourne les informations du forfait
 * @param idEtat
 * @return toutes les informations de l'état
 */
function getFraisForfait($idFrais, $idCnx){
    $req = "select * from fraisforfait where id = '". $idFrais . "'";
    $etat = mysqli_query($idCnx,$req);
    $etatL = false;
    if ( $etat ) {
        $etatL = mysqli_fetch_assoc($etat);
        mysqli_free_result($etat);
    }
    return $etatL;
}
/** $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
 * retourne les informations de fiches de frais
 * @param  idvisiteur et mois concerné
 * @return toutes les informations de la fiche de frais forfaitisés
 */
function getFicheFraisForfaitise($idVisiteur, $mois, $idCnx){
    $req = "select l.*, f.* from lignefraisforfait l, fraisforfait f where idVisiteur ='". $idVisiteur ."' and mois = '". $mois . "' and l.idFraisForfait = f.id";
    $fiche = mysqli_query($idCnx, $req);
    $ligne = false;
    if ( $fiche ) {
        $ligne = [];
        while($row = mysqli_fetch_assoc($fiche)) {
            array_push($ligne, $row);
        }
    }
    return $ligne;
}
/** $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
 * modifie la fiche de frais
 * @param idvisiteur , mois concerné et informations de la fiche
 * @return true si la modification est un succès
 */
function editFicheFraisForfaitise($idVisiteur, $mois, $idFraisForfait, $quantite, $idCnx){
    $req = "update lignefraisforfait set quantite = " . $quantite . " where idVisiteur = '". $idVisiteur . "' and mois = " . $mois . " and idFraisForfait = '" .$idFraisForfait . "'";
    $result = mysqli_query($idCnx, $req);
    return $result;
}
/** $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
 * envoie les informations hors forfait avant leur portage sur le mois suivant
 * @param idvisiteur , mois
 * @return les informations hors forfait
 */
function getHorsForfait($idVisiteur, $mois, $idCnx){
    $req = "select * from lignefraishorsforfait where idVisiteur = '". $idVisiteur . "' and mois = " . $mois;
    $fiche = mysqli_query($idCnx, $req);
    if ( $fiche ) {
        $ligne = [];
        while($row = mysqli_fetch_assoc($fiche)) {
            array_push($ligne, $row);
        }
    }
    return $ligne;
}

/** $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
 * modifie la ligne hors forfait et appelle l'ajout d'une nouvelle ligne
 * @param toutes les infos du forfait nécessaires
 * @return true si l'opération a été effectué
 */
function deleteHorsForfait($idVisiteur, $mois, $montant, $date, $libelle, $idCnx){
    $newlibelle = "REFUSE : " . $libelle;
    $req = "update lignefraishorsforfait set montant = 0, date = '". date("Y-m-d", strtotime($date)) . "' ,libelle = '". $newlibelle . "' where idVisiteur = '". $idVisiteur . "' and mois = '" . $mois . "'";
    $result = mysqli_query($idCnx, $req);
    $resultC = createNewHorsForfait($idVisiteur, $mois, $date, $montant, $libelle, $idCnx);
    if ($result && $resultC)
    {
        $r = true;
    }else{
        $r = false;
    }
    return $r;
}
/** $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
 * crée une ligne de frais forfait et de horsforfait du mois suivant l'appel à la fonction
 * @param toutes les infos de fiches récupérés
 * @return true si l'opération a été effectué
 */
function createNewHorsForfait($idVisiteur, $mois, $date, $montant, $libelle, $idCnx){
    $currentDate = date('Y-m-d');
    //converti $date en champ date puis ajoute 1 mois
    $d = new DateTime($date);
    $new = date_add($d, date_interval_create_from_date_string('1 month'));
    $moisCourant = $new->format('Y'). '' . $new->format('m');
    $req = "insert into fichefrais (idVisiteur, mois, nbJustificatifs, montantValide, dateModif, idEtat) VALUES ( '" . $idVisiteur . "','".$moisCourant . "', 0 , 0,'" .$currentDate. "', 'CR' )";

    $result = mysqli_query($idCnx, $req);

    $req2 = "insert into lignefraishorsforfait (idVisiteur, mois, libelle, date, montant) VALUES ('". $idVisiteur . "','". $moisCourant . "','" . $libelle . "','" . $date . "',"  .$montant .")";

    $result2 = mysqli_query($idCnx, $req2);

    if ($result && $result2)
    {
        $r = true;
    }else{
        $r = false;
    }
    return $r;

}
/** $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
 * récupère toutes les fiches de frais validées et mises en paiement
 * @param informations de connexion
 * @return liste de fiches de frais
 */
function getAllFichesVA($idCnx){
    $req = "select * from fichefrais f, visiteur v where f.idEtat = 'VA' and f.idVisiteur = v.id";
    $fiche = mysqli_query($idCnx, $req);
    $ligne = false;
    if ( $fiche ) {
        $ligne = [];
        while($row = mysqli_fetch_assoc($fiche)) {
            array_push($ligne, $row);
        }
    }
    return $ligne;
}
/** $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
 * récupère toutes les informations de la fiche forfaitisés ou pas
 * @param informations de connexion et idvisiteur et mois de la fiche
 * @return fiche
 */
function getAllInfosFiche($idCnx, $idVisiteur, $mois){
    $req = "select * from fichefrais f, visiteur v, lignefraishorsforfait hf, lignefraisforfait ff 
where f.idVisiteur = v.id 
and f.idVisiteur = hf.idVisiteur 
and f.idVisiteur = ff.idVisiteur 
and f.idVisiteur = '" . $idVisiteur . "' 
and f.mois = '" . $mois . "'";
    $fiche = mysqli_query($idCnx, $req);
    $f = false;
    if ( $fiche ) {
        $f = mysqli_fetch_assoc($fiche);
        mysqli_free_result($fiche);
    }
    return $f;
}
/** $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
 * modifie l'état de la fiche de frais
 * @param informations de connexion et idvisiteur, mois et nouvel état de la fiche
 * @return fiche
 */
function editEtatFicheFrais($idCnx, $idVisiteur, $mois, $etat){
    $req = "update fichefrais set idEtat = '". $etat . "' where idVisiteur = '". $idVisiteur . "' and mois = '" . $mois . "'  ";
    $result = mysqli_query($idCnx, $req);
    if ($result)
    {
        $r = true;
    }else{
        $r = false;
    }
    return $r;
}

/**
 * Retourne le texte de la requ�te select concernant les mois pour lesquels un 
 * visiteur a une fiche de frais. 
 * 
 * La requ�te de s�lection fournie permettra d'obtenir les mois (AAAAMM) pour 
 * lesquels le visiteur $unIdVisiteur a une fiche de frais. 
 * @param string $unIdVisiteur id visiteur  
 * @return string texte de la requ�te select
 */                                                 
function obtenirReqMoisFicheFrais($unIdVisiteur) {
    $req = "select fichefrais.mois as mois from  fichefrais where fichefrais.idvisiteur ='"
            . $unIdVisiteur . "' order by fichefrais.mois desc ";
    return $req ;
}  
                  
/**
 * Retourne le texte de la requ�te select concernant les �l�ments forfaitis�s 
 * d'un visiteur pour un mois donn�s. 
 * 
 * La requ�te de s�lection fournie permettra d'obtenir l'id, le libell� et la
 * quantit� des �l�ments forfaitis�s de la fiche de frais du visiteur
 * d'id $idVisiteur pour le mois $mois    
 * @param string $unMois mois demand� (MMAAAA)
 * @param string $unIdVisiteur id visiteur  
 * @return string texte de la requ�te select
 */                                                 
function obtenirReqEltsForfaitFicheFrais($unMois, $unIdVisiteur,$idCnx) {
    $unMois = filtrerChainePourBD($unMois,$idCnx);
    $requete = "select idFraisForfait, libelle, quantite from LigneFraisForfait
              inner join FraisForfait on FraisForfait.id = LigneFraisForfait.idFraisForfait
              where idVisiteur='" . $unIdVisiteur . "' and mois='" . $unMois . "'";
    return $requete;
}

/**
 * Retourne le texte de la requ�te select concernant les �l�ments hors forfait 
 * d'un visiteur pour un mois donn�s. 
 * 
 * La requ�te de s�lection fournie permettra d'obtenir l'id, la date, le libell� 
 * et le montant des �l�ments hors forfait de la fiche de frais du visiteur
 * d'id $idVisiteur pour le mois $mois    
 * @param string $unMois mois demand� (MMAAAA)
 * @param string $unIdVisiteur id visiteur  
 * @return string texte de la requ�te select
 */                                                 
function obtenirReqEltsHorsForfaitFicheFrais($unMois, $unIdVisiteur,$idCnx) {
    $unMois = filtrerChainePourBD($unMois,$idCnx);
    $requete = "select id, date, libelle, montant from LigneFraisHorsForfait
              where idVisiteur='" . $unIdVisiteur 
              . "' and mois='" . $unMois . "'"
              . " and id=(SELECT max(id) FROM LigneFraisHorsForfait)";
              //var_dump($requete);
    return $requete;
}

/**
 * Supprime une ligne hors forfait.
 * Supprime dans la BD la ligne hors forfait d'id $unIdLigneHF
 * @param resource $idCnx identifiant de connexion
 * @param string $idLigneHF id de la ligne hors forfait
 * @return void
 */
function supprimerLigneHF($idCnx, $unIdLigneHF) {
    $requete = "delete from LigneFraisHorsForfait where id = " . $unIdLigneHF;
    mysqli_query($idCnx, $requete);
}

/**
 * Ajoute une nouvelle ligne hors forfait.
 * Ins�re dans la BD la ligne hors forfait de libell� $unLibelleHF du montant 
 * $unMontantHF ayant eu lieu � la date $uneDateHF pour la fiche de frais du mois
 * $unMois du visiteur d'id $unIdVisiteur
 * @param resource $idCnx identifiant de connexion
 * @param string $unMois mois demand� (AAMMMM)
 * @param string $unIdVisiteur id du visiteur
 * @param string $uneDateHF date du frais hors forfait
 * @param string $unLibelleHF libell� du frais hors forfait 
 * @param double $unMontantHF montant du frais hors forfait
 * @return void
 */
function ajouterLigneHF($idCnx, $unMois, $unIdVisiteur, $uneDateHF, $unLibelleHF, $unMontantHF) {
    $unLibelleHF = filtrerChainePourBD($unLibelleHF,$idCnx);
    $uneDateHF = filtrerChainePourBD(convertirDateFrancaisVersAnglais($uneDateHF),$idCnx);
    $unMois = filtrerChainePourBD($unMois,$idCnx);
    $requete = "insert into LigneFraisHorsForfait(idVisiteur, mois, date, libelle, montant) 
                values ('" . $unIdVisiteur . "','" . $unMois . "','" . $uneDateHF . "','" . $unLibelleHF . "'," . $unMontantHF .")";
    mysqli_query($idCnx,$requete);
}

/**
 * Modifie les quantit�s des �l�ments forfaitis�s d'une fiche de frais. 
 * Met � jour les �l�ments forfaitis�s contenus  
 * dans $desEltsForfaits pour le visiteur $unIdVisiteur et
 * le mois $unMois dans la table LigneFraisForfait, apr�s avoir filtr� 
 * (annul� l'effet de certains caract�res consid�r�s comme sp�ciaux par 
 *  mysqli) chaque donn�e   
 * @param resource $idCnx identifiant de connexion
 * @param string $unMois mois demand� (MMAAAA) 
 * @param string $unIdVisiteur  id visiteur
 * @param array $desEltsForfait tableau des quantit�s des �l�ments hors forfait
 * avec pour cl�s les identifiants des frais forfaitis�s 
 * @return void  
 */
function modifierEltsForfait($idCnx, $unMois, $unIdVisiteur, $desEltsForfait) {
    $unMois=filtrerChainePourBD($unMois,$idCnx);
    $unIdVisiteur=filtrerChainePourBD($unIdVisiteur,$idCnx);
    foreach ($desEltsForfait as $idFraisForfait => $quantite) {
        $requete = "update LigneFraisForfait set quantite = " . $quantite 
                    . " where idVisiteur = '" . $unIdVisiteur . "' and mois = '"
                    . $unMois . "' and idFraisForfait='" . $idFraisForfait . "'";
      mysqli_query($idCnx,$requete);
    }
}

/**
 * Contr�le les informations de connexionn d'un utilisateur.
 * V�rifie si les informations de connexion $unLogin, $unMdp sont ou non valides.
 * Retourne les informations de l'utilisateur sous forme de tableau associatif 
 * dont les cl�s sont les noms des colonnes (id, nom, prenom, login, mdp)
 * si login et mot de passe existent, le bool�en false sinon. 
 * @param resource $idCnx identifiant de connexion
 * @param string $unLogin login 
 * @param string $unMdp mot de passe 
 * @return array tableau associatif avec le type de session ou tableau à deux case contenant vide et false
 */
function verifierInfosConnexion($idCnx, $unLogin, $unMdp) {
    $unLogin = filtrerChainePourBD($unLogin,$idCnx);
    $unMdp = filtrerChainePourBD($unMdp,$idCnx);
    // le mot de passe est crypt� dans la base avec la fonction de hachage md5
    $req = "select id, nom, prenom, login, mdp from visiteur where login='".$unLogin."' and mdp='" . $unMdp . "'";
    $idJeuRes = mysqli_query($idCnx,$req);
    $ligne = false;
    $data = [];
    if ( mysqli_num_rows($idJeuRes) > 0 ) {
        $ligne = mysqli_fetch_assoc($idJeuRes);
        mysqli_free_result($idJeuRes);
        $data = [$ligne, 'visiteur'];
    }
    $reqC = "select id, nom, prenom, login, mdp from comptable where login='".$unLogin."' and mdp='" . $unMdp . "'";
    $idJeuResC = mysqli_query($idCnx,$reqC);
    if( mysqli_num_rows($idJeuResC) ){
        $ligne = mysqli_fetch_assoc($idJeuResC);
        mysqli_free_result($idJeuResC);
        $data = [$ligne, 'comptable'];

    }

    //print_r($data);exit();

    return $data;
}

/**
 * Modifie l'�tat et la date de modification d'une fiche de frais
 
 * Met � jour l'�tat de la fiche de frais du visiteur $unIdVisiteur pour
 * le mois $unMois � la nouvelle valeur $unEtat et passe la date de modif � 
 * la date d'aujourd'hui
 * @param resource $idCnx identifiant de connexion
 * @param string $unIdVisiteur 
 * @param string $unMois mois sous la forme aaaamm
 * @return void 
 */
function modifierEtatFicheFrais($idCnx, $unMois, $unIdVisiteur, $unEtat) {
    $requete = "update FicheFrais set idEtat = '" . $unEtat . 
               "', dateModif = now() where idVisiteur ='" .
               $unIdVisiteur . "' and mois = '". $unMois . "'";
    mysqli_query($idCnx,$requete);
}             
?>