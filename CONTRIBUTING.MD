# CONTRIBUTING.md

Merci de vouloir contribuer au projet **P15-inazaoui** — toute aide est la bienvenue !  
Ce document explique comment installer, tester et contribuer efficacement au projet.

---

## Table des matières

- [Avant de commencer](#avant-de-commencer)  
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

1. Vérifier les issues ouvertes pour éviter les doublons.  
2. Ouvrir une issue avant d’ajouter une nouvelle fonctionnalité afin d’en discuter.  
3. Lire le fichier `README.md` pour comprendre le contexte du projet et les prérequis.

---

## Commandes Composer disponibles

### `composer database`
> Réinitialiser la base de données de développement :  
> - Supprimer la base si elle existe  
> - Créer une nouvelle base  
> - Exécuter les migrations  
> - Charger les fixtures  

### `composer compileCss`
> Minifier et compiler le fichier CSS principal du projet :  
> - Entrée : `public/style.css`  
> - Sortie : `public/style.min.css`  

### `composer test`
> Exécuter les tests unitaires et fonctionnels sur une base de test fraîche :  
> - Supprimer et recréer la base de test  
> - Exécuter les migrations et charger les fixtures  
> - Lancer PHPUnit  

### `composer testCoverage`
> Exécuter les tests et générer un rapport de couverture dans `var/coverage`.

---

## Flux Git & branches

- Ne jamais travailler directement sur `main`.  
- Créer une branche descriptive selon le type de modification :
  ```bash
  git checkout -b feature/nom-de-la-fonctionnalite
  git checkout -b fix/description-du-bug
  git checkout -b chore/maintenance
  ```

- Synchroniser régulièrement avec le dépôt principal :
  ```bash
  git fetch upstream
  git checkout main
  git pull upstream main
  ```

---

## Commits (bonnes pratiques)

- Faire des commits clairs et atomiques.  
- Utiliser un format explicite :  
  ```
  type(scope): description courte

  Description plus détaillée si nécessaire.
  ```
  Exemples :  
  - `feat(auth): ajouter le reset password`  
  - `fix(album): corriger la pagination`  
- Utiliser les types suivants : `feat`, `fix`, `docs`, `refactor`, `style`, `test`, `chore`.

---

## Tests et couverture

Avant toute Pull Request, s’assurer que :  
1. Faire passer tous les tests :  
   ```bash
   composer test
   ```
2. Générer un rapport de couverture :  
   ```bash
   composer testCoverage
   ```
3. Supprimer tout `dump()`, `dd()` ou `var_dump()` restant dans le code.

Consulter les résultats de couverture via :  
👉 `var/coverage/index.html`

---

## Créer une Pull Request (PR)

1. Pousser la branche :  
   ```bash
   git push origin feature/ma-feature
   ```
2. Ouvrir une PR vers `Neeemos/P15-inazaoui:main`.  
3. Inclure dans la PR :  
   - Une description claire du changement  
   - Les étapes pour tester  
   - Les issues liées (`Fixes #12`, etc.)  
   - L’assignation d’une revue de la PR  
4. Être prêt à ajuster le code après la revue.

---

## Issues

Pour signaler un bug ou proposer une amélioration :  
1. Vérifier que l’issue n’existe pas déjà.  
2. Fournir un maximum de détails (étapes, environnement, logs).

---

## Style de code

- Respecter **PSR-12** et les conventions **Symfony**.  
- Utiliser `phpstan` avant toute PR :  
  ```bash
  ./vendor/bin/phpstan analyse
  ```
- Documenter les méthodes publiques importantes avec PHPDoc.  
- Ajouter des tests pour toute nouvelle logique.  
- Mettre à jour la documentation si nécessaire.

---

## Sécurité

En cas de découverte d’une faille de sécurité :  
- Ne pas la rendre publique.  
- Contacter directement les mainteneurs.

---

## Code of Conduct

Maintenir un espace collaboratif bienveillant et inclusif.  
Garder des discussions respectueuses et constructives.

---

## Remerciements

Merci d’aider à faire évoluer **P15-inazaoui** 
