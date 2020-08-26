<?php

namespace travelsoft\sirenaintegration;

class EventsHandlers {

    public function onBeforeTsOperatorServiceBookCreate($bookFields, $touristsData = []) {
        if (!Tools::checkPassengersData($touristsData)) {
            $bookFields['errorStatus'] = \travelsoft\booking\Settings::BEFORE_SERVICE_BOOK_CREATE_STATUS_NEED_TOURISTS_DATA;
            return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::ERROR, $bookFields);
        }
        return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, $bookFields);
    }

    public function onAfterTsOperatorServiceBookCreate($serviceBookId, $touristsData = []) {

        $bookFields = \travelsoft\booking\stores\Bookings::getById($serviceBookId);

        if (!in_array($bookFields['UF_SERVICE_TYPE'], ['packagetour', 'transfer'])) {
            return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, $bookFields);
        }

        if (Tools::checkPassengersData($touristsData)) {

            $request = new \travelsoft\sirenaintegration\Request;

            $data['passengers'] = [];
            foreach ($touristsData['adults'] as $id => $adult) {

                $data['passengers'][] = [
                    'id' => $id,
                    'lastname' => Tools::translate($adult['LAST_NAME']),
                    'firstname' => Tools::translate($adult['FIRST_NAME']),
                    'sex' => $adult['MALE'],
                    'birthdate' => $adult['BIRTHDATE'],
                    'category' => 'ADT',
                    'phone' => str_replace(["+", " ", "(", ")"], "", $adult['PHONE'])
                ];
            }

            if (!empty($touristsData['children'])) {
                $adultsCount = count($touristsData['adults']);
                foreach ($touristsData['children'] as $id => $children) {

                    $category = "CNN";
                    if ($age >= 2 && $age <= 12) {

                        $category = "INF";
                    }

                    $data['passengers'][] = [
                        'id' => $id + $adultsCount,
                        'lastname' => Tools::translate($children['LAST_NAME']),
                        'firstname' => Tools::translate($children['FIRST_NAME']),
                        'sex' => $children['MALE'],
                        'birthdate' => $children['BIRTHDATE'],
                        'category' => $category
                    ];
                }
            }

            $data['segments'] = [];
            $transfer = \travelsoft\booking\stores\Buses::getById($bookFields['UF_TRANSFER']);
            $transferback = \travelsoft\booking\stores\Buses::getById($bookFields['UF_TRANSFER_BACK']);
            $propertyCitySirenaCode = Option::PROPERTY_SINA_IBLOCK_CODE;
            $cityFrom = \travelsoft\booking\stores\Cities::getById($bookFields['UF_CITY_FROM']);
            $cityTo = \travelsoft\booking\stores\Cities::getById($bookFields['UF_CITY_TO']);
            $cityFromBack = \travelsoft\booking\stores\Cities::getById($bookFields['UF_CITY_FROM_BACK']);
            $cityToBack = \travelsoft\booking\stores\Cities::getById($bookFields['UF_CITY_TO_BACK']);

            $date = date('d.m.Y', strtotime($bookFields['UF_DATE_FROM_TRANS'] ? $bookFields['UF_DATE_FROM_TRANS']->toString() : $bookFields['UF_DATE_FROM']->toString()));
            $data['segments'][] = [
                'id' => 1,
                'departure' => $cityFrom['PROPERTIES'][$propertyCitySirenaCode]['VALUE'],
                'arrival' => $cityTo['PROPERTIES'][$propertyCitySirenaCode]['VALUE'],
                'company' => $transfer['UF_COMPANY'],
                'flight' => $transfer['UF_NUMBER'],
                'date' => $date,
                'subclass' => "P"
            ];

            $date = date('d.m.Y', strtotime($bookFields['UF_DATE_TO_TRANS'] ? $bookFields['UF_DATE_TO_TRANS']->toString() : $bookFields['UF_DATE_TO']->toString()));
            $data['segments'][] = [
                'id' => 2,
                'departure' => $cityFromBack['PROPERTIES'][$propertyCitySirenaCode]['VALUE'],
                'arrival' => $cityToBack['PROPERTIES'][$propertyCitySirenaCode]['VALUE'],
                'company' => $transferback['UF_COMPANY'],
                'flight' => $transferback['UF_NUMBER'],
                'date' => $date,
                'subclass' => "P"
            ];

            $result = $request->send('booking', $data);

            if (
                    $result && $result['answer'] &&
                    $result['answer']['booking'] &&
                    $result['answer']['booking']['@attributes'] &&
                    $result['answer']['booking']['@attributes']['regnum']
            ) {

                $bookDetail = $result['answer'];
                $pnrData = [
                    'regnum' => $bookDetail['booking']['@attributes']['regnum'],
                    'agency' => $bookDetail['booking']['@attributes']['agency'],
                    'passengers' => [],
                    'segments' => [],
                    'price' => $bookDetail['booking']['pnr']['prices']['variant_total'] . " " . "Руб."
                ];

                if (isset($bookDetail['booking']['pnr']['passengers']['passenger']['@attributes'])) {
                    $passenger = $bookDetail['booking']['pnr']['passengers']['passenger'];
                    $pnrData['passengers'][] = [
                        'name' => $passenger['name'],
                        'surname' => $passenger['surname'],
                        'sex' => $passenger['sex'],
                        'birthdate' => $passenger['birthdate'],
                        'age' => $passenger['age'],
                        'contacts' => $passenger['contacts']['contact'] ?: null,
                    ];
                } else {
                    foreach ($bookDetail['booking']['pnr']['passengers']['passenger'] as $passenger) {
                        $pnrData['passengers'][] = [
                            'name' => $passenger->name->__toString(),
                            'surname' => $passenger->surname->__toString(),
                            'sex' => $passenger->sex->__toString(),
                            'birthdate' => $passenger->birthdate->__toString(),
                            'age' => $passenger->age->__toString(),
                            'contacts' => $passenger->contacts['contact'] ?: null,
                        ];
                    }
                }



                if (isset($bookDetail['booking']['pnr']['segments']['segment']['@attributes'])) {
                    $segment = $bookDetail['booking']['pnr']['segments']['segment'];
                    $pnrData['segments'][] = [
                        'company' => $segment['company'],
                        'flight' => $segment['flight'],
                        'class' => $segment['class'],
                        'seatcount' => $segment['seatcount'],
                        'departure' => $segment['departure'],
                        'arrival' => $segment['arrival'],
                        'status' => $segment['status'],
                        'flightTime' => $segment['flightTime']
                    ];
                } else {

                    foreach ($bookDetail['booking']['pnr']['segments']['segment'] as $segment) {
                        $pnrData['segments'][] = [
                            'company' => $segment->company,
                            'flight' => $segment->flight,
                            'class' => $segment->class,
                            'seatcount' => $segment->seatcount,
                            'departure' => $segment->departure,
                            'arrival' => $segment->arrival,
                            'status' => $segment->status,
                            'flightTime' => $segment->flightTime
                        ];
                    }
                }

                $res = (new BookManager)->table->add([
                    'UF_TS_BOOK_ID' => $serviceBookId,
                    'UF_SIRENA_BOOK_ID' => $result['answer']['booking']['@attributes']['regnum'],
                    'UF_BOOK_DETAIL' => json_encode($pnrData)
                ]);

                if ($res->isSuccess()) {
                    return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, ["serviceBookId" => $serviceBookId, 'extId' => $res->getId()]);
                }

                return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::ERROR, ["serviceBookId" => $serviceBookId, 'errorStatus' => \travelsoft\booking\Settings::BEFORE_SERVICE_BOOK_CREATE_STATUS_CANT_CREATE_TRANSFER_ORDER]);
            } else {
                return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::ERROR, ["serviceBookId" => $serviceBookId, 'errorStatus' => \travelsoft\booking\Settings::BEFORE_SERVICE_BOOK_CREATE_STATUS_CANT_CREATE_TRANSFER_ORDER]);
            }
        }

        $bookFields['errorStatus'] = \travelsoft\booking\Settings::BEFORE_SERVICE_BOOK_CREATE_STATUS_NEED_TOURISTS_DATA;
        return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::ERROR, $bookFields);
    }

    public static function onBeforeTsOperatorServiceBookDelete($parameters) {
        return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, $parameters);
    }

    public static function onActualizationPackagetourOffer($parameters) {

        if (
                !empty($parameters) && is_array($parameters) &&
                $parameters['transfer_id'] &&
                $parameters['transferback_id'] &&
                $parameters['date_from_transfer'] &&
                $parameters['date_back_transfer'] &&
                $parameters['city_from_id'] &&
                $parameters['city_to_id'] &&
                $parameters['city_from_back_id'] &&
                $parameters['city_to_back_id'] &&
                $parameters['adults'] > 0
        ) {
            $request = new \travelsoft\sirenaintegration\Request;

            /* актуализируем перелет */

            $transport = \travelsoft\booking\stores\Buses::getById($parameters['transfer_id']);

            $cityFrom = \travelsoft\booking\stores\Cities::getById($parameters['city_from_id']);
            $cityTo = \travelsoft\booking\stores\Cities::getById($parameters['city_to_id']);

            $propertySirenaCityCode = Option::PROPERTY_SINA_IBLOCK_CODE;

            $departure = $cityFrom['PROPERTIES'][$propertySirenaCityCode]['VALUE'];

            $arrival = $cityTo['PROPERTIES'][$propertySirenaCityCode]['VALUE'];

            // get aviability
            $response = $request->send('availability', [
                'flight' => $transport['UF_NUMBER'],
                'subclass' => "P",
                'departure' => $departure,
                'arrival' => $arrival,
                'date' => $parameters['date_from_transfer'],
                'direct' => true,
                'company' => $transport['UF_COMPANY']]
            );

            if (
                    !$response ||
                    !$response['answer'] ||
                    !$response['answer']['availability'] ||
                    !$response['answer']['availability']['flight']) {

                return self::notAvailTransferByPackagetourAfterActualization($parameters);
            }

            $quotaVal = $response['answer']['availability']['flight']['class'] && $response['answer']['availability']['flight']['class']["@attributes"]['count'] > 0 ? $response['answer']['availability']['flight']['class']["@attributes"]['count'] : 0;

            $quotaPeople = $parameters['adults'];
            foreach ($parameters['childrent_age'] as $age) {
                if ($age > 2) {
                    $quotaPeople++;
                }
            }
            if ($quotaVal < $quotaPeople) {
                return self::notAvailTransferByPackagetourAfterActualization($parameters);
            }

            // get price
            // create pessangers data
            $passengers = [
                [
                    'code' => 'ADT',
                    'count' => $parameters['adults']
                ]
            ];

            if ($parameters['children'] > 0 && !empty($parameters['children_age'])) {
                foreach ($parameters['children_age'] as $age) {
                    if ($age <= 2 && $age >= 0) {
                        $passengers[] = [
                            'code' => 'INF',
                            'count' => 1,
                            'age' => $age
                        ];
                    } else if ($age >= 2 && $age <= 12) {

                        $passengers[] = [
                            'code' => 'CNN',
                            'count' => 1,
                            'age' => $age
                        ];
                    }
                }
            }
            $response = $request->send('pricing_flight',
                    [
                        'passengers' => $passengers,
                        'curr' => $parameters['currency'],
                        'num' => $transport['UF_NUMBER'],
                        'subclass' => "P", 'class' => 'Э',
                        'departure' => $departure,
                        'arrival' => $arrival,
                        'date' => $parameters['date_from_transfer'],
                        'direct' => true,
                        'company' => $transport['UF_COMPANY']
                    ]
            );

            if (
                    !$response || !$response['answer'] ||
                    !$response['answer']['pricing_flight'] ||
                    !$response['answer']['pricing_flight']['variant'] ||
                    !$response['answer']['pricing_flight']['variant']['variant_total']
            ) {
                return self::notAvailTransferByPackagetourAfterActualization($parameters);
            }

            $delta = $response['answer']['pricing_flight']['variant']['variant_total'] - $parameters['transfer_gross'] - $parameters['transfer_discount'];
            if (abs($delta) >= \travelsoft\booking\Settings::FLOAT_NULL) {
                // значит цена перелета изменилась
                $parameters['transfer_gross'] = $response['answer']['pricing_flight']['variant']['variant_total'];
                $parameters['result_price'] += $delta;
                if (!in_array(\travelsoft\booking\Settings::ACTUALIZATION_PACKAGETOUR_COST_UPDATED, $parameters['ACTUALIZATION_NOTIFY_CODE'])) {
                    $parameters['ACTUALIZATION_NOTIFY_CODE'][] = \travelsoft\booking\Settings::ACTUALIZATION_PACKAGETOUR_COST_UPDATED;
                }
            }


            /* актуализируем обратный перелет */

            $transportback = \travelsoft\booking\stores\Buses::getById($parameters['transferback_id']);

            $cityFromBack = \travelsoft\booking\stores\Cities::getById($parameters['city_from_back_id']);
            $cityToBack = \travelsoft\booking\stores\Cities::getById($parameters['city_to_back_id']);

            $departureBack = $cityFromBack['PROPERTIES'][$propertySirenaCityCode]['VALUE'];

            $arrivalBack = $cityToBack['PROPERTIES'][$propertySirenaCityCode]['VALUE'];

            // get aviability
            $responseBack = $request->send('availability', [
                'flight' => $transportback['UF_NUMBER'],
                'subclass' => "P",
                'departure' => $departureBack,
                'arrival' => $arrivalBack,
                'date' => $parameters['date_from_transfer'],
                'direct' => true,
                'company' => $transportback['UF_COMPANY']]
            );

            if (
                    !$responseBack ||
                    !$responseBack['answer'] ||
                    !$responseBack['answer']['availability'] ||
                    !$responseBack['answer']['availability']['flight']) {

                return self::notAvailTransferbackByPackagetourAfterActualization($parameters);
            }

            $quotaValBack = $responseBack['answer']['availability']['flight']['class'] && $responseBack['answer']['availability']['flight']['class']["@attributes"]['count'] > 0 ? $responseBack['answer']['availability']['flight']['class']["@attributes"]['count'] : 0;

            $quotaPeopleBack = $parameters['adults'];
            foreach ($parameters['children_age'] as $age) {
                if ($age > 2) {
                    $quotaPeople++;
                }
            }
            if ($quotaValBack < $quotaPeopleBack) {
                return self::notAvailTransferbackByPackagetourAfterActualization($parameters);
            }

            // get price
            // create pessangers data
            $passengersBack = [
                [
                    'code' => 'ADT',
                    'count' => $parameters['adults']
                ]
            ];

            if ($parameters['children'] > 0 && $parameters && !empty($parameters['children_age'])) {
                foreach ($parameters['children_age'] as $age) {
                    if ($age <= 2 && $age >= 0) {
                        $passengersBack[] = [
                            'code' => 'INF',
                            'count' => 1,
                            'age' => $age
                        ];
                    } else if ($age >= 2 && $age <= 12) {

                        $passengersBack[] = [
                            'code' => 'CNN',
                            'count' => 1,
                            'age' => $age
                        ];
                    }
                }
            }
            $responseBack = $request->send('pricing_flight',
                    [
                        'passengers' => $passengersBack,
                        'curr' => $parameters['currency'],
                        'num' => $transportback['UF_NUMBER'],
                        'subclass' => "P", 'class' => 'Э',
                        'departure' => $departureBack,
                        'arrival' => $arrivalBack,
                        'date' => $parameters['date_from_transfer'],
                        'direct' => true,
                        'company' => $transportback['UF_COMPANY']
                    ]
            );

            if (
                    !$responseBack || !$responseBack['answer'] ||
                    !$responseBack['answer']['pricing_flight'] ||
                    !$responseBack['answer']['pricing_flight']['variant'] ||
                    !$responseBack['answer']['pricing_flight']['variant']['variant_total']
            ) {
                return self::notAvailTransferbackByPackagetourAfterActualization($parameters);
            }

            $deltaBack = $responseBack['answer']['pricing_flight']['variant']['variant_total'] - $parameters['transferback_gross'] - $parameters['transferback_discount'];
            if (abs($deltaBack) >= \travelsoft\booking\Settings::FLOAT_NULL) {
                // значит цена перелета изменилась
                $parameters['transferback_gross'] = $response['answer']['pricing_flight']['variant']['variant_total'];
                $parameters['result_price'] += $deltaBack;
                if (!in_array(\travelsoft\booking\Settings::ACTUALIZATION_PACKAGETOUR_COST_UPDATED, $parameters['ACTUALIZATION_NOTIFY_CODE'])) {
                    $parameters['ACTUALIZATION_NOTIFY_CODE'][] = \travelsoft\booking\Settings::ACTUALIZATION_PACKAGETOUR_COST_UPDATED;
                }
            }
        }

        return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, $parameters);
    }

    protected function notAvailTransferByPackagetourAfterActualization(array $parameters) {
        $parameters['ACTUALIZATION_NOTIFY_CODE'][] = \travelsoft\booking\Settings::ACTUALIZATION_PACKAGETOUR_NOT_AVAIL_TRANSFER_REASON;
        $parameters['CAN_BUY'] = false;
        return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, $parameters);
    }

    protected function notAvailTransferbackByPackagetourAfterActualization(array $parameters) {
        $parameters['ACTUALIZATION_NOTIFY_CODE'][] = \travelsoft\booking\Settings::ACTUALIZATION_PACKAGETOUR_NOT_AVAIL_TRANSFERBACK_REASON;
        $parameters['CAN_BUY'] = false;
        return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, $parameters);
    }

}
