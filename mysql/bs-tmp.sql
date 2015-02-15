-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2015 年 02 月 15 日 01:20
-- 服务器版本: 5.6.12-log
-- PHP 版本: 5.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `bs-tmp`
--
CREATE DATABASE IF NOT EXISTS `bs-tmp` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `bs-tmp`;

-- --------------------------------------------------------

--
-- 表的结构 `tmp_user`
--

CREATE TABLE IF NOT EXISTS `tmp_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `loginname` varchar(30) NOT NULL,
  `salt` varchar(10) CHARACTER SET utf8 NOT NULL,
  `password` varchar(128) NOT NULL,
  `site_id` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `tmp_user`
--

INSERT INTO `tmp_user` (`id`, `username`, `loginname`, `salt`, `password`, `site_id`, `created_at`, `updated_at`) VALUES
(1, 'qax', 'qax', '1Xtxlz', '911b008995e0ed77f137d1e58df96689', 1, 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `tmp_wx_link_trunk`
--

CREATE TABLE IF NOT EXISTS `tmp_wx_link_trunk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link_trunk_items_count` int(2) NOT NULL,
  `created_at` int(11) NOT NULL,
  `remark` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='链接蔟' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `tmp_wx_link_trunk_item`
--

CREATE TABLE IF NOT EXISTS `tmp_wx_link_trunk_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link_trunk_id` int(11) NOT NULL COMMENT '链接蔟id',
  `match_type` int(2) NOT NULL COMMENT '匹配类型',
  `remark` varchar(128) NOT NULL COMMENT '备注',
  `position` int(11) NOT NULL COMMENT '排序位置',
  `is_top` int(1) NOT NULL COMMENT '是否置顶',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `news_id` int(11) NOT NULL COMMENT '新闻id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `tmp_wx_menu`
--

CREATE TABLE IF NOT EXISTS `tmp_wx_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL COMMENT '菜单名字',
  `type` int(2) NOT NULL COMMENT '1click2view',
  `parent` int(11) NOT NULL COMMENT '父级id',
  `depth` int(11) NOT NULL COMMENT '深度',
  `remark` varchar(128) NOT NULL COMMENT '备注',
  `created_at` int(11) NOT NULL COMMENT '添加时间',
  `updated_at` int(11) NOT NULL COMMENT '更新时间',
  `position` int(11) NOT NULL COMMENT '排序位置',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='微信菜单表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `tmp_wx_message`
--

CREATE TABLE IF NOT EXISTS `tmp_wx_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ret_type` varchar(128) NOT NULL COMMENT '回复类型',
  `accept_type` varchar(128) NOT NULL COMMENT '接收类型',
  `accept_content` varchar(1024) NOT NULL COMMENT '接受内容',
  `ret_id` int(11) NOT NULL COMMENT '回复规则',
  `from_user_name` varchar(128) NOT NULL COMMENT '接受方',
  `to_user_name` varchar(128) NOT NULL COMMENT '发送方',
  `created_at` int(11) NOT NULL COMMENT '添加时间',
  `status` int(1) NOT NULL COMMENT '回复状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='微信公众号用户交互信息' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `tmp_wx_respond`
--

CREATE TABLE IF NOT EXISTS `tmp_wx_respond` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `even_key` varchar(128) CHARACTER SET utf8 NOT NULL COMMENT '事件类型',
  `ret_type` varchar(128) CHARACTER SET utf8 NOT NULL COMMENT '回复类型1TEXT/2ARTICLE/3MEDIA',
  `replies_count` int(2) NOT NULL COMMENT '回复条数',
  `send_all_replies` int(1) NOT NULL COMMENT '是否已发送所有匹配内容',
  `sub_id` int(11) NOT NULL COMMENT '对应文本、图文或媒体表的id',
  `remark` varchar(128) CHARACTER SET utf8 NOT NULL COMMENT '备注',
  `content` varchar(128) CHARACTER SET utf8 NOT NULL COMMENT '添加时间',
  `created_at` int(11) NOT NULL COMMENT '更新时间',
  `updated_at` int(11) NOT NULL COMMENT '当回复类型为TEXT时有效',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='回复规则' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `tmp_wx_respond`
--

INSERT INTO `tmp_wx_respond` (`id`, `even_key`, `ret_type`, `replies_count`, `send_all_replies`, `sub_id`, `remark`, `content`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 'subscribe', '', 0, 0, 0, '????', 'abc', 1423463269, 0, 1);

-- --------------------------------------------------------

--
-- 表的结构 `tmp_wx_tag`
--

CREATE TABLE IF NOT EXISTS `tmp_wx_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(128) CHARACTER SET utf8 NOT NULL,
  `user_id` int(11) NOT NULL,
  `respond_id` int(11) NOT NULL,
  `created_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='微信标签表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `tmp_wx_users`
--

CREATE TABLE IF NOT EXISTS `tmp_wx_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `url` varchar(128) CHARACTER SET utf8 NOT NULL COMMENT '绑定url',
  `token` varchar(128) CHARACTER SET utf8 NOT NULL COMMENT '绑定token',
  `app_id` varchar(128) CHARACTER SET utf8 DEFAULT NULL COMMENT '微信app_id',
  `app_secret` varchar(128) CHARACTER SET utf8 DEFAULT NULL COMMENT '微信app_scret',
  `wechat_name` varchar(128) CHARACTER SET utf8 DEFAULT NULL COMMENT '微信公众号id',
  `wechat_id` varchar(128) CHARACTER SET utf8 DEFAULT NULL COMMENT '微信原始id',
  `qr_code` varchar(128) CHARACTER SET utf8 DEFAULT NULL COMMENT '二维码图片',
  `created_at` int(11) DEFAULT NULL COMMENT '添加时间',
  `updated_at` int(11) DEFAULT NULL COMMENT '更新时间',
  `type` varchar(128) CHARACTER SET utf8 NOT NULL COMMENT '公众号类型',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `tmp_wx_users`
--

INSERT INTO `tmp_wx_users` (`id`, `user_id`, `url`, `token`, `app_id`, `app_secret`, `wechat_name`, `wechat_id`, `qr_code`, `created_at`, `updated_at`, `type`) VALUES
(1, 1, 'http://localhost/Traffic-Monitoring-Management-Platform-PHP/index.php/WechatBinding?id=1', '0cUpjkT5', '123456', '123456', '123', '1234', NULL, 1423462724, NULL, 'subscribe');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
