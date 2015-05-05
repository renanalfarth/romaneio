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


require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../src/app.php';

use Symfony\Component\Validator\Constraints as Assert;

$app->match('/produto', function () use ($app) {

            $table_columns = array(
                'ID',
                'CODIGO',
                'NOME',
                'VALOR',
            );

            $primary_key = "ID";
            $rows = array();

            $find_sql = "SELECT *, produto.valor as VALOR FROM `produto`";
            $rows_sql = $app['db']->fetchAll($find_sql, array());

            foreach ($rows_sql as $row_key => $row_sql) {
                for ($i = 0; $i < count($table_columns); $i++) {

                    $rows[$row_key][$table_columns[$i]] = $row_sql[$table_columns[$i]];
                }
            }
            
            $table_columns = '';
            
            $table_columns = array(
                'CODIGO',
                'NOME',
                'VALOR',
            );
            
            return $app['twig']->render('produto/list.html.twig', array(
                        "table_columns" => $table_columns,
                        "primary_key" => $primary_key,
                        "rows" => $rows
            ));
        })
        ->bind('produto_list')->value('require_authentication', true);

$app->match('/produto/create', function () use ($app) {

            $initial_data = array(
                'CODIGO' => '',
                'NOME' => '',
                'VALOR' => '',
            );

            $form = $app['form.factory']->createBuilder('form', $initial_data);
            
            $form = $form->add('CODIGO', 'text', array('required' => true));
            $form = $form->add('NOME', 'text', array('required' => true));
            $form = $form->add('VALOR', 'text', array('required' => false));

            $form = $form->getForm();

            if ("POST" == $app['request']->getMethod()) {

                $form->handleRequest($app["request"]);

                if ($form->isValid()) {
                    $data = $form->getData();

                    $update_query = "INSERT INTO `produto` (`CODIGO`, `NOME`, `VALOR`) VALUES (?, ?, ?)";
                    $app['db']->executeUpdate($update_query, array($data['CODIGO'], $data['NOME'], $data['VALOR']));
                    
                    $app['session']->getFlashBag()->add(
                            'success', array(
                        'message' => 'Produto Criado!',
                            )
                    );
                    return $app->redirect($app['url_generator']->generate('produto_list'));
                }
            }

            return $app['twig']->render('produto/create.html.twig', array(
                        "form" => $form->createView()
            ));
        })
        ->bind('produto_create')->value('require_authentication', true);

$app->match('/produto/edit/{id}', function ($id) use ($app) {

            $find_sql = "SELECT * FROM `produto` WHERE `ID` = ?";
            $row_sql = $app['db']->fetchAssoc($find_sql, array($id));

            if (!$row_sql) {
                $app['session']->getFlashBag()->add(
                        'danger', array(
                    'message' => 'Row not found!',
                        )
                );
                return $app->redirect($app['url_generator']->generate('produto_list'));
            }


            $initial_data = array(
                'CODIGO' => $row_sql['CODIGO'],
                'NOME' => $row_sql['NOME'],
                'VALOR' => $row_sql['VALOR'],
            );


            $form = $app['form.factory']->createBuilder('form', $initial_data);


            $form = $form->add('CODIGO', 'text', array('required' => true));
            $form = $form->add('NOME', 'text', array('required' => true));
            $form = $form->add('VALOR', 'text', array('required' => false));


            $form = $form->getForm();

            if ("POST" == $app['request']->getMethod()) {

                $form->handleRequest($app["request"]);

                if ($form->isValid()) {
                    $data = $form->getData();

                    $update_query = "UPDATE `produto` SET `CODIGO` = ?, `NOME` = ?, `VALOR` = ? WHERE `ID` = ?";
                    $app['db']->executeUpdate($update_query, array($data['CODIGO'], $data['NOME'], $data['VALOR'], $id));


                    $app['session']->getFlashBag()->add(
                            'success', array(
                        'message' => 'Produto Editado!',
                            )
                    );
                    return $app->redirect($app['url_generator']->generate('produto_edit', array("id" => $id)));
                }
            }

            return $app['twig']->render('produto/edit.html.twig', array(
                        "form" => $form->createView(),
                        "id" => $id
            ));
        })
        ->bind('produto_edit')->value('require_authentication', true);

$app->match('/produto/delete/{id}', function ($id) use ($app) {

            $find_sql = "SELECT * FROM `produto` WHERE `ID` = ?";
            $row_sql = $app['db']->fetchAssoc($find_sql, array($id));

            if ($row_sql) {
                $delete_query = "DELETE FROM `produto` WHERE `ID` = ?";
                $app['db']->executeUpdate($delete_query, array($id));

                $app['session']->getFlashBag()->add(
                        'success', array(
                    'message' => 'Produto Excluído!',
                        )
                );
            } else {
                $app['session']->getFlashBag()->add(
                        'danger', array(
                    'message' => 'Row not found!',
                        )
                );
            }

            return $app->redirect($app['url_generator']->generate('produto_list'));
        })
        ->bind('produto_delete')->value('require_authentication', true);



