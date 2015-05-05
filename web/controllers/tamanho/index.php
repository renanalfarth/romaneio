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

$app->match('/tamanho', function () use ($app) {

            $table_columns = array(
                'ID',
                'NOME',
            );

            $primary_key = "ID";
            $rows = array();

            $find_sql = "SELECT * FROM `tamanho`";
            $rows_sql = $app['db']->fetchAll($find_sql, array());

            foreach ($rows_sql as $row_key => $row_sql) {
                for ($i = 0; $i < count($table_columns); $i++) {

                    $rows[$row_key][$table_columns[$i]] = $row_sql[$table_columns[$i]];
                }
            }

            return $app['twig']->render('tamanho/list.html.twig', array(
                        "table_columns" => $table_columns,
                        "primary_key" => $primary_key,
                        "rows" => $rows
            ));
        })
        ->bind('tamanho_list')->value('require_authentication', true);

$app->match('/tamanho/create', function () use ($app) {

            $initial_data = array(
                'NOME' => '',
            );

            $form = $app['form.factory']->createBuilder('form', $initial_data);



            $form = $form->add('NOME', 'text', array('required' => true));


            $form = $form->getForm();

            if ("POST" == $app['request']->getMethod()) {

                $form->handleRequest($app["request"]);

                if ($form->isValid()) {
                    $data = $form->getData();

                    $update_query = "INSERT INTO `tamanho` (`NOME`) VALUES (?)";
                    $app['db']->executeUpdate($update_query, array($data['NOME']));


                    $app['session']->getFlashBag()->add(
                            'success', array(
                        'message' => 'Tamanho criado!',
                            )
                    );
                    return $app->redirect($app['url_generator']->generate('tamanho_list'));
                }
            }

            return $app['twig']->render('tamanho/create.html.twig', array(
                        "form" => $form->createView()
            ));
        })
        ->bind('tamanho_create')->value('require_authentication', true);



$app->match('/tamanho/edit/{id}', function ($id) use ($app) {

            $find_sql = "SELECT * FROM `tamanho` WHERE `ID` = ?";
            $row_sql = $app['db']->fetchAssoc($find_sql, array($id));

            if (!$row_sql) {
                $app['session']->getFlashBag()->add(
                        'danger', array(
                    'message' => 'Não encontrado!',
                        )
                );
                return $app->redirect($app['url_generator']->generate('tamanho_list'));
            }


            $initial_data = array(
                'NOME' => $row_sql['NOME'],
            );

            $form = $app['form.factory']->createBuilder('form', $initial_data);
            $form = $form->add('NOME', 'text', array('required' => true));
            $form = $form->getForm();

            if ("POST" == $app['request']->getMethod()) {

                $form->handleRequest($app["request"]);

                if ($form->isValid()) {
                    $data = $form->getData();

                    $update_query = "UPDATE `tamanho` SET `NOME` = ? WHERE `ID` = ?";
                    $app['db']->executeUpdate($update_query, array($data['NOME'], $id));

                    $app['session']->getFlashBag()->add(
                            'success', array(
                        'message' => 'Tamanho atualizado!',
                            )
                    );
                    return $app->redirect($app['url_generator']->generate('tamanho_edit', array("id" => $id)));
                }
            }

            return $app['twig']->render('tamanho/edit.html.twig', array(
                        "form" => $form->createView(),
                        "id" => $id
            ));
        })
        ->bind('tamanho_edit')->value('require_authentication', true);

$app->match('/tamanho/delete/{id}', function ($id) use ($app) {

            $find_sql = "SELECT * FROM `tamanho` WHERE `ID` = ?";
            $row_sql = $app['db']->fetchAssoc($find_sql, array($id));

            if ($row_sql) {
                $delete_query = "DELETE FROM `tamanho` WHERE `ID` = ?";
                $app['db']->executeUpdate($delete_query, array($id));

                $app['session']->getFlashBag()->add(
                        'success', array(
                    'message' => 'Tamanho excluído!',
                        )
                );
            } else {
                $app['session']->getFlashBag()->add(
                        'danger', array(
                    'message' => 'Não encontrado!',
                        )
                );
            }

            return $app->redirect($app['url_generator']->generate('tamanho_list'));
        })
        ->bind('tamanho_delete')->value('require_authentication', true);