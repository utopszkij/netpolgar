jquery.piegraph
===============

jQuery plugin to render a Pie Graph with CSS

Exemple:
-------

```html
<!DOCTYPE HTML>
<html>
  <head>   
  </head>
  <body>
    <div id="head">
      Teste com gráfico
    </div>  
    <div id="chart">
    </div>
  <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="https://github.com/edgardleal/jquery.piegraph/raw/master/jquery.piegraph.js"></script>
  <script>
     $("#chart").chart({data : new Array(90,90, 45, 10,5,25,15,30),
                    labels : new Array("A", "B", "C", "D", "E", "F", "G", "H"),
                    width : 400});  
  </script>
  </body>
</html>
```
    
Test the plugin:
[jsfiddle](http://jsfiddle.net/edgardleal/gfnw72ay/ "try it")

