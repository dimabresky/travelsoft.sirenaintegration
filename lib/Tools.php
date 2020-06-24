<?php

namespace travelsoft\sirenaintegration;

/**
 * Tools class
 *
 * @author dimabresky
 */
class Tools {

    public static function translate(string $str) {
        $compareTable = [
            "А" => "A",
            "Б" => "B",
            "В" => "V",
            "Г" => "G",
            "Д" => "D",
            "Е" => "E",
            "Ё" => "E",
            "Ж" => "ZH",
            "З" => "Z",
            "И" => "I",
            "Й" => "I",
            "К" => "K",
            "Л" => "L",
            "М" => "M",
            "Н" => "N",
            "О" => "O",
            "П" => "P",
            "Р" => "R",
            "С" => "S",
            "Т" => "T",
            "У" => "U",
            "Ф" => "F",
            "Х" => "KH",
            "Ц" => "TS",
            "Ш" => "CH",
            "Щ" => "SHCH",
            "Ь" => "",
            "Ы" => "Y",
            "Ъ" => "",
            "Э" => "E",
            "Ю" => "IU",
            "Я" => "IA",
        ];

        return str_replace(array_keys($compareTable), array_values($compareTable), strtoupper($str));
    }

    public static function checkPassengersData(array $passengers = []) {

        if (isset($passengers['adults'])) {

            for ($i = 0; $i < count($passengers['adults']); $i++) {
                if (isset($passengers['adults'][$i])) {

                    $passenger = $passengers['adults'][$i];
                    if (
                            !isset($passenger['FIRST_NAME']) ||
                            !isset($passenger['LAST_NAME']) ||
                            !isset($passenger['BIRTHDATE']) ||
                            !isset($passenger['MALE']) ||
                            strlen($passenger['FIRST_NAME']) < 2 ||
                            strlen($passenger['LAST_NAME']) < 2 ||
                            ($i == 0 && !$passenger['PHONE'])
                    ) {
                        return false;
                    }
                } else {
                    return false;
                }
            }

            if (isset($passengers['children'])) {
                for ($i = 0; $i < count($passengers['children']); $i++) {
                    if (isset($passengers['children'][$i])) {

                        $passenger = $passengers['children'][$i];
                        if (
                                !isset($passenger['FIRST_NAME']) ||
                                !isset($passenger['LAST_NAME']) ||
                                !isset($passenger['BIRTHDATE']) ||
                                !isset($passenger['MALE']) ||
                                strlen($passenger['FIRST_NAME']) < 2 ||
                                strlen($passenger['LAST_NAME']) < 2
                        ) {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }
            }

            return true;
        }
        return false;
    }

    /**
     * Include view
     * @param string $name
     * @param array $parameters
     * @return string
     */
    public static function includeView(string $name, array $parameters = []) {

        $viewPath = $_SERVER['DOCUMENT_ROOT'] . '/local/modules/travelsoft.sirenaintegration/views/' . $name . '/view.php';

        if (file_exists($viewPath)) {
            include $viewPath;
        }
    }

}
