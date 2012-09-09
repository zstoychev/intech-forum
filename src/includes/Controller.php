<?php
interface Controller {
	public function getPOSTTextFields();
	public function isUserForUpdate();
	public function execute($databaseConnection, $config, &$requestData);
}
?>
