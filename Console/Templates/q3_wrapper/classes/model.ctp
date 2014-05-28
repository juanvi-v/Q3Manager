<?php
require_once('variables.php');
echo "<?php\n";
echo "App::uses('{$the_plugin}{$name}', '{$the_plugin}.Model');\n";
?>
/**
 * <?php echo $the_plugin.$name ?> Model *
 */
class <?php echo $name ?> extends <?php echo $the_plugin.$name; ?> {


}
