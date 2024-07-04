# phalcon-swoole
一款基于phalcon和swoole的高性能php框架，运行环境要求：php >=8.1，phalcon >=5.4，swoole>=5.0。

[phalcon官方文档](https://docs.phalcon.io/5.4/installation/)

[swoole官方文档](https://wiki.swoole.com/)

## 裸机运行
##### 启动
php bin/phalconswoole start
##### 停止
php bin/phalconswoole stop
##### 重启
php bin/phalconswoole restart
##### 重载
php bin/phalconswoole reload

## docker环境运行
[docker compose官方文档](https://docs.docker.com/compose/)
##### 构建镜像并启动容器
docker compose up
##### 重启容器
docker compose restart web
## 运行环境docker镜像
https://hub.docker.com/r/rockyio/phalcon-swoole


