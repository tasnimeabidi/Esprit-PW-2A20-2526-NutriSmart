document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("postForm");

    const titreInput = document.querySelector("input[name='titre']");
    const contenuInput = document.querySelector("textarea[name='contenu']");

    const titreError = document.createElement("div");
    const contenuError = document.createElement("div");

    titreError.style.color = "red";
    titreError.style.fontSize = "12px";
    titreError.style.marginTop = "5px";

    contenuError.style.color = "red";
    contenuError.style.fontSize = "12px";
    contenuError.style.marginTop = "5px";

    titreInput.parentNode.appendChild(titreError);
    contenuInput.parentNode.appendChild(contenuError);

    function validate() {
        let valid = true;

        const titre = titreInput.value.trim();
        const contenu = contenuInput.value.trim();

        titreError.textContent = "";
        contenuError.textContent = "";

        // TITRE RULES
        if (!titre) {
            titreError.textContent = "Titre obligatoire";
            valid = false;
        } else if (titre.length < 5) {
            titreError.textContent = "Titre doit etre Minimum 10 caractères";
            valid = false;
        } else if (titre.length > 50) {
            titreError.textContent = "Titre doit etre Maximum 50 caractères";
            valid = false;
        }

        // CONTENU RULES
        if (contenu.length < 5) {
            contenuError.textContent = "Contenu doit etre minimum 5 caractères";
            valid = false;
        } else if (contenu.length > 100) {
            contenuError.textContent = "Contenu doit etre maximum 100 caractères";
            valid = false;
        }

        return valid;
    }

    form.addEventListener("submit", function (e) {
        if (!validate()) {
            e.preventDefault();
        }
    });

    titreInput.addEventListener("input", validate);
    contenuInput.addEventListener("input", validate);
});