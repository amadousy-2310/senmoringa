# SenMoringa — Site e-commerce PHP

Site de vente en ligne pour SenMoringa (poudre, huile, jus et compléments à base de moringa),
avec panier, tunnel de commande, et back-office d'administration.

## Stack technique
- PHP 8+ (procédural, PDO pour MySQL — aucun framework)
- MySQL / MariaDB
- Aucune dépendance externe à installer (pas de Composer, pas de npm)

## Installation en local (XAMPP / WAMP / MAMP)

1. **Copier le dossier** `senmoringa/` dans le dossier web de votre serveur :
   - XAMPP (Windows/Linux) : `htdocs/senmoringa`
   - WAMP : `www/senmoringa`
   - MAMP : `htdocs/senmoringa`

2. **Créer la base de données** :
   - Ouvrez phpMyAdmin (`http://localhost/phpmyadmin`)
   - Cliquez sur "Importer", sélectionnez le fichier `sql/senmoringa.sql`, validez.
   - Cela crée la base `senmoringa`, toutes les tables, et des produits de démonstration.

3. **Configurer la connexion** :
   - Ouvrez `config/config.php`
   - Vérifiez / adaptez `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` (par défaut : `root` sans mot de passe, standard sur XAMPP/WAMP/MAMP).

4. **Lancer Apache + MySQL** depuis le panneau de contrôle XAMPP/WAMP/MAMP.

5. **Ouvrir le site** : `http://localhost/senmoringa/index.php`

6. **Accéder à l'administration** : `http://localhost/senmoringa/admin/login.php`
   - Identifiant : `admin`
   - Mot de passe : `SenMoringa2026!`
   - **Changez ce mot de passe dès que possible** (voir section dédiée plus bas).

## Mise en ligne chez un hébergeur (mutualisé type OVH, Hostinger, etc.)

1. Uploadez tout le contenu du dossier `senmoringa/` à la racine de votre hébergement (ou dans un sous-dossier).
2. Créez une base de données MySQL depuis le panneau d'hébergement, notez l'hôte, le nom, l'utilisateur et le mot de passe fournis.
3. Importez `sql/senmoringa.sql` via phpMyAdmin.
4. Renseignez ces informations dans `config/config.php`.
5. Vérifiez que le dossier `assets/images/produits/` est accessible en écriture (droits 755 ou 775) pour permettre l'upload de photos depuis l'admin.
6. C'est prêt !

**Prérequis serveur** : PHP 8.0+ avec les extensions `pdo_mysql` et `mbstring` activées.
Ce sont des extensions standards, déjà actives par défaut chez la quasi-totalité des hébergeurs et sur XAMPP/WAMP/MAMP — vous n'avez normalement rien à faire.

## Changer le mot de passe administrateur

Le mot de passe par défaut est `SenMoringa2026!`. Pour le changer :

1. Générez un nouveau hash avec ce court script PHP (à exécuter une fois puis supprimer) :
```php
<?php
echo password_hash('VotreNouveauMotDePasse', PASSWORD_DEFAULT);
```
2. Copiez le résultat (commence par `$2y$...`)
3. Dans phpMyAdmin, table `admin_users`, modifiez la colonne `password_hash` de la ligne `admin` avec cette nouvelle valeur.

## Sécurité de l'espace admin

- **Protection anti-brute-force** : au bout de 5 tentatives de connexion échouées depuis la même adresse IP, la connexion est bloquée pendant 15 minutes. Si votre base de données existait déjà avant cette mise à jour, exécutez `sql/mise-a-jour-2026-08-securite.sql` dans phpMyAdmin pour créer la table nécessaire (`admin_tentatives_connexion`) ; pour une toute nouvelle installation, elle est déjà incluse dans `sql/senmoringa.sql`.
- **Sessions sécurisées** : à chaque connexion réussie, l'identifiant de session est régénéré (protège contre le vol de session), et le cookie de session est marqué `HttpOnly` (inaccessible en JavaScript) et `Secure` automatiquement si le site tourne en HTTPS.

## Structure du projet

```
senmoringa/
├── config/config.php          → connexion base de données + réglages du site
├── includes/                  → fonctions, en-tête, pied de page, icônes SVG
├── admin/                     → back-office (produits, catégories, commandes)
├── assets/css/                → styles (style.css = site public, admin.css = back-office)
├── assets/images/produits/    → photos des produits (uploadées depuis l'admin)
├── sql/senmoringa.sql         → schéma de base de données + données de démonstration
├── index.php                  → page d'accueil
├── boutique.php                → catalogue avec filtres
├── produit.php                 → fiche produit
├── panier.php / panier_*.php   → gestion du panier
├── commande.php                → tunnel de commande
├── merci.php                   → confirmation de commande
├── contact.php / a-propos.php  → pages de contenu
```

## Personnaliser le site

- **Coordonnées, WhatsApp, frais de livraison** : tout se règle en haut de `config/config.php`.
- **Produits, prix, stock, photos** : entièrement gérables depuis `/admin` (aucune modification de code nécessaire).
- **Textes de la page d'accueil / à propos** : directement modifiables dans `index.php` et `a-propos.php`.
- **Couleurs et typographies** : variables au tout début de `assets/css/style.css` (section `:root`).

## Fonctionnalités incluses

- Catalogue avec filtres par catégorie, recherche et tri
- Fiche produit avec sélecteur de quantité et suggestions "Ça se marie bien avec"
- Panier persistant (session) avec mise à jour des quantités et suggestions d'accompagnement
- Tunnel de commande (nom, téléphone, adresse, mode de paiement : Orange Money / Wave / à la livraison)
- Décrémentation automatique du stock à la commande
- Confirmation avec numéro de commande + lien WhatsApp
- Back-office complet : tableau de bord, CRUD produits avec upload photo, catégories, suivi des commandes avec changement de statut

## Notes importantes

- Le paiement Orange Money / Wave n'est **pas intégré techniquement** (pas de passerelle de paiement automatisée) : le mode de paiement choisi par le client est enregistré, et le contact se fait ensuite manuellement (WhatsApp/téléphone) pour finaliser le règlement — un fonctionnement courant et fiable pour ce type de commerce au Sénégal. Une vraie intégration API (Orange Money API, Wave API) peut être ajoutée plus tard si besoin.
- Sans photo ajoutée, chaque produit affiche une illustration générée automatiquement selon sa catégorie — le site reste donc présentable dès l'installation, en attendant vos vraies photos.
