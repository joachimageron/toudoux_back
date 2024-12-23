

# **Toodoux - Back-End**

Bienvenue dans le dépôt du back-end de l'application **Toodoux**, une application de gestion de tâches conçue pour s'entraîner à développer une application SaaS. Le back-end utilise **Symfony**, un framework PHP robuste et flexible.

---

## **Prérequis**

Avant de commencer, assurez-vous d'avoir les outils suivants installés sur votre machine :

- **PHP** (version 8.1 ou supérieure)
- **Composer** (gestionnaire de dépendances PHP)
- **Symfony CLI** (optionnel mais recommandé)
- **MySQL** ou tout autre SGBD compatible

---

## **Installation**

1. Clonez ce dépôt :

   ```bash
   git clone https://github.com/votre-compte/toodoux-back.git
   cd toodoux-back
   ```

2. Installez les dépendances PHP via Composer :

   ```bash
   composer install
   ```

3. Configurez le fichier `.env` :

    - Copiez le fichier `.env` et renommez-le `.env.local`.
    - Mettez à jour les variables de connexion à la base de données, par exemple :

      ```
      DATABASE_URL="mysql://symfony:symfony@127.0.0.1:3306/toodoux_db"
      ```
      
4. Lancer le conteneur Docker :

   ```bash
   docker-compose up -d
   ```

5. Initialisez la base de données :

   ```bash
   php bin/console doctrine:migrations:migrate
   ```

6. Démarrez le serveur de développement Symfony :

   ```bash
   symfony server:start
   ```

   L'application sera accessible par défaut sur `http://127.0.0.1:8000`.

---

## **Fonctionnalités**

Le back-end de Toodoux offre les fonctionnalités suivantes :

- Authentification des utilisateurs (Inscription, Connexion, Réinitialisation de mot de passe)
- CRUD pour les tâches
- Gestion des priorités, échéances, et marquage des tâches
- Recherche et filtrage des tâches
- API RESTful pour connecter le front-end (Next.js)

---

## **API Endpoints**

| Méthode | Endpoint            | Description                      |
|---------|---------------------|----------------------------------|
| POST    | `/api/register`     | Inscription utilisateur          |
| POST    | `/api/login`        | Connexion utilisateur            |
| GET     | `/api/tasks`        | Récupérer les tâches             |
| POST    | `/api/tasks`        | Créer une nouvelle tâche         |
| PUT     | `/api/tasks/{id}`   | Mettre à jour une tâche existante|
| DELETE  | `/api/tasks/{id}`   | Supprimer une tâche              |

---

## **Structure des Dossiers**

Voici une description rapide des principaux dossiers du projet :

- `src/`: Contient le code source principal.
    - `Controller/`: Contrôleurs pour gérer les requêtes HTTP.
    - `Entity/`: Entités Doctrine représentant les modèles de base de données.
    - `Repository/`: Gestion des requêtes spécifiques pour chaque entité.
- `config/`: Fichiers de configuration Symfony.
- `migrations/`: Scripts de migration de base de données.

---

## **Schéma de la Base de Données**

Le schéma de base de données actuel est vide. Voici un exemple simplifié qui pourrait être utilisé pour ce projet :

## Tables principales

### 1. `users`
| Colonne          | Type           | Null | Clé   | Description                       |
|-------------------|----------------|------|-------|-----------------------------------|
| `id`             | INT           | NON  | PK    | Identifiant unique de l'utilisateur |
| `email`          | VARCHAR(255)  | NON  | UNIQUE | Adresse email de l'utilisateur    |
| `password`       | VARCHAR(255)  | NON  |       | Mot de passe hashé                |
| `name`           | VARCHAR(100)  | NON  |       | Nom de l'utilisateur              |
| `created_at`     | DATETIME       | NON  |       | Date de création du compte        |
| `updated_at`     | DATETIME       | NON  |       | Date de la dernière modification  |

---

### 2. `tasks`
| Colonne          | Type           | Null | Clé   | Description                          |
|-------------------|----------------|------|-------|--------------------------------------|
| `id`             | INT           | NON  | PK    | Identifiant unique de la tâche       |
| `user_id`        | INT           | NON  | FK    | Référence à l'utilisateur (table `users`) |
| `title`          | VARCHAR(255)  | NON  |       | Titre de la tâche                    |
| `description`    | TEXT          | OUI  |       | Description détaillée de la tâche    |
| `priority`       | ENUM('low', 'medium', 'high') | NON  | | Niveau de priorité                  |
| `due_date`       | DATETIME       | OUI  |       | Date d'échéance                      |
| `is_completed`   | BOOLEAN       | NON  |       | Statut d'accomplissement             |
| `created_at`     | DATETIME       | NON  |       | Date de création de la tâche         |
| `updated_at`     | DATETIME       | NON  |       | Date de la dernière modification     |

---

### 3. `categories`
| Colonne          | Type           | Null | Clé   | Description                       |
|-------------------|----------------|------|-------|-----------------------------------|
| `id`             | INT           | NON  | PK    | Identifiant unique de la catégorie |
| `user_id`        | INT           | NON  | FK    | Référence à l'utilisateur (table `users`) |
| `name`           | VARCHAR(100)  | NON  |       | Nom de la catégorie                |
| `created_at`     | DATETIME       | NON  |       | Date de création de la catégorie   |
| `updated_at`     | DATETIME       | NON  |       | Date de la dernière modification   |

---

### 4. `task_categories` (table de liaison)
| Colonne          | Type           | Null | Clé   | Description                       |
|-------------------|----------------|------|-------|-----------------------------------|
| `task_id`        | INT           | NON  | FK    | Référence à une tâche (table `tasks`) |
| `category_id`    | INT           | NON  | FK    | Référence à une catégorie (table `categories`) |

---

### 5. `tags`
| Colonne          | Type           | Null | Clé   | Description                       |
|-------------------|----------------|------|-------|-----------------------------------|
| `id`             | INT           | NON  | PK    | Identifiant unique du tag         |
| `user_id`        | INT           | NON  | FK    | Référence à l'utilisateur (table `users`) |
| `name`           | VARCHAR(100)  | NON  | UNIQUE | Nom du tag                        |
| `created_at`     | DATETIME       | NON  |       | Date de création du tag           |
| `updated_at`     | DATETIME       | NON  |       | Date de la dernière modification  |

---

### 6. `task_tags` (table de liaison)
| Colonne          | Type           | Null | Clé   | Description                       |
|-------------------|----------------|------|-------|-----------------------------------|
| `task_id`        | INT           | NON  | FK    | Référence à une tâche (table `tasks`) |
| `tag_id`         | INT           | NON  | FK    | Référence à un tag (table `tags`) |

---

### 7. `notifications`
| Colonne          | Type           | Null | Clé   | Description                       |
|-------------------|----------------|------|-------|-----------------------------------|
| `id`             | INT           | NON  | PK    | Identifiant unique de la notification |
| `user_id`        | INT           | NON  | FK    | Référence à l'utilisateur (table `users`) |
| `task_id`        | INT           | OUI  | FK    | Référence à une tâche (table `tasks`) |
| `message`        | VARCHAR(255)  | NON  |       | Message de la notification         |
| `is_read`        | BOOLEAN       | NON  |       | Statut de lecture                  |
| `created_at`     | DATETIME       | NON  |       | Date de création de la notification |

---

### 8. `shared_tasks`
| Colonne          | Type           | Null | Clé   | Description                       |
|-------------------|----------------|------|-------|-----------------------------------|
| `id`             | INT           | NON  | PK    | Identifiant unique                |
| `task_id`        | INT           | NON  | FK    | Référence à une tâche (table `tasks`) |
| `owner_id`       | INT           | NON  | FK    | Référence au propriétaire (table `users`) |
| `shared_with_id` | INT           | NON  | FK    | Référence à l'utilisateur avec qui c'est partagé |
| `permissions`    | ENUM('read', 'write') | NON  | | Permissions accordées             |
| `created_at`     | DATETIME       | NON  |       | Date de création du partage       |

---

## Relations entre les tables
- **`users`** (1:N) **`tasks`**
- **`users`** (1:N) **`categories`**
- **`users`** (1:N) **`tags`**
- **`tasks`** (M:N) **`categories`** (via `task_categories`)
- **`tasks`** (M:N) **`tags`** (via `task_tags`)
- **`users`** (1:N) **`notifications`**
- **`tasks`** (1:N) **`shared_tasks`**

---

Ce schéma peut être modifié selon les besoins spécifiques de votre application ! 😊


> Ce schéma peut évoluer en fonction des fonctionnalités que vous ajoutez.

---
