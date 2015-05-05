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

$app->match('/faccao', function () use ($app) {

            $table_columns = array(
                'ID',
                'NUMERO',
                'NOME',
                'ENDERECO',
                'TELEFONE',
            );

            $primary_key = "ID";
            $rows = array();

            $find_sql = "SELECT * FROM `faccao`";
            $rows_sql = $app['db']->fetchAll($find_sql, array());

            foreach ($rows_sql as $row_key => $row_sql) {
                for ($i = 0; $i < count($table_columns); $i++) {

                    $rows[$row_key][$table_columns[$i]] = $row_sql[$table_columns[$i]];
                }
            }

            $table_columns = array(
                'NUMERO',
                'NOME',
                'ENDERECO',
                'TELEFONE',
            );
            
            return $app['twig']->render('faccao/list.html.twig', array(
                        "table_columns" => $table_columns,
                        "primary_key" => $primary_key,
                        "rows" => $rows
            ));
        })
        ->bind('faccao_list')->value('require_authentication', true);

$app->match('/faccao/create', function () use ($app) {

            $initial_data = array(
                'ID' => '',
                'NUMERO' => '',
                'NOME' => '',
                'ENDERECO' => '',
                'TELEFONE' => '',
            );

            $form = $app['form.factory']->createBuilder('form', $initial_data);
            $form = $form->add('NUMERO', 'text', array('required' => true));
            $form = $form->add('NOME', 'text', array('required' => true));
            $form = $form->add('ENDERECO', 'text', array('label' => 'Endereço','required' => false));
            $form = $form->add('TELEFONE', 'text', array('required' => false));
            $form = $form->getForm();
            
            if ("POST" == $app['request']->getMethod()) {

                $form->handleRequest($app["request"]);

                if ($form->isValid()) {
                    $data = $form->getData();

                    $update_query = "INSERT INTO `faccao` (`NOME`, `NUMERO`, `ENDERECO`, `TELEFONE`) VALUES (?, ?, ?, ?)";
                    
                    $app['db']->executeUpdate($update_query, array($data['NOME'], $data['NUMERO'], $data['ENDERECO'], $data['TELEFONE']));


                    $app['session']->getFlashBag()->add(
                            'success', array(
                        'message' => 'Facção criada!',
                            )
                    );
                    return $app->redirect($app['url_generator']->generate('faccao_list'));
                }
            }

            return $app['twig']->render('faccao/create.html.twig', array(
                        "form" => $form->createView()
            ));
        })
        ->bind('faccao_create')->value('require_authentication', true);

$app->match('/faccao/edit/{id}', function ($id) use ($app) {

            $find_sql = "SELECT * FROM `faccao` WHERE `ID` = ?";
            $row_sql = $app['db']->fetchAssoc($find_sql, array($id));

            if (!$row_sql) {
                $app['session']->getFlashBag()->add(
                        'danger', array(
                    'message' => 'Não encontrado!',
                        )
                );
                return $app->redirect($app['url_generator']->generate('faccao_list'));
            }

            $initial_data = array(
                'NUMERO' => $row_sql['NUMERO'],
                'NOME' => $row_sql['NOME'],
                'ENDERECO' => $row_sql['ENDERECO'],
                'TELEFONE' => $row_sql['TELEFONE'],
            );

            $form = $app['form.factory']->createBuilder('form', $initial_data);
            $form = $form->add('NOME', 'text', array('required' => true));
            $form = $form->add('NUMERO', 'text', array('required' => true));
            $form = $form->add('ENDERECO', 'text', array('label' => 'Endereço','required' => false));
            $form = $form->add('TELEFONE', 'text', array('required' => false));
            $form = $form->getForm();

            if ("POST" == $app['request']->getMethod()) {

                $form->handleRequest($app["request"]);

                if ($form->isValid()) {
                    $data = $form->getData();

                    $update_query = "UPDATE `faccao` SET `NUMERO` = ? ,`NOME` = ?, `ENDERECO` = ?, `TELEFONE` = ? WHERE `ID` = ?";
                    $app['db']->executeUpdate($update_query, array($data['NUMERO'], $data['NOME'], $data['ENDERECO'], $data['TELEFONE'], $id));


                    $app['session']->getFlashBag()->add(
                            'success', array(
                        'message' => 'Facção atualizada!',
                            )
                    );
                    return $app->redirect($app['url_generator']->generate('faccao_edit', array("id" => $id)));
                }
            }

            return $app['twig']->render('faccao/edit.html.twig', array(
                        "form" => $form->createView(),
                        "id" => $id
            ));
        })
        ->bind('faccao_edit')->value('require_authentication', true);

$app->match('/faccao/delete/{id}', function ($id) use ($app) {

            $find_sql = "SELECT * FROM `faccao` WHERE `ID` = ?";
            $row_sql = $app['db']->fetchAssoc($find_sql, array($id));

            if ($row_sql) {
                $delete_query = "DELETE FROM `faccao` WHERE `ID` = ?";
                $app['db']->executeUpdate($delete_query, array($id));

                $app['session']->getFlashBag()->add(
                        'success', array(
                    'message' => 'Facção excluída!',
                        )
                );
            } else {
                $app['session']->getFlashBag()->add(
                        'danger', array(
                    'message' => 'Row not found!',
                        )
                );
            }

            return $app->redirect($app['url_generator']->generate('faccao_list'));
        })
        ->bind('faccao_delete')->value('require_authentication', true);
