<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;


use Authentication\PasswordHasher\DefaultPasswordHasher;

class User extends Entity
{


      // Automatically hash passwords when they are changed.
    protected function _setPassword(string $password)
    {
        $hasher = new DefaultPasswordHasher();
        return $hasher->hash($password);
    }

    protected array $_accessible = [
        'email' => true,
        'password' => true,
        'created' => true,
        'modified' => true,
        'articles' => true,
        'refreshToken' => true,
        'refresh_token_validity' => true,
    ];

    protected array $_hidden = [
        'password',
    ];
}
