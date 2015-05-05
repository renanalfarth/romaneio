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

date_default_timezone_set('America/Sao_Paulo');


require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/../../src/app.php';

require_once __DIR__.'/auth.php';
require_once __DIR__.'/baixa/index.php';
require_once __DIR__.'/faccao/index.php';
require_once __DIR__.'/item/index.php';
require_once __DIR__.'/lote/index.php';
require_once __DIR__.'/tamanho/index.php';
require_once __DIR__.'/usuario/index.php';
require_once __DIR__.'/produto/index.php';
require_once __DIR__.'/composicao/index.php';

$app->match('/', function () use ($app) {

    return $app['twig']->render('ag_dashboard.html.twig', array());
        
})
->bind('dashboard')->value('require_authentication', true);

$app->match('/delete', function () use ($app) {
   
    $delete_query = "delete from lote where lote.DATA_ENVIO < ".strtotime('-2 months');
    $app['db']->executeUpdate($delete_query);
    
    $delete_query = "delete from lote where lote.DATA_ENVIO < ".strtotime('-2 months');
    $app['db']->executeUpdate($delete_query);
    
})
->bind('delete')->value('require_authentication', false);

$app->run();