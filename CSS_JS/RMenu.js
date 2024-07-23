window.onload = () => {
    var usecopymenu = false;
    // On écoute le clic pour retirer le menu
    document.addEventListener("click", 
    function () {

        if (document.querySelector('#context-menu').classList.contains('active') && usecopymenu === true) {
            document.querySelector('#context-menu').classList.remove('active');
            usecopymenu = false;
        } else if (document.querySelector('#context-menu').classList.contains('active') && usecopymenu === false) {
            usecopymenu = true;
        }
        // console.log(usecopymenu);
    }
    );

    document.addEventListener('keydown', function (event) {
        var name = event.key;
        var code = event.code;
        // Alert the key name and key code on keydown
        console.log(`Key pressed ${name} \r\n Key code value: ${code}`);
      }, false);



    // On écoute le clic droit (ouverture du menu contextuel)
    document.querySelector(".btncopy").addEventListener("click", function (event) {
        // document.addEventListener("contextmenu", function (event) {
        // On a ouvert le menu
        // On empêche le "vrai" menu d'apparaître
        // event.preventDefault();

        // On récupère le menu
        let menu = document.querySelector("#context-menu");

        // On met ou retire la classe active
        menu.classList.add("active");
        // On ouvre le menu là où se trouve la souris
        // On récupère les coordonnées de la souris
        let posX = event.clientX;
        let posY = event.clientY;

        // On calcule la position du menu pour éviter qu'il dépasse
        // Position la plus à droite "largeur fenêtre - largeur menu - 25"
        // menu.clientWidth = "500px";
        
        let maxX = window.innerWidth - menu.clientWidth - 25;

        // Position la plus basse "hauteur fenêtre - hauteur menu - 25"
        let maxY = window.innerHeight - menu.clientHeight - 25;

        // On vérifie si on dépasse
        if (posX > maxX) {
            posX = maxX;
        }
        if (posY > maxY) {
            posY = maxY;
        }

        // On positionne le menu
        menu.style.top = posY + "px";
        menu.style.left = posX + "px";
    });



};