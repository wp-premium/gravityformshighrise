<?php

	namespace Highrise;
	
	class HighriseParty extends HighriseAPI {
		
		private $highrise;
		public  $person;
		public  $company;
		public  $type;

		public function __construct( HighriseAPI $highrise, $object = null ) {
			
			$this->highrise = $highrise;		

			if ( ! empty( $object ) ) {
				
				if ( ! is_object( $object ) )
					throw new \Exception( 'You must pass an object to the HighriseParty constructor.' );

				if ( is_a( $object, 'Highrise\HighrisePerson' ) ) {
					
					$this->person = $object;
					$this->type   = 'Person';
					
				} else if ( is_a( $object, 'Highrise\HighriseCompany' ) ) {
					
					$this->company = $object;
					$this->type    = 'Company';
					
				} else {
					
					throw new \Exception( 'You must pass either a HighriseCompany or HighrisePerson object to the HighriseParty constructor.' );
				
				}
				
			}
			
		}

		public function create_xml( &$xml ) {

			if ( $this->type == 'Company' )
				return $this->company->create_xml( $xml );
				
			if ( $this->type == 'Person' )
				return $this->person->create_xml( $xml );
				
			throw new \Exception( 'Party type is not supported: ' . $this->type );

		}
		
		public function load_from_xml_object( $xml_object ) {

			if ( empty( $xml_object->{'type'} ) )
				return false;

			if ( $xml_object->{'type'} == 'Company' ) {
				
				$this->type = 'Company';
				$this->company = new HighriseCompany( $this->highrise );
				$this->company->load_from_xml_object( $xml_object );
				
			} elseif ( $xml_object->{'type'} == 'Person' ) {
				
				$this->type = 'Person';
				$this->person = new HighrisePerson( $this->highrise );
				$this->person->load_from_xml_object( $xml_object );
			
			} else {
			
				throw new \Exception( 'Party type is not supported: ' . $xml_object->{'type'} );
			
			}

		}

		public function to_xml() {
			
			if ( empty( $this->type ) )
				return null;

			if ( $this->type == 'Company' )
				return $this->company->to_xml( 'party' );
				
			if ($this->type == 'Person' )
				$xml = $this->person->toXML( 'party' );
			
			throw new \Exception( 'Party type is not supported: ' . $this->type );

		}		
		
	}
