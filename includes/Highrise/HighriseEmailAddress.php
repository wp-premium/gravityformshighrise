<?php

	namespace Highrise;

	class HighriseEmailAddress  {
		
		public $id;
		public $address;
		public $location;
		
		public function __construct( $id = null, $address = null, $location = null ) {
			
			$this->set_id( $id );
			$this->set_address( $address );
			$this->set_location( $location );
			
		}

		public function __toString() {
			
			return $this->get_address();
			
		}

		public function create_xml( &$xml ) {
			
			$xml->addChild( 'id', $this->get_id() );
			$xml->id->addAttribute( 'type', 'integer' );
			$xml->addChild( 'address', $this->get_address() );
			$xml->addChild( 'location', $this->get_location() );
			
			return $xml;
			
		}
				
		public function to_xml() {
			
			$xml = new \SimpleXMLElement( '<email-address></email-address>' );
			$xml = $this->create_xml( $xml );
			
			return $xml->asXML();
			
		}
		
		public function get_address() {
			
			return $this->address;
			
		}

		public function get_id() {
			
			return $this->id;
			
		}

		public function get_location() {
			
			return $this->location;
			
		}

		public function set_address( $address ) {
			
			$this->address = (string)$address;
			
		}

		public function set_id( $id ) {
			
			$this->id = (string)$id;
			
		}
		
		public function set_location( $location ) {

			$valid_locations = array( 'Work', 'Home', 'Other' );
			$location = ucwords( strtolower( $location ) );
			
			if ( ! is_null( $location ) && ! in_array( $location, $valid_locations ) )
				throw new \Exception( $location . ' is not a valid location. Available locations: ' . implode( ', ', $valid_locations ) );
				
			$this->location = (string)$location;
			
		}
		
	}
