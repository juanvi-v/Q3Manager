<?php
require_once(dirname(__FILE__).'/../utils/Tools.php');

$plugin_domain=Inflector::underscore($plugin);
$tools= new Tools($plugin_domain);

$parent_field=Inflector::underscore($modelClass);
echo "<?php\n";
echo "echo \$this->Form->create('{$modelClass}',array('url' => array('action' => 'index','plugin'=>false),'id'=>'index_form'));\n";
echo "?>\n";
?>
<div class="<?php echo $pluralVar; ?> index">
	<h2><?php echo "<?php echo ".$tools->translate($pluralHumanName)."; ?>"; ?></h2>

	<?php
		if (!empty($associations['belongsTo'])) {
			echo "\t<?php\n";
			echo "\t?>\n";
		}
	?>

	<div class="index_actions">
	<ul>
		<li>
			<?php echo "<?php echo \$this->Html->link(
							\$this->Html->image('icons/add.png',
							array('alt' => '')) . ' ' . ".$tools->translate('New').",
							array('action' => 'add'),
							array('escape' => false)
						); ?>\n"; ?>
		</li>
		<?php echo "<?php if(!empty(\${$pluralVar})):?>\n"; ?>
		<li>
			<?php echo "<?php echo \$this->Html->link(
							\$this->Html->image('icons/cross.png',
							array('alt' => '')) . ' ' . ".$tools->translate('Delete').",
							array('action' => 'delete'),
							array('escape' => false, 'class' => 'index_form_link')
						); ?>\n"; ?>
		</li>

		<?php if (ClassRegistry::init($modelClass)->hasField('status')): ?>

		<li>

		<?php
			echo "<?php echo \$this->Html->link(
					\$this->Html->image('icons/unlocked.png',
					array('alt'=>'')).' '.".$tools->translate('Enable').",
					array('action'=>'set_status', STATUS_ENABLED),
					array('escape'=>false, 'class' => 'index_form_link')
				);?>\n";
		?>

		</li>
		<li>
		<?php
			echo "<?php echo \$this->Html->link(
					\$this->Html->image('icons/locked.png',
					array('alt'=>'')).' '.".$tools->translate('Disable').",
					array('action'=>'set_status', STATUS_DISABLED),
					array('escape'=>false, 'class' => 'index_form_link')
				);?>\n";
		?>
		</li>
	<?php
	endif;
	echo  "<?php endif; ?>";
	?>
	</ul>
	</div>

	<table>
	<thead>
	<tr>
		<th rowspan="2"><?php echo "<?php echo \$this->Form->checkbox('main', array('id'=>'main')); ?>"; ?></th>
	<?php $filter_row='<tr class="filter">';?>
	<?php foreach ($fields as $field):
		if (in_array($field, array($primaryKey, 'modified','password'))) {
			continue;
		}
	?>
		<?php $field_name=preg_replace('/_id$/','',$field);?>
		<?php

			if($field_name!=$field){
				if($field_name==$parent_field){
					$referenced_field='Parent'.$modelClass.'.'.$displayField;
					$field_name='parent_'.$field_name;
				}
				else{
					$referenced_field=Inflector::camelize($field_name).'.name';
				}
			}
			else{
				$referenced_field=$field;
			}
			?>
		<th><?php echo "<?php echo \$this->Paginator->sort('{$referenced_field}',".$tools->translate(Inflector::humanize($field_name))."); ?>"; ?></th>
		<?php if($field=='status'){
				$filter_row.="\n\t\t<td><?php echo \$this->Form->input('{$field}',array('label'=>false, 'empty'=>".$tools->translate('(any)').", 'options'=>\$status_list,'required'=>false));?></td>";
			}
			elseif($field_name!=$field){
				if($field==$parent_field){
					$filter_row.="\n\t\t<td><?php echo \$this->Form->input('{$field}',array('label'=>false, 'empty'=>".$tools->translate('(any)').", 'options'=>\$parent_".Inflector::pluralize($field_name).",'required'=>false));?></td>";
				}
				else{
					$filter_row.="\n\t\t<td><?php echo \$this->Form->input('{$field}',array('label'=>false, 'empty'=>".$tools->translate('(any)').", 'options'=>\$".Inflector::pluralize($field_name).",'required'=>false));?></td>";
				}

			}
			else{
				$filter_row.="\n\t\t<td><?php echo \$this->Form->input('{$field}',array('label'=>false,'type'=>'text','placeholder'=>".$tools->translate('Filter by %s',$tools->translate($field)).",'required'=>false));?></td>";
			}
			?>
	<?php endforeach; ?>
		<?php $filter_row.="\n<td class=\"actions\"><?php
			echo \$this->Form->button(".$tools->translate('Search').");
			echo \$this->Html->link(".$tools->translate('Reset filter').", array('action' => 'index'));
		?></td>
	</tr>";?>
		<th class="actions"><?php echo "<?php echo ".$tools->translate('Actions')."; ?>"; ?></th>
	</tr>

	<?php
	echo $filter_row;
	?>
</thead>
<tbody>
	<?php
	echo "<?php foreach (\${$pluralVar} as \${$singularVar}): ?>\n";
	echo "\t<tr>\n";
	echo "\t\t<td><?php echo \$this->Form->checkbox('Selected.'.\${$singularVar}['{$modelClass}']['{$primaryKey}'] , array('class'=>'selected')); ?></td>\n";
		foreach ($fields as $field) {
			if (in_array($field, array($primaryKey, 'modified','password'))) {
				continue;
			}
			$field_name=preg_replace('/_id$/','',$field);

			$isKey = false;
			if (!empty($associations['belongsTo'])) {
				foreach ($associations['belongsTo'] as $alias => $details) {
					if ($field === $details['foreignKey']) {
						$isKey = true;
						echo "\t\t<td>\n\t\t\t<?php echo \$this->Html->link(\${$singularVar}['{$alias}']['{$details['displayField']}'], array('controller' => '{$details['controller']}', 'action' => 'view', \${$singularVar}['{$alias}']['{$details['primaryKey']}'])); ?>\n\t\t</td>\n";
						break;
					}
				}
			}
			if ($isKey !== true) {
				if ($field == 'status') {
					echo "\t\t<td class=\"icon\">
						<?php if (\${$singularVar}['{$modelClass}']['{$field}']) {
							\$image = \$this->Html->image('icons/unlocked.png', array('alt' => ".$tools->translate('Disable')."));
							echo \$this->Html->link(
								\$image,
								array('action' => 'set_status', STATUS_DISABLED, \${$singularVar}['{$modelClass}']['{$primaryKey}']),
								array('escape' => false)
							);
						} else {
							\$image = \$this->Html->image('icons/locked.png', array('alt' => ".$tools->translate('Enable')."));
							echo \$this->Html->link(
								\$image,
								array('action' => 'set_status', STATUS_ENABLED, \${$singularVar}['{$modelClass}']['{$primaryKey}']),
								array('escape' => false)
							);
						}\n
					?></td>\n";
				} elseif ($field == 'email') {
					echo "\t\t<td><?php echo \$this->Html->link(\${$singularVar}['{$modelClass}']['{$field}'],'mailto:'.\${$singularVar}['{$modelClass}']['{$field}']); ?>&nbsp;</td>\n";
				} elseif ($field == 'avatar') {
					echo "\t\t<td>
						<?php
							echo \$this->Html->image('{$modelClass}/' . \${$singularVar}['{$modelClass}']['{$field}'] . ',fitCrop,100,100.jpg');
					?></td>\n";
				} elseif($field_name!=$field){
					if($field_name==$parent_field){
						echo "\t\t<td><?php echo h(\${$singularVar}['Parent{$modelClass}']['{$displayField}']); ?>&nbsp;</td>\n";
					}
					else{
						$referenced_model=Inflector::camelize($field_name);
						/**
						 * @TODO this should use the display name of the referenced model instead of the name field
						 */
						echo "\t\t<td><?php echo h(\${$singularVar}['{$referenced_model}']['name']); ?>&nbsp;</td>\n";
					}
				} elseif (in_array(strtolower(ClassRegistry::init($modelClass)->getColumnType($field)), array('date', 'datetime'))) {
						echo "\t\t<td><?php echo \$this->Date->dateFormat(\${$singularVar}['{$modelClass}']['{$field}'],'timestamp'); ?>&nbsp;</td>\n";
				} elseif (strtolower(ClassRegistry::init($modelClass)->getColumnType($field)) == 'text') {
						echo "\t\t<td><?php echo h(\${$singularVar}['{$modelClass}']['{$field}'], 250); ?>&nbsp;</td>\n";
				} else {
						echo "\t\t<td><?php echo h(\${$singularVar}['{$modelClass}']['{$field}']); ?>&nbsp;</td>\n";
				}
			}
		}
		echo "\t\t<td class=\"actions\">\n";
		echo "\t\t\t<?php echo \$this->Html->link(".$tools->translate('View').", array('action' => 'view', \${$singularVar}['{$modelClass}']['{$primaryKey}'])); ?>\n";
		echo "\t\t\t<?php echo \$this->Html->link(".$tools->translate('Edit').", array('action' => 'edit', \${$singularVar}['{$modelClass}']['{$primaryKey}'])); ?>\n";
		echo "\t\t\t<?php echo \$this->Html->link(".$tools->translate('Delete').", array('action' => 'delete', \${$singularVar}['{$modelClass}']['{$primaryKey}']), array('confirm'=>".$tools->translate('Are you sure to delete %s?', "\${$singularVar}['{$modelClass}']['{$displayField}']").")); ?>\n";
		echo "\t\t</td>\n";
	echo "\t</tr>\n";

	echo "<?php endforeach; ?>\n";
	?>
</tbody>
	</table>
	<p>
	<?php echo "<?php
	echo \$this->Paginator->counter(array(
	'format' => ".$tools->translate('Page {:page} of {:pages}, viewing {:current} registers from total {:count}, beggining from {:start}, ending at {:end}')."
	));
	?>"; ?>
	</p>
	<div class="paging">
	<?php
		echo "<?php\n";
		echo "\t\techo \$this->Paginator->prev('< ' . ".$tools->translate('previous').", array(), null, array('class' => 'prev disabled'));\n";
		echo "\t\techo \$this->Paginator->numbers(array('separator' => ''));\n";
		echo "\t\techo \$this->Paginator->next(".$tools->translate('next')." . ' >', array(), null, array('class' => 'next disabled'));\n";
		echo "\t?>\n";
	?>
	</div>
</div>
<?php echo "<?php echo \$this->Form->end(); ?>"; ?>
