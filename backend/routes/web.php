<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () use ($router) {
    return response()->json([
        'success' => true,
        'message' => 'JstForHer API',
        'version' => $router->app->version()
    ]);
});

// 所有 API 路由统一使用 /api/ 前缀
$router->group(['prefix' => 'api'], function () use ($router) {
    
    // ========================================
    // 前端公共 API
    // ========================================
    
    // 栏目
    $router->get('categories', 'Api\CategoryController@index');
    $router->get('categories/{slug}', 'Api\CategoryController@show');
    
    // 文章
    $router->get('articles', 'Api\ArticleController@index');
    $router->get('articles/{slug}', 'Api\ArticleController@show');
    
    // 联系表单
    $router->post('contact', 'Api\ContactController@submit');
    
    // 邮件订阅
    $router->post('newsletter/subscribe', 'NewsletterController@subscribe');
    
    // 产品
    $router->get('products', 'Api\ProductController@index');
    $router->get('products/{id}', 'Api\ProductController@show');
    
    // 产品册
    $router->get('sub-categories', 'Api\SubCategoryController@index');
    $router->get('sub-categories/{id}', 'Api\SubCategoryController@show');
    
    // ========================================
    // 管理后台 API
    // ========================================
    
    // 管理员认证（放在 api group 下）
    $router->group(['prefix' => 'admin'], function () use ($router) {
        $router->post('login', 'AuthController@login');
        
        // 需要认证的路由
        $router->group(['middleware' => 'auth'], function () use ($router) {
            $router->get('me', 'AuthController@me');
            $router->post('logout', 'AuthController@logout');
            
            // 一级栏目管理
            $router->group(['prefix' => 'categories'], function () use ($router) {
                $router->get('/', 'Admin\CategoryController@index');
                $router->post('/', 'Admin\CategoryController@store');
                $router->get('/{id}', 'Admin\CategoryController@show');
                $router->put('/{id}', 'Admin\CategoryController@update');
                $router->delete('/{id}', 'Admin\CategoryController@destroy');
            });
            
            // 二级栏目管理
            $router->group(['prefix' => 'sub-categories'], function () use ($router) {
                $router->get('/', 'Admin\SubCategoryController@index');
                $router->post('/', 'Admin\SubCategoryController@store');
                $router->get('/{id}', 'Admin\SubCategoryController@show');
                $router->put('/{id}', 'Admin\SubCategoryController@update');
                $router->delete('/{id}', 'Admin\SubCategoryController@destroy');
            });
            
            // 文章管理
            $router->group(['prefix' => 'articles'], function () use ($router) {
                $router->get('/', 'Admin\ArticleController@index');
                $router->post('/', 'Admin\ArticleController@store');
                $router->get('/{id}', 'Admin\ArticleController@show');
                $router->put('/{id}', 'Admin\ArticleController@update');
                $router->delete('/{id}', 'Admin\ArticleController@destroy');
            });
            
            // 表单留资管理
            $router->group(['prefix' => 'contact-forms'], function () use ($router) {
                $router->get('/', 'Admin\ContactFormController@index');
                $router->get('/{id}', 'Admin\ContactFormController@show');
                $router->post('/{id}/mark-read', 'Admin\ContactFormController@markAsRead');
                $router->post('batch-mark-read', 'Admin\ContactFormController@batchMarkAsRead');
                $router->delete('/{id}', 'Admin\ContactFormController@destroy');
            });
            
            // 订阅管理
            $router->group(['prefix' => 'newsletter-subscriptions'], function () use ($router) {
                $router->get('/', 'Admin\NewsletterSubscriptionController@index');
                $router->get('export', 'Admin\NewsletterSubscriptionController@export');  // export 必须在 {id} 前面
                $router->post('batch-delete', 'Admin\NewsletterSubscriptionController@batchDelete');
                $router->get('/{id}', 'Admin\NewsletterSubscriptionController@show');
                $router->put('/{id}/status', 'Admin\NewsletterSubscriptionController@updateStatus');
                $router->delete('/{id}', 'Admin\NewsletterSubscriptionController@destroy');
            });
            
            // 产品管理
            $router->group(['prefix' => 'products'], function () use ($router) {
                $router->get('/', 'Admin\ProductController@index');
                $router->post('/', 'Admin\ProductController@store');
                $router->get('/{id}', 'Admin\ProductController@show');
                $router->put('/{id}', 'Admin\ProductController@update');
                $router->delete('/{id}', 'Admin\ProductController@destroy');
            });
            
            // 文件上传
            $router->post('upload/image', 'UploadController@uploadImage');
            $router->post('upload/images', 'UploadController@uploadImages');
        }); // end middleware group
        
    }); // end admin group
    
}); // end api group

