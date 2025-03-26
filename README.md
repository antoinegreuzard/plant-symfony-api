# 🚀 Plant Care API (Symfony 7)

Une API Symfony 7 (PHP 8.4) pour gérer un catalogue personnalisé de plantes et leurs photos associées.

---

## 📆 Prérequis

- PHP >= 8.4
- Composer
- SQLite ou MySQL

---

## ⚙️ Installation

1. **Cloner le projet**
   ```bash
   git clone https://github.com/antoinegreuzard/plant-symfony-api
   cd plant-symfony-api
   ```

2. **Installer les dépendances PHP**
   ```bash
   composer install
   ```

3. **Configurer l’environnement**
   ```bash
   cp .env .env.local
   ```

4. **Configurer la base de données**
    - Modifier `.env.local` pour utiliser SQLite (ou MySQL)
    - Exemple SQLite :
      ```dotenv
      DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
      ```

   Puis :
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   php bin/console doctrine:fixtures:load
   ```

5. **Lancer le serveur**
   ```bash
   symfony server:start
   # ou
   php -S 127.0.0.1:8000 -t public
   ```

L’API est accessible via `http://127.0.0.1:8000/api/`

---

## 🔐 Authentification (JWT)

L’API utilise `lexik/jwt-authentication-bundle` pour l’authentification via JWT.

| Méthode | Endpoint              | Description                |
|---------|-----------------------|----------------------------|
| POST    | `/api/register/`      | Enregistrer un utilisateur |
| POST    | `/api/token/`         | Obtenir un token JWT       |
| POST    | `/api/token/refresh/` | Rafraîchir un token JWT    |

👉 Ajouter le token dans l'en-tête :

```
Authorization: Bearer {token}
```

---

## 🌿 Gestion des plantes

| Méthode | Endpoint            | Description                   |
|---------|---------------------|-------------------------------|
| GET     | `/api/plants/`      | Liste paginée des plantes     |
| POST    | `/api/plants/`      | Ajouter une nouvelle plante   |
| GET     | `/api/plants/{id}/` | Récupérer une plante          |
| PUT     | `/api/plants/{id}/` | Modifier une plante existante |
| DELETE  | `/api/plants/{id}/` | Supprimer une plante          |

⚠️ Les routes avec ou sans **trailing slash** (`/`) fonctionnent toutes les deux.

---

## 📷 Photos de plantes

| Méthode | Endpoint                         | Description                           |
|---------|----------------------------------|---------------------------------------|
| POST    | `/api/plants/{id}/upload-photo/` | Ajouter une photo à une plante        |
| GET     | `/api/plants/{id}/photos/`       | Voir les photos associées à la plante |

---

## 🧪 Tests

Le projet utilise **PHPUnit** avec la surcouche **Pest**.

### Lancer tous les tests

```bash
php bin/phpunit
# ou
vendor/bin/pest
```

---

## 🛠 CI/CD avec GitHub Actions

Le projet est configuré pour :

- Lancer les tests (PHPUnit / Pest)
- Vérifier les migrations
- Vérifier le code avec PHPStan / Lint Symfony

---

## 📜 Licence

Ce projet est sous licence **MIT**. 📄

---

Développé avec ❤️ par [Antoine Greuzard](https://github.com/antoinegreuzard)
