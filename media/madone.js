var Madone = window.Madone = {};

// URI к системе управления
Madone.uri = /(\/[^\/]+)/.test(location.pathname) ? RegExp.$1 : '';

// Создание Визуального редактора
Madone.createRichTextEditor = function (id, options) {
    if (!options) {
        options = {};
    }
    if (!( 'height' in options )) {
        options.height = 300;
    }
    if (!( 'value' in options )) {
        options.value = '';
    }

    CKEDITOR.config.customConfig = '/media/ckeditor.config.js?20111023';

    return $('#' + id).ckeditor(options);
};

Madone.useRichTextEditorAPI = function (instanceName, callback) {
    ( function wait() {
        try {
            var instance = CKEDITOR.instances[instanceName];
            callback.call(instance);
        } catch (e) {
            setTimeout(wait, 200);
        }
    } )();
};

Madone.nestedSortableOptions = {
    disableNesting:'no-nest',
    forcePlaceholderSize:true,
    handle:'div',
    helper:'clone',
    items:'li',
    opacity:.6,
    placeholder:'placeholder',
    revert:100,
    tabSize:15,
    tolerance:'pointer',
    toleranceElement:'> div'
};

Madone.enableRichTextareas = function (immediate) {
    var fckCnt = 1;
    $('textarea[rich=yes]').bind('show',
            function () {
                var $this = $(this);
                if (!$this.data('fck')) {
                    var id = 'fck' + fckCnt++;
                    $this.attr('id', id).data('fck', true);
                    var inputHeight = $this.height();
                    Madone.createRichTextEditor(id, inputHeight ? { height:inputHeight } : null);
                    Madone.useRichTextEditorAPI(id, function () {
                        this.on('selectionChange', function () {
                            this.updateElement();
                        });
                    });
                }
            }).bind('update', function () {
                var $this = $(this);
                var id = $this.attr('id');
                Madone.useRichTextEditorAPI(id, function () {
                    this.updateElement();
                });
            });

    if (immediate !== false) {
        $('textarea[rich=yes]:visible').trigger('show');
    }

    return this;
};

Madone.enableDatepickers = function (immediate) {
    $('input[datepicker=yes]').bind('show', function () {
        $(this).datepicker({
            dateFormat:'dd.mm.yy',
            duration:'',
            firstDay:1,
            dayNamesMin:['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
            monthNames:['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь']
        });
    });

    if (immediate !== false) {
        $('input[datepicker=yes]:visible').trigger('show');
    }

    return this;
};

// Галерея
Madone.ImageGallery = Object.create(Storm.Form).extend({
    stormModel:'',
    form:$("<form>").addClass("a-unit-form").addClass("form-stacked").append(
            '<h2 text="title">Заголовок:</h2>' +
                    '<div class="block"><div class="gallery"></div></div>' +
                    '<form class="form-stacked" style="margin-top: 1em;"><div class="clearfix"><label>Файлы:</label>' +
                    '<input type="file" name="image" class="uploadify" multiple/>' +
                    '<span class="help-block">Можно выбрать сразу несколько файлов</span></div></form>' +
                    '<div class="actions">' +
                    '<button class="cancel btn">Закрыть</button>' +
                    '</div>'
    ),
    getItemFormTemplate:function () {
        return $("<span>").addClass("gallery-form")
                .append(
                '<img class="image" src=""><div class="form">' +
                        '<div><label>Название:</label><textarea class="width-100" name="title"></textarea></div>' +
                        '<div class="block">' +
                        '<button class="submit btn primary">Сохранить</button>' +
                        '<button class="cancel btn">Отмена</button>' +
                        '</div>' +
                        '</div>'
        )
    },
    onEditClick:function (event, Form, button) {
        var item = $(button).parents('.thumb');
        var obj = Object.create(Storm.Form).extend({
            object:Storm.buildPath(button),
            form:Form.getItemFormTemplate(),
            item:$(button).parents('.thumb'),
            onFill:function (form, data) {
                form.find('.image')
                        .attr('src', data.image.cms ? data.image.cms.uri : '/static/i/admin/1x1.gif')
                        .attr('width', data.image.cms ? data.image.cms.width : 50)
                        .attr('height', data.image.cms ? data.image.cms.height : 50);
            },
            onFillItem:function (item, data) {
                item.find('.image').attr('title', data.title || '');
            },
            onStart:function () {
            },
            onCreate:function (form) {
            }
        });
        obj.start();
    },
    getItemTemplate:function () {
        var Obj = this;
        var template = $("<a>").addClass('thumb').append(
                $("<span>").addClass('control').append(
                        $("<img>").attr('title', 'Редактировать')
                                .attr('src', '/static/i/admin/icons/16/pencil.png')
                                .attr('width', '16')
                                .attr('height', '16')
                                .addClass('edit-item')
                                .click(function (event) {
                                    Obj.onEditClick(event, Obj, this);
                                })
                ).append(
                        $("<a>").addClass('zoom-item-a').append(
                                $("<img>").attr('title', 'Увеличить')
                                        .attr('src', '/static/i/admin/icons/16/magnifier.png')
                                        .attr('width', '16')
                                        .attr('height', '16')
                                        .addClass('zoom-item')
                        )
                ).append(
                        $("<img>").attr('title', 'Удалить')
                                .attr('src', '/static/i/admin/icons/16/cross.png')
                                .attr('width', '16')
                                .attr('height', '16')
                                .addClass('delete-item')
                                .click(function () {
                                    var img = $(this).parents('.thumb');
                                    // Затеняем все изображения кроме удаляемого, чтобы дать понять пользователю, что он удаляет
                                    var other = $(this).parents('.gallery').children().not(img);
                                    other.addClass('semitransparent');
                                    if (confirm('Удалить изображение?')) {
                                        Storm.remove(Storm.buildPath(this), function () {
                                            var list = img.parents('.gallery');
                                            img.remove();
                                            if (!list.find('.thumb, .gallery-form').size()) {
                                                list.addClass('empty').text('Изображения не загружены.');
                                            }
                                        });
                                    }
                                    other.removeClass('semitransparent');
                                })
                )
        ).append(
                $("<img>").addClass("image")
        );

        return template;
    },

    onCreate:function (form) {
        var Obj = this;
        // Сортировка картинок в форме
        form.find('.gallery')
                .attr('stormModel', Obj.stormModel)
                .sortable({
                    stop:function (e, ui) {
                        var objects = {};
                        form.find('.gallery .thumb').each(function (i) {
                            objects[ $(this).attr('stormObject') ] = { position:i + 1 };
                        });
                        Storm.update(Obj.stormModel, objects);
                    }
                });
    },
    onShow:function (form) {
        var Obj = this;
        form.find('.uploadify').fileupload({
            formData:[
                {name:"section", value:Obj.loadedData.id},
                {name:"PHPSESSID", value:Obj.PHPSESSID}
            ],
            sequentialUploads:true,
            dropZone:form.find('.gallery'),
            dataType:'json',
            url:Storm.getPath(Obj.stormModel).getUri() + '/create/',
            done:function (e, data) {
                Obj.appendItem(data.result.data);
            }
        }).bind('fileuploaddragover', function (e) {
                    form.find('.gallery').css('background-color', '#fce7be');
                });
    },

    onFill:function (form, data) {
        var Obj = this;

        this.form.find('.gallery').addClass('loading');
        // На заполнение формы загружаем список картинок и выводим их
        var query = Object.create(Storm.Query).use('filter', { section:data.id }).use('order', 'position');
        Storm.retrieve(Obj.stormModel, query.get(), Function.delegate(this, function (data) {
            this.form.find('.gallery').removeClass('loading');
            if (Object.typeOf(data) === 'array' && data.length) {
                for (var i = 0; i < data.length; i++) {
                    this.appendItem(data[i]);
                }

                /* Зумилка картинок галереи */
                $('.zoom-item-a').fancybox();
            } else {
                this.form.find('.gallery').removeClass('loading').addClass('empty').text('Изображения не загружены.');
            }
        }));
    },

    // Уникальный метод формы — добавление изображения в список, используется при отображении списка изображений
    // на сервере и добавлении загруженных изображений.
    appendItem:function (data) {
        // Удаляем надпись «Нет изображений», если это изображения будет первым в списке
        if (!this.form.find('.gallery .thumb, .gallery .gallery-form').size()) {
            this.form.find('.gallery').html('').removeClass('empty');
        }
        var img = this.getItemTemplate();
        this.form.find('.gallery').append(img);
        img.attr('stormObject', data.id);
        img.find('.image')
                .attr('src', data.image.cms ? data.image.cms.uri : '/static/i/admin/1x1.gif')
                .attr('largeSrc', data.image.original.uri)
                .attr('title', data.title || '')
                .attr('width', data.image.cms ? data.image.cms.width : 50)
                .attr('height', data.image.cms ? data.image.cms.height : 50);

        img.find('.zoom-item-a')
                .attr('href', data.image.original.uri)
                .attr('title', data.title || '')
                .attr('rel', 'section_' + data.section);

        img.show();
    }
});

(function ($) {
    $.fn.madoneUnits = function (options) {
        // Create some defaults, extending them with any options that were provided
        var settings = $.extend({
            'itemSelector':'.a-unit',
            'sortable': false
        }, options);

        return this.each(function () {
            var $this = $(this);
            var model = $this.attr('stormModel');

            if (settings.sortable) {
                $this.sortable({
                    helper:'clone',
                    placeholder:'ui-sortable-placeholder',
                    forcePlaceholderSize:true,
                    stop:function (event, ui) {
                        var objects = {};
                        $.each($this.sortable('toArray'), function (i, id) {
                            try {
                                objects[ parseInt(id) ] = { position:i + 1 };
                            } catch (e) {
                            }
                        });
                        $.post("/admin/" + model + "/update/", { objects:JSON.stringify(objects) }, function (r) {
                            if (!r.success) {
                                alert(r.message);
                            }
                        }, 'json');
                    }
                });
            }

            $this.find('.enabled').live('click', function (e) {
                Storm.toggle(Storm.buildPath(this), Function.delegate(this, function (data) {
                    if (data.enabled) {
                        $(this).removeClass('enabled_off').parents(settings.itemSelector).filter(':first').removeClass('disabled');
                    }
                    else {
                        $(this).addClass('enabled_off').parents(settings.itemSelector).filter(':first').addClass('disabled');
                    }
                }));
            });
            $this.find('.delete').live('click', function (e) {
                var item = $(this).parents(settings.itemSelector).filter(':first');
                // TODO: correct title selector
                var title = item.find('h2:first').text().trim();
                if (confirm('Вы действительно хотите удалить «' + title + '»?')) {
                    // TODO: correct child nodes selector
                    var nested = item.children(settings.itemSelector).length;
                    if (nested) {
                        if (!confirm('Все вложенные элементы также будут удалены! Продолжить?')) {
                            return false;
                        }
                    }

                    Storm.remove(Storm.buildPath(this), Function.delegate(this, function () {
                        item.remove();
                    }));
                }
            });
        });
    };
})(jQuery);