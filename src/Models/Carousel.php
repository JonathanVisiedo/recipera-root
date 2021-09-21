<?php


namespace Ghost\Models;


use PDO;

class Carousel
{

    private PDO $PDO;
    
    /**
     * TagController constructor.
     * @param PDO $PDO
     */
    public function __construct(PDO $PDO)
    {
        $this->PDO = $PDO;
    }

    public function getAll()
    {
        $query = "SELECT id, title, content, cta, link, image FROM carousel ORDER BY id DESC;";
        $req = $this->PDO->query($query);
        $return = $req->fetchAll();
        $req->closeCursor();
        return $return;
    }


    public function getById(int $id)
    {
        $query = "SELECT id, title, content, cta, link, image FROM carousel WHERE id = :id;";
        $req = $this->PDO->prepare($query);
        $req->execute([
            'id' => $id
        ]);
        $return = $req->fetch();
        $req->closeCursor();
        return $return;
    }

    public function createCarousel(array $data): void
    {

        $query = "INSERT INTO carousel (title, content, cta, link, image) 
        VALUE (:title, :content, :cta, :link, :image)";
        $req = $this->PDO->prepare($query);
        $req->execute([
            'title' => $data['title'],
            'content' => $data['content'],
            'cta' => $data['cta'],
            'link' => $data['link'],
            'image' => $data['image']
        ]);
        $req->closeCursor();
    }

    public function updateCarousel(array $data): void
    {

        if(empty($data['image'])) {
            $query = "UPDATE carousel SET title=:title, content=:content, cta=:cta, link=:link WHERE id=:id";
            $exec = [
                'title' => $data['title'],
                'content' => $data['content'],
                'cta' => $data['cta'],
                'link' => $data['link'],
                'id' => $data['id']
            ];
        } else {
            $query = "UPDATE carousel SET title=:title, content=:content, cta=:cta, link=:link, image=:image WHERE id=:id";
            $exec = [
                'title' => $data['title'],
                'content' => $data['content'],
                'cta' => $data['cta'],
                'link' => $data['link'],
                'image' => $data['image'],
                'id' => $data['id']
            ];
        }

        $req = $this->PDO->prepare($query);
        $req->execute($exec);
        $req->closeCursor();
    }

    public function deleteCarousel(int $id): void
    {
        $query = "DELETE FROM carousel WHERE id=:id";
        $req = $this->PDO->prepare($query);
        $req->execute([
            'id' =>  $id
        ]);
        $req->closeCursor();
    }

}