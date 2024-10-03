<?php
declare(strict_types=1);

namespace App\Controller\V1;

use App\Controller\AppController;
use Cake\Http\Exception\UnauthorizedException;
use Firebase\JWT\JWT;

/**
 * Recipes Controller
 *
 */
class RecipesController extends AppController
{

    public function initialize() : void
    {
        parent::initialize();

        $this->loadComponent('Authentication.Authentication');
        $this->Authentication->allowUnauthenticated(['view', 'index', 'add']);
        $this->Users = $this->getTableLocator()->get('Users'); // Charger le modèle Users
        $this->loadComponent('Flash');




    }

    public function index()
    {

        $type = "tee-shirt";
        $username = "romain";
        $mdp = "123123";

        $articles = $this->fetchTable('Articles')->find('all'); // fetch dans la db


        // $status = "status";
        // $text = "text";


        // $this->set('recipes', $recipes);
        // $this->viewBuilder()->setOption('serialize', ['recipes']);

                //$this->response = $this->response->withStatus(200);

        return $this->response->withType('json')->withStringBody(json_encode([
            'type' => $type,
            'username' => $username,
            'Articles' => $articles,
        ]));

       // return $this->response = $this->response->withStatus(404);
    }

    public function view($id = null)
    {
        $recipe = $this->Recipes->get($id, contain: []);
        $this->set(compact('recipe'));
    }

    public function add()
    {
        $user = $this->Users->newEmptyEntity();

        if ($this->request->is('post')) {
            // Récupérer les données du formulaire
            $data = $this->request->getData();

            $data['refreshToken'] = $this->generateToken($data['email']); // Assuming generateToken method exists


            $user = $this->Users->patchEntity($user, $data);

            // Essayer de sauvegarder l'utilisateur
            if ($this->Users->save($user)) {
                $this->Flash->success(__('L’utilisateur a été ajouté avec succès.'));

                // Rediriger vers une autre page après la création
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('L’utilisateur n’a pas pu être ajouté. Veuillez réessayer.'));
        }

    }

    public function generateToken($userId)
    {
        // Récupérer la clé privée depuis .env
        $privateKey = file_get_contents(CONFIG . '/private.pem');

        if (!$privateKey) {
            throw new UnauthorizedException(__('Clé privée manquante')); // cas erreur clef non présente
        }

        // Définir le payload du JWT
        $payload = [
            'sub' => $userId,  // L'identifiant de l'utilisateur
            'iat' => time(),   // Date de création du token
            'exp' => time() + 3600 // Expiration dans 1 heure
        ];

        // Générer le token
        $jwt = JWT::encode($payload, $privateKey, 'RS256');

        return $jwt;
    }



    public function edit($id = null)
    {
        $recipe = $this->Recipes->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $recipe = $this->Recipes->patchEntity($recipe, $this->request->getData());
            if ($this->Recipes->save($recipe)) {
                $this->Flash->success(__('The recipe has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The recipe could not be saved. Please, try again.'));
        }
        $this->set(compact('recipe'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $recipe = $this->Recipes->get($id);
        if ($this->Recipes->delete($recipe)) {
            $this->Flash->success(__('The recipe has been deleted.'));
        } else {
            $this->Flash->error(__('The recipe could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
