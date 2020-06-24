<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

Bitrix\Main\Loader::includeModule("travelsoft.travelbooking");
Bitrix\Main\Loader::includeModule("travelsoft.sirenaintegration");

$request = \Bitrix\Main\Context::getCurrent()->getRequest();

if (!travelsoft\booking\crm\Utils::access()) {

    echo \travelsoft\booking\crm\Utils::sendJsonResponse(json_encode(array('error' => true, 'message' => 'access denided')));
}

////////////////////////////////////////////////////////////////////////////////

if (!$request->get('servicesBookId') || !is_array($request->get('servicesBookId')) || empty($request->get('servicesBookId'))) {
    echo \travelsoft\booking\crm\Utils::sendJsonResponse(json_encode(array('error' => true, 'message' => 'services ID not found.')));
}

$sirenaRequest = new travelsoft\sirenaintegration\Request;

$dbSirenaBooks = (new travelsoft\sirenaintegration\BookManager)->table->getList([
    'filter' => [
        'UF_TS_BOOK_ID' => $request->get('servicesBookId')
    ]
        ]);

$result = [];
while ($sirenaBook = $dbSirenaBooks->fetch()) {

    $result[] = json_decode($sirenaBook['UF_BOOK_DETAIL'], true);
}

echo \travelsoft\booking\crm\Utils::sendJsonResponse(json_encode(array('error' => false, 'result' => $result)));

