<?php

/**
 * GiixModelCode class file.
 *
 * @author Rodrigo Coelho <rodrigo@giix.org>
 * @link http://giix.org/
 * @copyright Copyright &copy; 2010-2011 Rodrigo Coelho
 * @license http://giix.org/license/ New BSD License
 */
Yii::import('system.gii.generators.model.ModelCode');
Yii::import('ext.giiy.helpers.*');

/**
 * GiixModelCode is the model for giix model generator.
 *
 * @author Rodrigo Coelho <rodrigo@giix.org>
 * @package giix.generators.giixModel
 */
class GiiyModelCode extends ModelCode {

	/**
	 * @var string The (base) model base class name.
	 */
	public $baseClass = 'CActiveRecord';
	/**
	 * @var string The path of the base model.
	 */
	public $baseModelPath;
	/**
	 * @var string The base model class name.
	 */
	public $baseModelClass;

    /** @var array */
    public $tables = array();

    public function rules()
    {
        return array(
            array('template', 'required'),
            array('template', 'validateTemplate', 'skipOnError'=>true),
            array('template', 'sticky'),
            array('tables','type','type'=>'array'),
            array('connectionId, tablePrefix, modelPath, baseClass, buildRelations, tables', 'sticky'),
        );
    }
	/**
	 * Prepares the code files to be generated.
	 * #MethodTracker
	 * This method is based on {@link ModelCode::prepare}, from version 1.1.7 (r3135). Changes:
	 * <ul>
	 * <li>Generates the base model.</li>
	 * <li>Provides the representing column for the table.</li>
	 * <li>Provides the pivot class names for MANY_MANY relations.</li>
	 * </ul>
	 */
	public function prepare() {
        $schema = '';
        $tables = Yii::app()->db->schema->getTables($schema);
        foreach ($tables as $tableName=>$table)
            if (!in_array($tableName,$this->tables))
                unset($tables[$tableName]);
        $this->files = array();
        $templatePath = $this->templatePath;

        $this->relations = $this->generateRelations();

        ksort($tables);
        foreach ($tables as $table) {
            /** @var CDbTableSchema $table */
            $tableName = $this->removePrefix($table->name);
            $className = $this->generateClassName($table->name);

            // Generate the pivot model data.
            $pivotModels = array();
            if (isset($this->relations[$className])) {
                foreach ($this->relations[$className] as $relationName => $relationData) {
                    if (preg_match('/^array\(self::MANY_MANY,.*?,\s*\'(.+?)\(/', $relationData, $matches)) {
                        // Clean the table name if needed.
                        $pivotTableName = str_replace(array('{', '}'), '', $matches[1]);
                        $pivotModels[$relationName] = $this->generateClassName($pivotTableName);
                    }
                }
            }
            if (isset($this->relations[$className]))
                ksort($this->relations[$className]);
            $params = array(
                'tableName' => $schema === '' ? $tableName : $schema . '.' . $tableName,
                'modelClass' => $className,
                'columns' => $table->columns,
                'labels' => $this->generateLabelsEx($table, $className),
                'rules' => $this->generateRules($table),
                'relations' => isset($this->relations[$className]) ? $this->relations[$className] : array(),
                'representingColumn' => $this->getRepresentingColumn($table), // The representing column for the table.
                'pivotModels' => $pivotModels, // The pivot models.
            );
            // Setup base model information.
            $this->baseModelPath = $this->modelPath . '._base';
            $this->baseModelClass = 'Base' . $className;
            // Generate the model.
            if (!file_exists(Yii::getPathOfAlias($this->modelPath . '.' . $className) . '.php')) {
                $this->files[] = new CCodeFile(
                                Yii::getPathOfAlias($this->modelPath . '.' . $className) . '.php',
                                $this->render($templatePath . DIRECTORY_SEPARATOR . 'model.php', $params)
                );
            }
            // Generate the base model.
            $this->files[] = new CCodeFile(
                            Yii::getPathOfAlias($this->baseModelPath . '.' . $this->baseModelClass) . '.php',
                            $this->render($templatePath . DIRECTORY_SEPARATOR . '_base' . DIRECTORY_SEPARATOR . 'basemodel.php', $params)
            );

            foreach ($table->columns as $column) {
                if (substr($column->name,-5) == '_enum') {

                    $enumName = $this->generateClassName($column->name);
                    $enumFile = Yii::getPathOfAlias($this->modelPath . '.enum.' . $className . $enumName ). '.php';

                    if (file_exists($enumFile))
                        continue;

                    $params['enumName'] = $enumName;
                    $this->files[] = new CCodeFile(
                        $enumFile,
                        $this->render($templatePath . DIRECTORY_SEPARATOR . 'enum.php', $params)
                    );
                }
            }
        }
	}

	/**
	 * Lists the template files.
	 * #MethodTracker
	 * This method is based on {@link ModelCode::requiredTemplates}, from version 1.1.7 (r3135). Changes:
	 * <ul>
	 * <li>Includes the base model.</li>
	 * </ul>
	 * @return array A list of required template filenames.
	 */
	public function requiredTemplates() {
		return array(
			'model.php',
			'_base' . DIRECTORY_SEPARATOR . 'basemodel.php',
		);
	}

	/**
	 * Generates the labels for the table fields and relations.
	 * By default, the labels for the FK fields and for the relations is null. This
	 * will cause them to be represented by the related model label.
	 * #MethodTracker
	 * This method is based on {@link ModelCode::generateLabels}, from version 1.1.7 (r3135). Changes:
	 * <ul>
	 * <li>Default label for FKs is null.</li>
	 * <li>Creates entries for the relations. The default label is null.</li>
	 * </ul>
	 * @param CDbTableSchema $table The table definition.
	 * @param string $className The model class name.
	 * @return array The labels.
	 * @see CActiveRecord::label
	 * @see CActiveRecord::getRelationLabel
	 */
	public function generateLabelsEx($table, $className) {
		$labels = array();
		// For the fields.
		foreach ($table->columns as $column) {
			if ($column->isForeignKey) {
				$label = null;
			} else {
				$label = ucwords(trim(strtolower(str_replace(array('-', '_'), ' ', preg_replace('/(?<![A-Z])[A-Z]/', ' \0', $column->name)))));
				$label = preg_replace('/\s+/', ' ', $label);

				if (strcasecmp(substr($label, -3), ' id') === 0)
					$label = substr($label, 0, -3);
				if ($label === 'Id')
					$label = 'ID';

				$label = "Yii::t('app', '{$label}')";
			}
			$labels[$column->name] = $label;
		}
		// For the relations.
		$relations = $this->getRelationsData($className);
		if (isset($relations)) {
			foreach (array_keys($relations) as $relationName) {
				$labels[$relationName] = null;
			}
		}

		return $labels;
	}

	/**
	 * Generates the rules for table fields.
	 * #MethodTracker
	 * This method overrides {@link ModelCode::generateRules}, from version 1.1.7 (r3135). Changes:
	 * <ul>
	 * <li>Adds the rule to fill empty attributes with null.</li>
	 * </ul>
	 * @param CDbTableSchema $table The table definition.
	 * @return array The rules for the table.
	 */
	public function generateRules($table) {
		$rules = array();
		$null = array();
		foreach ($table->columns as $column) {
			if ($column->autoIncrement)
				continue;
			if (!(!$column->allowNull && $column->defaultValue === null))
				$null[] = $column->name;
		}
		if ($null !== array())
			$rules[] = "array('" . implode(', ', $null) . "', 'default', 'setOnEmpty' => true, 'value' => null)";

		return array_merge(parent::generateRules($table), $rules);
	}

	/**
	 * Selects the representing column of the table.
	 * The "representingColumn" method is the responsible for the
	 * string representation of the model instance.
	 * @param CDbTableSchema $table The table definition.
	 * @return string|array The name of the column as a string or the names of the columns as an array.
	 * @see CActiveRecord::representingColumn
	 * @see CActiveRecord::__toString
	 */
	protected function getRepresentingColumn($table) {
		$columns = $table->columns;
		// If this is not a MANY_MANY pivot table
		if (!$this->isRelationTable($table)) {
			// First we look for a string, not null, not pk, not fk column, not original number on db.
			foreach ($columns as $name => $column) {
				if ($column->type === 'string' && !$column->allowNull && !$column->isPrimaryKey && !$column->isForeignKey && stripos($column->dbType, 'int') === false)
					return $name;
			}
			// Then a string, not null, not fk column, not original number on db.
			foreach ($columns as $name => $column) {
				if ($column->type === 'string' && !$column->allowNull && !$column->isForeignKey && stripos($column->dbType, 'int') === false)
					return $name;
			}
			// Then the first string column, not original number on db.
			foreach ($columns as $name => $column) {
				if ($column->type === 'string' && stripos($column->dbType, 'int') === false)
					return $name;
			}
		} // If the appropriate column was not found or if this is a MANY_MANY pivot table.
		// Then the pk column(s).
		$pk = $table->primaryKey;
		if ($pk !== null) {
			if (is_array($pk))
				return $pk;
			else
				return (string) $pk;
		}
		// Then the first column.
		return reset($columns)->name;
	}

	/**
	 * Finds the related class of the specified column.
	 * @param string $className The model class name.
	 * @param CDbColumnSchema $column The column.
	 * @return string The related class name. Or null if no matching relation was found.
	 */
	public function findRelatedClass($className, $column) {
		if (!$column->isForeignKey)
			return null;

		$relations = $this->getRelationsData($className);

		foreach ($relations as $relation) {
			// Must be BELONGS_TO.
			if (($relation[0] === CActiveRecord::BELONGS_TO) && ($relation[3] === $column->name))
				return $relation[1];
		}
		// None found.
		return null;
	}

	/**
	 * Finds the relation data for all the relations of the specified model class.
	 * @param string $className The model class name.
	 * @return array An array of arrays with the relation data.
	 * The array will have one array for each relation.
	 * The key is the relation name. There are 5 values:
	 * 0: the relation type,
	 * 1: the related active record class name,
	 * 2: the joining (pivot) table (note: it may come with curly braces) (if the relation is a MANY_MANY, else null),
	 * 3: the local FK (if the relation is a BELONGS_TO or a MANY_MANY, else null),
	 * 4: the remote FK (if the relation is a HAS_ONE, a HAS_MANY or a MANY_MANY, else null).
	 * Or null if the model has no relations.
	 */
	public function getRelationsData($className) {
		if (!empty($this->relations))
			$relations = $this->relations;
		else
			$relations = $this->generateRelations();

		if (!isset($relations[$className]))
			return null;

		$result = array();
		foreach ($relations[$className] as $relationName => $relationData) {
			$result[$relationName] = $this->getRelationData($className, $relationName, $relations);
		}
		return $result;
	}

	/**
	 * Finds the relation data of the specified relation name.
	 * @param string $className The model class name.
	 * @param string $relationName The relation name.
	 * @param array $relations An array of relations for the models
	 * in the format returned by {@link ModelCode::generateRelations}. Optional.
	 * @return array The relation data. The array will have 3 values:
	 * 0: the relation type,
	 * 1: the related active record class name,
	 * 2: the joining (pivot) table (note: it may come with curly braces) (if the relation is a MANY_MANY, else null),
	 * 3: the local FK (if the relation is a BELONGS_TO or a MANY_MANY, else null),
	 * 4: the remote FK (if the relation is a HAS_ONE, a HAS_MANY or a MANY_MANY, else null).
	 * Or null if no matching relation was found.
	 */
	public function getRelationData($className, $relationName, $relations = array()) {
		if (empty($relations)) {
			if (!empty($this->relations))
				$relations = $this->relations;
			else
				$relations = $this->generateRelations();
		}

		if (isset($relations[$className]) && isset($relations[$className][$relationName]))
			$relation = $relations[$className][$relationName];
		else
			return null;

		$relationData = array();
		if (preg_match("/^array\(([\w:]+?),\s?'(\w+)',\s?'([\w\s\(\),]+?)'\)$/", $relation, $matches_base)) {
			$relationData[1] = $matches_base[2]; // the related active record class name

			switch ($matches_base[1]) {
				case 'self::BELONGS_TO':
					$relationData[0] = CActiveRecord::BELONGS_TO; // the relation type
					$relationData[2] = null;
					$relationData[3] = $matches_base[3]; // the local FK
					$relationData[4] = null;
					break;
				case 'self::HAS_ONE':
					$relationData[0] = CActiveRecord::HAS_ONE; // the relation type
					$relationData[2] = null;
					$relationData[3] = null;
					$relationData[4] = $matches_base[3]; // the remote FK
					break;
				case 'self::HAS_MANY':
					$relationData[0] = CActiveRecord::HAS_MANY; // the relation type
					$relationData[2] = null;
					$relationData[3] = null;
					$relationData[4] = $matches_base[3]; // the remote FK
					break;
				case 'self::MANY_MANY':
					if (preg_match("/^((?:{{)?\w+(?:}})?)\((\w+),\s?(\w+)\)$/", $matches_base[3], $matches_manymany)) {
						$relationData[0] = CActiveRecord::MANY_MANY; // the relation type
						$relationData[2] = $matches_manymany[1]; // the joining (pivot) table
						$relationData[3] = $matches_manymany[2]; // the local FK
						$relationData[4] = $matches_manymany[3]; // the remote FK
					}
					break;
			}

			return $relationData;
		} else
			return null;
	}

	/**
	 * Returns the message to be displayed when the newly generated code is saved successfully.
	 * #MethodTracker
	 * This method overrides {@link CCodeModel::successMessage}, from version 1.1.7 (r3135). Changes:
	 * <ul>
	 * <li>Custom giix success message.</li>
	 * </ul>
	 * @return string The message to be displayed when the newly generated code is saved successfully.
	 */
	public function successMessage() {
		return <<<EOM
<p><strong>Sweet!</strong></p>
<ul style="list-style-type: none; padding-left: 0;">
	<li><img src="http://giix.org/icons/love.png"> Show how you love giix on <a href="http://www.yiiframework.com/forum/index.php?/topic/13154-giix-%E2%80%94-gii-extended/">the forum</a> and on its <a href="http://www.yiiframework.com/extension/giix">extension page</a></li>
	<li><img src="http://giix.org/icons/vote.png"> Upvote <a href="http://www.yiiframework.com/extension/giix">giix</a></li>
	<li><img src="http://giix.org/icons/powered.png"> Show everybody that you are using giix in <a href="http://www.yiiframework.com/forum/index.php?/topic/19226-powered-by-giix/">Powered by giix</a></li>
	<li><img src="http://giix.org/icons/donate.png"> <a href="http://giix.org/">Donate</a></li>
</ul>
<p style="margin: 2px 0; position: relative; text-align: right; top: -15px; color: #668866;">icons by <a href="http://www.famfamfam.com/lab/icons/silk/" style="color: #668866;">famfamfam.com</a></p>
EOM;
	}

    /**
     * Checks if the given table is a "many to many" pivot table.
     * Their PK has 2 fields, and both of those fields are also FK to other separate tables.
     * @param CDbTableSchema table to inspect
     * @var CDbTableSchema $table
     * @return boolean true if table matches description of helpter table.
     */
    protected function isRelationTable($table)
    {
        $pk=$table->primaryKey;
        return (count($pk) === 2 // we want 2 columns
            && isset($table->foreignKeys[$pk[0]]) // pk column 1 is also a foreign key
            && isset($table->foreignKeys[$pk[1]]));
    }

    protected function generateRelations()
    {
        if(!$this->buildRelations)
            return array();
        $relations=array();
        /** @var CDbTableSchema[] $tables */
        $tables = Yii::app()->{$this->connectionId}->schema->getTables();

        foreach($tables as $table)
        {
            if($this->tablePrefix!='' && strpos($table->name,$this->tablePrefix)!==0)
                continue;
            $tableName=$table->name;

            if ($this->isRelationTable($table))
            {
                $pks=$table->primaryKey;
                $fks=$table->foreignKeys;

                $table0=$fks[$pks[0]][0];
                $table1=$fks[$pks[1]][0];
                if ($table0 == $table1) {
                    $className=$this->generateClassName($table0);
                    $unprefixedTableName=$this->removePrefix($tableName);

                    $relationName1=$this->generateRelationName($table0, $pks[1], true);
                    $relationName2=$this->generateRelationName($table1, $pks[0], true);

                    $relations[$className][$relationName1]="array(self::MANY_MANY, '$className', '$unprefixedTableName($pks[1], $pks[0])')";
                    $relations[$className][$relationName2]="array(self::MANY_MANY, '$className', '$unprefixedTableName($pks[0], $pks[1])')";
                } else {
                    $className0=$this->generateClassName($table0);
                    $className1=$this->generateClassName($table1);

                    $unprefixedTableName=$this->removePrefix($tableName);

                    $relationName=$this->generateRelationName($table0, $pks[1], true);
                    $relations[$className0][$relationName]="array(self::MANY_MANY, '$className1', '$unprefixedTableName($pks[0], $pks[1])')";

                    $relationName=$this->generateRelationName($table1, $pks[0], true);

                    $i=1;
                    $rawName=$relationName;
                    while(isset($relations[$className1][$relationName]))
                        $relationName=$rawName.$i++;

                    $relations[$className1][$relationName]="array(self::MANY_MANY, '$className0', '$unprefixedTableName($pks[1], $pks[0])')";
                }
            }
            else
            {
                $className=$this->generateClassName($tableName);
                foreach ($table->foreignKeys as $fkName => $fkEntry)
                {
                    // Put table and key name in variables for easier reading
                    $refTable=$fkEntry[0]; // Table name that current fk references to
                    $refKey=$fkEntry[1];   // Key in that table being referenced
                    $refClassName=$this->generateClassName($refTable);

                    // Add relation for this table
                    $relationName=$this->generateRelationName($tableName, $fkName, false);
                    $relations[$className][$relationName]="array(self::BELONGS_TO, '$refClassName', '$fkName')";

                    // Add relation for the referenced table
                    $isUnique = Yii::app()->db->getPdoInstance()->query('
                        SELECT count(*)
                        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE t1
                        LEFT JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE t2
                        ON t1.CONSTRAINT_NAME = t2.CONSTRAINT_NAME
                        WHERE
                            t2.TABLE_NAME = \''.$table->name.'\'
                            AND t2.COLUMN_NAME = \'' . $fkName . '\'
                            AND t2.POSITION_IN_UNIQUE_CONSTRAINT IS NULL
                    ')->fetchColumn();

                    $relationType = $isUnique == 1 ? 'HAS_ONE' : 'HAS_MANY';
                    $relationName=$this->generateRelationName($refTable, $this->removePrefix($tableName,false), $relationType==='HAS_MANY');
                    if (strpos($fkName,'_'.$refTable.'_'.$refKey) !== false)
                        $relationName .= ucfirst(str_replace('_'.$refTable.'_'.$refKey,'',$fkName));
                    $i=1;
                    $rawName=$relationName;
                    while(isset($relations[$refClassName][$relationName]))
                        $relationName=$rawName.($i++);
                    $relations[$refClassName][$relationName]="array(self::$relationType, '$className', '$fkName')";
                }
            }
        }
        return $relations;
    }


}