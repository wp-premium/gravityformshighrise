<?php

	namespace Highrise;
	
	class HighriseWebAddress {
		
		public $id;
		public $location;
		public $url;
		
		public function __construct( $id = null, $url = null, $location = null ) {
			
			$this->set_id($id);
			$this->set_url($url);
			$this->set_location( $location );
			
		}

		public function create_xml( &$xml ) {
			
			$xml->addChild( 'id', $this->get_id() );
			$xml->id->addAttribute( 'type', 'integer' );
			$xml->location = $this->get_location();
			$xml->url = $this->get_url();
			
			return $xml;
			
		}
		
		public function to_xml() {
			
			$xml = new \SimpleXMLElement( '<web-address></web-address>' );
			$xml = $this->create_xml( $xml );
			
			return $xml->asXML;
			
		}
		
		public function get_id() {
			
			return $this->id;
			
		}
		
		public function get_location() {
			
			return $this->location;
			
		}
		
		public function get_url() {
			
			return $this->url;
			
		}

		
		public function set_id( $id ) {
			
			$this->id = (string)$id;
			
		}

		public function set_location( $location ) {
			
			$valid_locations = array( 'Work', 'Personal', 'Other' );
			$location = ucwords( strtolower( $location ) );
			
			if ( $location != null && ! in_array( $location, $valid_locations ) )
				throw new \Exception( $location . ' is not a valid location. Available locations: ' . implode( ', ', $valid_locations ) );
			
			$this->location = (string)$location;
			
		}
		
		public function set_url( $url ) {
			
			$this->url = (string)$url;
			
		}
		
	}
