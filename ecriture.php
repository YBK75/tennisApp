<?php  // On démarre la session AVANT d'écrire du code HTML
session_start();
?>

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

    if (isset($_POST['reserv'])) //quand on reçoit les informations
            { //écriture de la réservation dans la base de données
                $ecreserv = $bdd->prepare("INSERT INTO reservation (id_res, jour, heure, terrain, nom, prenom, id_etu) VALUES (NULL, ?, ?, ?, ?, ?, ? )");
                $ecreserv->execute(array($_POST['jour'], $_POST['heure'], $_POST['terrain'], $_SESSION['nom'], $_SESSION['prenom'], $_SESSION['id']));
                header('Location: accueil.php?valid=ok'); // Redirection du visiteur vers la page d'accueil
            }

    if (isset($_POST['suppr'])) //quand on demande de supprimer la réservation
            { //on fait la requête pour supprimer la ligne dans la base de données
                $suppreserv = $bdd->prepare("DELETE FROM reservation WHERE jour = ? AND heure =  ? AND terrain = ?");
                $suppreserv->execute(array($_POST['jour'], $_POST['heure'], $_POST['terrain']));
                header('Location: accueil.php?valid=suppr'); // Redirection du visiteur vers la page d'accueil
            }

    if (isset($_POST['inscrip'])) //quand on reçoit les informations d'inscription
            { //inscription de l'étudiant dans la base de données
                $inscript = $bdd->prepare("INSERT INTO etudiants (id_etu, nom, prenom, date_cotis, admin) VALUES (NULL, ?, ?, ?, ? )");
                $inscript->execute(array($_POST['nom'], $_POST['prenom'], $_POST['datefin'], 0));
                header('Location: connexion.php?inscript=ok'); // Redirection du visiteur vers la page de connexion
            }
    
    if (isset($_POST['modifterr'])) //quand on reçoit les paramètres des terrains
            { //on modifie la base de données des terrains
                $modifter = $bdd->prepare("UPDATE terrains SET heure_ouv = ?, heure_fer = ?, dispo = ? WHERE idter = ?");
                $modifter->execute(array($_POST['heureouver'], $_POST['heureferme'], $_POST['estdispo'], $_POST['terrain']));
                header('Location: admin.php'); // Redirection du visiteur vers la page de connexion
            }


?>