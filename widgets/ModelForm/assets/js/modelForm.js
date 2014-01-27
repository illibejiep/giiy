var iframes;
var iframesCount;
$(function(){
    $("input[relationName]").each(function(){
        initRelation($(this));
    });
});

function initRelation(element) {
    if (element.attr("isMultiple") != '1') {
        element.change(function(){
            $(this).select2("data",[$(this).select2("data").pop()]);
        });
    }
}

function initSortable(element) {
    element.select2("container").find('ul.select2-choices').sortable({
        containment: 'parent',
        cancel: ".select2-search-field",
        start: function() { $(this).parent().select2("onSortStart"); },
        update: function() { $(this).parent().select2("onSortEnd"); }
        //handle: 'legend, table, div'
    });
}

function initSelection(element,callback) {
        var relationName = element.attr('relationName');
        initRelation(element);
        initSortable(element);
        var data = modelData[relationName];
        callback(data);
}

function formatSelectionForm(data,container) {
    container.append('<hr>');
    container.append('<iframe modelName="' + data._modelName + '" modelId="' + data.id + '" onload="initSubform($(this));" width="345" allowtransparency="true" style="background-color:transparent" src="/' + data._modelName + '/form/id/' + data.id + '/fromModel/' + modelData._modelName + '" ></iframe>');
    return '';
}
function initSubform(iframe)
{
    iframe.attr('height',iframe.contents().find('body > .container').height());
    var onchange = iframe.contents().find("select[name$=\\[type\\]]").attr('onchange');
    onchange += "$('iframe',top.document).each(function(){$(this).height($(this).contents().find('div.container').height())});";
    iframe.contents().find("select[name$=\\[type\\]]").attr('onchange',onchange);

    iframe.contents().find('button[type=submit]').click(function(){
        var reload = false;
        var form = $(this).closest('form');
        if (!form.find('span.results').length)
            form.find('button[type=submit]').after("<span class=\"results\">sending...</span>");
        $.ajax({
            type: "POST",
            async: false,
            url: iframe.attr("src"),
            data: form.serialize(),
            success: function(result){

                if (result.id > 0 && form.attr('isNew') != 1) {
                    form.find(".results").html("done");
                    form.find('.alert-error').remove();
                    form.find('label').removeClass('error');
                } else if (result.id > 0) {
                    form.find(".results").html("done");
                    var old_data = iframe.closest('.select2-container').select2("data");
                    old_data.pop();
                    old_data.push(result);
                    iframe.closest('.select2-container').select2("data",old_data);
                    form.find('.alert-error').remove();
                    form.find('label').removeClass('error');
                    if (iframesCount) multiSubmit();
                } else if (!!result.errors) {
                    form.find(".results").html("error");
                    form.find('.alert-error').remove();
                    form.find('label').removeClass('error');

                    var html = '<div class="alert alert-block alert-error"><ul>';
                    for (attr in result.errors) {
                        html += '<li>' + result.errors[attr] + '</li>';
                        form.find('label[for='+result.modelName + '_' + attr + ']').addClass('error');
                    }
                    html += '</ul></div>';
                    form.prepend(html);

                    $('html, body').animate({
                        scrollTop: iframe.offset().top-120
                    }, 1000);
                } else  {
                    $('html, body').animate({
                        scrollTop: iframe.offset().top-120
                    }, 1000);
                    reload = true;
                }
            },
            dataType: "json"
        });

        if (!reload)
            iframe.attr('height',iframe.contents().find('body > .container').height());

        return reload;
    });
}

function formatSelectionSubform(data,container) {
    var html = '<table><tr>';
    html += '<td><a target="_blank" href ="/' + data._modelName + '/update/' + data.id + '">' + data._viewName + '</a></td></tr></table>';
    container.append(html);
    return '';
}

function formatSelection(data,container) {

    if (data._picture) {
        container.css("width",630);
        container.css("height",130);
        var html = '<table><tr>';
        html += '<td width="180"><img src="' + data._picture + '"></td>';
    } else {
        var html = '<table style="margin-bottom: 0;"><tr>';
    }

    if (data._modelName == 'Block')
        container.css("width",630);

    html += '<td><a target="_blank" href ="/' + data._modelName + '/update/' + data.id + '">' + data._viewName + '</a></td></tr></table>';

    container.append(html);

    return '';
}

function formatSelectionUpload(data,container) {

    container.css("width",630);
    container.css("height",130);
    var html = '<table><tr>';
    html += '<td width="180"><img src="' + data._picture + '"></td>';

    html += '<td><a target="_blank" href ="/' + data._modelName + '/view/' + data.id + '">' + data._viewName + '</a><br>';
    html += '<input type="file" name="' + data._modelName + '" data-url="/' + data._modelName + '/upload/' + data.id + '">';
    if (data.id > 0)
        html += '<p class="progress progress-striped progress-success"><span class="bar" style="width: 100%;color: black">&nbsp;</span></p></td></tr></table>';
    else
        html += '<p class="progress progress-striped"><span class="bar" style="width: 0%;color: black">&nbsp;</span></p></td></tr></table>';

    container.append(html);

    container.find('input[type=file]').fileupload({
        dataType: 'json',
        done: function (e, data) {
            if (data.result.error !== undefined) {
                container.find('.progress').addClass('progress-danger');
                container.find('.bar').text(data.result.error);
            } else {
                container.find('.progress').addClass('progress-success');
                var old_data = container.closest('.select2-container').select2("data");

                var rewrite = false;

                for($i = 0; $i < old_data.length ; $i++)
                    if(old_data[$i].id == data.result.id) {
                        rewrite = true;
                        old_data[$i] = data.result;
                    }

                if(!rewrite)
                {
                    old_data.shift();
                    old_data.unshift( data.result);
                }

                container.closest('.select2-container').select2("data",old_data);
            }
            container.find('.progress').removeClass('active');
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            container.find('.progress').addClass('active').removeClass('progress-danger').removeClass('progress-success');

            container.find('.bar').css(
                'width',
                progress + '%'
            ).text('');
        }
    });

    return '';
}

function formatResult(data) {
    var html = '<table><tr>';

    if (data._picture)
        html += '<table><tr><td style="height: 130px;width:190px;"><img src="' + data._picture + '"></td>';

    html += '<td>' + data._viewName + '</td></tr></table>';


    return html;
}

function addForm(select2container,modelName)
{
    if (select2container.next().attr("isMultiple") == '0')
        data = {id:0,_modelName: modelName};
    else {
        data = select2container.select2("data");
        data.unshift({id:0,_modelName: modelName});
    }

    select2container.select2("data",data);
    initSortable(select2container);
}

function modelFormSubmit() {
    iframes = $('iframe');
    iframesCount = iframes.length;

    multiSubmit();
    if (iframesCount > 0)
        return false;
}

function multiSubmi() {
    iframesCount--;
    var iframe = iframes.eq(iframesCount);
    subForm = iframe.contents().find('form');

    subForm.find(".form-actions").append('<span class="results">submiting...</span>');

    $.ajax({
        type: "POST",
        async: false,
        url: subForm.attr("action"),
        data: subForm.serialize(),
        success: function(result){
            if (result.id > 0 && subForm.attr('isNew') != 1) {
                subForm.find(".results").html("done");
                if (iframesCount) multiSubmit();
            } else if (result.id > 0) {
                var old_data = iframe.closest('.select2-container').select2("data");
                old_data.pop();
                old_data.push(result);
                iframe.closest('.select2-container').select2("data",old_data);
                subForm.find('.alert-error').remove();
                subForm.find('label').removeClass('error');
                if (iframesCount) multiSubmit();
            } else if (result.errors) {
                var html = '<div class="alert alert-block alert-error"><ul>';
                for (attr in result.errors) {
                    html += '<li>' + result.errors[attr] + '</li>';
                    subForm.find('.'+result.modelName + '_' + attr).addClass('error');
                }

                html += '</ul></div>';
                subForm.prepend(html);

                $('html, body').animate({
                    scrollTop: iframe.offset().top-120
                }, 1000);
            } else  {
                subForm.find('button[type=submit]').click();
                $('html, body').animate({
                    scrollTop: iframe.offset().top-120
                }, 1000);

            }
        },
        dataType: "json"
    });
}