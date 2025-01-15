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
        "actionOrderGridDefinitionModifier",
        "actionOrderGridQueryBuilderModifier", 
        "actionOrderGridDataModifier",
        "actionSlipPDFFormBuilderModifier",
        "actionValidateOrder",
        "displayAdminOrder",
        "addWebserviceResources"
    );

	public function __construct()
	{
		$this->name = 'ac_ordercolumns';
		$this->tab = 'back_office_features';
		$this->version = '1.0.0';
		$this->author = 'Alternetic';
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


    /*
     * Début des développements
     */

    /*
     * Refernce : https://github.com/Matt75/displayordercarrier/blob/master/displayordercarrier.php
     */

    public function hookActionOrderGridDefinitionModifier(array $params)
    {
//        /** @var GridDefinitionInterface $definition */
//        $definition = $params['definition'];
//
//        /** @var FilterCollection $filters */
//        $filters = $definition->getFilters();
//
//        /** @var ColumnCollection */
//        $columns = $definition->getColumns();
//
//        $columns
//            ->addAfter('date_add',
//                (new DataColumn('printed'))
//                    ->setName($this->l('Printed'))
//                    ->setOptions([
//                        'field' => 'printed',
//                    ])
//            );
//        $filters
//            ->add((new Filter('printed', CheckboxType::class))
//                ->setTypeOptions([
//                    'required' => false,
//                    'attr' => [
//                        'placeholder' => $this->trans('Printed', [], 'Modules.ACPrintedColumn'),
//                    ],
//                ])
//                ->setAssociatedColumn('printed'));

        if (empty($params['definition'])) {
            return;
        }

        /** @var PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface $definition */
        $definition = $params['definition'];


        $columnText = new PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn('carrier');
        $columnText->setName($this->l('Transporteur'));
        $columnText->setOptions([
            'field' => 'carrier',
        ]);


        if (static::CONFIGURATION_KEY_SHOW_LOGO) {
            $column = new PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ImageColumn('printed');
            $column->setName($this->l('Printed'));
            $column->setOptions([
                'src_field' => 'printed_flag',
                'clickable' => false,
            ]);
        } else {
            $column = new PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn('printed');
            $column->setName($this->l('Printed'));
            $column->setOptions([
                'field' => 'printed',
            ]);
        }


        if (static::CONFIGURATION_KEY_SHOW_LOGO) {
            $columnExported = new PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ImageColumn('exported');
            $columnExported->setName($this->l('exported'));
            $columnExported->setOptions([
                'src_field' => 'exported_flag',
                'clickable' => false,
            ]);
        } else {
            $columnExported = new PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn('exported');
            $columnExported->setName($this->l('Exported'));
            $columnExported->setOptions([
                'field' => 'exported',
            ]);
        }

        $definition
            ->getColumns()
            ->addAfter(
                'date_add',
                $columnText
            );

        $definition
            ->getColumns()
            ->addAfter(
                'date_add',
                $columnExported
            )
        ;
        $definition
            ->getColumns()
            ->addAfter(
                'date_add',
                $column
            );


        // https://devdocs.prestashop-project.org/8/development/components/grid/filter-types-reference/
        $definition->getFilters()
            /* ->add(
                (new Filter('carrier', TextType::class))->setAssociatedColumn('carrier')
            ) */
            ->add(
                (new Filter('printed', YesAndNoChoiceType::class))->setAssociatedColumn('printed')
            )
            ->add(
                (new Filter('exported', YesAndNoChoiceType::class))->setAssociatedColumn('exported')
            );
    }


    public function hookActionOrderDeliverySlipPdfForm(array $params)
    {
        /**
         * @var Symfony\Component\Form\FormBuilderInterface $formBuilder
         */
        $formBuilder = $params['form_builder'];
        // Find field named date_from
        $formBuilder->get("date_from")->setData((new \DateTime('-30 days'))->format('Y-m-d'));
    }


    /**
     * @param array $params
     *                      <pre>
     *                      array(
     *                      select => string,// optional, passed by reference
     *                      join => string,// optional, passed by reference
     *                      where => string,// optional, passed by reference
     *                      group_by => string,// optional, passed by reference
     *                      order_by => string,// optional, passed by reference
     *                      order_way => string,// optional, passed by reference
     *                      fields => array // @see AdminController::fields_list, passed by reference
     *                      cookie => Cookie,
     *                      cart => Cart// optional
     *                      )
     */
    public function hookActionOrderGridQueryBuilderModifier(array $params)
    {

//        if (!PosConfiguration::get('POS_SHOW_ORDERS_UNDER_PS_ORDERS')) {
//            $searchQueryBuilder = $params['search_query_builder'];
//            $searchQueryBuilder->andWhere("o.`module` <> '$this->name'");
//        }
//        $searchQueryBuilder = $params['search_query_builder'];
//        /** @var CustomerFilters $searchCriteria */
//        $searchCriteria = $params['search_criteria'];
//        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
//            if ('printed' === $filterName && $filterValue) {
//                $searchQueryBuilder
//                    ->where('o.`printed` = \'' . $filterValue . '\'')
//                    ->orWhere('o.`printed` LIKE "%'.$filterValue.'%"');
//                $searchQueryBuilder->setParameter(':s', $filterValue);
//            }
//        }
//
//        $searchQueryBuilder->addSelect('o.printed');


        if (empty($params['search_query_builder']) || empty($params['search_criteria'])) {
            return;
        }

        /** @var Doctrine\DBAL\Query\QueryBuilder $searchQueryBuilder */
        $searchQueryBuilder = $params['search_query_builder'];

        /** @var PrestaShop\PrestaShop\Core\Search\Filters\OrderFilters $searchCriteria */
        $searchCriteria = $params['search_criteria'];

        $searchQueryBuilder
            ->addSelect('ct.`name` AS `carrier`')
            ->addSelect('opp.`printed` AS `printed`')
            ->addSelect('opp.`exported` AS `exported`');

        $searchQueryBuilder->leftJoin(
            'o',
            '`' . _DB_PREFIX_ . 'carrier`',
            'ct',
            'ct.`id_carrier` = o.`id_carrier`'
        );

        $searchQueryBuilder->leftJoin(
            'o',
            '`' . _DB_PREFIX_ . 'order_printed`',
            'opp',
            'opp.`id_order` = o.`id_order`'
        );

        if ('id_carrier' === $searchCriteria->getOrderBy()) {
            $searchQueryBuilder->orderBy('ct.`name`', $searchCriteria->getOrderWay());
        }

        if ('printed' === $searchCriteria->getOrderBy()) {
            $searchQueryBuilder->orderBy('opp.`printed`', $searchCriteria->getOrderWay());
        }


        if ('exported' === $searchCriteria->getOrderBy()) {
            $searchQueryBuilder->orderBy('opp.`exported`', $searchCriteria->getOrderWay());
        }

        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if ('printed' === $filterName) {
                $searchQueryBuilder->andWhere('opp.`printed` = :printed');
                $searchQueryBuilder->setParameter('printed', $filterValue);
            }
            /*
            if ('carrier' === $filterName) {
                $searchQueryBuilder->andWhere('ct.`name` LIKE "%:carrier%"');
                $searchQueryBuilder->setParameter('carrier', $filterValue);
            }
            */
            if ('exported' === $filterName) {
                $searchQueryBuilder->andWhere('opp.`exported` = :exported');
                $searchQueryBuilder->setParameter('exported', $filterValue);
            }
        }

    }


    /*
     * Modification des données : si on veut transformer les données à l'affichage de la liste
     * Ex. monter une coche au lieu d'un 1
     */
    public function hookActionOrderGridDataModifier(array $params)
    {

        if (empty($params['data'])) {
            return;
        }

        /** @var PrestaShop\PrestaShop\Core\Grid\Data\GridData $gridData */
        $gridData = $params['data'];
        $modifiedRecords = $gridData->getRecords()->all();
        /** @var PrestaShop\PrestaShop\Core\Image\Parser\ImageTagSourceParserInterface $imageTagSourceParser */
//        $imageTagSourceParser = $this->get('prestashop.core.image.parser.image_tag_source_parser');
//        $carrierLogoThumbnailProvider = new \PrestaShop\Module\DisplayOrderCarrier\CarrierLogoThumbnailProvider($imageTagSourceParser);

        $imgEnabled  = Tools::getHttpHost(true)."/img/admin/enabled.gif";
        $imgDisabled = Tools::getHttpHost(true)."/img/admin/disabled.gif";

        foreach ($modifiedRecords as $key => $data) {
            if (empty($data['printed_flag'])) {
                $modifiedRecords[$key]['printed_flag'] = $modifiedRecords[$key]['printed'] ? $imgEnabled : $imgDisabled;
            }
            if (empty($data['exported_flag'])) {
                $modifiedRecords[$key]['exported_flag'] = $modifiedRecords[$key]['exported'] ? $imgEnabled : $imgDisabled;
            }
        }

        $params['data'] = new PrestaShop\PrestaShop\Core\Grid\Data\GridData(
            new PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection($modifiedRecords),
            $gridData->getRecordsTotal(),
            $gridData->getQuery()
        );
    }

    public function hookActionAdminOrdersListingResultsModifier(array $params)
    {

    }

    /**
     * @param array $params
     *                      <pre>
     *                      array(
     *                      select => string,// optional, passed by reference
     *                      join => string,// optional, passed by reference
     *                      where => string,// optional, passed by reference
     *                      group_by => string,// optional, passed by reference
     *                      order_by => string,// optional, passed by reference
     *                      order_way => string,// optional, passed by reference
     *                      fields => array // @see AdminController::fields_list, passed by reference
     *                      cookie => Cookie,
     *                      cart => Cart// optional
     *                      )
     */
    public function hookActionAdminOrdersListingFieldsModifier(array $params)
    {
//        if (!PosConfiguration::get('POS_SHOW_ORDERS_UNDER_PS_ORDERS')) {
//            if (in_array('where', array_keys($params))) {
//                $params['where'] .= " AND a.`module` <> '$this->name'";
//            }
//        }
    }

    public function hookActionValidateOrder(array $params) {
        $order = $params['order'];

        // Création de l'enregistrement à vide en premier
        Db::getInstance()->execute("INSERT INTO " . _DB_PREFIX_ . "order_printed (id_order, printed, exported) VALUES ({$order->id}, 0, 0)");

        // Puis en second Requête d'insertion du printed si les paramètres feedbiz l'indiquent
        // Utilisation d'un replace pour éviter les doublons
        if(isset($order->id)) {
            $orderId = (int)$order->id;
            $temptablename = "temp_".uniqid();
            $sql1 = "CREATE TEMPORARY TABLE ".$temptablename." AS 
                SELECT o.id_order,fo.multichannel
                FROM ("._DB_PREFIX_."feedbiz_orders AS fo,"._DB_PREFIX_."orders as o) 
                LEFT JOIN "._DB_PREFIX_."order_printed AS op ON op.id_order=o.id_order 
                WHERE (op.printed IS NULL OR op.printed=0) AND fo.id_order=o.id_order AND fo.multichannel='AFN' AND o.id_order=".$orderId.";";
            //SELECT * FROM temp_1234;

            $sql2 = "REPLACE INTO "._DB_PREFIX_."order_printed (id_order,printed,printed_date)
                SELECT id_order, IF(multichannel='AFN',1,0) AS printed, IF(multichannel='AFN',NOW(),NULL) FROM ".$temptablename.";";
            //SELECT * FROM ps_order_printed;

            //SELECT o.id_order,IF(f.multichannel='AFN',1,0),IF(f.multichannel='AFN',NOW(),NULL) FROM "._DB_PREFIX_."feedbiz_orders"._DB_PREFIX_."feedbiz_orders f WHERE f.id_order=o.id_order";
            try
            {
                Db::getInstance()->Execute($sql1);
                Db::getInstance()->Execute($sql2);
            }
            catch (Exception $e)
            {
                $message = ($e->getMessage()); // probablement car la table n'existe pas
            }
        }



    }

    function hookDisplayAdminOrder($params) {
    	$smarty = new Smarty();
    	$order = new Order(Tools::getValue("id_order"));
    	$url = "https://".$_SERVER['HTTP_HOST'].$this->_path;
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

