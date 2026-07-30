<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Shield auth route paths — login panel at /cp (not /login).
 */
class AuthRoutes extends BaseConfig
{
    public array $routes = [
        'register' => [
            [
                'get',
                'register',
                'RegisterController::registerView',
                'register',
            ],
            [
                'post',
                'register',
                'RegisterController::registerAction',
            ],
        ],
        'login' => [
            [
                'get',
                'cp',
                'LoginController::loginView',
                'login',
            ],
            [
                'post',
                'cp',
                'LoginController::loginAction',
            ],
        ],
        'magic-link' => [
            [
                'get',
                'cp/magic-link',
                'MagicLinkController::loginView',
                'magic-link',
            ],
            [
                'post',
                'cp/magic-link',
                'MagicLinkController::loginAction',
            ],
            [
                'get',
                'cp/verify-magic-link',
                'MagicLinkController::verify',
                'verify-magic-link',
            ],
        ],
        'logout' => [
            [
                'get',
                'logout',
                'LoginController::logoutAction',
                'logout',
            ],
        ],
        'auth-actions' => [
            [
                'get',
                'auth/a/show',
                'ActionController::show',
                'auth-action-show',
            ],
            [
                'post',
                'auth/a/handle',
                'ActionController::handle',
                'auth-action-handle',
            ],
            [
                'post',
                'auth/a/verify',
                'ActionController::verify',
                'auth-action-verify',
            ],
        ],
    ];
}
