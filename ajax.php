<?php

@require_once(dirname(__FILE__).'/../../config/config.inc.php');
@require_once(dirname(__FILE__).'/../../init.php');


function exitWithError() {
    die(json_encode(array("status" => "error")));
}

function printSuccess() {
    echo json_encode(array("status" => "success"));
}

function updateStatus($type, $status) {
    if (!in_array($status, [0, 1, 2])) {
        exitWithError();
    }

    $order = new Order(Tools::getValue("order"));
    $res = Db::getInstance()->execute("UPDATE " . _DB_PREFIX_ . "order_printed SET {$type}=" . $status . ", {$type}_date=NOW() WHERE id_order=" . $order->id);
    if ($res) {
        printSuccess();
    }

    else {
        exitWithError();
    }
}

function validateAuth() {
    $token = Tools::getAdminToken('AdminOrders'.(int)(Tab::getIdFromClassName('AdminOrders')). (int)(Tools::getValue("employee")));

    if (Tools::getValue("token") != $token) {
        exitWithError();
    }
}

function main() {
    if (!(Tools::getIsset("employee") AND Tools::getIsset("token") AND Tools::getIsset("order") AND Validate::isInt(Tools::getValue("order")))) {
        exitWithError();
    }

    validateAuth();

    if (Tools::getIsset("printed")) {
        updateStatus("printed", Tools::getValue("printed"));
    }

    else if (Tools::getIsset("exported")) {
        updateStatus("exported", Tools::getValue("exported"));
    }

    else {
        exitWithError();
    }
}

main();
