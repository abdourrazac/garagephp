<?php

namespace App\Controllers;
use App\Models\Car;

/**
 * Gère la logique de la page d'accueil
 */
class HomeController extends BaseController {


    /**
     * Affiche la page d'accueil avec la liste des voitures
     */
    public function index(): void {

        $carModel = new Car('required_argument_value');

        // On rend la vue 'Home/index' et on lui passe le titre et la liste des voitures.

        $this->render('home/index',[
             'title'=>'Accueil - Garage php',
             'cars' => $carModel->all(),
        ]);
    }
}