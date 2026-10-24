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
    initVideoUtilisation();
    initVideosSansPleinEcran();
    initLecteursPersonnalises();
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
  // -----------------------------------------------------
  // Vidéo d'utilisation d'un produit (boutique)
  // Clic sur le produit ou sur "Voir comment l'utiliser" -> la vidéo
  // s'affiche sous la grille. Sans JS (ou hors boutique) le lien normal
  // vers la fiche produit continue de fonctionner.
  // -----------------------------------------------------
  function initVideoUtilisation() {
    function fermer(panneau) {
      const video = panneau.querySelector('video');
      if (video) video.pause();
      panneau.hidden = true;
      document.querySelectorAll('[data-video-cible="' + panneau.id + '"]').forEach(function (el) {
        el.classList.remove('video-active');
        if (el.hasAttribute('aria-expanded')) el.setAttribute('aria-expanded', 'false');
      });
    }

    function ouvrir(panneau) {
      document.querySelectorAll('.panneau-video').forEach(function (autre) {
        if (autre !== panneau && !autre.hidden) fermer(autre);
      });
      panneau.hidden = false;
      document.querySelectorAll('[data-video-cible="' + panneau.id + '"]').forEach(function (el) {
        if (el.classList.contains('carte-produit')) el.classList.add('video-active');
        if (el.hasAttribute('aria-expanded')) el.setAttribute('aria-expanded', 'true');
      });
      panneau.scrollIntoView({ behavior: 'smooth', block: 'center' });
      const video = panneau.querySelector('video');
      if (video) {
        const lecture = video.play();
        if (lecture && lecture.catch) lecture.catch(function () {});
      }
    }

    // Délégation d'événements : fonctionne même si la carte est ajoutée après le chargement
    document.addEventListener('click', function (evenement) {
      const cible = evenement.target;
      if (!cible || !cible.closest) return;

      const fermeture = cible.closest('.panneau-video-fermer');
      if (fermeture) {
        const panneau = fermeture.closest('.panneau-video');
        fermer(panneau);
        const carte = document.querySelector('.carte-produit[data-video-cible="' + panneau.id + '"], .btn-voir-video[data-video-cible="' + panneau.id + '"]');
        if (carte) carte.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
      }

      const declencheur = cible.closest('.btn-voir-video');
      if (!declencheur) return;
      const zone = declencheur.closest('[data-video-cible]');
      if (!zone) return;
      const panneau = document.getElementById(zone.getAttribute('data-video-cible'));
      if (!panneau) return; // pas de panneau sur cette page (ex. accueil) : lien normal
      evenement.preventDefault();
      if (panneau.hidden) ouvrir(panneau); else fermer(panneau);
    });
  }
  // -----------------------------------------------------
  // Pas de plein écran sur les vidéos d'utilisation
  // (controlsList="nofullscreen" suffit sur Chrome/Edge ; ceci couvre les autres navigateurs)
  // -----------------------------------------------------
  function initVideosSansPleinEcran() {
    const videos = document.querySelectorAll('.panneau-video video');
    if (!videos.length) return;

    videos.forEach(function (video) {
      video.addEventListener('dblclick', function (e) { e.preventDefault(); });
      // iOS Safari
      video.addEventListener('webkitbeginfullscreen', function () {
        if (video.webkitExitFullscreen) video.webkitExitFullscreen();
      });
    });

    function sortirPleinEcran() {
      const el = document.fullscreenElement || document.webkitFullscreenElement;
      if (el && el.tagName === 'VIDEO' && el.closest('.panneau-video')) {
        if (document.exitFullscreen) document.exitFullscreen();
        else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
      }
    }
    document.addEventListener('fullscreenchange', sortirPleinEcran);
    document.addEventListener('webkitfullscreenchange', sortirPleinEcran);
  }
  // -----------------------------------------------------
  // Lecteur vidéo personnalisé : lecture / progression / son, SANS bouton plein écran.
  // (Les commandes natives des navigateurs ne permettent pas toujours de le masquer.)
  // Sans JS, la vidéo garde ses commandes natives.
  // -----------------------------------------------------
  function initLecteursPersonnalises() {
    const ICONE_PLAY  = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5.5v13a1 1 0 0 0 1.5.86l10.5-6.5a1 1 0 0 0 0-1.72L9.5 4.64A1 1 0 0 0 8 5.5z"/></svg>';
    const ICONE_PAUSE = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/></svg>';
    const ICONE_SON   = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 9.5v5h4l5 4.5v-14L7 9.5H3z"/><path d="M15.5 8.5a5 5 0 0 1 0 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    const ICONE_MUET  = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 9.5v5h4l5 4.5v-14L7 9.5H3z"/><path d="M16 9.5l5 5M21 9.5l-5 5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';

    function temps(sec) {
      if (!isFinite(sec)) return '0:00';
      const m = Math.floor(sec / 60), r = Math.floor(sec % 60);
      return m + ':' + (r < 10 ? '0' : '') + r;
    }

    document.querySelectorAll('.panneau-video-lecteur video').forEach(function (video) {
      const conteneur = video.parentElement;
      video.controls = false;
      video.removeAttribute('controls');
      conteneur.classList.add('lecteur-perso');

      const grandPlay = document.createElement('button');
      grandPlay.type = 'button';
      grandPlay.className = 'lecteur-grand-play';
      grandPlay.setAttribute('aria-label', 'Lire la vidéo');
      grandPlay.innerHTML = ICONE_PLAY;

      const barre = document.createElement('div');
      barre.className = 'lecteur-controles';
      barre.innerHTML =
        '<button type="button" class="lecteur-btn lecteur-btn-play" aria-label="Lecture / pause">' + ICONE_PLAY + '</button>' +
        '<input type="range" class="lecteur-progression" min="0" max="1000" value="0" step="1" aria-label="Progression de la vidéo">' +
        '<span class="lecteur-temps">0:00</span>' +
        '<button type="button" class="lecteur-btn lecteur-btn-son" aria-label="Activer / couper le son">' + ICONE_SON + '</button>';

      conteneur.appendChild(grandPlay);
      conteneur.appendChild(barre);

      const btnPlay = barre.querySelector('.lecteur-btn-play');
      const btnSon = barre.querySelector('.lecteur-btn-son');
      const progression = barre.querySelector('.lecteur-progression');
      const affTemps = barre.querySelector('.lecteur-temps');

      function basculer() {
        if (video.paused || video.ended) {
          const lecture = video.play();
          if (lecture && lecture.catch) lecture.catch(function () {});
        } else {
          video.pause();
        }
      }
      function majEtat() {
        const enPause = video.paused || video.ended;
        btnPlay.innerHTML = enPause ? ICONE_PLAY : ICONE_PAUSE;
        grandPlay.classList.toggle('visible', enPause);
        conteneur.classList.toggle('en-lecture', !enPause);
      }
      function majProgression() {
        if (video.duration) progression.value = Math.round((video.currentTime / video.duration) * 1000);
        affTemps.textContent = temps(video.currentTime) + ' / ' + temps(video.duration);
      }
      function majSon() {
        btnSon.innerHTML = (video.muted || video.volume === 0) ? ICONE_MUET : ICONE_SON;
      }

      grandPlay.addEventListener('click', basculer);
      btnPlay.addEventListener('click', basculer);
      video.addEventListener('click', basculer);
      btnSon.addEventListener('click', function () { video.muted = !video.muted; });
      progression.addEventListener('input', function () {
        if (video.duration) video.currentTime = (progression.value / 1000) * video.duration;
      });
      ['play', 'pause', 'ended'].forEach(function (nom) { video.addEventListener(nom, majEtat); });
      ['timeupdate', 'loadedmetadata', 'durationchange'].forEach(function (nom) { video.addEventListener(nom, majProgression); });
      video.addEventListener('volumechange', majSon);

      majEtat(); majProgression(); majSon();
    });
  }
})();
