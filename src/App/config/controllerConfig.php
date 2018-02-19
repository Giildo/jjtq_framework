<?php

use App\Admin\Model\UserModel;
use App\Blog\Model\CategoryModel;
use App\Blog\Model\PostModel;
use function DI\get;

return [
    'general.models' => [],

    'general.error.models' => [],

    'blog.models' => [
        'post'     => get(PostModel::class),
        'category' => get(CategoryModel::class)
    ],

    'admin.models' => [
        'user' => get(UserModel::class)
    ],

    'admin.user.models' => [
        'user' => get(UserModel::class)
    ],

    'admin.post.models' => [
        'post'     => get(PostModel::class),
        'category' => get(CategoryModel::class),
        'user'     => get(UserModel::class)
    ],

    'admin.category.models' => [
        'category' => get(CategoryModel::class)
    ]
];