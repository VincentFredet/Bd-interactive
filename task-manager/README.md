# Task Manager - Gestionnaire de Tâches Laravel

Une application Laravel simple pour gérer des tâches internes d'équipe avec des contextes (projets).

## 🚀 Installation et Configuration

### Prérequis
- PHP 8.1+
- Composer
- SQLite (inclus par défaut)

### Installation

1. **Cloner et installer les dépendances**
```bash
composer install
npm install && npm run build
```

2. **Configuration de l'environnement**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Configuration de la base de données**
```bash
touch database/database.sqlite
php artisan migrate
php artisan db:seed  # Optionnel: données de test
```

4. **Configuration du stockage**
```bash
php artisan storage:link
```

5. **Démarrer l'application**
```bash
php artisan serve
```

L'application sera accessible sur `http://localhost:8000`

## 👤 Comptes de test

Après avoir exécuté `php artisan db:seed`, vous pouvez vous connecter avec :

- **Email**: admin@example.com
- **Mot de passe**: password

ou

- **Email**: john@example.com  
- **Mot de passe**: password

## 🎯 Fonctionnalités

### ✅ Gestion des Tâches
- **Création/modification/suppression** de tâches
- **Statuts** : À faire, En cours, Terminé
- **Priorités** : Faible, Moyenne, Élevée, Urgente
- **Upload d'images** avec prévisualisation
- **Attribution** à des utilisateurs
- **Mise à jour rapide** du statut via dropdown

### 📁 Gestion des Contextes
- **Création de contextes** (équivalent de projets)
- **Filtrage des tâches** par contexte
- Exemples : "Scale Theme", "Tap It", "Vidéos Milo", "Perso"

### 🔐 Authentification
- **Laravel Breeze** intégré
- **Inscription/Connexion** simple
- **Pas de rôles** : tous les utilisateurs peuvent tout voir/modifier

### 🎨 Interface
- **Design responsive** avec Tailwind CSS
- **Interface intuitive** et moderne
- **Filtres visuels** par contexte
- **Badges colorés** pour priorités et statuts

## 📁 Structure du Projet

```
app/
├── Http/Controllers/
│   ├── TaskController.php      # Gestion des tâches
│   └── ContextController.php   # Gestion des contextes
├── Models/
│   ├── Task.php               # Modèle des tâches
│   ├── Context.php            # Modèle des contextes
│   └── User.php               # Modèle des utilisateurs
database/
├── migrations/
│   ├── create_contexts_table.php
│   └── create_tasks_table.php
resources/views/
├── tasks/
│   ├── index.blade.php        # Liste des tâches
│   ├── create.blade.php       # Création de tâche
│   └── edit.blade.php         # Modification de tâche
└── contexts/
    └── create.blade.php       # Création de contexte
```

## 🛠 Utilisation

### Créer un contexte
1. Cliquer sur "Nouveau Contexte"
2. Saisir le nom (ex: "Mon Projet")
3. Valider

### Créer une tâche
1. Cliquer sur "Nouvelle Tâche"
2. Remplir les informations :
   - **Titre** (obligatoire)
   - **Description** (optionnelle)
   - **Statut, Priorité, Contexte, Assigné**
   - **Image** (optionnelle)
3. Valider

### Filtrer les tâches
- Utiliser les boutons de contexte en haut de la liste
- "Tous" affiche toutes les tâches

### Modifier le statut rapidement
- Utiliser le dropdown directement dans la liste
- La modification se fait en temps réel

## 📦 Technologies Utilisées

- **Laravel 11** - Framework PHP
- **Laravel Breeze** - Authentification
- **Tailwind CSS** - Styling
- **SQLite** - Base de données
- **Blade** - Templates

## 🔧 Personnalisation

### Ajouter de nouveaux statuts
Modifier l'enum dans la migration `create_tasks_table.php` :
```php
$table->enum('status', ['todo', 'in_progress', 'done', 'cancelled']);
```

### Ajouter de nouvelles priorités
Modifier l'enum dans la migration :
```php
$table->enum('priority', ['low', 'medium', 'high', 'urgent', 'critical']);
```

### Personnaliser les couleurs des badges
Modifier les méthodes dans `app/Models/Task.php` :
```php
public function getPriorityBadgeClassAttribute(): string
{
    return match($this->priority) {
        'critical' => 'bg-purple-100 text-purple-800',
        // ...
    };
}
```

## 🚀 Déploiement

Pour déployer en production :

1. Configurer les variables d'environnement
2. Utiliser une vraie base de données (MySQL/PostgreSQL)
3. Configurer le stockage des fichiers (S3, etc.)
4. Optimiser avec `php artisan optimize`

---

**Développé avec ❤️ en Laravel**