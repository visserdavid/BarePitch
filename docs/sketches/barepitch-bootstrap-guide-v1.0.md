# BarePitch — PHP Bootstrap Guide

Version 1.0 — April 2026

---

## 1. Purpose

This document provides a minimal working bootstrap for BarePitch.

Goal:

- working request lifecycle
- no framework
- compatible with shared hosting

---

## 2. Entry Point

File: /public/index.php

```php
<?php

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Router;
use App\Core\Request;

$request = Request::capture();
$router = require __DIR__ . '/../app/Config/routes.php';

$response = Router::dispatch($router, $request);
$response->send();
```

---

## 3. Bootstrap

File: /app/bootstrap.php

```php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;

$config = require __DIR__ . '/Config/database.php';

Database::init($config);
session_start();
```

---

## 4. Database Config

File: /app/Config/database.php

```php
<?php

return [
    'host' => 'localhost',
    'dbname' => 'BarePitch',
    'user' => 'root',
    'pass' => '',
];
```

---

## 5. Database Core

File: /app/Core/Database.php

```php
<?php

namespace App\Core;

use PDO;

class Database
{
    private static $pdo;

    public static function init(array $config)
    {
        self::$pdo = new PDO(
            "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4",
            $config['user'],
            $config['pass'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }

    public static function connection()
    {
        return self::$pdo;
    }
}
```

---

## 6. Request

File: /app/Core/Request.php

```php
<?php

namespace App\Core;

class Request
{
    public $method;
    public $uri;

    public static function capture()
    {
        $instance = new self();
        $instance->method = $_SERVER['REQUEST_METHOD'];
        $instance->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        return $instance;
    }
}
```

---

## 7. Response

File: /app/Core/Response.php

```php
<?php

namespace App\Core;

class Response
{
    private $content;

    public function __construct($content)
    {
        $this->content = $content;
    }

    public function send()
    {
        echo $this->content;
    }

    public static function make($content)
    {
        return new self($content);
    }
}
```

---

## 8. Router

File: /app/Core/Router.php

```php
<?php

namespace App\Core;

class Router
{
    public static function dispatch($routes, $request)
    {
        foreach ($routes as $route) {
            [$method, $uri, $action] = $route;

            if ($method === $request->method && $uri === $request->uri) {
                [$controller, $method] = $action;
                $instance = new $controller;

                return $instance->$method();
            }
        }

        return Response::make('404 Not Found');
    }
}
```

---

## 9. Routes

File: /app/Config/routes.php

```php
<?php

use App\Controller\DashboardController;

return [
    ['GET', '/', [DashboardController::class, 'index']],
];
```

---

## 10. First Controller

File: /app/Controller/DashboardController.php

```php
<?php

namespace App\Controller;

use App\Core\Response;

class DashboardController
{
    public function index()
    {
        return Response::make('<h1>BarePitch is running</h1>');
    }
}
```

---

## 11. First Result

Open browser:

http://yourdomain/

Expected output:

BarePitch is running

---

## 12. Next Steps

1. Add View class
2. Add Auth layer
3. Add Match module
4. Add Database repositories
5. Add Services

---

## End
