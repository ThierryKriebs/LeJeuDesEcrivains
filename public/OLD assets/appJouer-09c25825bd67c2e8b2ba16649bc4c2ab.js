//Méthodes utilisées par le fichiers Twig du répertoire: partie_multi\jouer:

function redirigersurRouteJouer(codeConnexion = "-1") {
   
   let url = document.getElementById("hidden_url_jeu").value;
   url = url.replace('A_REMPLACER', codeConnexion);
   window.location.href = url;
   
}

async function recupererDureeRestanteEpreuve (idPartie){

   var urlActualiserDelais = document.getElementById("hidden_url_epreuve_duree_restante").value; 

   try
   {
       const response = await fetch(  urlActualiserDelais  )
       if (!response.ok) {
         throw new Error('Erreur de communication réseau, la route pour récupérer le délais avant fin de l\'épreuve ne réponds pas!');
       }

       //return response.json();
       const data = await response.json();
       console.log("data: ");
       console.log(data);
       
       var retour = data.retour;
       console.log(retour);
   
       return retour; 
   }
   catch (error) {
       console.error('Une erreur s\'est produite pendant une opération fetch pour récupérer le délais avant fin de l\'épreuve :', error);
   }
}


function recupererDureeRestanteEpreuve2 () {
   return 30;
}