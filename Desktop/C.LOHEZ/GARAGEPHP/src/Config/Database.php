<?php

namespace App\config;
use PDO;
use PDOException;


class Database {

    // Propriété static privée pour stocker l'instance unique de PDO
    private static ?PDO $instance = null;

    // le constructeur est privé pour empêcher la création d'objet via new database
    private function __construct() {}

    // la méthode de clonage est privé pour empêcher de cloner l'instance
    private function __clone() {}

    public static function getInstance(): PDO {
     
         //Si l'instance na pas été créée 
        if (self::$instance === null) {

            // on construit le DSN (Data source name) avec les infos du fichier .env
            $dsn = sprintf("mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4",config::get('DB_HOST'),config::get('DB_PORT', '3306'), config::get('DB_NAME'));
                
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,// lance des execptions en cas d'erreurs SQL
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,//Récupere les resultats sous forme de tableau associatif     
            ];

            try {

                // On crée l'instance de PDO et on le stock
                self::$instance = new PDO($dsn, config::get('DB_USER'), config::get('DB_PASSWORD', ''), $options);
            } catch (PDOException $e) {
                  die ("Erreur de connexion à la base de données : " . $e->getMessage());
            }
        }
        return self::$instance;
    }
          
}


