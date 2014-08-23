<?
$this->breadcrumbs=array(
    'Pictures'=>array('index'),
    $model->name,
);

$this->menu=array(
    array('label'=>'List Picture','url'=>array('index')),
    array('label'=>'Create Picture','url'=>array('create')),
    array('label'=>'Update Picture','url'=>array('update','id'=>$model->id)),
    array('label'=>'Delete Picture','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
    array('label'=>'Manage Picture','url'=>array('admin')),
);
?>

    <h1>View Picture #<?=$model->id; ?></h1>

<? $this->widget('bootstrap.widgets.TbDetailView',array(
    'data'=>$model,
    'attributes'=>array(
        'type_enum',
        'name',
        'width',
        'height',
        'created',
        'modified',
        'description',
        'announce',
    ),
)); ?>

    <script>
        $(function(){
            $('#start_cropImage').hide();
            $('#start_cropImage').click(function(){
                $('.newResolution').show();
            });
        });
    </script>
    <div style="margin: 10px 0">
        <input style="width: 30px" name="resizeW" value="<?=$model->width;?>">x<input style="width: 30px" size=1 name="resizeH" value="<?=$model->height;?>">
        <a class="btn btn-inverse" href="javascript:$('#start_cropImage').click();$('#cropImage').Jcrop({aspectRatio :$('input[name=resizeW]').val()/$('input[name=resizeH]').val()});$('#cropImage_w').val($('input[name=resizeW]').val());$('#cropImage_h').val($('input[name=resizeH]').val());">crop&resize</a>
    </div>
    <a name="resizer"></a>
<? $this->widget('ext.jcrop.EJcrop', array(
    //
    // Image URL
    'url' => $model->getUrl(),
    //
    // ALT text for the image
    'alt' => 'Crop This Image',
    //
    // options for the IMG element
    'htmlOptions' => array('id' => 'cropImage'),
    //
    // Jcrop options (see Jcrop documentation)
    'options' => array(
        'minSize' => array(10, 10),
        'aspectRatio' => 0,
        'onRelease' => "js:function() {ejcrop_cancelCrop(this);}",
    ),
    // if this array is empty, buttons will not be added
    'buttons' => array(
        'start' => array(
            'label' => Yii::t('promoter', 'crop'),
            'htmlOptions' => array(
                'class' => 'myClass',
                'style' => 'color:red' // make sure style ends with « ; »
            )
        ),
        'crop' => array(
            'label' => 'crop',
            'htmlOptions' => array(
                'class' => 'btn btn-primary',
            ),
        ),
        'cancel' => array(
            'label' => 'cancel',
            'htmlOptions' => array(
                'class' => 'btn',
            ),
        ),
    ),
    // URL to send request to (unused if no buttons)
    'ajaxUrl' => '/giiy/giiyPicture/cropResize',
    //
    // Additional parameters to send to the AJAX call (unused if no buttons)
    'ajaxParams' => array(
        'id' => $model->id,
    ),
));?>
<? foreach ($resolutions as $resolution):?>

    <div class="resizeView">
        <hr>
        <h5>(<?=$resolution['width'].'x'.$resolution['height'];?>):</h5>
        <div>
            <img src="<?=$model->resize($resolution['width'],$resolution['height']);?>">
        </div>
        <div style="margin:10px 0">
            <a class="btn" href="#resizer" onclick="$('#start_cropImage').click();$('#cropImage').Jcrop({aspectRatio :<?=$resolution['height']?$resolution['width']/$resolution['height']:0;?>});$('#cropImage_w').val(<?=$resolution['width'];?>);$('#cropImage_h').val(<?=$resolution['height'];?>);">reCrop&reResize</a>
            <a href="/Picture/unlink/id/<?=$model->id;?>/width/<?=$resolution['width'];?>/height/<?=$resolution['height'];?>" class="btn btn-danger">delete</a>
        </div>
    </div>
<? endforeach;?>