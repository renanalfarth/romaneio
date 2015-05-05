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

$app->match('/lote', function () use ($app) {

            $table_columns = array(
                'ID',
                'NUMERO',
                'DATA_ENVIO',
                'FACCAO',
            );

            $primary_key = "ID";
            $rows = array();

            $find_sql = "SELECT *, lote.ID as ID, DATE_FORMAT( `DATA_ENVIO` , '%d/%m/%Y' ) as DATA_ENVIO,faccao.NOME as FACCAO, lote.NUMERO as NUMERO FROM `lote` join faccao where lote.FACCAO_ID = faccao.ID";
            $rows_sql = $app['db']->fetchAll($find_sql, array());

            foreach ($rows_sql as $row_key => $row_sql) {
                for ($i = 0; $i < count($table_columns); $i++) {

                    $rows[$row_key][$table_columns[$i]] = $row_sql[$table_columns[$i]];
                }
            }

            return $app['twig']->render('lote/list.html.twig', array(
                        "table_columns" => $table_columns,
                        "primary_key" => $primary_key,
                        "rows" => $rows
            ));
        })
        ->bind('lote_list')->value('require_authentication', true);

$app->match('/lote/create', function () use ($app) {

            $find_sql = "SELECT lote.NUMERO FROM `lote` order by lote.ID desc";
            $last = $app['db']->fetchAssoc($find_sql);
            
            $initial_data = array(
                'NUMERO' => '',
                'DATA_ENVIO' => new \DateTime('today'),
                'FACCAO_ID' => '',
                'OBSERVACAO' => '',
            );

            $find_sql = "SELECT * FROM `faccao` order by NOME ASC";
            $resultFaccao = $app['db']->fetchAll($find_sql, array());
            $arrFaccao = array();

            foreach ($resultFaccao as $faccao) {
                $arrFaccao[$faccao['ID']] = $faccao['NOME'];
            }
            
            $find_sql = "SELECT * FROM `tamanho`";
            $resultTamanho = $app['db']->fetchAll($find_sql, array());
            $arrTamanho = array();
            
            foreach ($resultTamanho as $tamanho) {
                $arrTamanho[$tamanho['ID']] = $tamanho['NOME'];
            }
            
            $find_sql = "SELECT * FROM `composicao`";
            $resultComposicao = $app['db']->fetchAll($find_sql, array());
            $arrComposicao = array();
            
            foreach ($resultComposicao as $composicao) {
                $arrComposicao[$composicao['ID']] = $composicao['NOME'];
            }
            
            $find_sql   = "SELECT ID as ID, CODIGO as CODIGO, NOME as NOME FROM `produto` order by NOME asc";
            $arrProduto = $app['db']->fetchAll($find_sql, array());
                        
            $form = $app['form.factory']->createBuilder('form', $initial_data);

            $form = $form->add('NUMERO', 'text', array('label' => 'Número', 'required' => true));
            $form = $form->add('DATA_ENVIO', 'date', array('label' => 'Data de envio', 'required' => true,'format' => 'dd MM yyyy'));
            $form = $form->add('OBSERVACAO', 'text', array('label' => 'Observação', 'required' => true));
            $form = $form->add('LINHA_COR', 'text', array('label' => 'Usar linha cor:', 'required' => true));
            $form = $form->add('FACCAO_ID', 'choice', array('empty_value' => 'Selecione', 'choices' => $arrFaccao, 'label' => 'Facção', 'required' => true));
            $form = $form->add('COMPOSICAO_ID', 'choice', array('empty_value' => 'Selecione', 'choices' => $arrComposicao, 'label' => 'Composição', 'required' => true));
            $form = $form->getForm();

            if ("POST" == $app['request']->getMethod()) {
                
                $form->handleRequest($app["request"]);
                
                if ($form->isValid()) {
                    $data = $form->getData();
                    
                    $user = $app['session']->get('user');
                    $usuario_id = $user['id'];
                                        
                    $update_query = "INSERT INTO `lote` (`NUMERO`, `DATA_ENVIO`, `OBSERVACAO`, `LINHA_COR`, `USUARIO_ID`, `FACCAO_ID`, `COMPOSICAO_ID`) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $app['db']->executeUpdate($update_query, array($data['NUMERO'], $data['DATA_ENVIO']->format('Y-m-d'), $data['OBSERVACAO'], $data['LINHA_COR'], $usuario_id, $data['FACCAO_ID'], $data['COMPOSICAO_ID']));
                    
                    $find_sql = "SELECT LAST_INSERT_ID()";
                    $last_id = $app['db']->fetchAll($find_sql, array());
                    
                    // salvo os itens 
                    foreach ($app["request"]->get('quantidade') as $key => $quantidade) {
                        if((int)$quantidade > 0) {
                            $produto = $app["request"]->get('produto');
                            $tamanho = $app["request"]->get('tamanho');
                            
                            $update_query = "INSERT INTO `item` (`PRODUTO_ID`, `QUANTIDADE`, `TAMANHO_ID`, `LOTE_ID`) VALUES (?, ?, ?, ?)";
                            $app['db']->executeUpdate($update_query, array($produto, $quantidade, $tamanho[$key], $last_id[0]['LAST_INSERT_ID()']));
                        }
                    }
                    
                    $app['session']->getFlashBag()->add(
                            'success', array(
                        'message' => 'Lote criado!',
                            )
                    );
                    return $app->redirect($app['url_generator']->generate('lote_list'));
                }
                
            }
            
            return $app['twig']->render('lote/create.html.twig', array(
                        "form"       => $form->createView(),
                        "tamanhos"   => $arrTamanho,
                        "produtos"   => $arrProduto,
                        "composicao" => $arrComposicao,
            ));
        })
        ->bind('lote_create')->value('require_authentication', true);

$app->match('/lote/edit/{id}', function ($id) use ($app) {

            $find_sql = "SELECT *, composicao.NOME as COMPOSICAO, faccao.NOME as FACCAO_ID, lote.NUMERO as NUMERO FROM `lote` join faccao, composicao WHERE lote.`ID` = ? and lote.FACCAO_ID = faccao.ID and lote.COMPOSICAO_ID = composicao.ID";

            $row_sql = $app['db']->fetchAssoc($find_sql, array($id));

            if (!$row_sql) {
                $app['session']->getFlashBag()->add(
                        'danger', array(
                    'message' => 'Não encontrado!',
                        )
                );
                return $app->redirect($app['url_generator']->generate('lote_list'));
            }

            $initial_data = array(
                'NUMERO' => $row_sql['NUMERO'],
                'DATA_ENVIO' => $row_sql['DATA_ENVIO'],
                'FACCAO_ID' => $row_sql['FACCAO_ID'],
            );

            $find_sql = "SELECT * FROM `faccao`";
            $resultFaccao = $app['db']->fetchAll($find_sql, array());
            $arrFaccao = array();

            foreach ($resultFaccao as $faccao) {
                $arrFaccao[$faccao['ID']] = $faccao['NOME'];
            }

            $find_sql = "SELECT item.ID,item.QUANTIDADE as SALDO,produto.NOME as NOME, produto.VALOR as VALOR, item.QUANTIDADE as QUANTIDADE, tamanho.nome as TAMANHO FROM `item` join tamanho, produto where lote_id = ? and tamanho.id = item.tamanho_id and produto.ID = item.PRODUTO_ID";
            $itens = $app['db']->fetchAll($find_sql, array($id));

            //$find_sql = "select *,baixa.QUANTIDADE as QUANTIDADE, item.ID,produto.NOME as NOME, tamanho.NOME as TAMANHO from baixa join item, tamanho, produto where baixa.ITEM_ID = item.ID and item.LOTE_ID = ? and tamanho.id = item.tamanho_id and produto.ID = item.PRODUTO_ID group by baixa.DATA";
            $find_sql = "select item.ID as ITEM_ID, baixa.QUANTIDADE as QUANTIDADE, baixa.DATA as DATA, produto.NOME as PRODUTO, tamanho.NOME as TAMANHO from baixa join item, produto, tamanho where baixa.ITEM_ID = item.ID and item.PRODUTO_ID = produto.ID and item.TAMANHO_ID = tamanho.ID and item.LOTE_ID = ? group by baixa.DATA";
            $resultBaixa = $app['db']->fetchAll($find_sql, array($id));
            
            $form = $app['form.factory']->createBuilder('form', $initial_data);
            $form = $form->add('NUMERO', 'text', array('label' => 'Número', 'required' => true));
            $form = $form->add('DATA_ENVIO', 'text', array('label' => 'Data de envio', 'required' => true));
            $form = $form->add('FACCAO_ID', 'choice', array('empty_value' => 'Selecione', 'choices' => $arrFaccao, 'label' => 'Facção', 'required' => false));
            $form = $form->getForm();

            $saldo = 0;
            
            $resultItem = $itens;
                        
            if ("POST" == $app['request']->getMethod()) {
                return $app->redirect($app['url_generator']->generate('lote_list'));
            }
                        
            return $app['twig']->render('lote/edit.html.twig', array(
                        "lote" => $row_sql,
                        "form" => $form->createView(),
                        "id" => $id,
                        "itens" => $resultItem,
                        "baixas" => $resultBaixa,
            ));
        })
        ->bind('lote_edit')->value('require_authentication', true);

$app->match('/lote/delete/{id}', function ($id) use ($app) {

            $find_sql = "SELECT * FROM `lote` WHERE `ID` = ?";
            $row_sql = $app['db']->fetchAssoc($find_sql, array($id));

            if ($row_sql) {
                $delete_query = "DELETE FROM `lote` WHERE `ID` = ?";
                $app['db']->executeUpdate($delete_query, array($id));

                $app['session']->getFlashBag()->add(
                        'success', array(
                    'message' => 'Lote excluído!',
                        )
                );
            } else {
                $app['session']->getFlashBag()->add(
                        'danger', array(
                    'message' => 'Não encontrado!',
                        )
                );
            }

            return $app->redirect($app['url_generator']->generate('lote_list'));
        })
        ->bind('lote_delete')->value('require_authentication', true);

$app->match('/lote/print/{id}', function ($id) use ($app) {

            $find_sql = "SELECT *,DATE_FORMAT( `DATA_ENVIO` , '%d/%m/%Y' ) as DATA_ENVIO, faccao.NOME as FACCAO FROM `lote` join faccao WHERE lote.`ID` = ? and lote.FACCAO_ID = faccao.ID";
            $row_sql = $app['db']->fetchAssoc($find_sql, array($id));

            if ($row_sql) {

                $find_sql = "SELECT item.nome as NOME, item.quantidade as QUANTIDADE, tamanho.nome as TAMANHO FROM `item` join tamanho where lote_id = ? and tamanho.id = item.tamanho_id";
                $resultItem = $app['db']->fetchAll($find_sql, array($id));

                return $app['twig']->render('lote/print.html.twig', array(
                            "lote" => $row_sql,
                            "id" => $id,
                            "itens" => $resultItem
                ));
            } else {
                $app['session']->getFlashBag()->add(
                        'danger', array(
                    'message' => 'Não encontrado!',
                        )
                );
            }

            return $app->redirect($app['url_generator']->generate('lote_list'));
        })
        ->bind('lote_print')->value('require_authentication', true);

$app->match('/lote/search/', function () use ($app) {

            $primary_key = "ID";
            $rows = array();

            $find_sql = "SELECT * from item where item.LOTE_ID = 'e'";
            
            if ("POST" == $app['request']->getMethod()) {
                $string = $app['request']->get('busca');
                $find_sql = "SELECT *, lote.NUMERO as LOTE_NUMERO,lote.ID as LOTE_ID, faccao.NOME as FACCAO from item join lote, produto, faccao where produto.NOME like '%".$string."%' and produto.ID = item.PRODUTO_ID and lote.ID = item.LOTE_ID and lote.FACCAO_ID = faccao.ID group by lote.ID order by  item.LOTE_ID desc";
            }
                        
            $rows    = $app['db']->fetchAll($find_sql);
            $arrItem = array();
            
            foreach($rows as $item) {                
                if(isEmAberto($app, $item['LOTE_ID'])) {
                    $arrItem[] = $item;
                }
            }
            
            return $app['twig']->render('lote/search.html.twig', array(
                        "primary_key" => $primary_key,
                        "rows" => $arrItem
            ));
        })
        ->bind('lote_search')->value('require_authentication', true);

$app->match('/lote/quantidade/', function () use ($app) {
            
            $id = $app['request']->get('id');
            $data = $app['request']->get('data');
                         
            $find_sql = "SELECT sum(QUANTIDADE) as QUANTIDADE from baixa where baixa.ITEM_ID = " . $id . ' AND baixa.DATA = "'.$data.'"';
            
            $row_sql = $app['db']->fetchAssoc($find_sql);
            
            $quantidade = $row_sql['QUANTIDADE'];
            
            return $quantidade;        
        })
        ->bind('lote_quantidade')->value('require_authentication', true);

$app->match('/lote/inacabadas/', function () use ($app) {
            
            $id = $app['request']->get('id');
                         
            $find_sql = "SELECT sum(PECAS_INACABADAS) as QUANTIDADE from baixa where baixa.ITEM_ID = " . $id;
                        
            $row_sql = $app['db']->fetchAssoc($find_sql);
            
            $quantidade = $row_sql['QUANTIDADE'];
            
            return $quantidade;        
        })
        ->bind('lote_inacabadas')->value('require_authentication', true);
        
function isEmAberto($app, $id) {
    
    $saldo    = 0;
    $baixat   = 0;
    $qtd      = 0;
    $find_sql = "SELECT * FROM `item` WHERE item.`LOTE_ID` = ".$id;
    $itens    = $app['db']->fetchAll($find_sql);
    
    foreach($itens as $item) {
        
        $qtd      = $item['QUANTIDADE'];
                
        $find_sql = "SELECT * FROM `baixa` WHERE baixa.`ITEM_ID` = ".$item['ID'];
        $baixas   = $app['db']->fetchAll($find_sql);
        
        foreach($baixas as $baixa) {
            $baixat += $baixa['QUANTIDADE'];
        }
        
        if($baixat < $qtd) {
            $saldo += $qtd - $baixat;
        }
       
        $baixat = 0;
        
    }
    
    if($saldo > 0) {
        return true;
    }
    
    return false;
    
}        
