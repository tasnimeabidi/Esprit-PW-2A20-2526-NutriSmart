/**
 * Contrôles de saisie métier (sans HTML5) + erreurs par champ (bordure rouge + message).
 */
(function (global) {
  "use strict";

  function trim(v) {
    return v == null ? "" : String(v).trim();
  }

  function isDigitsPositiveInt(v) {
    return trim(v) !== "" && /^[1-9]\d*$/.test(trim(v));
  }

  function dateIsoOk(v) {
    var s = trim(v);
    if (!/^\d{4}-\d{2}-\d{2}$/.test(s)) return false;
    var p = s.split("-");
    var d = new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10));
    return (
      d.getFullYear() === parseInt(p[0], 10) &&
      d.getMonth() === parseInt(p[1], 10) - 1 &&
      d.getDate() === parseInt(p[2], 10)
    );
  }

  /** Lettres (toutes langues), espaces, tiret, apostrophe — pas de chiffres ni ponctuation (. , : ? % …). */
  function estTexteLettresEspacesTiretApostrophe(s) {
    var t = trim(s);
    if (t === "") return true;
    return /^[\p{L}\s'’\-]+$/u.test(t);
  }

  /** Lettres, chiffres, espaces, tiret, apostrophe — interdit . , : ? % */
  function estTexteObjectifOuTypeRepasOk(s) {
    var t = trim(s);
    if (t === "") return true;
    if (/[.,:?%]/.test(t)) return false;
    return /^[\p{L}\p{N}\s'’\-]+$/u.test(t);
  }

  function activiteFixePage() {
    if (typeof document === "undefined") return "";
    var h = document.documentElement && document.documentElement.getAttribute("data-ns-activite-fixe");
    return h ? trim(h) : "";
  }

  function progTypeSportElement() {
    if (typeof document === "undefined") return null;
    return document.getElementById("prog-type-sport");
  }

  /** @typedef {{ id: string, msg: string }} FieldErr */

  function NutriSmartSaisieCrudRegles() {}

  /** @return {FieldErr[]} */
  NutriSmartSaisieCrudRegles.prototype.planRepasFieldErrors = function (payload) {
    /** @type {FieldErr[]} */
    var e = [];
    if (!isDigitsPositiveInt(payload.idUtilisateur)) {
      e.push({ id: "pr-id-utilisateur", msg: "L'identifiant utilisateur doit être un entier strictement positif." });
    }
    if (!dateIsoOk(payload.dateDebut)) {
      e.push({ id: "pr-date-debut", msg: "La date de début doit être au format AAAA-MM-JJ." });
    }
    if (!dateIsoOk(payload.dateFin)) {
      e.push({ id: "pr-date-fin", msg: "La date de fin doit être au format AAAA-MM-JJ." });
    }
    if (dateIsoOk(payload.dateDebut) && dateIsoOk(payload.dateFin) && trim(payload.dateDebut) > trim(payload.dateFin)) {
      e.push({
        id: "pr-date-fin",
        msg: "La date de fin doit être postérieure ou égale à la date de début.",
      });
    }
    if (trim(payload.objectif) === "") {
      e.push({ id: "pr-objectif", msg: "L'objectif est obligatoire." });
    } else if (trim(payload.objectif).length > 255) {
      e.push({ id: "pr-objectif", msg: "L'objectif ne peut pas dépasser 255 caractères." });
    } else if (!estTexteLettresEspacesTiretApostrophe(payload.objectif)) {
      e.push({
        id: "pr-objectif",
        msg:
          "L'objectif ne doit contenir que des lettres, espaces, tiret (-) ou apostrophe. Pas de chiffres ni de ponctuation (. ? / …).",
      });
    }
    if (trim(payload.statut) === "") {
      e.push({ id: "pr-statut", msg: "Veuillez choisir un statut." });
    } else if (trim(payload.statut).length > 64) {
      e.push({ id: "pr-statut", msg: "Le statut ne peut pas dépasser 64 caractères." });
    }
    return e;
  };

  /** @return {FieldErr[]} */
  NutriSmartSaisieCrudRegles.prototype.repasFieldErrors = function (payload) {
    /** @type {FieldErr[]} */
    var e = [];
    if (!isDigitsPositiveInt(payload.idPlan)) {
      e.push({ id: "repas-id-plan", msg: "Choisissez un plan repas valide (identifiant entier positif)." });
    }
    if (trim(payload.type) === "") {
      e.push({ id: "repas-type", msg: "Le type de repas est obligatoire." });
    } else if (trim(payload.type).length > 64) {
      e.push({ id: "repas-type", msg: "Le type ne peut pas dépasser 64 caractères." });
    } else if (!estTexteObjectifOuTypeRepasOk(payload.type)) {
      e.push({
        id: "repas-type",
        msg: "Utilisez uniquement des lettres, chiffres, espaces, tiret ou apostrophe. Évitez . , : ? % et autres symboles.",
      });
    }
    var idRec = trim(payload.idRecette);
    if (idRec !== "" && !isDigitsPositiveInt(idRec)) {
      e.push({ id: "repas-id-recette", msg: "La recette doit être un identifiant entier positif ou vide." });
    }
    var cal = trim(payload.calories);
    if (cal !== "" && (!/^\d+$/.test(cal) || parseInt(cal, 10) < 0)) {
      e.push({ id: "repas-calories", msg: "Les calories doivent être un entier positif ou zéro, ou vide." });
    }
    return e;
  };

  /** @return {FieldErr[]} */
  NutriSmartSaisieCrudRegles.prototype.programmeSportifFieldErrors = function (payload) {
    /** @type {FieldErr[]} */
    var e = [];
    if (!isDigitsPositiveInt(payload.idPlan)) {
      e.push({ id: "prog-id-plan", msg: "Choisissez un plan repas valide." });
    }
    var fixe = activiteFixePage();
    var ts = trim(payload.typeSport);
    if (fixe) {
      if (ts !== fixe) {
        e.push({
          id: "prog-type-sport",
          msg:
            "Sur cette page, seule l’activité « " +
            fixe +
            " » est autorisée. Une autre discipline (ex. Cardio) ne peut pas être enregistrée.",
        });
      }
    } else if (ts === "") {
      e.push({ id: "prog-type-sport", msg: "Le type d'activité est obligatoire." });
    } else if (ts.length > 128) {
      e.push({ id: "prog-type-sport", msg: "Le type d'activité ne peut pas dépasser 128 caractères." });
    } else {
      var el = progTypeSportElement();
      var isSelect = el && el.tagName === "SELECT";
      if (!isSelect && !estTexteLettresEspacesTiretApostrophe(ts)) {
        e.push({
          id: "prog-type-sport",
          msg: "Utilisez uniquement des lettres (espaces, tiret ou apostrophe autorisés). Pas de chiffres ni de caractères comme . , : ? %.",
        });
      }
    }
    if (trim(payload.niveau).length > 64) {
      e.push({ id: "prog-niveau", msg: "Le niveau ne peut pas dépasser 64 caractères." });
    }
    if (trim(payload.intensite).length > 64) {
      e.push({ id: "prog-intensite", msg: "L'intensité ne peut pas dépasser 64 caractères." });
    }
    if (!dateIsoOk(payload.dateSeance)) {
      e.push({ id: "prog-date-seance", msg: "La date de séance doit être au format AAAA-MM-JJ." });
    }
    if (!isDigitsPositiveInt(payload.dureeMin)) {
      e.push({ id: "prog-duree-min", msg: "La durée doit être un nombre entier de minutes (≥ 1)." });
    }
    var stSeance = trim(payload.statut);
    if (stSeance === "") {
      stSeance = "prevue";
    }
    if (stSeance.length > 64) {
      e.push({ id: "prog-statut-seance", msg: "Le statut de séance ne peut pas dépasser 64 caractères." });
    }
    return e;
  };

  /** Objectif plan repas : lettres / espaces / tiret / apostrophe uniquement (pour affichage ou préremplissage). */
  NutriSmartSaisieCrudRegles.prototype.sanitizePlanObjectifLetters = function (s) {
    return String(s || "")
      .replace(/[^\p{L}\s'’\-]/gu, "")
      .replace(/\s+/g, " ")
      .trim();
  };

  /* Anciennes méthodes (premier message seulement) — conservées pour compatibilité */
  NutriSmartSaisieCrudRegles.prototype.planRepas = function (payload) {
    var arr = this.planRepasFieldErrors(payload);
    return arr.length ? arr[0].msg : null;
  };
  NutriSmartSaisieCrudRegles.prototype.repas = function (payload) {
    var arr = this.repasFieldErrors(payload);
    return arr.length ? arr[0].msg : null;
  };
  NutriSmartSaisieCrudRegles.prototype.programmeSportif = function (payload) {
    var arr = this.programmeSportifFieldErrors(payload);
    return arr.length ? arr[0].msg : null;
  };

  /** Erreurs serveur / métier → champ ciblé (message global affiché aussi par le CRUD). */
  NutriSmartSaisieCrudRegles.prototype.apiErrorsForProgramme = function (message) {
    var m = trim(message);
    if (!m) return [];
    if (/plan repas introuvable/i.test(m)) {
      return [{ id: "prog-id-plan", msg: m }];
    }
    if (/date de séance/i.test(m)) {
      return [{ id: "prog-date-seance", msg: m }];
    }
    if (/durée/i.test(m)) {
      return [{ id: "prog-duree-min", msg: m }];
    }
    if (/statut/i.test(m)) {
      return [{ id: "prog-statut-seance", msg: m }];
    }
    return [];
  };

  NutriSmartSaisieCrudRegles.prototype.apiErrorsForRepas = function (message) {
    var m = trim(message);
    if (!m) return [];
    if (/plan repas introuvable/i.test(m)) {
      return [{ id: "repas-id-plan", msg: m }];
    }
    return [];
  };

  NutriSmartSaisieCrudRegles.prototype.apiErrorsForPlanRepas = function (message) {
    var m = trim(message);
    if (!m) return [];
    if (/utilisateur/i.test(m)) {
      return [{ id: "pr-id-utilisateur", msg: m }];
    }
    return [];
  };

  var NutriSmartSaisieCrudUi = {
    clearFieldErrors: function (root) {
      if (!root || !root.querySelectorAll) return;
      root.querySelectorAll("[data-ns-saisie-error]").forEach(function (n) {
        if (n.parentNode) n.parentNode.removeChild(n);
      });
      root.querySelectorAll(".ns-saisie-input-invalid").forEach(function (el) {
        el.classList.remove("ns-saisie-input-invalid");
      });
    },

    /** @param {HTMLElement} root @param {FieldErr[]} items */
    showFieldErrors: function (root, items) {
      this.clearFieldErrors(root);
      if (!items || !items.length) return;
      items.forEach(function (it) {
        var el = document.getElementById(it.id);
        if (!el) return;
        el.classList.add("ns-saisie-input-invalid");
        var p = document.createElement("p");
        p.className = "ns-saisie-field-msg";
        p.setAttribute("data-ns-saisie-error", "1");
        p.setAttribute("role", "alert");
        p.textContent = it.msg;
        el.insertAdjacentElement("afterend", p);
      });
      var first = document.getElementById(items[0].id);
      if (first && typeof first.focus === "function") {
        try {
          first.focus({ preventScroll: false });
        } catch (err) {
          first.focus();
        }
      }
    },

    attachLiveClear: function (root) {
      if (!root || root.getAttribute("data-ns-live-clear") === "1") return;
      root.setAttribute("data-ns-live-clear", "1");
      root.addEventListener(
        "input",
        function (ev) {
          var t = ev.target;
          if (!t || !t.id) return;
          t.classList.remove("ns-saisie-input-invalid");
          var next = t.nextElementSibling;
          if (next && next.getAttribute && next.getAttribute("data-ns-saisie-error") === "1") {
            next.parentNode.removeChild(next);
          }
        },
        true
      );
      root.addEventListener(
        "change",
        function (ev) {
          var t = ev.target;
          if (!t || !t.id) return;
          t.classList.remove("ns-saisie-input-invalid");
          var next = t.nextElementSibling;
          if (next && next.getAttribute && next.getAttribute("data-ns-saisie-error") === "1") {
            next.parentNode.removeChild(next);
          }
        },
        true
      );
    },
  };

  var inst = new NutriSmartSaisieCrudRegles();
  global.NutriSmartSaisieCrud = inst;
  global.NutriSmartSaisieCrudUi = NutriSmartSaisieCrudUi;

  if (typeof document !== "undefined") {
    document.addEventListener("DOMContentLoaded", function () {
      document.addEventListener(
        "input",
        function (ev) {
          var t = ev.target;
          if (!t || !t.getAttribute) return;
          if (t.getAttribute("data-ns-texte-sans-ponct") === "1") {
            var next = t.value.replace(/[.,:?%]/g, "");
            if (next !== t.value) t.value = next;
          }
          if (t.getAttribute("data-ns-texte-lettres") === "1") {
            var cl = t.value.replace(/[^\p{L}\s'’\-]/gu, "");
            if (cl !== t.value) t.value = cl;
          }
        },
        true
      );
    });
  }
})(typeof window !== "undefined" ? window : this);
