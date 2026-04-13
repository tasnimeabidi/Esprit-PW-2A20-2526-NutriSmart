/**
 * Modèle CRUD : localStorage OU API PHP (MySQL) si NUTRISMART_API_BASE est défini.
 * MCD : PlanRepas 1—N Repas, PlanRepas 1—N ProgrammeSportif (date / durée / statut sur la même ligne).
 */
(function (global) {
  "use strict";

  var P = "nutrismart_crud_";
  var K = {
    planRepas: P + "planRepas",
    repas: P + "repas",
    programmeSportif: P + "programmeSportif",
  };

  var useApi = false;
  var mem = {
    planRepas: [],
    repas: [],
    programmeSportif: [],
  };

  function apiRoot() {
    if (typeof global.NUTRISMART_API_BASE === "string" && global.NUTRISMART_API_BASE) {
      return global.NUTRISMART_API_BASE.replace(/\/$/, "");
    }
    return "";
  }

  function reloadFromApi() {
    var root = apiRoot();
    if (!root) return Promise.resolve();
    return Promise.all([
      fetch(root + "/plan-repas.php").then(function (r) {
        return r.json();
      }),
      fetch(root + "/repas.php").then(function (r) {
        return r.json();
      }),
      fetch(root + "/programme-sportif.php").then(function (r) {
        return r.json();
      }),
    ]).then(function (parts) {
      mem.planRepas = parts[0] || [];
      mem.repas = parts[1] || [];
      mem.programmeSportif = parts[2] || [];
    });
  }

  function parseApi(res) {
    return res.json().then(function (j) {
      if (!res.ok) {
        return { error: j && j.error ? j.error : "Erreur " + res.status };
      }
      return j;
    });
  }

  function read(name) {
    if (useApi) {
      return mem[name] ? mem[name].slice() : [];
    }
    try {
      var raw = localStorage.getItem(K[name]);
      return raw ? JSON.parse(raw) : [];
    } catch (e) {
      return [];
    }
  }

  function write(name, arr) {
    if (useApi) {
      mem[name] = arr.slice();
      return;
    }
    localStorage.setItem(K[name], JSON.stringify(arr));
  }

  function nextId(arr) {
    if (!arr.length) return 1;
    return (
      Math.max.apply(
        null,
        arr.map(function (x) {
          return parseInt(String(x.id), 10) || 0;
        })
      ) + 1
    );
  }

  /** Même règle que le formulaire : objectif = lettres uniquement (+ espaces, tiret, apostrophe). */
  function planRepasObjectifValide(objectif) {
    var t = objectif == null ? "" : String(objectif).trim();
    if (t === "") {
      return { error: "L'objectif est obligatoire." };
    }
    if (t.length > 255) {
      return { error: "L'objectif ne peut pas dépasser 255 caractères." };
    }
    if (!/^[\p{L}\s'’\-]+$/u.test(t)) {
      return {
        error:
          "L'objectif doit être du texte uniquement : lettres, espaces, tiret ou apostrophe. Pas de chiffres ni de symboles (. ? / …).",
      };
    }
    return { value: t };
  }

  var Store = {
    isApi: function () {
      return useApi;
    },

    init: function () {
      var root = apiRoot();
      if (!root) {
        useApi = false;
        return Promise.resolve(false);
      }
      return fetch(root + "/health.php")
        .then(function (r) {
          return r.json().then(function (j) {
            if (!r.ok || !j || j.ok !== true || j.database !== true) {
              throw new Error("health");
            }
            return j;
          });
        })
        .then(function () {
          return reloadFromApi();
        })
        .then(function () {
          useApi = true;
          return true;
        })
        .catch(function () {
          useApi = false;
          return false;
        });
    },

    planRepas: {
      list: function () {
        return read("planRepas");
      },
      get: function (id) {
        return read("planRepas").find(function (p) {
          return String(p.id) === String(id);
        });
      },
      create: function (row) {
        if (useApi) {
          return fetch(apiRoot() + "/plan-repas.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(row),
          })
            .then(parseApi)
            .then(function (j) {
              if (j && j.error) return j;
              return reloadFromApi().then(function () {
                return j;
              });
            });
        }
        var arr = read("planRepas");
        var chkObj = planRepasObjectifValide(row.objectif);
        if (chkObj.error) {
          return { error: chkObj.error };
        }
        var r = {
          id: nextId(arr),
          idUtilisateur: String(row.idUtilisateur || ""),
          dateDebut: row.dateDebut || "",
          dateFin: row.dateFin || "",
          objectif: chkObj.value,
          statut: row.statut || "brouillon",
        };
        arr.push(r);
        write("planRepas", arr);
        return r;
      },
      update: function (id, patch) {
        if (useApi) {
          return fetch(apiRoot() + "/plan-repas.php?id=" + encodeURIComponent(id), {
            method: "PUT",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(patch),
          })
            .then(parseApi)
            .then(function (j) {
              if (j && j.error) return j;
              return reloadFromApi().then(function () {
                return j;
              });
            });
        }
        var arr = read("planRepas");
        var i = arr.findIndex(function (p) {
          return String(p.id) === String(id);
        });
        if (i === -1) return null;
        if (Object.prototype.hasOwnProperty.call(patch, "objectif")) {
          var chkUp = planRepasObjectifValide(patch.objectif);
          if (chkUp.error) {
            return { error: chkUp.error };
          }
          patch = Object.assign({}, patch, { objectif: chkUp.value });
        }
        Object.assign(arr[i], patch);
        write("planRepas", arr);
        return arr[i];
      },
      delete: function (id) {
        if (useApi) {
          return fetch(apiRoot() + "/plan-repas.php?id=" + encodeURIComponent(id), {
            method: "DELETE",
          })
            .then(parseApi)
            .then(function (j) {
              if (j && j.error) return j;
              return reloadFromApi().then(function () {
                return true;
              });
            });
        }
        id = String(id);
        write(
          "repas",
          read("repas").filter(function (x) {
            return String(x.idPlan) !== id;
          })
        );
        write(
          "programmeSportif",
          read("programmeSportif").filter(function (x) {
            return String(x.idPlan) !== id;
          })
        );
        write(
          "planRepas",
          read("planRepas").filter(function (p) {
            return String(p.id) !== id;
          })
        );
        return true;
      },
    },

    repas: {
      list: function () {
        return read("repas");
      },
      listByPlan: function (idPlan) {
        return read("repas").filter(function (x) {
          return String(x.idPlan) === String(idPlan);
        });
      },
      get: function (id) {
        return read("repas").find(function (r) {
          return String(r.id) === String(id);
        });
      },
      create: function (row) {
        if (useApi) {
          return fetch(apiRoot() + "/repas.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(row),
          })
            .then(parseApi)
            .then(function (j) {
              if (j && j.error) return j;
              return reloadFromApi().then(function () {
                return j;
              });
            });
        }
        if (!Store.planRepas.get(row.idPlan)) {
          return { error: "Plan repas introuvable" };
        }
        var arr = read("repas");
        var r = {
          id: nextId(arr),
          idPlan: String(row.idPlan),
          idRecette: row.idRecette != null ? String(row.idRecette) : "",
          type: row.type || "",
          calories: row.calories != null ? String(row.calories) : "",
        };
        arr.push(r);
        write("repas", arr);
        return r;
      },
      update: function (id, patch) {
        if (useApi) {
          return fetch(apiRoot() + "/repas.php?id=" + encodeURIComponent(id), {
            method: "PUT",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(patch),
          })
            .then(parseApi)
            .then(function (j) {
              if (j && j.error) return j;
              return reloadFromApi().then(function () {
                return j;
              });
            });
        }
        var arr = read("repas");
        var i = arr.findIndex(function (r) {
          return String(r.id) === String(id);
        });
        if (i === -1) return null;
        if (patch.idPlan != null && !Store.planRepas.get(patch.idPlan)) {
          return { error: "Plan repas introuvable" };
        }
        Object.assign(arr[i], patch);
        write("repas", arr);
        return arr[i];
      },
      delete: function (id) {
        if (useApi) {
          return fetch(apiRoot() + "/repas.php?id=" + encodeURIComponent(id), {
            method: "DELETE",
          })
            .then(parseApi)
            .then(function (j) {
              if (j && j.error) return j;
              return reloadFromApi().then(function () {
                return true;
              });
            });
        }
        write(
          "repas",
          read("repas").filter(function (r) {
            return String(r.id) !== String(id);
          })
        );
        return true;
      },
    },

    programmeSportif: {
      list: function () {
        return read("programmeSportif");
      },
      get: function (id) {
        return read("programmeSportif").find(function (p) {
          return String(p.id) === String(id);
        });
      },
      getByPlan: function (idPlan) {
        return read("programmeSportif").find(function (p) {
          return String(p.idPlan) === String(idPlan);
        });
      },
      create: function (row) {
        if (useApi) {
          return fetch(apiRoot() + "/programme-sportif.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(row),
          })
            .then(parseApi)
            .then(function (j) {
              if (j && j.error) return j;
              return reloadFromApi().then(function () {
                return j;
              });
            });
        }
        if (!Store.planRepas.get(row.idPlan)) {
          return { error: "Plan repas introuvable" };
        }
        var arr = read("programmeSportif");
        var r = {
          id: nextId(arr),
          idPlan: String(row.idPlan),
          typeSport: row.typeSport || "",
          niveau: row.niveau || "",
          intensite: row.intensite || "",
          dateSeance: row.dateSeance || "",
          dureeMin: row.dureeMin != null ? String(row.dureeMin) : "",
          statut: row.statut || "prevue",
        };
        arr.push(r);
        write("programmeSportif", arr);
        return r;
      },
      update: function (id, patch) {
        if (useApi) {
          return fetch(apiRoot() + "/programme-sportif.php?id=" + encodeURIComponent(id), {
            method: "PUT",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(patch),
          })
            .then(parseApi)
            .then(function (j) {
              if (j && j.error) return j;
              return reloadFromApi().then(function () {
                return j;
              });
            });
        }
        var arr = read("programmeSportif");
        var i = arr.findIndex(function (p) {
          return String(p.id) === String(id);
        });
        if (i === -1) return null;
        if (patch.idPlan != null && !Store.planRepas.get(patch.idPlan)) {
          return { error: "Plan repas introuvable" };
        }
        Object.assign(arr[i], patch);
        write("programmeSportif", arr);
        return arr[i];
      },
      delete: function (id) {
        if (useApi) {
          return fetch(apiRoot() + "/programme-sportif.php?id=" + encodeURIComponent(id), {
            method: "DELETE",
          })
            .then(parseApi)
            .then(function (j) {
              if (j && j.error) return j;
              return reloadFromApi().then(function () {
                return true;
              });
            });
        }
        id = String(id);
        write(
          "programmeSportif",
          read("programmeSportif").filter(function (p) {
            return String(p.id) !== id;
          })
        );
        return true;
      },
    },

    seedDemo: function () {
      if (useApi) return;
      if (read("planRepas").length) return;
      var p = Store.planRepas.create({
        idUtilisateur: "1",
        dateDebut: "2026-04-01",
        dateFin: "2026-04-30",
        objectif: "Perte de poids",
        statut: "actif",
      });
      if (p && p.error) return;
      Store.repas.create({
        idPlan: p.id,
        idRecette: "1",
        type: "Petit-déjeuner",
        calories: "420",
      });
      Store.programmeSportif.create({
        idPlan: p.id,
        typeSport: "Cardio",
        niveau: "intermédiaire",
        intensite: "modérée",
        dateSeance: "2026-04-09",
        dureeMin: "45",
        statut: "prevue",
      });
    },

    clearAll: function () {
      if (useApi) return;
      Object.keys(K).forEach(function (key) {
        localStorage.removeItem(K[key]);
      });
    },
  };

  global.NutriSmartCRUD = Store;
})(typeof window !== "undefined" ? window : this);
