SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
`id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(255) NOT NULL,
  `text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `comments` (`id`, `news_id`, `date`, `name`, `text`) VALUES
(1, 1, '2015-04-03 07:19:34', 'Name 1', 'Comment 1'),
(2, 1, '2015-04-03 07:19:46', 'Name 2', 'Comment 2'),
(3, 2, '2015-04-03 07:20:00', 'Name 3', 'Comment 3');

--
-- Table `news`
--

CREATE TABLE IF NOT EXISTS `news` (
`id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `title` varchar(255) NOT NULL,
  `text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `news` (`id`, `topic_id`, `date`, `title`, `text`) VALUES
(1, 1, '2015-04-03 07:17:46', 'News title 1', 'News text 1'),
(2, 1, '2015-04-03 07:17:59', 'News title 2', 'News text 2'),
(3, 2, '2015-04-03 07:18:13', 'News title 3', 'News text 3'),
(4, 3, '2015-04-03 07:18:27', 'News title 4', 'News text 4'),
(5, 4, '2015-04-03 07:18:47', 'News title 5', 'News text 5'),
(6, 5, '2015-04-03 07:18:58', 'News title 6', 'News text 6');

--
-- Table `topics`
--

CREATE TABLE IF NOT EXISTS `topics` (
`id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `parent_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `topics` (`id`, `title`, `parent_id`) VALUES
(1, 'Topic 1', 0),
(2, 'Topic 2', 0),
(3, 'Topic 3', 0),
(4, 'Topic 4', 1),
(5, 'Topic 5', 4);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
 ADD PRIMARY KEY (`id`), ADD KEY `comments_news_id` (`news_id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
 ADD PRIMARY KEY (`id`), ADD KEY `news_topic_id` (`topic_id`);

--
-- Indexes for table `topics`
--
ALTER TABLE `topics`
 ADD PRIMARY KEY (`id`), ADD KEY `topics_parent_id` (`parent_id`);

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `topics`
--
ALTER TABLE `topics`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- FOREIGN KEY for table `comments`
--
ALTER TABLE `comments`
ADD CONSTRAINT `comments_news_id` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`);

--
-- FOREIGN KEY for table `news`
--
ALTER TABLE `news`
ADD CONSTRAINT `news_topic_id` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`);