<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Authentication\Authenticators\Session;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use Config\Auth;

/**
 * Akun demo untuk presentasi — lihat USERS_DEMO.md.
 */
class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        /** @var UserModel $userModel */
        $userModel = model(setting('Auth.userProvider'));

        $this->seedUser(
            userModel: $userModel,
            username: 'superadmin',
            email: 'superadmin@paroki.demo',
            password: 'SuperAdmin2026!',
            group: 'superadmin',
        );

        $this->seedUser(
            userModel: $userModel,
            username: 'editor',
            email: 'editor@paroki.demo',
            password: 'Editor2026!',
            group: 'editor',
        );
    }

    private function seedUser(
        UserModel $userModel,
        string $username,
        string $email,
        string $password,
        string $group,
    ): void {
        /** @var Auth $authConfig */
        $authConfig = config('Auth');

        $existing = $this->db->table($authConfig->tables['identities'])
            ->where('type', Session::ID_TYPE_EMAIL_PASSWORD)
            ->where('secret', $email)
            ->get()
            ->getRowArray();

        if ($existing !== null) {
            return;
        }

        $user = new User([
            'username' => $username,
            'email'    => $email,
            'password' => $password,
            'active'   => true,
        ]);

        $userModel->save($user);

        $saved = $userModel->findById($userModel->getInsertID());

        if ($saved === null) {
            return;
        }

        $saved->addGroup($group);
    }
}
