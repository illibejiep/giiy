<?

class GiiyCRUDController extends Controller
{
    /**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
        $model = $this->loadModel($id);
        // параметр 'page' отправляет костыльно-ориентированный CGridView
        if (Yii::app()->getRequest()->isAjaxRequest && $model && !isset($_GET['page'])) {
            echo json_encode($model);
            Yii::app()->end();
        }

		$this->render('view',array(
			'model'=>$model,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model = $this->loadModel();

		if(isset($_POST[get_class($model)]))
		{
            $model = $this->_prepareModel($model);
            if (Yii::app()->getRequest()->isAjaxRequest) {
                if($model->save())
                    echo json_encode($model);
                else
                    echo json_encode(array('modelName' => get_class($model),'errors' => $model->getErrors()));

                Yii::app()->end();
            }

			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST[get_class($model)]))
		{
            $model = $this->_prepareModel($model);
            if (Yii::app()->getRequest()->isAjaxRequest) {
                if($model->save())
                    echo json_encode($model);
                else
                    echo json_encode(array('modelName' => get_class($model),'errors' => $model->getErrors()));

                Yii::app()->end();
            }

            if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
        $modelClassName = $this->_getModelClassName();

        $q = Yii::app()->getRequest()->getPost('q');
        if (Yii::app()->getRequest()->isAjaxRequest && $q) {

            $criteria = array(
                'condition' => "t.name LIKE :query",
                'order' => 'name',
                'limit' => 15,
                'params' => array('query' => "%$q%"),
            );

            if (ctype_digit($q)) {
                $criteria['condition'] .= ' OR t.id = :id';
                $criteria['order'] = 't.id';
                $criteria['params']['id'] = $q;
            }

            $list = GiiyActiveRecord::model($modelClassName)->findAll($criteria);

            echo json_encode($list);
            Yii::app()->end();
        }

        $this->actionAdmin();
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
        $modelClassName = $this->_getModelClassName();
        $model=new $modelClassName('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET[$modelClassName]))
            $model->attributes=$_GET[$modelClassName];
        $this->render('admin',array(
            'model' => $model,
			'dataProvider' => $this->getDataProvider($model),
		));
	}

    public function actionUpload($id = null)
    {
        try {
            $model = $this->loadModel($id);
            // Если модель описывает файл
            if (!($model instanceof IFileBased))
                throw new CException('wrong action');

            $file = CUploadedFile::getInstanceByName(get_class($model));

            if ($file->error) {
                // ошибки для виджета ModelFileUpload
                switch ($file->error) {
                    case UPLOAD_ERR_INI_SIZE:
                        $errmsg = 'UPLOAD_ERR_INI_SIZE';break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $errmsg = 'UPLOAD_ERR_FORM_SIZE';break;
                    case UPLOAD_ERR_PARTIAL:
                        $errmsg = 'UPLOAD_ERR_PARTIAL';break;
                    case UPLOAD_ERR_NO_FILE:
                        $errmsg = 'UPLOAD_ERR_NO_FILE';break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        $errmsg = 'UPLOAD_ERR_NO_TMP_DIR';break;
                    case UPLOAD_ERR_CANT_WRITE:
                        $errmsg = 'UPLOAD_ERR_CANT_WRITE';break;
                    case UPLOAD_ERR_EXTENSION:
                        $errmsg = 'UPLOAD_ERR_EXTENSION';break;
                    default:
                        $errmsg = 'WTF?';
                }
                throw new CException($errmsg,$file->error);
            }

            if (!$file)
                throw new СExeption('upload error');

            if ($model->hasAttribute('name'))
                $model->name = $file->name;

            if (Yii::app()->request->isAjaxRequest) {
                $model->setFile($file->getTempName());
                if($model->save()) {
                    echo json_encode($model);
                    Yii::app()->end();
                } else {
                    echo json_encode(array('modelName' => get_class($model),'errors' => $model->getErrors()));
                    Yii::app()->end();
                }
            } elseif (!$model->save()) {
                throw new CException('upload error: ' . (YII_DEBUG?print_r($model->getErrors(),true):''));
            }
        } catch (Exception $e) {
            if (Yii::app()->request->isAjaxRequest) {
                $errors = array(
                    'code'=>$e->getCode(),
                    'error'=>$e->getMessage(),
                );
                if (YII_DEBUG) {
                    $errors['trace'] = $e->getTrace();
                    $errors['file'] = $e->getFile();
                    $errors['line'] = $e->getLine();
                }
                echo json_encode($errors);
                Yii::app()->end();
            } else
                throw $e;
        }


        $this->redirect(array('view','id'=>$model->id));
    }

    /**
     * @param $id
     * @return GiiyActiveRecord
     * @throws CHttpException
     */
    public function loadModel($id = null)
	{
        $modelClassName = $this->_getModelClassName();
        if (!$id)
            return new $modelClassName;

        $model= GiiyActiveRecord::model($modelClassName)->findByPk($id);
        if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');

        return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
        $modelClassName = $this->_getModelClassName();
		if(isset($_POST['ajax']) && $_POST['ajax']===strtolower($modelClassName).'-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

    protected function _getModelClassName()
    {
        return substr(get_class($this),0,-10);
    }

    protected function _prepareModel(GiiyActiveRecord $model)
    {
        $values = $_POST[get_class($model)];

        foreach($values as $name => $value) {
            if (
                isset($model->relations()[$name])
                && ($model->relations()[$name][0] == CActiveRecord::HAS_MANY
                    || $model->relations()[$name][0] == CActiveRecord::MANY_MANY
                )
            ) {
                $ids = $value?$value:array();
                $pks = is_array($ids)?$ids:explode(',',$ids);
                if (!is_array(GiiyActiveRecord::model($model->relations()[$name][1])->getTableSchema()->primaryKey)) {

                    $models = array();
                    foreach (GiiyActiveRecord::model($model->relations()[$name][1])->findAllByPk($pks) as $res)
                        $models[$res->getPrimaryKey()] = $res;

                    $sortedModels = array();
                    foreach ($pks as $pk)
                        $sortedModels[] = $models[$pk];

                    $model->$name = $sortedModels;

                } else
                    $model->$name = $pks;
            } elseif (isset($model->relations()[$name])) {
                $relModel = CActiveRecord::model($model->relations()[$name][1])
                    ->findByPk($value);
                $model->$name = $relModel?$relModel:null;
            } elseif ($model->hasAttribute($name) || isset($model->$name) || method_exists($model,'set'.ucfirst($name))) {
                $model->$name = $value;
            }
        }
        return $model;
    }

    public function actionForm($id = null,$fromModel = null)
    {
        $this->layout = '//blank';
        $model = $this->loadModel($id);

        if(isset($_POST[get_class($model)]))
        {
            $model = $this->_prepareModel($model);
            if (Yii::app()->getRequest()->isAjaxRequest) {
                if($model->save())
                    echo json_encode($model);
                else
                    echo json_encode(array('modelName' => get_class($model),'errors' => $model->getErrors()));

                Yii::app()->end();
            }

            if($model->save())
                $this->redirect(array('view','id'=>$model->id,'fromModel' => $fromModel));
        }

        $this->render('form',array(
            'model'=>$model,
            'fromModel' => $fromModel
        ));

    }

    /**
     * @param GiiyActiveRecord $model
     * @return CActiveDataProvider
     */
    public function getDataProvider(GiiyActiveRecord $model)
    {
        /** @var CActiveDataProvider $dataProvider */
        $dataProvider = $model->search();
        $dataProvider->getPagination()->pageSize = 15;

        return $dataProvider;
    }

}