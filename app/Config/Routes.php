<?php

declare(strict_types=1);

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// Logout outside the session filter — CSRF-exempt (see Config\Filters).
// .cursorrules §4.3 / CONTEXT.md §3
$routes->match(['GET', 'POST'], 'logout', 'ProfileController::logout');

// Shield auth routes (login, magic-link, auth-actions). Register & logout handled above.
service('auth')->routes($routes, ['except' => ['register', 'logout']]);
