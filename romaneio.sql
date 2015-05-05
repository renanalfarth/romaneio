-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           5.6.16 - MySQL Community Server (GPL)
-- OS do Servidor:               Win32
-- HeidiSQL Versão:              8.3.0.4694
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Copiando estrutura do banco de dados para romaneio
CREATE DATABASE IF NOT EXISTS `romaneio` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `romaneio`;


-- Copiando estrutura para tabela romaneio.baixa
CREATE TABLE IF NOT EXISTS `baixa` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `DATA` datetime NOT NULL,
  `QUANTIDADE` int(11) NOT NULL,
  `PECAS_INACABADAS` int(11) NOT NULL,
  `ITEM_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `UNIQ_3C1F11C91567EAA4` (`ITEM_ID`),
  CONSTRAINT `FK_3C1F11C91567EAA4` FOREIGN KEY (`ITEM_ID`) REFERENCES `item` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Copiando dados para a tabela romaneio.baixa: ~9 rows (aproximadamente)
DELETE FROM `baixa`;
/*!40000 ALTER TABLE `baixa` DISABLE KEYS */;
INSERT INTO `baixa` (`ID`, `DATA`, `QUANTIDADE`, `PECAS_INACABADAS`, `ITEM_ID`) VALUES
	(13, '2014-10-29 00:00:00', 4, 0, 11),
	(14, '2014-10-29 00:00:00', 2, 0, 12),
	(15, '2014-10-28 00:00:00', 2, 0, 10),
	(16, '2014-10-28 00:00:00', 2, 0, 11),
	(17, '2014-10-28 00:00:00', 2, 0, 12),
	(21, '2014-10-29 00:00:00', 0, 3, 11),
	(22, '2014-10-29 00:00:00', 1, 0, 12),
	(26, '2014-11-10 00:00:00', 3, 0, 14),
	(27, '2014-11-10 00:00:00', 5, 0, 15),
	(28, '2014-11-10 00:00:00', 6, 0, 16);
/*!40000 ALTER TABLE `baixa` ENABLE KEYS */;


-- Copiando estrutura para tabela romaneio.composicao
CREATE TABLE IF NOT EXISTS `composicao` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NOME` varchar(150) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela romaneio.composicao: ~0 rows (aproximadamente)
DELETE FROM `composicao`;
/*!40000 ALTER TABLE `composicao` DISABLE KEYS */;
INSERT INTO `composicao` (`ID`, `NOME`) VALUES
	(1, '100% Poliamida');
/*!40000 ALTER TABLE `composicao` ENABLE KEYS */;


-- Copiando estrutura para tabela romaneio.faccao
CREATE TABLE IF NOT EXISTS `faccao` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NUMERO` int(11) NOT NULL DEFAULT '0',
  `NOME` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `ENDERECO` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `TELEFONE` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Copiando dados para a tabela romaneio.faccao: ~1 rows (aproximadamente)
DELETE FROM `faccao`;
/*!40000 ALTER TABLE `faccao` DISABLE KEYS */;
INSERT INTO `faccao` (`ID`, `NUMERO`, `NOME`, `ENDERECO`, `TELEFONE`) VALUES
	(1, 152, 'Compania Costura', 'R', '47'),
	(2, 48, 'Teste', 'R', '47');
/*!40000 ALTER TABLE `faccao` ENABLE KEYS */;


-- Copiando estrutura para tabela romaneio.item
CREATE TABLE IF NOT EXISTS `item` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `QUANTIDADE` int(11) NOT NULL,
  `PRODUTO_ID` int(11) NOT NULL,
  `TAMANHO_ID` int(11) DEFAULT NULL,
  `LOTE_ID` int(11),
  PRIMARY KEY (`ID`),
  KEY `UNIQ_1F1B251E6334D846` (`TAMANHO_ID`),
  KEY `UNIQ_1F1B251EB67AA186` (`LOTE_ID`),
  KEY `FK_item_produto` (`PRODUTO_ID`),
  CONSTRAINT `FK_item_lote` FOREIGN KEY (`LOTE_ID`) REFERENCES `lote` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_item_produto` FOREIGN KEY (`PRODUTO_ID`) REFERENCES `produto` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_item_tamanho` FOREIGN KEY (`TAMANHO_ID`) REFERENCES `tamanho` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Copiando dados para a tabela romaneio.item: ~6 rows (aproximadamente)
DELETE FROM `item`;
/*!40000 ALTER TABLE `item` DISABLE KEYS */;
INSERT INTO `item` (`ID`, `QUANTIDADE`, `PRODUTO_ID`, `TAMANHO_ID`, `LOTE_ID`) VALUES
	(9, 10, 2, 1, 6),
	(10, 10, 1, 1, 7),
	(11, 10, 1, 3, 7),
	(12, 5, 1, 4, 7),
	(14, 3, 1, 1, 9),
	(15, 5, 1, 4, 9),
	(16, 6, 1, 6, 9);
/*!40000 ALTER TABLE `item` ENABLE KEYS */;


-- Copiando estrutura para tabela romaneio.lote
CREATE TABLE IF NOT EXISTS `lote` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NUMERO` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `DATA_ENVIO` date NOT NULL,
  `OBSERVACAO` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `LINHA_COR` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `USUARIO_ID` int(11) DEFAULT NULL,
  `FACCAO_ID` int(11) DEFAULT NULL,
  `COMPOSICAO_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `UNIQ_65B4329FE8EA1841` (`USUARIO_ID`),
  KEY `UNIQ_65B4329FC3979AAC` (`FACCAO_ID`),
  KEY `COMPOSICAO_ID` (`COMPOSICAO_ID`),
  CONSTRAINT `FK_65B4329FC3979AAC` FOREIGN KEY (`FACCAO_ID`) REFERENCES `faccao` (`ID`),
  CONSTRAINT `FK_65B4329FE8EA1841` FOREIGN KEY (`USUARIO_ID`) REFERENCES `usuario` (`ID`),
  CONSTRAINT `FK_lote_composicao` FOREIGN KEY (`COMPOSICAO_ID`) REFERENCES `composicao` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Copiando dados para a tabela romaneio.lote: ~2 rows (aproximadamente)
DELETE FROM `lote`;
/*!40000 ALTER TABLE `lote` DISABLE KEYS */;
INSERT INTO `lote` (`ID`, `NUMERO`, `DATA_ENVIO`, `OBSERVACAO`, `LINHA_COR`, `USUARIO_ID`, `FACCAO_ID`, `COMPOSICAO_ID`) VALUES
	(6, '990', '2014-10-27', 'Teste', '', 2, 1, 1),
	(7, '900', '2014-10-29', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi pretium enim ligula, ut ullamcorper enim facilisis ac. Donec congue congue erat sit amet lacinia. Duis non pulvinar sapien.', 'Preta e cinza', 2, 1, 1),
	(9, '650', '2014-10-29', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi pretium enim ligula, ut ullamcorper enim facilisis ac. Donec congue congue erat sit amet lacinia. Duis non pulvinar sapien.', 'Preto', 2, 1, 1);
/*!40000 ALTER TABLE `lote` ENABLE KEYS */;


-- Copiando estrutura para tabela romaneio.produto
CREATE TABLE IF NOT EXISTS `produto` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CODIGO` varchar(50) NOT NULL,
  `NOME` varchar(50) NOT NULL,
  `VALOR` float DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela romaneio.produto: ~2 rows (aproximadamente)
DELETE FROM `produto`;
/*!40000 ALTER TABLE `produto` DISABLE KEYS */;
INSERT INTO `produto` (`ID`, `CODIGO`, `NOME`, `VALOR`) VALUES
	(1, '00129', 'Corsário', 2.5),
	(2, '003292', 'Legging', 2.8);
/*!40000 ALTER TABLE `produto` ENABLE KEYS */;


-- Copiando estrutura para tabela romaneio.tamanho
CREATE TABLE IF NOT EXISTS `tamanho` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NOME` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Copiando dados para a tabela romaneio.tamanho: ~5 rows (aproximadamente)
DELETE FROM `tamanho`;
/*!40000 ALTER TABLE `tamanho` DISABLE KEYS */;
INSERT INTO `tamanho` (`ID`, `NOME`) VALUES
	(1, 'P'),
	(3, 'M'),
	(4, 'G'),
	(5, 'GG'),
	(6, 'XGG');
/*!40000 ALTER TABLE `tamanho` ENABLE KEYS */;


-- Copiando estrutura para tabela romaneio.usuario
CREATE TABLE IF NOT EXISTS `usuario` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NOME` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `EMAIL` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `SENHA` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Copiando dados para a tabela romaneio.usuario: ~0 rows (aproximadamente)
DELETE FROM `usuario`;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` (`ID`, `NOME`, `EMAIL`, `SENHA`) VALUES
	(1, 'Renan', 'renan@renan.com', 'e10adc3949ba59abbe56e057f20f883e'),
	(2, 'Usuario', 'usuario@usuario.com', 'e10adc3949ba59abbe56e057f20f883e');
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
