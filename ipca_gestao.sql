-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 19-Mar-2026 às 23:52
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `ipca_gestao`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `cursos`
--

CREATE TABLE `cursos` (
  `Id_cursos` int(11) NOT NULL,
  `nome_cursos` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `cursos`
--

INSERT INTO `cursos` (`Id_cursos`, `nome_cursos`) VALUES
(1, 'Desenvolvimento Web e Multimédia'),
(2, 'Comércio Eletrónico'),
(3, 'Redes de Computadores');

-- --------------------------------------------------------

--
-- Estrutura da tabela `disciplinas`
--

CREATE TABLE `disciplinas` (
  `Id_disciplina` int(11) NOT NULL,
  `nome_disciplina` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `disciplinas`
--

INSERT INTO `disciplinas` (`Id_disciplina`, `nome_disciplina`) VALUES
(1, 'Matemática'),
(2, 'Programação Web'),
(3, 'Analise de sistemas');

-- --------------------------------------------------------

--
-- Estrutura da tabela `ficha_aluno`
--

CREATE TABLE `ficha_aluno` (
  `id` int(11) NOT NULL,
  `id_aluno` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `nome_completo` varchar(100) NOT NULL,
  `data_nascimento` date NOT NULL,
  `morada` varchar(255) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `email_contato` varchar(100) NOT NULL,
  `fotografia` varchar(255) DEFAULT NULL,
  `estado` enum('rascunho','submetida','aprovada','rejeitada') NOT NULL DEFAULT 'rascunho',
  `observacoes_gestor` text DEFAULT NULL,
  `data_submissao` datetime DEFAULT NULL,
  `data_validacao` datetime DEFAULT NULL,
  `id_gestor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `nota`
--

CREATE TABLE `nota` (
  `id` int(11) NOT NULL,
  `id_pauta` int(11) NOT NULL,
  `id_aluno` int(11) NOT NULL,
  `nota` decimal(5,2) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `data_lancamento` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `nota`
--

INSERT INTO `nota` (`id`, `id_pauta`, `id_aluno`, `nota`, `observacoes`, `data_lancamento`) VALUES
(2, 1, 1, 20.00, NULL, '2026-03-19 22:26:09');

-- --------------------------------------------------------

--
-- Estrutura da tabela `pauta`
--

CREATE TABLE `pauta` (
  `id` int(11) NOT NULL,
  `id_disciplina` int(11) NOT NULL,
  `ano_letivo` varchar(9) NOT NULL,
  `epoca` varchar(50) NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp(),
  `id_funcionario_criador` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `pauta`
--

INSERT INTO `pauta` (`id`, `id_disciplina`, `ano_letivo`, `epoca`, `data_criacao`, `id_funcionario_criador`) VALUES
(1, 1, '2025/2026', '1º Semestre', '2026-03-18 17:15:16', 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `pedido_matricula`
--

CREATE TABLE `pedido_matricula` (
  `id` int(11) NOT NULL,
  `id_aluno` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `data_pedido` datetime DEFAULT current_timestamp(),
  `estado` enum('pendente','aprovado','rejeitado') NOT NULL DEFAULT 'pendente',
  `observacoes` text DEFAULT NULL,
  `data_decisao` datetime DEFAULT NULL,
  `id_funcionario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `pedido_matricula`
--

INSERT INTO `pedido_matricula` (`id`, `id_aluno`, `id_curso`, `data_pedido`, `estado`, `observacoes`, `data_decisao`, `id_funcionario`) VALUES
(1, 1, 1, '2026-03-18 17:04:04', 'aprovado', '', '2026-03-18 17:04:48', 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `plano_estudos`
--

CREATE TABLE `plano_estudos` (
  `id` int(11) NOT NULL,
  `cursos` int(11) NOT NULL,
  `disciplinas` int(11) NOT NULL,
  `ano` int(11) NOT NULL,
  `semestre` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `plano_estudos`
--

INSERT INTO `plano_estudos` (`id`, `cursos`, `disciplinas`, `ano`, `semestre`) VALUES
(1, 2, 1, 0, 0),
(2, 1, 2, 0, 0),
(3, 2, 3, 0, 0),
(4, 1, 2, 1, 1),
(5, 2, 1, 1, 1),
(6, 2, 3, 2, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `utilizadores`
--

CREATE TABLE `utilizadores` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('aluno','funcionario','gestor','admin') NOT NULL,
  `data_registo` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `utilizadores`
--

INSERT INTO `utilizadores` (`id`, `nome`, `email`, `password`, `role`, `data_registo`) VALUES
(1, 'Aluno Teste', 'aluno@teste.com', '$2y$10$VGoNc/tvB1KA0NkK.fQCLekvnZrBeQ6AUjdD9I49HMryFrxY3OKmi', 'aluno', '2026-03-18 11:26:30'),
(2, 'Funcionário Teste', 'func@teste.com', '$2y$10$numoHNkw4u1L96Y6CYLhp.n.d9ugetmUDJOgmkLltt7o.XyYVTMgG', 'funcionario', '2026-03-18 11:26:30'),
(3, 'Gestor Teste', 'gestor@teste.com', '$2y$10$2rjspICZocYxSUvxLYqDme6g.0uBPVRoHgiYACVo8zwnNM/szebuu', 'gestor', '2026-03-18 11:26:30'),
(5, 'Afonsus', 'afonsosantos.25062007@gmail.com', '$2y$10$cwMAuYRCTK9CBdaOd3fWJuiflnNk7osKuEEXuAncsRatdptnbvtku', 'admin', '2026-03-18 13:53:38'),
(8, 'rodrigo', 'rodrigo22passos@gmail.com', '$2y$10$aAJOZqU90RQ5hkRcW5jy3eQAJOvw8j02/oLa6y3suGLnXqCzPecVe', 'admin', '2026-03-19 22:40:44');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`Id_cursos`);

--
-- Índices para tabela `disciplinas`
--
ALTER TABLE `disciplinas`
  ADD PRIMARY KEY (`Id_disciplina`);

--
-- Índices para tabela `ficha_aluno`
--
ALTER TABLE `ficha_aluno`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ficha_aluno` (`id_aluno`),
  ADD KEY `fk_ficha_curso` (`id_curso`),
  ADD KEY `fk_ficha_gestor` (`id_gestor`);

--
-- Índices para tabela `nota`
--
ALTER TABLE `nota`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_pauta_aluno` (`id_pauta`,`id_aluno`),
  ADD KEY `fk_nota_aluno` (`id_aluno`);

--
-- Índices para tabela `pauta`
--
ALTER TABLE `pauta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pauta_disciplina` (`id_disciplina`),
  ADD KEY `fk_pauta_funcionario` (`id_funcionario_criador`);

--
-- Índices para tabela `pedido_matricula`
--
ALTER TABLE `pedido_matricula`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pedido_aluno` (`id_aluno`),
  ADD KEY `fk_pedido_curso` (`id_curso`),
  ADD KEY `fk_pedido_funcionario` (`id_funcionario`);

--
-- Índices para tabela `plano_estudos`
--
ALTER TABLE `plano_estudos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_plano_cursos` (`cursos`),
  ADD KEY `fk_plano_disciplinas` (`disciplinas`);

--
-- Índices para tabela `utilizadores`
--
ALTER TABLE `utilizadores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cursos`
--
ALTER TABLE `cursos`
  MODIFY `Id_cursos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `disciplinas`
--
ALTER TABLE `disciplinas`
  MODIFY `Id_disciplina` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `ficha_aluno`
--
ALTER TABLE `ficha_aluno`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `nota`
--
ALTER TABLE `nota`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `pauta`
--
ALTER TABLE `pauta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `pedido_matricula`
--
ALTER TABLE `pedido_matricula`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `plano_estudos`
--
ALTER TABLE `plano_estudos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `utilizadores`
--
ALTER TABLE `utilizadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `ficha_aluno`
--
ALTER TABLE `ficha_aluno`
  ADD CONSTRAINT `fk_ficha_aluno` FOREIGN KEY (`id_aluno`) REFERENCES `utilizadores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ficha_curso` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`Id_cursos`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ficha_gestor` FOREIGN KEY (`id_gestor`) REFERENCES `utilizadores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limitadores para a tabela `nota`
--
ALTER TABLE `nota`
  ADD CONSTRAINT `fk_nota_aluno` FOREIGN KEY (`id_aluno`) REFERENCES `utilizadores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_nota_pauta` FOREIGN KEY (`id_pauta`) REFERENCES `pauta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `pauta`
--
ALTER TABLE `pauta`
  ADD CONSTRAINT `fk_pauta_disciplina` FOREIGN KEY (`id_disciplina`) REFERENCES `disciplinas` (`Id_disciplina`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pauta_funcionario` FOREIGN KEY (`id_funcionario_criador`) REFERENCES `utilizadores` (`id`) ON UPDATE CASCADE;

--
-- Limitadores para a tabela `pedido_matricula`
--
ALTER TABLE `pedido_matricula`
  ADD CONSTRAINT `fk_pedido_aluno` FOREIGN KEY (`id_aluno`) REFERENCES `utilizadores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pedido_curso` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`Id_cursos`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pedido_funcionario` FOREIGN KEY (`id_funcionario`) REFERENCES `utilizadores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limitadores para a tabela `plano_estudos`
--
ALTER TABLE `plano_estudos`
  ADD CONSTRAINT `fk_plano_cursos` FOREIGN KEY (`cursos`) REFERENCES `cursos` (`Id_cursos`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_plano_disciplinas` FOREIGN KEY (`disciplinas`) REFERENCES `disciplinas` (`Id_disciplina`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
