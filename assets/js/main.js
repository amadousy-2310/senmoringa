/**
 * SenMoringa — Interactions et animations
 * Amélioration progressive : le site fonctionne entièrement sans JS
 * (formulaires classiques, liens directs) ; ce script ajoute de la fluidité.
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    initMenuMobile();
    initEnteteAuScroll();
    initBoutonHaut();
    initApparitionAuScroll();
    initAjoutPanierAjax();
    initCompteurStats();
  });

  // -----------------------------------------------------
  // Menu mobile (hamburger)
  // -----------------------------------------------------
  function initMenuMobile() {
    const bouton = document.getElementById('bouton-menu-mobile');
    const nav = document.getElementById('nav-mobile');
    if (!bouton || !nav) return;

    bouton.addEventListener('click', function () {
      const ouvert = nav.classList.toggle('ouvert');
      bouton.setAttribute('aria-expanded', ouvert ? 'true' : 'false');
    });

    // Ferme le menu si on clique un lien
    nav.querySelectorAll('a').forEach(function (lien) {
      lien.addEventListener('click', function () {
        nav.classList.remove('ouvert');
        bouton.setAttribute('aria-expanded', 'false');
      });
    });
  }

  // -----------------------------------------------------
  // Ombre sur l'en-tête au défilement
  // -----------------------------------------------------
  function initEnteteAuScroll() {
    const entete = document.querySelector('.entete');
    if (!entete) return;
    function majEntete() {
      entete.classList.toggle('entete-scrolled', window.scrollY > 8);
    }
    majEntete();
    window.addEventListener('scroll', majEntete, { passive: true });
  }

  // -----------------------------------------------------
  // Bouton "retour en haut"
  // -----------------------------------------------------
  function initBoutonHaut() {
    const bouton = document.getElementById('bouton-haut');
    if (!bouton) return;
    window.addEventListener('scroll', function () {
      bouton.classList.toggle('visible', window.scrollY > 500);
    }, { passive: true });
    bouton.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // -----------------------------------------------------
  // Apparition progressive des blocs au scroll
  // -----------------------------------------------------
  function initApparitionAuScroll() {
    const selecteurs = [
      '.carte-bienfait', '.carte-categorie', '.carte-produit', '.etape-process',
      '.carte-temoignage', '.cta', '.section-tete', '.hero-texte', '.hero-visuel',
      '.grille-valeurs .carte-bienfait', '.carte-contact-info',
    ];
    const elements = document.querySelectorAll(selecteurs.join(','));
    if (!elements.length) return;

    if (!('IntersectionObserver' in window)) {
      elements.forEach(function (el) { el.classList.add('revele'); });
      return;
    }

    const observateur = new IntersectionObserver(function (entrees) {
      entrees.forEach(function (entree) {
        if (entree.isIntersecting) {
          entree.target.classList.add('revele');
          observateur.unobserve(entree.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    elements.forEach(function (el, index) {
      el.classList.add('reveal');
      // Effet de cascade au sein d'une même grille
      const delai = (index % 4) * 90;
      el.style.transitionDelay = delai + 'ms';
      observateur.observe(el);
    });
  }

  // -----------------------------------------------------
  // Ajout au panier en AJAX (sans recharger la page)
  // -----------------------------------------------------
  function initAjoutPanierAjax() {
    document.querySelectorAll('form[action$="panier_ajouter.php"]').forEach(function (form) {
      form.addEventListener('submit', function (evenement) {
        evenement.preventDefault();
        const bouton = form.querySelector('button[type="submit"]');
        if (!bouton || bouton.disabled) return;

        const texteInitial = bouton.textContent;
        bouton.disabled = true;
        bouton.classList.add('btn-ajout-etat');
        bouton.textContent = 'Ajout...';

        fetch(form.action, {
          method: 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
          body: new FormData(form),
        })
          .then(function (reponse) { return reponse.json(); })
          .then(function (donnees) {
            if (donnees.success) {
              bouton.textContent = 'Ajouté ✓';
              bouton.classList.add('succes');
              majBadgePanier(donnees.count);
              afficherToast(donnees.message, true);
            } else {
              bouton.textContent = texteInitial;
              afficherToast(donnees.message || "Impossible d'ajouter ce produit.", false);
            }
          })
          .catch(function () {
            // En cas d'échec réseau, on retombe sur le comportement classique du formulaire
            form.submit();
          })
          .finally(function () {
            setTimeout(function () {
              bouton.disabled = false;
              bouton.classList.remove('succes');
              bouton.textContent = texteInitial;
            }, 1600);
          });
      });
    });
  }

  function majBadgePanier(nombre) {
    const badge = document.getElementById('badge-panier');
    if (!badge) return;
    badge.textContent = nombre;
    badge.style.display = nombre > 0 ? 'flex' : 'none';
    badge.classList.remove('anime');
    // force le redémarrage de l'animation
    void badge.offsetWidth;
    badge.classList.add('anime');
  }

  function afficherToast(message, succes) {
    let zone = document.getElementById('zone-toasts');
    if (!zone) {
      zone = document.createElement('div');
      zone.id = 'zone-toasts';
      document.body.appendChild(zone);
    }
    const toast = document.createElement('div');
    toast.className = 'toast';
    const icone = succes
      ? '<svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.6"/><path d="M8 12.5l2.5 2.5L16 9.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>'
      : '<svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.6"/><path d="M12 8v5M12 16h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';
    toast.innerHTML = icone + '<span></span>';
    toast.querySelector('span').textContent = message;
    zone.appendChild(toast);

    requestAnimationFrame(function () { toast.classList.add('visible'); });

    setTimeout(function () {
      toast.classList.remove('visible');
      setTimeout(function () { toast.remove(); }, 350);
    }, 3200);
  }

  // -----------------------------------------------------
  // Compteur animé pour les statistiques du hero
  // -----------------------------------------------------
  function initCompteurStats() {
    const compteurs = document.querySelectorAll('[data-compte-jusqua]');
    if (!compteurs.length) return;

    function animer(el) {
      const cible = parseInt(el.getAttribute('data-compte-jusqua'), 10) || 0;
      const suffixe = el.getAttribute('data-suffixe') || '';
      const duree = 1100;
      const debut = performance.now();

      function etape(maintenant) {
        const progression = Math.min((maintenant - debut) / duree, 1);
        const valeur = Math.round(cible * (1 - Math.pow(1 - progression, 3))); // easing out-cubic
        el.textContent = valeur + suffixe;
        if (progression < 1) requestAnimationFrame(etape);
      }
      requestAnimationFrame(etape);
    }

    if (!('IntersectionObserver' in window)) {
      compteurs.forEach(animer);
      return;
    }
    const observateur = new IntersectionObserver(function (entrees) {
      entrees.forEach(function (entree) {
        if (entree.isIntersecting) {
          animer(entree.target);
          observateur.unobserve(entree.target);
        }
      });
    }, { threshold: 0.5 });
    compteurs.forEach(function (el) { observateur.observe(el); });
  }
})();
