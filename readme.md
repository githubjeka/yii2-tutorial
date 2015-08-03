# Yii2 Интерактивное руководство создания сайта на русском языке

## Содержание

| Наименование | Состояние  |
|---|---|
| 1. Начальная установка  |   |
| 2. Знакомство с Advanced   |  [![Build Status](https://travis-ci.org/githubjeka/yii2-tutorial.svg?branch=step-0)](https://travis-ci.org/githubjeka/yii2-tutorial)  |
| 3. Виды и шаблоны | [![Build Status](https://travis-ci.org/githubjeka/yii2-tutorial.svg?branch=step-0.1)](https://travis-ci.org/githubjeka/yii2-tutorial)
| 4. Формы, Active Record, Gii, Валидация. | [![Build Status](https://travis-ci.org/githubjeka/yii2-tutorial.svg?branch=step-0.2)](https://travis-ci.org/githubjeka/yii2-tutorial)
| 5. Сессия. События и поведения. Сохранение данных.| 
| 6. Backend. GridView. Авторизация.| 
| 7. Знакомство с тестированием.| 
| 8. Доступ к реляционным данным.| 
| 9. Отображение реляционных данных.| [![Build Status](https://travis-ci.org/githubjeka/yii2-tutorial.svg?branch=step-1.2)](https://travis-ci.org/githubjeka/yii2-tutorial)
| 10. Сохранение реляционных данных.| 

##Запуск
Для запуска понадобится Git и PHP.

###Установка Git
Вы можете скачать и установить git из [http://git-scm.com/download](http://git-scm.com/download). 
После установки вы должны иметь доступ к git командной строки. Основные команды, которые вам понадобятся:

- `git clone` клонирует удалённый репозиторий на локальный компьютер
- `git checkout` проверяет определённый тег или версию кода и переключается на него
 
###Установка PHP
Для запуска понадобится PHP версии не ниже 5.4 версии.
Скачать можно отсюда: [http://php.net/downloads.php](http://php.net/downloads.php)

Если у вас возникли сложности с установкой инструментов, то обратитесь на 
[форум за помощью](http://yiiframework.ru/forum/viewforum.php?f=17&sid=7d16a10cc45601f77dfd89c094b0b4f9))

### Установка руководства

Если всё удачно, то выполните три команды:

```
git clone "https://github.com/githubjeka/yii2-tutorial"
cd yii2-tutorial
php -S localhost:8888
```

не закрывая терминал, перейдите по адресу [http://localhost:8888/scripts/](http://localhost:8888/scripts/).
Если страница не доступна, то возможно порт 8888 занят каким-нибудь процессом. Попробуйте другой порт, например 9000

```
php -S localhost:9000
```
не закрывая терминал, перейдите по адресу [http://localhost:9000/scripts/](http://localhost:8888/scripts/).

### Как обновить руководство до актуального состояния?

Самый простой способ - удалить учебник и повторить шаги из секции "Установка руководства".

Второй способ - скачать только отличия, которых в вашей версии руководства нету. Для этого выполните:

```
cd yii2-tutorial
git remote update
```

Далее нужно обратить внимание на ветки git, которые имеют изменения:

```
git remote update                                         
    Fetching origin                                             
    remote: Counting objects: 3, done.                          
    remote: Compressing objects: 100% (3/3), done.              
    remote: Total 3 (delta 0), reused 0 (delta 0), pack-reused 0
    Unpacking objects: 100% (3/3), done.                        
    From https://github.com/githubjeka/yii2-tutorial            
       8d08bb3..537ddc6  master     -> origin/master
       9aa3ff1..fb14483  step-1.2   -> origin/step-1.2                                    
```
  
Изменения имеются в `master` и `step-1.2` ветках. Их необходимо локально обновить, выполнив команды `git checkout` и
`git pull`. Например для `step-1.2` необходимо выполнить в yii2-tutorial:
  
```
  git checkout step-1.2
  git pull  
                                               
    Updating 9aa3ff1..fb14483                                   
    Fast-forward                     
     readme.md | 15 ++++++++++++++-                             
     1 file changed, 14 insertions(+), 1 deletion(-)            
```

### Лицензия & Авторские права

<a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/3.0/deed.ru">
<img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by-nc-nd/3.0/88x31.png" />
</a>

Yii2 Интерактивное руководство создания сайта на русском языке (с) 2015 Evgeniy Tkachenko (<et.coder@gmail.com>)
распространятся под <a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/3.0/deed.ru">Creative Commons Attribution-NonCommercial-NoDerivs 3.0 Unported License.</a>
