<?php
	@require_once(dirname(__FILE__).'/../../config/config.inc.php');
	@require_once(dirname(__FILE__).'/../../init.php');
	if (!(Tools::getIsset("employee") AND Tools::getIsset("token") AND Tools::getIsset("printed") AND Tools::getIsset("order") AND Validate::isInt(Tools::getValue("order"))))
		die(json_encode(array("status" => "error")));

	$token = Tools::getAdminToken('AdminOrders'.(int)(Tab::getIdFromClassName('AdminOrders')). (int)(Tools::getValue("employee")));
	if (Tools::getValue("token") != $token)
		die(json_encode(array("status" => "error")));

	if (Tools::getValue("printed") < 0 || Tools::getValue("printed") > 1)
		die(json_encode(array("status" => "error")));

	$order = new Order(Tools::getValue("order"));
	$retour = Db::getInstance()->execute("UPDATE " . _DB_PREFIX_ . "order_printed SET printed=" . Tools::getValue("printed") . " WHERE id_order=" . $order->id);
	if($retour) echo json_encode(array("status" => "success"));
	else echo json_encode(array("status" => "error"));
