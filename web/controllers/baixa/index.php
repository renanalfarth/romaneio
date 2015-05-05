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

$app->match('/baixa', function () use ($app) {

            $table_columns = array(
                'ID',
                'DATA',
                'PRODUTO',
                'TAMANHO',
                'QUANTIDADE',
                'LOTE',
            );
            
            $primary_key = "ID";
            $rows = array();
            
            $find_sql = "SELECT *, baixa.QUANTIDADE as QUANTIDADE, tamanho.NOME AS TAMANHO,baixa.ID as ID, lote.NUMERO as LOTE, produto.NOME as PRODUTO, DATE_FORMAT( baixa.`DATA` , '%d/%m/%Y' ) as DATA FROM `baixa` join item, produto, tamanho, lote where baixa.ITEM_ID = item.ID and item.LOTE_ID = lote.ID and item.PRODUTO_ID = produto.ID and item.TAMANHO_ID = tamanho.ID";
                        
            $rows_sql = $app['db']->fetchAll($find_sql, array());
            
            foreach ($rows_sql as $row_key => $row_sql) {
                for ($i = 0; $i < count($table_columns); $i++) {

                    $rows[$row_key][$table_columns[$i]] = $row_sql[$table_columns[$i]];
                }
            }

            return $app['twig']->render('baixa/list.html.twig', array(
                        "table_columns" => $table_columns,
                        "primary_key" => $primary_key,
                        "rows" => $rows
            ));
        })
        ->bind('baixa_list')->value('require_authentication', true);

$app->match('/baixa/create', function () use ($app) {

            $initial_data = array(
                'DATA' => new \DateTime('today'),
            );

            $form = $app['form.factory']->createBuilder('form', $initial_data);
            $form = $form->add('DATA', 'date', array('required' => true,'format' => 'dd MM yyyy'));
            $form = $form->getForm();
            
            if ("POST" == $app['request']->getMethod()) {
                 
                $form->handleRequest($app["request"]);
                                    
                $data = $form->getData();
        
                $itens = $app["request"]->get('ITEM_ID');
                                
                foreach($itens as $key => $item) {
                                        
                    $item_id = $item;
                    
                    $quantidade = $app["request"]->get('quantidade')[$key];
                    $pecas      = $app["request"]->get('pecas')[$key];
                    
                    if ($data['DATA'] != '' && $item_id != '') {
                        
                        $find_sql = "SELECT * FROM `item` WHERE `ID` = ?";
                        $row_sql  = $app['db']->fetchAssoc($find_sql, array($item_id));
                        
                        if($row_sql) {

                            if($quantidade > $row_sql['QUANTIDADE']) {
                                $app['session']->getFlashBag()->add(
                                        'success', array(
                                        'message' => 'Erro ao dar baixa, a quantidade é maior que a disponível no lote!',
                                        )
                                );

                                return $app['twig']->render('baixa/create.html.twig', array(
                                            "form" => $form->createView()
                                ));

                            }
                            
                        }                    
                        
                        $update_query = "INSERT INTO `baixa` (`DATA`, `QUANTIDADE`, `PECAS_INACABADAS`, `ITEM_ID`) VALUES (?, ?, ?, ?)";
                        
                        $app['db']->executeUpdate($update_query, array($data['DATA']->format('Y-m-d'), $quantidade, $pecas, $item_id));

                    }
                
                }
                
                    $app['session']->getFlashBag()->add(
                            'success', array(
                            'message' => 'Baixa criada!',
                        )
                    );
                    return $app->redirect($app['url_generator']->generate('baixa_list'));
            }

            return $app['twig']->render('baixa/create.html.twig', array(
                "form" => $form->createView()
            ));
        })
        ->bind('baixa_create')->value('require_authentication', true);

$app->match('/baixa/delete/{id}', function ($id) use ($app) {

            $find_sql = "SELECT * FROM `baixa` WHERE `ID` = ?";
            $row_sql = $app['db']->fetchAssoc($find_sql, array($id));

            if ($row_sql) {
                $delete_query = "DELETE FROM `baixa` WHERE `ID` = ?";
                $app['db']->executeUpdate($delete_query, array($id));

                $app['session']->getFlashBag()->add(
                        'success', array(
                    'message' => 'Baixa excluída!',
                        )
                );
            } else {
                $app['session']->getFlashBag()->add(
                        'danger', array(
                    'message' => 'Não encontrado!',
                        )
                );
            }

            return $app->redirect($app['url_generator']->generate('baixa_list'));
        })
        ->bind('baixa_delete')->value('require_authentication', true);

$app->match('/baixa/find-itens/{id}', function ($id) use ($app) {

            $find_sql = "SELECT tamanho.NOME as tamanho_nome, tamanho.ID as tamanho_id, item.ID, produto.NOME as produto_nome, produto.ID as produto_id FROM `item` join `lote`, `tamanho`, `produto` WHERE `lote`.`NUMERO` = ? and item.PRODUTO_ID = produto.ID and item.TAMANHO_ID = tamanho.ID and item.LOTE_ID = lote.ID group by `item`.`ID`";            
            $itens    = $app['db']->fetchAll($find_sql, array($id));
            
            return $app['twig']->render('baixa/carregar.html.twig', array(
                "itens" => $itens,
            ));
            
        })
        ->bind('baixa_find_itens')->value('require_authentication', true);