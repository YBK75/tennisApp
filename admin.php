<?php  // On démarre la session AVANT d'écrire du code HTML
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Réservation Tennis</title>
</head>
<body>
    
<!-- CONNEXION A LA BASE DE DONNEES -->
    <?php
    try
    {
    // On se connecte à MySQL
        $bdd = new PDO('mysql:host=localhost;dbname=projetinfo;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

    }
    catch (Exception $e)
    {
    // En cas d'erreur, on affiche un message et on arrête tout
        die('Erreur : ' . $e->getMessage());
    }
    ?>


<h1>Réservation des Terrains de Tennis</h1>
<br>

<p>Bonjour <?php echo $_SESSION['prenom'] . " " . $_SESSION['nom']; ?>. Vous êtes administrateur.</p>
<form action="admin.php" method="post">
    <input type="submit" name="deco" value="Déconnexion"> <!-- bouton déconnexion -->
</form>
<form action="admin.php" method="post">
    <input type="submit" name="retour" value="Vers l'accueil"> <!-- bouton retour accueil -->
</form>
<p>Aujourd'hui nous sommes le <?php echo date('d/m/Y'); ?>. </p> <!-- date du jour -->


<!-- Si on clique sur le bouton de déconnexion -->
    <?php  
    if (isset($_POST['deco'])) { 

        header('Location: connexion.php?connect=deco'); //On renvoie vers la page de connexion
    } ?>

<!-- Si on clique sur le bouton de retour vers l'accueil -->
    <?php
    if (isset($_POST['retour'])) { //

        header('Location: accueil.php'); //On redirige vers l'accueil
    }   ?>



<h2>Horaires et disponibilités des terrains</h2>
    <!-- Affichage des paramètres de disponibilité actuels -->
        <?php
        $reqhor = $bdd->query("SELECT * FROM terrains"); //requête sur la base des terrains
        while ($datahor = $reqhor->fetch()) { //ligne par ligne
            if ($datahor['dispo'] == 1) {
                $dispo = 'Oui';
            }
            else {
                $dispo = 'Non';
            }
            echo "Terrain " . $datahor['idter'] . " : de " . $datahor['heure_ouv'] . " à " . $datahor['heure_fer'] . ". Dispo : " . $dispo . '<br>'; //on affiche
        }
        $reqhor->closeCursor();
        ?>

    <!-- Formulaire de modification des paramètres -->
        <form action="ecriture.php" method="post"> <!-- On envoie ces informations vers ecriture.php -->
            <p>
                <select name="terrain">
                    <option value="1">Terrain 1</option>
                    <option value="2">Terrain 2</option>
                    <option value="3">Terrain 3</option>
                </select>
                <label for="heureouver">Heure d'ouverture</label> : <input type="time" name="heureouver" id="heureouver" />
                <label for="heureouver">Heure de fermeture</label> : <input type="time" name="heureferme" id="heureferme" />
                <input type="radio" name="estdispo" value="1" id="oui" checked="checked" /> <label for="oui">Oui</label>
                <input type="radio" name="estdispo" value="0" id="non" /> <label for="non">Non</label>
                <input type="submit" name="modifterr" value="Modifier les paramètres" />
            </p>
        </form>


<h2>Nombre maximum de réservations par semaine</h2>
    <!-- Affichage des paramètres de réservation actuels -->
        <?php
        $reqnbmax = $bdd->query("SELECT * FROM parametres");
        $datanbmax = $reqnbmax->fetch();
        echo "Actuellement : " . $datanbmax['nbresmax'];
        $reqnbmax->closeCursor();
        ?>
    <!-- Modification des paramètres de réservation -->
        <form action="ecriture.php" method="post">
            <p>
                <label for="nbmax">Nouveau maximum</label> : <input type="number" min="0" name="nbmax" id="nbmax" /> <!-- Le formulaire doit envoyer un nombre positif -->
                <input type="submit" name="modifnbmax" value="Modifier" />
            </p>
        </form>

<h2>Date de fin de cotisation des étudiants</h2>
    
    <!-- Sélection de l'étudiant -->
        <form action="admin.php" method="post"> 
            <p>
                <label for="prenom">Prénom</label> : <input type="text" name="prenom" id="prenom" />
                <label for="nom">Nom</label> : <input type="text" name="nom" id="nom"  />
                <input type="submit" name="modifdatecotis" value="OK" />
                
            </p>
        </form>
        
    <?php
    if (isset($_POST['modifdatecotis'])) { //Si le formulaire est rempli
        //On vérifie que l'étudiant est bien dans la base de données :
        $reqinscr = $bdd->prepare("SELECT COUNT(*) AS nblignes FROM etudiants WHERE nom = ? AND prenom =  ?");
        $reqinscr->execute(array($_POST['nom'],$_POST['prenom']));        
        while ($datainscr = $reqinscr->fetch()) {
            if ($datainscr['nblignes'] > 0) { // si il y a déja des entrées dans la base de données i.e étudiant bien inscrit
                //Requête pour récupérer la date dans la base des étudiants
                $datetu = $bdd->prepare("SELECT nom, prenom, id_etu, date_cotis FROM etudiants WHERE nom = ? AND prenom = ?");
                $datetu->execute(array($_POST['nom'], $_POST['prenom']));
                $datadatetu = $datetu->fetch();
                echo $datadatetu['prenom'] . " " . $datadatetu['nom'] . ". Fin le : " . $datadatetu['date_cotis']; //affichage des résultats
                
                ?>
                <form action="ecriture.php" method="post"> <!-- Formulaire pour changer la date de cotisation, on envoie à ecriture.php -->
                    <p>
                        <input type="hidden" name="idetu" value=<?php echo $datadatetu['id_etu']; ?> />
                        <label for="newdatecotis">Changer cette date</label> : <input type="date" name="newdatecotis" id="newdatecotis" />
                        <input type="submit" name="new_date_cotis" value="Modifier" />
                    </p>
                </form>
                <?php
                $datetu->closeCursor();

            $reqinscr->closeCursor(); // Termine le traitement de la requête 
            }
            else{
                echo "Erreur : l'étudiant n'est pas inscrit dans la base des étudiants";
                }
        }
    } ?>

    <?php
    if (isset($_GET['modif']) AND $_GET['modif'] == 'ok') { //Si on reçoit que la modification a bien été faite

        echo "Modifications effectuées avec succès !"; //Message de confirmation
    }  
    ?>  
 
</body>
</html>