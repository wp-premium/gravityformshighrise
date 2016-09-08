<?php
	
	namespace Highrise;

	class HighriseTwitterAccount {
		
		public $id;
		public $location;
		public $username;
		
		public function __construct( $id = null, $username = null, $location = null ) {
			
			$this->set_id( $id );
			$this->set_username( $username );
			$this->set_location( $location );
					
		}
		
		public function create_xml( &$xml ) {
			
			$xml->addChild( 'id', $this->get_id() );
			$xml->id->addAttribute( 'type', 'integer' );
			$xml->addChild( 'username', $this->get_username() );
			$xml->addChild( 'location', $this->get_location() );
			
			return $xml;
			
		}

		public function to_xml() {
			
			$xml = new \SimpleXMLElement( '<twitter-account></twitter-account>' );
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
			
			return 'http://twitter.com/' . $this->get_username();
			
		}
		
		public function get_username() {
			
			return $this->username;
			
		}
		
		public function set_id( $id ) {
			
			$this->id = (string)$id;
			
		}

		public function set_location( $location ) {
			
			$valid_locations = array( 'Business', 'Personal', 'Other' );
			$location = ucwords( strtolower( $location ) );
			
			if ( ! is_null( $location ) && ! in_array( $location, $valid_locations ) )
				throw new \Exception( $location .' is not a valid location. Available locations: ' . implode( ', ', $valid_locations ) );
			
			$this->location = (string)$location;
			
		}

		public function set_url( $url ) {
		 	
		 	throw new \Exception("Cannot set URLs, change Username instead");
		
		}

		public function set_username( $username ) {
			
			$this->username = (string)$username;
			$this->url = $this->get_url();
			
		}

	}
	
