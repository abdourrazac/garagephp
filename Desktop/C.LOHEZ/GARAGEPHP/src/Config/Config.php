<?php

namespace App\config;
use Dotenv\Dotenv;

class Config {

    /* Class config manuel, cette class sert à charger le fichier .env, à le lire ,séparer et à nettoyer les données 
    public static array $config = [];
    public static bool $loaded = false;

    public static function load():void {

        if(self::$loaded) return;

        $envFile = __DIR__ . '/../../.env';
        if(!file_exists($envFile)) {
            throw new Exception("Fichier .env manquant");
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue; // Ignore les commentaires
           
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim(trim($value), "'\"/"); // Enlever les guillemets autour de la valeur si présents

            self::$config[$key] = $value;
            $_ENV[$key] = $value; // Optionnel: Charger dans $_ENV
            putenv("$key=$value"); // Optionnel: Charger dans les variables d'environnement
        }

        self::validateConfig();
        self::$loaded = true;
   }

   public static function get(string $key, $default = null) {
        if (!self::$loaded) {
            self::load();
        }
        return self::$config[$key] ?? $default;
   }

   private static function validateConfig(): void {


        $required = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_APP_KEY'];
        $missing = array_filter($required, fn($key) => empty(self::$config[$key]));

        if (!empty($missing)) {
            throw new Exception("Variables d'environnement manquantes: " . implode(', ', $missing));
        }
   }

   public function isDebug(): bool {
        return self::get('APP_DEBUG', 'false') === 'true';
   }*/

   /**
    * @param string $path le chemin vers le dossier contenant le fichier .env
    */

    public static function load($path = __DIR__ . '../'): void {

        // on verifie si le fichier  .env existe
        if(!file_exists($path . '/.env')) {
            $dotenv = Dotenv::createImmutable($path);
            $dotenv->load();
        }    
    }

    /**
     * @param string $key le nom de la variable 
     * @param mixed $default une valeur par defaut à retourner si la variable n'existe pas 
     * @return mixed la valeur de la variable ou la valeur par defaut
     */

         public static function get(string $key, $default = null) {
            return $_ENV[$key] ?? $default;
         }

    }
