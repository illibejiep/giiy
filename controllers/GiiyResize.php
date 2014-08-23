<?php
Yii::app()->getModule('giiy');

class GiiyResizeController extends Controller
{
    /**
     * rote pattern example:
     * GiiyModule::$pixUrl.'/resize/<id1:\d+>/<id2:\d+>/<width:\d+>x<height:\d+>.<type:\w+>' => 'giiy/giiyResize/resize'
     *
     * @param $id1
     * @param $id2
     * @param $width
     * @param $height
     * @param $type
     * @throws CHttpException
     */
    public function actionResize($id1,$id2,$width,$height,$type) {
        $id = $id1*1000+$id2;
        /** @var GiiyPicture $picture */
        $picture = GiiyPicture::model()->findByPk($id);
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