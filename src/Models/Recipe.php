<?php

namespace Ghost\Models;

use Ghost\Exception\ValidationException;
use PDO;

class Recipe
{

    private PDO $pdo;

    public function __construct (PDO $pdo) {

        $this->pdo = $pdo;
    }

    public function getAll() {

        $query = $this->pdo->query('select id, name, slug, picture, body from recipes');
        $recipes = $query->fetchAll();

        $query = $this->pdo->prepare('select id, name, barcode, quantity, recipe_id from ingredients where recipe_id = :recipe_id');

        for($i=0;$i < count($recipes); $i++) {
            $query->execute([
                'recipe_id' => $recipes[$i]['id']
            ]);
            $recipes[$i]['ingredients'] = $query->fetchAll();
        }

        return $recipes;

    }

    public function getById ($recipe_id) {
        $query = $this->pdo->prepare('select id, name, slug, picture, body from recipes where id = :id');
        $query->execute(['id' => $recipe_id]);
        $recipes = $query->fetchAll();

        $query = $this->pdo->prepare('select id, name, barcode, quantity, recipe_id from ingredients where recipe_id = :recipe_id');
        for($i=0;$i < count($recipes); $i++) {
            $query->execute([
                'recipe_id' => $recipes[$i]['id']
            ]);
            $recipes[$i]['ingredients'] = $query->fetchAll();
        }

        return $recipes;
    }

    public function create ($data) {

        try {

            $this->pdo->beginTransaction();

            $query = $this->pdo->prepare('insert into recipes (name, slug, picture, body) VALUE (:name, :slug, :picture, :body)');

            $query->execute([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'picture' => $data['picture'],
                'body' => $data['body']
            ]);

            $recipe_id = $this->pdo->lastInsertId();

            $query = $this->pdo->prepare('insert into ingredients (barcode, name, quantity, recipe_id) VALUE (:barcode, :name,  :quantity, :recipe_id)');

            for ($i=0; $i<count($data['i_barcode']); $i++) {
                $query->execute([
                    'barcode' => $data['i_barcode'][$i],
                    'name' => $data['i_name'][$i],
                    'quantity' => $data['i_quantity'][$i],
                    'recipe_id' => $recipe_id
                ]);
            }

            $this->pdo->commit();

        } catch (ValidationException $e) {
            $this->pdo->rollBack();
            throw $e;
        }

    }

}