<?php

	namespace Highrise;
	
	class HighriseNote extends HighriseAPI {
		
		protected $_note_type;
		protected $_note_url;
		private   $highrise;
		public    $id;
		public    $author_id;
		public    $body;
		public    $created_at;
		public    $owner_id;
		public    $collection_id;
		public    $collection_type;
		public    $subject_id;
		public    $subject_type;
		public    $updated_at;
		public    $visible_to;
		public    $subject_name;
		public    $deleted;
		
		// public $group_id
		
		public function __construct( HighriseAPI $highrise ) {
		
			$this->highrise = $highrise;

			$this->attachments = array();
			$this->_note_type  = 'note';
			$this->_note_url   = 'notes';
			
		}

		public function __toString() {
			
			return $this->id;
			
		}
	
		public function delete() {
			
			$this->highrise->make_request( $this->_note_url . '/' . $this->get_id(), array(), 'DELETE' );
			$this->highrise->check_for_errors( ucwords( $this->_note_type ), 200 );	
			$this->deleted = true;
			
		}

		public function save() {
			
			if ( empty( $this->subject_type ) || empty( $this->subject_id ) )
				throw new \Exception( 'Subject Type and Subject ID must be set in order to create a new ' . $this->_note_type );

			if ( empty( $this->id ) ) {
				
				$xml = $this->to_xml();
				$new_note_xml = $this->highrise->make_request( $this->_note_url, $xml, 'POST' );
				$this->highrise->check_for_errors( ucwords( $this->_note_type ), 201 );	
				$this->load_from_xml_object( simplexml_load_string( $new_note_xml ) );
			
				return true;
				
			} else  {
				
				$xml = $this->to_xml();
				$new_note_xml = $this->highrise->make_request( $this->_note_url . '/' . $this->get_id(), $xml, 'POST' );
				$this->check_for_errors( ucwords( $this->_note_type ), 200 );	
				
				return true;
				
			}
			
		}
		
		public function load_attachments_from_xml_object( $xml_object ) {
			
			$this->attachments = array();

			if ( isset( $xml_object->{'attachments'}) ) {
				
				foreach( $xml_object->{'attachments'}->{'attachment'} as $_attachment ) {
					
					$attachment = new HighriseAttachment( $_attachment->{'id'}, $_attachment->{'url'}, $_attachment->{'name'}, $_attachment->{'size'} );
					$this->attachments[] = $attachment;
					
				}
				
			}
			
		}

		public function load_from_xml_object( $xml_object ) {
			
			if ( $this->debug )
				print_r( $xml_object );

			$this->set_id( $xml_object->{'id'} );
			$this->set_author_id( $xml_object->{'author-id'} );
			$this->set_owner_id( $xml_object->{'owner-id'} );
			$this->set_collection_id( $xml_object->{'collection-id'} );
			//$this->set_collection_type( $xml_object->{'collection-type'} );
			$this->set_subject_id( $xml_object->{'subject-id'} );
			$this->set_subject_type( $xml_object->{'subject-type'} );
			$this->set_created_at( $xml_object->{'created-at'} );
			$this->set_updated_at( $xml_object->{'updated-at'} );
			$this->set_visible_to( $xml_object->{'visible-to'} );
			$this->set_subject_name( $xml_object->{'subject-name'} );
			$this->set_body( $xml_object->{'body'} );
			$this->load_attachments_from_xml_object( $xml_object );
			
			return true;
			
		}

		public function to_xml() {

			$note = new \SimpleXMLElement( '<' . $this->_note_type . '></' . $this->_note_type . '>' );

			$note->addChild( 'id',              $this->get_id() );
			$note->addChild( 'author-id',       $this->get_author_id() );
			$note->addChild( 'body',            $this->get_body() );
			$note->addChild( 'owner-id',        $this->get_owner_id() );
			$note->addChild( 'collection-id',   $this->get_collection_id() );
			$note->addChild( 'collection-type', $this->get_collection_type() );
			$note->addChild( 'subject-id',      $this->get_subject_id() );
			$note->addChild( 'subject-type',    $this->get_subject_type() );
			$note->addChild( 'visible-to',      $this->get_visible_to() );
			$note->addChild( 'created-at',      $this->get_created_at() );
			$note->id->addAttribute( 'type', 'integer' );
			$note->{'created-at'}->addAttribute( 'type', 'datetime' );
			$this->to_xml_additional_fields( $note );

			return $note->asXML();
			
		}

		protected function to_xml_additional_fields( &$note ) {
			
			return;
			
		}

		public function get_attachments() {
			
			return $this->attachments;
			
		}

		public function get_author_id() {
			
			return $this->author_id;
		}
		
		public function get_body() {
			
			return $this->body;
			
		}
		
		public function get_collection_id() {
			
			return $this->collection_id;
			
		}
		
		public function get_collection_type() {
			
			return $this->collection_type;
			
		}
		
		public function get_created_at() {
			
			return $this->created_at;
			
		}
		
		public function get_id() {
			
			return $this->id;
			
		}

		public function get_owner_id() {
			
			return $this->owner_id;
			
		}
		
		public function get_subject_id() {
			
			return $this->subject_id;
			
		}

		public function get_subject_name() {
			
			return $this->subject_name;
			
		}
		
		public function get_subject_type() {
			
			return $this->subject_type;
			
		}

		public function get_updated_at() {
			
			return $this->updated_at;
			
		}

		public function get_visible_to() {
			
			return $this->visible_to;
			
		}

		public function set_author_id( $author_id ) {
			
			$this->author_id = (string)$author_id;
			
		}
		
		public function set_body( $body ) {
			
			$this->body = (string)$body;
			
		}
		
		public function set_collection_id( $collection_id ) {
			
			$this->collection_id = (string)$collection_id;
			
		}
		
		public function set_collection_type( $collection_type ) {
			
			$valid_types = array( 'Deal', 'Kase' );
			$collection_type = ucwords( strtolower( $collection_type ) );
			
			if ( ! is_null( $collection_type ) && ! in_array( $collection_type, $valid_types ) )
				throw new \Exception( $collection_type . ' is not a valid collection type. Available collection types: ' . implode( ', ', $valid_types ) );
	
			$this->collection_type = (string)$collection_type;
			
		}
		
		public function set_created_at( $created_at ) {
			
			$this->created_at = (string)$created_at;
			
		}
		
		public function set_id( $id ) {
			
			$this->id = (string)$id;
			
		}
		
		public function set_owner_id( $owner_id ) {
			
			$this->owner_id = (string)$owner_id;
			
		}
		
		public function set_subject_id( $subject_id ) {
			
			$this->subject_id = (string)$subject_id;
			
		}
		
		public function set_subject_name( $subject_name ) {
			
			$this->subject_name = (string)$subject_name;
			
		}
		
		public function set_subject_type($subject_type) {
			
			$valid_types = array( 'Company', 'Deal', 'Kase', 'Party' );
			$subject_type = ucwords( strtolower( $subject_type ) );
			
			if ( ! is_null( $subject_type ) && ! in_array( $subject_type, $valid_types ) )
				throw new \Exception( $subject_type . ' is not a valid subject type. Available subject types: ' . implode( ', ', $valid_types ) );
	
			$this->subject_type = (string)$subject_type;
			
		}
		
		public function set_updated_at( $updated_at ) {
			
			$this->updated_at = (string)$updated_at;
			
		}
		
		public function set_visible_to( $visible_to ) {
			
			$this->visible_to = (string)$visible_to;
			
		}
		
	}
