//Méthodes utilisées par le fichiers Twig du répertoire: partie_multi\jouer:

function redirigersurRouteJouer(codeConnexion = "-1") {
   
   let url = document.getElementById("hidden_url_jeu").value;
   url = url.replace('A_REMPLACER', codeConnexion);
   window.location.href = url;
   
}