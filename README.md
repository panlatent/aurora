Aurora
======
[![Build Status](https://travis-ci.org/panlatent/aurora.svg)](https://travis-ci.org/panlatent/aurora)
[![Latest Stable Version](https://poser.pugx.org/panlatent/aurora/v/stable.svg)](https://packagist.org/packages/panlatent/aurora)
[![Total Downloads](https://poser.pugx.org/panlatent/aurora/downloads.svg)](https://packagist.org/packages/panlatent/aurora) 
[![Latest Unstable Version](https://poser.pugx.org/panlatent/aurora/v/unstable.svg)](https://packagist.org/packages/panlatent/aurora)
[![License](https://poser.pugx.org/panlatent/aurora/license.svg)](https://packagist.org/packages/panlatent/aurora)

Aurora is a HTTP Application Server of PHP Script.

    > Notice : Aurora is now not a stable version, please do not use in the environment.

## 简介

Aurora是一个使用PHP编写的HTTP应用服务器, 它可以使PHP脚本以常驻内存的方式执行. Aurora目前使用了简单的fork模型, 每个请求对应一个子进程.
它支持守护进程, 采用管道的概念使数据流与处理程序解耦. 除了直接将Aurora作为一个HTTP服务器, 还可以使用Aurora组件构建基于HTTP的网络服务.

## 开发进度

Aurora版本号遵循 主版本号.次版本号.修订版本号 的规则. v0.x 的所有版本均为开发版且不遵循此版本号奇偶数的约定.

v0.1.0支持简单的解析HTTP协议, 但不能针对请求进行自动的处理和响应. 对静态文件的响应及对PHP脚本的处理预计将在v0.2.0实现.

## License

The Aurora is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).