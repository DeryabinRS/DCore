# DCore
Установка CMS
1. Основной репозиторий проекта расположен в открытом доступе на сайте github.com и доступен по ссылке https://github.com/DeryabinRS/DCore. 
Необходимо загрузить проект в папку на локальном сервере (Open Server) через консоль при помощи команды: git clone https://github.com/DeryabinRS/DCore
2. Сделать импорт структуры таблиц с базу данных MySQL, при помощи файла dcore.sql
3. Редактирование файла настроек CMS расположенного в lib/setting.php. Необходимо задать следующие параметры настроек:
- SITE_URL – «Ваш домен (Локальный домен)»
- SMTP_MAIL_HOST – «SMTP хостинг почтового сервера»
- SMTP_MAIL_PORT – «порт почтового сервера»
- SMTP_MAIL_USER – «Логин вашего почтового ящика»
- SMTP_MAIL_PASS – «Пароль вашего почтового ящика»
4. После того как приложение установки приложения, необходимо зарегистрировать пользователя – администратора CMS:
- Перейти по адресу «Ваш домен»/users/
- Заполнить форму регистрации
- Подтвердить регистрацию пользователя при помощи ссылки, которая прийдет на электронный почтовый ящик указанный пользователем при регистрации.
- В базе данных, в таблице dcore_users, колонка «status» изменить значение с 0 на 1, для того чтобы зарегистрированный пользователь приобрел права администрирования с доступом в панель администратора.
5. Доступ в панель администратора осуществляется по адресу: «Ваш_домен»/dc-admin. Необходимо ввести логин и пароль пользователя.
После вышеуказанных процедур, будет доступен весь функционал CMS 