# **Toodoux - Back-End**

Bienvenue dans le dépôt du back-end de l'application **Toodoux**, une application de gestion de tâches conçue pour
s'entraîner à développer une application SaaS. Le back-end utilise **Symfony**, un framework PHP robuste et flexible.
Réalisé par Joachim Ageron et Sandara Ly.

Le front-end de l'application est disponible **[ici](https://github.com/joachimageron/toudoux_front.git)**. 
Il a été réalisé en **[Next](https://nextjs.org/) et [NextUI](https://nextui.org/)**.

**Attention** : toutes les fonctionnalités ne sont pas encore implémentées.

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

## Principaux Endpoints de l’API

Cette API expose quatre ressources principales : **User**, **Task**, **Category** et **ImportData**.  
Ci-dessous, vous trouverez la liste des endpoints générés ou attendus via **ApiPlatform** en fonction des annotations présentes dans chaque entité.

---

### 1. Endpoints pour **User**

| Méthode | Endpoint          | Description                                                      |
|---------|-------------------|------------------------------------------------------------------|
| GET     | `/api/users`      | Récupérer la **liste** de tous les utilisateurs                  |
| POST    | `/api/users`      | **Créer** un nouvel utilisateur                                  |
| GET     | `/api/users/{id}` | Récupérer un **utilisateur** en particulier                      |
| PUT     | `/api/users/{id}` | **Remplacer** entièrement les données d’un utilisateur           |
| PATCH   | `/api/users/{id}` | **Mettre à jour partiellement** un utilisateur                   |
| DELETE  | `/api/users/{id}` | **Supprimer** un utilisateur                                     |

---

### 2. Endpoints pour **Task**

| Méthode | Endpoint          | Description                                                                |
|---------|-------------------|----------------------------------------------------------------------------|
| GET     | `/api/tasks`      | Récupérer la **liste** de toutes les tâches                                |
| POST    | `/api/tasks`      | **Créer** une nouvelle tâche                                               |
| GET     | `/api/tasks/{id}` | Récupérer une **tâche** en particulier                                     |
| PUT     | `/api/tasks/{id}` | **Remplacer** entièrement les données d’une tâche                          |
| PATCH   | `/api/tasks/{id}` | **Mettre à jour partiellement** une tâche                                  |
| DELETE  | `/api/tasks/{id}` | **Supprimer** une tâche                                                    |

---

### 3. Endpoints pour **Category**

| Méthode | Endpoint               | Description                                                                |
|---------|------------------------|----------------------------------------------------------------------------|
| GET     | `/api/categories`      | Récupérer la **liste** de toutes les catégories                            |
| POST    | `/api/categories`      | **Créer** une nouvelle catégorie                                           |
| GET     | `/api/categories/{id}` | Récupérer une **catégorie** en particulier                                 |
| PUT     | `/api/categories/{id}` | **Remplacer** entièrement les données d’une catégorie                      |
| PATCH   | `/api/categories/{id}` | **Mettre à jour partiellement** une catégorie                              |
| DELETE  | `/api/categories/{id}` | **Supprimer** une catégorie                                                |

---

### 4. Endpoints pour **ImportData**

Cette ressource ne dispose actuellement que des opérations **GET (item)**, **GET (collection)** et **POST**. Aucun endpoint `PUT`, `PATCH` ou `DELETE` n’est défini.

| Méthode | Endpoint                  | Description                                                                         |
|---------|---------------------------|-------------------------------------------------------------------------------------|
| GET     | `/api/import_datas`       | Récupérer la **liste** de tous les imports déjà effectués                           |
| POST    | `/api/import_datas`       | **Importer** de nouvelles données (par exemple depuis Google Keep)                  |
| GET     | `/api/import_datas/{id}`  | Récupérer un **import** en particulier (pour consulter son état, son log, etc.)     |

---

### Notes complémentaires

- L’URL de base peut varier selon votre configuration : par défaut, il s’agit de `/api/`.
- ApiPlatform génère également une documentation OpenAPI accessible via `/api/docs`.
- Les paramètres de pagination, de filtrage ou de tri peuvent être configurés et utilisés sous forme de query parameters (ex: `?page=2`, `?title=…`, etc.), si vous activez les fonctionnalités correspondantes.
---

## **Schéma de la Base de Données**

Le schéma de base de données actuel est vide. Voici un exemple simplifié qui pourrait être utilisé pour ce projet :

## Tables principales

### Table `users`

| Colonne                    | Type          | Null | Clé    | Description                                      |
|----------------------------|---------------|------|--------|--------------------------------------------------|
| **id**                     | INT           | NON  | PK     | Identifiant unique de l'utilisateur              |
| **email**                  | VARCHAR(180)  | NON  | UNIQUE | Adresse email de l'utilisateur                   |
| **password**               | VARCHAR(255)  | NON  |        | Mot de passe hashé                               |
| **roles**                  | JSON          | NON  |        | Rôles attribués à l’utilisateur (tableau JSON)   |
| **reset_token**            | VARCHAR(255)  | OUI  |        | Token pour réinitialiser le mot de passe         |
| **reset_token_expires_at** | DATETIME      | OUI  |        | Date d’expiration du token                       |

### Table `categories`

| Colonne        | Type          | Null | Clé    | Description                                   |
|----------------|---------------|------|--------|-----------------------------------------------|
| **id**         | INT           | NON  | PK     | Identifiant unique de la catégorie            |
| **user_id**    | INT           | NON  | FK     | Référence à l’utilisateur (table `users`)     |
| **name**       | VARCHAR(50)   | NON  |        | Nom de la catégorie                           |
| **slug**       | VARCHAR(50)   | NON  | UNIQUE | Slug unique de la catégorie                   |
| **description**| VARCHAR(250)  | OUI  |        | Description de la catégorie                   |
| **color**      | VARCHAR(50)   | OUI  |        | Couleur associée à la catégorie               |
| **created_at** | DATETIME      | NON  |        | Date de création de la catégorie              |
| **updated_at** | DATETIME      | NON  |        | Date de la dernière modification              |

### Table `tasks`

| Colonne         | Type             | Null | Clé  | Description                                      |
|-----------------|------------------|------|------|--------------------------------------------------|
| **id**          | INT              | NON  | PK   | Identifiant unique de la tâche                   |
| **category_id** | INT              | NON  | FK   | Référence à la catégorie (table `categories`)    |
| **title**       | VARCHAR(50)      | NON  |      | Titre de la tâche                                |
| **description** | VARCHAR(255)     | OUI  |      | Description détaillée de la tâche                |
| **due_date**    | DATETIME         | OUI  |      | Date d’échéance                                  |
| **done**        | BOOLEAN          | NON  |      | Statut d’accomplissement de la tâche             |
| **priority**    | SMALLINT         | OUI  |      | Priorité (1=haute, 2=moyenne, 3=basse, etc.)     |
| **created_at**  | DATETIME         | NON  |      | Date de création de la tâche                     |
| **updated_at**  | DATETIME         | NON  |      | Date de la dernière modification                 |

### Table `import_data`

| Colonne        | Type          | Null | Clé  | Description                                                        |
|----------------|---------------|------|------|--------------------------------------------------------------------|
| **id**         | INT           | NON  | PK   | Identifiant unique de l'import                                     |
| **user_id**    | INT           | NON  | FK   | Référence à l'utilisateur (table `users`)                          |
| **name**       | VARCHAR(255)  | NON  |      | Nom de l'import                                                    |
| **created_at** | DATETIME      | NON  |      | Date de création de l’import (stockée en `DateTimeImmutable`)      |
| **status**     | VARCHAR(50)   | NON  |      | Statut de l'import (pending, success, error, etc.)                 |
| **item_number**| INT           | NON  |      | Nombre d'éléments traités par l'import                             |
| **log**        | VARCHAR(1000) | OUI  |      | Journal/trace de l’import                                          |
| **data**       | TEXT          | OUI  |      | Données JSON originales (contenu de l'import)                      |

## Relations entre les tables

- **`categories`** (1 : N) **`tasks`**  
  Une catégorie peut contenir plusieurs tâches (chaque tâche référence une catégorie).

- **`users`** (1 : N) **`categories`**  
  Un utilisateur peut posséder plusieurs catégories (chaque catégorie référence un utilisateur).

- **`users`** (1 : N) **`import_data`**  
  Un utilisateur peut créer/importer plusieurs jeux de données (chaque import référence un utilisateur).

