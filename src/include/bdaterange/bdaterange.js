function bdaterange_start(id, options)
{
    let start = document.getElementById(options.id_start);
    let end = document.getElementById(options.id_end);

    $(start).data('range_type', 'normal');

    let pickerOptions = {
        element: start,
        css: ['lib/independent/css/easepick.css'],
        header: options.title,
        zIndex: options.zIndex,
        grid: options.grid,
        calendars: options.calendars,
        readonly: false,
        autoApply: options.autoApply,
        plugins: ['RangePlugin', 'LockPlugin'],
        lang: options.language,
        format: options.format,
        locale: {
            cancel: options.language == 'pt' ? "Cancelar" : 'Cancel',
            apply: options.language == 'pt' ? "Aplicar" : 'Apply'
        },
        setup(picker) {
            picker.on('select', (e) => {
                if (options.changeaction) {
                    eval(options.changeaction);
                }
            });
        },
        RangePlugin: {
            delimiter: options.separator,
            tooltipNumber: (num) => num - 1,
            locale: options.locale ?? {
              one: options.language == 'pt' ? 'Dia' : 'Day',
              other: options.language == 'pt' ? 'Dias' : 'Days',
            },
          }
    };

    if (options.time) {
        pickerOptions.plugins.push('TimePlugin');
        pickerOptions.TimePlugin = {
            seconds: options.seconds,
            format: 'HH:mm:ss',
            stepSeconds: options.stepSeconds,
            stepHours: options.stepHours,
            stepMinutes: options.stepMinutes
        };
    }

    if (options.id_end) {
        $(start).data('range_type', 'start');
        $(end).data('range_type', 'end');
        pickerOptions.RangePlugin.elementEnd = end;
    }

    if (options.enableDates || options.disableDates ) {
        pickerOptions.LockPlugin ={
        minDate: new Date(),
        minDays: 1,
        inseparable: true,
            filter(date) {
                if (options.enableDates) {
                    return !options.enableDates.includes(date.format('YYYY-MM-DD'));
                } else if (options.disableDates) {
                    return options.disableDates.includes(date.format('YYYY-MM-DD'));
                }
            }
        }
    }

    const picker = new easepick.create(pickerOptions);

    $(start).on('change', function() {
        if (! this.value)
        {
            let endVal = $(end).val();
            picker.clear();

            if (end && endVal)
            {
                picker.setEndDate(endVal);
            }
        }
        else
        {
            picker.setStartDate(this.value);
        }

        if (options.changeaction)
        {
            eval(options.changeaction);
        }
    });

    $(end).on('change', function() {
        if (! this.value)
        {
            let startVal = $(start).val();
            picker.clear();

            if (startVal)
            {
                picker.setStartDate(startVal);
            }
        }
        else
        {
            picker.setEndDate(this.value);
        }

        if (options.changeaction)
        {
            eval(options.changeaction);
        }
    });

    $('#'+id).data('picker', picker);
}

function bdaterange_disable_field(form_name, id) {
    let field = $('form[name='+form_name+'] [id='+id+']');

    if (field.length == 0) {
        field = $('form[name='+form_name+'] [name='+id+']').closest('.bdaterange-container');
    }

    field.find('.bdaterange-icon').hide();
    field.find('input').addClass('tfield_disabled');
}

function bdaterange_enable_field(form_name, id) {
    let field = $('form[name='+form_name+'] [id='+id+']');

    if (field.length == 0) {
        field = $('form[name='+form_name+'] [name='+id+']').closest('.bdaterange-container');
    }

    field.find('.bdaterange-icon').show();
    field.find('input').removeClass('tfield_disabled');
}

function bdaterange_set_value(id, value)
{
    if ($(id).data('range_type') == 'normal') {
        $(id).closest('.bdaterange-container').data('picker').setDateRange(value);
    } else if ($(id).data('range_type') == 'end') {
        $(id).closest('.bdaterange-container').data('picker').setEndDate(value);
    } else if ($(id).data('range_type') == 'start') {
        $(id).closest('.bdaterange-container').data('picker').setStartDate(value);
    }

    $(id).val(value);
}