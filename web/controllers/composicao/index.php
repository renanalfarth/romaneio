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


require_once __DIR__.'/../../../vendor/autoload.php';
require_once __DIR__.'/../../../src/app.php';

use Symfony\Component\Validator\Constraints as Assert;

$app->match('/composicao', function () use ($app) {
    
	$table_columns = array(
		'ID', 
		'NOME', 

    );

    $primary_key = "ID";
	$rows = array();

    $find_sql = "SELECT * FROM `composicao`";
    $rows_sql = $app['db']->fetchAll($find_sql, array());

    foreach($rows_sql as $row_key => $row_sql){
    	for($i = 0; $i < count($table_columns); $i++){

		$rows[$row_key][$table_columns[$i]] = $row_sql[$table_columns[$i]];


    	}
    }

    return $app['twig']->render('composicao/list.html.twig', array(
    	"table_columns" => $table_columns,
        "primary_key" => $primary_key,
    	"rows" => $rows
    ));
        
})
->bind('composicao_list')->value('require_authentication', true);



$app->match('/composicao/create', function () use ($app) {
    
    $initial_data = array(
		'NOME' => '', 

    );

    $form = $app['form.factory']->createBuilder('form', $initial_data);



	$form = $form->add('NOME', 'text', array('required' => true));


    $form = $form->getForm();

    if("POST" == $app['request']->getMethod()){

        $form->handleRequest($app["request"]);

        if ($form->isValid()) {
            $data = $form->getData();

            $update_query = "INSERT INTO `composicao` (`NOME`) VALUES (?)";
            $app['db']->executeUpdate($update_query, array($data['NOME']));            


            $app['session']->getFlashBag()->add(
                'success',
                array(
                    'message' => 'Composição Criada!',
                )
            );
            return $app->redirect($app['url_generator']->generate('composicao_list'));

        }
    }

    return $app['twig']->render('composicao/create.html.twig', array(
        "form" => $form->createView()
    ));
        
})
->bind('composicao_create')->value('require_authentication', true);



$app->match('/composicao/edit/{id}', function ($id) use ($app) {

    $find_sql = "SELECT * FROM `composicao` WHERE `ID` = ?";
    $row_sql = $app['db']->fetchAssoc($find_sql, array($id));

    if(!$row_sql){
        $app['session']->getFlashBag()->add(
            'danger',
            array(
                'message' => 'Row not found!',
            )
        );        
        return $app->redirect($app['url_generator']->generate('composicao_list'));
    }

    
    $initial_data = array(
		'NOME' => $row_sql['NOME'], 

    );


    $form = $app['form.factory']->createBuilder('form', $initial_data);


	$form = $form->add('NOME', 'text', array('required' => true));


    $form = $form->getForm();

    if("POST" == $app['request']->getMethod()){

        $form->handleRequest($app["request"]);

        if ($form->isValid()) {
            $data = $form->getData();

            $update_query = "UPDATE `composicao` SET `NOME` = ? WHERE `ID` = ?";
            $app['db']->executeUpdate($update_query, array($data['NOME'], $id));            


            $app['session']->getFlashBag()->add(
                'success',
                array(
                    'message' => 'composicao edited!',
                )
            );
            return $app->redirect($app['url_generator']->generate('composicao_edit', array("id" => $id)));

        }
    }

    return $app['twig']->render('composicao/edit.html.twig', array(
        "form" => $form->createView(),
        "id" => $id
    ));
        
})
->bind('composicao_edit')->value('require_authentication', true);



$app->match('/composicao/delete/{id}', function ($id) use ($app) {

    $find_sql = "SELECT * FROM `composicao` WHERE `ID` = ?";
    $row_sql = $app['db']->fetchAssoc($find_sql, array($id));

    if($row_sql){
        $delete_query = "DELETE FROM `composicao` WHERE `ID` = ?";
        $app['db']->executeUpdate($delete_query, array($id));

        $app['session']->getFlashBag()->add(
            'success',
            array(
                'message' => 'Composição Excluída!',
            )
        );
    }
    else{
        $app['session']->getFlashBag()->add(
            'danger',
            array(
                'message' => 'Row not found!',
            )
        );  
    }

    return $app->redirect($app['url_generator']->generate('composicao_list'));

})
->bind('composicao_delete')->value('require_authentication', true);






