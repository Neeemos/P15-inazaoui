# CONTRIBUTING.md

Merci de vouloir contribuer au projet **P15-inazaoui** — ton aide est la bienvenue ! 
Ce document t’explique comment installer, tester et contribuer efficacement au projet.

---

## Table des matières

- [Avant de commencer](#avant-de-commencer)
- [Installation et configuration locale](#installation-et-configuration-locale)
- [Commandes Composer disponibles](#commandes-composer-disponibles)
- [Flux Git & branches](#flux-git--branches)
- [Commits (bonnes pratiques)](#commits-bonnes-pratiques)
- [Tests et couverture](#tests-et-couverture)
- [Créer une Pull Request (PR)](#créer-une-pull-request-pr)
- [Issues](#issues)
- [Style de code](#style-de-code)
- [Sécurité](#sécurité)
- [Code of Conduct](#code-of-conduct)
- [Remerciements](#remerciements)

---

## Avant de commencer

1. Vérifie les issues ouvertes pour éviter les doublons.  
2. Si tu veux ajouter une grosse fonctionnalité, ouvre d’abord une issue pour en discuter.  
3. Lis le `README.md` pour comprendre le contexte du projet et les prérequis.

---

## Installation et configuration locale

1. **Forke et clone le projet :**
   ```bash
   git clone https://github.com/TON_UTILISATEUR/P15-inazaoui.git
   cd P15-inazaoui
   ```

2. **Installe les dépendances :**
   ```bash
   composer install
   ```

3. **Configure l’environnement :**
   ```bash
   cp .env .env.local
   # Puis adapte tes variables (base de données, mailer, etc.)
   ```

4. **Initialise la base de données et charge les fixtures :**
   ```bash
   composer database
   ```

5. **Compile les fichiers CSS :**
   ```bash
   composer compileCss
   ```

6. **Lance le serveur de développement :**
   ```bash
   symfony serve
   ```

---

## Commandes Composer disponibles

### `composer database`
> Réinitialise la base de données de développement :  
> - Supprime la base si elle existe  
> - Crée une nouvelle base  
> - Exécute les migrations  
> - Charge les fixtures

### `composer compileCss`
> Minifie et compile le fichier CSS principal du projet :  
> - Entrée : `public/style.css`  
> - Sortie : `public/style.min.css`

### `composer test`
> Exécute les tests unitaires et fonctionnels sur une base de test fraîche :  
> - Drop & recrée la base test  
> - Exécute les migrations et charge les fixtures  
> - Lance PHPUnit

### `composer testCoverage`
> Identique à `composer test` mais génère un rapport de couverture dans `var/coverage`.

---

## Flux Git & branches

- Ne travaille **jamais directement sur `main`**.  
- Crée une branche descriptive selon le type de modification :
  ```bash
  git checkout -b feature/nom-de-la-fonctionnalite
  git checkout -b fix/description-du-bug
  git checkout -b chore/maintenance
  ```

- Synchronise régulièrement avec le dépôt principal :
  ```bash
  git fetch upstream
  git checkout main
  git pull upstream main
  ```

---

## Commits (bonnes pratiques)

- Fais des commits clairs et atomiques.  
- Utilise un format explicite :  
  ```
  type(scope): description courte

  Description plus détaillée si nécessaire.
  ```
  Exemples :
  - `feat(auth): ajout du reset password`
  - `fix(album): corriger la pagination`
- Types recommandés : `feat`, `fix`, `docs`, `refactor`, `style`, `test`, `chore`.

---

## Tests et couverture

Avant toute Pull Request, assure-toi que :  
1. Tous les tests passent :  
   ```bash
   composer test
   ```
2. Tu peux générer un rapport de couverture :  
   ```bash
   composer testCoverage
   ```
3. Aucun `dump()`, `dd()` ou `var_dump()` ne reste dans ton code.

Les résultats de couverture sont accessibles via :  
👉 `var/coverage/index.html`

---

## Créer une Pull Request (PR)

1. Pousse ta branche :  
   ```bash
   git push origin feature/ma-feature
   ```
2. Ouvre une PR vers `Neeemos/P15-inazaoui:main`.
3. Ta PR doit inclure :
   - Une description claire du changement
   - Les étapes pour tester
   - Les issues liées (`Fixes #12`, etc.)
4. Sois prêt à ajuster ton code après la revue.

---

## Issues

Pour signaler un bug ou proposer une amélioration :  
1. Vérifie que l’issue n’existe pas déjà.  
2. Fournis un maximum de détails (étapes, environnement, logs).

---

## Style de code

- Respecte **PSR-12** et les conventions **Symfony**.  
- Utilise `phpstan` avant toute PR :
  ```bash
  ./vendor/bin/phpstan analyse
  ```
- Documente les méthodes publiques importantes avec PHPDoc.
- Ajouter des tests pour toute nouvelle logique
- Mettre à jour la documentation si nécessaire

---

## Sécurité

Si tu découvres une faille de sécurité :  
- Ne la rends **pas publique**.  
- Contacte directement les mainteneurs.

---

## Code of Conduct

Nous souhaitons un espace collaboratif bienveillant et inclusif.  
Les discussions doivent rester respectueuses et constructives.

---

## Remerciements

Merci d’aider à faire évoluer **P15-inazaoui** 

