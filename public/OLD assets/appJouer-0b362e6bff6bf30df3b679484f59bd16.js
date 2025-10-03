//Méthodes utilisées par le fichiers Twig du répertoire: partie_multi\jouer:

function redirigersurRouteJouer(codeConnexion = "-1") {
   
   let url = document.getElementById("hidden_url_jeu").value;
   console.log("JE SORS: " + url)
   url = url.replace('A_REMPLACER', codeConnexion);
   window.location.href = url;
}

async function demandeInfosSurpartieAuBackend() {
   // console.log("L'onglet a retrouvé le focus !");

   try {
         var urlContacterBackend = document.getElementById("hidden_url_epreuve_duree_restante").value; 
         urlContacterBackend = decodeURIComponent(decodeURIComponent(decodeURIComponent(urlContacterBackend)));

         const chaineARetirer = "envoi_info_frontend/" 
         const debut = urlContacterBackend.indexOf(chaineARetirer);
         const taille = chaineARetirer.length;
         tailleUrlAvantDonnees = debut + taille;

         urlContacterBackend2 = urlContacterBackend.slice(tailleUrlAvantDonnees);
         const [partieId, typeInfo, numeroEpreuve, epreuveEtat, codeConnexion, FlagRedacsTerminees, FlagNotationsTerminees] = urlContacterBackend2.split("/");
         
         const response = await fetch(urlContacterBackend);

         if (!response.ok) {
            throw new Error(`Erreur lors de la réception de la durée restante: ${response.status}`);
         }

         const data = await response.json();
         // console.log("Réponse reçue :", data);

         //data:
         // 'numeroEpreuve' 
         // 'epreuveEtat' 
         // 'dureeRestante' 
         // 'typeInfo' 

         // console.log("  data.numeroEpreuve: " + data.numeroEpreuve);
         // console.log("  numeroEpreuve: " + numeroEpreuve);
         // console.log("  data: " + data.epreuveEtat);
         // console.log("  epreuveEtat: " + epreuveEtat);

         //Vérification
         if (numeroEpreuve != data.numeroEpreuve || epreuveEtat != data.epreuveEtat || typeInfo != data.typeInfo || FlagRedacsTerminees != data.FlagRedacsTerminees || FlagNotationsTerminees != data.FlagNotationsTerminees) {
            
            console.log("Les données ont changées");
            if (typeInfo === "epreuve" || typeInfo === "notation") {
               console.log("Les données ont changées:: EPREUVE ou NOTATION ");

               //Le joueur a loupé une étape => Actualisation de la page
               const form = document.getElementById('auto-submit-form');
               form.submit();
            }
            else { //S'il s'agit de la salle d'attente
               redirigersurRouteJouer(codeConnexion)
            }
         }

         else {
            console.log("AUCUN changement majeur");
            
            if (data.erreur !== "")
            {
               console.log("Erreur. Lors de la demande de la durée restante, le serveur a retourné l'erreur suivante: ", data.erreur);
               if (typeInfo === "epreuve" || typeInfo === "notation") {
                  const form = document.getElementById('auto-submit-form');
                  form.submit();
               }

               else {
                  redirigersurRouteJouer(codeConnexion)
               }
            }

            else {
               if (typeInfo === "salleAttenteDebutEpreuve") {
                  delay = data.dureeRestanteAvantDebutEpreuve;
               }

               else if (typeInfo === "salleAttenteDebutNotation") {
                  delay = data.dureeRestanteAvantDebutNotation;
               }

               else if (typeInfo === "epreuve" || typeInfo === "salleAttenteFinEpreuve") {
                  delay = data.dureeRestanteAvantFinEpreuve;
               }

               else if (typeInfo === "notation" || typeInfo === "salleAttenteFinNotation") {
                  delay = data.dureeRestanteAvantFinNotation;
               }

               delayMilli = delay * 1000;

               clearTimeout(timeoutId);
   
               if (typeInfo === "epreuve" || typeInfo === "notation") {
                  timeoutId = setTimeout(() => {
                     const form = document.getElementById('auto-submit-form');
                     form.submit();
                  }, delayMilli);
               }
               else { //S'il s'agit de la salle d'attente
                  console.log("On doit sortir de la salle d'attente. delayMilli: " + delayMilli);
                  timeoutId = setTimeout(() => {
                     redirigersurRouteJouer(codeConnexion) 
                  }, delayMilli); 
               }
            }
         }

   } catch (error) {
            console.error("Erreur lors de la demande au serveur de la durée restante: ", error);
   }
}