<?php

use Bitrix\Main\ModuleManager,
    Bitrix\Main\Config\Option;

class travelsoft_sirenaintegration extends CModule {

    public $MODULE_ID = "travelsoft.sirenaintegration";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_GROUP_RIGHTS = "N";
    
    public $highloadblocksFiles = [
        'sirenabook.php'
    ];

    function __construct() {
        $arModuleVersion = array();
        $path_ = str_replace("\\", "/", __FILE__);
        $path = substr($path_, 0, strlen($path_) - strlen("/index.php"));
        include($path . "/version.php");
        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
        $this->MODULE_NAME = "TS:Интеграция с АПК СИРЕНА";
        $this->MODULE_DESCRIPTION = "Интеграция с АПК СИРЕНА";
        $this->PARTNER_NAME = "TRAVELSOFT";
        $this->PARTNER_URI = "http://travelsoft.by/";


        set_time_limit(0);
    }

    public function DoInstall() {
        try {

            $errors = [];
            # проверка зависимостей модуля
            if (!ModuleManager::isModuleInstalled("travelsoft.travelbooking")) {
                $errors[] = "Для работы модуля необходимо наличие установленного модуля TS:Operator";
            }

            if (isset($errors) && !empty($errors)) {
                throw new Exception(implode("<br>", $errors));
            }

            # регистрируем модуль
            ModuleManager::registerModule($this->MODULE_ID);

            # создание higloadblock модуля
            $this->createHighloadblockTables();

            # добавление зависимостей модуля
            $this->addModuleDependencies();

            # добавление параметров модуля
            $this->addOptions();
        } catch (Exception $ex) {

            $GLOBALS["APPLICATION"]->ThrowException($ex->getMessage());

            $this->DoUninstall();

            return false;
        }

        return true;
    }

    public function DoUninstall() {

        # удаляем зависимости модуля
        $this->deleteModuleDependencies();

        # удаление таблиц higloadblock
        $this->deleteHighloadblockTables();

        # удаление параметров модуля
        $this->deleteOptions();

        ModuleManager::unRegisterModule($this->MODULE_ID);

        return true;
    }

    public function addOptions() {
        Option::set($this->MODULE_ID, "SIRENAINTEGRATION_ADDRESS");
        Option::set($this->MODULE_ID, "SIRENAINTEGRATION_PORT");
        Option::set($this->MODULE_ID, "SIRENAINTEGRATION_CLIENT_ID");
    }

    public function deleteOptions() {
        Option::delete($this->MODULE_ID, array("name" => "SIRENAINTEGRATION_ADDRESS"));
        Option::delete($this->MODULE_ID, array("name" => "SIRENAINTEGRATION_PORT"));
        Option::delete($this->MODULE_ID, array("name" => "SIRENAINTEGRATION_CLIENT_ID"));
    }

    public function createHighloadblockTables() {

        foreach ($this->highloadblocksFiles as $file) {

            $arr = include "highloadblocks/" . $file;

            $result = Bitrix\Highloadblock\HighloadBlockTable::add(array(
                        'NAME' => $arr["table_data"]["NAME"],
                        'TABLE_NAME' => $arr["table"]
            ));

            if (!$result->isSuccess()) {
                throw new Exception($arr["table_data"]['ERR'] . "<br>" . implode("<br>", (array) $result->getErrorMessages()));
            }

            $table_id = $result->getId();

            $arr_fields = $arr["fields"];

            $oUserTypeEntity = new CUserTypeEntity();

            foreach ($arr_fields as $arr_field) {

                $arr_field["ENTITY_ID"] = str_replace("{{table_id}}", $table_id, $arr_field["ENTITY_ID"]);

                if (!$oUserTypeEntity->Add($arr_field)) {
                    throw new Exception("Возникла ошибка при добавлении свойства " . $arr_field["ENTITY_ID"] . "[" . $arr_field["FIELD_NAME"] . "]" . $oUserTypeEntity->LAST_ERROR);
                }
            }

            if (isset($arr["items"]) && !empty($arr["items"])) {

                $entity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity(
                                Bitrix\Highloadblock\HighloadBlockTable::getById($table_id)->fetch());
                $class = $entity->getDataClass();
                foreach ($arr["items"] as $item) {
                    $class::add($item);
                }
            }
        }
    }

    public function deleteHighloadblockTables() {

        foreach ($this->highloadblocksFiles as $file) {

            $arr = include "highloadblocks/" . $file;

            $dbTableData = HL::getList(['filter' => ['NAME' => $arr["table_data"]['NAME']]])->fetch();

            if ($dbTableData['ID'] > 0) {
                Bitrix\Highloadblock\HighloadBlockTable::delete($dbTableData['ID']);
            }
        }
    }

    public function addModuleDependencies() {
        RegisterModuleDependences("travelsoft.travelbooking", "beforeServiceBookDelete", $this->MODULE_ID, "\\travelsoft\\sirenaintegration\\EventsHandlers", "onBeforeTsOperatorServiceBookDelete");
        RegisterModuleDependences("travelsoft.travelbooking", "beforeServiceBookCreate", $this->MODULE_ID, "\\travelsoft\\sirenaintegration\\EventsHandlers", "onBeforeTsOperatorServiceBookCreate");        
        RegisterModuleDependences("travelsoft.travelbooking", "afterServiceBookCreate", $this->MODULE_ID, "\\travelsoft\\sirenaintegration\\EventsHandlers", "onAfterTsOperatorServiceBookCreate");        
        RegisterModuleDependences("travelsoft.travelbooking", "actualizationPackagetourOffer", $this->MODULE_ID, "\\travelsoft\\sirenaintegration\\EventsHandlers", "onActualizationPackagetourOffer");
    }

    public function deleteModuleDependencies() {
        UnRegisterModuleDependences("travelsoft.travelbooking", "beforeServiceBookDelete", $this->MODULE_ID, "\\travelsoft\\sirenaintegration\\EventsHandlers", "onBeforeTsOperatorServiceBookDelete");
        UnRegisterModuleDependences("travelsoft.travelbooking", "beforeServiceBookCreate", $this->MODULE_ID, "\\travelsoft\\sirenaintegration\\EventsHandlers", "onBeforeTsOperatorServiceBookCreate");
        UnRegisterModuleDependences("travelsoft.travelbooking", "afterServiceBookCreate", $this->MODULE_ID, "\\travelsoft\\sirenaintegration\\EventsHandlers", "onAfterTsOperatorServiceBookCreate");        
        UnRegisterModuleDependences("travelsoft.travelbooking", "actualizationPackagetourOffer", $this->MODULE_ID, "\\travelsoft\\sirenaintegration\\EventsHandlers", "onActualizationPackagetourOffer");
    }

}
