-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- ホスト: localhost:8889
-- 生成日時: 2022 年 4 月 22 日 13:05
-- サーバのバージョン： 5.7.34
-- PHP のバージョン: 7.4.21
​
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
​
​
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
​
--
-- データベース: `autumn_curriculum`
--
​
-- --------------------------------------------------------
​
--
-- テーブルの構造 `enemy_cards`
--
​
CREATE TABLE `enemy_cards` (
  `id` int(11) NOT NULL,
  `mark` varchar(10) NOT NULL,
  `number` int(10) NOT NULL,
  `create_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='相手が保持しているカードテーブル';
​
--
-- テーブルのデータのダンプ `enemy_cards`
--
​
INSERT INTO `enemy_cards` (`id`, `mark`, `number`, `create_timestamp`, `update_timestamp`) VALUES
(1, 'heart', 13, '2022-04-17 06:44:14', '2022-04-17 06:44:14'),
(2, 'diamond', 9, '2022-04-17 06:44:14', '2022-04-17 06:44:14'),
(3, 'spade', 1, '2022-04-17 06:44:14', '2022-04-17 06:44:14'),
(4, 'club', 9, '2022-04-17 06:44:14', '2022-04-17 06:44:14'),
(5, 'diamond', 6, '2022-04-17 06:44:14', '2022-04-17 06:44:14');
​
--
-- ダンプしたテーブルのインデックス
--
​
--
-- テーブルのインデックス `enemy_cards`
--
ALTER TABLE `enemy_cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `create_timestamp` (`create_timestamp`),
  ADD KEY `update_timestamp` (`update_timestamp`);
​
--
-- ダンプしたテーブルの AUTO_INCREMENT
--
​
--
-- テーブルの AUTO_INCREMENT `enemy_cards`
--
ALTER TABLE `enemy_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;
​
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;