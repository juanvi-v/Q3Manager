<?php
/**
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
 * @package       Cake.Console.Templates.default.views
 * @since         CakePHP(tm) v 1.2.0.5234
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<?php
  $plugin_domain=Inflector::underscore($plugin);
	$actionTitle = Inflector::humanize($action);
	switch ($actionTitle) {
		case 'Edit': $actionTitle = 'Editar'; break;
		case 'Add': $actionTitle = 'Nuevo'; break;
		default: break;
	}
?>
<div class="<?php echo $pluralVar; ?> form">
<?php
if (ClassRegistry::init($modelClass)->hasField('avatar')) {
	echo "<?php echo \$this->Form->create('{$modelClass}', array('type' => 'file')); ?>\n";
} else {
	echo "<?php echo \$this->Form->create('{$modelClass}'); ?>\n";
}
?>
	<fieldset>
		<legend><?php printf("<?php echo __d('{$plugin_domain}','%s %s'); ?>", $actionTitle , $singularHumanName); ?></legend>
<?php
		echo "\t<?php\n";

    echo "\t\techo \$this->Form->hidden('return_url',array('value'=>\$return_url));\n";
		foreach ($fields as $field) {
        $field_name=preg_replace('/_id$/','',$field);
        $field_options="'label'=>__d('{$plugin_domain}','".Inflector::humanize($field_name)."')";
			if (strpos($action, 'add') !== false && $field == $primaryKey) {
				continue;
			} elseif (!in_array($field, array('created', 'modified', 'updated'))) {
				$columnType = strtolower(ClassRegistry::init($modelClass)->getColumnType($field));
				if (in_array($columnType, array('date', 'datetime'))) {
					echo "\t\techo \$this->Form->input('{$field}', array({$field_options},'type' => 'text', 'class' => '{$columnType}'));\n";
				} elseif ($field == 'avatar') {
					echo "\t\techo \$this->Form->input('file', array({$field_options},'type' => 'file', 'label' => __d('{$plugin_domain}','Image')));\n";
				} elseif($field!=$field_name){
          echo "\t\techo \$this->Form->input('{$field}',array({$field_options}, 'empty'=>__d('{$plugin_domain}','(none)')));\n";
        } else{
					echo "\t\techo \$this->Form->input('{$field}',array({$field_options}));\n";
				}
			}
		}
		if (!empty($associations['hasAndBelongsToMany'])) {
			foreach ($associations['hasAndBelongsToMany'] as $assocName => $assocData) {
				echo "\t\techo \$this->Form->input('{$assocName}');\n";
			}
		}
		echo "\t?>\n";
?>
	</fieldset>
<?php
	echo "<?php \$link = \$this->Html->link(__d('{$plugin_domain}','Cancelar'), array('action' => 'index'), array(), __d('{$plugin_domain}','¿Descartar los cambios realizados?')); ?>";
	echo "<?php echo \$this->Form->submit(__d('{$plugin_domain}','Guardar'), array('before' => \$link)); ?>\n";
	echo "<?php echo \$this->Form->end(); ?>\n";

?>
</div>
