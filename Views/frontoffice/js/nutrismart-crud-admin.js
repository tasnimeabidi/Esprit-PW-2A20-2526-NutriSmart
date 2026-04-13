/**
 * Interface admin / front : onglets + formulaires + tableaux branchés sur NutriSmartCRUD.
 * Optionnel (front office) :
 *   window.NUTRISMART_CRUD_ADMIN_CONFIG = { root: "#fo-crud-plan", tabSelector: ".fo-crud-tab", ... }
 *   window.NUTRISMART_CRUD_ENTITIES = ["planRepas", "repas"]; // sous-ensemble
 */
(function () {
  "use strict";

  var CFG = (typeof window !== "undefined" && window.NUTRISMART_CRUD_ADMIN_CONFIG) || {};
  var ENTITIES =
    (typeof window !== "undefined" && window.NUTRISMART_CRUD_ENTITIES) || [
      "planRepas",
      "repas",
      "programmeSportif",
    ];
  var TAB_SEL = CFG.tabSelector || ".bo-crud-tab";
  var PANEL_SEL = CFG.panelSelector || ".bo-crud-panel";
  var MSG_CLASS = CFG.msgClassBase || "bo-crud-msg";
  var ACTIONS_CLASS = CFG.actionsClass || "bo-crud-actions";

  function entityEnabled(name) {
    return ENTITIES.indexOf(name) !== -1;
  }

  function rootEl() {
    if (CFG.root) {
      var r = document.querySelector(CFG.root);
      return r || document;
    }
    return document;
  }

  function $(id) {
    return document.getElementById(id);
  }

  function showMsg(text, isErr) {
    var el = $("crud-global-msg");
    if (!el) return;
    el.textContent = text || "";
    el.className = MSG_CLASS + (isErr ? " " + MSG_CLASS + "--err" : "");
  }

  function fillSelect(sel, items, valueKey, labelFn, emptyLabel) {
    if (!sel) return;
    var v = sel.value;
    sel.innerHTML = "";
    var o0 = document.createElement("option");
    o0.value = "";
    o0.textContent = emptyLabel || "—";
    sel.appendChild(o0);
    items.forEach(function (it) {
      var o = document.createElement("option");
      o.value = String(it[valueKey]);
      o.textContent = labelFn(it);
      sel.appendChild(o);
    });
    if (v && Array.prototype.some.call(sel.options, function (opt) { return opt.value === v; })) {
      sel.value = v;
    }
  }

  var editing = {
    planRepas: null,
    repas: null,
    programmeSportif: null,
  };

  function renderPlanRepas() {
    var tb = $("tbody-plan-repas");
    if (!tb) return;
    var rows = NutriSmartCRUD.planRepas.list();
    tb.innerHTML = "";
    rows.forEach(function (r) {
      var tr = document.createElement("tr");
      var objRaw = r.objectif == null ? "" : String(r.objectif);
      var objCell = escapeHtml(objRaw);
      if (typeof window !== "undefined" && window.NutriSmartSaisieCrud && window.NutriSmartSaisieCrud.sanitizePlanObjectifLetters) {
        var san = window.NutriSmartSaisieCrud.sanitizePlanObjectifLetters(objRaw);
        if (objRaw.trim() !== "" && san === "") {
          objCell = "—";
        } else {
          objCell = escapeHtml(san);
        }
      }
      tr.innerHTML =
        "<td>" +
        r.id +
        "</td><td>" +
        escapeHtml(r.idUtilisateur) +
        "</td><td>" +
        escapeHtml(r.dateDebut) +
        "</td><td>" +
        escapeHtml(r.dateFin) +
        "</td><td>" +
        objCell +
        "</td><td>" +
        escapeHtml(r.statut) +
        '</td><td class="' +
        ACTIONS_CLASS +
        '">' +
        '<button type="button" class="action-btn" data-act="edit" data-id="' +
        r.id +
        '">✏️</button> ' +
        '<button type="button" class="action-btn" data-act="del" data-id="' +
        r.id +
        '">🗑️</button>' +
        "</td>";
      tb.appendChild(tr);
    });
    refreshFkSelects();
  }

  function renderRepas() {
    var tb = $("tbody-repas");
    if (!tb) return;
    var rows = NutriSmartCRUD.repas.list();
    tb.innerHTML = "";
    rows.forEach(function (r) {
      var tr = document.createElement("tr");
      tr.innerHTML =
        "<td>" +
        r.id +
        "</td><td>" +
        escapeHtml(r.idPlan) +
        "</td><td>" +
        escapeHtml(r.idRecette) +
        "</td><td>" +
        escapeHtml(r.type) +
        "</td><td>" +
        escapeHtml(r.calories) +
        '</td><td class="' +
        ACTIONS_CLASS +
        '">' +
        '<button type="button" class="action-btn" data-act="edit" data-id="' +
        r.id +
        '">✏️</button> ' +
        '<button type="button" class="action-btn" data-act="del" data-id="' +
        r.id +
        '">🗑️</button>' +
        "</td>";
      tb.appendChild(tr);
    });
    refreshFkSelects();
  }

  function renderProgrammes() {
    var tb = $("tbody-programme");
    if (!tb) return;
    var rows = NutriSmartCRUD.programmeSportif.list();
    tb.innerHTML = "";
    rows.forEach(function (r) {
      var tr = document.createElement("tr");
      tr.innerHTML =
        "<td>" +
        r.id +
        "</td><td>" +
        escapeHtml(r.idPlan) +
        "</td><td>" +
        escapeHtml(r.typeSport) +
        "</td><td>" +
        escapeHtml(r.niveau) +
        "</td><td>" +
        escapeHtml(r.intensite) +
        "</td><td>" +
        escapeHtml(r.dateSeance) +
        "</td><td>" +
        escapeHtml(r.dureeMin) +
        "</td><td>" +
        escapeHtml(r.statut) +
        '</td><td class="' +
        ACTIONS_CLASS +
        '">' +
        '<button type="button" class="action-btn" data-act="edit" data-id="' +
        r.id +
        '">✏️</button> ' +
        '<button type="button" class="action-btn" data-act="del" data-id="' +
        r.id +
        '">🗑️</button>' +
        "</td>";
      tb.appendChild(tr);
    });
    refreshFkSelects();
  }

  function escapeHtml(s) {
    if (s == null) return "";
    var d = document.createElement("div");
    d.textContent = s;
    return d.innerHTML;
  }

  function refreshFkSelects() {
    var plans = NutriSmartCRUD.planRepas.list();
    if (entityEnabled("repas")) {
      fillSelect(
        $("repas-id-plan"),
        plans,
        "id",
        function (p) {
          return "#" + p.id + " — " + (p.objectif || "sans titre");
        },
        "Choisir un plan"
      );
    }
    if (entityEnabled("programmeSportif")) {
      fillSelect(
        $("prog-id-plan"),
        plans,
        "id",
        function (p) {
          return "#" + p.id + " — " + (p.objectif || "sans titre");
        },
        "Choisir un plan"
      );
    }
  }

  var LEGACY_SELECT_IDS = {
    programmeSportif: ["prog-type-sport", "prog-niveau", "prog-intensite", "prog-statut-seance"],
    planRepas: ["pr-statut"],
  };

  function stripLegacySelectsForForm(name) {
    var ids = LEGACY_SELECT_IDS[name];
    if (!ids) return;
    ids.forEach(function (id) {
      var sel = $(id);
      if (!sel || sel.tagName !== "SELECT") return;
      sel.querySelectorAll("option[data-legacy]").forEach(function (o) {
        o.remove();
      });
    });
  }

  /** Valeur en base absente de la liste → option temporaire pour l’édition (ou champ texte / hidden). */
  function ensureSelectValue(selectId, value) {
    var sel = $(selectId);
    if (!sel) return;
    var v = String(value == null ? "" : value);
    if (sel.tagName === "INPUT" || sel.tagName === "TEXTAREA") {
      sel.value = v;
      return;
    }
    if (sel.tagName !== "SELECT") return;
    sel.querySelectorAll("option[data-legacy]").forEach(function (o) {
      o.remove();
    });
    if (v === "") {
      sel.selectedIndex = 0;
      return;
    }
    var has = Array.prototype.some.call(sel.options, function (o) {
      return o.value === v;
    });
    if (!has) {
      var o = document.createElement("option");
      o.value = v;
      o.textContent = v;
      o.setAttribute("data-legacy", "1");
      sel.appendChild(o);
    }
    sel.value = v;
  }

  function resetForm(name) {
    editing[name] = null;
    stripLegacySelectsForForm(name);
    var form = $("form-" + name);
    if (form) {
      form.reset();
      if (typeof window !== "undefined" && window.NutriSmartSaisieCrudUi) {
        window.NutriSmartSaisieCrudUi.clearFieldErrors(form);
      }
    }
    setEditingHint(name, null);
  }

  function setEditingHint(name, id) {
    var hint = $("hint-" + name);
    if (hint) {
      hint.textContent = id ? "Modification de l’enregistrement #" + id : "";
    }
  }

  function switchTab(name) {
    rootEl().querySelectorAll(TAB_SEL).forEach(function (btn) {
      var on = btn.getAttribute("data-tab") === name;
      btn.classList.toggle("is-active", on);
      btn.setAttribute("aria-selected", on ? "true" : "false");
    });
    rootEl().querySelectorAll(PANEL_SEL).forEach(function (panel) {
      panel.hidden = panel.getAttribute("data-panel") !== name;
    });
    if (typeof window !== "undefined" && window.NutriSmartSaisieCrudUi) {
      window.NutriSmartSaisieCrudUi.clearFieldErrors(rootEl());
    }
  }

  function bindTableActions(tbodyId, entity, onRefresh) {
    var tb = $(tbodyId);
    if (!tb) return;
    tb.addEventListener("click", function (e) {
      var btn = e.target.closest("button[data-act]");
      if (!btn) return;
      var id = btn.getAttribute("data-id");
      var act = btn.getAttribute("data-act");
      if (act === "del") {
        if (!confirm("Supprimer cet enregistrement ?")) return;
        Promise.resolve(NutriSmartCRUD[entity].delete(id)).then(function (delRes) {
          if (delRes && delRes.error) {
            showMsg(delRes.error, true);
            return;
          }
          showMsg("Supprimé.");
          onRefresh();
          renderAll();
        });
        return;
      }
      if (act === "edit") {
        var row = NutriSmartCRUD[entity].get(id);
        if (!row) return;
        editing[entity] = id;
        setEditingHint(entity, id);
        if (entity === "planRepas") {
          $("pr-id-utilisateur").value = row.idUtilisateur || "";
          $("pr-date-debut").value = row.dateDebut || "";
          $("pr-date-fin").value = row.dateFin || "";
          var o0 = row.objectif || "";
          $("pr-objectif").value =
            window.NutriSmartSaisieCrud && window.NutriSmartSaisieCrud.sanitizePlanObjectifLetters
              ? window.NutriSmartSaisieCrud.sanitizePlanObjectifLetters(o0)
              : o0;
          ensureSelectValue("pr-statut", row.statut || "");
        } else if (entity === "repas") {
          $("repas-id-plan").value = row.idPlan || "";
          $("repas-id-recette").value = row.idRecette || "";
          $("repas-type").value = row.type || "";
          $("repas-calories").value = row.calories || "";
        } else if (entity === "programmeSportif") {
          $("prog-id-plan").value = row.idPlan || "";
          var fixeAct =
            typeof document !== "undefined" && document.documentElement.getAttribute("data-ns-activite-fixe");
          fixeAct = fixeAct ? String(fixeAct).trim() : "";
          ensureSelectValue("prog-type-sport", fixeAct || row.typeSport || "");
          ensureSelectValue("prog-niveau", row.niveau || "");
          ensureSelectValue("prog-intensite", row.intensite || "");
          if ($("prog-date-seance")) $("prog-date-seance").value = row.dateSeance || "";
          if ($("prog-duree-min")) $("prog-duree-min").value = row.dureeMin || "";
          ensureSelectValue("prog-statut-seance", row.statut || "");
        }
        switchTab(entity);
      }
    });
  }

  function renderAll() {
    if (entityEnabled("planRepas")) renderPlanRepas();
    if (entityEnabled("repas")) renderRepas();
    if (entityEnabled("programmeSportif")) renderProgrammes();
  }

  document.addEventListener("DOMContentLoaded", function () {
    function boot() {
      showMsg("", false);
      if (!NutriSmartCRUD.isApi || !NutriSmartCRUD.isApi()) {
        NutriSmartCRUD.seedDemo();
      }
      renderAll();
    }

    if (NutriSmartCRUD.init) {
      NutriSmartCRUD.init().then(function (ok) {
        if (!ok && typeof window.NUTRISMART_API_BASE === "string" && window.NUTRISMART_API_BASE) {
          showMsg("API indisponible — mode navigateur (localStorage).", true);
        }
        boot();
      });
    } else {
      boot();
    }

    rootEl().querySelectorAll(TAB_SEL).forEach(function (btn) {
      btn.addEventListener("click", function () {
        switchTab(btn.getAttribute("data-tab"));
      });
    });

    if (entityEnabled("planRepas")) bindTableActions("tbody-plan-repas", "planRepas", renderAll);
    if (entityEnabled("repas")) bindTableActions("tbody-repas", "repas", renderAll);
    if (entityEnabled("programmeSportif")) bindTableActions("tbody-programme", "programmeSportif", renderAll);

    if (typeof window !== "undefined" && window.NutriSmartSaisieCrudUi) {
      ["form-planRepas", "form-repas", "form-programmeSportif"].forEach(function (fid) {
        var f = $(fid);
        if (f) window.NutriSmartSaisieCrudUi.attachLiveClear(f);
      });
    }

    var fp = $("form-planRepas");
    if (fp) fp.addEventListener("submit", function (e) {
      e.preventDefault();
      var payload = {
        idUtilisateur: $("pr-id-utilisateur").value.trim(),
        dateDebut: $("pr-date-debut").value,
        dateFin: $("pr-date-fin").value,
        objectif: $("pr-objectif").value.trim(),
        statut: $("pr-statut").value.trim(),
      };
      if (typeof window !== "undefined" && window.NutriSmartSaisieCrud && window.NutriSmartSaisieCrudUi) {
        var fePlan = window.NutriSmartSaisieCrud.planRepasFieldErrors(payload);
        if (fePlan.length) {
          showMsg("Veuillez corriger les champs surlignés en rouge.", true);
          window.NutriSmartSaisieCrudUi.showFieldErrors(fp, fePlan);
          return;
        }
        window.NutriSmartSaisieCrudUi.clearFieldErrors(fp);
      }
      var op = editing.planRepas
        ? NutriSmartCRUD.planRepas.update(editing.planRepas, payload)
        : NutriSmartCRUD.planRepas.create(payload);
      Promise.resolve(op).then(function (res) {
        if (res && res.error) {
          var apiE =
            window.NutriSmartSaisieCrud && window.NutriSmartSaisieCrud.apiErrorsForPlanRepas
              ? window.NutriSmartSaisieCrud.apiErrorsForPlanRepas(res.error)
              : [];
          if (apiE.length && window.NutriSmartSaisieCrudUi) {
            showMsg("", false);
            window.NutriSmartSaisieCrudUi.showFieldErrors(fp, apiE);
          } else {
            showMsg(res.error, true);
          }
          return;
        }
        if (window.NutriSmartSaisieCrudUi) window.NutriSmartSaisieCrudUi.clearFieldErrors(fp);
        showMsg(editing.planRepas ? "Plan repas mis à jour." : "Plan repas créé.");
        resetForm("planRepas");
        renderAll();
      });
    });

    var fr = $("form-repas");
    if (fr) fr.addEventListener("submit", function (e) {
      e.preventDefault();
      var payload = {
        idPlan: $("repas-id-plan").value,
        idRecette: $("repas-id-recette").value.trim(),
        type: $("repas-type").value.trim(),
        calories: $("repas-calories").value.trim(),
      };
      if (typeof window !== "undefined" && window.NutriSmartSaisieCrud && window.NutriSmartSaisieCrudUi) {
        var feRepas = window.NutriSmartSaisieCrud.repasFieldErrors(payload);
        if (feRepas.length) {
          showMsg("Veuillez corriger les champs surlignés en rouge.", true);
          window.NutriSmartSaisieCrudUi.showFieldErrors(fr, feRepas);
          return;
        }
        window.NutriSmartSaisieCrudUi.clearFieldErrors(fr);
      }
      var resPromise = editing.repas
        ? NutriSmartCRUD.repas.update(editing.repas, payload)
        : NutriSmartCRUD.repas.create(payload);
      Promise.resolve(resPromise).then(function (res) {
        if (res && res.error) {
          var apiR =
            window.NutriSmartSaisieCrud && window.NutriSmartSaisieCrud.apiErrorsForRepas
              ? window.NutriSmartSaisieCrud.apiErrorsForRepas(res.error)
              : [];
          if (apiR.length && window.NutriSmartSaisieCrudUi) {
            showMsg("", false);
            window.NutriSmartSaisieCrudUi.showFieldErrors(fr, apiR);
          } else {
            showMsg(res.error, true);
          }
          return;
        }
        if (window.NutriSmartSaisieCrudUi) window.NutriSmartSaisieCrudUi.clearFieldErrors(fr);
        showMsg(editing.repas ? "Repas mis à jour." : "Repas ajouté.");
        resetForm("repas");
        renderAll();
      });
    });

    var fprog = $("form-programmeSportif");
    if (fprog) fprog.addEventListener("submit", function (e) {
      e.preventDefault();
      var fixeAct =
        typeof document !== "undefined" && document.documentElement.getAttribute("data-ns-activite-fixe");
      fixeAct = fixeAct ? String(fixeAct).trim() : "";
      var typeSportVal = $("prog-type-sport") ? $("prog-type-sport").value.trim() : "";
      if (fixeAct) typeSportVal = fixeAct;
      var payload = {
        idPlan: $("prog-id-plan").value,
        typeSport: typeSportVal,
        niveau: $("prog-niveau").value.trim(),
        intensite: $("prog-intensite").value.trim(),
        dateSeance: $("prog-date-seance") ? $("prog-date-seance").value : "",
        dureeMin: $("prog-duree-min") ? $("prog-duree-min").value.trim() : "",
        statut: $("prog-statut-seance") ? $("prog-statut-seance").value.trim() || "prevue" : "prevue",
      };
      if (typeof window !== "undefined" && window.NutriSmartSaisieCrud && window.NutriSmartSaisieCrudUi) {
        var feProg = window.NutriSmartSaisieCrud.programmeSportifFieldErrors(payload);
        if (feProg.length) {
          showMsg("Veuillez corriger les champs surlignés en rouge.", true);
          window.NutriSmartSaisieCrudUi.showFieldErrors(fprog, feProg);
          return;
        }
        window.NutriSmartSaisieCrudUi.clearFieldErrors(fprog);
      }
      var resPromise = editing.programmeSportif
        ? NutriSmartCRUD.programmeSportif.update(editing.programmeSportif, payload)
        : NutriSmartCRUD.programmeSportif.create(payload);
      Promise.resolve(resPromise).then(function (res) {
        if (res && res.error) {
          var apiP =
            window.NutriSmartSaisieCrud && window.NutriSmartSaisieCrud.apiErrorsForProgramme
              ? window.NutriSmartSaisieCrud.apiErrorsForProgramme(res.error)
              : [];
          if (apiP.length && window.NutriSmartSaisieCrudUi) {
            showMsg("", false);
            window.NutriSmartSaisieCrudUi.showFieldErrors(fprog, apiP);
          } else {
            showMsg(res.error, true);
          }
          return;
        }
        if (window.NutriSmartSaisieCrudUi) window.NutriSmartSaisieCrudUi.clearFieldErrors(fprog);
        showMsg(editing.programmeSportif ? "Programme mis à jour." : "Programme créé.");
        resetForm("programmeSportif");
        renderAll();
      });
    });

    rootEl().querySelectorAll("[data-reset-form]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        resetForm(btn.getAttribute("data-reset-form"));
        showMsg("");
        if (typeof window !== "undefined" && window.NutriSmartSaisieCrudUi) {
          window.NutriSmartSaisieCrudUi.clearFieldErrors(rootEl());
        }
      });
    });

  });
})();
