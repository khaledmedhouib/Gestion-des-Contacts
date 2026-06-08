// public/js/validation.js

'use strict';

(function () {
  const form = document.getElementById('contactForm');
  if (!form) return;

  // ── Real-time field validation ─────────────────────────────────

  const rules = {
    nom:       { required: true, maxLength: 50, label: 'Le nom' },
    prenom:    { required: true, maxLength: 50, label: 'Le prénom' },
    telephone: { required: true, pattern: /^\+?[0-9\s]{8,15}$/, label: 'Le téléphone' },
    email:     { required: true, email: true,  label: 'L\'email' },
  };

  Object.keys(rules).forEach(fieldId => {
    const input = document.getElementById(fieldId);
    if (!input) return;

    input.addEventListener('blur', () => validateField(input, rules[fieldId]));
    input.addEventListener('input', () => {
      if (input.classList.contains('is-invalid')) {
        validateField(input, rules[fieldId]);
      }
    });
  });

  // ── Submit validation ──────────────────────────────────────────

  form.addEventListener('submit', (e) => {
    let valid = true;

    Object.keys(rules).forEach(fieldId => {
      const input = document.getElementById(fieldId);
      if (!input) return;
      if (!validateField(input, rules[fieldId])) valid = false;
    });

    if (!valid) {
      e.preventDefault();
      // Scroll to first invalid field
      const first = form.querySelector('.is-invalid');
      if (first) {
        first.scrollIntoView({ behavior: 'smooth', block: 'center' });
        first.focus();
      }
    } else {
      // Show loading state on submit button
      const btn = document.getElementById('submitBtn');
      if (btn) {
        btn.disabled   = true;
        btn.innerHTML  = '<span class="spinner-border spinner-border-sm me-2"></span>Enregistrement…';
      }
    }
  });

  function validateField(input, rule) {
    const val = input.value.trim();
    let errorMsg = null;

    if (rule.required && val === '') {
      errorMsg = `${rule.label} est obligatoire.`;
    } else if (rule.maxLength && val.length > rule.maxLength) {
      errorMsg = `${rule.label} ne doit pas dépasser ${rule.maxLength} caractères.`;
    } else if (rule.email && val && !isValidEmail(val)) {
      errorMsg = `Adresse email invalide.`;
    } else if (rule.pattern && val && !rule.pattern.test(val)) {
      errorMsg = `${rule.label} est invalide (8-15 chiffres).`;
    }

    setFieldState(input, errorMsg);
    return errorMsg === null;
  }

  function setFieldState(input, errorMsg) {
    // Remove old feedback
    const oldFeedback = input.parentElement.querySelector('.live-feedback');
    if (oldFeedback) oldFeedback.remove();

    if (errorMsg) {
      input.classList.add('is-invalid');
      input.classList.remove('is-valid');

      const fb = document.createElement('div');
      fb.className   = 'invalid-feedback live-feedback';
      fb.textContent = errorMsg;
      input.parentElement.appendChild(fb);
    } else {
      input.classList.remove('is-invalid');
      if (input.value.trim()) input.classList.add('is-valid');
    }
  }

  function isValidEmail(str) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(str);
  }
})();
