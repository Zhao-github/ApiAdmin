# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.14-log)
# Database: admin
# Generation Time: 2016-11-20 12:17:44 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table auth_group
# ------------------------------------------------------------

DROP TABLE IF EXISTS `auth_group`;

CREATE TABLE `auth_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '组名称',
  `description` varchar(50) NOT NULL COMMENT '组描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '组状态：为1正常，为0禁用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='权限组';


# Dump of table auth_group_access
# ------------------------------------------------------------

DROP TABLE IF EXISTS `auth_group_access`;

CREATE TABLE `auth_group_access` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL,
  `groupId` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户和组的对应关系';

# Dump of table auth_rule
# ------------------------------------------------------------

DROP TABLE IF EXISTS `auth_rule`;

CREATE TABLE `auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `url` char(80) NOT NULL DEFAULT '' COMMENT '规则唯一标识',
  `groupId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '权限所属组的ID',
  `auth` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '权限数值',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：为1正常，为0禁用',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限细节';

# Dump of table menu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `menu`;

CREATE TABLE `menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '菜单名',
  `fid` int(11) NOT NULL COMMENT '父级菜单ID',
  `url` varchar(50) NOT NULL DEFAULT '' COMMENT '链接',
  `auth` tinyint(2) NOT NULL DEFAULT '0' COMMENT '访客权限',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `hide` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否显示',
  `icon` varchar(50) NOT NULL DEFAULT '' COMMENT '菜单图标',
  `level` tinyint(2) NOT NULL DEFAULT '0' COMMENT '菜单认证等级',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='目录信息';

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;

INSERT INTO `menu` (`id`, `name`, `fid`, `url`, `auth`, `sort`, `hide`, `icon`, `level`)
VALUES
	(1,'系统维护',0,'',0,1,0,'fa-institution',1),
	(2,'菜单管理',1,'Menu/index',0,1,0,'fa-navicon',0),
	(3,'用户管理',1,'User/index',0,2,0,'fa-users',0),
	(4,'权限管理',1,'Auth/index',0,3,0,'fa-bolt',0),
	(5,'操作日志',1,'UserLog/index',0,4,0,'fa-suitcase',0),
	(6,'首页',0,'Index/index',0,0,1,'',0),
	(7,'新增菜单',2,'Menu/add',0,0,0,'',0),
	(8,'编辑菜单',2,'Menu/edit',0,0,0,'',0),
	(9,'删除菜单',2,'Menu/del',0,0,0,'',0),
	(10,'新增用户',3,'User/add',0,0,0,'',0),
	(11,'删除用户',3,'User/del',0,0,0,'',0),
	(12,'启用用户',3,'User/open',0,0,0,'',0),
	(13,'禁用用户',3,'User/close',0,0,0,'',0),
	(14,'修改用户',3,'User/edit',0,0,0,'',0),
	(15,'新增用户组',4,'Auth/add',0,0,0,'',0),
	(16,'删除用户组',4,'Auth/del',0,0,0,'',0),
	(17,'编辑用户组',4,'Auth/edit',0,0,0,'',0),
	(18,'启用用户组',4,'Auth/open',0,0,0,'',0),
	(19,'禁用用户组',4,'Auth/close',0,0,0,'',0),
	(20,'获取组权限',4,'Auth/access',0,0,0,'',0),
	(21,'组用户管理',4,'Auth/userAuth',0,0,0,'',0),
	(22,'用户赋权',4,'Auth/group',0,0,0,'',0),
	(23,'应用管理',0,'',0,6,0,'fa-cubes',0),
	(24,'应用组管理',23,'AppManager/index',0,0,0,'fa-rss',0),
	(25,'基础配置',0,'',0,5,0,'fa-cogs',0),
	(26,'管理员配置',25,'Member/index',0,0,0,'fa-user',0),
	(27,'秘钥配置',25,'KeyManager/index',0,0,0,'fa-key',0),
	(28,'规则组配置',25,'FilterManager/index',0,0,0,'fa-filter',0),
	(29,'监控组配置',25,'WatchManager/index',0,0,0,'fa-eye',0),
	(30,'报警组配置',25,'WarnManager/index',0,0,0,'fa-warning',0),
	(31,'API接口管理',23,'ApiManager/index',0,0,0,'fa-usb',0),
	(32,'API接口调试',23,'ApiDebug/index',0,0,0,'fa-bug',0),
	(33,'接管第三方',0,'',0,7,0,'fa-cloud',0),
	(34,'认证方式',33,'TakeOver/auth',0,0,0,'fa-fire',0),
	(35,'公共参数',33,'TakeOver/param',0,0,0,'fa-file',0),
	(36,'接口映射',33,'TakeOver/copy',0,0,0,'fa-copy',0),
	(37, '新增管理员', 26, 'AppMember/add', 0, 0, 0, '', 0),
	(38, '启用管理员', 26, 'AppMember/open', 0, 0, 0, '', 0),
	(39, '禁用管理员', 26, 'AppMember/close', 0, 0, 0, '', 0),
	(40, '删除管理员', 26, 'AppMember/del', 0, 0, 0, '', 0),
	(41, '编辑管理员', 26, 'AppMember/edit', 0, 0, 0, '', 0),
	(42, '新增秘钥', 27, 'KeyManager/add', 0, 0, 0, '', 0),
	(43, '启用秘钥', 27, 'KeyManager/open', 0, 0, 0, '', 0),
	(44, '禁用秘钥', 27, 'KeyManager/close', 0, 0, 0, '', 0),
	(45, '删除秘钥', 27, 'KeyManager/del', 0, 0, 0, '', 0),
	(46, '新增规则组', 28, 'FilterManager/add', 0, 0, 0, '', 0),
	(47, '启用规则组', 28, 'FilterManager/open', 0, 0, 0, '', 0),
	(48, '禁用规则组', 28, 'FilterManager/close', 0, 0, 0, '', 0),
	(49, '删除规则组', 28, 'FilterManager/del', 0, 0, 0, '', 0),
	(50, '编辑规则组', 28, 'FilterManager/edit', 0, 0, 0, '', 0),
	(51,'编辑秘钥',27,'KeyManager/edit',0,0,0,'',0);

/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '' COMMENT '用户名',
  `nickname` varchar(64) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '用户密码',
  `regTime` int(10) NOT NULL DEFAULT '0' COMMENT '注册时间',
  `regIp` varchar(11) NOT NULL DEFAULT '' COMMENT '注册IP',
  `updateTime` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '账号状态 0封号 1正常',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员认证信息';

# Dump of table user_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_data`;

CREATE TABLE `user_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `loginTimes` int(11) NOT NULL COMMENT '账号登录次数',
  `lastLoginIp` varchar(11) NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `lastLoginTime` int(11) NOT NULL COMMENT '最后登录时间',
  `uid` varchar(11) NOT NULL DEFAULT '' COMMENT '用户ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员数据表';

# Dump of table keys
# ------------------------------------------------------------

DROP TABLE IF EXISTS `keys`;

CREATE TABLE `keys` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(50) NOT NULL DEFAULT '' COMMENT '当前Key的备注',
  `accessKey` varchar(64) NOT NULL DEFAULT '' COMMENT '公钥',
  `secretKey` varchar(64) NOT NULL DEFAULT '' COMMENT '私钥',
  `appId` int(11) NOT NULL DEFAULT '0' COMMENT '适配App的ID',
  `filterId` int(11) NOT NULL DEFAULT '0' COMMENT '适配过滤组的ID',
  `addTime` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间戳',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '秘钥状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='API认证秘钥对表';

# Dump of table app_member
# ------------------------------------------------------------

DROP TABLE IF EXISTS `app_member`;

CREATE TABLE `app_member` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '管理员名称',
  `phone` varchar(11) NOT NULL DEFAULT '' COMMENT '管理员手机号',
  `email` varchar(50) NOT NULL DEFAULT '' COMMENT '管理员邮箱',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '管理员状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='应用管理员表';

# Dump of table filter
# ------------------------------------------------------------

DROP TABLE IF EXISTS `filter`;

CREATE TABLE `filter` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `total` int(11) NOT NULL DEFAULT '0' COMMENT '请求总数',
  `month` int(11) NOT NULL DEFAULT '0' COMMENT '每月请求频率',
  `day` int(11) NOT NULL DEFAULT '0' COMMENT '每天请求频率',
  `hour` int(11) NOT NULL DEFAULT '0' COMMENT '每小时请求频率',
  `minute` int(11) NOT NULL DEFAULT '0' COMMENT '每分钟请求频率',
  `second` int(11) NOT NULL DEFAULT '0' COMMENT '没秒钟请求频率',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '过滤组名称',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '过滤组状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Api过滤组配置';

/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
