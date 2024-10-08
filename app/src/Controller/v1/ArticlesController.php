<?php
declare(strict_types=1);

namespace App\Controller\V1;

use App\Controller\AppController;

/**
 * Articles Controller
 *
 */


class ArticlesController extends AppController
{

    public function initialize(): void
    {
        parent::initialize();


        //     $this->loadComponent('Authentication.Authentication');

        //$this->Authentication->setConfig('unauthenticatedRedirect', '/v1/login/test');
        // ici redirect pour les autres controller si non identifier


        //$this->Authentication->allowUnauthenticated(['login']);
    }

    public function index()
    {
        $recipes = "Articles Perfect";
        // $this->set('recipes', $recipes);
        // $this->viewBuilder()->setOption('serialize', ['recipes']);
        return $this->response->withType('json')->withStringBody(json_encode([
            'recipes' => $recipes
        ]));
    }

    public function GetallProducts()
    {
        $this->loadModel('Articles');
        $articles = $this->Articles->find('all');
        $this->set(compact('articles'));
    }

    public function view($id = null)
    {
        $article = $this->Articles->get($id, contain: []);
        $this->set(compact('article'));
    }

   public function add()
    {
        $userinfo = $this->userinfo->newEmptyEntity();
        if ($this->request->is('post')) {
            $userinfo = $this->userinfo->patchEntity($userinfo, $this->request->getData());
            if ($this->userinfo->save($userinfo)) {
                $this->Flash->success(__('The article has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The article could not be saved. Please, try again.'));
        }
        $this->set(compact('userinfo'));
    }



    public function edit($id = null)
    {
        $article = $this->Articles->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $article = $this->Articles->patchEntity($article, $this->request->getData());
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('The article has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The article could not be saved. Please, try again.'));
        }
        $this->set(compact('article'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $article = $this->Articles->get($id);
        if ($this->Articles->delete($article)) {
            $this->Flash->success(__('The article has been deleted.'));
        } else {
            $this->Flash->error(__('The article could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
