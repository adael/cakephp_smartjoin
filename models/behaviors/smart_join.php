<?php

/**
 * Containable
 * @author Carlos Gant
 */
class SmartJoinBehavior extends ModelBehavior {

	/**
	 * Clave para el $queryData de beforeFind
	 * @var string
	 */
	private $__key = 'assoc';

	/**
	 *  Los joins asociados a cada modelo
	 * @var array
	 */
	private $__joins = array();

	/**
	 * Claves que tiene un join
	 * @var array
	 */
	private $__joinKeys = array(
		'table' => null,
		'alias' => null,
		'type' => null,
		'conditions' => null,
	);

	/**
	 * Configuración para cada join
	 * @var type
	 */
	private $__joinConfig = array(
		'type' => 'left',
		'fields' => null,
		'conditions' => null,
		'assocConditions' => null,
		'foreignKey' => null
	);

	/**
	 * Configuración por defecto
	 * @var type
	 */
	private $__defaultSettings = array(
		/**
		 * Tipos permitidos
		 */
		'findTypes' => array('all', 'first', 'count', 'list'),
	);

	function setup(&$model, $config = array()) {
		parent::setup($model, $config);
		$this->settings[$model->alias] = $config + $this->__defaultSettings;
	}

	/**
	 * Agrega contains a un modelo
	 * @param AppModel $model
	 * @param array $data
	 */
	function smartJoinAdd($model, $data) {
		if(!is_array($data)){
			$data = array($data);
		}
		if(!isset($this->__joins[$model->alias])){
			$this->__joins[$model->alias] = array();
		}
		foreach($data as $k => $v){
			if(is_numeric($k)){
				$k = $v;
				$v = array();
			}
			$this->__joins[$model->alias][$k] = $v;
		}
	}

	/**
	 * Establece los joins de un modelo
	 * @param AppModel $model
	 * @param array $data
	 */
	function smartJoinSet($model, $data) {
		if(!is_array($data)){
			$data = array($data);
		}
		$this->__joins[$model->alias] = array();
		foreach($data as $k => $v){
			if(is_numeric($k)){
				$k = $v;
				$v = array();
			}
			$this->__joins[$model->alias][$k] = $v;
		}
	}

	/**
	 * Devuelve los joins que tiene un modelo.
	 * @param AppModel $model
	 * @return array
	 */
	function smartJoinGet($model) {
		return $this->__joins[$model->alias];
	}

	/**
	 * Vacía los joins
	 * @param AppModel $model
	 * @param array $except
	 */
	function smartJoinClear($model, $except = null) {
		if($except === null){
			$this->__joins[$model->alias] = array();
		}else{
			if(!is_array($except)){
				$except = array($except);
			}
			foreach($this->__joins[$model->alias] as $k => $v){
				if(!in_array($k, $except)){
					unset($this->__joins[$model->alias][$k]);
				}
			}
		}
	}

	/**
	 *
	 * @param AppModel $model
	 * @param Aray $query
	 * @return array
	 */
	public function beforeFind($model, $query) {
		if(!in_array($model->findQueryType, $this->settings[$model->alias]['findTypes'])){
			return $query;
		}
		if(isset($query[$this->__key]) || !empty($this->__joins[$model->alias])){

			$model->recursive = -1;

			if(!isset($query[$this->__key])){
				$query[$this->__key] = array();
			}elseif(!is_array($query[$this->__key])){
				$query[$this->__key] = array($query[$this->__key]);
			}

			if(!empty($this->__joins[$model->alias])){
				$query[$this->__key] += $this->__joins[$model->alias];
			}

			$assoc = & $query[$this->__key];

			// Proceso los joins para que las claves sean todas los nombres de los modelos.
			foreach($assoc as $k => $v){
				if(is_numeric($k)){
					unset($assoc[$k]);
					if(!isset($assoc[$v])){
						$assoc[$v] = array();
					}
				}
			}

			if($model->findQueryType !== 'list'){
				if(empty($query['fields'])){
					$db = $model->getDataSource();
					$query['fields'] = $db->fields($model, $model->alias);
				}elseif(!is_array($query['fields'])){
					$query['fields'] = array($query['fields']);
				}
			}

			$query['findQueryType'] = $model->findQueryType;
			$this->__buildJoins($query, $model, $assoc, $model->alias);

			/**
			 * Si no hay conditions y el tipo de consulta es count
			 * todos los left joins los puedo quitar sin problemas.
			 */
			if($model->findQueryType === 'count' && empty($query['conditions'])){
				foreach($query['joins'] as $k => $v){
					if(strtolower($v['type']) === 'left'){
						unset($query['joins'][$k]);
					}
				}
			}
		}
		return $query;
	}

	/**
	 * Para cada contain tengo que:
	 * 1.- Hallar el tipo de relación (fácil)
	 * 2.- Según el tipo construir el join correspondiente
	 * 3.- Comprobar si hay subniveles y lo mismo
	 * @param array $query
	 * @param AppModel $model
	 * @param array $joins
	 */
	private function __buildJoins(&$query, $model, $joins, $parentAlias = '') {
		$associated = $model->getAssociated();
		foreach($joins as $joinAlias => $joinConfig){

			if(!isset($associated[$joinAlias])){
				trigger_error("El modelo $model->name no está asociado con $joinAlias");
				continue;
			}

			$type = $associated[$joinAlias];

			if($type !== 'hasOne' && $type !== 'belongsTo'){
				trigger_error("El modelo $model->name está asociado con $joinAlias pero no es hasOne o belongsTo");
				continue;
			}

			$assoc = $model->{$type}[$joinAlias];

//			if($assoc['className'] !== $model->name){
//				$assocModel = $model->{$assoc['className']};
//			}else{
//				$assocModel = new $model->name;
//			}
			$assocModel = $model->{$joinAlias};

			if(!$assocModel instanceof Model){
				trigger_error("El modelo $model->name no está asociado con {$assoc['className']}");
				continue;
			}

			$join = $this->__joinKeys;

			if(!empty($joinConfig)){
				$config = array_intersect_key($joinConfig, $this->__joinConfig);
				$models = array_diff_key($joinConfig, $this->__joinConfig);
				foreach($models as $k => $v){
					if(is_numeric($k)){
						unset($models[$k]);
						$models[$v] = array();
					}
				}
			}else{
				$config = array();
				$models = null;
			}

			$config += $this->__joinConfig;

			if(!empty($config['conditions'])){
				$join['conditions'] = $config['conditions'];
			}

			if($parentAlias){
				$alias = $parentAlias . '__' . $joinAlias;
			}else{
				$alias = $joinAlias;
			}

			$foreignKey = null;
			if($config['foreignKey'] === null){
				$foreignKey = $assoc['foreignKey'];
			}else{
				$foreignKey = $config['foreignKey'];
			}

			if($foreignKey){
				if($type === 'belongsTo'){
					$join['conditions'][] = "`{$parentAlias}`.`{$foreignKey}` = `{$alias}`.`{$assocModel->primaryKey}`";
				}elseif($type === 'hasOne'){
					$join['conditions'][] = "`{$parentAlias}`.`{$model->primaryKey}` = `{$alias}`.`{$foreignKey}`";
				}
			}

			if($assoc['conditions'] && $config['assocConditions'] !== false){
				$join['conditions'] = array_merge($join['conditions'], $assoc['conditions']);
			}

			if($config['conditions']){
				$join['conditions'] = array_merge($join['conditions'], $config['conditions']);
			}

			$join['conditions'] = array_unique($join['conditions']);

			// Agrego los campos si procede
			if($query['findQueryType'] !== 'count' && $query['findQueryType'] !== 'list'){
				$db = $model->getDataSource();
				if(!empty($config['fields'])){
					$query['fields'] = array_merge($query['fields'], $db->fields($assocModel, $alias, $config['fields']));
				}elseif($config['fields'] !== false){
					$query['fields'] = array_merge($query['fields'], $db->fields($assocModel, $alias));
				}
			}

			$search = "{{$assocModel->alias}}";

			foreach($query['fields'] as $k => $v){
				if(strpos($v, $search) !== false){
					$query['fields'][$k] = str_replace($search, $alias, $v);
				}
			}

			$join['table'] = $assocModel->table;
			$join['alias'] = $alias;
			$join['type'] = $config['type'];
			$query['joins'][] = $join;

			if($models){
				$this->__buildJoins($query, $assocModel, $models, $alias);
			}
		}
	}

}