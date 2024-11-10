<?php

class OrderWithPrinted extends Order
{
    protected $webserviceParameters = [
        'objectMethods' => ['add' => 'addWs'],
        'objectNodeName' => 'order',
        'objectsNodeName' => 'orders',
        'fields' => [
            'id_address_delivery' => ['xlink_resource' => 'addresses'],
            'id_address_invoice' => ['xlink_resource' => 'addresses'],
            'id_cart' => ['xlink_resource' => 'carts'],
            'id_currency' => ['xlink_resource' => 'currencies'],
            'id_lang' => ['xlink_resource' => 'languages'],
            'id_customer' => ['xlink_resource' => 'customers'],
            'id_carrier' => ['xlink_resource' => 'carriers'],
            'current_state' => [
                'xlink_resource' => 'order_states',
                'setter' => 'setWsCurrentState',
            ],
            'module' => ['required' => true],
            'invoice_number' => [],
            'invoice_date' => [],
            'delivery_number' => [],
            'delivery_date' => [],
            'valid' => [],
            'date_add' => [],
            'date_upd' => [],
            'shipping_number' => [
                'getter' => 'getWsShippingNumber',
                'setter' => 'setWsShippingNumber',
            ],
            'note' => [],
        ],
	'linked_tables' => [
	    'orders_printed' => [
	        'table' => 'order_printed',
		'fields' => [
		    'exported'  => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'sqlId' => 'exported')
		],
            ],
        ],
		
        'associations' => [
            'order_rows' => ['resource' => 'order_row', 'setter' => false, 'virtual_entity' => true,
                'fields' => [
                    'id' => [],
                    'product_id' => ['required' => true, 'xlink_resource' => 'products'],
                    'product_attribute_id' => ['required' => true],
                    'product_quantity' => ['required' => true],
                    'product_name' => ['setter' => false],
                    'product_reference' => ['setter' => false],
                    'product_ean13' => ['setter' => false],
                    'product_isbn' => ['setter' => false],
                    'product_upc' => ['setter' => false],
                    'product_price' => ['setter' => false],
                    'id_customization' => ['required' => false, 'xlink_resource' => 'customizations'],
                    'unit_price_tax_incl' => ['setter' => false],
                    'unit_price_tax_excl' => ['setter' => false],
                ], ],
        ],
    ];
}
