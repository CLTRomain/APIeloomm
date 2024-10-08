<?php
declare(strict_types=1);

namespace App\Controller\V1;

use App\Controller\AppController;
use Cake\Http\Exception\UnauthorizedException;
use Firebase\JWT\JWT;



class ManageConnectionController extends AppController
{
    public $Users;
    public function initialize(): void
    {
        parent::initialize();

        $this->Users = $this->fetchTable('Users');
        //$this->Authentication->allowUnauthenticated(['register','login']);
        $this->Authentication->addUnauthenticatedActions(['login','register']);


    }


    public function index()
    {
        $query = $this->Users->find();
        $users = $this->paginate($query);

        $this->set(compact('users'));
    }

    public function view($id = null)
    {
        $user = $this->Users->get($id, contain: ['Articles']);
        $this->set(compact('user'));
    }




    public function edit($id = null)
    {
        $user = $this->Users->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function logout()
    {
        $this->Authentication->logout();
        return $this->redirect(['controller' => 'Users', 'action' => 'login']); // par encore faite
    }



    public function login()
    {
        $this->request->allowMethod(['post']);
        $result = $this->Authentication->getResult();
            if ($result && $result->isValid()) {
                $user = $result->getData();
                $NewtokenJWT = $this->generateToken($user->email);

                if (empty($user->refresh_token)) {
                    // compte bloqué si refresh token vide
                    return $this->response->withStatus(401)->withType('application/json')
                        ->withStringBody(json_encode(['success' => false, 'message' => 'Token is empty, account blocked']));

                } else {
                        return $this->response->withType('application/json')
                            ->withStringBody(json_encode([
                                'success' => true,
                                'message' => 'User found',
                                'JWTtoken' => $NewtokenJWT
                            ]));
                    }
                }


           if (!$result->isValid()) {
               return $this->response->withStatus(401)->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => false,
                        'message' => 'wrong credential.',
                    ]));
            }else if (!$this->request->is('post')) {
               return $this->response->withStatus(401)->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'invalid method',
                ]));
            }
    }


    public function register()
    {
        if ($this->request->is('post')) {
            $user = $this->Users->newEmptyEntity();

            // Récupérer les données du formulaire
            $data = $this->request->getData();

            // Date jusqu'à expiration du refreshToken pour la base de données
            $time = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
            $timeAdd14days = $time->modify('+14 days')->modify('+2 hours');

            $data['refresh_token_validity'] = $timeAdd14days->format('Y-m-d H:i:s');
            $data['refresh_token'] = $this->generateRefreshToken($data['email']); // Ajout dans $data
            $data['role_id'] = 1; // Ajout dans $data

            $JWTtoken = $this->generateToken($data['email']); // Génération du JWT

            $user = $this->Users->patchEntity($user, $data);

            // Essayer de sauvegarder l'utilisateur
            if ($this->Users->save($user)) {
                // Si l'utilisateur est ajouté avec succès
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => true,
                        'TokenJWT' => $JWTtoken,
                    ]));
            } else {
                // Si l'enregistrement échoue
                return $this->response->withStatus(401)->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => false,
                        'message' => 'credentials already used',
                    ]));
            }
        } else {
            // Si la méthode n'est pas POST
            return $this->response->withStatus(401)->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'Invalid request method',
                ]));
        }
    }




    public function generateRefreshToken($userId)
    {
        // Récupérer la clé privée depuis .env
        $privateKey = file_get_contents(CONFIG . '/private.pem');

        if (!$privateKey) {
            throw new UnauthorizedException(__('Clé privée manquante')); // cas erreur clef non présente
        }

        // Définir le payload du JWT
        $payload = [
            'sub' => $userId,  // L'email
            'iat' => time(),   // Date de création du token
            'exp' => time() + 1209600 // Expiration dans 14 days
        ];

        // Générer le token
        $jwt = JWT::encode($payload, $privateKey, 'RS256');

        return $jwt;
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
            'sub' => $userId,  // L'email
            'iat' => time(),   // Date de création du token
            'exp' => time() + 600 // Expiration dans 10 min pour test à changer
        ];

        // Générer le token
        $jwt = JWT::encode($payload, $privateKey, 'RS256');

        return $jwt;
    }









 /*   public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['login', 'register']);
    }*/
}
