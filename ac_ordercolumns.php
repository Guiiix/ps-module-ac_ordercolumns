<?php
/**
* Copyright 2024 - Foxchip
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    Foxchip <contact@foxchip.com>
*  @copyright 2024 Foxchip
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registred Trademark & Property of PrestaShop SA
*/

require_once _PS_MODULE_DIR_ . 'ac_ordercolumns/src/Entity/OrderPrinted.php';
require_once _PS_MODULE_DIR_ . 'ac_ordercolumns/src/Entity/OrderWithPrinted.php';

Use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
Use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

if (!defined('_PS_VERSION_')) {
	exit;
}

class ac_ordercolumns extends Module
{
	/* @var boolean error */
	protected $error = false;
	private $templateFile;
    const CONFIGURATION_KEY_SHOW_LOGO = true;
    const HOOKS_LIST = array(
        "displayAdminOrder",
        "addWebserviceResources"
    );

	public function __construct()
	{
		$this->name = 'ac_ordercolumns';
		$this->tab = 'back_office_features';
		$this->version = '1.0.0';
		$this->author = 'Unknown';
		$this->need_instance = 0;

		$this->bootstrap = false;
		parent::__construct();

		$this->displayName = $this->trans('AC - Order columns for Printed and Exported', array(), 'Modules.ACPrintedColumn');
		$this->description = $this->trans('Add two columns to PS orders.', array(), 'Modules.ACPrintedColumn');

		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function install()
	{
        if(parent::install() &&
            $this->installDB() )
        {
            foreach(self::HOOKS_LIST as $hook)
            {
                if (!$this->registerHook($hook))
                    return false;
            }
        }
		return true;
	}

	public function installDB()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."order_printed`(
            `id_order_printed` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `id_order` INT(11) NOT NULL,
            `printed` INT(11),
            `printed_date` DATETIME,
            `exported` INT(11),
            `exported_date` DATETIME);";
        
        if(!$result=Db::getInstance()->Execute($sql))
        {
            return false;
        }
        
        return true;
	}

	public function uninstall()
	{
		if(parent::uninstall() && $this->uninstallDB())
        {
            foreach(self::HOOKS_LIST as $hook)
            {
                if (!$this->unregisterHook($hook))
                    return false;
            }
        }
        return true;
	}
	
	public function uninstallDB($drop_table = true)
    {
		return true;
	}

    function hookDisplayAdminOrder($params) {
    	$smarty = new Smarty();
    	$order = new Order(Tools::getValue("id_order"));
    	$url = Context::getContext()->shop->getBaseURL() . '/' .$this->_path;
		$res = Db::getInstance()->getRow("SELECT printed FROM " . _DB_PREFIX_ . "order_printed WHERE id_order=" . $order->id);
        $printed = $res !== false && array_key_exists('printed', $res) ? $res['printed'] : 0;
        $res = Db::getInstance()->getRow("SELECT exported FROM " . _DB_PREFIX_ . "order_printed WHERE id_order=" . $order->id);
        $exported = $res !== false && array_key_exists('exported', $res) ? $res['exported'] : 0;
    	$smarty->assign(array(
    		"url" => $url,
    		"id_employee" => $this->context->employee->id,
    		"id_order" => Tools::getValue("id_order"),
            "exported" => $exported,
    		"printed" => $printed,
    		"token" => Tools::getAdminToken('AdminOrders'.(int)(Tab::getIdFromClassName('AdminOrders')). (int)$this->context->employee->id)
    	));
    	$js = '<script type="text/javascript">'.$smarty->fetch(__DIR__."/js/gestionprintedexported.js")."</script>";
	    return $smarty->fetch(__DIR__."/tpl/admin.tpl").$js;
    }

    public function hookAddWebserviceResources($params)
    {
        return [
            'orders_printed' => array(
                'description' => 'Orders printed status',
                'class' => 'OrderPrinted',
            ),
            'orders_with_printed' => array(
                'description' => 'Order table with orders_printed association',
                'class' => 'OrderWithPrinted',
            )
		
		];
	}
}

