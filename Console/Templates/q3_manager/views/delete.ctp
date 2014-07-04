<?php
require_once(dirname(__FILE__).'/../utils/Tools.php');

$plugin_domain=Inflector::underscore($plugin);
$tools= new Tools($plugin_domain);
?>
<div class="<?php echo $pluralVar; ?> form">
<?php echo "<?php echo \$this->Form->create('{$modelClass}', array('url' => array('action' => 'delete','plugin'=>false, 0, 1))); ?>\n"; ?>
	<fieldset class="delete">
		<legend><?php echo "<?php echo ".$tools->translate('Eliminar '.$modelClass)."; ?>"; ?></legend>
		<p><?php echo "<?php echo ".$tools->translate('¿Confirma que desea eliminar los siguientes '.$pluralHumanName.'?')."; ?>"; ?></p>
		<ul>
<?php
echo "\t<?php\n";
echo "\t\tforeach (\${$pluralVar} as \${$singularVar}) {\n";
echo "\t\t\techo '<li>';\n";
echo "\t\t\techo '<label>', \${$singularVar}['{$modelClass}']['{$displayField}'], '</label>';\n";
echo "\t\t\techo \$this->Form->hidden('Selected.'.\${$singularVar}['{$modelClass}']['{$primaryKey}'] , array('class'=>'selected','value'=>1));\n";
echo "\t\t\techo '</li>';\n";
echo "\t\t}\n";
echo "\t?>\n";
?>
	</ul>
	</fieldset>
<?php
echo "<?php \$link = \$this->Html->link(".$tools->translate('Cancelar').", array('action' => 'index')); ?>\n";
echo "<?php echo \$this->Form->submit(".$tools->translate('Eliminar').", array('before' => \$link)); ?>\n";
echo "<?php echo \$this->Form->end(); ?>";
?>
</div>
