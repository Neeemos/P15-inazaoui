# Ina Zaoui

Pour se connecter avec le compte de Ina, il faut utiliser les identifiants suivants:
- identifiant : `ina`
- mot de passe : `password`

Vous trouverez dans le fichier `backup.zip` un dump SQL anonymisé de la base de données et toutes les images qui se trouvaient dans le dossier `public/uploads`.
Faudrait peut être trouver une meilleure solution car le fichier est très gros, il fait plus de 1Go.


# Tools d'analyse : 

PhpStan analyse du code 
```vendor/bin/phpstan analyse```

# Test case 
* Authentification
``` - Login sucess (email:password valid)
    - Login fail (email:password wrong)
```

* Media
```- J’ai accès au CRUD des médias (boutons, routes, affichage des lignes avec pagination, affichage de formulaires valides)
    - Ajout d'un média (success : user:album:titre:média, affichage du média sur la page post-ajout + bdd)
    - Ajout d'un média (error : user:album:titre:média, vérification affichage erreur)
    - Modification d'un média (success : user:album:titre:média, vérification des valeurs post-ajout bdd/page)
    - Modification d'un média (error : user:album:titre:média, vérification affichage erreur)
    - Suppression d'un média (vérification post-suppression bdd + page + path files)
```
* Album 
```
-   J’ai accès au CRUD des album (boutons, routes, affichage des lignes avec pagination, affichage de formulaires valides)
    - Ajout d'un Album (success : Nom, affichage du média sur la page post-ajout + bdd)
    - Ajout d'un Album (error : Aucun nom, vérification affichage erreur)
    - Modification d'un Album (success : Nom, vérification des valeurs post-ajout bdd/page)
    - Modification d'un Album (error :  nom vide, vérification affichage erreur)
    - Suppression d'un Album (vérification post-suppression cascade bdd + page )
    ```
 * Invités 
```
-   J’ai accès au CRUD des invités (boutons, routes, affichage des lignes avec pagination, affichage de formulaires valides)
    - Ajout d'un invités (success : Nom:email;password:description:grade affichage de l'invité sur la page post-ajout + bdd)
    - Ajout d'un invités (error : Nom:email;password:description:grade, vérification affichage erreur)
    - Modification d'un invités (success : Nom:email;password:description:grade, vérification des valeurs post-modif bdd/page)
    - Modification d'un invités (error : Nom:email;password:description:grade vide, vérification affichage erreur)
    - Suppression d'un invités (vérification post-suppression cascade bdd + page )
    ```