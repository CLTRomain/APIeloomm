<?php
namespace App\Controller;

use App\Controller\AppController;

class ApiController extends AppController
{
    public function test()
    {
        $this->set([
            'message' => 'API test route is working!',
            '_serialize' => ['message']
        ]);
    }
}