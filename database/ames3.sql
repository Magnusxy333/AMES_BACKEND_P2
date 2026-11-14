-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2026 at 02:07 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ames3`
--

-- --------------------------------------------------------

--
-- Table structure for table `atividade`
--

CREATE TABLE `atividade` (
  `id_atividade` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `titulo` varchar(150) DEFAULT NULL,
  `descricao` varchar(500) DEFAULT NULL,
  `data_hora_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_hora_inicio` timestamp NULL DEFAULT NULL,
  `data_hora_fim` timestamp NULL DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL,
  `latitude` decimal(9,6) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `atividade`
--

INSERT INTO `atividade` (`id_atividade`, `id_usuario`, `id_categoria`, `titulo`, `descricao`, `data_hora_criacao`, `data_hora_inicio`, `data_hora_fim`, `status`, `longitude`, `latitude`, `endereco`) VALUES
(20, 6, 2, 'lecionar na fatec mauá', 'Aula de Laboratório Web', '2026-11-14 00:47:35', '2026-11-18 22:00:00', '2026-11-19 01:30:00', 'pendente', -46.460176, -23.663471, 'Av. Antônia Rosa Fioravanti, 804 - Centro, Mauá - SP, 09390-120'),
(21, 6, 2, 'Reunião na prefeitura', 'Organização da equipe de ti', '2026-11-14 00:50:47', '2026-10-08 00:48:00', '2026-10-08 01:48:00', 'pendente', -46.464123, -23.666913, 'Av. João Ramalho, 205 - Vila Noemia, Mauá - SP, 09371-522'),
(22, 6, 2, 'Palestra na fatec', 'Palestrar sobre o quão bom é o PHP', '2026-11-14 00:55:04', '2026-11-11 22:00:00', '2026-11-11 23:30:00', 'concluida', -46.460176, -23.663471, 'Av. Antônia Rosa Fioravanti, 804 - Centro, Mauá - SP, 09390-120');

-- --------------------------------------------------------

--
-- Table structure for table `categoria_atividade`
--

CREATE TABLE `categoria_atividade` (
  `id_categoria` int(11) NOT NULL,
  `nome_categoria` varchar(80) DEFAULT NULL,
  `descricao` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categoria_atividade`
--

INSERT INTO `categoria_atividade` (`id_categoria`, `nome_categoria`, `descricao`) VALUES
(1, 'tarefa domestica', NULL),
(2, 'compromisso', NULL),
(3, 'medicamento', NULL),
(4, 'outro', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lembrete`
--

CREATE TABLE `lembrete` (
  `id_lembrete` int(11) NOT NULL,
  `id_atividade` int(11) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `horario` time DEFAULT NULL,
  `mensagem` varchar(500) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'agendado',
  `data_hora_envio` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lembrete`
--

INSERT INTO `lembrete` (`id_lembrete`, `id_atividade`, `data`, `horario`, `mensagem`, `status`, `data_hora_envio`) VALUES
(12, 22, '2026-11-11', '16:00:00', 'Chegar antes', 'agendado', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `log_usuario`
--

CREATE TABLE `log_usuario` (
  `id_log` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `horario` time DEFAULT NULL,
  `acao` varchar(50) DEFAULT NULL,
  `descricao` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_usuario`
--

INSERT INTO `log_usuario` (`id_log`, `id_usuario`, `data`, `horario`, `acao`, `descricao`) VALUES
(23, 6, '2026-11-13', '21:31:17', 'CADASTRO_NOVO', 'Usuário realizou o cadastro no sistema.'),
(24, 6, '2026-11-13', '21:31:27', 'LOGIN', 'Usuário autenticou no sistema com sucesso.'),
(25, 6, '2026-11-13', '21:31:50', 'ATUALIZACAO_FOTO', 'Usuário alterou a foto de perfil.'),
(26, 6, '2026-11-13', '21:40:49', 'Cadastro de atividade', 'Cadastrou a atividade: lecionar na fatec mauá'),
(27, 6, '2026-11-13', '21:41:00', 'Atividade: excluir', 'Atividade: lecionar na fatec mauá'),
(28, 6, '2026-11-13', '21:47:35', 'Cadastro de atividade', 'Cadastrou a atividade: lecionar na fatec mauá'),
(29, 6, '2026-11-13', '21:50:47', 'Cadastro de atividade', 'Cadastrou a atividade: Reunião na prefeitura'),
(30, 6, '2026-11-13', '21:55:04', 'Cadastro de atividade', 'Cadastrou a atividade: Palestra na fatec'),
(31, 6, '2026-11-13', '21:55:08', 'Atividade: concluir', 'Atividade: Palestra na fatec'),
(32, 6, '2026-11-13', '21:56:34', 'ATUALIZACAO_PERFIL', 'Usuário atualizou seus dados cadastrais.'),
(33, 6, '2026-11-13', '21:58:10', 'Cadastro de lembrete', 'Cadastrou um lembrete para a atividade 22');

-- --------------------------------------------------------

--
-- Table structure for table `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `sobrenome` varchar(150) DEFAULT NULL,
  `email` varchar(254) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `telefone` varchar(20) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nome`, `sobrenome`, `email`, `senha`, `data_criacao`, `telefone`, `data_nascimento`, `endereco`, `foto_perfil`) VALUES
(6, 'Kleber', 'da Silva Divino', 'kleber@gmail.com', '$2y$10$quyP8qnQ.n289VNXBNjhreRNILziiPHZtoIa2Q.l0ZaPhZwSlfk9C', '2026-11-14 00:56:34', '(11) 4543-3221', '1782-02-13', 'Av. Antônia Rosa Fioravanti, 804 - Centro, Mauá - SP, 09390-120', 'perfil_6_1794616310.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `atividade`
--
ALTER TABLE `atividade`
  ADD PRIMARY KEY (`id_atividade`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indexes for table `categoria_atividade`
--
ALTER TABLE `categoria_atividade`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indexes for table `lembrete`
--
ALTER TABLE `lembrete`
  ADD PRIMARY KEY (`id_lembrete`),
  ADD KEY `id_atividade` (`id_atividade`);

--
-- Indexes for table `log_usuario`
--
ALTER TABLE `log_usuario`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indexes for table `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `atividade`
--
ALTER TABLE `atividade`
  MODIFY `id_atividade` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `categoria_atividade`
--
ALTER TABLE `categoria_atividade`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lembrete`
--
ALTER TABLE `lembrete`
  MODIFY `id_lembrete` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `log_usuario`
--
ALTER TABLE `log_usuario`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `atividade`
--
ALTER TABLE `atividade`
  ADD CONSTRAINT `atividade_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `atividade_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categoria_atividade` (`id_categoria`);

--
-- Constraints for table `lembrete`
--
ALTER TABLE `lembrete`
  ADD CONSTRAINT `lembrete_ibfk_1` FOREIGN KEY (`id_atividade`) REFERENCES `atividade` (`id_atividade`);

--
-- Constraints for table `log_usuario`
--
ALTER TABLE `log_usuario`
  ADD CONSTRAINT `log_usuario_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
