<?php  // On démarre la session AVANT d'écrire du code HTML
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Réservation Tennis</title>
    <style>
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    td, th {
        border: 1px solid #272822;
        text-align: left;
        padding: 8px;
    }

    tr:nth-child(even) {
        background-color: #dddddd;
    }
</style>
</head>
<body>

<!-- Connexion à la base de données -->
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

<!-- Quand on vient de se connecter, message de confirmation -->
    <?php  
    if (isset($_GET['connect']) AND $_GET['connect'] == 'ok') { 
        ?> 
        <p><em>Connexion réussie !</em></p> 
    <?php } ?>

<!-- Affichage du nom et de la date de cotisation -->
    <p>Bonjour <?php echo $_SESSION['prenom'] . " " . $_SESSION['nom']; ?> ! Vous avez cotisé jusqu'au <?php echo date_format(date_create($_SESSION['date_cotisation']), 'd/m/Y'); ?>. </p>

<!-- Bouton de déconnexion -->
    <form action="accueil.php" method="post"> 
        <input type="submit" name="deco" value="Déconnexion">
    </form>
    <?php
    if (isset($_POST['deco'])) { //Quand on clique sur le bouton de déconnexion

        header('Location: connexion.php?connect=deco'); //On va vers la page de connexion
    }  ?>

<!-- Pour afficher un bouton vers la page administrateur si l'étudiant l'est -->
    <?php 
    if (isset($_SESSION['admin']) AND $_SESSION['admin'] == 'oui') { ?>
    <form action="accueil.php" method="post">
        <input type="submit" name="versadmin" value="Page administrateur">
    </form>
    <?php
    }
    ?>
    <?php
    if (isset($_POST['versadmin'])) { //Quand on clique sur le bouton pour aller sur la page d'administration

        header('Location: admin.php'); //on y va
    }  ?>


<p>Aujourd'hui nous sommes le <?php echo date('d/m/Y'); ?>.</p> <!-- date du jour -->
<br>
<p>Choisissez un jour :</p>

<!-- Sélection du jour -->
    <form action="accueil.php" method="post"> 
        <select name="journee">
                    <?php //sélection du jour dans une liste
                    $datedujour = date_create(date('m/d/Y')); //on prend la date du jour
                        for ($i=0; $i < 8; $i++) { //on boucle sur la semaine
                            $datejourformated = date_format($datedujour, 'd/m/Y'); ?>
                            <option value=" <?php echo $i ; ?> "> <?php echo $datejourformated ; ?> </option> <!-- on affiche -->
                            <?php date_add($datedujour, date_interval_create_from_date_string('1 day')); //on augmente la date d'un jour
                        }
                        ?>
        </select>
        <br><br>
        <input type="submit" value="Valider">
    </form>

<!-- Affichage du message de confirmation de validation -->
    <?php 
    if (isset($_GET['valid']) AND $_GET['valid'] == 'ok') 
    {
        echo "Réservation confirmée !";
    }
    ?>

<!-- Affichage du message de confirmation de suppression -->
    <?php 
    if (isset($_GET['valid']) AND $_GET['valid'] == 'suppr') 
    {
        echo "Réservation supprimée !";
    }
    ?>

<!-- Vérification du nombre de réservations dans la semaine -->
    <?php
    $date1 = date_create(date('Y-m-d')); //date du jour
    $date2 = date_create(date('Y-m-d'));
    for ($k=0; $k < 7; $k++) { 
        date_add($date2, date_interval_create_from_date_string('1 day'));
    }  //date de fin de la semaine
    $date1 = date_format($date1, 'Y-m-d');
    $date2bis = $date2;
    $date2 = date_format($date2, 'Y-m-d');
    $reqnbre = $bdd->prepare("SELECT id_res, COUNT(*) AS nblignes FROM reservation WHERE id_etu = ? AND jour BETWEEN ? AND ?"); //On récupère le nombre de réservations pour cet utilisateur pour cette semaine
    $reqnbre->execute(array($_SESSION['id'], $date1, $date2));
    while ($datanbre = $reqnbre->fetch()) {
        $reqmax = $bdd->query("SELECT nbresmax FROM parametres WHERE id_param = 1"); //requête pour récupérer le nombre de réservations maximum défini par l'administrateur
        $datamax = $reqmax->fetch();

        if ($datanbre['nblignes'] >= $datamax['nbresmax']) { //Si nombre de réservations >= au nombre max autorisé : message d'avertissement
            $_SESSION['overeserv'] = 'oui';
            echo "<em>Vous avez dépassé le nombre de réservations maximum pour cette semaine ! </em>";
        }
        else{
            $_SESSION['overeserv'] = 'non';
        }
        $reqnbre->closeCursor();
    }
    ?>

<!-- Vérification de la date de cotisation -->
    <?php
    if (date_create($_SESSION['date_cotisation']) < $date2bis) { //Si date de cotisation inférieure à la date de la fin de la semaine
        $_SESSION['cotis'] = 'notok';
        echo "<em>Vous n'êtes plus à jour de la cotisation.</em>";
    }
    else{
        $_SESSION['cotis'] = 'ok';
    }
    ?>

<!-- Récupération des horaires des terrains -->
    <?php //requête sur la base des terrains
    $reqhor = $bdd->query("SELECT min(heure_ouv) AS premheure, max(heure_fer) AS derheure FROM terrains WHERE dispo = 1 ");
    $datahor = $reqhor->fetch();

    $heureouverture = $datahor['premheure']; //On prend le minimum des heures d'ouverture
    $heurefermeture = $datahor['derheure']; //Et le maximum des heures de fermetures, pour les terrains ouverts

    $reqhor->closeCursor();

    $creneau = date_diff(date_create($heureouverture),date_create($heurefermeture))->format('%H'); //on peut ainsi calculer le nombre de créneaux par jour

    ?>



<!-- Affichage de l'emploi du temps du jour -->

<?php
if (isset($_POST['journee'])) //si le jour est sélectionné
{
    //Boucle pour récupérer la date sélectionnée
    $datedujour = date_create(date('m/d/Y'));
    for ($k=0; $k < $_POST['journee']; $k++) { 
        date_add($datedujour, date_interval_create_from_date_string('1 day'));
    }
    $datejourformated = date_format($datedujour, 'd/m/Y');
    ?>
            
    <h2>Planning du <?php echo "" . $datejourformated . ""; ?> </h2> <!-- On affiche le jour choisi-->


    <table width="100%"> <!-- On créer le tableau -->

    <!-- 1ère ligne : les 3 terrains -->
        <tr>
            <th width="(100/3)%">Terrain 1</th>
            <th width="(100/3)%">Terrain 2</th>
            <th width="(100/3)%">Terrain 3</th>
        </tr>

    <!-- Autres lignes : les heures -->                    
        <?php
        $heure = date_create($heureouverture); 

        for ($j=0; $j < $creneau; $j++) { //on boucle sur les lignes, nombre de créneaux est défini plus haut
            $datedujour = date_create(date('m/d/Y'));

            for ($k=0; $k < $_POST['journee']; $k++) { //pour savoir la date sélectionnée
                date_add($datedujour, date_interval_create_from_date_string('1 day'));
            }
            $datejourformated2 = date_format($datedujour, 'Y-m-d');

            $heureformated = date_format($heure, 'H:i');

            echo "<tr>" ; //Dans la ligne
            for ($i=1; $i < 4; $i++) { //on boucle sur les colonnes
            ?>
                <td width="(100/3)%"> <!-- Dans la cellule -->
                <?php //Dans chaque case du tableau :
                echo $heureformated ; //on affiche l'heure
                
                // On récupère tout le contenu de la table reservation
                $reqreserv = $bdd->prepare("SELECT id_res, jour, DATE_FORMAT(heure, '%H:%i') AS heure, terrain, nom, prenom, id_etu, COUNT(*) AS nblignes FROM reservation WHERE jour = ? AND heure =  ? AND terrain = ? ");
                $reqreserv->execute(array($datejourformated2, $heureformated, $i)); // pour ce jour, cette heure, ce terrain
                                
                while ($datareserv = $reqreserv->fetch()) // On affecte la réponse dans un tableau
                { //on affiche les résultats

                    $reqterr = $bdd->query("SELECT dispo, heure_ouv,heure_fer FROM terrains WHERE idter = $i ");
                    $dataterr = $reqterr->fetch();

                    //Si le terrain est ouvert (disponible et dans les heures d'ouvertures)
                    if ($dataterr['dispo'] == 1 AND $heure >= date_create($dataterr['heure_ouv']) AND $heure < date_create($dataterr['heure_fer'])) {

                        $reqterr->closeCursor();
                        if ($datareserv['nblignes'] > 0) { // si il y a déja des entrées dans la base de données i.e si créneau déja réservé

                            echo " <strong>" . $datareserv['prenom'] . " " . $datareserv['nom'] . "</strong>"; //On affiche qui a réservé à ce moment

                            $reqreserv->closeCursor(); 
                            
                            if ($datareserv['id_etu'] == $_SESSION['id']) { //Si la réservation est faite par l'utilisateur actuel
                                ?>
                                <!-- On affiche la possibilité de supprimer cette réservation -->
                                <form action="ecriture.php" method="post">
                                    <p>
                                        <input type="hidden" name="jour" value=<?php echo $datejourformated2; ?> />
                                        <input type="hidden" name="heure" value=<?php echo $heureformated; ?> />
                                        <input type="hidden" name="terrain" value=<?php echo $i; ?> />
                                        <input type="submit" name="suppr" value="Supprimer" />
                                    </p>
                                </form>
                                <?php
                            }
                        }

                        else {  // sinon, le créneau est libre
                            if ($_SESSION['overeserv'] == 'non' AND $_SESSION['cotis'] == 'ok') { //Si la cotisation est ok ET que le maximum de réservations n'est pas atteint, on peut réserver                         
                                ?>

                                <form action="ecriture.php" method="post"> <!-- formulaire pour rentrer les données, on envoie sur ecriture.php -->
                                    <p>
                                        <input type="hidden" name="jour" value=<?php echo $datejourformated2; ?> />
                                        <input type="hidden" name="heure" value=<?php echo $heureformated; ?> />
                                        <input type="hidden" name="terrain" value=<?php echo $i; ?> />
                                        <input type="submit" name="reserv" value="Réservez" />
                                    </p>
                                </form>

                                <?php 
                            }
                        }
                    }
                    else { //Le terrain est fermé
                        echo "<strong> Terrain fermé</strong>";
                    }
                }
            }

            echo "</td>" ; //Fermeture cellule
            echo "</tr>"; //Fermeture ligne
            date_add($heure, date_interval_create_from_date_string('1 hour')); //on ajoute 1 heure pour la prochaine ligne                     
        }

        ?>
    </table>

    <?php
} ?>

</body>
</html>