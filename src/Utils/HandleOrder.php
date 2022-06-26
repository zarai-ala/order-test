<?php

namespace App\Utils;

use App\Utils\Orders;
use App\Utils\Contacts;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class HandleOrder
{
    private $orders;
    private $contacts;
    private $fileUploader;
    private $appKernel;

    function __construct(Orders $orders, Contacts $contacts, FileUploader $fileUploader, KernelInterface $appKernel)
    {
        $this->orders = $orders;
        $this->contacts = $contacts;
        $this->fileUploader = $fileUploader;
        $this->appKernel = $appKernel;
    }

    public function hendle()
    {
        $orders = $this->orders->GetOrders();
        $contacts = $this->contacts->GetContacts();

        $datas = [];
        foreach ($orders['results'] as $key => $order) {
            foreach ($contacts['results'] as $contact) {
                if ($order['DeliverTo'] == $contact['ID']) {
                    $datas[$key]['order'] = $order;
                    $datas[$key]['order']['user'] = $contact;
                }
            }
        }



        $rows = array();
        foreach ($datas as $key  => $order) {
            $article = [];
            $orderNumber = $order['order']['OrderNumber'];
            $delivery_name = $order['order']['user']['AccountName'];
            $delivery_address = $order['order']['user']['AddressLine1'];
            $delivery_country = $order['order']['user']['Country'];
            $delivery_zipcode = $order['order']['user']['ZipCode'];
            $delivery_city = $order['order']['user']['City'];
            array_push($article,  $orderNumber, $delivery_name, $delivery_address, $delivery_country, $delivery_zipcode, $delivery_city);

            $rows[] = implode(',', $article);
            foreach ($order['order']['SalesOrderLines']['results'] as $key => $line) {
                $orderFile = [];
                $description = $line['Description'];
                $item_id = $line['Item'];
                $quantity =  $line['Quantity'];
                $line_price_excl_vat = $line['Amount'] * $line['VATPercentage'];
                $line_price_incl_vat = $line['Amount'];
                array_push($orderFile,  $key + 1, $description, $item_id, $quantity, $line_price_excl_vat, $line_price_incl_vat);


                $rows[] = implode(',', $orderFile);
            }
        }



        $content = implode("\n", $rows);

        return $content;
    }
}
