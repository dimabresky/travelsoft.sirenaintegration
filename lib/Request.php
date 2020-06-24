<?php

namespace travelsoft\sirenaintegration;

/**
 * request class
 * 
 * @author dimabresky
 */
class Request {

    public $output = '';
    protected $_address = null;
    protected $_port = null;
    protected $_clientId = null;

    public function __construct() {
        $this->_address = Option::get('SIRENAINTEGRATION_ADDRESS');
        if (!$this->_address) {
            throw new Exception('Не указан адрес подключения');
        }
        $this->_port = Option::get('SIRENAINTEGRATION_PORT');
        if (!$this->_port) {
            throw new Exception('Не указан порт подключения');
        }
        $this->_clientId = Option::get('SIRENAINTEGRATION_CLIENT_ID');
        if (!$this->_clientId) {
            throw new Exception('Не указан ID клиента');
        }
    }

    public function send(string $requestType, array $data) {

        $xml = $this->createXml($requestType, $data);
        $errno = '';
        $errstr = '';

        $socket = fsockopen("tcp://{$this->_address}", $this->_port, $errno, $errstr, 2);

        $message = $this->createHeader($xml) . $xml;

        fwrite($socket, $message);
        stream_set_blocking($socket, TRUE);
        stream_set_timeout($socket, 2);
        $info = stream_get_meta_data($socket);
        $this->output = '';
        while ((!feof($socket)) && (!$info['timed_out'])) {
            $this->output .= fgets($socket, 4096);
            $info = stream_get_meta_data($socket);
            flush();
        }

        fclose($socket);
        
        return Xml::xml2array(trim(substr($this->output, strpos($this->output, '<') - 5)));
    }

    public function createXml(string $requestType, array $data) {

        $oXml = new Xml();

        if (!method_exists($oXml, $requestType)) {
            throw new Exception('Неизвестный тип запроса.');
        }

        return $oXml->$requestType($data);
    }

    public function createHeader(string $xml) {

        $head = pack('N*', strlen($xml)); // xml_len
        $head .= pack('N*', time()); // create_time
        $head .= pack('N*', 2); // message_id
        $head .= str_repeat("\0", 32); // null32
        $head .= pack('n*', $this->_clientId); // client_id
        $head .= "\0"; // firstfl
        $head .= "\0"; // secondfl
        $head .= str_repeat("\0", 52); // null52
        return $head;
    }

}
