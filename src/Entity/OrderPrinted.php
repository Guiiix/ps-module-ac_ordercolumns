<?php

class OrderPrinted extends ObjectModel
{
    public $title;
    public $type;
    public $content;
    public $meta_title;

    public $date_add;
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'order_printed',
        'primary' => 'id_order_printed',
        'multilang' => false,
        'fields' => array(
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'printed'  => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'printed_date' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'exported'  => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'exported_date' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        )
    );

    protected $webserviceParameters = array(
      'objectNodeName' => 'order_printed',
      'objectsNodeName' => 'orders_printed',
      'fields' => array(
          'id_order' => array('required' => true),
          'printed' => array('required' => true),
          'printed_date' => array(),
          'exported' => array('required' => true),
          'exported_date' => array(),
        )
    );
}
