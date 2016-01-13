<?php

	namespace Highrise;
	
	class HighrisePerson extends HighriseEntity {
		
		public $company_id;
		public $company_name;
		public $first_name;
		public $last_name;
		public $title;
		public $linkedin_url;

		public function __construct( HighriseAPI $highrise ) {
			
			parent::__construct( $highrise );
			
			$this->url_base = 'people';
			$this->error_check = 'Person';
			$this->set_type( 'Person' );
			
		}

		public function create_xml( $xml ) {

			$xml = parent::create_xml( $xml );
			
			$xml->addChild( 'company-id',   $this->get_company_id() );
			$xml->addChild( 'company-name', htmlspecialchars( $this->get_company_name() ) );
			$xml->addChild( 'first-name',   htmlspecialchars ($this->get_first_name() ) );
			$xml->addChild( 'last-name',    htmlspecialchars( $this->get_last_name() ) );
			$xml->addChild( 'title',        htmlspecialchars( $this->get_title() ) );
			$xml->addChild( 'linkedin-url', htmlspecialchars( $this->get_linkedin_url() ) );
			
			$subject_datas = $xml->addChild( 'subject_datas' );
			$subject_datas->addAttribute( 'type', 'array' );
			
			foreach( $this->custom_fields as $custom_field ) {
				
				$subject_data = $subject_datas->addChild( 'subject_data' );
				
				foreach ( $custom_field->get_xml_object()->children() as $child ) {
					
					$field = $subject_data->addChild( $child->getName(), htmlspecialchars( $child ) );
					
					foreach( $child->attributes() as $attribute ) {
					
						$field->addAttribute( $attribute->getName(), (string) $attribute );
					
					}
				
				}
	  			
			}
			
			return $xml;
			
		}
		
		public function to_xml( $header = 'person' ) {

			$xml = new \SimpleXMLElement( '<' . $header . '></' . $header . '>' );
			$xml = $this->create_xml( $xml );
			
			return $xml->asXML();
			
		}
		
		public function load_from_xml_object( $xml_object ) {
			
			parent::load_from_xml_object( $xml_object );

			$this->set_first_name( $xml_object->{'first-name'} );
			$this->set_last_name( $xml_object->{'last-name'} );
			$this->set_title( $xml_object->{'title'} );
			$this->set_company_id( $xml_object->{'company-id'} );
			$this->set_company_name( $xml_object->{'company-name'} );
			
		}
		
		public function get_company_id() {
			
			return $this->company_id;
			
		}
		
		public function get_company_name() {
			
			return $this->company_name;
			
		}
		
		public function get_first_name() {
			
			return $this->first_name;
			
		}
		
		public function get_full_name() {
			
			return $this->get_first_name() . ' ' . $this->get_last_name();
			
		}
		
		public function get_last_name() {
			
			return $this->last_name;
			
		}
		
		public function get_linkedin_url() {
			
			return $this->linkedin_url;
			
		}
		
		public function get_title() {
			
			return $this->title;
			
		}

		public function set_company_id( $company_id ) {
			
			$this->company_id = (string)$company_id;
			
		}
		
		public function set_company_name( $company_name ) {
			
			$this->company_name = (string)$company_name;
			
		}

		public function set_last_name( $last_name ) {
			
			$this->last_name = (string)$last_name;
			
		}

		public function set_first_name( $first_name ) {
			
			$this->first_name = (string)$first_name;
			
		}

		public function set_title( $title ) {
			
			$this->title = (string)$title;
			
		}

		public function set_linkedin_url( $linkedin_url ) {
			
			$this->linkedin_url = (string)$linkedin_url;
			
		}

	}
