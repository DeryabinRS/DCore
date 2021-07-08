-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Июл 08 2021 г., 07:39
-- Версия сервера: 5.6.41
-- Версия PHP: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `dcore`
--

-- --------------------------------------------------------

--
-- Структура таблицы `dcore_banners`
--

CREATE TABLE `dcore_banners` (
  `id` int(11) NOT NULL,
  `img` int(11) NOT NULL,
  `date1` int(11) DEFAULT NULL,
  `date2` int(11) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `position` int(11) NOT NULL,
  `visible` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `dcore_banners`
--

INSERT INTO `dcore_banners` (`id`, `img`, `date1`, `date2`, `link`, `position`, `visible`) VALUES
(2, 1625469689, 1625418000, 1625418000, '', 1, 1),
(3, 1625469878, 1625418000, 1625418000, '', 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `dcore_gallery`
--

CREATE TABLE `dcore_gallery` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` int(11) DEFAULT NULL,
  `author` int(11) DEFAULT NULL,
  `date_create` int(11) DEFAULT NULL,
  `visible` int(1) DEFAULT '1',
  `name_en` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `dcore_menu`
--

CREATE TABLE `dcore_menu` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent` int(11) NOT NULL,
  `sort` int(11) NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `link` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `icon` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `dcore_menu`
--

INSERT INTO `dcore_menu` (`id`, `name`, `name_en`, `parent`, `sort`, `description`, `link`, `icon`) VALUES
(1, 'Главная', 'Home', 0, 1, NULL, '', 'fa fa-home'),
(2, 'О нас', 'About', 0, 2, NULL, 'about', 'fa fa-list'),
(3, 'Контентная страница', 'Content Page', 0, 3, NULL, 'pages/contentpage', 'fa fa-list-alt');

-- --------------------------------------------------------

--
-- Структура таблицы `dcore_news`
--

CREATE TABLE `dcore_news` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_pub` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `keywords` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alias` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `content_en` text COLLATE utf8_unicode_ci,
  `desc` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `social_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dateS` int(11) DEFAULT NULL,
  `datePo` int(11) DEFAULT NULL,
  `date_create` int(11) NOT NULL,
  `album` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `visible` int(1) DEFAULT NULL,
  `author` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `dcore_news`
--

INSERT INTO `dcore_news` (`id`, `name`, `name_en`, `date_pub`, `type`, `keywords`, `alias`, `content`, `content_en`, `desc`, `social_url`, `dateS`, `datePo`, `date_create`, `album`, `visible`, `author`) VALUES
(1, 'Новость 1', 'New 1', 1625504400, 1, '', 'new-1', 'Контент', 'Content', '', NULL, 0, 0, 1625542945, '0', 1, 4),
(2, 'Новость 2', 'New 2', 1625504400, 1, '', 'novost-2', 'Новость 2', 'New 2', '', NULL, 0, 0, 1625557281, NULL, 1, 4),
(3, 'Новость 3', 'New 3', 1625504400, 1, '', 'novost-3', 'Новость 3', 'New 3', '', NULL, 0, 0, 1625558135, NULL, 1, 4),
(5, 'Новость 4', 'New 4', 1625590800, 1, '', 'novost-4', 'Новость 4', 'New 4', '', NULL, 0, 0, 1625638127, NULL, 1, 4);

-- --------------------------------------------------------

--
-- Структура таблицы `dcore_news_types`
--

CREATE TABLE `dcore_news_types` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alias` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `desc` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `keywords` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `content_en` text COLLATE utf8_unicode_ci,
  `date_create` int(11) NOT NULL,
  `period` int(1) DEFAULT NULL,
  `author` int(11) NOT NULL,
  `visible` int(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `dcore_news_types`
--

INSERT INTO `dcore_news_types` (`id`, `name`, `name_en`, `alias`, `desc`, `keywords`, `content`, `content_en`, `date_create`, `period`, `author`, `visible`) VALUES
(1, 'Новости', 'News', 'novosti', '', '', '', '', 1625542780, 0, 4, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `dcore_pages`
--

CREATE TABLE `dcore_pages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name_en` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alias` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `desc` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `content_en` text COLLATE utf8_unicode_ci,
  `keywords` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_create` int(11) NOT NULL,
  `date_change` int(11) DEFAULT NULL,
  `author` int(11) NOT NULL,
  `visible` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `dcore_pages`
--

INSERT INTO `dcore_pages` (`id`, `name`, `name_en`, `alias`, `desc`, `content`, `content_en`, `keywords`, `date_create`, `date_change`, `author`, `visible`) VALUES
(1, 'Контентная страница', 'Content Page', 'contentpage', NULL, NULL, 'Content Page', '', 1625541037, 1625541344, 4, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `dcore_partners`
--

CREATE TABLE `dcore_partners` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `content` text,
  `content_en` text,
  `link` varchar(255) DEFAULT NULL,
  `visible` int(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `dcore_users`
--

CREATE TABLE `dcore_users` (
  `id` int(11) NOT NULL,
  `login` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pass` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `firstname` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastname` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `surname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `birthday` int(11) DEFAULT NULL,
  `activate` int(1) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT '0',
  `member` int(11) DEFAULT '0',
  `salt` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `time_online` int(11) DEFAULT NULL,
  `time_reg` int(11) DEFAULT NULL,
  `ip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `block` int(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `dcore_user_status`
--

CREATE TABLE `dcore_user_status` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `dcore_user_status`
--

INSERT INTO `dcore_user_status` (`id`, `name`) VALUES
(2, 'Administrator'),
(4, 'Manager'),
(3, 'Moderator'),
(1, 'Super Administrator'),
(0, 'User');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `dcore_banners`
--
ALTER TABLE `dcore_banners`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `dcore_gallery`
--
ALTER TABLE `dcore_gallery`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `dcore_menu`
--
ALTER TABLE `dcore_menu`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `dcore_news`
--
ALTER TABLE `dcore_news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dcore_events_id_index` (`id`);

--
-- Индексы таблицы `dcore_news_types`
--
ALTER TABLE `dcore_news_types`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `dcore_pages`
--
ALTER TABLE `dcore_pages`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `dcore_partners`
--
ALTER TABLE `dcore_partners`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `dcore_users`
--
ALTER TABLE `dcore_users`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `dcore_user_status`
--
ALTER TABLE `dcore_user_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dcore_user_status_name_uindex` (`name`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `dcore_banners`
--
ALTER TABLE `dcore_banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `dcore_gallery`
--
ALTER TABLE `dcore_gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dcore_menu`
--
ALTER TABLE `dcore_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `dcore_news`
--
ALTER TABLE `dcore_news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `dcore_news_types`
--
ALTER TABLE `dcore_news_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `dcore_pages`
--
ALTER TABLE `dcore_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `dcore_partners`
--
ALTER TABLE `dcore_partners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `dcore_users`
--
ALTER TABLE `dcore_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
