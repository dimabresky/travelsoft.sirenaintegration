<?php

namespace travelsoft\sirenaintegration;

/**
 * Класс для управления бронированием в системе CИРЕНА АПК
 *
 * @author dimabresky
 */
class BookManager {

    public $table = null;

    public function __construct() {

        $this->table = HighloadblockAdapter::getTable('TSSIRENABOOK');
    }
}
