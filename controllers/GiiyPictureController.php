<?
Yii::app()->getModule('giiy');

class GiiyPictureController extends GiiyCRUDController
{

    public function actionCropResize()
    {
        $id = $_POST['id'];
        $x = $_POST['cropImage_x'];
        $x2 = $_POST['cropImage_x2'];
        $y = $_POST['cropImage_y'];
        $y2 = $_POST['cropImage_y2'];
        $h = $_POST['cropImage_h'];
        $w = $_POST['cropImage_w'];

        $picture = $this->loadModel($id);

        if (!$picture OR !$picture->cropResize($x,$x2,$y,$y2,$w,$h)) {
            echo 'error';
            Yii::app()->end();
        }

        echo 'done';
    }

    public function actionView($id)
    {
        $model = $this->loadModel($id);
        if (Yii::app()->getRequest()->isAjaxRequest && $model) {
            echo json_encode($model);
            Yii::app()->end();
        }

        $resolutions = array();
        $dir = $model->getResizeDir();
        if (is_dir($dir))
            foreach (scandir($dir) as $file) {
                if (strpos($file,'.'.$model->type)) {
                    $resolution= str_replace('.'.$model->type,'',$file);
                    $widthHeight = explode('x',$resolution);
                    if (count($widthHeight) != 2) continue;
                    $resolutions[] = array('width'=>$widthHeight[0],'height'=>$widthHeight[1]);
                }
            }

        $this->render('view',array(
            'model' => $model,
            'resolutions' => $resolutions,
        ));
    }

    public function actionUnlink($id,$width,$height)
    {
        $model = $this->loadModel($id);

        unlink($model->getResizePath($width,$height));

        $this->redirect(array('view','id' => $id));
    }

    /**
     * @param null $id
     * @return Picture
     */
    public function loadModel($id = null)
    {
        return parent::loadModel($id);
    }


    public function actionResize($id1,$id2,$width,$height,$type) {
        $id = $id1*1000+$id2;
        /** @var Picture $picture */
        $picture = Picture::model()->findByPk($id);
        if (!$picture)
            throw new CHttpException(404,'The requested page does not exist.');

        if ($picture->resize($width,$height) !== null) {
            header("Content-type: ".$picture->getType()->getMime());
            echo file_get_contents($picture->getResizePath($width,$height));
            exit();
        }

        throw new CHttpException(404,'The requested page does not exist.');
    }

}