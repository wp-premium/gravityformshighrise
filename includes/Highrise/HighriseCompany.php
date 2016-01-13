<?php

	namespace Highrise;

	class HighriseCompany extends HighriseEntity {
		
		public $name;

		public function __construct( HighriseAPI $highrise ) {
			
			parent::__construct( $highrise );
			$this->url_base   = 'companies';
			$this->error_check = 'Company';
			$this->setType( 'Company' );
			
		}

		public function create_xml( $xml ) {
			
			$xml = parent::create_xml( $xml );
			$xml->name = $this->get_name();
			
			return $xml;
			
		}

		public function to_xml( $header = 'company' ) {

			$xml = new \SimpleXMLElement( '<' . $header . '></' . $header . '>' );
			$xml = $this->create_xml( $xml );
			
			return $xml->asXML();
			
		}
		
		public function load_from_xml_object( $xml_object ) {

			parent::load_from_xml_object($xml_obj);
			$this->set_name( $xml_obj->{'name'} );
		}
		
		public function get_name() {
			
			return $this->name;
			
		}

		public function set_name( $name ) {
			
			$this->name = (string)$name;
			
		}

	}
