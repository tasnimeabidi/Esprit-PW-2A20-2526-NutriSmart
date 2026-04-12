<?php
include_once __DIR__ . '/../../Models/config.php';
include_once __DIR__ . '/../../Models/Aliment.php';
$db = (new Database())->getConnection();
$stmt = (new Aliment($db))->readAll();
$aliments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NutriSmart — Recettes</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/shared-styles.css">
  <style>
    body { background:#f7f9f6; }
    .hero { text-align:center; padding:5rem 2rem 3rem; }
    .hero h1 { font-family:'Playfair Display',serif; font-size:clamp(2rem,5vw,3.5rem); color:#2D6A2D; }
    .hero h1 em { color:#F2994A; font-style:italic; display:block; }
    .hero p  { color:#7A7A7A; margin-top:.8rem; }
    .section-title { text-align:center; padding:2rem 1rem .5rem; font-family:'Playfair Display',serif; font-size:1.8rem; color:#2D6A2D; }
    .grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(270px,1fr)); gap:1.5rem; padding:1rem 5% 3rem; }
    .card { background:#fff; border-radius:1rem; box-shadow:0 4px 20px rgba(0,0,0,.07); overflow:hidden; transition:.3s; }
    .card:hover { transform:translateY(-5px); box-shadow:0 10px 30px rgba(0,0,0,.12); }
    .card img { width:100%; height:190px; object-fit:cover; }
    .card-body { padding:1.2rem; }
    .card-body h3 { font-family:'Playfair Display',serif; font-size:1.2rem; color:#2D6A2D; margin-bottom:.4rem; }
    .card-body p  { font-size:.88rem; color:#666; line-height:1.5; }
    .btn { display:inline-block; margin-top:.8rem; padding:.5rem 1.4rem; border-radius:2rem; background:#4CAF50; color:#fff; font-weight:600; font-size:.85rem; border:none; cursor:pointer; }
    .detail { display:none; margin-top:.8rem; font-size:.83rem; color:#444; border-top:1px solid #eee; padding-top:.8rem; }
    .detail h4 { color:#2D6A2D; margin:.5rem 0 .2rem; }
    .detail ul, .detail ol { margin-left:1.1rem; }
    .badge { display:inline-block; padding:.15rem .6rem; border-radius:1rem; font-size:.72rem; font-weight:700; background:#C4D4A8; color:#2D6A2D; margin-bottom:.5rem; }
    .macros { display:grid; grid-template-columns:1fr 1fr; gap:.4rem; margin:.6rem 0; }
    .macro { background:#f1f5f1; border-radius:.4rem; padding:.4rem; text-align:center; font-size:.78rem; color:#2D6A2D; font-weight:700; }
    .macro span { display:block; font-size:.65rem; color:#888; font-weight:400; }
    footer { text-align:center; padding:2rem; color:#999; font-size:.85rem; }
  </style>
</head>
<body>

<!-- NAV -->
<nav id="navbar">
  <a href="nutrismart-website.html" class="nav-logo">
    <svg width="30" height="30" viewBox="0 0 100 100" fill="none" style="overflow:visible">
      <mask id="m"><rect x="-20" y="-20" width="140" height="140" fill="white"/><circle cx="92" cy="35" r="18" fill="black"/><circle cx="84" cy="62" r="14" fill="black"/></mask>
      <g mask="url(#m)">
        <path d="M 20 80 C 35 45 65 25 90 10 C 90 60 70 90 20 80 Z" fill="#4a7c59"/>
        <path d="M 20 80 C 10 30 40 10 90 10 C 65 25 35 45 20 80 Z" fill="#8fbc8f"/>
      </g>
      <path d="M 22 78 L 12 92" stroke="#4a7c59" stroke-width="7" stroke-linecap="round"/>
    </svg>
    <div><span style="color:#4a7c59">Nutri</span><span style="color:#8fbc8f">Smart</span></div>
  </a>
  <ul class="nav-links">
    <li><a href="nutrismart-website.html">Accueil</a></li>
    <li><a href="suivi-statistiques.php">Suivi</a></li>
    <li><a href="profile.html">Profil</a></li>
    <li><a href="recette.php" class="active">Recettes</a></li>
    <li><a href="contact.html">Contact</a></li>
    <li><a href="../backoffice/nutrismart-dashboard.php" style="color:#f2994a;font-weight:700">Admin</a></li>
  </ul>
  <div class="nav-auth"><a href="register.html" class="nav-cta">Commencer</a></div>
</nav>

<!-- HERO -->
<div class="hero">
  <h1>Recettes <em>& Aliments Santé</em></h1>
  <p>Recettes équilibrées + aliments ajoutés par l'administration</p>
</div>

<!-- RECETTES STATIQUES -->
<div class="section-title">🍽️ Nos Recettes</div>
<div class="grid">
  <?php
  $static = [
    ['Salade Quinoa & Avocat',    'images/salade.jpg',     'Salade fraîche à base de quinoa et avocat.',    '12g','350','Salade',  ['100g quinoa','1 avocat','Tomates cerises','Roquette','Huile olive'],['Cuire quinoa.','Couper avocat & tomates.','Mélanger et assaisonner.']],
    ['Bol Smoothie Vert',         'images/smoothie.jpg',   'Riche en vitamines pour démarrer la journée.',  '3g', '180','Boisson', ['1 pomme verte','Épinards','1 banane','200ml eau de coco'],['Tout mettre dans un blender.','Mixer.','Servir frais.']],
    ['Riz au Poulet',             'images/riz-poulet.jpg', 'Plat léger et savoureux pour un dîner équilibré.','25g','450','Plat', ['150g riz basmati','200g poulet','Carottes, petits pois','Épices'],['Cuire le riz.','Griller le poulet.','Mélanger avec légumes vapeur.']],
    ['Omelette aux Légumes',      'images/omelette.jpg',   'Omelette moelleuse et saine.',                  '18g','250','Plat',   ['3 œufs','Poivrons, oignons','Herbes, sel'],['Battre les œufs.','Revenir les légumes.','Cuire jusqu\'à ferme.']],
    ['Steak & Légumes Sautés',    'images/steak.jpg',      'Riche en protéines et fibres.',                 '28g','400','Plat',   ['150g steak','Poivrons, brocolis','Huile olive'],['Revenir légumes.','Griller steak.','Servir ensemble.']],
    ['Pasta High Protéine',       'images/pasta.jpg',      'Pâtes complètes avec poulet et épinards.',      '30g','500','Plat',   ['100g pâtes','150g poulet','Épinards, ail'],['Cuire pâtes al dente.','Revenir poulet & épinards.','Mélanger.']],
    ['Poisson Grillé & Légumes',  'images/poisson.jpg',    'Riche en oméga-3 avec légumes vapeur.',         '25g','350','Poisson',['150g poisson blanc','Courgettes, brocolis','Citron'],['Assaisonner & griller 5 min.','Cuire légumes vapeur.','Servir avec citron.']],
    ['Salade de Fruits Fraîche',  'images/saladefruit.jpg','Dessert léger et vitaminé.',                    '2g', '120','Dessert',['Pomme, poire, kiwi','Fraises, myrtilles','Jus de citron'],['Couper les fruits.','Mélanger avec citron.','Servir frais.']],
  ];
  foreach($static as $i => [$nom,$img,$desc,$prot,$cal,$cat,$ing,$prep]):
  ?>
  <div class="card">
    <img src="<?= $img ?>" alt="<?= $nom ?>" onerror="this.style.display='none'">
    <div class="card-body">
      <span class="badge"><?= $cat ?></span>
      <h3><?= $nom ?></h3>
      <p><?= $desc ?></p>
      <p style="margin-top:.5rem;font-size:.82rem;color:#4CAF50;font-weight:700">🔥 <?= $cal ?> kcal &nbsp;|&nbsp; 💪 <?= $prot ?> protéines</p>
      <button class="btn" onclick="toggle(this)">Voir la recette</button>
      <div class="detail">
        <h4>Ingrédients</h4>
        <ul><?php foreach($ing as $i) echo "<li>$i</li>"; ?></ul>
        <h4>Préparation</h4>
        <ol><?php foreach($prep as $p) echo "<li>$p</li>"; ?></ol>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- ALIMENTS BASE DE DONNÉES -->
<?php if (!empty($aliments)): ?>
<div class="section-title">🥗 Aliments Nutritionnels</div>
<div class="grid">
  <?php foreach($aliments as $a): ?>
  <div class="card">
    <div class="card-body">
      <span class="badge"><?= htmlspecialchars($a['categorie'] ?? 'Autre') ?></span>
      <h3><?= htmlspecialchars($a['nom']) ?></h3>
      <div class="macros">
        <div class="macro"><?= (int)($a['calories']??0) ?> kcal<span>Calories</span></div>
        <div class="macro"><?= number_format((float)($a['proteines']??0),1) ?>g<span>Protéines</span></div>
        <div class="macro"><?= number_format((float)($a['glucides']??0),1) ?>g<span>Glucides</span></div>
        <div class="macro"><?= number_format((float)($a['lipides']??0),1) ?>g<span>Lipides</span></div>
      </div>
      <?php if(!empty($a['prix'])): ?>
      <p style="color:#F2994A;font-weight:700;font-size:.9rem">Prix : <?= number_format((float)$a['prix'],2) ?> €</p>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<footer>&copy; 2026 NutriSmart. Tous droits réservés.</footer>

<script>
function toggle(btn) {
  var d = btn.nextElementSibling;
  d.style.display = d.style.display === 'block' ? 'none' : 'block';
  btn.textContent = d.style.display === 'block' ? 'Masquer' : 'Voir la recette';
}
</script>
</body>
</html>
