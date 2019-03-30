# iContent Extractor

Собирает контент из ссылок собранных в sitemap.xml.

**Параметры запуска:**
- --url (-u) - Спарсить контент только с указанного sitemap
- -- file (-f) - Спарсить контент из ссылок в указанном файле
- --text-mode (-m) - Варианты: `text` либо `html`. При выборе варианта `text`(по-умолчанию) все html теги будут вырезаться. При выборе варианта `html` исходная структура html будут сохраняться.
- --save-mode (-s) - Варианты: `single` либо `multi`. При выборе варианта `single` статьи будут складываться в один TXT файл. При выборе варианта `multi` статьи будут складываться в указанную папку, каждая в отдельный файл.
- --save-to - В зависимости от выбранного `save-mode` нужно указать либо путь к файлу (при выборе `save-mode=single`), либо путь к папке (при выборе `save-mode=multi`)

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
```

- Автор: NoHate
- Контакты: @PlzDontHate