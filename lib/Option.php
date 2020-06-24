<?php

namespace travelsoft\sirenaintegration;

class Option {

    const MODULE_NAME = 'travelsoft.sirenaintegration';
    
    const PROPERTY_SINA_IBLOCK_CODE = "SIRENA_CODE";
    
    public static function get(string $optionName) {

        return (string) \Bitrix\Main\Config\Option::get(self::MODULE_NAME, $optionName);
    }

}
