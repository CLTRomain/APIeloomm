<?php

// src/JwtIdentifier.php
namespace App;

use Authentication\Identifier\AbstractIdentifier;
use Authentication\Identifier\IdentifierInterface;
use Cake\ORM\TableRegistry;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

class JwtCheck extends AbstractIdentifier implements IdentifierInterface
{
    public function identify(array $identifiers): ?array
    {
        // Récupère l'email et le mot de passe
        $email = $identifiers['username'] ?? null;
        $password = $identifiers['password'] ?? null;
        $token = $identifiers['token'] ?? null;
        $privateKey = str_replace('\n', "\n", env('JWT_PRIVATE_KEY'));

        if ($token) {
            try {
                $decoded = JWT::decode($token, $privateKey);
                return $this->getUserByEmail($decoded->email);
            } catch (ExpiredException $e) {
                return null; // Le token a expiré
            } catch (\Exception $e) {
                return null; // Token invalide
            }
        }

        // Si le token n'est pas présent, vérifie le mot de passe
        return $this->getUserByEmail($email, $password);
    }

    protected function getUserByEmail(?string $email, ?string $password = null): ?array
    {
        if (!$email) {
            return null; // Aucune adresse e-mail fournie
        }

        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $user = $usersTable->find()
            ->where(['email' => $email])
            ->first();

        if ($user && $password) {
            // Vérifie le mot de passe
            if (password_verify($password, $user->password)) {
                return $user->toArray(); // Authentification réussie
            }
            return null; // Mot de passe incorrect
        }

        // Si aucun mot de passe n'est fourni, retourne l'utilisateur trouvé
        return $user ? $user->toArray() : null;
    }
}
