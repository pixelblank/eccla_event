# ECCLA Simple Agenda (v1.3)

Un plugin WordPress ultra-léger et modulaire conçu pour gérer un agenda d'événements associatifs avec calendrier interactif, documents PDF et gestion des horaires.

## 🚀 Caractéristiques
- **Architecture Pro** : Séparation complète de la logique (PHP), des vues (Templates) et des assets (CSS/JS).
- **Custom Post Type** : Gère les événements indépendamment du reste du site.
- **Taxonomie dédiée** : Catégories d'événements isolées pour éviter les conflits d'interface.
- **Gestion du temps** : Date de début, Date de fin, Heure de début et Heure de fin (tous optionnels).
- **Calendrier Interactif** : Utilisation de FullCalendar v6 (via CDN pour la performance).
- **Système de Modale** : Ouverture des détails de l'événement dans une fenêtre large (80%) sans rechargement.
- **PDF Ready** : Champ dédié pour lier un document à chaque événement.

## 🛠 Shortcodes
- `[eccla_agenda]` : Affiche le calendrier mensuel complet avec modale interactive.
- `[eccla_upcoming_events]` : Affiche la liste des 5 prochains événements à venir (avec pagination).

## 🧩 Intégration Oxygen Builder
Pour la page `single` des événements, utilisez un **Code Block** et insérez le contenu du fichier `single-eccla_event-helper.php`. Cela affichera automatiquement le titre, la date formatée, les horaires, le bouton PDF et le contenu de l'article.

## 📁 Structure du Plugin
- `eccla-agenda.php` : Coeur du plugin (CPT, Taxonomies, Shortcodes).
- `assets/css/calendar.css` : Design global.
- `assets/js/calendar.js` : Initialisation FullCalendar et gestion modale.
- `templates/` : Fichiers PHP/HTML pour les vues (Admin, Calendrier, Liste).

## ⚙️ Installation
1. Copiez le dossier `eccla-agenda` dans `/wp-content/plugins/`.
2. Activez-le depuis l'administration.
3. **Important** : Allez dans `Réglages > Permaliens` et cliquez sur "Enregistrer" pour activer le slug `/evenements/`.

---
Auteur : [pixelblank](https://github.com/pixelblank)
Projet : ECCLA 2026
