<?php

use function \DI\get;

return [
    'app.prefix' => '\App',
    'app.routes' => __DIR__ . '/routes.xml',

    'orm.config' => __DIR__ . '/orm_config.php',

    'db.name'     => 'app',
    'db.user'     => 'root',
    'db.password' => 'jOn79613226',
    'db.host'     => 'localhost',

    'twig.pathViews' => dirname(__DIR__, 3) . '/views',
    'twig.options'   => [],

    'blog.limit.post'      => 9,
    'admin.limit.post'     => 10,
    'admin.limit.category' => 10,
    'admin.limit.user'     => 10,

    'users'      => get(App\Models\UserModel::class),
    'posts'      => get(App\Models\PostModel::class),
    'categories' => get(App\Models\CategoryModel::class),
    'comments'   => get(App\Models\CommentModel::class),
    'admin'      => get(App\Models\AdminModel::class)
];
