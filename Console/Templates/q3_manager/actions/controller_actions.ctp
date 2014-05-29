<?php
/**
 * Bake Template for Controller action generation.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Console.Templates.default.actions
 * @since         CakePHP(tm) v 1.3
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<?php $plugin_domain=Inflector::underscore($plugin); ?>
/**
 * <?php echo $admin ?>index method
 *
 * @return void
 */
	public function <?php echo $admin ?>index() {
		//$this-><?php echo $currentModelName ?>->recursive = 0;
		/**
		 * These are ignored here
		 */
		unset($this->params->data['Selected']); //select items
		unset($this->params->data['<?php echo $currentModelName;?>']['main']); //select all items

		$conditions = $this->_parseSearch();
		$this->set('<?php echo $pluralName ?>', $this->paginate($conditions));
    <?php
    if($modelObj){
      $schema=$modelObj->schema(true);
      $fields=array_keys($schema);
    }
    else{
      $fields=$schema=$associations=array();
      echo "//no model object\n";
    }
    $references_list=array();
    $models_list=array();
    foreach($fields as $field):
        $field_name=preg_replace('/_id$/','',$field);
        if($field=='status'){
          $references_list[]="'status_list'";
          echo "\t\t\$status_list=\$this->{$currentModelName}->getStatusList();\n";
        }
        elseif($field_name!=$field){
          $field_list=Inflector::pluralize($field_name);
          $field_model=Inflector::camelize($field_name);


          if($field_model<>$currentModelName){
               echo "\t\t\$${field_list}=ClassRegistry::init('$field_model')->find('list');\n";
               $models_list[]="'${field_model}'";
               $references_list[]="'${field_list}'";
          }
          else{
               echo "\t\t\$parent_${field_list}=ClassRegistry::init('$field_model')->find('list');\n";
               $models_list[]="'Parent{$field_model}'=>array('className'=>'{$field_model}','foreignKey'=>'{$field}')";
               $references_list[]="'parent_${field_list}'";
          }

        }
        //else{
        //  echo "//normal $field";
        //}
    endforeach;
    if(!empty($references_list)){
      echo "\t\t\$this->set(compact(".implode(',',$references_list)."));\n";
    }
    if(!empty($models_list)){
      echo "\t\t\$this->{$currentModelName}->bindModel(array('belongsTo'=>array(".implode(',',$models_list).")),true);\n";
    }
    ?>

    $this->set('<?php echo $pluralName ?>', $this->paginate($conditions));
		$this->_setSelects();
	}

/**
 * <?php echo $admin ?>view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function <?php echo $admin ?>view($id = null) {
		if (!$this-><?php echo $currentModelName; ?>->exists($id)) {
			throw new NotFoundException(__d('{$plugin_domain}','Invalid <?php echo strtolower($singularHumanName); ?>'));
		}
		$options = array('conditions' => array('<?php echo $currentModelName; ?>.' . $this-><?php echo $currentModelName; ?>->primaryKey => $id));
		$this->set('<?php echo $singularName; ?>', $this-><?php echo $currentModelName; ?>->find('first', $options));
	}

<?php $compact = array(); ?>
/**
 * <?php echo $admin ?>add method
 *
 * @return void
 */
	public function <?php echo $admin ?>add() {
		if ($this->request->is('post')) {
			if(!empty($this->request->data['<?php echo $currentModelName;?>']['return_url'])){
				$return_url=$this->request->data['<?php echo $currentModelName;?>']['return_url'];
			}
			else{
				$return_url=array('action'=>'index');
			}
			$this-><?php echo $currentModelName; ?>->create();
			if ($this-><?php echo $currentModelName; ?>->save($this->request->data)) {
<?php if ($wannaUseSession): ?>
				$this->Session->setFlash(__d('{$plugin_domain}','The <?php echo strtolower($singularHumanName); ?> has been saved'));
				$this->redirect($return_url);
<?php else: ?>
				$this->flash(__d('{$plugin_domain}','<?php echo ucfirst(strtolower($currentModelName)); ?> saved.'), $return_url);
<?php endif; ?>
			} else {
<?php if ($wannaUseSession): ?>
				$this->Session->setFlash(__d('{$plugin_domain}','The <?php echo strtolower($singularHumanName); ?> could not be saved. Please, try again.'));
<?php endif; ?>
			}
		}
<?php
	foreach (array('belongsTo', 'hasAndBelongsToMany') as $assoc):
		foreach ($modelObj->{$assoc} as $associationName => $relation):
			if (!empty($associationName)):
				$otherModelName = $this->_modelName($associationName);
				$otherPluralName = $this->_pluralName($associationName);
				echo "\t\t\${$otherPluralName} = \$this->{$currentModelName}->{$otherModelName}->find('list');\n";
				$compact[] = "'{$otherPluralName}'";
			endif;
		endforeach;
	endforeach;
	if (!empty($compact)):
		echo "\t\t\$this->set(compact(".join(', ', $compact)."));\n";
	endif;

  if($modelObj){
    $schema=$modelObj->schema(true);
    $fields=array_keys($schema);
  }
  else{
    $fields=$schema=$associations=array();
    echo "//no model object\n";
  }
  $references_list=array();
  foreach($fields as $field):
      $field_name=preg_replace('/_id$/','',$field);
      if($field=='status'){
        echo "//status $field\n";
      }
      elseif($field_name!=$field){
        $field_list=Inflector::pluralize($field_name);
        $field_model=Inflector::camelize($field_name);
        if(!in_array("'$field_list'",$compact)){
          echo "\t\t\$$field_list=ClassRegistry::init('$field_model')->find('list');\n";
          $references_list[]="'$field_list'";
        }
      }
      //else{
      //  echo "//normal $field";
      //}
  endforeach;
  if(!empty($references_list)){
    echo "\t\t\$this->set(compact(".implode(',',$references_list)."));\n";
  }
?>
    $this->set('return_url',$this->referer());
		$this->render('<?php echo $admin ?>edit');
	}

<?php $compact = array(); ?>
/**
 * <?php echo $admin ?>edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function <?php echo $admin; ?>edit($id = null) {
		if (!$this-><?php echo $currentModelName; ?>->exists($id)) {
			throw new NotFoundException(__d('{$plugin_domain}','Invalid <?php echo strtolower($singularHumanName); ?>'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if(!empty($this->request->data['<?php echo $currentModelName;?>']['return_url'])){
				$return_url=$this->request->data['<?php echo $currentModelName;?>']['return_url'];
			}
			else{
				$return_url=array('action'=>'index');
			}
			if ($this-><?php echo $currentModelName; ?>->save($this->request->data)) {
<?php if ($wannaUseSession): ?>
				$this->Session->setFlash(__d('{$plugin_domain}','The <?php echo strtolower($singularHumanName); ?> has been saved'));
				$this->redirect($return_url);
<?php else: ?>
				$this->flash(__d('{$plugin_domain}','The <?php echo strtolower($singularHumanName); ?> has been saved.'), $return_url);
<?php endif; ?>
			} else {
<?php if ($wannaUseSession): ?>
				$this->Session->setFlash(__d('{$plugin_domain}','The <?php echo strtolower($singularHumanName); ?> could not be saved. Please, try again.'));
<?php endif; ?>
			}
		} else {
			$options = array('conditions' => array('<?php echo $currentModelName; ?>.' . $this-><?php echo $currentModelName; ?>->primaryKey => $id));
			$this->request->data = $this-><?php echo $currentModelName; ?>->find('first', $options);
		}
<?php
		foreach (array('belongsTo', 'hasAndBelongsToMany') as $assoc):
			foreach ($modelObj->{$assoc} as $associationName => $relation):
				if (!empty($associationName)):
					$otherModelName = $this->_modelName($associationName);
					$otherPluralName = $this->_pluralName($associationName);
					echo "\t\t\${$otherPluralName} = \$this->{$currentModelName}->{$otherModelName}->find('list');\n";
					$compact[] = "'{$otherPluralName}'";
				endif;
			endforeach;
		endforeach;
		if (!empty($compact)):
			echo "\t\t\$this->set(compact(".join(', ', $compact)."));\n";
		endif;

    if($modelObj){
      $schema=$modelObj->schema(true);
      $fields=array_keys($schema);
    }
    else{
      $fields=$schema=$associations=array();
      echo "//no model object\n";
    }
    $references_list=array();
    $models_list=array();
    foreach($fields as $field):
        $field_name=preg_replace('/_id$/','',$field);
        if($field=='status'){
          echo "//status $field\n";
        }
        elseif($field_name!=$field){
          $field_list=Inflector::pluralize($field_name);
          $field_model=Inflector::camelize($field_name);
          if(!in_array("'$field_list'",$compact)){
            echo "\t\t\$$field_list=ClassRegistry::init('$field_model')->find('list');\n";
            $references_list[]="'$field_list'";
          }
        }
        //else{
        //  echo "//normal $field";
        //}
    endforeach;
    if(!empty($references_list)){
      echo "\t\t\$this->set(compact(".implode(',',$references_list)."));\n";
    }


	?>
    $this->set('return_url',$this->referer());
	}

/**
 * <?php echo $admin ?>delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @param boolean $confirm
 * @return void
 */
	public function <?php echo $admin; ?>delete($id = null, $confirm = false) {
		if (!empty($id)) {
			$ids = array($id);
		} else {
			$ids = array();
		}

		if(!empty($this->request->data['Selected'])) {
			foreach ($this->request->data['Selected'] as $selected_id => $selected) {
				if ($selected) {
					$ids[] = $selected_id;
				}
			}
		}

		if (!empty($ids) && $confirm) {
			$error = false;
			foreach ($ids as $id) {
				$this-><?php echo $currentModelName; ?>->id = $id;
				if (!$this-><?php echo $currentModelName; ?>->delete()) {
					$error = true;
				}
				if ($error) {
<?php if ($wannaUseSession): ?>
					$this->Session->setFlash(__d('{$plugin_domain}','Operation error'));
<?php else: ?>
					$this->flash(__d('{$plugin_domain}','Operation error'), array('action' => 'index'));
<?php endif; ?>
				} else {
<?php if ($wannaUseSession): ?>
					$this->Session->setFlash(__d('{$plugin_domain}','<?php echo ucfirst(strtolower($pluralHumanName)); ?> deleted'));
<?php else: ?>
					$url = array('action' => 'index');
					$this->flash(__d('{$plugin_domain}','<?php echo ucfirst(strtolower($pluralHumanName)); ?> deleted'), $url);
<?php endif; ?>
				}
			}
			$this->redirect(array('action' => 'index'));
		} elseif (empty($ids)) {
			throw new NotFoundException(__d('{$plugin_domain}','<?php echo ucfirst(strtolower($pluralHumanName)); ?> not found'));
			$this->redirect(array('action' => 'index'));
		}
		$<?php echo $pluralName; ?> = $this-><?php echo $currentModelName; ?>->find('all', array(
			'conditions' => array('<?php echo $currentModelName; ?>.<?php echo $primaryKey; ?>' => $ids)
		));
		$this->set(compact('<?php echo $pluralName; ?>'));

	}

<?php if (ClassRegistry::init($currentModelName)->hasField('status')): ?>
/**
 * <?php echo $admin ?>set_status method
 *
 * @throws NotFoundExceptiona
 * @throws MethodNotAllowedException
 * @param int $status
 * @param string $id
 * @return void
 */

	public function <?php echo $admin; ?>set_status($status = 0, $id = null) {
		if (!empty($id)) {
			$ids = array($id);
		} else {
			$ids = array();
		}

		if(!empty($this->request->data['Selected'])) {
			foreach ($this->request->data['Selected'] as $selected_id => $selected) {
				if ($selected) {
					$ids[] = $selected_id;
				}
			}
		}

		if (!empty($ids)) {
			$error = false;
			foreach ($ids as $id) {
				$this-><?php echo $currentModelName; ?>->id = $id;
				if (!$this-><?php echo $currentModelName; ?>->save(compact('status'))) {
					$error = true;
				}
				if ($error) {
<?php if ($wannaUseSession): ?>
					$this->Session->setFlash(__d('{$plugin_domain}','Operation error'));
<?php else: ?>
					$this->flash(__d('{$plugin_domain}','Operation error'), array('action' => 'index'));
<?php endif; ?>
				} else {
<?php if ($wannaUseSession): ?>
					($status)?$this->Session->setFlash(__d('{$plugin_domain}','<?php echo ucfirst(strtolower($pluralHumanName)); ?> enabled')):$this->Session->setFlash(__d('{$plugin_domain}','<?php echo ucfirst(strtolower($pluralHumanName)); ?> disabled'));
<?php else: ?>
					$url = array('action' => 'index');
					($status)?$this->flash(__d('{$plugin_domain}','<?php echo ucfirst(strtolower($pluralHumanName)); ?> enabled'), $url):$this->flash(__d('{$plugin_domain}','<?php echo ucfirst(strtolower($pluralHumanName)); ?> disabled'), $url);
<?php endif; ?>
				}
			}
		} else {
			throw new NotFoundException(__d('{$plugin_domain}','<?php echo ucfirst(strtolower($pluralHumanName)); ?> not found'));
		}
		$this->redirect(array('action' => 'index'));
	}

<?php endif; //hasField status ?>
