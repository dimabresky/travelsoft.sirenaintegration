<?php
if (!$USER->isAdmin())
    return;

$mid = "travelsoft.sirenaintegration";

global $APPLICATION;

function renderOptions($arOptions, $mid) {

    foreach ($arOptions as $name => $arValues) {

        $cur_opt_val = htmlspecialcharsbx(Bitrix\Main\Config\Option::get($mid, $name));
        $name = htmlspecialcharsbx($name);

        $options .= '<tr>';
        $options .= '<td width="40%">';
        $options .= '<label for="' . $name . '">' . $arValues['DESC'] . ' ' . (($cur_opt_val > 0 && $name === "CONTRACT_FILE_ID" ? "<br><b>Текущий загруженный договор: " . (CFile::GetByID($cur_opt_val)->Fetch())["ORIGINAL_NAME"] . "</b>" : "")) . ':</label>';
        $options .= '</td>';
        $options .= '<td width="60%">';
        if ($arValues['TYPE'] == 'select') {

            $options .= '<select id="' . $name . '" name="' . $name . '">';
            foreach ($arValues['VALUES'] as $key => $value) {
                $options .= '<option ' . ($cur_opt_val == $key ? 'selected' : '') . ' value="' . $key . '">' . $value . '</option>';
            }
            $options .= '</select>';
        } elseif ($arValues['TYPE'] == 'text') {

            $options .= '<input type="text" name="' . $name . '" value="' . $cur_opt_val . '">';
        } elseif ($arValues['TYPE'] == 'checkbox') {

            $options .= '<input type="hidden" name="' . $name . '" value="N">';
            $options .= '<input type="checkbox" ' . ($cur_opt_val === "Y" ? "checked" : "") . ' name="' . $name . '" value="Y">';
        } elseif ($arValues['TYPE'] == 'file') {

            $options .= '<input type="file" name="' . $name . '">';
        }
        $options .= '</td>';
        $options .= '</tr>';
    }
    echo $options;
}

$main_options = array(
    'STORES' => array(
        "SIRENAINTEGRATION_ADDRESS" => array("DESC" => "Адрес подключения(ip)", 'TYPE' => 'text'),
        "SIRENAINTEGRATION_PORT" => array("DESC" => "Порт подключения", 'TYPE' => 'text'),
        "SIRENAINTEGRATION_CLIENT_ID" => array("DESC" => "ID пользователя", 'TYPE' => 'text')
    )
);

$tabs = array(
    array(
        "DIV" => "edit1",
        "TAB" => "Настройки модуля",
        "ICON" => "",
        "TITLE" => "Настройки модуля"
    ),
);

$o_tab = new CAdminTabControl("TravelsoftTabControl", $tabs);
if ($REQUEST_METHOD == "POST" && strlen($save . $reset) > 0 && check_bitrix_sessid()) {

    if (strlen($reset) > 0) {
        foreach ($main_options as $arBlockOption) {
            foreach (array_keys($arBlockOption) as $name) {
                \Bitrix\Main\Config\Option::set($mid, $name, '');
            }
        }
    } else {
        foreach ($main_options as $arBlockOption) {
            foreach (array_keys($arBlockOption) as $name) {
                \Bitrix\Main\Config\Option::set($mid, $name, trim($_REQUEST[$name]));
            }
        }
    }

    LocalRedirect($APPLICATION->GetCurPage() . "?mid=" . urlencode($mid) . "&lang=" . urlencode(LANGUAGE_ID) . "&" . $o_tab->ActiveTabParam());
}
$o_tab->Begin();
?>

<form enctype="multipart/form-data" method="post" action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($mid) ?>&amp;lang=<? echo LANGUAGE_ID ?>">
    <?
    foreach ($main_options as $tab_name => $arOption) {

        $o_tab->BeginNextTab();
        renderOptions($arOption, $mid);
    }
    $o_tab->Buttons();
    ?>
    <input type="submit" name="save" value="Сохранить" title="Сохранить" class="adm-btn-save">
    <input type="submit" name="reset" title="Сбросить" OnClick="return confirm('Уверены ?')" value="Сбросить">
    <?= bitrix_sessid_post(); ?>
    <? $o_tab->End(); ?>
</form>
