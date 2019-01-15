> 站在巨人的肩膀上，并不是高的表现，反而使自己变得渺小~只有吸收了巨人的营养，茁壮自己才是真正的高大！ --笔者


# ApiAdmin
[![ApiAdmin](https://img.shields.io/hexpm/l/plug.svg)](http://www.apiadmin.org/)
[![ApiAdmin](https://img.shields.io/badge/release-v3.0.8-blue.svg)](http://www.apiadmin.org/)
[![ApiAdmin](https://img.shields.io/badge/build-passing-brightgreen.svg)](http://www.apiadmin.org/)
[![ApiAdmin](https://img.shields.io/badge/ApiAdmin-v3.0.8-brightgreen.svg)](http://www.apiadmin.org/)

## 前端页面
ApiAdmin3.0是一个前后端完全分离的项目，前端采用Vue构建，如需要可视化配置的请移步：[ApiAdmin-WEB](https://gitee.com/apiadmin/ApiAdmin-WEB)

## 灵 感

首先自我介绍下吧，我是一个PHP程序员，目前就职于某上市集团。我第一份工作是做微信开发的，这也是我入行以来第一次做的商业上线项目，虽然我只是充当了其中一个不是太重要的角色，但是感谢它让我第一次接触了API，也让我第一次对于API产生了浓厚的兴趣。之后的一段时间内甚至疯狂的收集过各种免费的API接口！然而一直只是在用API，却没有为API贡献过些什么。

开源框架用了很多，开源代码看了很多，github、git@osc、Stack Overflow这些优秀的平台帮助了我很多，所以，我觉得是时候为开源做点什么。更是给开源项目PhalApi贡献过代码，也正是这一个契机使得我正式迈向开源社区。随着时间的推移，PhalApi的战绩赫赫，它的壮大更加坚定了Api的地位，既然未来的互联网世界中API占了很重要的地位，既然越来越多的人开始开发API，那么无状态的API如何去管理呢？因此**ApiAdmin**来了~

## 愿 景

> 希望有人用它，希望更多的人用它。
> 希望它能帮助到你，希望它能帮助到更多的你。

## 项目简介

**系统需求**

- PHP >= 5.6
- MySQL >= 5.5.3
- Redis

**项目构成**

- ThinkPHP v5.0.19
- Vue 2.0
- semanticUI
- ...

**功能简介**

 1. 接口文档自动生成
 2. 接口输入参数自动检查
 3. 接口输出参数数据类型自动规整
 4. 灵活的参数规则设定
 5. 支持三方Api无缝融合
 6. 本地二次开发友好
 7. ...
 
 ```
 ApiAdmin（PHP部分）
 ├─ 系统维护
 |  ├─ 菜单管理 - 编辑访客权限，处理菜单父子关系，被权限系统依赖（极为重要）
 |  ├─ 用户管理 - 添加新用户，封号，删号以及给账号分配权限组
 |  ├─ 权限管理 - 权限组管理，给权限组添加权限，将用户提出权限组
 |  └─ 操作日志 - 记录管理员的操作，用于追责，回溯和备案
 |  ...
 ```

**页面截图**

![输入图片说明](https://gitee.com/uploads/images/2018/0224/095358_19cb42d0_110856.png "api.png")

![输入图片说明](https://gitee.com/uploads/images/2018/0224/095410_55dc23e1_110856.png "app.png")

![输入图片说明](https://gitee.com/uploads/images/2018/0224/095420_bddff990_110856.png "auth1.png")

![输入图片说明](https://gitee.com/uploads/images/2018/0224/095427_fa86e42d_110856.png "auth2.png")

![输入图片说明](https://gitee.com/uploads/images/2018/0224/095436_3600de17_110856.png "lock.png")

![输入图片说明](https://gitee.com/uploads/images/2018/0224/095444_d2a88da0_110856.png "user.png")

**项目特性**

- 开放源码
- 保持生机
- 不断更新
- 响应市场

**开源，我们在路上！**

## 鸣谢

ApiAdmin走到今天，也正式迈入3.0时代了，同时，ApiAdmin也迎来了它的一岁生日，我们怀着激动的心情迎来这次发布。在新版本发布之际，我们真诚的感谢从1.0到2.0陪我们一路走来的朋友们。感谢你们的支持和信任！当然也感谢#开源中国#给大陆本土开源提供这样一个优秀的平台。

## 附：升级指南

很抱歉的告诉大家，虽然我们尽可能的和往期版本进行了兼容，但是，由于整体架构变化很大，所以想要零成本升级有点困难。我们建议大家可以使用3.0做新接口，慢慢的将2.0版本的接口移植到3.0。
