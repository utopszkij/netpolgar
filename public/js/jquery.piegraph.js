/*global jQuery */
/*
 *
 *
 *
 * @author Edgard Leal <edgardleal@gmail.com>
 */
'use strict';
(function ($) {
    var chartFactory = function (opt) {
        this.options = {
            width: 400,
            height: 200,
            data: null,
            labels: null
        };
        this.colors = [
            'red',
            'green',
            'yellow',
            'grey',
            'blue',
            'lime',
            'orange',
            'purple'
        ];

        this.options = $.extend(this.options, opt);
        this.create = function () {
            var pierIndex = 0, 
            	 i = 0, 
            	 labelIndex = 0, 
            	 colorIndex = 0,
                width = this.options.width, 
                height = width * 0.5, 
                self = this;
            function getPier(deg) {
                var cssSize = 'width:' + height + 'px;height:' + height + 'px;',
                    halfSize = height * 0.5,
                    commonCss = 'position:absolute;clip : rect(' + halfSize + 'px,' + height + 'px,' + height + 'px,0px);',
                    html = $('<div style = \'-moz-transform: rotate(' + pierIndex + 'deg);-webkit-transform: rotate(' + pierIndex + 'deg);' + cssSize + commonCss + '\'><a href = \'#\' class = \'pier\' style = \'-moz-transform:rotate(' + (deg - 180) + 'deg);-webkit-transform:rotate(' + (deg - 180) + 'deg);' + cssSize + commonCss + 'border-radius:' + height + 'px;box-shadow : inset 0 0 8px black;background:' + self.colors[colorIndex++ % self.colors.length] + '\' title = \'' + deg + '\'></a></div>');
                pierIndex += deg;
                return html;
            }
            this.css('width', width + 'px').css('height', height + 'px').toggleClass('ui-state-default');
            var total = 0;
            if (this.options.labels == null) {
                this.options.labels = [];
            }
            for (i = 0; i < this.options.data.length; i++) {
                if (this.options.labels[i] == undefined)
                  this.options.labels[i] = '';
                total += this.options.data[i];
            }
            this.createLabels();
            for (i = 0; i < this.options.data.length; i++) {
                this.append(getPier(360 / total * this.options.data[i]));
            }
        };
        this.createLabels = function () {
          var left = this.options.width * 1.2, rows = '', self = this;
          for (var i = 0; i < this.options.data.length; i++) {
            rows += '<tr><td style = \'background-color: ' + self.colors[i % self.colors.length] + '\'>&nbsp;&nbsp;</td>'+
            		'<td>' + this.options.data[i] + '</td><td>' + this.options.unit + ' ' + this.options.labels[i] + '</td></tr>';
          }
          console.log(this);
          // this.append('<table style = \'position:absolute; left:'+this.options.width+'px \'>' + rows + '</table>');
          $('#chartLabels').append('<table>' + rows + '</table>');
        }

        this.create();
    };

  $.fn.chart = chartFactory;
}(jQuery));
