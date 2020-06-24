<?php

namespace travelsoft\sirenaintegration;

/**
 * xml creation data
 */
class Xml {

    /**
     * Xml object for request
     * @var object
     */
    public $xml;

    /**
     * Begin constructor xml
     */
    public function createBegin() {
        $this->xml = xmlwriter_open_memory();
        /* xmlwriter_set_indent($this->xml, 0);
          xmlwriter_set_indent_string($this->xml, ''); */
        xmlwriter_start_document($this->xml, '1.0', 'UTF-8');

        xmlwriter_start_element($this->xml, 'sirena');
        xmlwriter_start_element($this->xml, 'query');
    }

    /**
     * End construcor xml
     */
    public function createEnd() {
        xmlwriter_end_element($this->xml);
        xmlwriter_end_element($this->xml);

        xmlwriter_end_document($this->xml);
    }

    /**
     * Describe
     */
    public function describe($data) {
        self::createBegin();
        xmlwriter_start_element($this->xml, 'describe');
        xmlwriter_start_element($this->xml, 'data');
        xmlwriter_text($this->xml, $data['type']);
        xmlwriter_end_element($this->xml);
        xmlwriter_end_element($this->xml);
        self::createEnd();

        $this->xml = xmlwriter_output_memory($this->xml);

        return $this->xml;
    }

    /**
     * Describe
     */
    public function order($data) {
        self::createBegin();
        xmlwriter_start_element($this->xml, 'order');

        xmlwriter_start_element($this->xml, 'surmane');
        xmlwriter_text($this->xml, $data['surname']);
        xmlwriter_end_element($this->xml);

        xmlwriter_start_element($this->xml, 'regnum');
        xmlwriter_text($this->xml, $data['regnum']);
        xmlwriter_end_element($this->xml);

        xmlwriter_start_element($this->xml, 'answer_params');

        xmlwriter_start_element($this->xml, 'lang');
        xmlwriter_text($this->xml, "en");
        xmlwriter_end_element($this->xml);

        xmlwriter_start_element($this->xml, 'tickinfo');
        xmlwriter_text($this->xml, "true");
        xmlwriter_end_element($this->xml);

        xmlwriter_start_element($this->xml, 'show_actions');
        xmlwriter_text($this->xml, "true");
        xmlwriter_end_element($this->xml);

        xmlwriter_start_element($this->xml, 'add_common_status');
        xmlwriter_text($this->xml, "true");
        xmlwriter_end_element($this->xml);

        xmlwriter_end_element($this->xml);

        xmlwriter_end_element($this->xml);
        self::createEnd();

        $this->xml = xmlwriter_output_memory($this->xml);

        return $this->xml;
    }

    /**
     * Get company routes
     * @param string $company Company code
     * @param string $lang
     * @return string
     */
    public function companyRoutes($data) {
        self::createBegin();
        xmlwriter_start_element($this->xml, 'get_company_routes');

        xmlwriter_start_element($this->xml, 'company');
        xmlwriter_text($this->xml, $data['company']);
        xmlwriter_end_element($this->xml);

        xmlwriter_start_element($this->xml, 'answer_params');
        xmlwriter_start_element($this->xml, 'lang');
        xmlwriter_text($this->xml, $data['lang']);
        xmlwriter_end_element($this->xml);
        xmlwriter_end_element($this->xml);

        xmlwriter_end_element($this->xml);
        self::createEnd();

        $this->xml = xmlwriter_output_memory($this->xml);

        return $this->xml;
    }

    /**
     * Get shedule
     * @return string
     */
    public function schedule($data) {
        self::createBegin();
        xmlwriter_start_element($this->xml, 'get_schedule');

        xmlwriter_start_element($this->xml, 'departure');
        xmlwriter_text($this->xml, $data['departure']);
        xmlwriter_end_element($this->xml);

        xmlwriter_start_element($this->xml, 'company');
        xmlwriter_text($this->xml, $data['company']);
        xmlwriter_end_element($this->xml);

        xmlwriter_start_element($this->xml, 'date');
        xmlwriter_text($this->xml, date('d.m.y'));
        xmlwriter_end_element($this->xml);

        xmlwriter_start_element($this->xml, 'date2');
        xmlwriter_text($this->xml, date('d.m.y', strtotime('+1 week')));
        xmlwriter_end_element($this->xml);

        xmlwriter_start_element($this->xml, 'direct');
        xmlwriter_text($this->xml, 'false');
        xmlwriter_end_element($this->xml);

        // xmlwriter_start_element($this->xml, 'answer_params');
        // 	xmlwriter_start_element($this->xml, 'lang');
        // 		xmlwriter_text($this->xml, $data['lang']);
        // 	xmlwriter_end_element($this->xml);
        // xmlwriter_end_element($this->xml);

        xmlwriter_end_element($this->xml);
        self::createEnd();

        $this->xml = xmlwriter_output_memory($this->xml);

        return $this->xml;
    }

    /**
     * Availability
     */
    public function availability($data) {
        self::createBegin();
        xmlwriter_start_element($this->xml, 'availability');

        xmlwriter_start_element($this->xml, 'departure');
        xmlwriter_text($this->xml, $data['departure']);
        xmlwriter_end_element($this->xml);

        xmlwriter_start_element($this->xml, 'arrival');
        xmlwriter_text($this->xml, $data['arrival']);
        xmlwriter_end_element($this->xml);

        xmlwriter_start_element($this->xml, 'date');
        xmlwriter_text($this->xml, $data['date']);
        xmlwriter_end_element($this->xml);

        xmlwriter_start_element($this->xml, 'company');
        xmlwriter_text($this->xml, $data['company']);
        xmlwriter_end_element($this->xml);

        xmlwriter_start_element($this->xml, 'show_baseclass');
        xmlwriter_text($this->xml, $data['show_baseclass'] ? 'true' : 'false');
        xmlwriter_end_element($this->xml);

        xmlwriter_start_element($this->xml, 'direct');
        xmlwriter_text($this->xml, $data['direct'] ? 'true' : 'false');
        xmlwriter_end_element($this->xml);

        if ($data['flight']) {
            xmlwriter_start_element($this->xml, 'flight');
            xmlwriter_text($this->xml, $data['flight']);
            xmlwriter_end_element($this->xml);
        }

        if ($data['subclass']) {
            xmlwriter_start_element($this->xml, 'subclass');
            xmlwriter_text($this->xml, $data['subclass']);
            xmlwriter_end_element($this->xml);
        }


        xmlwriter_start_element($this->xml, 'request_params');
        xmlwriter_start_element($this->xml, 'lang');
        xmlwriter_text($this->xml, 'en');
        xmlwriter_end_element($this->xml);
        xmlwriter_end_element($this->xml);

        xmlwriter_start_element($this->xml, 'answer_params');
        xmlwriter_start_element($this->xml, 'show_flighttime');
        xmlwriter_text($this->xml, $data['show_flighttime'] ? 'true' : 'false');
        xmlwriter_end_element($this->xml);
        xmlwriter_start_element($this->xml, 'lang');
        xmlwriter_text($this->xml, 'en');
        xmlwriter_end_element($this->xml);
        xmlwriter_end_element($this->xml);

        xmlwriter_end_element($this->xml);
        self::createEnd();

        $this->xml = xmlwriter_output_memory($this->xml);

        return $this->xml;
    }

    /**
     * Pricing flight
     */
    public function pricing_flight(array $data) {

        self::createBegin();
        xmlwriter_start_element($this->xml, 'pricing_flight');

        xmlwriter_start_element($this->xml, 'segment');

        xmlwriter_start_element($this->xml, 'departure');
        xmlwriter_text($this->xml, $data['departure']);
        xmlwriter_end_element($this->xml);

        xmlwriter_start_element($this->xml, 'arrival');
        xmlwriter_text($this->xml, $data['arrival']);
        xmlwriter_end_element($this->xml);

        xmlwriter_start_element($this->xml, 'date');
        xmlwriter_text($this->xml, $data['date']);
        xmlwriter_end_element($this->xml);

        xmlwriter_start_element($this->xml, 'company');
        xmlwriter_text($this->xml, $data['company']);
        xmlwriter_end_element($this->xml);

        xmlwriter_start_element($this->xml, 'num');
        xmlwriter_text($this->xml, $data['num']);
        xmlwriter_end_element($this->xml);

        xmlwriter_start_element($this->xml, 'direct');
        xmlwriter_text($this->xml, $data['direct'] ? 'true' : 'false');
        xmlwriter_end_element($this->xml);

        if ($data['class']) {
            xmlwriter_start_element($this->xml, 'subclass');
            xmlwriter_text($this->xml, $data['subclass']);
            xmlwriter_end_element($this->xml);
        }

        xmlwriter_end_element($this->xml); // segment

        foreach ($data['passengers'] as $passenger) {
            xmlwriter_start_element($this->xml, 'passenger');

            xmlwriter_start_element($this->xml, 'code');
            xmlwriter_text($this->xml, $passenger['code']);
            xmlwriter_end_element($this->xml); // code

            xmlwriter_start_element($this->xml, 'count');
            xmlwriter_text($this->xml, $passenger['count'] ?: 1);
            xmlwriter_end_element($this->xml); // count

            if ($passenger['age']) {
                xmlwriter_start_element($this->xml, 'age');
                xmlwriter_text($this->xml, $passenger['age']);
                xmlwriter_end_element($this->xml); // age
            }

            xmlwriter_end_element($this->xml); // passenger
        }

        xmlwriter_start_element($this->xml, 'answer_params');

        xmlwriter_start_element($this->xml, 'curr');
        xmlwriter_text($this->xml, $data['curr']);
        xmlwriter_end_element($this->xml); // curr

        xmlwriter_start_element($this->xml, 'lang');
        xmlwriter_text($this->xml, 'en');
        xmlwriter_end_element($this->xml); // lang

        xmlwriter_end_element($this->xml); // answer_params

        xmlwriter_start_element($this->xml, 'request_params');
        xmlwriter_start_element($this->xml, 'lang');
        xmlwriter_text($this->xml, 'en');
        xmlwriter_end_element($this->xml);
        xmlwriter_end_element($this->xml); // request_params

        xmlwriter_end_element($this->xml); // pricing_flight

        self::createEnd();

        $this->xml = xmlwriter_output_memory($this->xml);

        return $this->xml;
    }

    public function booking($data) {
        self::createBegin();

        xmlwriter_start_element($this->xml, 'booking');

        foreach ($data['segments'] as $segment) {
            xmlwriter_start_element($this->xml, 'segment');

            xmlwriter_start_element($this->xml, 'departure');
            xmlwriter_text($this->xml, $segment['departure']);
            xmlwriter_end_element($this->xml);

            xmlwriter_start_element($this->xml, 'arrival');
            xmlwriter_text($this->xml, $segment['arrival']);
            xmlwriter_end_element($this->xml);

            xmlwriter_start_element($this->xml, 'date');
            xmlwriter_text($this->xml, $segment['date']);
            xmlwriter_end_element($this->xml);

            xmlwriter_start_element($this->xml, 'company');
            xmlwriter_text($this->xml, $segment['company']);
            xmlwriter_end_element($this->xml);

            xmlwriter_start_element($this->xml, 'flight');
            xmlwriter_text($this->xml, $segment['flight']);
            xmlwriter_end_element($this->xml);

            xmlwriter_start_element($this->xml, 'subclass');
            xmlwriter_text($this->xml, $segment['subclass']);
            xmlwriter_end_element($this->xml);

            xmlwriter_end_element($this->xml); // segment
        }


        foreach ($data['passengers'] as $passeger) {
            xmlwriter_start_element($this->xml, 'passenger');
            xmlwriter_start_element($this->xml, 'lastname');
            xmlwriter_text($this->xml, $passeger['lastname']);
            xmlwriter_end_element($this->xml);

            xmlwriter_start_element($this->xml, 'firstname');
            xmlwriter_text($this->xml, $passeger['firstname']);
            xmlwriter_end_element($this->xml);

            xmlwriter_start_element($this->xml, 'birthdate');
            xmlwriter_text($this->xml, $passeger['birthdate']);
            xmlwriter_end_element($this->xml);

            xmlwriter_start_element($this->xml, 'sex');
            xmlwriter_text($this->xml, $passeger['sex']);
            xmlwriter_end_element($this->xml);

            xmlwriter_start_element($this->xml, 'category');
            xmlwriter_text($this->xml, $passeger['category']);
            xmlwriter_end_element($this->xml);

            if ($passeger['phone']) {
                xmlwriter_start_element($this->xml, 'phone');
                xmlwriter_start_attribute($this->xml, 'type');
                xmlwriter_text($this->xml, 'mobile');
                xmlwriter_end_attribute($this->xml);
                xmlwriter_text($this->xml, $passeger['phone']);
                xmlwriter_end_element($this->xml);
            }


            xmlwriter_end_element($this->xml);
        }

        xmlwriter_start_element($this->xml, 'customer');
        xmlwriter_start_element($this->xml, 'phone');
        xmlwriter_text($this->xml, $data['passengers'][0]['phone']);
        xmlwriter_end_attribute($this->xml);
        xmlwriter_end_element($this->xml);

        xmlwriter_start_element($this->xml, 'answer_params');

        xmlwriter_start_element($this->xml, 'add_remarks');
        xmlwriter_text($this->xml, "true");
        xmlwriter_end_element($this->xml);


        xmlwriter_start_element($this->xml, 'lang');
        xmlwriter_text($this->xml, "en");
        xmlwriter_end_element($this->xml);
        xmlwriter_end_element($this->xml);

        xmlwriter_start_element($this->xml, 'request_params');


        xmlwriter_start_element($this->xml, 'allow_waitlist');
        xmlwriter_text($this->xml, 'false');
        xmlwriter_end_element($this->xml);

        xmlwriter_start_element($this->xml, 'lang');
        xmlwriter_text($this->xml, 'en');
        xmlwriter_end_element($this->xml);
        xmlwriter_end_element($this->xml); // request_params

        xmlwriter_end_element($this->xml); // booking

        self::createEnd();

        $this->xml = xmlwriter_output_memory($this->xml);
        return $this->xml;
    }

    /**
     * This is function transform an xml to an array
     * @param string|\SimpleXMLElement $xml
     * @return array
     */
    static function xml2array($xml, $convert = true) {

        if ($convert) {
            $xmlObject = new \SimpleXMLElement($xml);
        } else {
            $xmlObject = $xml;
        }

        $out = [];
        foreach ((array) $xmlObject as $index => $node) {
            $out[$index] = (is_object($node)) ? self::xml2array($node, false) : $node;
        }

        return $out;
    }

}
