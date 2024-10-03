<?php
declare(strict_types=1);

namespace App\Controller\V1;

use App\Controller\AppController;
use Cake\Http\Exception\UnauthorizedException;
use Firebase\JWT\JWT;

class ManageConnectionController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');
   //     $this->loadComponent('Authentication.Authentication');

       // $this->Authentication->setConfig('unauthenticatedRedirect', '/v1/login/test');
    // ici redirect pour les autres controller si non identifier


        //$this->Authentication->allowUnauthenticated(['login']);
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


    public function add()
    {
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
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
        return $this->redirect(['controller' => 'Users', 'action' => 'login']);
    }



    public function login()
    {

        $result = $this->Authentication->getResult();

  /*      if ($result->isValid()) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => true, 'message' => 'Data received', 'data' => "Connecté Via TokenJWT"]));*/

    /*    } else {*/
            if ($this->request->is('post')) {
                $data = $this->request->getData();

                $userid = "1"; // val en dur pour test  / possibilité d'envoyer un nombre aléatoire en js lors de la première connection
                //id mais il mettre le user

                $NewtokenJWT = $this->generateToken($userid);

                return $this->response->withType('application/json')
                    //->withStringBody(json_encode(['success' => true, 'message' => 'Data received', 'data' => $NewtokenJWT]));
                    ->withStringBody(json_encode(['success' => true, 'message' => 'Data received', 'JWTtoken' =>  $NewtokenJWT]));
                //check email/password
            } else {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'message' => 'Invalid request method']));
            }
      /*  }*/
    }








    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['login']);
    }
}
