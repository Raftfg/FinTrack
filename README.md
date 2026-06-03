# 📊 FinTrack — Gestionnaire Financier Personnel Intelligent

[![Laravel Version](https://img.shields.io/badge/Laravel-v13.x-red.svg)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-v8.5.x-blue.svg)](https://php.net)
[![Database](https://img.shields.io/badge/Database-PostgreSQL-blue.svg)](https://www.postgresql.org/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

**FinTrack** est une application web moderne et épurée conçue pour vous aider à suivre, analyser et maîtriser vos finances personnelles au quotidien. Elle vous permet d'enregistrer vos transactions, de suivre vos objectifs d'épargne avec des indicateurs de progression dynamiques, et de gérer les prêts octroyés à des tiers avec un système intégré de relances par e-mail et de notifications.

---

## 🚀 Fonctionnalités Clés

### 💸 Gestion des Transactions
- Enregistrement catégorisé : **Revenus**, **Dépenses**, **Épargne** et **Remboursements**.
- Regroupement chronologique automatique par jour et mois.
- Filtres avancés et cumulables par mois et type de flux.
- Graphiques et indicateurs de solde en temps réel.
- **Modales de modification interactive** pour corriger les erreurs de saisie en direct.

### 🎯 Objectifs d'Épargne
- Définition d'objectifs avec montants cibles et dates limites.
- Ajout incrémental d'épargne en un clic.
- **Détection automatique d'icônes** basée sur des mots-clés (ex. *maison, voiture, voyage, études*).
- Progression globale calculée sur l'ensemble de vos projets.
- **Analyse prédictive** intégrée pour estimer vos progrès.
- **Modales d'édition et de mise à jour** des objectifs.

### 🤝 Suivi des Prêts & Relances
- Enregistrement des prêts accordés avec e-mail, téléphone et date d'échéance.
- Barre de progression des remboursements reçus.
- Gestion automatique du statut (*Non payé, Partiel, Soldé*).
- **Rappels automatiques** : Commande console pour envoyer des e-mails aux débiteurs lorsque la date d'échéance approche ou est dépassée, avec simulation SMS dans les logs.

### 🔔 Notifications In-App
- Cloche de notification interactive intégrée dans l'en-tête (Desktop & Mobile).
- Alertes instantanées lorsque des rappels de prêts sont envoyés.
- Possibilité de marquer toutes les notifications comme lues ou de les supprimer individuellement.

### 📄 Rapports et Exports
- **Export PDF** : Rapport financier propre et formaté avec en-tête professionnel et tableau récapitulatif.
- **Export Excel / CSV** : Export de données encodé en UTF-8 (BOM) pour une compatibilité parfaite avec MS Excel.

---

## 🛡️ Sécurité & Fiabilité

FinTrack intègre des mesures de sécurité robustes pour protéger vos données :
- **Protection Anti-Spam** : Intégration globale de **Spatie Honeypot** sur tous les formulaires d'écriture pour bloquer silencieusement les robots de spam.
- **Limitation de Débit (Rate Limiting)** : Protection contre les attaques par force brute (max. 30 soumissions/minute pour les actions d'écriture).
- **En-têtes HTTP de Sécurité** : Injection globale de règles anti-clickjacking (`X-Frame-Options`), protection MIME (`X-Content-Type-Options`), et politique de référent sécurisée.
- **Sauvegardes Automatiques** : Intégration de **Spatie Laravel Backup** pour sauvegarder et archiver régulièrement la base de données PostgreSQL vers un stockage cloud isolé.

---

## 🛠️ Stack Technique

- **Framework :** Laravel 13 (PHP 8.5)
- **Base de données :** PostgreSQL
- **Moteur de Template :** Blade & Alpine.js (pour la réactivité des modales)
- **Styles :** Tailwind CSS (compilé localement en interne via Vite)
- **Gestionnaire d'Assets :** Vite

---

## ⚙️ Installation et Configuration

### Prérequis
- PHP >= 8.5
- Composer
- Node.js & npm
- PostgreSQL

### 1. Cloner le projet
```bash
git clone https://github.com/Raftfg/FinTrack.git
cd FinTrack
```

### 2. Installer les dépendances
```bash
composer install
npm install
```

### 3. Configurer l'environnement
Copiez le fichier d'exemple et renseignez vos informations de connexion à la base de données et votre configuration mail :
```bash
cp .env.example .env
```
Générez la clé d'application :
```bash
php artisan key:generate
```

### 4. Lancer les migrations et seeders
```bash
php artisan migrate --seed
```

### 5. Compiler les assets et démarrer
Pour le développement :
```bash
npm run dev
```
Pour compiler les assets de production (Tailwind CSS en local) :
```bash
npm run build
```

Pour lancer le serveur PHP de Laravel :
```bash
php artisan serve
```

---

## ⏰ Tâches Planifiées (Rappels de Prêts)

La commande de vérification et d'envoi des rappels de prêts est configurée pour s'exécuter quotidiennement. 

Pour la tester manuellement :
```bash
php artisan app:send-loan-reminders
```

Pour que le planificateur Laravel fonctionne en production, ajoutez cette entrée Cron à votre serveur :
```cron
* * * * * cd /chemin/vers/votre/projet && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📝 Licence

Distribué sous la licence MIT. Voir `LICENSE` pour plus d'informations.
