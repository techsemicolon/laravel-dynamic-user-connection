<?php

namespace Techsemicolon\DynamicConnection;

use Illuminate\Support\ServiceProvider;
use Techsemicolon\DynamicConnection\DynamicConnectionMiddleware;

class DynamicConnectionServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register(){

    	$this->app['router']->pushMiddlewareToGroup('web', DynamicConnectionMiddleware::class);
    }
}