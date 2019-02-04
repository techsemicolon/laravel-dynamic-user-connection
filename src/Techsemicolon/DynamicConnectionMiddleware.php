<?php

namespace Techsemicolon\DynamicConnection;

use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Techsemicolon\DynamicConnection\DynamicConnectionFailedException;
use Techsemicolon\DynamicConnection\DynamicConnectionInvalidPasswordException;

class DynamicConnectionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(\Auth::check()){

            $user = \Auth::user();

            // Check if user model has property for dynamic database
            if(property_exists($user, 'dynamic_connection_database')){

                $userDatabase = $user->{$user->dynamic_connection_database};

                $currentDatabase = \DB::connection()->getDatabaseName();

                // If current database is no set
                if($userDatabase && $userDatabase != $currentDatabase){

                    $config = \Config::get('database.connections.mysql');

                    $config['database'] = $userDatabase;

                    // Set new username if applicable
                    if(property_exists($user, 'dynamic_connection_username')){

                        $config['username'] = $user->{$user->dynamic_connection_username};
                    }

                    try{

                        // Set new password if applicable
                        if(property_exists($user, 'dynamic_connection_password')){

                            $config['password'] = decrypt($user->{$user->dynamic_connection_password});
                        }
                    }
                    catch(DecryptException $e){
                        throw new DynamicConnectionInvalidPasswordException('The payload provider for user password is invalid');
                    }

                    // Update config
                    \Config::set('database.connections.mysql', $config);

                    // Refresh config array in connection cache
                    \DB::purge('mysql');

                    // Reconnect
                    \DB::reconnect('mysql');
                }

            }
        }

        return $next($request);
    }
}
