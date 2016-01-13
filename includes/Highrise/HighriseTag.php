<?php

	namespace Highrise;
	
	class HighriseTag {

		protected $_tag_type;
		public    $id;
		public    $name;
		public    $deleted;
		
		public function __construct( $id = null, $name = null, $type = null ) {
			
			$this->set_id( $id );
			$this->set_name( $name );
			
			if ( ! empty( $type ) )
				$this->set_type( $type );

		}
		
		public function __toString() {
			
			return $this->name;
			
		}
		
		public function delete( $subject_id ) {
			
			$this->make_request( $this->_tag_type . '/' . $subject_id . '/tags/' . $this->get_id(), array(), 'DELETE' );
			$this->check_for_errors( ucwords( $this->_tag_type ), 200 );
			
			$this->deleted = true;
			
		}

		public function to_xml() {

			$xml = new \SimpleXMLElement( '<tag></tag>' );
			$xml->addChild( 'id', $this->get_id() );
			$xml->id->addAttribute( 'type', 'integer' );
			$xml->addChild( 'name', $this->get_name() );
			
			return $xml->asXML();
			
		}
		
		public function get_id() {
			
			return $this->id;
			
		}

		public function get_name() {
			
			return $this->name;
			
		}

		public function set_id( $id ) {
			
			$this->id = (string)$id;
			
		}
		
		public function set_name( $name ) {
			
			$this->name = (string)$name;
			
		}

		public function set_type( $type ) {

			switch ($type) {
				
				case 'Company':
					$this->_tag_type = 'companies';
					break;
					
				case 'Deal':
					$this->_tag_type = 'deals';
					break;
					
				case 'Kase':
					$this->_tag_type = 'kases';
					break;
					
				case 'Person':
					$this->_tag_type = 'people';
					break;
		
				default:
					throw new \Exception( $type . ' is not a valid status type. Available status names: Person, Company, Kase, Deal' );
					
			}
			
		}

	}
