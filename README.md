<h1>
Установка и запуск
</h1>

<h2>Установка</h2>

Установить зависимости:
<code>
composer install
</code>
</br>

Скопировать файл окружения:
<code>
cp .env.example .env
</code>
</br>

Изменить настройки коннекта к БД в файле окружения <b>.env</b>:
<p>
<code>
DB_DATABASE= 
</code>
</br>

<code>
DB_USERNAME= 
</code>
</br>

<code>
DB_PASSWORD= 
</code>
</p>
</br>


Залинковать папки, для доступа к логу ошибок импорта:
<code>
php artisan storage:link
</code>
</br>

Накатить миграцию:
<code>
php artisan migrate
</code>
</br>

Сгенерировать ключ шифрования:
<code>
php artisan key:generate
</code>
</br>

Запустить сервер (опционально, для доступа к ссылкам на отчет):
<code>
php artisan serve
</code>
</br>


<h2>Непосредственно запуск</h2>
<p>Выполнить консольную команду, с именем файла, в качестве параметра:</p>
</br>

<p>
Файл из ТЗ: 
<code>
php artisan example:merge random.csv
</code>
</br>

Файл с данными, без ошибок: 
<code>
php artisan example:merge random_clean.csv
</code>
</br>

Файл с миллоном строк:
<code>
php artisan example:merge random_large.csv
</code>
</p>
</br>

<h2>Комментарии разработчика</h2>

<p>
В результате выполнения/ошибки в консоли отобразяться соответвующие уведомления.
</p>

<p>
В случае частичного импорта (из-за ошибок валидации), будет сгенерирован отчёт в формате .csv по шаблону исходного файла из ТЗ и требованиям дополнения его результатами валидации. Я решил оформить его в таком виде, что бы в случае импорта большого колличества данных, можно было его адекватно обработать.

<p>
"Особенный" формат .csv из ТЗ дублируется при формировании отчета, что бы можно было манипулировать строками, в соответвии с исходным файлом.
</p>

<p>
Обработку файла с миллионом строк ждать не рекомендую (по приблизительной оценке 68% данных валидны) ~ 680000 записей, 
файл создан для примера, что бы показать, что выбранный способ чтения файла (с использованием генератора) позволяет это сделать без отказа по памяти.
</p>
<p>
Цели оптимизации импорта большого кол-ва строк не стояло. 
</p>


<h1>
ТЗ Migrate data
</h1>

<p>
Задача: 
Используя Laravel/Lumen фреймворк, написать консольную команду которая перенесет данные из файла random.csv в базу данных в таблицу customers
</p>

<h2>Условия:</h2>
<p>
Данные должны быть нормализованы и приведены к следующим типам:
name - string
email - string
age - int
location - string
    
Перед записью в БД необходимо провести валидацию:</br>
Записи с невалидным email не должны быть созданы</br>
Записи с невалидным age не должны быть созданы (допустимые значения 18 - 99)</br>
Записи с невалидным location должны быть созданы, однако невалидные значения должны быть заменены на Unknown</br>
Консольная команда должна после исполнения должна выводить отчет, содержащий в себе все не созданные записи и причину их невалидности. </br>
Например, если email был не валиден, необходимо вывести исходную строку из файла полностью и добавить в поле error название (не значение) невалидной колонки (email)</br>
Перед каждым запуском консольная команда должна очищать таблицу customers 
</p>


<h2>Результат:</h2>
<p>
Ссылка на гит репозиторий, содержащий в себе Readme файл с описанием шагов для выполнения задачи.
</p>

