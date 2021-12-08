ToDoList
========

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/3ddf1c35d3cb4c9b84a435009274460a)](https://app.codacy.com/gh/Mamoon97150/Todolist?utm_source=github.com&utm_medium=referral&utm_content=Mamoon97150/Todolist&utm_campaign=Badge_Grade_Settings)
[Lien vers la page html de code coverage](/app/web/test-coverage/index.html)

Base du projet #8 : Améliorez un projet existant

https://openclassrooms.com/projects/ameliorer-un-projet-existant-1
***

## Environnement de développement

### Pré-requis
-   PHP 8
-   Mysql 8.0
-   Symfony 5.3
-   Symfony CLI
-   Docker
-   Docker-compose

Vous pouvez vérifier les pré-requis de symfony avec la commande suivante (de la CLI de Symfony)

```bash
 symfony check:requirements
```

### Lancer l'environnement de développement

####Avec Docker

Créez un dossier mysql à la racine du projet. Puis entrez les commande suivante :
```
    docker-compose up -d
```

L'environnement de developpement est lancé !!

#### Sans Docker

```
    cd app
    symfony serve
```

### Ajouter les dépendances

Ouvrez un terminal a la racine du projet et exécutez les commandes suivantes :

```
    cd app
    composer install
```

### Installer la base de données
Mettez à jour le fichier .env et lancé la ligne de commande suivante:
```
    composer prepare
```

### Lancer les test

#### Sans Docker
Dans le terminal, lancé les commandes suivantes afin d'exécuter tous les tests
```
    cd app
    php ./vendor/bin/phpunit
```

Pour un test en particulier lancé par exemple:
```
    php ./vendor/bin/phpunit tests/Controller/UserControllerTest.php
```

#### Avec Docker

Rendez vous dans container et exécuter les ligne de commandes phpunit.

```
    docker run CONTAINER
    php ./vendor/bin/phpunit
```

**_Si vous avez PhpStorm :_
- Créez un interprète PHP avec le service php8-todolist
- Ajoutez en variable d'environnement "KERNEL_CLASS=App\Kernel"
- En cas d'échec, dans chaque classe de test modifié le fichier de configuration en y ajoutant la variable d'environnement**
