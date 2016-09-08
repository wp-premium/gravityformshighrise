<?php

	namespace Highrise;

	class HighriseInstantMessenger {
		
		public  $address;
		public  $id;
		private $location;
		private $protocol;
		
		public function __construct( $id = null, $protocol = null, $address = null, $location = null ) {
			
			$this->set_id( $id );
			$this->set_protocol( $protocol );
			$this->set_address( $address );		
			$this->set_location( $location );
							
		}
		
		public function __toString() {
			
			return $this->get_protocol() . ":" . $this->get_address();
			
		}

		public function create_xml(&$xml) {
			
            $xml->addChild( 'id', $this->get_id() );
            $xml->id->addAttribute( 'type', 'integer' );
            $xml->addChild( 'protocol', $this->get_protocol() );
            $xml->addChild( 'location', $this->get_location() );
            $xml->addChild( 'address', $this->get_address() );
			return $xml;
			
		}
		
		public function to_xml() {
			
			$xml = new \SimpleXMLElement("<instant-messenger></instant-messenger>");
			$xml = $this->create_xml($xml);
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
		
		public function get_protocol() {
			
			return $this->protocol;
			
		}

		public function set_address( $address ) {
			
			$this->address = (string)$address;
			
		}
		
		public function set_id( $id ) {
			
			$this->id = (string)$id;
			
		}

		public function set_location( $location ) {
			
			$valid_locations = array( 'Work', 'Personal', 'Other' );
			$location = ucwords( strtolower( $location ) );
			
			if ( ! is_null( $location ) && ! in_array( $location, $valid_locations ) ) {
				throw new \Exception( $location . ' is not a valid location. Available locations: ' . implode( ', ', $valid_locations ) );
			}
			
			$this->location = (string)$location;
		}
		
		public function set_protocol( $protocol ) {
			
			$valid_protocols = array( 'AIM', 'MSN', 'ICQ', 'Jabber', 'Yahoo', 'Skype', 'QQ', 'Sametime', 'Gadu-Gadu', 'Google Talk', 'Other' );
			
			if ( ! is_null( $protocol ) && ! in_array( $protocol, $valid_protocols ) ) {
				throw new \Exception( $protocol . ' is not a valid protocol. Available protocols: ' . implode( ', ', $valid_protocols ) );
			}
		
			$this->protocol = (string)$protocol;
			
		}
		
	}
	
