/**
 * Validation des formulaires d'authentification / profil sans HTML5 (novalidate côté HTML).
 */
(function (global) {
  "use strict";

  function trim(v) {
    return v == null ? "" : String(v).trim();
  }

  function emailOk(v) {
    var s = trim(v);
    if (s === "") return false;
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(s);
  }

  function NutriSmartSaisieAuth() {}

  NutriSmartSaisieAuth.prototype.login = function (email, password) {
    if (!emailOk(email)) return "Veuillez saisir une adresse e-mail valide.";
    if (trim(password).length < 6) return "Le mot de passe doit contenir au moins 6 caractères.";
    return null;
  };

  NutriSmartSaisieAuth.prototype.register = function (idUser, nom, email, password) {
    if (!/^[1-9]\d*$/.test(trim(idUser))) return "L'identifiant utilisateur doit être un entier positif.";
    if (trim(nom).length < 2) return "Le nom doit contenir au moins 2 caractères.";
    if (!emailOk(email)) return "Veuillez saisir une adresse e-mail valide.";
    if (trim(password).length < 6) return "Le mot de passe doit contenir au moins 6 caractères.";
    return null;
  };

  NutriSmartSaisieAuth.prototype.profil = function (age, poids, taille) {
    var a = trim(age);
    var po = trim(poids).replace(",", ".");
    var t = trim(taille);
    if (a === "" || !/^\d+$/.test(a) || parseInt(a, 10) < 1 || parseInt(a, 10) > 120) {
      return "L'âge doit être un entier entre 1 et 120.";
    }
    if (po === "" || isNaN(Number(po)) || Number(po) <= 0) {
      return "Le poids doit être un nombre positif.";
    }
    if (t === "" || !/^\d+$/.test(t) || parseInt(t, 10) < 50 || parseInt(t, 10) > 260) {
      return "La taille doit être un entier raisonnable (cm), entre 50 et 260.";
    }
    return null;
  };

  NutriSmartSaisieAuth.prototype.afficherErreur = function (form, message) {
    var id = form.getAttribute("data-error-id") || "form-js-error";
    var el = document.getElementById(id);
    if (!el) {
      el = document.createElement("p");
      el.id = id;
      el.setAttribute("role", "alert");
      el.style.cssText = "color:#b71c1c;font-size:0.9rem;margin:0 0 0.75rem;";
      form.insertBefore(el, form.firstChild);
    }
    el.textContent = message || "";
  };

  global.NutriSmartSaisieAuth = new NutriSmartSaisieAuth();
})(typeof window !== "undefined" ? window : this);
