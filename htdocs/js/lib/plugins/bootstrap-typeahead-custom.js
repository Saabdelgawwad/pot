//  ----------------------------------------------------------------------------
//
//  bootstrap-typeahead-custom.js
//  
//  Dieses Plugin basiert auf Bootstrap Typeahead+ v2.0 von Terry Rosen
//  https://github.com/tcrosen/twitter-bootstrap-typeahead
//
//  ----------------------------------------------------------------------------

!
function ($) {

    var _defaults = {
        className: '',
        minLength: 1,
        source: [],
        sourceDefaults: {
            title: null,
            action: null,
            tmpl: null,
            maxResults: 8,
            doFilter: true,
            doSort: true,
            display: null,
            val: null,
            info: null,
            operator: null,
            operatorMaxResults: 8
        },
        itemSelected: function() {}
    },

    _keyCodes = {
        DOWN: 40,
        ENTER: 13,
        ESCAPE: 27,
        TAB: 9,
        UP: 38
    },

    Typeahead = function (element, options) {
        this.$element = $(element);
        this.options = $.extend(true, {}, $.fn.typeahead.defaults, options);
        this.$menu = $('<ul class="typeahead dropdown-menu"/>').addClass(this.options.className).appendTo('body');
        this.shown = false;
        this.xhr = {};
        this.items = {};
        
        this.initSources();
        this.listen();
    };

    Typeahead.prototype = {

        constructor: Typeahead,

        initSources: function() {
            var that = this,
            sources = [];
            
            this.sources = [];
            
            this.abortXhr();
            this.items = {};
            
            if (this.options.source) {
                if (this.options.source instanceof Array) {
                    sources = this.options.source;
                } else {
                    sources = [this.options.source];
                }
                
                $.each(sources, function(key, source) {
                    if (typeof source !== "object") {
                        source = {action: source};
                    }
                    that.sources.push($.extend({}, that.options.sourceDefaults, source));
                });
            }
        },
        
        setSource: function(source) {
            this.options.source = source;
            this.initSources();
        },
                
        abortXhr: function() {
            var that = this;
            $.each(this.xhr, function(key) {
                that.xhr[key].abort();
            });
            this.xhr = {};
        },

        eventSupported: function(eventName) {
            var isSupported = (eventName in this.$element);

            if (!isSupported) {
                this.$element.setAttribute(eventName, 'return;');
                isSupported = typeof this.$element[eventName] === 'function';
            }

            return isSupported;
        },

        lookup: function () {
            var that = this;
            
            this.abortXhr();
            
            var sources = this.sources;
            var maxResultsOption = 'maxResults';
            var items = this.items;
            this.items = {};
            this.query = this.$element.val();
            
            $.each(this.sources, function(key, source) {
                if(source.operator && that.query.toLowerCase().indexOf(source.operator.toLowerCase()) === 0) {
                    sources = {};
                    sources[key] = source;
                    maxResultsOption = 'operatorMaxResults';
                    that.query = that.query.substr(source.operator.length).replace(/^\s*/, '');
                    return false;// break
                }
            });
            
            if (this.query.length < this.options.minLength) {
                this.hide();
                return;
            }
            
            $.each(sources, function(key) {
                if(items[key]) {
                    that.items[key] = items[key];
                }
            });
            
            $.each(sources, function(key, source) {
                if (typeof source.action === 'string') {
                    that.xhr[key] = $.ajax({
                        url: source.action,
                        data: {
                            query: that.query
                        },
                        success: function(data) {
                            delete that.xhr[key];
                            that.items[key] = that.filter(source, data, maxResultsOption);
                            that.render();
                        }
                    });
                } else if (typeof source.action === 'function') {
                    that.items[key] = that.filter(source, source.action(that.query), maxResultsOption);
                }
            });
            
            that.render();
        },

        filter: function(source, items, maxResultsOption) {
            var that = this;
            
            if (source.doFilter) {
                items = $.grep(items, function (item) {
                    return item[source.display].toLowerCase().indexOf(that.query.toLowerCase()) !== -1;
                });
            }
            
            if (source.doSort) {
                items = this.sorter(source, items);
            }

            return items.slice(0, source[maxResultsOption]);
        },

        sorter: function (source, items) {
            var beginswith = [],
            caseSensitive = [],
            caseInsensitive = [],
            noMatch = [],
            item;

            while (item = items.shift()) {
                if (!item[source.display].toLowerCase().indexOf(this.query.toLowerCase())) {
                    beginswith.push(item);
                } else if (item[source.display].indexOf(this.query) !== -1) {
                    caseSensitive.push(item);
                } else if (item[source.display].toLowerCase().indexOf(this.query.toLowerCase()) !== -1) {
                    caseInsensitive.push(item);
                } else {
                    noMatch.push(item);
                }
            }

            return beginswith.concat(caseSensitive, caseInsensitive, noMatch);
        },

        show: function () {
            // Falls das Input-Feld in einem Modal ist und das Modal geschlossen wurde, brechen wir ab.
            if (!this.$element.is(':visible')) {
                return;
            }
            
            var pos = $.extend({}, this.$element.offset(), {
                height: this.$element[0].offsetHeight
            });

            this.$menu.css({
                top: pos.top + pos.height,
                left: pos.left
            });

            this.$menu.show();
            this.shown = true;
        },

        hide: function () {
            this.$menu.hide();
            this.shown = false;
        },

        highlighter: function (text) {
            var query = window.encodeHtml(this.query).replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&');
            text = window.encodeHtml(text);
            return text.replace(new RegExp('(' + query + ')', 'ig'), function(match) {
                return '<strong>' + match + '</strong>';
            });
        },

        render: function () {
            var that = this,
            html = [];
    
            var selectedValue = null;
            
            var $selectedItem = this.$menu.find('li[data-value].active');
            if ($selectedItem.length === 1) {
                selectedValue = $selectedItem.attr('data-value');
            }
    
            $.each(this.sources, function(key, source) {
                var isLoading = that.xhr[key] !== undefined;
                
                if (!that.items[key] || that.items[key].length === 0) {
                    return;
                }
                
                if (source.title) {
                    if (html.length) {
                        html.push('<li class="divider"/>');
                    }
                    html.push('<li class="nav-header">' + window.encodeHtml(source.title) + '</li>');
                }
                
                html = html.concat($.map(that.items[key], function (item) {
                    var display = item[source.display],
                    value = JSON.stringify(typeof source.val === 'string' ? item[source.val] : $.extend({}, source.val, item)),
                    info = item[source.info],
                    content = that.highlighter(display);
                    
                    if (typeof source.tmpl === 'function') {
                        content = source.tmpl(item, content);
                    } else if ($.trim(info) !== '') {
                        content += '<span class="info">' + info + '</span>';
                    }
                    
                    return $('<li/>')
                            .attr('data-display', display)
                            .attr('data-value', value)
                            .addClass(selectedValue === value ? 'active' : '')
                            .html('<a href="#">' + content + '</a>');
                }));
            });
            
            this.$menu.html(html);

            if (html.length) {
                if (this.$menu.find('.active').length === 0) {
                    this.$menu.find('li[data-value]:first').addClass('active');
                }
                this.show();
            } else {
                this.hide();
            }
        },

        select: function () {
            var $selectedItem = this.$menu.find('li[data-value].active');
            if ($selectedItem.length === 1) {
                this.$element.val($selectedItem.attr('data-display')).change();
                this.options.itemSelected(JSON.parse($selectedItem.attr('data-value')));
                this.abortXhr();
                this.items = {};
                this.hide();
            }
        },

        next: function () {
            var active = this.$menu.find('.active').removeClass('active');
            var next = active.nextAll("li[data-value]:first"); 

            if (!next.length) {
                next = this.$menu.find('li[data-value]:first');
            }

            next.addClass('active');
        },

        prev: function () {
            var active = this.$menu.find('.active').removeClass('active');
            var prev = active.prevAll("li[data-value]:first");          

            if (!prev.length) {
                prev = this.$menu.find('li[data-value]:last');
            }

            prev.addClass('active');
        },

        listen: function () {
            this.$element
            .on('focus', $.proxy(this.focus, this))
            .on('blur', $.proxy(this.blur, this))
            .on('keyup', $.proxy(this.keyup, this));

            if (this.eventSupported('keydown')) {
                this.$element
                .on('keydown', $.proxy(this.keypress, this));
            } else {
                this.$element
                .on('keypress', $.proxy(this.keypress, this));
            }

            this.$menu
            .on('click', $.proxy(this.click, this))
            .on('mouseenter', 'li', $.proxy(this.mouseenter, this));
        },

        keyup: function (e) {
            e.stopPropagation();
            e.preventDefault();

            switch (e.keyCode) {
                case _keyCodes.DOWN:
                case _keyCodes.UP:
                    break;
                case _keyCodes.TAB:
                case _keyCodes.ENTER:
                    if (!this.shown) return;
                    this.select();
                    break;
                case _keyCodes.ESCAPE:
                    this.abortXhr();
                    this.items = {};
                    this.hide();
                    break;
                default:
                    this.lookup();
            }
        },

        keypress: function (e) {
            e.stopPropagation();

            if (!this.shown) return;

            switch (e.keyCode) {
                case _keyCodes.TAB:
                case _keyCodes.ESCAPE:
                case _keyCodes.ENTER:
                    e.preventDefault();
                    break;
                case _keyCodes.UP:
                    e.preventDefault();
                    this.prev();
                    break;
                case _keyCodes.DOWN:
                    e.preventDefault();
                    this.next();
                    break;
            }
        },

        focus: function() {
            if(!this.shown) {
                this.lookup();
            }
        },

        blur: function (e) {
            var that = this;
            e.stopPropagation();
            e.preventDefault();
            setTimeout(function () {
                if (!that.$element.is(':focus') && that.$menu.has(':focus').length === 0) {
                    that.abortXhr();
                    that.items = {};
                    that.hide();
                }
            }, 150);
        },

        click: function (e) {
            e.stopPropagation();
            e.preventDefault();
            this.select();
        },

        mouseenter: function (e) {
            this.$menu.find('.active').removeClass('active');
            $(e.currentTarget).addClass('active');
        }
    };

    //  Plugin definition
    $.fn.typeahead = function (option) {
        return this.each(function () {
            var $this = $(this),
            data = $this.data('typeahead'),
            options = typeof option === 'object' && option;

            if (!data) {
                $this.data('typeahead', (data = new Typeahead(this, options)));
            }

            if (typeof option === 'string') {
                data[option]();
            }
        });
    };

    $.fn.typeahead.defaults = _defaults;
    $.fn.typeahead.Constructor = Typeahead;

} (window.jQuery);
