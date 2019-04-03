# iContent Extractor

Собирает контент из ссылок собранных в sitemap.xml либо по прямой ссылке на статью.

Скрипту можно давать ссылки как на обычный sitemap, так и на групповой файл sitemap.
Скрипт автоматически пройдется по всем внутренним sitemap.

**Параметры запуска:**
- --url (-u) - Спарсить контент только с указанного sitemap
- -- file (-f) - Спарсить контент из ссылок в указанном файле (можно указать как ссылки на множество sitemap, так и на конкретные статьи)
- --text-mode (-m) - Варианты: `text` либо `html`. При выборе варианта `text`(по-умолчанию) все html теги будут вырезаться. При выборе варианта `html` исходная структура html будут сохраняться.
- --save-mode (-s) - Варианты: `single` либо `multi`. При выборе варианта `single` статьи будут складываться в один TXT файл. При выборе варианта `multi` статьи будут складываться в указанную папку, каждая в отдельный файл.
- --save-to - В зависимости от выбранного `save-mode` нужно указать либо путь к файлу (при выборе `save-mode=single`), либо путь к папке (при выборе `save-mode=multi`)
- --user-agent (-g) - Указать свой кастомный User Agent.

**Примеры запуска:**
```
# Вариант с параметрами по-умолчанию
$ php run.php download --url="http://site.com/sitemap.xml"

# Вариант запуска с различными параметрами text-mode/save-mode
$ php run.php download --text-mode=html --save-mode=multi --url="http://site.com/sitemap.xml"
$ php run.php download --text-mode=text --save-mode=single --url="http://site.com/sitemap.xml"
$ php run.php download --text-mode=text --save-mode=multi --url="http://site.com/sitemap.xml"

# Запуск с параметрами по-умолчанию, но ссылки беруться из файла links.txt
$ php run.php download --file links.txt

# Сохранять только текст, каждый в свой файл в папку my-content
$ php run.php download --text-mode=text --save-mode=multi --save-to=my-content --url="http://site.com/sitemap.xml"

# Указываем свой User Agent
$ php run.php download --url="http://site.com/sitemap.xml" --user-agent "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36"
```

## FAQ

> Скрипт падает с ошибкой: "PHP Fatal error: Uncaught TypeError: Argument 1 passed to iterator_to_array() must implement interface Traversable" что делать?
>
У некоторых __Windows__ пользователей выскакивает подобная ошибка. При запуске на Linux такой ошибки не наблюдалось.
Пока что у меня нету решения для данной проблемы, так что остается либо запускать скрипт на Linux, либо пропускать ссылку/sitemap на которых вылетает данная ошибка.
 

- Автор: NoHate
- Контакты: @PlzDontHate