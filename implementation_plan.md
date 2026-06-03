# Plan d'Implémentation : Sécurisation Multi-Niveau de FinTrack

Ce document décrit les étapes pour renforcer la sécurité applicative de FinTrack au niveau du code, des formulaires et de la gestion des données.

---

## User Review Required

> [!IMPORTANT]
> - Nous allons installer deux packages Spatie très réputés pour la sécurité : `spatie/laravel-honeypot` (contre les bots de spam) et `spatie/laravel-backup` (pour les sauvegardes automatiques).
> - Nous allons configurer des **En-têtes HTTP de Sécurité** pour protéger l'application contre les attaques par détournement de clic (Clickjacking), XSS et sniffing de type MIME.

---

## Proposed Changes

### 1. Sécurisation des Formulaires (Anti-Bots)
Nous installerons `spatie/laravel-honeypot` et l'ajouterons à tous nos formulaires de création et de connexion.
* **Honeypot** ajoute un champ invisible pour les humains mais visible pour les robots. Si un robot remplit ce champ, la soumission est bloquée automatiquement.

#### [MODIFY] [app.blade.php](file:///c:/Users/HP/Music/fintrack/resources/views/layouts/app.blade.php)
* Ajouter la directive `@honeypot` dans les formulaires d'ajout de Transaction, de Prêt, et d'Objectif.

---

### 2. En-têtes HTTP de Sécurité (Sécurité Web)
Nous allons implémenter un middleware personnalisé pour injecter des en-têtes HTTP sécurisés sur toutes les requêtes de l'application.

#### [NEW] [SecurityHeaders.php](file:///c:/Users/HP/Music/fintrack/app/Http/Middleware/SecurityHeaders.php)
Créer un middleware qui ajoute les en-têtes suivants aux réponses HTTP :
* `X-Frame-Options: SAMEORIGIN` (Empêche l'inclusion du site dans des iframes externes - Anti-Clickjacking).
* `X-Content-Type-Options: nosniff` (Force le navigateur à respecter le type MIME déclaré).
* `Referrer-Policy: strict-origin-when-cross-origin` (Protège l'envoi de l'URL référente).
* `Permissions-Policy` (Limite l'accès aux capteurs de l'appareil comme la caméra ou le micro).
* `Strict-Transport-Security` (Force HTTPS en production).

#### [MODIFY] [app.php](file:///c:/Users/HP/Music/fintrack/bootstrap/app.php)
* Enregistrer le middleware `SecurityHeaders` globalement dans le pipeline des middlewares de l'application.

---

### 3. Sauvegardes Automatiques (Anti-Pertes & Ransomware)
Nous allons installer `spatie/laravel-backup` pour permettre des sauvegardes automatiques régulières de la base de données et des fichiers vers un stockage externe ou local (configuré temporairement en local puis prêt pour AWS S3 / Dropbox).

#### [NEW] Commandes d'installation
* `composer require spatie/laravel-backup`
* `php artisan backup:install`

---

### 4. Limitation de Débit (Rate Limiting)
Nous allons configurer des limites de requêtes sur les formulaires de soumission pour éviter les attaques par force brute (Brute Force / DDoS applicatif).

#### [MODIFY] [web.php](file:///c:/Users/HP/Music/fintrack/routes/web.php)
* Appliquer le middleware `throttle` (ex. `throttle:10,1` pour maximum 10 créations par minute) sur les routes POST d'enregistrement de données.

---

## Verification Plan

### Automated Tests
* Vérifier le bon chargement des dépendances après installation.
* Tester le bon fonctionnement des formulaires avec Honeypot.

### Manual Verification
* Inspecter les réponses réseau dans la console du navigateur pour vérifier la présence des nouveaux en-têtes de sécurité (`X-Frame-Options`, `Referrer-Policy`, etc.).
* Lancer un test de sauvegarde manuelle avec `php artisan backup:run` pour s'assurer que la base de données est correctement compressée et sauvegardée.
