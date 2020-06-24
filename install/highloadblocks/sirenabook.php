<?php

return array(
    "table" => "ts_sirena_book",
    "table_data" => array(
        "NAME" => "TSSIRENABOOK",
        "ERR" => "Ошибка при создании highloadblock'a бронировки в АПК СИРЕНА",
        "LANGS" => array(
            "ru" => 'Таблица бронировки в АПК СИРЕНА',
            "en" => "Books in sirena"
        )
    ),
    "fields" => [
        array(
            "ENTITY_ID" => 'HLBLOCK_{{table_id}}',
            "FIELD_NAME" => "UF_SIRENA_BOOK_ID",
            "USER_TYPE_ID" => 'string',
            "XML_ID" => "",
            "SORT" => 50,
            "MULTIPLE" => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => array(
                'DEFAULT_VALUE' => "",
                'SIZE' => '20',
                'ROWS' => 1,
                'MIN_LENGTH' => 0,
                'MAX_LENGTH' => 0,
                'REGEXP' => ''
            ),
        ),
        array(
            "ENTITY_ID" => 'HLBLOCK_{{table_id}}',
            "FIELD_NAME" => "UF_TS_BOOK_ID",
            "USER_TYPE_ID" => 'string',
            "XML_ID" => "",
            "SORT" => 100,
            "MULTIPLE" => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
        ),
        array(
            "ENTITY_ID" => 'HLBLOCK_{{table_id}}',
            "FIELD_NAME" => "UF_BOOK_DETAIL",
            "USER_TYPE_ID" => 'string',
            "XML_ID" => "",
            "SORT" => 100,
            "MULTIPLE" => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => array(
                'DEFAULT_VALUE' => "",
                'SIZE' => '20',
                'ROWS' => 1,
                'MIN_LENGTH' => 0,
                'MAX_LENGTH' => 0,
                'REGEXP' => ''
            )
        )
    ]
);
