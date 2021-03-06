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

$app->match('/usuario', function () use ($app) {

            $table_columns = array(
                'ID',
                'NOME',
                'EMAIL',
            );

            $primary_key = "ID";
            $rows = array();

            $find_sql = "SELECT * FROM `usuario`";
            $rows_sql = $app['db']->fetchAll($find_sql, array());

            foreach ($rows_sql as $row_key => $row_sql) {
                for ($i = 0; $i < count($table_columns); $i++) {

                    $rows[$row_key][$table_columns[$i]] = $row_sql[$table_columns[$i]];
                }
            }

            return $app['twig']->render('usuario/list.html.twig', array(
                        "table_columns" => $table_columns,
                        "primary_key" => $primary_key,
                        "rows" => $rows
            ));
        })
        ->bind('usuario_list')->value('require_authentication', true);

$app->match('/usuario/create', function () use ($app) {

            $initial_data = array(
                'NOME' => '',
                'EMAIL' => '',
                'SENHA' => '',
            );

            $form = $app['form.factory']->createBuilder('form', $initial_data);
            $form = $form->add('NOME', 'text', array('required' => true));
            $form = $form->add('EMAIL', 'text', array('label' => 'E-mail','required' => true));
            $form = $form->add('SENHA', 'password', array('required' => true));
            $form = $form->getForm();

            if ("POST" == $app['request']->getMethod()) {

                $form->handleRequest($app["request"]);

                if ($form->isValid()) {
                    $data = $form->getData();

                    $update_query = "INSERT INTO `usuario` (`NOME`, `EMAIL`, `SENHA`) VALUES (?, ?, ?)";
                    $app['db']->executeUpdate($update_query, array($data['NOME'], $data['EMAIL'], md5($data['SENHA'])));


                    $app['session']->getFlashBag()->add(
                            'success', array(
                        'message' => 'Usuário criado!',
                            )
                    );
                    return $app->redirect($app['url_generator']->generate('usuario_list'));
                }
            }

            return $app['twig']->render('usuario/create.html.twig', array(
                        "form" => $form->createView()
            ));
        })
        ->bind('usuario_create')->value('require_authentication', true);

$app->match('/usuario/edit/{id}', function ($id) use ($app) {

            $find_sql = "SELECT * FROM `usuario` WHERE `ID` = ?";
            $row_sql = $app['db']->fetchAssoc($find_sql, array($id));

            if (!$row_sql) {
                $app['session']->getFlashBag()->add(
                        'danger', array(
                    'message' => 'Não encontrado!',
                        )
                );
                return $app->redirect($app['url_generator']->generate('usuario_list'));
            }

            $initial_data = array(
                'NOME' => $row_sql['NOME'],
                'EMAIL' => $row_sql['EMAIL'],
                'SENHA' => $row_sql['SENHA'],
            );
            
            $form = $app['form.factory']->createBuilder('form', $initial_data);
            $form = $form->add('NOME', 'text', array('required' => true));
            $form = $form->add('EMAIL', 'text', array('label' => 'E-mail','required' => true));
            $form = $form->add('SENHA', 'password', array('required' => true));
            $form = $form->getForm();

            if ("POST" == $app['request']->getMethod()) {

                $form->handleRequest($app["request"]);

                if ($form->isValid()) {
                    $data = $form->getData();

                    $update_query = "UPDATE `usuario` SET `NOME` = ?, `EMAIL` = ?, `SENHA` = ? WHERE `ID` = ?";
                    $app['db']->executeUpdate($update_query, array($data['NOME'], $data['EMAIL'], md5($data['SENHA'], $id)));


                    $app['session']->getFlashBag()->add(
                            'success', array(
                        'message' => 'Usuário atualizado!',
                            )
                    );
                    return $app->redirect($app['url_generator']->generate('usuario_edit', array("id" => $id)));
                }
            }

            return $app['twig']->render('usuario/edit.html.twig', array(
                        "form" => $form->createView(),
                        "id" => $id
            ));
        })
        ->bind('usuario_edit')->value('require_authentication', true);



$app->match('/usuario/delete/{id}', function ($id) use ($app) {

            $find_sql = "SELECT * FROM `usuario` WHERE `ID` = ?";
            $row_sql = $app['db']->fetchAssoc($find_sql, array($id));

            if ($row_sql) {
                $delete_query = "DELETE FROM `usuario` WHERE `ID` = ?";
                $app['db']->executeUpdate($delete_query, array($id));

                $app['session']->getFlashBag()->add(
                        'success', array(
                    'message' => 'Usuário excluído!',
                        )
                );
            } else {
                $app['session']->getFlashBag()->add(
                        'danger', array(
                    'message' => 'Não encontrado!',
                        )
                );
            }

            return $app->redirect($app['url_generator']->generate('usuario_list'));
        })
        ->bind('usuario_delete')->value('require_authentication', true);

