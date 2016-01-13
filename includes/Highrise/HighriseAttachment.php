<?php

	namespace Highrise;

	class HighriseAttachment {
		
		public $id;
		public $url;
		public $name;
		public $size;

		public function __construct( $id = null, $url = null, $name = null, $size = null ) {
			
			$this->setId($id);
			$this->setName($name);
			$this->setUrl($url);
			$this->setSize($size);
			
		}

		public function __toString() {
			
			return $this->get_name();
			
		}

		public function create_xml( &$xml ) {
			
			$xml->addChild( 'id', $this->get_id() );
			$xml->id->addAttribute( 'type', 'integer' );
			$xml->addChild( 'url', $this->get_url() );
			$xml->addChild( 'name', $this->get_name() );
			$xml->addChild( 'size', $this->get_size() );
			$xml->size->addAttribute( 'type', 'integer' );
			
			return $xml;
			
		}

		public function to_xml() {
			
			$xml = new \SimpleXMLElement( '<attachment></attachment>');
			$xml = $this->create_xml( $xml );
			
			return $xml->asXML();
			
		}

		public function get_id() {
			
			return $this->id;
			
		}
		
		public function get_name() {
			
			return $this->name;
			
		}
		
		public function get_size() {
			
			return $this->size;
			
		}
		
		public function get_url() {
			
			return $this->url;
			
		}

		public function set_id( $id ) {
			
			$this->id = (string)$id;
			
		}
		
		public function set_name( $name ) {
			
			$this->name = (string)$name;
			
		}

		public function set_size( $size ) {
			
			$this->size = (string)$size;
			
		}
		
		public function set_url( $url ) {
			
			$this->url = (string)$url;
			
		}

	}
