<p align="center"><img src="https://raw.githubusercontent.com/panlatent/aurora/master/htdocs/images/logo.png" alt="aurora" /></p>

[![Build Status](https://travis-ci.org/panlatent/aurora.svg)](https://travis-ci.org/panlatent/aurora)
[![Latest Stable Version](https://poser.pugx.org/panlatent/aurora/v/stable.svg)](https://packagist.org/packages/panlatent/aurora)
[![Total Downloads](https://poser.pugx.org/panlatent/aurora/downloads.svg)](https://packagist.org/packages/panlatent/aurora) 
[![Latest Unstable Version](https://poser.pugx.org/panlatent/aurora/v/unstable.svg)](https://packagist.org/packages/panlatent/aurora)
[![License](https://poser.pugx.org/panlatent/aurora/license.svg)](https://packagist.org/packages/panlatent/aurora)

Aurora is a HTTP Application Server of PHP Script.

    > Notice : Aurora is now not a stable version, please do not use in the environment.

## 简介

Aurora是一个使用PHP编写的HTTP应用服务器, 它可以使PHP脚本以常驻内存的方式执行. Aurora目前使用了简单的fork模型, 每个请求对应一个子进程.
它支持守护进程, 采用管道的概念使数据流与处理程序解耦. 除了直接将Aurora作为一个HTTP服务器, 还可以使用Aurora组件构建基于基于HTTP协议的网络服务.
Aurora是一个实验性质的项目, 目前并未对性能做到较多的考虑. Aurora与WorkerMan项目和Swoole项目不同的是, 它努力成为一个全面且坚实的HTTP服务器,
并且能够为其他以HTTP协议为基础的项目提供复用方式, 而不是重复制造一个高性能的网络库.

## 环境和依赖

+ PHP7 and Event, Posix, Pcntl Extensions
+ Composer

## 使用说明

aurora [start|stop|restart|status]
```shell
php ./bin/aurora start
```

## 开发进度和版本

Aurora版本号遵循 主版本号.次版本号.修订版本号 的规则. v0.x 的所有版本均为开发版且不遵循版本号奇偶数和修订版本号的约定. 点击查看
[完整的更新日志](./CHANGELOG.md)

### 预计在下个版本实现的特性

添加HTTP Handle Framework, Aurora的请求处理逻辑会使用这个框架编写. 更全面的解析HTTP请求, 处理静态文件资源, HTTP响应和错误实现, 日志功能


## 感谢

[Aurora Logo](https://raw.githubusercontent.com/panlatent/aurora/master/htdocs/images/logo.png) by [@Clagrae](https://github.com/Clagrae)

## License

The Aurora is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).