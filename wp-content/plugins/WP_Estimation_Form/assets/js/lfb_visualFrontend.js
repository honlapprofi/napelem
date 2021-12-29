(function ($) {
    "use strict";
    var lfb_isDraggingComponent = false;
    var lfb_elementHoverTimer = false;
    var lfb_currentFormID = 0;
    var lfb_currentStepID = 0;
    var lfb_currentStep = false;
    var lfb_editedItem = false;
    var lfb_copyHelper = null;

    $(window).on('load', function () {
        
        $('#lfb_form').addClass('lfb_visualReady');
        $('#lfb_form').on('lfb_initComponentsMenu', lfb_initComponentsMenu);
        $('#lfb_form').on('lfb_initVisualStep', lfb_initVisualStep);
        $('#lfb_form').on('lfb_onItemDeleted', lfb_onItemDeleted);
        $('#lfb_form').on('lfb_showComponentsMenu', lfb_showComponentsMenu);
        $('#lfb_form').on('lfb_refreshItemDom', function(e,itemID){lfb_refreshItemDom(itemID);});
                
        lfb_currentFormID = $('#lfb_form').attr('data-form');
        if (typeof (window.parent) != 'undefined' && $(window.parent.document).find("#lfb_form").length > 0) {
            window.parent.jQuery('#lfb_form').trigger('lfb_stepFrameLoaded');
            lfb_initComponentsMenu();
        }
        if (lfb_currentStepID != 0) {
            $('#lfb_form .lfb_genSlide[data-stepid="' + lfb_currentStepID + '"]').show();
            $('#lfb_form .lfb_genSlide[data-stepid="' + lfb_currentStepID + '"]').addClass('lfb_activeStep');
        }
    });

    function lfb_initVisualStep(event,stepID, formID) {
        lfb_currentStepID = stepID;
        $(window).resize(function () {

            $('#lfb_bootstraped').css({
                height: $(window).height()
            });
        });
        $('#lfb_bootstraped').css({
            height: $(window).height()
        });
        var domStepID = stepID;
        if (domStepID == 0) {
            domStepID = 'final';
        }

        $('#lfb_bootstraped').addClass('lfb_visualEditing');
        $('#lfb_form').addClass('lfb_visualEditing');
        $('#lfb_form').attr('data-animspeed', '0');
        $('#lfb_form .lfb_genSlide.lfb_activeStep').removeClass('lfb_activeStep');
        $('#lfb_form .lfb_genSlide').hide();
        $('#lfb_form .lfb_genSlide[data-stepid="' + domStepID + '"]').show();
        $('#lfb_form .lfb_genSlide[data-stepid="' + domStepID + '"]').addClass('lfb_activeStep');

        setTimeout(function () {
            $('#lfb_form .lfb_genSlide:not([data-stepid="' + domStepID + '"])').removeClass('lfb_activeStep');
            $('#lfb_form .lfb_genSlide[data-stepid="' + domStepID + '"]').addClass('lfb_activeStep');
        }, 1000);
        $('#lfb_form .lfb_genSlide[data-stepid="' + domStepID + '"] .errorMsg').hide();
        $('#lfb_form .lfb_genSlide[data-stepid="' + domStepID + '"] .lfb_btn-next').show();
        if (!$('#lfb_form .lfb_genSlide[data-stepid="' + domStepID + '"]').is('[data-start="1"]')) {
            $('#lfb_form .lfb_genSlide[data-stepid="' + domStepID + '"] .lfb_linkPreviousCt').show();

        }

        $('#lfb_form .lfb_genSlide[data-stepid="' + domStepID + '"] .lfb_stepTitle').addClass('positioned');

        $('#lfb_form .lfb_genSlide[data-stepid="' + domStepID + '"] .lfb_genContent').css({opacity: 1});

        $('#lfb_form.lfb_bootstraped #lfb_mainPanel').show();
        $('#lfb_form.lfb_bootstraped #lfb_panel > .container-fluid > .row').hide();

        setTimeout(function () {
            var titleHeight = $('#lfb_form.lfb_bootstraped #lfb_mainPanel .lfb_genSlide[data-stepid="' + domStepID + '"] .lfb_stepTitle').height();

            var heightP = $(' #lfb_form.lfb_bootstraped #lfb_mainPanel .lfb_genSlide[data-stepid="' + domStepID + '"] .lfb_genContent').outerHeight() + parseInt($(' #lfb_form.lfb_bootstraped #lfb_mainPanel').css('padding-bottom')) + 102 + titleHeight;

            if (domStepID == 'final') {
                heightP -= 80;
            }
            $(' #lfb_form.lfb_bootstraped[data-form="' + formID + '"] #lfb_mainPanel').animate({minHeight: heightP});
            $(' #lfb_form.lfb_bootstraped[data-form="' + formID + '"] #lfb_mainPanel').css('max-height', 'none');
        }, 300);




        $('#lfb_form .lfb_item:not(.lfb_componentInitialized)').each(function () {
            $(this).addClass('lfb_componentInitialized');
            lfb_initItemToolbar($(this));

            lfb_initItemContent($(this));
        });

        jQuery(window).trigger('resize');
    }
    
    function lfb_getStepByID(stepID, form) {
        var rep = false;
        for (var i = 0; i < form.steps.length; i++) {
            if (form.steps[i].id == stepID) {
                rep = form.steps[i];
                break;
            }
        }
        return rep;
    }


    function lfb_refreshItemDom(itemID) {
        jQuery.ajax({
            url: lfb_data.ajaxurl,
            type: 'post',
            data: {
                action: 'lfb_getItemDom',
                formID: lfb_currentFormID,
                itemID: itemID,
                stepID: lfb_currentStepID,
            }, success: function (itemDom) {
                var $item = $(itemDom);
                if (itemID > 0) {

                    var exItem = $('#lfb_form .lfb_item[data-id="' + itemID + '"]');
                    exItem.after($item);
                    exItem.remove();
                } else {
                    var _stepID = lfb_currentStepID;
                    if (_stepID == 0) {
                        _stepID = 'final';
                    }
                    $('#lfb_form .lfb_genSlide[data-stepid="' + lfb_currentStepID + '"] > .lfb_genContent > .lfb_row').html($item);
                }
                if (itemID > 0) {
                    lfb_initItemToolbar($item);
                    lfb_initItemContent($item);
                    lfb_initNewItemContent($item);

                    $item.find('.lfb_item').each(function () {
                        lfb_initItemToolbar($(this));
                        lfb_initItemContent($(this));
                        lfb_initNewItemContent($(this));

                    });
                } else {

                    $('#lfb_form .lfb_genSlide[data-stepid="' + lfb_currentStepID + '"] > .lfb_genContent > .lfb_row').find('.lfb_item').each(function () {
                        lfb_initItemToolbar($(this));
                        lfb_initItemContent($(this));
                        lfb_initNewItemContent($(this));

                    });
                }


            }
        });
    }

    function lfb_initRowMenu() {
        var menu = $('<div id="lfb_rowMenu" class="lfb_lPanel lfb_lPanelRight"></div>');
        menu.append('<div class="lfb_lPanelHeader"><span class="fas fa-pencil-alt"></span><span id="lfb_lPanelHeaderTitle">' + lfb_data.texts['Row settings'] + '</span>' +
                '<a href="javascript:" id="lfb_rowMenuCloseBtn" class="btn btn-default btn-circle btn-inverse"><span class="glyphicon glyphicon-remove"></span></a>' +
                '</div>'
                );
        menu.append(' <div class="lfb_lPanelBody"></div>');
        $('#lfb_bootstraped').append(menu);

        menu.find('.lfb_lPanelBody').append('<label>' + lfb_data.texts['Columns'] + '</label>');
        menu.find('.lfb_lPanelBody').append('<table class="table table-striped"></table>');
        menu.find('.table').append('<thead></thead>');
        menu.find('.table thead').append('<tr><th>' + lfb_data.texts['Size'] + '</th><th class="text-right"><a href="javascript:" data-action="addColumn" class="btn btn-primary btn-circle" title="' + lfb_data.texts['Add a column'] + '"><span class="fas fa-plus"></span></a></th></tr>');
        menu.find('.table').append('<tbody></tbody>');

        menu.find('#lfb_rowMenuCloseBtn').on('click', function () {
            menu.removeClass('lfb_open');
        });
        menu.find('a[data-action="addColumn"]').on('click', function () {
            var table = $(this).closest('table');

            var columnID = $(this).closest('tr[data-id]').attr('data-id');

            jQuery.ajax({
                url: lfb_data.ajaxurl,
                type: 'post',
                data: {
                    action: 'lfb_createRowColumn',
                    rowID: lfb_editedItem.attr('data-id')
                }, success: function (columnID) {

                    var column = {
                        size: 'medium',
                        id: columnID
                    };

                    addColumnRow(column);

                }
            });
        });

    }

    function addColumnRow(column) {
        var table = $('#lfb_rowMenu').find('table');

        var $tr = $('<tr data-id="' + column.id + '"></tr>');
        $tr.append('<td><select name="size" class="form-control form-control-sm"></select></td>');
        $tr.find('[name="size"]').append('<option value="auto">' + lfb_data.texts['Automatic'] + '</option>');
        $tr.find('[name="size"]').append('<option value="small">' + lfb_data.texts['Small'] + '</option>');
        $tr.find('[name="size"]').append('<option value="medium">' + lfb_data.texts['Medium'] + '</option>');
        $tr.find('[name="size"]').append('<option value="large">' + lfb_data.texts['Large'] + '</option>');
        $tr.find('[name="size"]').append('<option value="xl">' + lfb_data.texts['XL'] + '</option>');
        $tr.find('[name="size"]').append('<option value="fullWidth">' + lfb_data.texts['Full width'] + '</option>');
        $tr.append('<td class="text-right lfb_tdAction"></td>');
        $tr.find('.lfb_tdAction').append('<a href="javascript:" data-action="deleteColumn" class="btn btn-danger btn-circle"><span class="fas fa-trash"></span></a>');

        table.find('tbody').append($tr);
        lfb_updateRowColumns(lfb_editedItem);
        lfb_refreshItemDom(lfb_editedItem.attr('data-id'));
        $tr.find('[name="size"]').val(column.size);

        $tr.find('[name="size"]').on('change', function () {

            var size = $(this).val();
            lfb_updateRowColumns(lfb_editedItem);

            jQuery.ajax({
                url: lfb_data.ajaxurl,
                type: 'post',
                data: {
                    action: 'lfb_editRowColumn',
                    rowID: lfb_editedItem.attr('data-id'),
                    columnID: column.id,
                    size: size
                }, success: function () {
                    lfb_refreshItemDom(lfb_editedItem.attr('data-id'));
                }
            });

        });
        $tr.find('[data-action="deleteColumn"]').on('click', function () {
            var columnID = $(this).closest('tr[data-id]').attr('data-id');
            $(this).closest('tr[data-id]').remove();
            jQuery.ajax({
                url: lfb_data.ajaxurl,
                type: 'post',
                data: {
                    action: 'lfb_deleteRowColumn',
                    rowID: lfb_editedItem.attr('data-id'),
                    columnID: column.id
                }, success: function () {
                    lfb_updateRowColumns(lfb_editedItem);
                    lfb_refreshItemDom(lfb_editedItem.attr('data-id'));

                }
            });
        });
    }

    function lfb_updateRowColumns($row) {
        var columns = new Array();
        $('#lfb_rowMenu table tr[data-id]').each(function () {
            columns.push({
                id: $(this).attr('data-id'),
                size: $(this).find('[name="size"]').val()
            });
        });

        $row.attr('data-columns', JSON.stringify(columns));

    }
    function lfb_updateItemsSortOrder() {
        var itemsIDs = new Array();
        var indexes = new Array();
        var columnsIDs = new Array();
        $('#lfb_form .lfb_activeStep .lfb_item').each(function () {
            itemsIDs.push($(this).attr('data-id'));
            indexes.push($(this).index());
            var columnID = '';
            if ($(this).closest('.lfb_column').length > 0) {
                columnID = $(this).closest('.lfb_column').attr('data-columnid');
            }
            columnsIDs.push(columnID);
        });
        jQuery.ajax({
            url: lfb_data.ajaxurl,
            type: 'post',
            data: {
                action: 'lfb_itemsSort',
                stepID: lfb_currentStepID,
                itemsIDs: itemsIDs,
                indexes: indexes,
                columnsIDs: columnsIDs
            }
        });
    }

    function lfb_initComponentsMenu() {
        jQuery.ajax({
            url: lfb_data.ajaxurl,
            type: 'post',
            data: {
                action: 'lfb_getComponentMenu'
            },
            success: function (menu) {
                $('#lfb_bootstraped').prepend(menu);


                $('#lfb_componentsCloseBtn').on('click', function () {
                    $('#lfb_componentsPanel').removeClass('lfb_open');
                });

                $('#lfb_form').find('.lfb_genSlide .lfb_genContent > .lfb_row').sortable({
                    connectWith: '.lfb_sortable',
                    revert: 10,
                    delay: 200,
                    scroll: false,
                    items: '.lfb_item',
                    receive: function (e, ui) {
                        $('#lfb_rowMenu').removeClass('lfb_open');
                        if (ui.sender.closest('.lfb_lPanelBodyContent').length > 0) {
                            lfb_renderComponent(ui.item, '');
                        } else {
                            ui.item.removeClass('col-md-12');
                            ui.item.addClass('col-md-2');
                            setTimeout(function () {
                                lfb_updateItemsSortOrder();
                            }, 100);
                        }
                        lfb_copyHelper = null;
                    },
                    start: function (e, ui) {
                        $('#lfb_form').addClass('lfb_draggingComponent');
                        ui.helper.css('background-color', $('#lfb_mainPanel').css('background-color'));
                    },
                    over: function (event, ui) {

                    },
                    stop: function (event, ui) {
                        $('#lfb_form').removeClass('lfb_draggingComponent');
                        ui.item.removeClass('col-md-12');
                        ui.item.addClass('col-md-2');
                        setTimeout(function () {
                            lfb_updateItemsSortOrder();
                        }, 100);

                    },
                    update: function (event, ui) {

                    }
                }).disableSelection();

                $('#lfb_componentsPanel .lfb_lPanelBodyContent').addClass('lfb_sortable').sortable({
                    connectWith: '.lfb_sortable',
                    revert: 10,
                    delay: 200,
                    scroll: false,
                    appendTo: $('#lfb_bootstraped'),
                    items: '.lfb_item',
                    forcePlaceholderSize: false,
                    helper: function (e, li) {
                        lfb_copyHelper = li.clone().insertAfter(li);
                        return li.clone();
                    },
                    start: function (e, ui) {
                        lfb_isDraggingComponent = true;
                        ui.helper.css('background-color', $('#lfb_mainPanel').css('background-color'));
                        $('.lfb_tmpComponent').remove();
                        var tmpElement = $('<div class="lfb_tmpComponent"></div>');
                    },
                    over: function (event, ui) {},
                    stop: function (event, ui) {
                        lfb_copyHelper && lfb_copyHelper.remove();
                        lfb_isDraggingComponent = false;
                        $('.lfb_tmpComponent').remove();
                        if (ui.item.closest('#lfb_componentsPanel').length > 0) {
                            return false;
                        }
                    },
                    update: function (event, ui) {

                    }
                }).disableSelection();

                $('#lfb_componentsPanel').find('[data-toggle="switch"]').wrap('<div class="switch"  data-on-label="<i class=\'fui-check\'></i>" data-off-label="<i class=\'fui-cross\'></i>" />').parent().bootstrapSwitch();

                $('#lfb_componentsPanel').find('[data-type="slider"]').slider({
                    min: 0,
                    max: 100,
                    value: 50,
                    step: 1,
                    orientation: "horizontal",
                    range: "min"
                });

                $('#lfb_componentsPanel').find('.lfb_rate').rate({
                    initial_value: 3
                }).css({
                    color: '#bdc3c7'
                });


                $('#lfb_componentsPanel #lfb_componentsFilters input.form-control').on('keyup change', function () {
                    var start = $(this).val().toLowerCase();
                    $('#lfb_componentsPanel .lfb_componentModel').each(function () {
                        if ($(this).find('.lfb_componentTitle').text().toLowerCase().indexOf(start) > -1 || start.trim().length == 0) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                });

                lfb_initRowMenu();

            }
        });

    }
    function lfb_editRow($item) {
        $('#lfb_rowMenu').addClass('lfb_open');

        $('#lfb_rowMenu table tbody').html('');
        var columns = JSON.parse($item.attr('data-columns'));
        for (var i = 0; i < columns.length; i++) {
            var column = columns[i];
            addColumnRow(column);

        }
    }
    function lfb_initNewItemContent($item) {

        $item.find('[data-toggle="switch"][data-checkboxstyle="switch"]').each(function () {
            if ($(this).closest('.switch').length == 0) {
                $(this).wrap('<div class="switch"  data-on-label="<i class=\'fui-check\'></i>" data-off-label="<i class=\'fui-cross\'></i>" />').parent().bootstrapSwitch();
            }
        });
        $item.find('.lfb_colorpicker').each(function () {
            var $this = $(this);
            $(this).prev('.lfb_colorPreview').on('click', function () {
                if (!lfb_tld_selectionMode) {
                    $(this).next('.lfb_colorpicker').trigger('click');
                }
            });
            $(this).prev('.lfb_colorPreview').css({
                backgroundColor:  $('#lfb_form').data('lfb_form').colorA
            });
            $(this).colpick({
                color: $('#lfb_form').data('lfb_form').colorA,
                layout: 'hex',
                onSubmit: function () {
                    $('body > .colpick').fadeOut();
                },
                onChange: function (hsb, hex, rgb, el, bySetColor) {
                    $(el).val('#' + hex);
                    $(el).prev('.lfb_colorPreview').css({
                        backgroundColor: '#' + hex
                    });
                }
            });
        });

        if ($item.is('.lfb_gmap')) {
            if ($item.find('.gm-style').length == 0) {
                $item.css({
                    backgroundImage: 'url(' + lfb_data.assets_url + '/img/mapBg.jpg)',
                    height: $item.attr('data-height')
                });
            }
        }

        $item.find('img[data-tint="true"]').each(function () {
            $(this).css('opacity', 0);
            $(this).show();
            var $canvas = $('<canvas class="img"></canvas>');
            $canvas.css({
                width: $(this).get(0).width,
                height: $(this).get(0).height
            });
            $(this).hide();
            $(this).after($canvas);
            var ctx = $canvas.get(0).getContext('2d');
            var img = new Image();
            img.onload = function () {
                ctx.fillStyle =  $('#lfb_form').data('lfb_form').colorA;
                ctx.fillRect(0, 0, $canvas.get(0).width, $canvas.get(0).height);
                ctx.fill();
                ctx.globalCompositeOperation = 'destination-in';
                ctx.drawImage(img, 0, 0, $canvas.get(0).width, $canvas.get(0).height);
            };
            if ($(this).is('[data-lazy-src]')) {
                img.src = $(this).attr('data-lazy-src');
            } else {
                img.src = $(this).attr('src');
            }
        });

        $item.find('[data-type="slider"]:not(.ui-slider)').each(function () {

            var min = parseInt($(this).attr('data-min'));
            if (isNaN(min)) {
                min = 0;
            }
            var max = parseInt($(this).attr('data-max'));
            if (max == 0) {
                max = 30;
            }
            $(this).slider({
                min: min,
                max: max,
                value: 0,
                step: 1,
                orientation: "horizontal",
                range: "min"
            });
        });


        $item.find('.lfb_rate').each(function () {
            if ($(this).children().length == 0) {
                var max = parseInt($(this).closest('.lfb_itemBloc').attr('data-max'));
                var initialValue = parseInt($(this).closest('.lfb_itemBloc').attr('data-value'));
                if (isNaN(initialValue)) {
                    initialValue = 5;
                }
                var color = '#bdc3c7';
                if ($(this).closest('.lfb_itemBloc').attr('data-color') != '') {
                    color = $(this).closest('.lfb_itemBloc').attr('data-color');
                }
                if (color.indexOf('#') == -1) {
                    color = '#' + color;
                }
                var stepSize = $(this).closest('.lfb_itemBloc').attr('data-interval');
                $(this).rate({
                    initial_value: initialValue,
                    max_value: max,
                    step_size: 1
                }).css('color', color);
            }
        });
        setTimeout(function(){
            
        $item.find('canvas.img').each(function () {

            jQuery(this).parent().children('img').css('opacity', 0);
            jQuery(this).parent().children('img').show();
            jQuery(this).css({
                width: jQuery(this).parent().children('img').get(0).width,
                height: jQuery(this).parent().children('img').get(0).height
            });
            jQuery(this).parent().children('img').hide();

        });
        },200);

    }

    function lfb_initItemContent($item) {


        $item.find('.lfb_column').sortable({
            connectWith: '.lfb_sortable',
            revert: 10,
            delay: 200,
            scroll: false,
            items: '.lfb_item',
            start: function (e, ui) {
                $('#lfb_form').addClass('lfb_draggingComponent');
                ui.helper.css('background-color', $('#lfb_mainPanel').css('background-color'));
            },
            over: function (event, ui) {},
            receive: function (e, ui) {
                lfb_copyHelper = null;
                $('#lfb_rowMenu').removeClass('lfb_open');
                if (ui.sender.closest('.lfb_lPanelBodyContent').length > 0) {

                    lfb_renderComponent(ui.item, $(this).attr('data-columnid'));
                } else {
                    ui.item.removeClass('col-md-2');
                    setTimeout(function () {
                        lfb_updateItemsSortOrder();
                    }, 100);
                }
            },
            stop: function (event, ui) {

                if ($(this).is('.lfb_column')) {
                    ui.item.removeClass('col-md-2');
                } else {
                    ui.item.removeClass('col-md-12');
                    ui.item.addClass('col-md-2');
                }
                $('#lfb_form').removeClass('lfb_draggingComponent');
            },
            update: function (event, ui) {
            }
        }).disableSelection();
    }
    function lfb_initItemToolbar($item) {
        var tb = $('<div class="lfb_elementToolbar"></div>');
        tb.append('<a href="javascript:" class="btn-primary lfb-handler"><span class="fas fa-arrows-alt" data-tooltip="true"  data-placement="bottom" title="' + lfb_data.texts['move'] + '"></span></a>');
        tb.append('<a href="javascript:" data-action="edit" class="btn-default"><span class="fas fa-pencil-alt" data-tooltip="true"  data-placement="bottom" title="' + lfb_data.texts['edit'] + '"></span></a>');
        tb.append('<a href="javascript:" data-action="duplicate" class="btn-default"><span class="fas fa-copy" data-tooltip="true"  data-placement="bottom" title="' + lfb_data.texts['duplicate'] + '"></span></a>');
        tb.append('<a href="javascript:" data-action="style" class="btn-default"><span class="fas fa-magic" data-tooltip="true"  data-placement="bottom" title="' + lfb_data.texts['style'] + '"></span></a>');

        tb.append('<a href="javascript:" data-action="delete" class="btn-danger"><span class="fas fa-trash" data-tooltip="true"  data-placement="bottom" title="' + lfb_data.texts['remove'] + '"></span></a>');
        tb.find('[data-action="edit"]').on('click', function () {
            lfb_editedItem = $(this).closest('.lfb_item');
            if ($(this).closest('.lfb_item').attr('data-itemtype') == 'row') {
                lfb_editRow($item);
            } else {
               
                window.parent.jQuery('#lfb_form').trigger('lfb_editItem', [$(this).closest('.lfb_item').attr('data-id')]);
            }
        });
        tb.find('[data-action="style"]').on('click', function () {
            var domElement = '.lfb_item[data-id="' + $item.attr('data-id') + '"]';
            var targetStep = lfb_currentStepID;
            if (targetStep == 0) {
                targetStep = 'final';
            }
            window.parent.jQuery('#lfb_form').trigger('lfb_openFormDesigner', [targetStep, domElement]);

        });
        tb.find('[data-action="duplicate"]').on('click', function () {
            window.parent.jQuery('#lfb_form').trigger('lfb_duplicateItem', [$(this).closest('.lfb_item').attr('data-id')]);
        });
        tb.find('[data-action="delete"]').on('click', function () {
            window.parent.jQuery('#lfb_form').trigger('lfb_askDeleteItem', [$(this).closest('.lfb_item').attr('data-id')]);

        });

        $item.prepend(tb);
        $item.on('mouseenter',function () {
            clearTimeout(lfb_elementHoverTimer);
            var chkChildrenhover = false;
            $(this).find('.lfb_item').each(function () {
                if ($(this).is(':hover')) {
                    chkChildrenhover = true;
                }
            });
            if ((lfb_isDraggingComponent && $(this).find('.lfb-column-inner.lfb_hoverEdit').length > 0) || (!lfb_isDraggingComponent && $(this).find('.lfb-column-inner:hover').length > 0)) {
                chkChildrenhover = true;
            }
            if (!chkChildrenhover) {
                $('.lfb_hoverEdit').removeClass('lfb_hoverEdit');
                $(this).addClass('lfb_hover');
                $(this).addClass('lfb_hoverEdit');
            } else {
                $(this).removeClass('lfb_hover');
                $(this).removeClass('lfb_hoverEdit');
            }
            var _self = $(this);
            $(this).parent().closest('.lfb_item ').removeClass('lfb_hover');
        }).on('mouseleave', function () {
            var _self = $(this);
            _self.removeClass('lfb_hover');
            _self.children('.lfb_hover').removeClass('lfb_hover');
            lfb_elementHoverTimer = setTimeout(function () {
                _self.removeClass('lfb_hoverEdit');
                _self.children('.lfb_hoverEdit').removeClass('lfb_hoverEdit');
            }, 500);
            if ($(this).closest('.lfb_item :hover').length > 0) {
                $(this).closest('.lfb_item :hover').trigger('mouseenter');
            }
        });
        $item.prepend('<div class="lfb_itemLoader"><div class="lfb_spinner" data-tldinit="true"><div class="double-bounce1" data-tldinit="true"></div><div class="double-bounce2" data-tldinit="true"></div></div></div>');
        $item.find('.lfb_itemLoader').show().fadeOut(500);

    }

    function lfb_onItemDeleted(event,itemID) {
        if ($('.lfb_item[data-id="' + itemID + '"]').length > 0) {
            var $item = $('.lfb_item[data-id="' + itemID + '"]');
            $item.remove();
            if (lfb_editedItem && lfb_editedItem.attr('data-id') == itemID) {
                $('#lfb_rowMenuCloseBtn').trigger('click');
            }
        }
    }

    function lfb_renderComponent($component, columnID) {
        var index = $component.index();
        $component.prepend('<div class="lfb_itemLoader"><div class="lfb_spinner" data-tldinit="true"><div class="double-bounce1" data-tldinit="true"></div><div class="double-bounce2" data-tldinit="true"></div></div></div>');
        $component.find('.lfb_itemLoader').show();

        var $content = $('<div class="lfb_elementContent"></div>');

        var type = $component.attr('data-component');
        var title = lfb_data.texts['Item'];
        if ($('#lfb_componentsPanel .lfb_componentModel[data-component="' + type + '"] .lfb_componentTitle').length > 0) {
            title = $('#lfb_componentsPanel .lfb_componentModel[data-component="' + type + '"] .lfb_componentTitle').html();
        }
        jQuery.ajax({
            url: lfb_data.ajaxurl,
            type: 'post',
            data: {
                action: 'lfb_createNewItem',
                formID: $('#lfb_form').attr('data-form'),
                stepID: lfb_currentStepID,
                title: title,
                type: type,
                columnID: columnID,
                index: index
            },
            success: function (rep) {
                rep = JSON.parse(rep);

                var itemData = rep.itemData;

                window.parent.jQuery('#lfb_form').trigger('lfb_newItemAdded', [itemData]);

                var $item = $(rep.itemDom);
                $component.after($item);
                lfb_initItemToolbar($item);
                lfb_initNewItemContent($item);
                lfb_initItemContent($item);
                $component.remove();

                lfb_updateItemsSortOrder();

            }
        });
    }

    function lfb_showComponentsMenu() {
        $('#lfb_componentsFilters input').val('').trigger('keyup');
        $('#lfb_componentsFilters input').focus();
        $('#lfb_componentsPanel').addClass('lfb_open');
    }
})(jQuery);