<?php

	namespace Highrise;

	class HighrisePhoneNumber {
		
		public $id;
		public $number;
		public $location;
	
		public function __construct( $id = null, $number = null, $location = null ) {
			
			$this->set_id( $id );
			$this->set_number( $number );
			$this->set_location( $location );
						
		}
		
		public function __toString() {
			
			return $this->number;
			
		}

		public function create_xml( &$xml ) { 
			
            $xml->addChild( 'id', $this->get_id() );
            $xml->id->addAttribute( 'type', 'integer' );
            $xml->number = $this->get_number();
            $xml->location = $this->get_location();
            
			return $xml;
			
		}
			
		public function to_xml() {

			$xml = new \SimpleXMLElement( '<phone-number></phone-number>' );
			$xml = $this->create_xml( $xml );
			
			return $xml->asXML();
			
		}
				
		public function get_id() {
			
			return $this->id;
			
		}
		
		public function get_location() {
			
			return $this->location;
			
		}
		
		public function get_number() {
			
			return $this->number;
			
		}
		
		public function set_id( $id ) {
			
			$this->id = (string)$id;
			
		}
		
		public function set_location( $location ) {
			
			$valid_locations = array( 'Work', 'Mobile', 'Fax', 'Pager', 'Home', 'Skype', 'Other' );
			$location = ucwords( strtolower( $location ) );
			
			if ( ! is_null( $location ) && ! in_array( $location, $valid_locations ) )
				throw new \Exception( $location . ' is not a valid location. Available locations: ' . implode( ', ', $valid_locations ) );
			
			$this->location = (string)$location;
			
		}

		public function set_number( $number ) {
			
			$this->number = (string)$number;
			
		}

	}
