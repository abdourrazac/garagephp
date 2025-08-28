<?php
namespace App\Models;

use PDO;

// Modele Car, représente une voiture  en BDD
class Car extends BaseModels{

    protected string $table = 'cars';

    /**
     * Récupere toutes les voitures
     * @return array tableau dde voitures
     */
    public function all():array {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY created_at DESC");
        // FETCH_ASSOC est deja defini par defaut dans dans notre class DATABASE
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $car_id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE car_id = :id");
        $stmt->execute([':id' => $car_id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null ;  
    }
}