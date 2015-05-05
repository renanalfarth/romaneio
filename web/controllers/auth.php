<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../src/app.php';

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

$app->before(function () use($app) {
    if ($app['request']->get('require_authentication')) {
        $user = $app['session']->get('user');

        if (!empty($user)) {
            $id = $user['id'];
            $user = $app['db']->fetchAssoc('SELECT * FROM usuario WHERE id = "' . $id . '" ');

            if (!is_array($user)) {
                throw new AccessDeniedHttpException("acesso negado...");
            }
        } else {
            throw new AccessDeniedHttpException("acesso negado...");
        }
    }
});

$app->error(function (\Exception $e) use ($app) {
    if ($e instanceof AccessDeniedHttpException) {
        return $app->redirect($app['url_generator']->generate('login'));
    }
    return $app->redirect($app['url_generator']->generate('login'));
});

$app->match('/login', function () use ($app) {


            if ("POST" == $app['request']->getMethod()) {

                $request = $app['request'];
                
                $email = $request->get('email');
                $password = $request->get('senha');
                                
                $user = $app['db']->fetchAssoc('SELECT * FROM usuario WHERE email ="' . $email . '" AND senha = "' . md5($password) . '" ');
                
                if (!empty($user)) {

                    $app['session']->set('user', array('id' => $user['ID'], 'name' => $user['NOME']));
                    
                    return $app->redirect($app['url_generator']->generate('dashboard'));
                }
            }

            $user = $app['session']->get('user');

            if (!empty($user)) {
                $id = $user['id'];
                $user = $app['db']->fetchAssoc('SELECT * FROM usuario WHERE id = "' . $id . '" ');

                if (is_array($user)) {
                    return $app->redirect($app['url_generator']->generate('dashboard'));
                }
            }

            return $app['twig']->render('auth/login.html.twig');
        })
        ->bind('login');

$app->get('/logout', function() use ($app) {

    $app['session']->set('user', null);

    return $app['twig']->render('auth/login.html.twig');
})->bind('logout')->value('require_authentication', true);

