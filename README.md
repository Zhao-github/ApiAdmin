> 站在巨人的肩膀上，并不是高的表现，反而使自己变得渺小~只有吸收了巨人的营养，茁壮自己才是真正的高大！ --笔者

## 灵 感

首先自我介绍下吧，我是一个PHP程序员，一个刚刚干了两年左右的小菜鸟。我第一份工作是做微信开发的，这也是我入行以来第一次做的商业上线项目，虽然我只是充当了其中一个不是太重要的角色，但是感谢它让我第一次接触了API，也让我第一次对于API产生了浓厚的兴趣。之后的一段时间内甚至疯狂的收集过各种免费的API接口！然而一直只是在用API，却没有为API贡献过些什么。

开源框架用了很多，开源代码看了很多，github、git@osc、Stack Overflow这些优秀的平台帮助了我很多，所以，我觉得是时候为开源做点什么。2015年初，我遇到了PhalApi，一个非常有生命力的API项目，是PHP语言写的，当时的它还是一个宝宝，在项目组的细心呵护下茁壮成长，很荣幸的是，我也是项目组成员之一，然而在它的成长中，我输送的营养简直不值一提~也感谢创始人 [@dogstar][1] 对我的信任，一直没有把我踢出项目组。既然API这么火，既然未来的互联网世界中API占了很重要的地位，既然越来越多的人开始开发API，那么无状态的API如何去管理呢？因此**ApiAdmin**来了~

## 愿 景

> 希望有人用它，希望更多的人用它。
> 希望它能帮助到你，希望它能帮助到更多的你。

## 项目简介

**体验地址**

[http://admin.our-dream.cn/](http://admin.our-dream.cn/)

**源码地址**

国内OSC：[http://git.oschina.net/xiaoxunzhao/ApiAdmin](http://git.oschina.net/xiaoxunzhao/ApiAdmin)

国际GitHub：[https://github.com/Zhao-github/ApiAdmin](https://github.com/Zhao-github/ApiAdmin)

**下载地址**

国内OSC：[http://git.oschina.net/xiaoxunzhao/ApiAdmin/releases](http://git.oschina.net/xiaoxunzhao/ApiAdmin)

国际GitHub：[https://github.com/Zhao-github/ApiAdmin/releases](https://github.com/Zhao-github/ApiAdmin/releases)

**项目构成**

- ThinkPHP v5.0.3
- AdminLTE v2.3.7
- fastClick
- jQuery v3.1.1
- bootstrap v3.3.6
- bootBox v4.4.0
- slimscroll v1.3.8
- ...

**功能简介**

 1. 页面实现纯JS加载。（已完成）
 2. 拥有易懂的项目使用手册。（完善中...）
 3. 拥有完善的技术服务及其技术支持渠道。（待规划）
 4. 拥有丰富的应用场景解决方案。（待规划）
 
 ```
 ApiAdmin（PHP部分）
 ├─ 系统维护
 |  ├─ 菜单管理 - 编辑访客权限，处理菜单父子关系，被权限系统依赖（极为重要）
 |  ├─ 用户管理 - 添加新用户，封号，删号以及给账号分配权限组
 |  ├─ 权限管理 - 权限组管理，给权限组添加权限，将用户提出权限组
 |  └─ 操作日志 - 记录管理员的操作，用于追责，回溯和备案
 ├─ 基础配置
 |  ├─ 管理员配置 - 配置APP管理员的手机号，邮箱用于明确APP的责任人和接收APP所属API的报警信息
 |  ├─ 秘钥配置 - 用于生成与ApiAdmin相对接的秘钥对
 |  ├─ 规则组配置 - 用于定义API接口过滤规则（例：请求上限，请求频率等）
 |  ├─ 监控组配置 - 用于实时监测API健康状态（例：HTTP状态码，服务器ping等）
 |  └─ 报警组配置 - 定义报警阀值，以及指定接受报警信息的管理员
 ├─ 应用管理
 |  ├─ 应用组管理 - 所有API都至少属于一个APP组
 |  ├─ API接口管理 - 全部在用接口列表，包含了接口的开发、测试、上线的状态变更，包含了接口统计，文档生成
 |  └─ API接口调试 - 调试已配置的API接口
 ├─ 接管第三方
 |  ├─ 认证方式 - 目前拟定系统预置的方式有微信、微博、阿里云、聚合数据、百度APIStore以及一套通用的APP认证
 |  ├─ 接口映射 - ApiAdmin既然接管了第三方，那么必须要提供一套自己的个性接口
 |  └─ 公共参数 - 接入第三方的时候允许配置一些公共参数。
 |  ...
 ```

**页面截图**

![输入图片说明](http://git.oschina.net/uploads/images/2016/1115/153057_5fb85494_110856.png "在这里输入图片标题")
![输入图片说明](http://git.oschina.net/uploads/images/2016/1115/153108_43ba4095_110856.png "在这里输入图片标题")
![输入图片说明](http://git.oschina.net/uploads/images/2016/1115/153745_ef999653_110856.png "在这里输入图片标题")

**项目特性**

- 开放源码
- 保持生机
- 不断更新
- 响应市场

**开源，我们在路上！**

[1]: http://my.oschina.net/dogstar/blog