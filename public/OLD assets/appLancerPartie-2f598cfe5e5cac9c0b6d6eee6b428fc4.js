//Méthodes utilisées par le fichier Twig: lancer.html.twig

console.log('This log comes from assets/appLancerPartie.js - welcome to AssetMapper! 🎉');

//Méthode Javascript appelées par les fichiers Twig

//Permet de copier des informations dans le presse papier
function copierPressePapier (nomElaCopier = "adrACopier", nomElConfCopie="TextElCopie", attributACopier="href") {
    console.log ("Appel de la méthode copierPressePapier");

    let elementACopier = document.getElementById(nomElaCopier)[attributACopier];
    console.log("Elément copié dans le presse papier: "+elementACopier);

    navigator.clipboard.writeText(elementACopier); // méthode numéro 2
    let labelCopie= document.getElementById(nomElConfCopie);
    labelCopie.innerHTML=" Lien copié!";

    myVarEffacer = setTimeout(() => {
        labelCopie.innerHTML="";
        
    }, 5000);
    
}

//Appelé par lancer.html.twig
//Recup de l'état de la partie et si elle est tjrs en cours, met à jour la liste des joueurs
//Si elle a été lancée, redirige vers la route pour jouer
function rafraichirElementsPages(codePartieEnCours, codePartieTerminee, codePartieEnCoursDeConnexion, codePartieAbandonnee, codeConnexion) {

    rechercheEtatPartie(codeConnexion).then(result => {
   
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
            console.log("url: " + url);
            window.location.href = url;
        }
    });

    //Ancien code
    // verifPartieAnnulee(codePartieAbandonnee).then(result => {
        
    //     //Partie tjrs en cours => Actualisation de la liste des joueurs
    //     if (!result){ 
    //         rafraichirTableauListeJoueurs();
    //     }
    // });
}

async function rechercheEtatPartie(codePartieEnCours, codePartieTerminee, codePartieEnCoursDeConnexion, codePartieAbandonnee,codeConnexion)
{
    var urlEtatPartie = document.getElementById("hidden_etat_partie").value;

    try
        {
            const response = await fetch(  urlEtatPartie  )
            if (!response.ok) {
                throw new Error('Erreur de communication réseau, la route pour récupérer l\'état de la partie ne réponds pas!');
            }

            //return response.json();
            const data = await response.json();
            var retour = data.id

            // if (retour == codePartieAbandonnee){
            //     document.getElementById("corpsPageLancerPartie").innerHTML = "<span>La partie a été annulée. Pour lancer une autre partie, allez dans le menu général et cliquez sur: </span> <span class='lien_relancer_une_partie'> << Créer/lancer une partie multijoueur >></span>";
            // }

            // else if (retour == codePartieTerminee){
            //     document.getElementById("corpsPageLancerPartie").innerHTML = "<span>La partie est terminée! Pour lancer une autre partie, allez dans le menu général et cliquez sur: </span> <span class='lien_relancer_une_partie'> << Créer/lancer une partie multijoueur >></span>";
            // }

            // else if (retour == codePartieEnCoursDeConnexion){
            //     //Que faire?
            // }

            // else if (retour == codePartieEnCours){
            //     //Redirection vers la route pour jouer
            //     let url = "{{ path('app_jouer-multi_jouer', { 'codeConnexion': '' }) }}" + codeConnexion;
            //     console.log("url: " + url)
            //     window.location.href = url;
            // }
        
            return retour; 
        }
        catch (error) {
            console.error('Une erreur s\'est produite pendant une opération fetch pour voir si la partié était annulée:', error);
        }

}

//OLD Appelé par rafraichirElementsPages
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

            //return response.json();
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

    // })
    // .then (data => {

    //     //Si la partie a été annulée, l'affiche sur la page.
    //     console.log(data.id );
    //     var retour = data.id
       
    // })
    // .catch(error => {
    //     console.error('Une erreur s\'est produite pendant une opération fetch pour voir si la partié était annulée:', error);
    // })

}
//Plus utilisé?!!!
// async function verifPartieAnnulee2()
// {
//     var urlEtatPartie = await document.getElementById("hidden_etat_partie").value; 
//     const reponse = await fetch(  urlEtatPartie  );
//     const resultat = await reponse.json();
//     console.log(resultat.id );
//     var retour = resultat.id

//     if (retour == 4){
//         document.getElementById("corpsPageLancerPartie").innerHTML = "<span>La partie a été annulée. Pour lancer vous-même une partie, allez dans le menu général et cliquez sur: Créer/lancer une partie multijoueur</span>";
//         console.log("partie annulée => return TRUE");
//         return true;
//     }

//     console.log("partie maintenue => return FALSE");
//     return false; //la Partie n'a pas été annulée

// }


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
            <table class="table">
            <tbody>
                    <tr>
                        <th class="demarragePartiMulti_th">Login</th> <th class="demarragePartiMulti_th">Rôle</th>
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

                    <td class="demarragePartiMulti_login">
                        ${joueur.Joueur.login}
                    </td>
                                       
                    <td class="demarragePartiMulti_role">
                        ${roleJoueur}
                    </td>
                </tr>
            `
        }); 

        let tableauJoueurFin = `
            </tbody>
            </table>
        `;

        document.getElementById("idListeJoueur").innerHTML= "<div class='divDemarragePartiMulti'>" + tableauJoueurEntete+ tableauJoueurCorps + tableauJoueurFin + "</div>";
        //console.log("Rafraichissement terminé");
    })
    .catch(error => {
        console.error('Une erreur s\'est produite pendant une opération fetch pour obtenir la liste des joueurs:', error);
    })
}



function rafraichirPage () {
    //window.setTimeout("location=('tonurl');",30000)
    location.reload();
    console.log("La page vient d'être rafraichie");
}

//Appelé par rejoindre.html.twig => Modifie l'adresse d'un lien en y ajoutant le contenu d'un input text
// function injecterInputDansLien(chemin) {
//     document.getElementById('idCodeConnexion').addEventListener('input', ()=>{
//         var inputValue = this.value;
//         console.log("inputValue") ;
//         var lien = document.getElementById("idLien");
//         lien.href= chemin + encodeURIComponent(inputValue);
//     })

// }


//Appelé par demarrage.html.twig => PLUS UTILISE!!!
// function rafraichirRegulierementPage(interval=5){
//     setInterval(()  => { 
//                     window.location.reload();
//                     console.log("La page vient d'être rafraichie");
//                 }, interval*1000);
// }


