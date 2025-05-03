//Méthodes utilisées par le fichier Twig: lancer.html.twig

console.log('This log comes from assets/appLancerPartie.js - welcome to AssetMapper! 🎉');

//Méthode Javascript appelées par les fichiers Twig

//Permet de copier des informations dans le presse papier
function copierPressePapier (nomElaCopier = "adrACopier", nomElConfCopie="TextElCopie", attributACopier="href") {
    console.log ("Appel de la méthode copierPressePapier");

    let elementACopier = document.getElementById(nomElaCopier)[attributACopier];
    console.log("Elément copié dans le presse papier: "+elementACopier);

    navigator.clipboard.writeText(elementACopier);
    let labelCopie= document.getElementById(nomElConfCopie);
    labelCopie.innerHTML="Copié!";

    myVarEffacer = setTimeout(() => {
        labelCopie.innerHTML="";
        
    }, 5000);
    
}

//Appelé par lancer.html.twig
//Recup de l'état de la partie et si elle est tjrs en cours, met à jour la liste des joueurs
//Si elle a été lancée, redirige vers la route pour jouer
function rafraichirElementsPages(codePartieEnCours, codePartieTerminee, codePartieEnCoursDeConnexion, codePartieAbandonnee, codeConnexion) {

    rechercheEtatPartie().then(result => {

        if (result == codePartieAbandonnee){
            document.getElementById("corpsPageLancerPartie").innerHTML = "<span>La partie a été annulée. Pour lancer une autre partie, allez dans le menu général et cliquez sur: </span> <span class='lien_relancer_une_partie'> << Créer/lancer une partie multijoueur >></span>";
        }

        else if (result == codePartieTerminee){
            document.getElementById("corpsPageLancerPartie").innerHTML = "<span>La partie est terminée! Pour lancer une autre partie, allez dans le menu général et cliquez sur: </span> <span class='lien_relancer_une_partie'> << Créer/lancer une partie multijoueur >></span>";
        }

        else if (result == codePartieEnCoursDeConnexion){
            
            //Partie tjrs en cours de connexion => Actualisation de la liste des joueurs
            console.log("Rafraichir tableau liste des joueurs")
            rafraichirTableauListeJoueurs();
        }

        else if (result == codePartieEnCours){
            
            //Partie en cours => Redirection vers la route pour jouer
            let url = document.getElementById("hidden_url_jeu").value;
            console.log("Appeler route jouer, url: " + url);
            window.location.href = url;
        }
    });
}


async function rechercheEtatPartie()
{
    var urlEtatPartie = document.getElementById("hidden_etat_partie").value;

    try
        {
            const response = await fetch(  urlEtatPartie  )
            if (!response.ok) {
                throw new Error('Erreur de communication réseau, la route pour récupérer l\'état de la partie ne réponds pas!');
            }

            const data = await response.json();
            var retour = data.id

            return retour; 

        }
        catch (error) {
            console.error('Une erreur s\'est produite pendant une opération fetch pour voir si la partié était annulée:', error);
        }
}


//Appelé par lancer.html.twig
//Permet de rafraichir le tableau de la liste des joueurs, SANS rafraichir toute la page
function rafraichirTableauListeJoueurs()
{
        var urlListeJoueurs= document.getElementById("hidden_url_liste_joueurs").value; 

        fetch(  urlListeJoueurs  ).then(response => {
        if (!response.ok) {
            throw new Error('Erreur de communication réseau, la route pour récupérer la liste des joueurs ne réponds pas!');
        }
        return response.json();
    })
    .then (data => {

        let tableauJoueurEntete = `
            <table class="table lancer_partie_mulitijoueur__tableau_lien_tableau_liste_joueurs">
            <tbody>
                    <tr>
                        <th>Login</th> <th>Rôle</th>
                    </tr>
        `
        let tableauJoueurCorps = ``;
        
        
        let roleJoueur = ""
        data.forEach((joueur) => {
            
            if (joueur.estCreateur == false)
            {
                roleJoueur= "Joueur";
            }
            else
            {
                roleJoueur = "Créateur de la partie";
            }
            tableauJoueurCorps += `
                <tr>

                    <td class="login">
                        ${joueur.Joueur.login}
                    </td>
                                       
                    <td class="role">
                        ${roleJoueur}
                    </td>
                </tr>
            `
        }); 

        let tableauJoueurFin = `
            </tbody>
            </table>
        `;

        document.getElementById("idListeJoueur").innerHTML = tableauJoueurEntete+ tableauJoueurCorps + tableauJoueurFin;
    })
    .catch(error => {
        console.error('Une erreur s\'est produite pendant une opération fetch pour obtenir la liste des joueurs:', error);
    })
}


//OLD Plus utilisée était appelée par rafraichirElementsPages
//Return true si la partie a été annulée
async function verifPartieAnnulee(codePartieAbandonnee)
{
        var urlEtatPartie = document.getElementById("hidden_etat_partie").value; 
        
        try
        {
            const response = await fetch(  urlEtatPartie  )
            if (!response.ok) {
                throw new Error('Erreur de communication réseau, la route pour récupérer l\'état de la partie ne réponds pas!');
            }

            const data = await response.json();
            var retour = data.id

            if (retour == codePartieAbandonnee){
                document.getElementById("corpsPageLancerPartie").innerHTML = "<span>La partie a été annulée. Pour lancer une autre partie, allez dans le menu général et cliquez sur: </span> <span class='lien_relancer_une_partie'> << Créer/lancer une partie multijoueur >></span>";
                return true;
            }
        
            //la Partie n'a pas été annulée
            return false; 
        }
        catch (error) {
            console.error('Une erreur s\'est produite pendant une opération fetch pour voir si la partié était annulée:', error);
        }
}


function rafraichirPage () {
    location.reload();
    console.log("La page vient d'être rafraichie");
}