Az ebben a fájlban lévő html fájlok Angular JS szintaxis szerinti direktivákat tartalmazhatnak.

** A fájl nevek kötöjelet nem tartalmazhatnak! **

Ha vannak ezzekkel azonos nevü fájlok a templates/temname/html könyvtárban akkor a rendszer
a templates -ben lévőket használja.

Az ng-include -okban használt templatesek utvonalát 

```
$view->setTemplates($p,['tempname',...]) 
````

hívással kell definiálni a php viewer -ben, és

```
<div ng-include="templates.tempname"></div>
```

formában kell használni a html template -ben. (tempname ne tartalmazza a ".html" -stringet)



