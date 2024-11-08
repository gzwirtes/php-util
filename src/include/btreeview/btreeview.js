function btreeview_start(id, expand)
{
    if (expand) {
        $(`#${id} .btreeview-group-name, #${id} .btreeview-group-icon`).on('click', function(ele) {
            $(this).closest('.btreeview-group').toggleClass('btreeview-open');
        });
    }
}

function btreeview_toggle_all(event, element) {
    event.stopPropagation();
    $(element).closest('.btreeview-group').find('.btreeview-group-items').find('[type=checkbox]').prop('checked', $(element).is(':checked'));
}


function btreeview_add_option(form_name, field, group, key, value) {
    var group = $('form[name='+form_name+'] [btreeview='+field+'] .btreeview-group[id="'+group+'"]');
    var item = group.find('.btreeview-group-items .btreeview-item:first').clone();
    var oldId = item.attr('data-id');
    console.log(item, group, oldId);
    item.find('.btreeview-checkbox-item').attr('value', key);
    item.find('span').html(value);
    item.attr('data-id', key);

    group.find('.btreeview-group-items').append(item);
}

function btreeview_reload(form_name, field, content) {
    $('form[name='+form_name+'] [btreeview='+field+']').replaceWith(base64_decode(content));
}