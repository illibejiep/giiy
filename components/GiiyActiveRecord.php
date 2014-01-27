<?php
abstract class GiiyActiveRecord extends CActiveRecord implements JsonSerializable
{
    public function behaviors()
    {
        return array(
            'activerecord-relation'=>array(
                'class'=>'ext.behaviors.activerecord-relation.EActiveRecordRelationBehavior',
            )
        );
    }

    public function init()
    {
        if($this->hasAttribute('created') && $this->isNewRecord) {
            $this->created = date('Y-m-d H:i:s');
            if($this->hasAttribute('modified'))
                $this->modified = date('Y-m-d H:i:s');
        }
    }

    // -----------------------cache functions -------------------------------
    public function __get($name)
    {
        $getter = 'get'.ucfirst($name);
        if (method_exists($this,$getter))
            return $this->$getter();
        else
            return parent::__get($name);
    }

    public function __set($name, $value)
    {
        $setter = 'set'.ucfirst($name);
        if (method_exists($this,$setter))
            return $this->$setter($value);
        else
            parent::__set($name, $value);
        return $this;
    }

    protected function beforeSave()
    {
        if($this->hasAttribute('modified'))
            $this->modified = date('Y-m-d H:i:s');

        return parent::beforeSave();
    }

    // ------------------- helpers ----------------------------
    
    public function getIdentifier()
    {
        if (is_array($this->primaryKey))
            return join('-',$this->primaryKey);

        return $this->primaryKey;
    }
    /**
     * @param string $relationName
     * @param bool $asTable
     * @return array
     */
    public function getRelationNames($relationName,$asTable = false) {

        if (!in_array($relationName,array_keys($this->relations())))
            return array();

        $relations = $this->getRelated($relationName);
        if (!is_array($relations))
            $relations = array($relations);

        $names = array();
        foreach ($relations as $model) {
            if (!($model instanceof GiiyActiveRecord))
                continue;
            /** @var $model GiiyActiveRecord */
            if ($asTable) {
                $names[] = $model->toArray();
            } else
                $names[$model->getIdentifier()] = $model->toArray()['_viewName'];
        }

        return $names;
    }

    /**
     * @param $relationName string
     * @return array
     * @throws CException
     */
    public function getRelationIds($relationName) {
        $ids = array();
        if (!in_array($relationName,array_keys($this->relations())))
            throw new CException('whrong relation name');

        $relation = $this->getRelated($relationName);

        if ($relation === null)
            return array();

        if (!is_array($relation))
            $relation = array($relation);

        foreach ($relation as $model)
            if ($model instanceof GiiyActiveRecord)
                $ids[] = $model->getIdentifier();
            elseif (is_numeric($model))
                $ids[] = $model;

        return $ids;
    }


    public function toArray()
    {
        if ($this->hasAttribute('name'))
            $name = $this->getAttribute('name');
        elseif (method_exists($this,'__toString'))
            $name = $this;

        if (!isset($name) || !strlen($name))
            $name = '['.$this->getIdentifier().']';

        $data = $this->attributes;
        $data['_modelName'] = get_class($this);
        $data['_viewName'] = $name;
        if (isset($data['parent_id'])) {
            $current = $this;
            $parent = $current->getRelated('parent');
            while ($parent) {
                if ($parent->hasAttribute('name'))
                    $parentName = $parent->getAttribute('name');
                elseif (method_exists($parent,'__toString'))
                    $parentName = $parent;
                else
                    $parentName = '['.$parent->getIdentifier().']';
                $data['_viewName'] = $parentName .' > '. $data['_viewName'];
                $current = $parent;
                $parent = $current->getRelated('parent');
            }
        }

        if ($this instanceof ITypeEnumerable)
            $data['_viewName'] .= ' ['.(string)$this->getType().']';

        if(method_exists($this, 'getUrl'))
            $data['_url'] = $this->getUrl();
        elseif ($this->hasAttribute('url'))
            $data['_url'] = $this->getAttribute('url');

        if ($this instanceof Iillustrated)
            $data['_picture'] = $this->getPicture()?$this->getPicture()->resize(180,120):'';

        return $data;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

}
