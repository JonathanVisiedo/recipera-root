Pour lancer le projet
```shell
docker compose up
```

PHP7.4 - MYSQL8

### credentials DB
* 'host' => **'localhost'** (hors du container) / **'mysql'** (dans le container)
* 'database' => 'db',
* 'username' => 'user',
* 'password' => 'password'


### Logique

1. controller : src/Controllers/DisplayController.php (logique d'implémentation)
2. Service : src/Services/FoodApiService.php (Outils de gestion générique)
3. Model : src/Models/Recipe.php (Model de connexion db)

Je part comme dit à l'entretient du principe que les codes barres sont tous encodé, si ils ne sont pas existant
une validation sommaire aura tout de même lieux lors de la création.

