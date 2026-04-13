document.addEventListener('DOMContentLoaded', () => {

  // --- Helper Functions ---
  const validateEmail = (email) => {
    return String(email)
      .toLowerCase()
      .match(/^[^@\s]+@[^@\s]+\.[^@\s]+$/);
  };

  const validateName = (name) => {
    return /^[a-zA-ZÀ-ÿ\s'-]{3,}$/.test(name);
  };

  const validatePassword = (password) => {
    // Min 8 chars, 1 uppercase, 1 number
    return /^(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/.test(password);
  };


  const showError = (input, message) => {
    // Highlight input
    input.style.borderColor = '#e53935';
    input.style.boxShadow = '0 0 0 4px rgba(229, 57, 53, 0.1)';
    
    // Add error message text
    let errorDiv = input.parentNode.querySelector('.error-msg');
    if (!errorDiv) {
      errorDiv = document.createElement('div');
      errorDiv.className = 'error-msg';
      errorDiv.style.color = '#e53935';
      errorDiv.style.fontSize = '0.78rem';
      errorDiv.style.marginTop = '0.4rem';
      errorDiv.style.fontWeight = '600';
      input.parentNode.appendChild(errorDiv);
    }
    errorDiv.innerText = message;
  };

  const clearError = (input) => {
    input.style.borderColor = '';
    input.style.boxShadow = '';
    const errorDiv = input.parentNode.querySelector('.error-msg');
    if (errorDiv) errorDiv.remove();
  };

  // Attach clear event to all inputs
  document.querySelectorAll('input, textarea, select').forEach(input => {
    input.addEventListener('input', () => clearError(input));
  });

  // --- 1. Register Form Validation ---
  const registerForm = document.getElementById('registerForm');
  if (registerForm) {
    registerForm.addEventListener('submit', (e) => {
      let isValid = true;
      const nom = registerForm.querySelector('input[name="nom"]');
      const email = registerForm.querySelector('input[name="email"]');
      const password = registerForm.querySelector('input[name="password"]');


      if (!validateName(nom.value.trim())) {
        showError(nom, 'Le nom doit contenir au moins 3 lettres.');
        isValid = false;
      }
      if (!validateEmail(email.value.trim())) {
        showError(email, 'Adresse e-mail invalide.');
        isValid = false;
      }
      if (!validatePassword(password.value)) {
        showError(password, 'Min. 8 caractères, 1 majuscule, 1 chiffre.');
        isValid = false;
      }

      if (!isValid) e.preventDefault();
    });
  }

  // --- 2. Login Form Validation ---
  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', (e) => {
      let isValid = true;
      const email = loginForm.querySelector('input[name="email"]');
      const password = loginForm.querySelector('input[name="password"]');

      if (!validateEmail(email.value.trim())) {
        showError(email, 'Adresse e-mail manquante ou invalide.');
        isValid = false;
      }
      if (password.value.trim() === '') {
        showError(password, 'Le mot de passe est requis.');
        isValid = false;
      }

      if (!isValid) e.preventDefault();
    });
  }

  // --- 3. Profile Form Validation ---
  const profileForm = document.getElementById('profileForm');
  if (profileForm) {
    profileForm.addEventListener('submit', (e) => {
      let isValid = true;
      const age = profileForm.querySelector('input[name="age"]');
      const poids = profileForm.querySelector('input[name="poids"]');
      const taille = profileForm.querySelector('input[name="taille"]');



      const ageVal = parseInt(age.value);
      if (isNaN(ageVal) || ageVal < 10 || ageVal > 120) {
        showError(age, 'Veuillez saisir un âge valide (entre 10 et 120).');
        isValid = false;
      }

      const poidsVal = parseFloat(poids.value);
      if (isNaN(poidsVal) || poidsVal < 20 || poidsVal > 300) {
        showError(poids, 'Veuillez saisir un poids valide en kg.');
        isValid = false;
      }

      const tailleVal = parseInt(taille.value);
      if (isNaN(tailleVal) || tailleVal < 50 || tailleVal > 250) {
        showError(taille, 'Veuillez saisir une taille valide en cm.');
        isValid = false;
      }

      if (!isValid) e.preventDefault();
    });
  }

  // --- 4. Contact Form Validation ---
  const contactForm = document.getElementById('contactForm');
  if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
      let isValid = true;
      const nom = contactForm.querySelector('input[name="nom"]');
      const email = contactForm.querySelector('input[name="email"]');
      const message = contactForm.querySelector('textarea[name="message"]');

      if (!validateName(nom.value.trim())) {
        showError(nom, 'Le nom doit contenir au moins 3 lettres.');
        isValid = false;
      }
      if (!validateEmail(email.value.trim())) {
        showError(email, 'Adresse e-mail valide requise.');
        isValid = false;
      }
      if (message.value.trim().length < 10) {
        showError(message, 'Votre message doit contenir au moins 10 caractères.');
        isValid = false;
      }

      if (!isValid) e.preventDefault();
    });
  }
});
