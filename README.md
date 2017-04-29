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

**2.0安装指引**

[http://git.oschina.net/xiaoxunzhao/ApiAdmin/wikis/%E9%A1%B9%E7%9B%AE%E5%AE%89%E8%A3%852.0](http://git.oschina.net/xiaoxunzhao/ApiAdmin/wikis/%E9%A1%B9%E7%9B%AE%E5%AE%89%E8%A3%852.0)

**源码地址**

国内OSC：[http://git.oschina.net/xiaoxunzhao/ApiAdmin](http://git.oschina.net/xiaoxunzhao/ApiAdmin)

国际GitHub(暂未开通)：[https://github.com/Zhao-github/ApiAdmin](https://github.com/Zhao-github/ApiAdmin)

**下载地址**

国内OSC：[http://git.oschina.net/xiaoxunzhao/ApiAdmin/releases](http://git.oschina.net/xiaoxunzhao/ApiAdmin)

国际GitHub(暂未开通)：[https://github.com/Zhao-github/ApiAdmin/releases](https://github.com/Zhao-github/ApiAdmin/releases)

**项目构成**

- ThinkPHP v3.2.3
- LayUI
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

![输入图片说明](https://git.oschina.net/uploads/images/2017/0415/221526_c2090391_110856.png "在这里输入图片标题")
![输入图片说明](https://git.oschina.net/uploads/images/2017/0415/221536_be4efd42_110856.png "在这里输入图片标题")
![输入图片说明](https://git.oschina.net/uploads/images/2017/0415/221550_5d92dbdf_110856.png "在这里输入图片标题")
![输入图片说明](https://git.oschina.net/uploads/images/2017/0415/221559_44530e0b_110856.png "在这里输入图片标题")
![输入图片说明](https://git.oschina.net/uploads/images/2017/0415/221609_fd20b776_110856.png "在这里输入图片标题")
![输入图片说明](https://git.oschina.net/uploads/images/2017/0415/221618_bcfd94b5_110856.png "在这里输入图片标题")

**项目特性**

- 开放源码
- 保持生机
- 不断更新
- 响应市场

**开源，我们在路上！**

[1]: http://my.oschina.net/dogstar/blog