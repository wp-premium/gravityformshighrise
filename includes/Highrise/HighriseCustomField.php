<?php

	namespace Highrise;

	class HighriseCustomField {
	    
	    public $id;
	    public $label;
	    public $value;
	    public $subject_field_id;
	    public $subject_field_label;
	    public $xml_type;

	    public function __construct( HighriseAPI $highrise ) {
		    
	        $this->highrise = $highrise;
	        $this->xml_type = 'subject-field';
	        
	    }

	    public function __toString() {
		    
	        return $this->subject_field_label . ": " . $this->value;
	  
	    }

		public function get_xml_object() {
			
			$xml = new \SimpleXMLElement( '<' . $this->xml_type . '></' . $this->xml_type . '>' );
			
			if ( $this->get_id() ) {
				
				$xml->addChild( 'id', $this->get_id() );
				$xml->id->addAttribute( 'type', 'integer' );
				
			}
			
			if ( $this->get_label() )
				$xml->addChild( 'label', htmlspecialchars( $this->get_label() ) );

			if ( $this->get_value() )
				$xml->addChild( 'value', htmlspecialchars( $this->get_value() ) );

			if ( $this->get_subject_field_id() ) {
				
				$xml->addChild( 'subject_field_id', $this->get_subject_field_id() );
				$xml->subject_field_id->addAttribute( 'type', 'integer' );
				
			}
			
			if ( $this->get_subject_field_label() )
				$xml->addChild( 'subject_field_label', $this->get_subject_field_label() );

			return $xml;
		
		}

		public function load_from_xml_object( $xml_object ) {
	
			$this->set_id( $xml_object->{'id'} );
			$this->set_label( $xml_object->{'label'} );
			$this->set_value( $xml_object->{'value'} );
			$this->set_subject_field_id( $xml_object->{'subject_field_id'} );
			$this->set_subject_field_label( $xml_object->{'subject_field_label'} );
			
			return true;
			
		}

	    public function to_xml() {
	       
			return $this->get_xml_object()->asXML();
	    
	    }

		public function delete() {
			
			$this->highrise->make_request( 'subject_fields/' . $this->get_subject_field_id(), array(), 'DELETE' );
			$this->highrise->check_for_errors( 'Custom Fields', 200 );	

		}

		public function save() {

			if ( is_null( $this->get_id() ) ) {
				
				$xml = $this->to_xml();
				
				$new_field = $this->highrise->make_request( 'subject_fields', $xml, 'POST' );
				$this->highrise->check_for_errors( 'Custom Fields', 201 );
				$this->load_from_xml_object( simplexml_load_string( $new_field ) );
				return true;
				
			} else {
				
				$xml = $this->to_xml();
				$this->highrise->make_request( 'subject_fields/'. $this->get_id(), $xml, 'PUT' );
				$this->highrise->check_for_errors( 'Custom Fields', 200 );	
				return true;
				
			}
			
		}

	    public function get_id() {
		    
	        return $this->id;
	        
	    }
	    	    
	    public function get_label() {
	      
	        return $this->label;
	        
	    }

	    public function get_subject_field_id() {
	      
	        return $this->subject_field_id;
	        
	    }
	    
	    public function get_subject_field_label() {
	      
	        return $this->subject_field_label;
	        
	    }


	    public function get_value() {
	      
	        return $this->value;
	        
	    }

	    public function set_id( $id ) {
		    
	        $this->id = (string)$id;
	        
	    }
	    
	    public function set_label( $label ) {
		    
			$this->label = (string)$label;
	        
	    }

	    public function set_subject_field_id( $subject_field_id ) {
		    
			$this->subject_field_id = (string)$subject_field_id;
	        
	    }

	    public function set_subject_field_label( $subject_field_label ) {
		    
			$this->subject_field_label = (string)$subject_field_label;
	        
	    }

	    public function set_value( $value ) {
		    
			$this->value = (string)$value;
	        
	    }
	    
	    public function set_xml_type( $xml_type ) {
		    
		    $this->xml_type = (string)$xml_type;
		    
	    }

	}
