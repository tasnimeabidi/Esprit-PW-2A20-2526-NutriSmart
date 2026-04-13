<?php
include_once '../../controllers/SuiviController.php';
$controller = new SuiviController();
$logs = $controller->listLogs();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>NutriSmart — Admin Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg:      #0d1210;
    --surface: #141a16;
    --card:    #1a2420;
    --border:  #253028;
    --primary: #3dba52;
    --lime:    #8bc34a;
    --gold:    #f9a825;
    --red:     #e53935;
    --blue:    #29b6f6;
    --purple:  #ab47bc;
    --white:   #e8f0e9;
    --muted:   #5a7060;
    --mono:    'Space Mono', monospace;
    --sans:    'Syne', sans-serif;
  }

  html, body { height: 100%; }

  body {
    font-family: var(--sans);
    background: var(--bg);
    color: var(--white);
    display: grid;
    grid-template-columns: 240px 1fr;
    grid-template-rows: 56px 1fr;
    min-height: 100vh;
  }

  /* ── TOPBAR ── */
  .topbar {
    grid-column: 1 / -1;
    background: var(--surface);
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 1.5rem 0 1.5rem;
    position: sticky; top: 0; z-index: 50;
  }

  .topbar-logo {
    display: flex; align-items: center; gap: .6rem;
    font-family: var(--sans); font-size: 1.1rem; font-weight: 800;
    color: var(--white); text-decoration: none; letter-spacing: -.01em;
  }
  .logo-dot {
    width: 28px; height: 28px; border-radius: 6px;
    background: var(--primary); display: grid; place-items: center;
    font-size: .85rem;
  }
  .logo-badge {
    font-size: .65rem; font-weight: 700; background: rgba(61,186,82,.15);
    color: var(--primary); padding: .1rem .4rem; border-radius: .25rem;
    letter-spacing: .05em;
  }

  .topbar-right {
    display: flex; align-items: center; gap: 1.2rem;
  }
  .notif-btn {
    width: 34px; height: 34px; border-radius: 8px;
    background: var(--card); border: 1px solid var(--border);
    display: grid; place-items: center; cursor: pointer;
    font-size: .9rem; transition: border-color .2s; position: relative;
  }
  .notif-btn:hover { border-color: var(--primary); }
  .notif-dot {
    position: absolute; top: 6px; right: 6px; width: 6px; height: 6px;
    background: var(--gold); border-radius: 50%;
  }

  .admin-avatar {
    display: flex; align-items: center; gap: .6rem; cursor: pointer;
  }
  .avatar-img {
    width: 32px; height: 32px; border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), var(--lime));
    display: grid; place-items: center; font-size: .85rem; font-weight: 700;
    color: var(--bg);
  }
  .admin-info { line-height: 1.2; }
  .admin-name { font-size: .82rem; font-weight: 700; }
  .admin-role { font-size: .68rem; color: var(--muted); }

  /* ── SIDEBAR ── */
  aside {
    background: var(--surface);
    border-right: 1px solid var(--border);
    padding: 1.2rem .75rem;
    display: flex; flex-direction: column; gap: .2rem;
    overflow-y: auto;
  }

  .nav-section-label {
    font-size: .62rem; font-weight: 700; letter-spacing: .1em;
    text-transform: uppercase; color: var(--muted);
    padding: .8rem .6rem .3rem; margin-top: .4rem;
  }

  .nav-item {
    display: flex; align-items: center; gap: .7rem;
    padding: .55rem .8rem; border-radius: .5rem;
    font-size: .83rem; font-weight: 600; color: var(--muted);
    cursor: pointer; text-decoration: none; transition: all .15s;
    border: 1px solid transparent;
  }
  .nav-item:hover { background: var(--card); color: var(--white); }
  .nav-item.active {
    background: rgba(61,186,82,.12); color: var(--primary);
    border-color: rgba(61,186,82,.2);
  }
  .nav-icon { width: 18px; text-align: center; font-size: .95rem; }
  .nav-badge {
    margin-left: auto; font-size: .62rem; font-weight: 700;
    background: var(--primary); color: var(--bg);
    padding: .1rem .4rem; border-radius: .25rem; font-family: var(--mono);
  }
  .nav-badge.warn { background: var(--gold); color: var(--bg); }

  /* ── MAIN ── */
  main {
    overflow-y: auto; padding: 1.8rem 2rem;
    display: flex; flex-direction: column; gap: 1.5rem;
  }

  /* ── PAGE HEADER ── */
  .page-header {
    display: flex; align-items: flex-start; justify-content: space-between;
    flex-wrap: wrap; gap: 1rem;
  }
  .page-title { font-size: 1.5rem; font-weight: 800; color: var(--white); }
  .page-sub { font-size: .82rem; color: var(--muted); margin-top: .2rem; font-family: var(--mono); }

  .header-actions { display: flex; gap: .7rem; }
  .btn-sm {
    padding: .45rem 1.1rem; border-radius: .5rem; font-size: .8rem;
    font-weight: 700; cursor: pointer; border: none; transition: all .2s;
    font-family: var(--sans);
  }
  .btn-ghost {
    background: var(--card); color: var(--white); border: 1px solid var(--border);
  }
  .btn-ghost:hover { border-color: var(--primary); color: var(--primary); }
  .btn-green { background: var(--primary); color: var(--bg); }
  .btn-green:hover { opacity: .88; }

  /* ── KPI GRID ── */
  .kpi-grid {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;
  }

  .kpi-card {
    background: var(--card); border: 1px solid var(--border);
    border-radius: .9rem; padding: 1.2rem 1.3rem;
    display: flex; flex-direction: column; gap: .7rem;
    transition: border-color .2s;
  }
  .kpi-card:hover { border-color: var(--primary); }

  .kpi-top { display: flex; align-items: center; justify-content: space-between; }
  .kpi-label { font-size: .72rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .07em; }
  .kpi-icon {
    width: 32px; height: 32px; border-radius: .5rem;
    display: grid; place-items: center; font-size: .9rem;
  }

  .kpi-val {
    font-family: var(--mono); font-size: 1.9rem; font-weight: 700; color: var(--white);
    line-height: 1;
  }
  .kpi-trend {
    display: flex; align-items: center; gap: .3rem;
    font-size: .72rem; font-weight: 700;
  }
  .kpi-trend.up { color: var(--primary); }
  .kpi-trend.down { color: var(--red); }

  /* ── MAIN GRID ── */
  .main-grid {
    display: grid; grid-template-columns: 2fr 1fr; gap: 1.2rem;
  }

  /* ── CHART CARD ── */
  .chart-card {
    background: var(--card); border: 1px solid var(--border); border-radius: .9rem;
    padding: 1.3rem;
  }
  .card-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 1.2rem;
  }
  .card-title { font-size: .9rem; font-weight: 700; }
  .card-sub { font-size: .72rem; color: var(--muted); margin-top: .1rem; font-family: var(--mono); }

  .chart-tabs { display: flex; gap: .3rem; }
  .chart-tab {
    font-size: .7rem; font-weight: 700; padding: .25rem .65rem;
    border-radius: .3rem; cursor: pointer; background: var(--border); color: var(--muted);
    border: none; font-family: var(--sans);
  }
  .chart-tab.active { background: rgba(61,186,82,.15); color: var(--primary); }

  /* SVG bar chart */
  .bar-chart { width: 100%; height: 160px; overflow: visible; }

  /* ── RECENT USERS ── */
  .users-table { width: 100%; border-collapse: collapse; }
  .users-table th {
    font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .07em;
    color: var(--muted); text-align: left; padding: .5rem .6rem;
    border-bottom: 1px solid var(--border);
  }
  .users-table td {
    padding: .7rem .6rem; font-size: .82rem;
    border-bottom: 1px solid rgba(37,48,40,.6);
  }
  .users-table tr:last-child td { border-bottom: none; }
  .users-table tr:hover td { background: rgba(61,186,82,.03); }

  .user-cell { display: flex; align-items: center; gap: .6rem; }
  .u-avatar {
    width: 28px; height: 28px; border-radius: 50%; display: grid; place-items: center;
    font-size: .7rem; font-weight: 700; color: var(--bg); flex-shrink: 0;
  }
  .u-name { font-weight: 600; font-size: .82rem; }
  .u-email { font-size: .7rem; color: var(--muted); font-family: var(--mono); }

  .status-pill {
    font-size: .65rem; font-weight: 700; padding: .2rem .6rem; border-radius: 2rem;
    letter-spacing: .04em;
  }
  .s-active { background: rgba(61,186,82,.15); color: var(--primary); }
  .s-inactive { background: rgba(229,57,53,.12); color: #ef5350; }
  .s-pending { background: rgba(249,168,37,.12); color: var(--gold); }

  /* ── SIDE PANELS ── */
  .side-panels { display: flex; flex-direction: column; gap: 1.2rem; }

  .mini-card {
    background: var(--card); border: 1px solid var(--border); border-radius: .9rem;
    padding: 1.2rem;
  }

  /* Modules usage donut-style */
  .module-usage { display: flex; flex-direction: column; gap: .65rem; margin-top: .8rem; }
  .module-info { display: flex; justify-content: space-between; font-size: .78rem; margin-bottom: .3rem; }
  .module-name { font-weight: 600; }
  .module-pct { font-family: var(--mono); color: var(--muted); }
  .prog-bar { height: 5px; background: var(--border); border-radius: 3px; overflow: hidden; }
  .prog-fill { height: 100%; border-radius: 3px; transition: width .6s ease; }

  /* Activity feed */
  .activity-list { display: flex; flex-direction: column; gap: 0; margin-top: .6rem; }
  .activity-item {
    display: flex; gap: .8rem; padding: .7rem 0;
    border-bottom: 1px solid rgba(37,48,40,.6);
  }
  .activity-item:last-child { border-bottom: none; }
  .act-dot {
    width: 8px; height: 8px; border-radius: 50%;
    flex-shrink: 0; margin-top: .35rem;
  }
  .act-text { font-size: .78rem; line-height: 1.5; }
  .act-time { font-size: .68rem; color: var(--muted); font-family: var(--mono); }

  /* ── BOTTOM GRID ── */
  .bottom-grid {
    display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.2rem;
  }

  /* Top foods table */
  .food-row {
    display: flex; align-items: center; gap: .8rem;
    padding: .6rem 0; border-bottom: 1px solid rgba(37,48,40,.6);
    font-size: .8rem;
  }
  .food-row:last-child { border-bottom: none; }
  .food-rank {
    width: 22px; height: 22px; border-radius: .3rem;
    background: var(--border); display: grid; place-items: center;
    font-family: var(--mono); font-size: .7rem; font-weight: 700; flex-shrink: 0;
  }
  .food-name { flex: 1; font-weight: 600; }
  .food-count { font-family: var(--mono); font-size: .72rem; color: var(--muted); }

  /* Grocery stats */
  .budget-display {
    text-align: center; padding: 1rem 0;
  }
  .budget-big {
    font-family: var(--mono); font-size: 2.4rem; font-weight: 700; color: var(--gold);
  }
  .budget-sub { font-size: .72rem; color: var(--muted); margin-top: .2rem; }
  .budget-progress {
    margin-top: 1rem; height: 8px; background: var(--border); border-radius: 4px; overflow: hidden;
  }
  .budget-fill { height: 100%; border-radius: 4px; background: linear-gradient(90deg, var(--gold), #f57f17); width: 68%; }

  /* Quick actions */
  .quick-actions { display: grid; grid-template-columns: 1fr 1fr; gap: .6rem; margin-top: .8rem; }
  .qa-btn {
    background: var(--border); border: 1px solid transparent;
    border-radius: .6rem; padding: .8rem; text-align: center;
    cursor: pointer; transition: all .2s; font-family: var(--sans);
    color: var(--white);
  }
  .qa-btn:hover { border-color: var(--primary); background: rgba(61,186,82,.08); }
  .qa-icon { font-size: 1.2rem; margin-bottom: .3rem; }
  .qa-label { font-size: .72rem; font-weight: 600; color: var(--muted); }

  /* ── SCROLLBAR ── */
  ::-webkit-scrollbar { width: 5px; height: 5px; }
  ::-webkit-scrollbar-track { background: transparent; }
  ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

  /* ── ANIMATIONS ── */
  @keyframes fadeIn { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:none} }
  .kpi-card { animation: fadeIn .4s ease both; }
  .kpi-card:nth-child(1){animation-delay:.05s}
  .kpi-card:nth-child(2){animation-delay:.1s}
  .kpi-card:nth-child(3){animation-delay:.15s}
  .kpi-card:nth-child(4){animation-delay:.2s}

  /* ── RESPONSIVE ── */
  @media (max-width: 1100px) {
    body { grid-template-columns: 200px 1fr; }
    .kpi-grid { grid-template-columns: repeat(2, 1fr); }
    .bottom-grid { grid-template-columns: 1fr 1fr; }
  }
  @media (max-width: 800px) {
    body { grid-template-columns: 1fr; }
    aside { display: none; }
    .main-grid { grid-template-columns: 1fr; }
    .bottom-grid { grid-template-columns: 1fr; }
    main { padding: 1.2rem; }
  }
</style>
</head>
<body>

<!-- TOPBAR -->
<header class="topbar">
  <a href="nutrismart-website.html" class="topbar-logo">
    <div class="logo-dot">🍎</div>
    NutriSmart
    <span class="logo-badge">ADMIN</span>
  </a>

  <div class="topbar-right">
    <div class="notif-btn" title="Notifications">
      🔔
      <div class="notif-dot"></div>
    </div>
    <div class="notif-btn" title="Paramètres">⚙️</div>
    <div class="admin-avatar">
      <div class="avatar-img">A</div>
      <div class="admin-info">
        <div class="admin-name">Admin Principal</div>
        <div class="admin-role">Super Administrator</div>
      </div>
    </div>
  </div>
</header>

<script>
  function showView(viewId, element) {
    // Hide all views
    document.querySelectorAll('.content-view').forEach(v => v.style.display = 'none');
    // Show selected view
    document.getElementById(viewId).style.display = 'flex';
    
    // Update active state in sidebar
    document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
    if(element) element.classList.add('active');
    else {
        // Find element by viewId if not provided (for reload)
        const items = document.querySelectorAll('.nav-item');
        items.forEach(item => {
            if(item.getAttribute('onclick') && item.getAttribute('onclick').includes(viewId)) {
                item.classList.add('active');
            }
        });
    }

    // Save state
    localStorage.setItem('activeView', viewId);
  }

  // Restore state on reload
  window.addEventListener('DOMContentLoaded', () => {
    const savedView = localStorage.getItem('activeView') || 'view-welcome';
    showView(savedView, null);
  });
</script>

<!-- SIDEBAR -->
<aside>
  <div style="padding: 1.2rem; border-bottom: 1px solid rgba(255,255,255,0.05); margin-bottom: 1rem;">
    <a href="../frontoffice/nutrismart-website.html" style="color: var(--lime); text-decoration: none; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; gap: 0.6rem;">
      <span style="font-size: 1.1rem;">←</span> Retour au site
    </a>
  </div>
  <div class="nav-section-label">Principal</div>
  <a class="nav-item active" href="#" onclick="showView('view-welcome', this)">
    <span class="nav-icon">🏠</span> Accueil Admin
  </a>
  <a class="nav-item" href="#" onclick="showView('view-dashboard', this)">
    <span class="nav-icon">📊</span> Tableau de bord
  </a>

  <div class="nav-section-label">Modules NutriSmart</div>
  <a class="nav-item" href="users.html">
    <span class="nav-icon">👥</span> Utilisateurs
  </a>
  <a class="nav-item" href="aliment.php">
    <span class="nav-icon">🥗</span> Aliments
  </a>
  <a class="nav-item" href="planRepas.html">
    <span class="nav-icon">📅</span> Plans de repas
  </a>
  <a class="nav-item" href="#" onclick="showView('view-suivi', this)">
    <span class="nav-icon">📈</span> Suivi et Statistiques
  </a>
  <a class="nav-item" href="#">
    <span class="nav-icon">🛒</span> Courses & Budget
  </a>
  <a class="nav-item" href="recettes.php">
    <span class="nav-icon">📖</span> Recettes
  </a>

  <div class="nav-section-label">Système & Logs</div>
  <a class="nav-item" href="#">
    <span class="nav-icon">📤</span> Exports
  </a>
  <a class="nav-item" href="#">
    <span class="nav-icon">🔒</span> Permissions
  </a>
  <a class="nav-item" href="#">
    <span class="nav-icon">📋</span> Logs d'activité
  </a>
  <a class="nav-item" href="#">
    <span class="nav-icon">⚙️</span> Paramètres
  </a>

  <div style="margin-top:auto;padding-top:1.5rem">
    <a class="nav-item" href="../frontoffice/nutrismart-website.html" style="color:var(--primary)">
      <span class="nav-icon">🌐</span> Voir le site
    </a>
  </div>
</aside>

  <main class="main dash-workspace">
    <div id="view-welcome" class="content-view" style="display: flex; flex-direction: column; gap: 1.5rem;">
      <div class="page-header">
        <div>
          <h1 class="page-title">Bienvenue sur NutriSmart Admin</h1>
          <p class="page-sub">G�rez vos modules et suivez les statistiques en temps r�el.</p>
        </div>
      </div>
      <div class="kpi-grid">
        <div class="kpi-card">
          <div class="kpi-top"><span class="kpi-label">Utilisateurs</span><div class="kpi-icon" style="background:rgba(61,186,82,.1);color:var(--primary)">??</div></div>
          <div class="kpi-val">284</div>
        </div>
        <div class="kpi-card">
          <div class="kpi-top"><span class="kpi-label">Recettes</span><div class="kpi-icon" style="background:rgba(255,167,38,.1);color:var(--gold)">??</div></div>
          <div class="kpi-val">12</div>
        </div>
      </div>
    </div>
    <div id="view-dashboard" class="content-view" style="display: none;">
      <!-- Dashboard content placeholder -->
      <h2 class="serif">Tableau de Bord D�taill�</h2>
    </div>
    <div id="view-suivi" class="content-view" style="display: none;">
      <!-- Suivi content placeholder -->
      <h2 class="serif">Suivi et Statistiques</h2>
    </div>
  </main>
</body>
</html>
