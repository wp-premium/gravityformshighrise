<?php

	namespace Highrise;
	
	class HighriseTask extends HighriseAPI {
		
		private $highrise;
		public  $id;
		public  $author_id;
		public  $subject_id;
		public  $subject_type;
		public  $subject_name;
		public  $category_id;
		public  $body;
		public  $frame;
		public  $due_at;
		public  $done_at;
		public  $alert_at;
		public  $created_at;
		public  $updated_at;
		public  $public;
		public  $recording_id;
		public  $notify;
		public  $owner_id;
		public  $deleted;
		
		public function __construct( HighriseAPI $highrise ) {
			
			$this->highrise = $highrise;
			
		}

		public function load_from_xml_object( $xml_object ) {
	
			if ( $this->debug )
				print_r( $xml_object );

			$this->set_id( $xml_object->{'id'} );
			$this->set_author_id( $xml_object->{'author-id'} );
			$this->set_subject_id( $xml_object->{'subject-id'} );
			$this->set_subject_type( $xml_object->{'subject-type'} );
			$this->set_subject_name( $xml_object->{'subject-name'} );
			$this->set_category_id( $xml_object->{'category-id'} );
			$this->set_body( $xml_object->{'body'} );
			$this->set_frame( $xml_object->{'frame'} );
			$this->set_done_at( $xml_object->{'done-at'} );
			$this->set_due_at( $xml_object->{'due-at'} );
			$this->set_alert_at( $xml_object->{'alert-at'} );
		
			$this->set_created_at( $xml_object->{'created-at'} );
			$this->set_updated_at( $xml_object->{'updated-at'} );
			$this->set_public( ( $xml_object->{'public'} == 'true' ) );
			
			return true;
			
		}

		public function to_xml() {

			$xml = new \SimpleXMLElement( '<task></task>' );
			
			$xml->addChild( 'id',$this->get_id() );
			$xml->id->addAttribute( 'type', 'integer' );
			
			$xml->addChild( 'recording-id', $this->get_recording_id() );
			$xml->addChild( 'subject-id',   $this->get_subject_id() );
			$xml->addChild( 'subject-type', $this->get_subject_type() );
			$xml->addChild( 'body',         $this->get_body() );
			$xml->addChild( 'frame',        $this->get_frame() );
			$xml->addChild( 'category-id',  $this->get_category_id() );
			$xml->addChild( 'owner-id',     $this->get_owner_id() );
			$xml->addChild( 'due-at',       $this->get_due_at() );
			$xml->addChild( 'done-at',      $this->get_done_at() );
			$xml->addChild( 'alert-at',     $this->get_alert_at() );
			
			$xml->addChild( 'public', ( $this->get_public() ? 'true' : 'false' ) );
			$xml->public->addAttribute( 'type', 'boolean' );
			
			$xml->addChild( 'notify', ( $this->get_notify() ? 'true' : 'false' ) );
			$xml->notify->addAttribute( 'type', 'boolean' );

			return $xml->asXML();
		}		

		public function complete() {

			$updated_xml = $this->highrise->make_request( 'tasks/' . $this->get_id() . '/complete', array(), 'POST' );
			$this->highrise->check_for_errors( 'Task', 200 );
			$this->load_from_xml_object( simplexml_load_string( $updated_xml ) );
			
			return true;
			
		}
		
		public function delete() {
			
			$this->highrise->make_request( 'tasks/'. $this->get_id(), array(), 'DELETE' );
			$this->highrise->check_for_errors( 'Task', 200 );
			$this->deleted = true;
			
		}

		public function save() {
			
			if ( $this->get_frame() == null )
				throw new \Exception( 'You need to specify a valid time frame to save a task.' );

			if ( is_null( $this->id ) ) {
				
				$new_xml = $this->highrise->make_request( 'tasks', $this->to_xml(), 'POST' );
				$this->highrise->check_for_errors( 'Task', 201 );
				$this->load_from_xml_object( simplexml_load_string( $new_xml ) );
				
				return true;
				
			} else {
				
				$updated_xml = $this->highrise->make_request( 'tasks/'. $this->get_id(), $this->to_xml(), 'PUT' );
				$this->highrise->check_for_errors( 'Task', 200 );
				$this->load_from_xml_object( simplexml_load_string( $updated_xml ) );
				
				return true;
				
			}
			
		}
		
		public function assign_to_user( HighriseUser $user ) {
			
			if ( ! $user instanceof HighriseUser )
				throw new Exception( '$user must be an instance of HighriseUser' );
			
			$this->set_owner_id( $user->get_id() );
		
		}

		public function get_alert_at() {
			
			return $this->alert_at;
			
		}
		
		public function get_author_id() {
			
			return $this->author_id;
			
		}
		
		public function get_body() {
			
			return $this->body;
			
		}
		
		public function get_category_id() {
			
			return $this->category_id;
			
		}
		
		public function get_created_at() {
			
			return $this->created_at;
			
		}
		
		public function get_done_at() {
			
			return $this->done_at;
			
		}

		public function get_due_at() {
			
			return $this->due_at;
			
		}
		
		public function get_frame() {
			
			return $this->frame;
			
		}
		
		public function get_id() {
			
			return $this->id;
			
		}
		
		public function get_notify() {
			
			return $this->notify;
			
		}
		
		public function get_owner_id() {
			
			return $this->owner_id;
			
		}
		
		public function get_public() {
			
			return $this->public;
			
		}
		
		public function get_recording_id() {
			
			return $this->recording_id;
			
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

		public function set_alert_at( $alert_at ) {
			
			$this->alert_at = (string)$alert_at;
			
		}

		public function set_author_id( $author_id ) {
			
			$this->author_id = (string)$author_id;
			
		}
		
		public function set_body( $body ) {
			
			$this->body = (string)$body;
			
		}
		
		public function set_category_id( $category_id ) {
			
			$this->category_id = (string)$category_id;
			
		}

		public function set_created_at( $created_at ) {
			
			$this->created_at = (string)$created_at;
			
		}
		
		public function set_done_at( $done_at ) {
			
			$this->done_at = (string)$done_at;
			
		}

		public function set_due_at( $due_at ) {
			
			$this->due_at = (string)$due_at;
			
		}
		
		public function set_frame( $subject_type ) {
			
			$valid_frames = array( 'today', 'tomorrow', 'this_week', 'next_week', 'later', 'overdue' );
			$frame = str_replace( ' ', '_', strtolower( $subject_type ) );
			
			if ( ! is_null( $frame ) && ! in_array( $frame, $valid_frames ) )
				throw new \Exception( $subject_type . ' is not a valid frame. Available frames: ' . implode( ', ', $valid_frames ) );
	
			$this->frame = (string)$frame;
			
		}
		
		public function set_id( $id ) {
			
			$this->id = (string)$id;
			
		}
		
		public function set_notify( $notify ) {
			
			$notify = ( $notify == "true" || $notify == true || $notify == 1 );
				
			$this->notify = (string)$notify;
			
		}
		
		public function set_owner_id( $owner_id ) {
			
			$this->owner_id = (string)$owner_id;
			
		}
		
		public function set_public( $public ) {
			
			$this->public = (string)$public;
			
		}

		public function set_recording_id( $recording_id ) {
			
			$this->recording_id = (string)$recording_id;
			
		}
		
		public function set_subject_id( $subject_id ) {
			
			$this->subject_id = (string)$subject_id;
			
		}
	
		public function set_subject_name( $subject_name ) {
			
			$this->subject_name = (string)$subject_name;
			
		}

		public function set_subject_type( $subject_type ) {
			
			$valid_types = array( 'Party', 'Company', 'Deal', 'Kase' );
			$subject_type = ucwords( strtolower( $subject_type ) );
			
			if ( ! is_null( $subject_type ) && ! in_array( $subject_type, $valid_types ) )
				throw new \Exception( $subject_type . ' is not a valid subject type. Available subject types: ' . implode( ', ', $valid_types ) );
	
			$this->subject_type = (string)$subject_type;
			
		}
		
		public function set_updated_at( $updated_at ) {
			
			$this->updated_at = (string)$updated_at;
			
		}

	}
