<div class="fileUploadWidget">
    <? foreach ($value as $fileModel):?>
        <div style="width: 200px">
        <? if ($fileModel instanceof Video):?>
            <? $this->widget('Jwplayer',array(
                    'width'=>$fileModel->width,
                    'height'=>$fileModel->height,
                    'file'=>$fileModel->getUrl(),
                    'image'=>$fileModel->picture?$fileModel->picture->getUrl():'#',
                    'options'=>array(
//                        'controlbar'=>'bottom',
                        'file'=>$fileModel->getUrl(),
                        'image'=>$fileModel->picture?$fileModel->picture->getUrl():'#',
                    )
            ));?>
        <? elseif ($fileModel instanceof Picture):?>
            <img src="<?=$fileModel->getResizeUrl(180,120);?>">
        <? elseif (isset($fileModel->picture)):?>
            <img src="<?=$fileModel->picture?$fileModel->picture->getResizeUrl(180,120):'';?>">
        <? endif; ?>
        <input type="file" name="<?=get_class($fileModel);?>" data-url="/<?=get_class($fileModel);?>/upload/<?=$fileModel->id;?>">
        <div id="progress" class="progress progress-striped <?=$fileModel?'progress-success':'';?>"><div class="bar" style="width: <?=$fileModel?'100':'0';?>%;"></div></div>
            </div>
    <? endforeach;?>
</div>

<script>
    $(function(){
        $('.fileUploadWidget input[name=<?=get_class($fileModel);?>]').fileupload({
            dataType: 'json',
            resizeSourceFileTypes: '*',
            done: function (e, data) {
                if (data.result.error !== undefined) {
                    $('#progress').addClass('progress-danger').removeClass('active');
                    $('.bar').text(data.result.error);
                } else {
                    $('#progress').addClass('progress-success').removeClass('active');
                    document.location = '/<?=get_class($fileModel);?>/update/' + data.result.id;
                }
            },
            progressall: function (e, data) {
                $('#progress').addClass('active').removeClass('progress-danger').removeClass('progress-success');
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#progress .bar').css(
                    'width',
                    progress + '%'
                ).text('');
            }
        });
    });
</script>
