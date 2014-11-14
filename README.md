Версия 0.0.3
- bRewrite добавлено поле pageno  в базу, удалено поле data, изменен механизм редиректа на страницы

<table border=0>
    <tr>
        <td>
            <a href="http://blib.xe0.ru" target="_blanck" style="text-decoration:none;">
               <img src="http://blib.xe0.ru/index_logo.png" />
            </a>
        </td>
        <td>
            <strong>
                Библиотека автономных и полуавтономных блоков, которые можно впилить в любой интерфейс.
            </strong>
        </td>
    </tr>
</table>
<hr />

<ul>
    <li>Есть как визуальные блоки для оформления чего-либо (например блок автогенерации таблицы из json-ответа сервера),</li>
    <li>так и блоки логики (b-blib - блок для подключения блоков)</li>
</ul>

<table>
    <tr>
        <th colspan="2">Функционал b-blib</th>
    </tr>
    <tr>
        <th>метод</th>
        <th>описание</th>
    </tr>
    <tr>
        <td rowspan="2">blib(selector)</td>
        <td>выделение дом элемента (урезанный аналог $ jquery)</td>
    </tr>
    <tr>
        <td>
            selector - поиск по тегу<br />
            #selector - поиск по идентификатору<br />
            .selector - поиск по классу<br />
            массив селекторов формируется из аргументов (selector, #selector, .selector)
            возвращает объект
        </td>
    </tr>
    <tr>
        <td rowspan="2">
        example = blib(selector)<br />
        example.each(function)<br />
        example.html(string)<br />
        example.append(object)<br />
        example.length<br />
        example[number]<br />
        </td>
        <td>example - обьект с методами</td>
    </tr>
    <tr>
        <td>
            function - функция, применяемая к каждому элементу в обьекте<br />
            string - текст для вставки в дом элемент, если нет аргументов возвращает то, что было<br />
            object - добавляет дом элемент внутрь каждого<br />
            length - сколько всего выделено элементов<br />
            number - индексы элементов
        </td>
    </tr>
    <tr>
        <td rowspan="2">blib.include(file [,target])</td>
        <td>основной метод подключения блока (css - сразу, шаблон - если есть, js - после загрузки дом дерева )</td>
    </tr>
    <tr>
        <td>
            file - путь до блока,<br />
            target - селектор контейнера, куда будет загружен блок
        </td>
    </tr>
    <tr>
        <td rowspan="2">blib.css(cssFile [,inCache])</td>
        <td>подрубить таблицу стилей к шапке</td>
    </tr>
    <tr>
        <td>
            cssFile - путь до css файла,<br />
            inCache - массив имен стилевых таблиц содержащихся в подключенном css(в случае если подключается кэш а не стили для отдельного блока)
        </td>
    </tr>
    <tr>
        <td rowspan="2">blib.js(jsFile [,inCache])</td>
        <td>подрубить файлы скриптов к шапке</td>
    </tr>
    <tr>
        <td>
            jsFile - путь до js файла,<br />
            inCache - массив имен скриптов содержащихся в подключенном js(в случае если подключается кэш а не скрипты для отдельного блока)
        </td>
    </tr>
    <tr><th colspan="2">дополнительные методы</th></tr>
    <tr></tr>
    <tr>
		<td rowspan="2">blib.vanishLoad({<br />'script':(bool),<br />'exception':(array),<br />'order': (array)<br />})</td>
		<td>
			Метод для склейки и кеширования файлов (стилей и скриптов) решение о подгрузке принимается на основе данных localStotage
		</td>
    </tr>
    <tr>
       <td>
        	{<br />
		 script - грузить все скрипты или только прописанные в order,<br />
		 exception - блоки которые надо игнорировать при сборке кеша,<br />
		 order - порядок склейки блоков (jQuery раньше jQuery-ui)<br />
		}
       </td>
    </tr>
    <tr><th colspan="2">расширения</th></tr>
    <tr>
        <td rowspan="2">blib.build({<br />'blockName'(string), <br />handle(function)}||<br />{<br />'get':{'x':n, 'y':n},<br /> 'data':{'x':n, 'y':n},<br /> 'post1':n,<br /> 'post2':n <br />...<br />})</td>
        <td>метод для отправки запроса на сервер и сбора дом дерева на стороне клиента из ответа(ajax)</td>
    </tr>
    <tr>
        <td>
	    	{<br />
		 blockName(string) - идентификатор для сопоставления заранее загруженного обработчика,<br />
		 handle(function) - собственно сам обработчик,<br />
		}<br />
		{<br />
		 'get':{object} - обьект параметров передаваемых в запросе get-методом,<br />
		 следующие параметры - передаются post-ом,<br />
		 кроме data - передаются post-ом сам обьект без идентификатора (чистые данные)<br />
		}
        </td>
    </tr>
    <tr>
        <td rowspan="2">blib.build.handler({server's request})</td>
        <td>метод для обработки сериализованных данных взятых на клиентской стороне</td>
    </tr>
    <tr>
        <td>
	    	{server's request} - сериализованные данные.
        </td>
    </tr>
</table>


