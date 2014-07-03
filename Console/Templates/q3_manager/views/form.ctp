<?php
     require_once(dirname(__FILE__).'/../utils/Tools.php');

     $plugin_domain=Inflector::underscore($plugin);
     $tools= new Tools($plugin_domain);

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
		<legend><?php printf("<?php echo ".$tools->translate('%s %%s',$tools->translate('%s'))."; ?>", $actionTitle , $singularHumanName); ?></legend>
<?php
		echo "\t<?php\n";

    echo "\t\techo \$this->Form->hidden('return_url',array('value'=>\$return_url));\n";
		foreach ($fields as $field) {
        $field_name=preg_replace('/_id$/','',$field);
        $field_options="'label'=>".$tools->translate(Inflector::humanize($field_name));
			if (strpos($action, 'add') !== false && $field == $primaryKey) {
				continue;
			} elseif (!in_array($field, array('created', 'modified', 'updated'))) {
				$columnType = strtolower(ClassRegistry::init($modelClass)->getColumnType($field));
				if (in_array($columnType, array('date', 'datetime'))) {
					echo "\t\techo \$this->Form->input('{$field}', array({$field_options},'type' => 'text', 'class' => '{$columnType}'));\n";
				} elseif ($field == 'avatar') {
					echo "\t\techo \$this->Form->input('file', array({$field_options},'type' => 'file', 'label' => ".$tools->translate('Image')."));\n";
                    } elseif ($field == 'status') {
                         echo "\t\techo \$this->Form->input('{$field}', array({$field_options},'options'=>\$status_list));\n";
				} elseif($field!=$field_name){
                         echo "\t\techo \$this->Form->input('{$field}',array({$field_options}, 'empty'=>".$tools->translate('(none)')."));\n";
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
	echo "<?php \$link = \$this->Html->link(".$tools->translate('Cancel').", array('action' => 'index'), array(), ".$tools->translate('Discard changes?')."); ?>";
	echo "<?php echo \$this->Form->submit(".$tools->translate('Save').", array('before' => \$link)); ?>\n";
	echo "<?php echo \$this->Form->end(); ?>\n";

?>
</div>
