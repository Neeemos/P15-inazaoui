# 📸 P15 – Inzaoui

> **P15 – Inzaoui** est un site web de photographe collaboratif développé avec **Symfony**.  
> Il permet de créer et gérer des albums photo, tout en offrant la possibilité à plusieurs **invités (“guests”)** de publier leurs propres photos sur des albums partagés.

---

## 🖼️ Aperçu

![Capture d’écran du site](https://i.imgur.com/Ijzb25G.png)

---

## 🚀 Fonctionnalités principales

- 📷 **Albums photo** : création, édition et affichage d’albums.  
- 👥 **Invités** : possibilité d’ajouter des “guests” qui peuvent uploader leurs photos.  
- 🖼️ **Gestion des médias** : upload, stockage et affichage des images.  
- 🔐 **Authentification** : espace sécurisé pour le photographe principal.  
- 💬 **Interface intuitive** : design épuré et responsive.  
- ⚙️ **Administration** : gestion des utilisateurs, albums et contributions.

---

## 🧱 Stack technique

- **Framework** : Symfony 7.x  
- **Langage** : PHP 8.2+  
- **Base de données** : PostgreSQL  
- **ORM** : Doctrine  
- **Front-end** : Twig + CSS (minifié via `composer run compileCss`)  
- **Tests** : PHPUnit  
- **Analyse statique** : PHPStan  

---

## 🛠️ Installation du projet

### 1. Cloner le dépôt

```bash
git clone https://github.com/Neeemos/P15-inazaoui.git
cd P15-inazaoui
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Créer ton fichier d’environnement

```bash
cp .env .env.local
```

➡️ Configure ensuite tes variables selon ton environnement.

### 4. Initialiser la base de données

```bash
composer run database
```

Cette commande :
- supprime la base si elle existe,
- crée une nouvelle base,
- exécute les migrations,
- charge les fixtures.

### 5. Compiler les fichiers CSS

```bash
composer run compileCss
```

### 6. Lancer le serveur

```bash
symfony serve
```

---

## 🧪 Tests et qualité du code

### Lancer la suite de tests

```bash
composer run test
```

Cette commande :
- réinitialise la base de test,
- exécute les migrations et charge les fixtures,
- lance PHPUnit.

### Générer un rapport de couverture

```bash
composer run testCoverage
```

➡️ Le rapport est généré dans `var/coverage/index.html`.

### Analyse statique

```bash
./vendor/bin/phpstan analyse
```

---

## 🧭 Structure du projet

```
P15-inazaoui/
├── config/                # Configuration symfony
├── migrations/            # Scripts de migration Doctrine
├── public/                # Point d’entrée du site
│   ├── images/            # Images structurel du site
│   ├── uploads/           # Répertoire de réception des images upload
│   ├── style.css
│   └── style.min.css
├── src/                   # Code source Symfony (Controllers, Entities, Services…)
├── templates/             # Templates Twig
├── tests/                 # Tests unitaires et fonctionnels
├── var/                   # Cache, logs, rapport de couverture…
├── .env, .env.local       # Configuration d’environnement
└── composer.json
```

---

## 🧩 Commandes utiles (Composer)

| Commande | Description |
|-----------|-------------|
| `composer run database` | Réinitialise et peuple la base de données |
| `composer run compileCss` | Minifie le CSS principal |
| `composer run test` | Lance les tests unitaires et fonctionnels |
| `composer run testCoverage` | Lance les tests avec couverture de code |

---

## 🤝 Contribuer

Les contributions sont les bienvenues !  
Merci de lire le [CONTRIBUTING.md](./CONTRIBUTING.md) pour connaître le workflow de contribution, les bonnes pratiques Git et les normes de code à suivre.

---

## ⚠️ Sécurité

Si tu découvres une faille de sécurité, **ne la publie pas publiquement**.  
Contacte les mainteneurs ou ouvre une issue privée marquée `security`.

---

## 💬 Remerciements

Merci à toutes les personnes qui contribuent à faire évoluer **P15 – Inzaoui** !


---

> _Développé avec Symfony et passion par [Neeemos](https://github.com/Neeemos)._ 

