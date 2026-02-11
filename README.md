# 📘 README Technique – API Books (Symfony 7 + API Platform 4)

## 🧩 Architecture générale

Ce projet implémente une API REST basée sur **Symfony 7** et **API Platform 4**, avec une gestion avancée et sécurisée des fichiers (upload, validation, suppression).  
L’API expose un CRUD complet sur l’entité `Book` ainsi qu’un endpoint dédié à l’upload d’une image de couverture.  
L’upload et la suppression sont gérés via des **Processors API Platform**, conformément aux bonnes pratiques de la version 4.

## ⚙️ Fonctionnalités techniques

### 🔹 Upload d’image sécurisé

- Formats autorisés : **JPEG** et **PNG**
- Taille maximale : **5 Mo**
- Vérification du type MIME réel (`getMimeType()`)
- Génération d’un nom de fichier unique
- Stockage dans `public/uploads/books/`
- Suppression automatique de l’ancienne image lors d'un update

### 🔹 Suppression propre

Lors d’un `DELETE /books/{id}` :

- l’entité est supprimée
- l’image associée est supprimée du disque

### 🔹 Processors API Platform

Deux processors dédiés :

- **UploadProcessor** : validation, sécurité, déplacement du fichier, mise à jour de l’entité
- **DeleteProcessor** : suppression du fichier associé lors de la suppression du livre

## 🛠️ Installation & exécution

```bash
composer install
symfony console doctrine:database:create
symfony console doctrine:migrations:migrate
symfony server:start

```

# 🚀 Améliorations techniques possibles (version condensée)

Axes d’évolution envisageables pour renforcer la robustesse, la performance et l’évolutivité de l’API :

### 🌍 URL absolue pour les images

Exposer une URL complète (`https://domaine.com/uploads/...`) plutôt qu’un chemin relatif.  
Implémentation via une variable d’environnement (`APP_URL`) et un service ou normalizer dédié.

### 🧩 Normalizer personnalisé

Créer un normalizer pour enrichir automatiquement les réponses API (ex : `coverImageUrl`, métadonnées, miniature).  
Permet de garder l’entité pure et d’éviter toute logique HTTP dans le domaine métier.

### ☁️ Intégration CDN

Externaliser le stockage des images vers un CDN (Cloudflare, AWS S3, OVH Object Storage).  
Avantages : meilleure performance, réduction de la charge serveur, URLs signées, cache distribué.

### 🖼️ Thumbnails

Générer automatiquement des miniatures à l’upload (ex : 200×200).  
Outils possibles : `liip/imagine-bundle`, `intervention/image`.  
Permet d’optimiser les listes et d’améliorer l’expérience frontend.

### 🗜️ Compression & optimisation

Optimiser les images uploadées :

- compression JPEG
- conversion PNG → WebP
- réduction automatique de la résolution
- optimisation via `imagemagick` ou `spatie/image-optimizer`  
  Objectif : réduire la taille des fichiers, accélérer le chargement et économiser du stockage.
