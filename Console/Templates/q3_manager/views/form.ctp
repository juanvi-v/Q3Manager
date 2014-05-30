<?php
     $plugin_domain = Inflector::underscore($plugin);
       $actionTitle = Inflector::humanize($action);
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
		<legend><?php printf("<?php echo __d('{$plugin_domain}','%s %%s',__d('{$plugin_domain}','%s')); ?>", $actionTitle , $singularHumanName); ?></legend>
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
        } else{
          echo "\t\techo \$this->Form->input('{$field}',array({$field_options}, 'empty'=>__d('{$plugin_domain}','(none)')));\n";
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
	echo "<?php \$link = \$this->Html->link(__d('{$plugin_domain}','Cancel'), array('action' => 'index'), array(), __d('{$plugin_domain}','Discard changes?')); ?>";
	echo "<?php echo \$this->Form->submit(__d('{$plugin_domain}','Save'), array('before' => \$link)); ?>\n";
	echo "<?php echo \$this->Form->end(); ?>\n";

?>
</div>
