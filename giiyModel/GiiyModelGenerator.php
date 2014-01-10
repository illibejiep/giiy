<?php

/**
 * GiixModelGenerator class file.
 *
 * @author Rodrigo Coelho <rodrigo@giix.org>
 * @link http://giix.org/
 * @copyright Copyright &copy; 2010-2011 Rodrigo Coelho
 * @license http://giix.org/license/ New BSD License
 */

/**
 * GiixModelGenerator is the controller for giix model generator.
 *
 * @author Rodrigo Coelho <rodrigo@giix.org>
 * @package giix.generators.giixModel
 */
class GiiyModelGenerator extends CCodeGenerator {

	public $codeModel = 'ext.giiy.giiyModel.GiiyModelCode';

	/**
	 * Returns the table names in an array.
	 * The array is used to build the autocomplete field.
	 * An '*' is appended to the end of the list to allow the generation
	 * of models for all tables.
	 * @return array The names of all tables in the schema, plus an '*'.
	 */
	protected function getTables() {
		$tables = array();
        foreach (Yii::app()->db->schema->tableNames as $i => $tableName)
            if (strpos($tableName,'__to__') === false
                && strpos($tableName,'_archive') === false
                && !in_array($tableName,array(
                    'import',
                    'auth_assignment',
                    'auth_item',
                    'auth_item_child',
                    'rights',
                    'tbl_migration',
                )))
                $tables[] = $tableName;
        sort($tables);
		return $tables;
	}

}