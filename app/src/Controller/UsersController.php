<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{


    
   
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');


        $this->Authentication->allowUnauthenticated(['login']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Users->find();
        $users = $this->paginate($query);

        $this->set(compact('users'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, contain: ['Articles']);
        $this->set(compact('user'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
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

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
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

// public function login()
// {
//     // // Vérifier si l'utilisateur est déjà connecté
//     // $result = $this->Authentication->getResult();
//     // if ($result->isValid()) {
//     //     $target = $this->Authentication->getLoginRedirect() ?? '/home';
//     //     return $this->response->withType('application/json')->withStringBody(json_encode(['success' => true, 'redirect' => $target]));
//     // }

//     // Vérifier si la requête est une requête POST
//     if ($this->request->is('post')) {
//         // Récupérer les données envoyées dans la requête
//         $data = $this->request->getData();



//         // // Déboguer les données de la requête (à retirer en production)
//           debug($data);

//         // Tenter d'authentifier l'utilisateur
//         $this->Authentication->setIdentity($data);
//         $result = $this->Authentication->getResult();

//         if ($result->isValid()) {
//             // Authentification réussie
//             $target = $this->Authentication->getLoginRedirect() ?? '/home';
//             return $this->response->withType('application/json')->withStringBody(json_encode(['success' => true, 'redirect' => $target]));
//         } else {
//             // Authentification échouée
//             return $this->response->withType('application/json')->withStringBody(json_encode(['success' => false, 'message' => 'Invalid username or password']));
//         }
//     }

//     // Si la méthode de requête n'est pas POST
//     return $this->response->withType('application/json')->withStringBody(json_encode(['success' => false, 'message' => 'Invalid request method']));
// }
public function login()
{
    if ($this->request->is('post')) {
        // Récupérer les données envoyées dans la requête
        $data = $this->request->getData();


        // Répondre avec une réponse de succès simple
        return $this->response->withType('application/json')
            ->withStringBody(json_encode(['success' => true, 'message' => 'Data received', 'data' => $data]));
    }

    // Si la méthode de requête n'est pas POST
    return $this->response->withType('application/json')
        ->withStringBody(json_encode(['success' => false, 'message' => 'Invalid request method']));
}





public function beforeFilter(\Cake\Event\EventInterface $event)
{
    parent::beforeFilter($event);

    $this->Authentication->allowUnauthenticated(['login']);
}
}
