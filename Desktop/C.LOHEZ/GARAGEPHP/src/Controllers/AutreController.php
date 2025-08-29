<?php
namespace App\Controllers;

use App\Models\User;
use App\Security\Validator;
use App\Security\TokenManger;
use App\Controllers\LoggerInterface;
use App\Services\Logger; // Assuming Logger is in the App\Services namespace and implements LoggerInterface

/**
 * Cette classe gére les actions liées à l'authentification et à l'inscription des utilisateurs
 */

 class AuthController extends BaseController {
    
    // Attributs
    private User $userModel;
    private TokenManager $tokenManager;
    private Logger $logger;

    /**
     *  Constructeur est appellé à chaque création d'un objet AuthController,
     * on en profite pour instancier les modeles dont on aura besoin
     */
    public function __construct() {

        parent::__construct();
        $this->userModel = new User();
        $this->tokenManager = new TokenManager();
        $this->logger = new Logger();       
    }
    
    /**
     * Méthode qu'affiche la page avec le formulaire de connexion
     */
    public function showLogin(): void {
        
        $this->render('auth/login', [
            'title' => 'Connexion',
            'csrf_token' => $this->tokenManager->generateCsrfToken()
        ]);
    }

    public function login(): void {

        // On s'assure que la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

            $this->response->redirect('/login');           
        }

        $data = $this->getPostData();

        // Validation du jeton CSRF
        if($this->tokenManager->validateCsrfToken($data['csrf_token'] ?? '')) {
            $this->response->errors('Token de sécurité invalide.', 403);
        }

        // le modele User s'occupe de la logique d'authentification 
        $user = $this->userModel->authenticate($data['email'], $data['password']);

        if($user) {

              // Si l'authentification réussi,on stocke les informations en session 
              $_SESSION['user_id'] = $user->getId();
              $_SESSION['user_role'] = $user->getRole();
              $_SESSION['user_username'] = $user->getUsername();

              // Redirection vers le tableau de bord
              $this->response->redirect('/cars');
        } else {


            // Si l'authentification échoue, on ré-affiche avec un message d'erreur
            $this->render('auth/login', [
               'title' => 'Connexion',
               'error' => 'Email ou mot de passe incorrect.',
               'old' => ['email' => $data['email']],
               'csrf_token' => $this->tokenManager->generateCsrfToken()
            ]);
        }    
    }
    /**
     *  Affichage du formulaire d'inscription
     */

     public function showRegister(): void {
        $this->render('auth/register', [
            'title' => 'Inscription',
            'csrf_token' => $this->tokenManager->generateCsrfToken()
        ]);
     }

     /**
      * Traitement des données soumissions formulaire inscription
      */

    public function register(): void {

        // On verifie que la méthode est bie POST sinon on redirige vers register
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {

            $this->response->redirect('/register');
        }

        $data = $this->getPostData();

        // Validation du jeton CSRF
        if($this->tokenManager->validateCsrfToken($data['csrf_token'] ?? '')) {
            $this->response->errors('Token de sécurité invalide.', 403);
        }

        // Validation des données du formulaire
        $error = $this->validator->validate($data, [
            'username' => 'required|min:3|max:50',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'password_confirm' => 'required|match:password'
        ]);

        if(!empty($errors)) {
            $this->render('auth/register',[
                'title'=>'Inscription',
                'errors' => $errors,
                'old' =>$data,
                'csrf_token' => $this->tokenManager->generateCsrfToken()
            ]);
            return;
        }

        // Vérification de l'email si deja existant en BDD
        if($this->userModel->findByEmail($data['email'])) {

            $this->render('auth/register',[
                'title'=>'Inscription',
                // On ajoute une erreur au champ email pour afficher
                'errors' => ['email' => 'Cet email est déjà utilisé.'],
                'old' =>$data,
                'csrf_token' => $this->tokenManager->generateCsrfToken()
            ]);
            return;
        }

        /**
         * Si tout est correcte alors on crée un nouvel utilisateur
         */
        try {

            // On instancie un nouvel utilisateur
            $newUser = new User();

            // On utilise les setters pour assigner les valeurs(inclut la validation et le hashage du MDP)
            $newUser->setUsername($data['username'])
                    ->setEmail($data['email'])
                    ->setPassword(password($data['password']))
                    ->setRole($data['user']); // role par defaut

            // On sauvegarde en BDD
            if($newUser->save()) {
                // Si la création réussi, on connecte automatiquement l'utilisateur
                $_SESSION['user_id'] = $newUser->getId();
                $_SESSION['user_role'] = $newUser->getRole();
                $_SESSION['user_username'] = $newUser->getUsername();
                $this->response->redirect('/cars');            
            } else {
                // Si la sauvegarde échoue
                throw new \Exception("la creation du compte a échoué.");
            }

        } catch (\Exception $e) {
            
            $this->render('auth/register', [
                'title' => 'Inscription',
                'errors' => "Erreur: " . $e->getMessage(),
                'old' => $data,
                'csrf_token' => $this->tokenManager->generateCsrfToken()
            ]);

        }  

    }




        
         /** 
         * Méthode de déconnexion avec destruction de la session
         */      
         public function logout(): void {

            // On vérifie que la méthode est bien POST
            if($_SERVER['REQUEST_METHOD'] === 'POST') {
                        
                $this->response->redirect('/register');
          }

        /**
         * Détruit toutes les données de la session actuelle
         */
            session_destroy();

            // Redirection vers la page de connexion
            $this->response->redirect('/login');
         }

         
}
    

