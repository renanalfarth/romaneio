<?php

/*
 * This file is part of the CRUD Admin Generator project.
 *
 * Author: Jon Segador <jonseg@gmail.com>
 * Web: http://crud-admin-generator.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

date_default_timezone_set("America/Sao_Paulo");

use Silex\Application;

$app = new Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../web/views',
));
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translator.messages' => array(),
));
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(

        'dbs.options' => array(
            'db' => array(
                'driver'   => 'pdo_mysql',
                'dbname'   => 'romaneio',
                'host'     => '127.0.0.1',
                'user'     => 'root',
                'password' => '',
                'charset'  => 'utf8',
            ),
        )
));

$app['asset_path'] = 'http://localhost/sistema/web/resources';
$app['debug'] = true;
$app['locale'] = 'brl';
$app['login'] = 0;

return $app;
