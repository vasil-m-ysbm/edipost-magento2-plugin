<?php
namespace Edipost\Shipment\Controller\Adminhtml\Ajax;

use Edipost\Shipment\Helper\View as helperView;
require_once( helperView::getDirectory() . DIRECTORY_SEPARATOR . 'Lib' . DIRECTORY_SEPARATOR . 'php-rest-client' . DIRECTORY_SEPARATOR . 'EdipostService.php' );
use EdipostService\Client\Builder\ConsigneeBuilder;
use EdipostService\Client\Builder\ConsignmentBuilder;
use EdipostService\Client\Item;
use EdipostService\EdipostService;


class CreateShipment extends \Magento\Backend\App\AbstractAction {
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Edipost\Shipment\Helper\ConfigData $configData
     */

    protected $configHelper;

    protected $_api;
    protected $_apiData;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Edipost\Shipment\Helper\ConfigData $configData
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->configHelper = $configData;
        $this->_apiData = $this->configHelper->apiData();
        $this->_api = new EdipostService( $this->_apiData ['api_token'], $this->_apiData ['api_endpoint'] );
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute() {
        if(!$this->getRequest()->isAjax()){
            return false;
        }
        $result = $this->resultJsonFactory->create();
        $error = '';

        $order_id = $this->getRequest()->getParam('order_id', 0);
        $product_id = $this->getRequest()->getParam('product_id', 0);
        $reference = $this->getRequest()->getParam('reference', '');

        $order = helperView::getOrderById($order_id);

        $shippingAddressArray = $order->getShippingAddress()->getData();

        $builder = new ConsigneeBuilder();

        $company_name = 'no company';
        if($shippingAddressArray['company']){
            $shippingAddressArray['company'];
        }



        $consignee = $builder
            ->setCompanyName( $company_name )
            ->setCustomerNumber( (string)$order->getCustomerId() )
            ->setPostAddress( $shippingAddressArray['street'] )
            ->setPostZip( $shippingAddressArray['postcode'] )
            ->setPostCity( $shippingAddressArray['city'] )
            ->setStreetAddress( $shippingAddressArray['street'] )
            ->setStreetZip( $shippingAddressArray['postcode'] )
            ->setStreetCity( $shippingAddressArray['city'] )
            ->setContactName($shippingAddressArray['firstname'].' '. $shippingAddressArray['lastname'] )
            ->setContactEmail( $shippingAddressArray['email'] )
            ->setContactPhone( $shippingAddressArray['telephone'] )
            ->setContactCellPhone( $shippingAddressArray['telephone'] )
            ->setContactTelefax( $shippingAddressArray['fax'] )
            ->setCountry( $shippingAddressArray['country_id'] )
            ->build();
        $pdf = '';

//        try {
            $newConsignee = $this->_api->createConsignee( $consignee );
            $consigneeId =  $newConsignee->ID;

            $builder = new ConsignmentBuilder();

            $consignor = $this->_api->getDefaultConsignor();

            $consignment = $builder
                ->setConsignorID( $consignor->ID )
                ->setConsigneeID( $consigneeId )
                ->setProductID( $product_id )
                ->setTransportInstructions( '' )
                ->setContentReference( $reference )
                ->setInternalReference( '' );

            $consignment->addItem( new Item( 1, 0, 0, 0 ) );
//
            $newConsignment = $this->_api->createConsignment( $consignment->build() );
//            $pdf = $this->_api->printConsignment( $newConsignment->id );

//        } catch (WebException $exception){
//            $error = $exception->getMessage();
//        }




        return $result->setData([
            'error' => $error,
            'pdf' => $pdf,
            'id' => $newConsignment->id,
        ]);
    }
}