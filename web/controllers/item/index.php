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

$app->match('/item', function () use ($app) {
    
	$table_columns = array(
		'ID', 
		'NOME', 
		'QUANTIDADE', 
		'TAMANHO_ID', 
		'LOTE_ID', 

    );

    $primary_key = "ID";
	$rows = array();

    $find_sql = "SELECT * FROM `item`";
    $rows_sql = $app['db']->fetchAll($find_sql, array());

    foreach($rows_sql as $row_key => $row_sql){
    	for($i = 0; $i < count($table_columns); $i++){

		$rows[$row_key][$table_columns[$i]] = $row_sql[$table_columns[$i]];


    	}
    }

    return $app['twig']->render('item/list.html.twig', array(
    	"table_columns" => $table_columns,
        "primary_key" => $primary_key,
    	"rows" => $rows
    ));
        
})
->bind('item_list')->value('require_authentication', true);



$app->match('/item/create', function () use ($app) {
    
    $initial_data = array(
		'NOME' => '', 
		'QUANTIDADE' => '', 
		'TAMANHO_ID' => '', 
		'LOTE_ID' => '', 

    );

    $form = $app['form.factory']->createBuilder('form', $initial_data);



	$form = $form->add('NOME', 'text', array('required' => true));
	$form = $form->add('QUANTIDADE', 'text', array('required' => true));
	$form = $form->add('TAMANHO_ID', 'text', array('required' => false));
	$form = $form->add('LOTE_ID', 'text', array('required' => false));


    $form = $form->getForm();

    if("POST" == $app['request']->getMethod()){

        $form->handleRequest($app["request"]);

        if ($form->isValid()) {
            $data = $form->getData();

            $update_query = "INSERT INTO `item` (`NOME`, `QUANTIDADE`, `TAMANHO_ID`, `LOTE_ID`) VALUES (?, ?, ?, ?)";
            $app['db']->executeUpdate($update_query, array($data['NOME'], $data['QUANTIDADE'], $data['TAMANHO_ID'], $data['LOTE_ID']));            


            $app['session']->getFlashBag()->add(
                'success',
                array(
                    'message' => 'item created!',
                )
            );
            return $app->redirect($app['url_generator']->generate('item_list'));

        }
    }

    return $app['twig']->render('item/create.html.twig', array(
        "form" => $form->createView()
    ));
        
})
->bind('item_create')->value('require_authentication', true);



$app->match('/item/edit/{id}', function ($id) use ($app) {

    $find_sql = "SELECT * FROM `item` WHERE `ID` = ?";
    $row_sql = $app['db']->fetchAssoc($find_sql, array($id));

    if(!$row_sql){
        $app['session']->getFlashBag()->add(
            'danger',
            array(
                'message' => 'Row not found!',
            )
        );        
        return $app->redirect($app['url_generator']->generate('item_list'));
    }

    
    $initial_data = array(
		'NOME' => $row_sql['NOME'], 
		'QUANTIDADE' => $row_sql['QUANTIDADE'], 
		'TAMANHO_ID' => $row_sql['TAMANHO_ID'], 
		'LOTE_ID' => $row_sql['LOTE_ID'], 

    );


    $form = $app['form.factory']->createBuilder('form', $initial_data);


	$form = $form->add('NOME', 'text', array('required' => true));
	$form = $form->add('QUANTIDADE', 'text', array('required' => true));
	$form = $form->add('TAMANHO_ID', 'text', array('required' => false));
	$form = $form->add('LOTE_ID', 'text', array('required' => false));


    $form = $form->getForm();

    if("POST" == $app['request']->getMethod()){

        $form->handleRequest($app["request"]);

        if ($form->isValid()) {
            $data = $form->getData();

            $update_query = "UPDATE `item` SET `NOME` = ?, `QUANTIDADE` = ?, `TAMANHO_ID` = ?, `LOTE_ID` = ? WHERE `ID` = ?";
            $app['db']->executeUpdate($update_query, array($data['NOME'], $data['QUANTIDADE'], $data['TAMANHO_ID'], $data['LOTE_ID'], $id));            


            $app['session']->getFlashBag()->add(
                'success',
                array(
                    'message' => 'item edited!',
                )
            );
            return $app->redirect($app['url_generator']->generate('item_edit', array("id" => $id)));

        }
    }

    return $app['twig']->render('item/edit.html.twig', array(
        "form" => $form->createView(),
        "id" => $id
    ));
        
})
->bind('item_edit')->value('require_authentication', true);



$app->match('/item/delete/{id}', function ($id) use ($app) {

    $find_sql = "SELECT * FROM `item` WHERE `ID` = ?";
    $row_sql = $app['db']->fetchAssoc($find_sql, array($id));

    if($row_sql){
        $delete_query = "DELETE FROM `item` WHERE `ID` = ?";
        $app['db']->executeUpdate($delete_query, array($id));

        $app['session']->getFlashBag()->add(
            'success',
            array(
                'message' => 'item deleted!',
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

    return $app->redirect($app['url_generator']->generate('item_list'));

})
->bind('item_delete')->value('require_authentication', true);






