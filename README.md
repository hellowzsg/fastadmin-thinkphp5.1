# **FastAdmin 完全升级 thinkphp5.1**  
fastadmin： 1.0.0.20190418_beta  
thinkphp: 5.1.38 LTS

## **安装使用**
### **环境要求**
```
PHP >= PHP7.1
Mysql >= 5.5.0 (需支持innodb引擎)
Apache 或 Nginx
PDO PHP Extension
MBstring PHP Extension
CURL PHP Extension
Node.js (可选,用于安装Bower和LESS,同时打包压缩也需要使用到)
Composer (可选,用于管理第三方扩展包)
Bower (可选,用于管理前端资源)
Less (可选,用于编辑less文件,如果你需要增改css样式,最好安装上)
```
### **安装**
1. 克隆项目  
`git clone https://github.com/hellowzsg/fastadmin-thinkphp5.1.git`
2. 进入项目  
`cd fastadmin-thinkphp5.1`
2. 下载前端插件依赖包  
`bower install`
3. 下载PHP依赖包  
`composer install`
4. 添加虚拟主机并绑定到fastadmin/public目录
5. 访问主机地址即可在线安装(或者使用命令`php think install ..`)
6. 点个star呗~~
## **升级日志**  
[日志](https://github.com/hellowzsg/fastadmin-thinkphp5.1/blob/master/UPGRADE.md)
## **注意事项**
不可使用fastadmin官方插件,所有插件基本都需要适配

## **项目地址**
 fastadmin-tp5.1  https://github.com/hellowzsg/fastadmin-thinkphp5.1.git  
 fastadmin-addons-tp5.1   https://github.com/hellowzsg/fastadmin-addons-thinkphp51.git  
 
## **参考项目**
fastadmin  https://gitee.com/karson/fastadmin.git  
cygmris/think2h4ck  https://github.com/cygmris/think2h4ck.git
