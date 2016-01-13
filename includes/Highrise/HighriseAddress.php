<?php

	namespace Highrise;

	class HighriseAddress {
		
		public $id;
		public $city;
		public $country;
		public $location;
		public $state;
		public $street;
		public $zip;

		public function __toString() {
			
			return $this->get_full_address();
			
		}

		public function create_xml( &$xml ) {
			
			$xml->addChild( 'id', $this->get_id() );
			$xml->id->addAttribute( 'type', 'integer' );
			
			$xml->city     = $this->get_city();
			$xml->country  = $this->get_country();
			$xml->location = $this->get_location();
			$xml->state    = $this->get_state();
			$xml->street   = $this->get_street();
			$xml->zip      = $this->get_zip();
			
			return $xml;
			
		}
		
		public function to_xml() {
			
			$xml = new \SimpleXMLElement( '<address></address>' );
			$xml = $this->create_xml( $xml );
			
			return $xml->asXML();
			
		}
		
		public function get_city() {
			
			return $this->city;
			
		}
		
		public function get_country() {
			
			return $this->country;
			
		}
		
		public function get_full_address() {
			
			$full_address = '';
			
			if ( ! empty ( $this->street ) )
				$full_address .= $this->get_street() . ', ';
			
			if ( ! empty ( $this->city ) )
				$full_address .= $this->get_city() . ', ';

			if ( ! empty ( $this->state ) )
				$full_address .= $this->get_state() . ', ';

			if ( ! empty ( $this->zip ) )
				$full_address .= $this->get_zip() . ', ';

			if ( ! empty ( $this->country ) )
				$full_address .= $this->get_country() . ', ';
			
			if ( substr( $full_address, -2 ) == ', ' )
				$full_address = substr( $full_address, 0, -2 );
				
			return $full_address;
			
		}
		
		public function get_id() {
			
			return $this->id;
			
		}
		
		public function get_location() {
			
			return $this->location;
		
		}
		
		public function get_state() {
			
			return $this->state;
			
		}
		
		public function get_street() {
			
			return $this->street;
			
		}
		
		public function get_zip() {
			
			return $this->zip;
			
		}
		
		public function set_city( $city ) {
			
			$this->city = (string)$city;
			
		}

		public function set_country( $country ) {
			
			$this->country = (string)$country;
			
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
		
		public function set_state( $state ) {
			
			$this->state = (string)$state;
			
		}
		
		public function set_street( $street ) {
			
			$this->street = (string)$street;
			
		}
		
		public function set_zip( $zip ) {
			
			$this->zip = (string)$zip;
			
		}

	}
