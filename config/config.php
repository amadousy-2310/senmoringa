<?php
/**
 * SenMoringa — Configuration générale
 * Renseignez vos identifiants MySQL ci-dessous.
 */

// ---- Base de données ----
define('DB_HOST', 'localhost');
define('DB_NAME', 'senmoringa');
define('DB_USER', 'root');
define('DB_PASS', '');

// ---- Informations du site ----
define('SITE_NOM', 'SenMoringa');
define('SITE_DEVISE', 'FCFA');
define('SITE_TELEPHONE', '+221 71 011 56 82');
define('SITE_WHATSAPP', '221710115682');
define('SITE_EMAIL', 'Senmoringa11@gmail.com');
define('SITE_ADRESSE', 'Dakar, Sénégal');
define('SITE_LINKEDIN', 'https://www.linkedin.com/in/ousmane-sy-91b854384');
define('SITE_INSTAGRAM', 'https://www.instagram.com/sen.moringa?igsh=MTViY3h4d2U3Z2ZxdA%3D%3D&utm_source=qr');
define('SITE_FACEBOOK', 'https://www.facebook.com/share/1FRkDUYPfk/?mibextid=wwXIfr');
define('FRAIS_LIVRAISON_DAKAR', 1500);
define('FRAIS_LIVRAISON_AUTRE', 3000);

// ---- Session ----
if (session_status() === PHP_SESSION_NONE) {
    $connexionSecurisee = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') == 443);
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $connexionSecurisee, // cookie envoyé uniquement en HTTPS si le site est servi en HTTPS
        'httponly' => true,                // inaccessible en JavaScript, protège contre le vol de cookie
        'samesite' => 'Lax',
    ]);
    session_start();
}

// ---- Connexion PDO ----
function getPDO(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            die('Connexion à la base de données impossible. Vérifiez config/config.php. (' . $e->getMessage() . ')');
        }
    }
    return $pdo;
}

// ---- Chemin de base (utile si le site est dans un sous-dossier) ----
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
if (strpos($scriptDir, '/admin') !== false) {
    $scriptDir = substr($scriptDir, 0, strpos($scriptDir, '/admin'));
}
define('BASE_URL', rtrim($scriptDir, '/'));
