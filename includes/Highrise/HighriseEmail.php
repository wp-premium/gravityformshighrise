<?php

	namespace Highrise;

	class HighriseEmail extends HighriseNote {
		
		public $title;
		
		public function __construct( HighriseAPI $highrise ) {
			
			parent::__construct( $highrise );
			
			$this->_note_type = "email";
			$this->_note_url  = "/emails";
			
		}

		public function load_from_xml_object( $xml_object ) {
			
			if ( $this->debug )
				print_r( $xml_object );

			$this->set_id( $xml_object->{'id'} );
			$this->set_author_id( $xml_object->{'author-id'} );
			$this->set_owner_id( $xml_object->{'owner-id'} );
			$this->set_collection_id( $xml_object->{'collection-id'} );
			$this->set_collection_type( $xml_object->{'collection-type'} );
			$this->set_subject_id( $xml_object->{'subject-id'} );
			$this->set_subject_type( $xml_object->{'subject-type'} );
			$this->set_created_at( $xml_object->{'created-at'} );
			$this->set_updated_at( $xml_object->{'updated-at'} );
			$this->set_visible_to( $xml_object->{'visible-to'} );
			$this->set_subject_name( $xml_object->{'subject-name'} );
			$this->set_title( $xml_object->{'title'} );
			$this->set_body( $xml_object->{'body'} );
			$this->load_attachments_from_xml_object( $xml_object );

			return true;
		}

		protected function to_xml_additional_fields( &$note ) {
			
			$note->addChild( 'title', $this->title );
			
		}
		
		public function get_title() {
			
			return $this->title;
			
		}
		
		public function set_title( $title ) {
			
			$this->title = (string)$title;
			
		}

	}
