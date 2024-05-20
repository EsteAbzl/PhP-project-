<?php
function rechercherProfils(){
    //Note: Cherche les profils du serveur
    
    $resultats = array();
    
    $dossierProfils = 'data/profils/';
    
    $dossier = opendir($dossierProfils);
    
    while (($fichier = readdir($dossier)) !== false){
        if($fichier != '.' && $fichier != '..'){
        
            // récupérer le pseudo de l'utilisateur (nom du fichier sans extension)
            $pseudo = pathinfo($fichier, PATHINFO_FILENAME);
        
            // chemin vers la photo de profil
            $cheminPhoto = $dossierProfils . $pseudo . '/pfp.png';

            $cheminBio = $dossierProfils . $pseudo . '/bio.csv';
            $file = fopen($cheminBio, "r");
            $bio = fread($file,3001);
            fclose($file);
        
            // vérifier si le pseudo correspond à la recherche
            if($pseudo != $_SESSION['pseudo']){
                $resultats[] = array(
                    'pseudo' => $pseudo,
                    'lien_pfp' => $cheminPhoto,
                    'bio' => $bio
                );
            }
        }
    }

    closedir($dossier);

    return $resultats;
}

$resultats = rechercherProfils();

// modif hauteur de la liste en fonct° de nbr de résultats
$nb_profils = count($resultats); 

?>
<!DOCTYPE html>
<head>
    <style>
        div.swipeBox{
            position: relative;
            left: 10vw;
            width: 30vw;
            height: 45vw;
            background-color: burlywood;

            text-align: center;
            font-size: 2vw;
        }

        button.swipe{
            width: 8vw;
            height: 5vw;
            margin-right: 3vw;
            margin-left: 3vw;
            padding: 0;

            border: 0.1vw solid;

            text-align: center;
            font-size: 2vw;
            cursor: pointer;
        }

        
        div.swipeProfil{
            position: relative;
            left: 2.5vw;
            top: 2vw;
            width: 25vw;
            height: 33vw;

            background-color: grey;

            font-size: 0;
        }
        
        img.swipeImg{
            position: relative;
            width: 15vw;
            height: 15vw;
            top: 0.5vw;

            border-radius: 100%;
        }

        p.swipeName{
            position: relative;
            bottom: 2.5vw;

            font-family: 'against.projet';
            color: #e9b938;
            font-size: 3vw;
        }

        p.swipeBio{
            position: relative;
            bottom: 5vw;
            left: 0.5vw;
            padding: 0.5vw;

            width: 23vw;
            height: 11vw;
            background-color: darkgray;

            text-align: center;
            font-family: 'quicksand';
            font-size: 1vw;
            color: white;
        }
    </style>
</head>

<body>
    <div class="main">
        <div class="swipeBox">
            <div class="swipeProfil" id="swipeProfil">
                <img class="swipeImg" id="swipeImg" src="<?php echo $resultats[0]['lien_pfp']; ?>">
                <p class="swipeName" id="swipeName"><strong><?php echo $resultats[0]['pseudo']; ?></strong></p>
                <p class="swipeBio" id="swipeBio"><?php echo $resultats[0]['bio']; ?></p>
            </div>
            <p>Likez-vous ce profil?</p>

            <button onclick="reloadProfil(); envoyer_notif();" class="swipe">oui</button>
            <button onclick="reloadProfil();" class="swipe">non</button>

        </div>
    </div>

    <script>

        var resultats = <?php echo json_encode($resultats); ?>;
        var currentIndex = 0;

        function reloadProfil() {
            currentIndex++;


            if (currentIndex >= resultats.length) {
                currentIndex = 0;
            }




            var swipeImg = document.getElementById('swipeImg');
            var swipeName = document.getElementById('swipeName');
            var swipeBio = document.getElementById('swipeBio');

            swipeImg.src = resultats[currentIndex]['lien_pfp'];
            swipeName.innerHTML = '<strong>' + resultats[currentIndex]['pseudo'] + '</strong>';
            swipeBio.innerHTML = resultats[currentIndex]['bio'];
        }

        function envoyer_notif() {
            var pseudo = encodeURIComponent(resultats[currentIndex]['pseudo']);

            var xhr = new XMLHttpRequest();
            var params = 'pseudo=' + pseudo;

            xhr.open('GET', 'scripts/notifs/add_notifs_6.php?' + params, true);


            xhr.send();
            xhr.onreadystatechange = function() {

                if (xhr.readyState == XMLHttpRequest.DONE) {

                    if (xhr.status == 200) {

                        console.log(xhr.responseText);
                    } else {

                        console.error('La requête a échoué avec le statut ' + xhr.status);
                    }

                }
            }; 


        }
    </script>
</body>