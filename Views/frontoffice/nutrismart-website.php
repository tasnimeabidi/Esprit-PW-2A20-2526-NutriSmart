<?php
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
    header("Location: /ProjetNutrismart/index.php?action=login");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>NutriSmart</title>

  <link rel="stylesheet" href="css/shared-styles.css" />
  <style>
      /* ── HERO ── */
      .hero {
        min-height: calc(100vh - 130px);
        display: grid;
        grid-template-columns: 1fr 1fr;
        align-items: center;
        gap: 4rem;
        padding: 4rem 5rem 5rem;
        position: relative;
        overflow: hidden;
      }

      .hero::before {
        content: "";
        position: absolute;
        top: -80px;
        right: -120px;
        width: 680px;
        height: 680px;
        border-radius: 50%;
        background: radial-gradient(
          circle,
          rgba(76, 175, 80, 0.18) 0%,
          transparent 70%
        );
        pointer-events: none;
      }

      .hero::after {
        content: "";
        position: absolute;
        bottom: -60px;
        left: -80px;
        width: 420px;
        height: 420px;
        border-radius: 50%;
        background: radial-gradient(
          circle,
          rgba(139, 195, 74, 0.14) 0%,
          transparent 70%
        );
        pointer-events: none;
      }

      .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(76, 175, 80, 0.12);
        border: 1px solid rgba(76, 175, 80, 0.3);
        color: var(--primary);
        padding: 0.35rem 0.9rem;
        border-radius: 2rem;
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        margin-bottom: 1.4rem;
        animation: fadeUp 0.8s 0.1s ease both;
      }

      .hero-badge::before {
        content: "✦";
        color: var(--mid);
      }

      .hero h1 {
        font-family: "Playfair Display", serif;
        font-size: clamp(2.8rem, 5vw, 4.2rem);
        line-height: 1.12;
        font-weight: 900;
        color: var(--forest);
        animation: fadeUp 0.8s 0.2s ease both;
      }

      .hero h1 em {
        font-style: italic;
        color: var(--orange);
        display: block;
      }

      .hero-sub {
        margin-top: 1.4rem;
        font-size: 1.05rem;
        color: var(--gray);
        line-height: 1.7;
        max-width: 480px;
        animation: fadeUp 0.8s 0.35s ease both;
      }

      .hero-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2.2rem;
        flex-wrap: wrap;
        animation: fadeUp 0.8s 0.5s ease both;
      }

      .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--mid));
        color: var(--white);
        padding: 0.85rem 2rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
      }

      .btn-primary:hover,
      .nav-cta:hover {
        background: var(--lime) !important;
        color: var(--forest) !important;
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(196, 212, 168, 0.4);
      }

      .btn-primary:active,
      .nav-cta:active {
        background: var(--orange) !important;
        color: var(--white) !important;
        transform: translateY(0);
        box-shadow: 0 4px 12px rgba(242, 153, 74, 0.4);
      }

      .btn-gradient {
        background: linear-gradient(135deg, var(--orange), var(--peach));
        color: var(--white);
        padding: 0.85rem 2rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
      }

      .btn-gradient:hover {
        background: var(--lime) !important;
        color: var(--forest) !important;
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(196, 212, 168, 0.4);
      }

      .btn-gradient:active {
        background: var(--primary) !important;
        color: var(--white) !important;
        transform: translateY(0);
        box-shadow: 0 4px 12px rgba(74, 124, 89, 0.4);
      }

      .btn-outline {
        background: transparent;
        color: var(--forest);
        padding: 0.85rem 2rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        border: 2px solid var(--lime);
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
      }

      .btn-outline:hover {
        background: var(--lime);
      }

      .hero-stats {
        display: flex;
        gap: 2.5rem;
        margin-top: 3rem;
        animation: fadeUp 0.8s 0.65s ease both;
      }

      .stat-num {
        font-family: "Playfair Display", serif;
        font-size: 1.9rem;
        font-weight: 900;
        color: var(--forest);
      }

      .stat-lbl {
        font-size: 0.78rem;
        color: var(--gray);
        text-transform: uppercase;
        letter-spacing: 0.06em;
      }

      /* Hero visual */
      .hero-visual {
        position: relative;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        animation: fadeUp 0.8s 0.3s ease both;
      }

      .hero-image-main {
        position: absolute;
        width: 320px;
        height: 320px;
        object-fit: cover;
        border-radius: 50%;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        box-shadow: 0 20px 50px rgba(45, 106, 45, 0.2);
        z-index: 0;
      }

      .card-float {
        position: relative;
        z-index: 1;
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(8px);
        border-radius: 1.2rem;
        padding: 1.3rem;
        box-shadow: 0 4px 24px rgba(26, 58, 26, 0.08);
        transition: transform 0.3s;
      }

      .card-float:hover {
        transform: translateY(-4px);
      }

      .card-float.tall {
        grid-row: span 2;
      }

      .card-float.green-card {
        background: rgba(45, 106, 45, 0.92);
        color: var(--white);
      }

      .card-icon {
        width: 42px;
        height: 42px;
        border-radius: 0.75rem;
        display: grid;
        place-items: center;
        font-size: 1.2rem;
        margin-bottom: 0.75rem;
      }

      .card-icon.g {
        background: rgba(76, 175, 80, 0.15);
      }

      .card-icon.w {
        background: rgba(255, 255, 255, 0.2);
      }

      .card-float h4 {
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 0.3rem;
      }

      .card-float p {
        font-size: 0.78rem;
        color: var(--gray);
        line-height: 1.5;
      }

      .green-card p {
        color: rgba(255, 255, 255, 0.75);
      }

      .mini-chart {
        display: flex;
        align-items: flex-end;
        gap: 5px;
        height: 55px;
        margin-top: 0.8rem;
      }

      .bar {
        flex: 1;
        border-radius: 3px 3px 0 0;
        background: rgba(76, 175, 80, 0.25);
      }

      .bar.active {
        background: var(--mid);
      }

      .progress-ring {
        width: 70px;
        height: 70px;
        margin: 0.8rem auto 0;
        position: relative;
        display: grid;
        place-items: center;
      }

      .progress-ring svg {
        position: absolute;
        inset: 0;
        transform: rotate(-90deg);
      }

      .ring-val {
        font-family: "Playfair Display", serif;
        font-size: 1rem;
        font-weight: 700;
      }

      .macro-row {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-top: 0.8rem;
      }

      .macro-lbl {
        display: flex;
        justify-content: space-between;
        font-size: 0.72rem;
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 3px;
      }

      .macro-bar {
        height: 5px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 3px;
      }

      .macro-fill {
        height: 100%;
        border-radius: 3px;
        background: var(--lime);
      }

      @keyframes fadeUp {
        from {
          opacity: 0;
          transform: translateY(30px);
        }

        to {
          opacity: 1;
          transform: none;
        }
      }

      /* ── FEATURES ── */
      .section {
        padding: 6rem 5rem;
      }

      .section-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.78rem;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--primary);
        margin-bottom: 1rem;
      }

      .section-tag::before {
        content: "";
        width: 24px;
        height: 2px;
        background: var(--mid);
        display: block;
      }

      .section h2 {
        font-family: "Playfair Display", serif;
        font-size: clamp(2rem, 3.5vw, 3rem);
        font-weight: 900;
        color: var(--forest);
        line-height: 1.2;
        margin-bottom: 1rem;
      }

      .section-sub {
        color: var(--gray);
        line-height: 1.7;
        max-width: 540px;
        font-size: 1rem;
      }

      .features-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        margin-top: 3.5rem;
      }

      .feat-card {
        background: var(--white);
        border-radius: 1.3rem;
        padding: 2.5rem;
        border: 1px solid var(--sand);
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
      }

      .feat-card:nth-child(1) {
        border-top: 6px solid var(--primary);
      }

      .feat-card:nth-child(2) {
        border-top: 6px solid var(--orange);
      }

      .feat-card:nth-child(3) {
        border-top: 6px solid var(--mid);
      }

      .feat-card:nth-child(4) {
        border-top: 6px solid var(--peach);
      }

      .feat-card:nth-child(5) {
        border-top: 6px solid var(--lime);
      }

      .feat-card:nth-child(6) {
        border-top: 6px solid var(--forest);
      }

      .feat-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--mid), var(--lime));
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.3s;
      }

      .feat-card:hover {
        box-shadow: 0 12px 40px rgba(26, 58, 26, 0.1);
        transform: translateY(-3px);
      }

      .feat-card:hover::before {
        transform: scaleX(1);
      }

      .feat-img {
        width: calc(100% + 4rem);
        height: 180px;
        margin: -2rem -2rem 1.5rem -2rem;
        object-fit: cover;
        display: block;
      }

      .feat-emoji {
        font-size: 2rem;
        margin-bottom: 1rem;
        display: block;
      }

      .feat-card h3 {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--forest);
        margin-bottom: 0.6rem;
      }

      .feat-card p {
        font-size: 0.88rem;
        color: var(--gray);
        line-height: 1.65;
      }

      .feat-tag {
        display: inline-block;
        margin-top: 1rem;
        background: rgba(76, 175, 80, 0.1);
        color: var(--primary);
        font-size: 0.72rem;
        font-weight: 600;
        padding: 0.2rem 0.65rem;
        border-radius: 1rem;
        letter-spacing: 0.04em;
      }

      .feat-tag.ai {
        background: rgba(249, 168, 37, 0.15);
        color: #b07d05;
      }

      /* ── HOW IT WORKS ── */
      .how-section {
        background: linear-gradient(
            rgba(34, 60, 42, 0.88),
            rgba(34, 60, 42, 0.94)
          ),
          url("https://images.unsplash.com/photo-1494390248081-4e521a5940db?auto=format&fit=crop&w=1600&q=80")
            center/cover fixed;
        color: var(--white);
        padding: 8rem 5rem;
        border-top: 5px solid var(--lime);
        border-bottom: 5px solid var(--orange);
      }

      .how-section .section-tag {
        color: var(--lime);
      }

      .how-section .section-tag::before {
        background: var(--lime);
      }

      .how-section h2 {
        color: var(--white);
      }

      .how-section .section-sub {
        color: rgba(255, 255, 255, 0.65);
      }

      .steps-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2rem;
        margin-top: 3.5rem;
      }

      .step {
        position: relative;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 2rem;
        border-radius: 1.5rem;
        backdrop-filter: blur(10px);
        transition: transform 0.3s;
      }

      .step:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.08);
      }

      .step::after {
        content: "→";
        position: absolute;
        top: 1.2rem;
        right: -1.5rem;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.2);
        font-size: 1.2rem;
      }

      .step:last-child::after {
        display: none;
      }

      .step-num {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: var(--lime);
        box-shadow: 0 4px 15px rgba(196, 212, 168, 0.3);
        display: grid;
        place-items: center;
        font-family: "Playfair Display", serif;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--forest);
        margin-bottom: 1.2rem;
      }

      .step h4 {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--white);
      }

      .step p {
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.6);
        line-height: 1.6;
      }

      /* ── MODULES ── */
      .modules-section {
        padding: 6rem 5rem;
        background: var(--sand);
      }

      .modules-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.2rem;
        margin-top: 3.5rem;
        max-width: 900px;
      }

      .module-item {
        display: flex;
        align-items: center;
        gap: 1.2rem;
        background: var(--white);
        border-radius: 1rem;
        padding: 1.3rem 1.5rem;
        border: 1px solid rgba(45, 106, 45, 0.1);
        transition: all 0.25s;
        text-decoration: none;
        color: inherit;
      }

      .module-item:hover {
        box-shadow: 0 8px 28px rgba(45, 106, 45, 0.12);
        transform: translateX(4px);
      }

      .module-dot {
        width: 44px;
        height: 44px;
        border-radius: 0.75rem;
        flex-shrink: 0;
        display: grid;
        place-items: center;
        font-size: 1.25rem;
      }

      .module-item h4 {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--forest);
      }

      .module-item p {
        font-size: 0.8rem;
        color: var(--gray);
        margin-top: 0.15rem;
      }

      /* ── AI SECTION ── */
      .ai-section {
        padding: 6rem 5rem;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 5rem;
        align-items: center;
      }

      .ai-visual {
        background: linear-gradient(to right, rgba(34, 60, 42, 0.85), rgba(34, 60, 42, 0.7)), url(\'https://images.unsplash.com/photo-1490645935967-10de6ba17061?auto=format&fit=crop&w=1200&q=80\') center/cover;
        border-radius: 1.8rem;
        padding: 2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(26, 58, 26, 0.15);
      }

      .ai-visual::before {
        content: "";
        position: absolute;
        top: -40px;
        right: -40px;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: rgba(76, 175, 80, 0.15);
      }

      .ai-prompt {
        background: rgba(255, 255, 255, 0.08);
        border-radius: 0.9rem;
        padding: 1.2rem;
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.85rem;
        line-height: 1.6;
        border: 1px solid rgba(255, 255, 255, 0.1);
        margin-bottom: 1rem;
      }

      .ai-prompt strong {
        color: var(--lime);
        display: block;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 0.4rem;
      }

      .ai-response {
        background: var(--primary);
        border-radius: 0.9rem;
        padding: 1.2rem;
        color: var(--white);
      }

      .ai-response strong {
        color: var(--lime);
        display: block;
        font-size: 0.75rem;
        margin-bottom: 0.5rem;
      }

      .meal-plan {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
      }

      .meal-item {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        font-size: 0.82rem;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 0.5rem;
        padding: 0.5rem 0.8rem;
      }

      .meal-icon {
        font-size: 1rem;
      }

      /* ── PRICING / CTA ── */
      .cta-section {
        padding: 6rem 5rem;
        text-align: center;
        background: var(--cream);
      }

      .cta-section h2 {
        font-family: "Playfair Display", serif;
        font-size: 2.8rem;
        font-weight: 900;
        color: var(--forest);
        margin-bottom: 1rem;
      }

      .cta-section p {
        color: var(--gray);
        font-size: 1.05rem;
        margin-bottom: 2.5rem;
      }

      .footer-bottom-links a:hover {
        color: rgba(255, 255, 255, 0.7);
      }

      @media (max-width: 900px) {
        .footer-inner {
          grid-template-columns: 1fr 1fr;
          gap: 2rem;
        }

        footer {
          padding: 4rem 2rem 0;
        }
      }

      button:hover,
      .btn-primary:hover,
      .btn-gradient:hover,
      .btn-outline:hover,
      .nav-cta:hover,
      input[type="submit"]:hover {
        background: var(--light-g) !important;
        color: var(--forest) !important;
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(197, 213, 192, 0.4) !important;
      }
    </style>
</head>

<body>
<nav id="navbar">
      <a href="#" class="nav-logo">
        <svg
          width="34"
          height="34"
          viewBox="0 0 100 100"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
          style="overflow: visible"
        >
          <mask id="biteMask">
            <rect x="-20" y="-20" width="140" height="140" fill="white" />
            <circle cx="92" cy="35" r="18" fill="black" />
            <circle cx="84" cy="62" r="14" fill="black" />
          </mask>
          <g mask="url(#biteMask)">
            <path
              d="M 20 80 C 35 45 65 25 90 10 C 90 60 70 90 20 80 Z"
              fill="#4a7c59"
            />
            <path
              d="M 20 80 C 10 30 40 10 90 10 C 65 25 35 45 20 80 Z"
              fill="#8fbc8f"
            />
          </g>
          <path
            d="M 22 78 L 12 92"
            stroke="#4a7c59"
            stroke-width="7"
            stroke-linecap="round"
          />
        </svg>
        <div>
          <span style="color: #4a7c59">Nutri</span
          ><span style="color: #8fbc8f">Smart</span>
        </div>
      </a>
      <ul class="nav-links">
  <li><a href="nutrismart-website.php" class="active">Accueil</a></li>
  <li><a href="blog.php">Blog</a></li>
</ul>

      <div class="nav-auth">
        <a href="/ProjetNutrismart/index.php?action=register" class="nav-cta">Commencer gratuitement</a>
      </div>
    </nav>

<section class="hero">
      <div class="hero-content">
        <div class="hero-badge">
          Nutrition Intelligente · 100% Personnalisée
        </div>
        <h1>
          Mangez mieux,
          <em>vivez pleinement.</em>
        </h1>
        <p class="hero-sub">
          NutriSmart vous offre un suivi nutritionnel complet et sur mesure pour
          vous aider à atteindre vos objectifs de santé simplement et
          efficacement.
        </p>
        <div class="hero-actions">
          <a href="/ProjetNutrismart/index.php?action=login">Connexion</a>
          <a href="#features" class="btn-gradient"> Fonctionnalités</a>
        </div>
        <div class="hero-stats">
          <div class="stat-item">
            <div class="stat-num">6</div>
            <div class="stat-lbl">Modules complets</div>
          </div>
          <div class="stat-item">
            <div class="stat-num">+500</div>
            <div class="stat-lbl">Aliments indexés</div>
          </div>
          <div class="stat-item">
            <div class="stat-num">100%</div>
            <div class="stat-lbl">Gratuit</div>
          </div>
        </div>
      </div>

      <!-- Visual cards -->
      <div class="hero-visual">
        <img
          src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=800&q=80"
          alt="Plat Nutritif"
          class="hero-image-main"
        />
        <div class="card-float">
          <h4>Plan du jour</h4>
          <p>Repas générés selon votre profil</p>
          <div class="mini-chart">
            <div class="bar" style="height: 40%"></div>
            <div class="bar active" style="height: 70%"></div>
            <div class="bar active" style="height: 90%"></div>
            <div class="bar active" style="height: 65%"></div>
            <div class="bar" style="height: 50%"></div>
            <div class="bar active" style="height: 80%"></div>
            <div class="bar" style="height: 45%"></div>
          </div>
        </div>

        <div class="card-float green-card tall">
          <h4>Objectif calories</h4>
          <p style="margin-bottom: 0.5rem">2 100 kcal / jour</p>
          <div class="progress-ring">
            <svg viewBox="0 0 70 70" width="70" height="70">
              <circle
                cx="35"
                cy="35"
                r="29"
                fill="none"
                stroke="rgba(255,255,255,.15)"
                stroke-width="6"
              />
              <circle
                cx="35"
                cy="35"
                r="29"
                fill="none"
                stroke="#8bc34a"
                stroke-width="6"
                stroke-dasharray="182"
                stroke-dashoffset="46"
                stroke-linecap="round"
              />
            </svg>
            <span class="ring-val">75%</span>
          </div>
          <div class="macro-row">
            <div class="macro">
              <div class="macro-lbl">
                <span>Protéines</span><span>68g</span>
              </div>
              <div class="macro-bar">
                <div class="macro-fill" style="width: 72%"></div>
              </div>
            </div>
            <div class="macro">
              <div class="macro-lbl">
                <span>Glucides</span><span>220g</span>
              </div>
              <div class="macro-bar">
                <div
                  class="macro-fill"
                  style="width: 80%; background: var(--orange)"
                ></div>
              </div>
            </div>
            <div class="macro">
              <div class="macro-lbl"><span>Lipides</span><span>54g</span></div>
              <div class="macro-bar">
                <div
                  class="macro-fill"
                  style="width: 55%; background: var(--peach)"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <div class="card-float">
          <h4>Courses</h4>
          <p>Budget restant</p>
          <div
            style="
              font-family: 'Playfair Display', serif;
              font-size: 1.7rem;
              font-weight: 900;
              color: #2d6a2d;
              margin-top: 0.5rem;
            "
          >
            38,50 TND
          </div>
        </div>

        <div
          class="card-float"
          style="
            background: var(--sand);
            display: flex;
            align-items: center;
            gap: 1rem;
          "
        >
          <img
            src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=150&q=80"
            style="
              width: 50px;
              height: 50px;
              border-radius: 12px;
              object-fit: cover;
              border: 2px solid var(--white);
            "
            alt="Fitness"
          />
          <div>
            <h4 style="margin-bottom: 0">Activité</h4>
            <p style="font-size: 0.75rem; margin-bottom: 0">Séance Cardio</p>
            <div
              style="
                font-family: 'Playfair Display', serif;
                font-size: 1.4rem;
                font-weight: 900;
                color: var(--forest);
                margin-top: 0.2rem;
              "
            >
              45 min
            </div>
          </div>
        </div>
      </div>
    </section>

</body>
</html>