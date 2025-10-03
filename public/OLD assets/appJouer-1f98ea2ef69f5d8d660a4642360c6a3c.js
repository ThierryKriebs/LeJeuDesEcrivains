//Méthodes utilisées par le fichiers Twig du répertoire: partie_multi\jouer:

function redirigersurRouteJouer(codeConnexion = "-1") {
   
   let url = document.getElementById("hidden_url_jeu").value;
   url = url.replace('A_REMPLACER', codeConnexion);
   window.location.href = url;
   
}

//Méthode utilisée par epreuve.html.twig, pour demander au serveur le temps restant avant la fin de l'épreuve en cours
// function recupererDureeRestanteEpreuve() {
//    console.log("L'onglet a retrouvé le focus !");
//    var urlContacterBackend = document.getElementById("hidden_url_epreuve_duree_restante").value; 

//    fetch(urlContacterBackend)
//    .then(response => response.json())
//    .then(data => {
//        console.log("Réponse reçue :", data);
//        delay = data;
//        delayMilli = delay * 1000; //Pour l'avoir en millisecondes
       
//        clearTimeout(timeoutId);

//        timeoutId = setTimeout(() => {
//        const form = document.getElementById('auto-submit-form');
//        const txt_redac = document.getElementById('redaction_form_redaction').value;            
       
//        form.submit();
  
//    }, delayMilli); 

//    })
//    .catch(error => console.error("Erreur :", error));
// }

async function demandeInfosSurpartieAuBackend() {
   console.log("L'onglet a retrouvé le focus !");

   try {
         var urlContacterBackend = document.getElementById("hidden_url_epreuve_duree_restante").value;
         console.log("Valeur initiale :", urlContacterBackend);
      
         var urlContacterBackend = document.getElementById("hidden_url_epreuve_duree_restante").value; 
         console.log(urlContacterBackend);
         urlContacterBackend = decodeURIComponent(decodeURIComponent(urlContacterBackend));
         console.log(urlContacterBackend);
         //<input type="hidden" id="hidden_url_epreuve_duree_restante" value= {{ url("app_jouer_multi__envoi_info_frontend") }}/{{ partieId }}/{{ typeInfo }}/{{ numeroEpreuve }}/ {{ epreuveEtat }}  /> 
         
         //https://127.0.0.1:8000/jouer-multi/envoi_info_frontend/80/epreuve/3/En%2520cours?codeConnexion=/

         const chaineARetirer = "envoi_info_frontend/" 
         const debut = urlContacterBackend.indexOf(chaineARetirer);
         const taille = chaineARetirer.length;
         tailleUrlAvantDonnees = debut + taille;
         // console.log("tailleUrlAvantDonnees: " + tailleUrlAvantDonnees);
         // console.log("URL à contacter: "+ urlContacterBackend) ;
         urlContacterBackend2 = urlContacterBackend.slice(tailleUrlAvantDonnees);

         const [partieId, typeInfo, numeroEpreuve, epreuveEtat, codeConnexion] = urlContacterBackend2.split("/");
         // console.log("URL à contacter: "+ urlContacterBackend) ;
         // console.log("Analyse de l'URL: partieId: " + partieId + ", typeInfo: " + typeInfo + ", numeroEpreuve:" + numeroEpreuve + ", epreuveEtat: " + epreuveEtat + ", codeConnexion: " + codeConnexion);
         
         const response = await fetch(urlContacterBackend);

         if (!response.ok) {
            throw new Error(`Erreur lors de la réception de la durée restante: ${response.status}`);
         }

         const data = await response.json();
         console.log("Réponse reçue :", data);

         //data:
         // 'numeroEpreuve' 
         // 'epreuveEtat' 
         // 'dureeRestante' 
         // 'typeInfo' 

         console.log("coucou0!!!! partieId:"   + partieId);
         console.log("coucou0!!!! typeInfo:"   + typeInfo);
         console.log("coucou0!!!! numeroEpreuve:"   + numeroEpreuve);
         console.log("coucou0!!!! epreuveEtat:"   + epreuveEtat);
         console.log("coucou0!!!! codeConnexion:"   + codeConnexion);


         console.log("coucou0!!!!  data: " + data.numeroEpreuve);
         console.log("coucou0!!!!  data: " + data.epreuveEtat);
         console.log("coucou0!!!!  data: " + data.typeInfo);

         //Vérification
         if (numeroEpreuve !== data.numeroEpreuve || epreuveEtat !== data.epreuveEtat ) {
            
            console.log("coucou0-1!!!!");
            if (typeInfo === "epreuve" || typeInfo === "notation") {
               console.log("coucou0 EPREUVE OU NOTATION!!!! ");


               //Le joueur a loupé une étape => Actualisation de la page
               // const form = document.getElementById('auto-submit-form');
               // form.submit();
            }
            else { //S'il s'agit de la salle d'attente
               // redirigersurRouteJouer(codeConnexion)
            }
         }

         else {
            console.log("coucou1!!!!");
            // if (Number.isInteger(data.dureeRestante)) {
            //     if (typeInfo === "epreuve" || typeInfo === "salleAttenteFinEpreuve") {
            //       delay = data.dureeRestanteEpreuve;
            //     }  

            //     else {
            //       delay = data.dureeRestanteNotation;
            //     }
            //     delayMilli = delay * 1000; //Pour l'avoir en millisecondes
   
            //    clearTimeout(timeoutId);
   
            //    if (typeInfo === "epreuve" || typeInfo === "notation") {
            //       timeoutId = setTimeout(() => {
            //          const form = document.getElementById('auto-submit-form');
            //          form.submit();
            //       }, delayMilli);
            //    }
            //    else { //S'il s'agit de la salle d'attente
            //       timeoutId = setTimeout(() => {
            //          redirigersurRouteJouer(codeConnexion) 
            //       }, delayMilli); 
            //    }
            // }
   
            // else {
            //    console.log("Erreur. Lors de la demande de la durée restante, le serveur a retourné l'erreur suivante: ", data.dureeRestante);
   
            //    //Si le serveur a retourné une erreur => actualiser la page! => A FAIRE!
            //    const form = document.getElementById('auto-submit-form');
            //    form.submit();
            // }

            if (data.erreur !== "")
            {
               console.log("Erreur. Lors de la demande de la durée restante, le serveur a retourné l'erreur suivante: ", data.erreur);
               if (typeInfo === "epreuve" || typeInfo === "notation") {
                  // const form = document.getElementById('auto-submit-form');
                  // form.submit();
               }

               else {
                  // redirigersurRouteJouer(codeConnexion)
               }
            }

            else {
               console.log("coucou!!!!");
               if (typeInfo === "salleAttenteDebutEpreuve") {
                  delay = data.dureeRestanteAvDebEpreuve;
               }

               if (typeInfo === "epreuve" || typeInfo === "salleAttenteFinEpreuve") {
                  delay = data.dureeRestanteAvFinEpreuve;
               }

               else if (typeInfo === "notation" || typeInfo === "salleAttenteFinNotation") {
                  delay = data.dureeRestanteAvFinNotation;
               }

               // ne pas en mettre dans ce cas de figure
               // else if (typeInfo === "SalleAttenteResultatFinal") {
               //    delay = ;
               // }

               // else {
               //    delay = data.dureeRestanteAvFinNotation;
               // }
            }
         }

   } catch (error) {
            console.error("Erreur lors de la demande au serveur de la durée restante: ", error);
   }
}