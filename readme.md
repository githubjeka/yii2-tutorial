#Интерактивное руководство создания сайта на Yii2 на русском языке

##Запуск
Для запуска понадобится Git и PHP.

###Установка Git
Вы можете скачать и установить git из [http://git-scm.com/download](http://git-scm.com/download). 
После установки вы должны иметь доступ к git командной строки. Основные команды, которые вам понадобятся:

git clone ... : клонирует удалённый репозиторий на локальную компьютер
git checkout ... : проверяет определённый тег или версию кода и переключается на него
 
###Установка PHP
Для запуска понадобится PHP версии не ниже 5.4 версии.
Скачать можно отсюда: [http://php.net/downloads.php](http://php.net/downloads.php)

###Когда всё готово.
После установок, запустите в консоли:
```
php -v
```
должно вывестись вроде этого:
```
PHP 5.6.4 (cli) (built: Dec 17 2014 13:20:35)
Copyright (c) 1997-2014 The PHP Group
Zend Engine v2.6.0, Copyright (c) 1998-2014 Zend Technologies
    with Xdebug v2.2.6, Copyright (c) 2002-2014, by Derick Rethans
```

и для `git --version` нечто такое - `git version 1.8.5.2.msysgit.0`

Если у вас возникли сложности с установкой инструментов, то обратитесь на 
[форум за помощью](http://yiiframework.ru/forum/viewforum.php?f=17&sid=7d16a10cc45601f77dfd89c094b0b4f9))

Если всё удачно, то выполните три команды:
```
git clone "https://github.com/githubjeka/yii2-tutorial"
cd yii2-tutorial
php -S localhost:9000
```
и перейдите по адресу [http://localhost:9000/scripts/](http://localhost:9000/scripts/)