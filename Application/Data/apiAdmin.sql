# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.14-log)
# Database: apiAdmin
# Generation Time: 2017-05-01 09:04:19 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table api_app
# ------------------------------------------------------------

DROP TABLE IF EXISTS `api_app`;

CREATE TABLE `api_app` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` varchar(50) NOT NULL DEFAULT '' COMMENT '应用id',
  `app_secret` varchar(50) NOT NULL DEFAULT '' COMMENT '应用密码',
  `app_name` varchar(50) NOT NULL DEFAULT '' COMMENT '应用名称',
  `app_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '应用状态：0表示禁用，1表示启用',
  `app_info` tinytext NOT NULL COMMENT '应用说明',
  PRIMARY KEY (`id`),
  UNIQUE KEY `app_id` (`app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='appId和appSecret表';


# Dump of table api_auth_group
# ------------------------------------------------------------

DROP TABLE IF EXISTS `api_auth_group`;

CREATE TABLE `api_auth_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '组名称',
  `description` varchar(50) NOT NULL COMMENT '组描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '组状态：为1正常，为0禁用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限组';


# Dump of table api_auth_group_access
# ------------------------------------------------------------

DROP TABLE IF EXISTS `api_auth_group_access`;

CREATE TABLE `api_auth_group_access` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL,
  `groupId` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户和组的对应关系';


# Dump of table api_auth_rule
# ------------------------------------------------------------

DROP TABLE IF EXISTS `api_auth_rule`;

CREATE TABLE `api_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `url` char(80) NOT NULL DEFAULT '' COMMENT '规则唯一标识',
  `groupId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '权限所属组的ID',
  `auth` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '权限数值',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：为1正常，为0禁用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限细节';


# Dump of table api_fields
# ------------------------------------------------------------

DROP TABLE IF EXISTS `api_fields`;

CREATE TABLE `api_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fieldName` varchar(50) NOT NULL DEFAULT '' COMMENT '字段名称',
  `hash` varchar(50) NOT NULL DEFAULT '' COMMENT '对应接口的唯一标识',
  `dataType` tinyint(2) NOT NULL DEFAULT '0' COMMENT '数据类型，来源于DataType类库',
  `default` varchar(500) NOT NULL DEFAULT '' COMMENT '默认值',
  `isMust` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否必须 0为不必须，1为必须',
  `range` varchar(500) NOT NULL DEFAULT '' COMMENT '范围，Json字符串，根据数据类型有不一样的含义',
  `info` varchar(500) NOT NULL DEFAULT '' COMMENT '字段说明',
  `type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '字段用处：0为request，1为response',
  `showName` varchar(50) NOT NULL DEFAULT '' COMMENT 'wiki显示用字段',
  PRIMARY KEY (`id`),
  KEY `hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用于保存各个API的字段规则';


# Dump of table api_list
# ------------------------------------------------------------

DROP TABLE IF EXISTS `api_list`;

CREATE TABLE `api_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `apiName` varchar(50) NOT NULL DEFAULT '' COMMENT 'api索引，保存了类和方法',
  `hash` varchar(50) NOT NULL DEFAULT '' COMMENT 'api唯一标识',
  `accessToken` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否需要认证AccessToken 1：需要，0：不需要',
  `needLogin` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否需要认证用户token  1：需要 0：不需要',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT 'API状态：0表示禁用，1表示启用',
  `method` tinyint(2) NOT NULL DEFAULT '2' COMMENT '请求方式0：不限1：Post，2：Get',
  `info` varchar(500) NOT NULL DEFAULT '' COMMENT 'api中文说明',
  `isTest` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否是测试模式：0:生产模式，1：测试模式',
  `returnStr` text COMMENT '返回数据示例',
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用于维护接口信息';


# Dump of table api_menu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `api_menu`;

CREATE TABLE `api_menu` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='目录信息';

LOCK TABLES `api_menu` WRITE;
/*!40000 ALTER TABLE `api_menu` DISABLE KEYS */;

INSERT INTO `api_menu` (`id`, `name`, `fid`, `url`, `auth`, `sort`, `hide`, `icon`, `level`)
VALUES
	(1,'欢迎页',0,'Index/welcome',0,0,0,'',0),
	(2,'系统配置',0,'',0,1,0,'',0),
	(3,'菜单维护',2,'Menu/index',0,0,0,'',0),
	(4,'用户管理',2,'User/index',0,1,0,'',0),
	(5,'权限管理',2,'Permission/index',0,2,0,'',0),
	(6,'操作日志',2,'Log/index',0,3,0,'',0),
	(7,'应用管理',0,'',0,2,0,'',0),
	(8,'应用列表',7,'App/index',0,0,0,'',0),
	(9,'接口列表',7,'ApiManage/index',0,1,0,'',0),
	(10,'字段注解（暂未开放）',7,'FieldsInfoManage/index',0,2,1,'',0),
	(11,'首页',0,'Index/index',0,0,1,'',0),
	(12,'新增菜单',3,'Menu/add',0,0,1,'',0),
	(13,'编辑菜单',3,'Menu/edit',0,0,1,'',0),
	(14,'隐藏菜单',3,'Menu/close',0,0,1,'',0),
	(15,'显示菜单',3,'Menu/open',0,0,1,'',0),
	(16,'删除菜单',3,'Menu/del',0,0,1,'',0),
	(17,'新增用户',4,'User/add',0,0,1,'',0),
	(18,'账号封停',4,'User/close',0,0,1,'',0),
	(19,'账号解封',4,'User/open',0,0,1,'',0),
	(20,'账号删除',4,'User/del',0,0,1,'',0),
	(21,'编辑应用',8,'App/edit',0,0,1,'',0),
	(22,'新增应用',8,'App/add',0,0,1,'',0),
	(23,'启用应用',8,'App/open',0,0,1,'',0),
	(24,'禁用应用',8,'App/close',0,0,1,'',0),
	(25,'删除应用',8,'App/del',0,0,1,'',0),
	(26,'新增接口',9,'ApiManage/add',0,0,1,'',0),
	(27,'启用接口',9,'ApiManage/open',0,0,1,'',0),
	(28,'禁用接口',9,'ApiManage/close',0,0,1,'',0),
	(29,'编辑接口',9,'ApiManage/edit',0,0,1,'',0),
	(30,'删除接口',9,'ApiManage/del',0,0,1,'',0),
	(31,'返回字段编辑',9,'FieldsManage/response',0,0,1,'',0),
	(32,'请求字段编辑',9,'FieldsManage/request',0,0,1,'',0),
	(33,'新增字段',9,'FieldsManage/add',0,0,1,'',0),
	(34,'字段编辑',9,'FieldsManage/edit',0,0,1,'',0),
	(35,'批量上传返回字段',9,'FieldsManage/upload',0,0,1,'',0),
	(36,'Ajax查询Log列表',6,'Log/ajaxGetIndex',0,0,1,'',0),
	(37,'日志删除',6,'Log/del',0,0,1,'',0),
	(38,'日志详情查看',6,'Log/showDetail',0,0,1,'',0),
	(39,'添加权限组',5,'Permission/add',0,0,1,'',0),
	(40,'禁用权限组',5,'Permission/close',0,0,1,'',0),
	(41,'启用权限组',5,'Permission/open',0,0,1,'',0),
	(42,'编辑权限组',5,'Permission/edit',0,0,1,'',0),
	(43,'删除权限组',5,'Permission/del',0,0,1,'',0),
	(44,'用户入组',5,'Permission/group',0,0,1,'',0),
	(45,'组用户列表',5,'Permission/member',0,0,1,'',0),
	(46,'踢出成员',5,'Permission/delMember',0,0,1,'',0),
	(47,'权限组权限配置',5,'Permission/rule',0,0,1,'',0),
	(48,'三方接口',0,'',0,4,0,'',0),
	(49,'接口仓库',48,'ApiStore/index',0,0,0,'',0),
	(50,'Ajax获取接口列表',49,'ApiStore/ajaxGetIndex',0,0,1,'',0),
	(51,'刷新接口',49,'ApiStore/refresh',0,0,1,'',0),
	(52,'编辑接口',49,'ApiStore/edit',0,0,1,'',0),
	(53,'启用接口',49,'ApiStore/open',0,0,1,'',0),
	(54,'禁用接口',49,'ApiStore/close',0,0,1,'',0),
	(55,'Ajax获取秘钥列表',61,'ApiKey/ajaxGetIndex',0,0,1,'',0),
	(56,'新增秘钥类别',61,'ApiKey/add',0,0,1,'',0),
	(57,'编辑秘钥类别',61,'ApiKey/edit',0,0,1,'',0),
	(58,'删除秘钥分类',61,'ApiKey/del',0,0,1,'',0),
	(59,'启用秘钥分类',61,'ApiKey/open',0,0,1,'',0),
	(60,'禁用秘钥分类',61,'ApiKey/close',0,0,1,'',0),
	(61,'秘钥管理',48,'ApiKey/index',0,1,0,'',0),
  (66, '文档管理', 0, '', 0, 5, 0, '', 0),
  (67, '秘钥管理', 66, 'Document/index', 0, 0, 0, '', 0),
  (68, 'Ajax获取文档记录', 67, 'Document/ajaxGetIndex', 0, 1, 1, '', 0),
  (69, '创建访问秘钥', 67, 'Document/add', 0, 2, 1, '', 0),
  (70, '延长Key时间', 67, 'Document/addTime', 0, 3, 1, '', 0),
  (71, '启用Key', 67, 'Document/open', 0, 4, 1, '', 0),
  (72, '禁用Key', 67, 'Document/close', 0, 5, 1, '', 0);

/*!40000 ALTER TABLE `api_menu` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table api_store
# ------------------------------------------------------------

DROP TABLE IF EXISTS `api_store`;

CREATE TABLE `api_store` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(50) NOT NULL DEFAULT '' COMMENT '对应的代码路径',
  `auth` int(11) NOT NULL DEFAULT '0' COMMENT '使用的接口秘钥',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '接口状态',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '接口名称（提供辨识使用）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='三方接口仓库';


# Dump of table api_store_auth
# ------------------------------------------------------------

DROP TABLE IF EXISTS `api_store_auth`;

CREATE TABLE `api_store_auth` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '秘钥名称',
  `appId` varchar(50) NOT NULL DEFAULT '' COMMENT '应用ID',
  `appSecret` varchar(255) NOT NULL DEFAULT '' COMMENT '应用秘钥',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '秘钥状态',
  `accessToken` varchar(255) DEFAULT '' COMMENT '授权秘钥（千米）',
  `refreshToken` varchar(255) DEFAULT '' COMMENT '刷新秘钥（千米）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='三方接口秘钥管理';



# Dump of table api_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `api_user`;

CREATE TABLE `api_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '' COMMENT '用户名',
  `nickname` varchar(64) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '用户密码',
  `regTime` int(10) NOT NULL DEFAULT '0' COMMENT '注册时间',
  `regIp` varchar(11) NOT NULL DEFAULT '' COMMENT '注册IP',
  `updateTime` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '账号状态 0封号 1正常',
  `openId` varchar(100) DEFAULT NULL COMMENT '微信唯一ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员认证信息';
INSERT INTO `api_user` (`username`, `nickname`, `password`, `regTime`, `regIp`, `updateTime`, `status`)
VALUES
	('root', 'root', '912601e4ad1b308c9ae41877cf6ca754', 1492004246, '3682992231', 1492236545, 1);


# Dump of table api_user_action
# ------------------------------------------------------------

DROP TABLE IF EXISTS `api_user_action`;

CREATE TABLE `api_user_action` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `actionName` varchar(50) NOT NULL DEFAULT '' COMMENT '行为名称',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '操作用户ID',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `addTime` int(11) NOT NULL DEFAULT '0' COMMENT '操作时间',
  `data` text COMMENT '用户提交的数据',
  `url` varchar(200) NOT NULL DEFAULT '' COMMENT '操作URL',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户操作日志';


# Dump of table api_user_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `api_user_data`;

CREATE TABLE `api_user_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `loginTimes` int(11) NOT NULL COMMENT '账号登录次数',
  `lastLoginIp` varchar(11) NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `lastLoginTime` int(11) NOT NULL COMMENT '最后登录时间',
  `uid` varchar(11) NOT NULL DEFAULT '' COMMENT '用户ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员数据表';


# Dump of table api_document
# ------------------------------------------------------------

DROP TABLE IF EXISTS `api_document`;

CREATE TABLE `api_document` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`key` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '授权秘钥',
	`endTime` INT(11) NOT NULL DEFAULT '0' COMMENT '失效时间戳',
	`times` INT(11) NOT NULL DEFAULT '0' COMMENT '访问次数',
	`lastTime` INT(11) NOT NULL DEFAULT '0' COMMENT '最后访问时间',
	`lastIp` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '最后访问IP',
	`createTime` INT(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `info` varchar(50) NOT NULL COMMENT '备注',
  `status` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '1生效，0失效',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文档访问秘钥';


/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
