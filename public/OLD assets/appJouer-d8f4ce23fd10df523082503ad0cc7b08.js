//Méthodes utilisées par le fichiers Twig du répertoire: partie_multi\jouer:

function redirigersurRouteJouer(codeConnexion = "-1") {
   
   let url = document.getElementById("hidden_url_jeu").value;
   url = url.replace('A_REMPLACER', codeConnexion);
   window.location.href = url;
   
}

//Méthode utilisée par epreuve.html.twig, pour demander au serveur le temps restant avant la fin de l'épreuve en cours
// function recupererDureeRestanteEpreuve() {
//    console.log("L'onglet a retrouvé le focus !");
//    var urlActualiserDelais = document.getElementById("hidden_url_epreuve_duree_restante").value; 

//    fetch(urlActualiserDelais)
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

async function recupererDureeRestanteEpreuve() {
   console.log("L'onglet a retrouvé le focus !");

   try {
         var urlActualiserDelais = document.getElementById("hidden_url_epreuve_duree_restante").value; 
         console.log("URL à contacter: "+ urlActualiserDelais) ;
         const response = await fetch(urlActualiserDelais);

         if (!response.ok) {
            throw new Error(`Erreur lors de la réception de la durée restante: ${response.status}`);
         }

         const data = await response.json();
         console.log("Réponse reçue :", data);

         if (Number.isInteger(data.dureeRestante)) {
            delay = data.dureeRestante;
            delayMilli = delay * 1000; //Pour l'avoir en millisecondes

            clearTimeout(timeoutId);

            timeoutId = setTimeout(() => {
               const form = document.getElementById('auto-submit-form');
               form.submit();
            }, delayMilli);
         }

         else {
            console.log("Erreur. Lors de la demande de la durée restante, le serveur a retourné l'erreur suivante: ", data.dureeRestante);
         }
         

   } catch (error) {
            console.error("Erreur lors de la demande au serveur de la durée restante: ", error);
   }
}

//A supprimer!
// async function demanderAuServeurDureeRestante (idPartie){

//    var urlActualiserDelais = document.getElementById("hidden_url_epreuve_duree_restante").value; 

//    try
//    {
//        const response = await fetch(  urlActualiserDelais  )
//        if (!response.ok) {
//          throw new Error('Erreur de communication réseau, la route pour récupérer le délais avant fin de l\'épreuve ne réponds pas!');
//        }

//        //return response.json();
//        const data = await response.json();
//        console.log("data: ");
//        console.log(data);
       
//        var retour = data.retour;
//        console.log(retour);
   
//        return retour; 
//    }
//    catch (error) {
//        console.error('Une erreur s\'est produite pendant une opération fetch pour récupérer le délais avant fin de l\'épreuve :', error);
//    }
// }

