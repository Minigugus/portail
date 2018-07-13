# Documentation

- [Documentation de l'api](api/)
    - [Utilisateur](api/user/)
    - [Client](api/client/)
- [Documentation du backend](backend/)
    - [Installation](backend/installation.md)
    - [Authentification](backend/oauth.md)
    - [Models](backend/models.md)
    - [Controllers](backend/controllers.md)
    - [Traits](backend/traits.md)
    - [Services](backend/services.md)
    - [Routes](backend/routes.md)
    - [Comment commenter](backend/commenter.md)
- [Documentation du frontend](frontend/)
    - [Installation](frontend/installation.md)
- [Documentation de la documentation](#métadocumentation)

## Liens utiles

- Frontend du Portail des Assos : https://github.com/simde-utc/portail-web
- Documentation Laravel 5.6 : https://laravel.com/docs/5.6

# Métadocumentation

*Comment contribuer à la documentation ?*

***NB :*** *Veillez à également lire le guide pour commenter : [Comment commenter](backend/commenter.md)*

## Forme

Organisation en deux grandes parties, une **documentation pour utiliser l'api** et une **documentation pour le code et l'architecture de l'api** (que l'on appelera documentation du *backend*).

Chaque dossier à son `readme.md` où l'on trouve une *table des matières* qui doit être mise à jour. Celle-ci va jusqu'à une profondeur de 2 :

**Correct :** 

```
- [Lien vers dossier]
    - [Lien vers dossier fils]
    - [Lien vers fichier fils]
```

**Incorrect :**

```
- [Lien vers dossier]
    - [Lien vers dossier fils]
        - [Lien vers fichier petit-fils]
    - [Lien vers fichier fils]
```

## Contenu

### Documentation de l'api

Le but de cette documentation est de montrer aux utilisateurs de l'api comment se servir de celle-ci. On incluera donc des exemples de requêtes et réponses (en json).

Il faut également montrer en différents languages comment intéragir avec l'api. Par exemple, on fournira des exemples de code de connexion à l'api en js, php et python.

### Documentation du backend

Le but étant de documenter l'architecture, les services crées, les controllers, les modèles et autres afin que le projet soit repris par d'autres. Le but n'est pas de refaire la documentation de Laravel, **il faut se concentrer sur ce qui est spécifique à notre projet.**

Quelques exemples :
- Implémentation de Oauth 2.
- Template de controller.
- Système de visibilité.