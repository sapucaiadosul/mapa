-- phpMyAdmin SQL Dump
-- version 3.5.4
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tempo de Geração: 
-- Versão do Servidor: 5.5.28-log
-- Versão do PHP: 5.4.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Banco de Dados: `mapa`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `acao`
--

CREATE TABLE IF NOT EXISTS `acao` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `meta_id` int(5) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `prazo` date DEFAULT NULL,
  `concluida` datetime DEFAULT NULL,
  `concluida_id` int(5) DEFAULT NULL,
  `monitorada` datetime DEFAULT NULL,
  `monitor_id` int(5) DEFAULT NULL,
  `aprovada` tinyint(1) DEFAULT NULL,
  `criado` datetime NOT NULL,
  `criador_id` int(5) NOT NULL,
  `modificado` datetime DEFAULT NULL,
  `modificador_id` int(5) DEFAULT NULL,
  `desativado` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `meta_id` (`meta_id`),
  KEY `criador_id` (`criador_id`),
  KEY `modificador_id` (`modificador_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `acompanhamento`
--

CREATE TABLE IF NOT EXISTS `acompanhamento` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `meta_id` int(5) NOT NULL,
  `texto` text NOT NULL,
  `tipo` enum('0','1','2','3') NOT NULL DEFAULT '0',
  `criado` datetime NOT NULL,
  `criador_id` int(5) NOT NULL,
  `modificado` datetime DEFAULT NULL,
  `modificador_id` int(5) DEFAULT NULL,
  `desativado` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `meta_id` (`meta_id`),
  KEY `criador_id` (`criador_id`),
  KEY `modificador_id` (`modificador_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `anexo`
--

CREATE TABLE IF NOT EXISTS `anexo` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `meta_id` int(5) NOT NULL,
  `nome` varchar(80) NOT NULL,
  `extensao` varchar(4) NOT NULL,
  `criado` datetime NOT NULL,
  `criador_id` int(5) NOT NULL,
  `modificado` datetime DEFAULT NULL,
  `modificador_id` int(5) DEFAULT NULL,
  `desativado` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `meta_id` (`meta_id`),
  KEY `criador_id` (`criador_id`),
  KEY `modificador_id` (`modificador_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `mapa`
--

CREATE TABLE IF NOT EXISTS `mapa` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `nome` varchar(40) NOT NULL,
  `criado` datetime NOT NULL,
  `criador_id` int(5) NOT NULL,
  `modificado` datetime DEFAULT NULL,
  `modificador_id` int(5) DEFAULT NULL,
  `desativado` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Extraindo dados da tabela `mapa`
--

INSERT INTO `mapa` (`id`, `nome`, `criado`, `criador_id`, `modificado`, `modificador_id`, `desativado`) VALUES
(1, '2025', '2025-01-21 12:00:58', 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `meta`
--

CREATE TABLE IF NOT EXISTS `meta` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `mapa_id` int(3) NOT NULL DEFAULT '1',
  `numero` int(4) DEFAULT NULL,
  `titulo` varchar(200) NOT NULL,
  `responsavel_id` int(3) NOT NULL,
  `responsavel_nome` varchar(40) DEFAULT NULL,
  `data_inicial` date NOT NULL,
  `data_final` date NOT NULL,
  `data_conclusao` date DEFAULT NULL,
  `ind_objetivo` varchar(300) DEFAULT NULL,
  `ind_ods` varchar(200) DEFAULT '[]',
  `ind_titulo` varchar(200) DEFAULT NULL,
  `ind_referencia` double DEFAULT NULL,
  `ind_indicador` double DEFAULT NULL,
  `ind_unidade` varchar(40) DEFAULT NULL,
  `ind_sec_valor` double DEFAULT NULL,
  `ind_sec_datahora` datetime DEFAULT NULL,
  `ind_mon_valor` double DEFAULT NULL,
  `ind_mon_datahora` datetime DEFAULT NULL,
  `manter_monitoria` tinyint(1) DEFAULT NULL,
  `criado` datetime NOT NULL,
  `criador_id` int(5) NOT NULL,
  `modificado` datetime DEFAULT NULL,
  `modificador_id` int(5) DEFAULT NULL,
  `desativado` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero` (`mapa_id`,`numero`) USING BTREE,
  KEY `responsavel_id` (`responsavel_id`),
  KEY `criador_id` (`criador_id`),
  KEY `modificador_id` (`modificador_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Extraindo dados da tabela `meta`
--

INSERT INTO `meta` (`id`, `mapa_id`, `numero`, `titulo`, `responsavel_id`, `responsavel_nome`, `data_inicial`, `data_final`, `data_conclusao`, `ind_objetivo`, `ind_ods`, `ind_titulo`, `ind_referencia`, `ind_indicador`, `ind_unidade`, `ind_sec_valor`, `ind_sec_datahora`, `ind_mon_valor`, `ind_mon_datahora`, `manter_monitoria`, `criado`, `criador_id`, `modificado`, `modificador_id`, `desativado`) VALUES
(1, 1, 1, 'Teste 01 2025', 5, 'Maria', '2025-01-14', '2025-12-01', NULL, 'Teste', '["5","6"]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-01-21 13:55:59', NULL, '2025-01-21 13:55:59', 2, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `modulo`
--

CREATE TABLE IF NOT EXISTS `modulo` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `nome` varchar(20) NOT NULL,
  `arquivo` varchar(20) NOT NULL,
  `criado` datetime NOT NULL,
  `criador_id` int(5) NOT NULL,
  `modificado` datetime DEFAULT NULL,
  `modificador_id` int(5) DEFAULT NULL,
  `desativado` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Extraindo dados da tabela `modulo`
--

INSERT INTO `modulo` (`id`, `nome`, `arquivo`, `criado`, `criador_id`, `modificado`, `modificador_id`, `desativado`) VALUES
(1, 'Usuário', 'usuario', '2016-08-17 16:20:22', 1, NULL, NULL, NULL),
(2, 'Perfil', 'perfil', '2016-09-06 17:09:23', 1, NULL, NULL, NULL),
(3, 'Meta', 'meta', '2017-01-19 11:02:36', 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `perfil`
--

CREATE TABLE IF NOT EXISTS `perfil` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `nome` varchar(20) NOT NULL,
  `criado` datetime NOT NULL,
  `criador_id` int(5) NOT NULL,
  `modificado` datetime DEFAULT NULL,
  `modificador_id` int(5) DEFAULT NULL,
  `desativado` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Extraindo dados da tabela `perfil`
--

INSERT INTO `perfil` (`id`, `nome`, `criado`, `criador_id`, `modificado`, `modificador_id`, `desativado`) VALUES
(1, 'Administrador', '2016-08-15 16:37:06', 1, '2022-01-22 22:31:22', 598, NULL),
(2, 'GP', '2016-09-12 15:53:40', 1, '2017-05-05 11:53:51', 1, NULL),
(3, 'SMAD', '2016-09-12 15:53:57', 1, '2021-02-25 13:41:51', 3893, NULL),
(4, 'SMCTE', '2016-09-12 15:54:11', 1, '2018-02-22 09:22:28', 1, NULL),
(5, 'PGM', '2017-03-06 15:24:36', 1, '2024-01-30 15:57:40', 4008, NULL),
(6, 'SMDUH', '2017-03-06 15:24:49', 1, '2017-03-07 15:57:19', 1, NULL),
(7, 'SMCEL', '2017-03-06 15:24:59', 1, '2017-03-07 15:57:07', 1, NULL),
(8, 'SMF', '2017-03-06 15:25:22', 1, '2017-03-07 15:57:28', 1, NULL),
(9, 'SMDEI', '2017-03-06 15:25:55', 1, '2017-03-07 15:57:15', 1, NULL),
(10, 'SME', '2017-03-06 15:26:03', 1, '2017-03-07 15:57:24', 1, NULL),
(11, 'SMMA', '2017-03-06 15:26:15', 1, '2017-03-07 15:57:36', 1, NULL),
(12, 'SMOSU', '2017-03-06 15:26:31', 1, '2017-03-07 15:57:40', 1, NULL),
(13, 'SMS', '2017-03-06 15:26:38', 1, '2017-03-07 15:57:50', 1, NULL),
(14, 'SMSMU', '2017-03-06 15:26:48', 1, '2017-03-07 15:57:55', 1, NULL),
(15, 'FSPSCE', '2017-03-09 16:05:00', 1, '2024-01-30 15:57:23', 4008, NULL),
(16, 'Visualização', '2018-06-25 11:38:51', 1, '2018-06-25 11:39:52', 1, NULL),
(17, 'PREV-ESTEIO', '2019-12-11 09:39:11', 74, NULL, NULL, NULL),
(18, 'SMGG', '2020-12-29 11:11:25', 74, NULL, NULL, NULL),
(19, 'SMSP', '2020-12-29 11:11:47', 74, '2020-12-29 11:23:03', 74, 1),
(20, 'SMCDH', '2020-12-29 11:12:01', 74, NULL, NULL, NULL),
(21, 'SMCDH', '2020-12-29 11:12:08', 74, NULL, NULL, NULL),
(22, 'SMSP', '2020-12-29 11:12:18', 74, NULL, NULL, NULL),
(23, 'SMU', '2020-12-29 11:12:27', 74, NULL, NULL, NULL),
(24, 'SMDEMA', '2020-12-29 11:12:40', 74, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `permissao`
--

CREATE TABLE IF NOT EXISTS `permissao` (
  `perfil_id` int(3) NOT NULL,
  `modulo_id` int(2) NOT NULL,
  `acao` int(1) NOT NULL,
  `tipo` int(1) NOT NULL,
  `criado` datetime NOT NULL,
  `criador_id` int(5) NOT NULL,
  `modificado` datetime DEFAULT NULL,
  `modificador_id` int(5) DEFAULT NULL,
  `desativado` tinyint(1) DEFAULT NULL,
  UNIQUE KEY `perfil_id` (`perfil_id`,`modulo_id`,`acao`) USING BTREE,
  KEY `tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `permissao`
--

INSERT INTO `permissao` (`perfil_id`, `modulo_id`, `acao`, `tipo`, `criado`, `criador_id`, `modificado`, `modificador_id`, `desativado`) VALUES
(1, 1, 1, 3, '2016-08-17 16:21:22', 1, '2022-01-22 22:31:22', 598, NULL),
(1, 1, 2, 1, '0000-00-00 00:00:00', 0, '2022-01-22 22:31:22', 598, NULL),
(1, 1, 3, 2, '2016-09-08 12:09:52', 1, '2022-01-22 22:31:22', 598, NULL),
(1, 1, 4, 2, '2016-09-06 12:52:27', 1, '2022-01-22 22:31:22', 598, NULL),
(1, 2, 1, 3, '2016-09-06 16:17:55', 1, '2022-01-22 22:31:22', 598, NULL),
(1, 2, 2, 1, '2016-09-12 12:52:28', 1, '2022-01-22 22:31:22', 598, NULL),
(1, 2, 3, 2, '2016-09-06 12:52:27', 1, '2022-01-22 22:31:22', 598, NULL),
(1, 2, 4, 2, '2016-09-08 12:21:54', 1, '2022-01-22 22:31:22', 598, NULL),
(1, 3, 1, 3, '2017-01-19 11:02:49', 1, '2022-01-22 22:31:22', 598, NULL),
(1, 3, 2, 1, '2017-01-19 11:02:49', 1, '2022-01-22 22:31:22', 598, NULL),
(1, 3, 3, 2, '2017-01-19 11:02:49', 1, '2022-01-22 22:31:22', 598, NULL),
(1, 3, 4, 2, '2017-01-19 11:02:49', 1, '2022-01-22 22:31:22', 598, NULL),
(2, 1, 1, 2, '2017-05-05 11:53:51', 1, NULL, NULL, NULL),
(2, 3, 1, 2, '2017-01-26 17:39:55', 1, '2017-05-05 11:53:51', 1, NULL),
(2, 3, 2, 1, '2017-01-26 17:39:55', 1, '2017-05-05 11:53:51', 1, NULL),
(2, 3, 3, 2, '2017-01-26 17:39:55', 1, '2017-05-05 11:53:51', 1, NULL),
(2, 3, 4, 2, '2017-01-26 17:39:55', 1, '2017-05-05 11:53:51', 1, NULL),
(3, 3, 1, 2, '2017-03-06 15:21:18', 1, '2021-02-25 13:41:51', 3893, NULL),
(3, 3, 2, 1, '2017-03-06 15:21:18', 1, '2021-02-25 13:41:51', 3893, 1),
(3, 3, 3, 2, '2017-03-06 15:21:18', 1, '2021-02-25 13:41:51', 3893, NULL),
(4, 3, 1, 2, '2017-03-06 15:21:28', 1, '2018-02-22 09:22:28', 1, NULL),
(4, 3, 2, 1, '2017-03-06 15:21:28', 1, '2018-02-22 09:22:28', 1, 1),
(4, 3, 3, 2, '2017-03-06 15:21:28', 1, '2018-02-22 09:22:28', 1, NULL),
(5, 3, 1, 2, '2017-03-06 15:24:36', 1, '2024-01-30 15:57:40', 4008, NULL),
(5, 3, 2, 1, '2017-03-06 15:24:36', 1, '2024-01-30 15:57:40', 4008, 1),
(5, 3, 3, 2, '2017-03-06 15:24:36', 1, '2024-01-30 15:57:40', 4008, NULL),
(6, 3, 1, 2, '2017-03-06 15:24:49', 1, '2017-03-07 15:57:19', 1, NULL),
(6, 3, 2, 1, '2017-03-06 15:24:49', 1, '2017-03-07 15:57:19', 1, 1),
(6, 3, 3, 2, '2017-03-06 15:24:49', 1, '2017-03-07 15:57:19', 1, NULL),
(7, 3, 1, 2, '2017-03-06 15:24:59', 1, '2017-03-07 15:57:07', 1, NULL),
(7, 3, 2, 1, '2017-03-06 15:24:59', 1, '2017-03-07 15:57:07', 1, 1),
(7, 3, 3, 2, '2017-03-06 15:24:59', 1, '2017-03-07 15:57:07', 1, NULL),
(8, 3, 1, 2, '2017-03-06 15:25:22', 1, '2017-03-07 15:57:28', 1, NULL),
(8, 3, 2, 1, '2017-03-06 15:25:22', 1, '2017-03-07 15:57:28', 1, 1),
(8, 3, 3, 2, '2017-03-06 15:25:22', 1, '2017-03-07 15:57:28', 1, NULL),
(9, 3, 1, 2, '2017-03-06 15:25:55', 1, '2017-03-07 15:57:15', 1, NULL),
(9, 3, 2, 1, '2017-03-06 15:25:55', 1, '2017-03-07 15:57:15', 1, 1),
(9, 3, 3, 2, '2017-03-06 15:25:55', 1, '2017-03-07 15:57:15', 1, NULL),
(10, 3, 1, 2, '2017-03-06 15:26:03', 1, '2017-03-07 15:57:24', 1, NULL),
(10, 3, 2, 1, '2017-03-06 15:26:03', 1, '2017-03-07 15:57:24', 1, 1),
(10, 3, 3, 2, '2017-03-06 15:26:04', 1, '2017-03-07 15:57:24', 1, NULL),
(11, 3, 1, 2, '2017-03-06 15:26:15', 1, '2017-03-07 15:57:36', 1, NULL),
(11, 3, 2, 1, '2017-03-06 15:26:15', 1, '2017-03-07 15:57:36', 1, 1),
(11, 3, 3, 2, '2017-03-06 15:26:15', 1, '2017-03-07 15:57:36', 1, NULL),
(12, 3, 1, 2, '2017-03-06 15:26:31', 1, '2017-03-07 15:57:40', 1, NULL),
(12, 3, 2, 1, '2017-03-06 15:26:31', 1, '2017-03-07 15:57:40', 1, 1),
(12, 3, 3, 2, '2017-03-06 15:26:31', 1, '2017-03-07 15:57:40', 1, NULL),
(13, 3, 1, 2, '2017-03-06 15:26:38', 1, '2017-03-07 15:57:50', 1, NULL),
(13, 3, 2, 1, '2017-03-06 15:26:38', 1, '2017-03-07 15:57:50', 1, 1),
(13, 3, 3, 2, '2017-03-06 15:26:38', 1, '2017-03-07 15:57:50', 1, NULL),
(14, 3, 1, 2, '2017-03-06 15:26:48', 1, '2017-03-07 15:57:55', 1, NULL),
(14, 3, 2, 1, '2017-03-06 15:26:48', 1, '2017-03-07 15:57:55', 1, 1),
(14, 3, 3, 2, '2017-03-06 15:26:48', 1, '2017-03-07 15:57:55', 1, NULL),
(15, 3, 1, 2, '2017-03-09 16:05:00', 1, '2024-01-30 15:57:23', 4008, NULL),
(15, 3, 3, 2, '2017-03-09 16:05:00', 1, '2024-01-30 15:57:23', 4008, NULL),
(16, 3, 1, 2, '2018-06-25 11:38:51', 1, '2018-06-25 11:39:52', 1, NULL),
(16, 3, 3, 2, '2018-06-25 11:39:52', 1, NULL, NULL, NULL),
(17, 3, 1, 2, '2019-12-11 09:39:11', 74, NULL, NULL, NULL),
(17, 3, 3, 2, '2019-12-11 09:39:11', 74, NULL, NULL, NULL),
(18, 3, 1, 2, '2020-12-29 11:11:25', 74, NULL, NULL, NULL),
(18, 3, 3, 2, '2020-12-29 11:11:25', 74, NULL, NULL, NULL),
(19, 3, 1, 2, '2020-12-29 11:11:47', 74, NULL, NULL, NULL),
(19, 3, 3, 2, '2020-12-29 11:11:47', 74, NULL, NULL, NULL),
(20, 3, 1, 2, '2020-12-29 11:12:01', 74, NULL, NULL, NULL),
(20, 3, 3, 2, '2020-12-29 11:12:01', 74, NULL, NULL, NULL),
(21, 3, 1, 2, '2020-12-29 11:12:08', 74, NULL, NULL, NULL),
(21, 3, 3, 2, '2020-12-29 11:12:08', 74, NULL, NULL, NULL),
(22, 3, 1, 2, '2020-12-29 11:12:18', 74, NULL, NULL, NULL),
(22, 3, 3, 2, '2020-12-29 11:12:18', 74, NULL, NULL, NULL),
(23, 3, 1, 2, '2020-12-29 11:12:27', 74, NULL, NULL, NULL),
(23, 3, 3, 2, '2020-12-29 11:12:27', 74, NULL, NULL, NULL),
(24, 3, 1, 2, '2020-12-29 11:12:40', 74, NULL, NULL, NULL),
(24, 3, 3, 2, '2020-12-29 11:12:40', 74, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `perfil_id` int(3) NOT NULL,
  `usuario` varchar(20) NOT NULL,
  `senha` varchar(32) NOT NULL COMMENT 'MD5',
  `provisoria` tinyint(1) DEFAULT NULL,
  `nome` varchar(40) NOT NULL,
  `email` varchar(120) DEFAULT NULL,
  `criado` datetime NOT NULL,
  `criador_id` int(5) NOT NULL,
  `modificado` datetime DEFAULT NULL,
  `modificador_id` int(5) DEFAULT NULL,
  `desativado` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`id`, `perfil_id`, `usuario`, `senha`, `provisoria`, `nome`, `email`, `criado`, `criador_id`, `modificado`, `modificador_id`, `desativado`) VALUES
(1, 1, 'admin', '21232f297a57a5a743894a0e4a801fc3', NULL, 'Administrador', 'teste@email.rs.gov.br', '2016-08-15 16:36:36', 1, '2025-01-06 16:09:07', 0, NULL),
(2, 1, 'teste', '698dc19d489c4e4db73e28a713eab07b', NULL, 'Administrador', 'teste@email.rs.gov.br', '2016-08-15 16:36:36', 1, '2025-01-06 16:09:07', 4008, NULL),
(3, 16, 'teste1', 'e959088c6049f1104c84c9bde5560a13', NULL, 'Perfil básico', 'teste@gmail.com', '2025-01-21 14:05:55', 2, NULL, NULL, NULL);

--
-- Restrições para as tabelas dumpadas
--

--
-- Restrições para a tabela `acompanhamento`
--
ALTER TABLE `acompanhamento`
  ADD CONSTRAINT `meta_comentario` FOREIGN KEY (`meta_id`) REFERENCES `meta` (`id`) ON UPDATE NO ACTION;

--
-- Restrições para a tabela `anexo`
--
ALTER TABLE `anexo`
  ADD CONSTRAINT `meta_anexo` FOREIGN KEY (`meta_id`) REFERENCES `meta` (`id`) ON UPDATE NO ACTION;

--
-- Restrições para a tabela `meta`
--
ALTER TABLE `meta`
  ADD CONSTRAINT `meta_perfil` FOREIGN KEY (`responsavel_id`) REFERENCES `perfil` (`id`) ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
