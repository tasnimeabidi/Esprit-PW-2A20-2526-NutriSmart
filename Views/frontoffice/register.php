<?php
require_once __DIR__ . "/../../config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = isset($_POST["fullname"]) ? trim($_POST["fullname"]) : "";
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
    $passwordRaw = isset($_POST["password"]) ? trim($_POST["password"]) : "";
    $confirmPassword = isset($_POST["confirmPassword"]) ? trim($_POST["confirmPassword"]) : "";

    // VALIDATION
    if (empty($name) || empty($email) || empty($passwordRaw) || empty($confirmPassword)) {
        $message = "Tous les champs sont obligatoires.";
    } elseif ($passwordRaw !== $confirmPassword) {
        $message = "Les mots de passe ne correspondent pas.";
    } else {

        $password = password_hash($passwordRaw, PASSWORD_DEFAULT);

        // CHECK EMAIL
        $check = $conn->prepare("SELECT id_utilisateur FROM utilisateur WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $message = "Email déjà utilisé.";
        } else {

            // INSERT (FIXED COLUMN NAMES)
            $role = "user"; // default role

            $stmt = $conn->prepare("INSERT INTO utilisateur (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $password, $role);

            if ($stmt->execute()) {
                $message = "Compte créé avec succès !";
            } else {
                $message = "Erreur lors de la création.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inscription — NutriSmart</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family=DM+Sans:wght@300;400;500;600&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="css/shared-styles.css" />
    <style>
      .auth-hero {
        min-height: 90vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(
            rgba(34, 60, 42, 0.65),
            rgba(34, 60, 42, 0.65)
          ),
          url("https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?auto=format&fit=crop&w=1600&q=80")
            center/cover fixed;
        padding: 4rem 2rem;
      }

      .auth-card {
        text-align: center;
      }

      .auth-card h2 {
        font-family: "Playfair Display", serif;
        font-size: clamp(2rem, 4vw, 2.8rem);
        font-weight: 900;
        color: var(--forest);
        margin-bottom: 0.5rem;
      }

      .auth-card p {
        color: var(--gray);
        margin-bottom: 2.5rem;
        font-size: 1.05rem;
      }

      .auth-footer {
        margin-top: 2rem;
        font-size: 0.95rem;
        color: var(--gray);
      }

      .auth-footer a {
        color: var(--primary);
        font-weight: 700;
        text-decoration: none;
      }
    </style>
  </head>

<?php if (!empty($_SESSION["error"])): ?>
  <p style="color:red"><?= $_SESSION["error"] ?></p>
  <?php unset($_SESSION["error"]); ?>
<?php endif; ?>

<?php if (!empty($_SESSION["success"])): ?>
  <p style="color:green"><?= $_SESSION["success"] ?></p>
  <?php unset($_SESSION["success"]); ?>
<?php endif; ?>
<body>
    <!-- NAV -->
    <nav id="navbar">
      <a href="nutrismart-website.html" class="nav-logo">
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
        <li><a href="/ProjetNutrismart/Views/frontoffice/nutrismart-website.php">Accueil</a></li>
        <li><a href="blog.php">Blog</a></li>
      </ul>
      <div class="nav-auth"></div>
    </nav>

    <main class="auth-hero">
      <div class="form-card auth-card">
        <h2>Inscription</h2>
        <p>Commencez votre transformation nutritionnelle dès aujourd'hui.</p>
        <?php if (!empty($message)): ?>
  <p style="color:red; font-weight:600; margin-bottom:1rem;">
    <?= $message ?>
  </p>
<?php endif; ?>

?>

        <form action="/ProjetNutrismart/index.php?action=register" method="POST" id="registerForm" novalidate>

  <div class="form-group">
    <label for="fullname">Nom complet</label>
    <input 
      type="text" 
      name="fullname"
      id="fullname"
      placeholder="Votre nom complet" 
      required 
    />
    <small class="error-message" id="nameError"></small>
  </div>

  <div class="form-group">
    <label for="email">Adresse e-mail</label>
    <input 
      type="email" 
      name="email"
      id="email"
      placeholder="jean@exemple.com" 
      required 
    />
    <small class="error-message" id="emailError"></small>
  </div>

  <div class="form-group">
    <label for="password">Mot de passe</label>
    <input 
      type="password" 
      name="password"
      id="password"
      placeholder="••••••••" 
      required 
    />
    <small class="error-message" id="passwordError"></small>
  </div>

  <div class="form-group">
    <label for="confirmPassword">Confirmer le mot de passe</label>
    <input 
      type="password" 
      name="confirmPassword"
      id="confirmPassword"
      placeholder="••••••••" 
      required 
    />
    <small class="error-message" id="confirmPasswordError"></small>
  </div>

  <button
    type="submit"
    class="btn-primary"
    style="width: 100%; margin-top: 1rem"
  >
    Créer mon compte →
  </button>

</form>

<script src="assets/js/register.js"></script>

        <div class="auth-footer">
          Déjà membre ?<a href="/ProjetNutrismart/index.php?action=login">Se connecter ici</a>
        </div>
      </div>
    </main>

    <!-- FOOTER -->
    <footer id="contact">
      <div class="footer-inner">
        <div class="footer-brand">
          <div class="footer-logo">
            <svg
              width="34"
              height="34"
              viewBox="0 0 100 100"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <mask id="biteMaskFooter">
                <rect x="-20" y="-20" width="140" height="140" fill="white" />
                <circle cx="92" cy="35" r="18" fill="black" />
                <circle cx="84" cy="62" r="14" fill="black" />
              </mask>
              <g mask="url(#biteMaskFooter)">
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
            <span style="margin-left: 10px">NutriSmart</span>
          </div>
          <p class="footer-desc">
            Votre plateforme de nutrition intelligente et personnalisée. Mangez
            mieux, vivez pleinement.
          </p>
          <div class="footer-contact-list">
            <a
              href="mailto:contact@nutrismart.demo"
              class="footer-contact-item"
            >
              <div class="fc-icon">
                <svg viewBox="0 0 24 24">
                  <path
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                  />
                </svg>
              </div>
              contact@nutrismart.demo
            </a>
            <a href="tel:+21612345678" class="footer-contact-item">
              <div class="fc-icon">
                <svg viewBox="0 0 24 24">
                  <path
                    d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"
                  />
                </svg>
              </div>
              +216 12 345 678
            </a>
            <div class="footer-contact-item">
              <div class="fc-icon">
                <svg viewBox="0 0 24 24">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" />
                  <circle cx="12" cy="10" r="3" />
                </svg>
              </div>
              Tunis, Tunisie
            </div>
          </div>
        </div>

        <div class="footer-col">
          <h5>Plateforme</h5>
          <ul>
            <li><a href="#features">Fonctionnalités</a></li>
            <li><a href="#how-it-works">Comment ça marche</a></li>
            <li><a href="#modules">Modules</a></li>
            <li><a href="#programme">Programmes Repas</a></li>
            <li><a href="#sport">Programmes Sport</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <h5>Ressources</h5>
          <ul>
            <li><p>Base alimentaire</p></li>
            <li><p>Carnet de recettes</p></li>
            <li><p>Blog Nutrition</p></li>
            <li><p>Suivi de progrès</p></li>
            <li><p>FAQ</p></li>
          </ul>
        </div>

        <div class="footer-col">
          <h5>Suivez-nous</h5>
          <div class="footer-social">
            <a href="#" class="social-btn" aria-label="Instagram">
              <svg viewBox="0 0 24 24">
                <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                <path
                  d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zM17.5 6.5h.01"
                />
              </svg>
            </a>
            <a href="#" class="social-btn" aria-label="Facebook">
              <svg viewBox="0 0 24 24">
                <path
                  d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"
                />
              </svg>
            </a>
            <a href="#" class="social-btn" aria-label="X">
              <svg viewBox="0 0 24 24">
                <path
                  d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"
                />
              </svg>
            </a>
            <a href="#" class="social-btn" aria-label="LinkedIn">
              <svg viewBox="0 0 24 24">
                <path
                  d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"
                />
                <circle cx="4" cy="4" r="2" />
              </svg>
            </a>
          </div>
          <h5>Légal</h5>
          <ul>
            <li>Politique de confidentialité</li>
            <li>Conditions d'utilisation</li>
            <li>Mentions légales</li>
          </ul>
        </div>
      </div>

      <div class="footer-bottom">
        <span>© 2026 NutriSmart — Tous droits réservés</span>
        <div class="footer-bottom-links">
          <a href="#">Confidentialité</a>
          <a href="#">CGU</a>
        </div>
      </div>
    </footer>
  </body>
</html>