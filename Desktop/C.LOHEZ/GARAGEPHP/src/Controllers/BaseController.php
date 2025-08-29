<?php
namespace App\Controllers;
use App\Security\Validator; // Ensure the Validator class exists in the App\Security namespace or adjust the namespace if incorrect
use App\Utils\Response; // Ensure the Response class exists in the App\Utils namespace

/**
 * Controller de base
 * Toutes les autres classe de controller hériteront de celle ci
 * 
 */

abstract class BaseController {

    protected Response $response;
    protected Validator $validator;
    
    public function __construct() {
        $this->response = new Response();
        $this->validator =  new Validator();
    }

    /**
     * Affiche une vue en l'injectant dans layout principale
     * @param string $view le nom de fichier de vue
     * @param array $data les données à rendre accessibles dans la vue
     * 
     */
    protected function render(string $view, array $data = []): void {       
       
        // On construit le chemin complet vers le fichier de vue 
        $viewPath = __DIR__ .'/view/' .$view .'.php';

        // On verifie que le fichier de vue existe bien
        if (!file_exists($viewPath)) {
            $this->response->errors("vue non trouvée : $viewPath", 500);
            return;
        }

        // Extract transforme les clés d'un tableau en variables
        // ex: $data = ['title => 'Accueil'] devient $title = 'Accueil'
        extract($data);

        // On utilise la mise en tempon de sortie (output buffering) pour capturer le HTML de la vue.
        ob_start();
        include $viewPath;

        // Ici on vide le cache la variable $content contient la vue
        $content = ob_get_clean();

        // Finalement, on inclut le layout principal, qui peut maintenant utiliser la variable $content.
        include __DIR__ . '/view/layout.php';      
    }

    /**
     * Récupére et nettoie les données envoyées via une requete POST
     */
    protected function getPostData(): array {

        return $this->validator->sanitize($_POST);
    }

    /**
     * Verifie si l'utilisateur est connecter sinon le rédirige vers la page login
     */
    protected function requireAuth(): void {

        if (!isset($_SESSION['user'])) {
            $this->response->redirect('/login');
        }
    }
}
