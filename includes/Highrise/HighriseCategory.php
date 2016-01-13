<?php

	namespace Highrise;

	class HighriseCategory extends HighriseAPI {
 
		protected $type;
		public    $id;
		public    $name;
		public    $account_id;
		public    $color;
		public    $created_at;
		public    $elements_count;
		public    $updated_at;

		public function __construct( HighriseAPI $highrise, $type ) {
			
			$this->highrise = $highrise;
			$this->type = $type;
			
		}
		
		public function __toString() {
			
			return $this->name . ": " . $this->id;

		}
		
		protected function getXMLObject() {
			
			$xml = new \SimpleXMLElement( '<' . $this->type . '_category></' . $this->type . '_category>' );
			$xml->addChild( 'id', $this->get_id() );
			$xml->id->addAttribute( 'type', 'integer' );
			$xml->addChild( 'name', $this->get_name() );
			$xml->addChild( 'account-id', $this->get_account_id() );
			$xml->addChild( 'color', $this->get_color() );
			$xml->addChild( 'created-at', $this->get_created_at() );
			$xml->addChild( 'elements-count', $this->get_elements_count() );
			$xml->addChild( 'updated-at', $this->get_updated_at() );
			
			return $xml;
		
		}

		public function load_from_xml_object( $xml_object ) {
			
			$this->set_id( $xml_object->{'id'} );
			$this->set_name( $xml_object->{'name'} );
			$this->set_account_id( $xml_object->{'account-id'} );
			$this->set_color( $xml_object->{'color'} );
			$this->set_created_at( $xml_object->{'created-at'} );
			$this->set_elements_count( $xml_object->{'elements-count'} );
			$this->set_updated_at( $xml_object->{'updated-at'} );
			
			return true;
			
		}

		public function to_xml() {
			
			return $this->getXMLObject()->asXML();
		
		}
		
		public function get_account_id() {
			
			return $this->account_id;
		
		}
		
		public function get_color() {
		
			return $this->color;
		
		}
		
		public function get_created_at() {
		
			return $this->created_at;
		
		}
		
		public function get_elements_count() {
		
			return $this->elements_count;
		
		}
		
		public function get_id() {
		
			return $this->id;
		
		}
		
		public function get_name() {
		
			return $this->name;
		
		}
		
		public function get_type() {
		
			return $this->type;
		
		}
		
		public function get_updated_at() {
		
			return $this->updated_at;
		
		}

		protected function set_account_id( $account_id ) {
		
			$this->account_id = (string)$account_id;
		
		}
		
		public function set_color( $color ) {
		
			$this->color = (string)$color;
		
		}
		
		protected function set_created_at( $created_at ) {
		
			$this->created_at = (string)$created_at;
		
		}
		
		protected function set_elements_count( $elements_count ) {
		
			$this->elements_count = (string)$elements_count;
		
		}
		
		protected function set_id( $id ) {
		
			$this->id = (string)$id;
		
		}
		
		public function set_name( $name ) {
		
			$this->name = (string)$name;
		
		}
		
		protected function set_type( $type ) {
		
			$this->type = (string)$type;
		
		}

		protected function set_updated_at( $updated_at ) {
		
			$this->updated_at = (string)$updated_at;
		
		}
		
	}
