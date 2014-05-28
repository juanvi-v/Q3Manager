<?php
require_once('variables.php');
echo "<?php\n";
echo "App::uses('{$the_plugin}{$controllerName}Controller', '{$the_plugin}.Controller');\n";
?>
/**
 * <?php echo $controllerName; ?> Controller
 */
class <?php echo $controllerName; ?>Controller extends <?php echo $the_plugin.$controllerName; ?>Controller {

}
