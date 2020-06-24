<?php

namespace travelsoft\sirenaintegration;

use Bitrix\Highloadblock\HighloadBlockTable as HL;

\Bitrix\Main\Loader::includeModule('highloadblock');

class HighloadblockAdapter {

    /**
     * @return string
     */
    public static function getTable(string $table) {

        static $tables = [];

        if (!$tables[$table]) {
            $dbTableData = HL::getList(['filter' => ['NAME' => $table]])->fetch();
            $className = HL::compileEntity($dbTableData['ID'])->getDataClass();

            $tables[$table] = new $className;
        }

        return $tables[$table];
    }

    /**
     * @param array $filter
     * @return int
     */
    public static function getRowsCount(string $table, array $filter = []) {

        $res = self::getTable($table)->getList(['filter' => $filter, 'select' => [new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(1)')]])->fetch();

        return intval($res['CNT']);
    }

}
