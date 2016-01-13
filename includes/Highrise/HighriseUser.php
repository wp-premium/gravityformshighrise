<?php

	namespace Highrise;

	class HighriseUser {
		
		public $id;
		public $name;
		public $email_address;
		public $token;
		public $dropbox;
		public $created_at;
		public $updated_at;
	
		public function load_from_xml_object( $xml_object ) {
			
			$this->set_id( $xml_object->{'id'} );
			$this->set_name( $xml_object->{'name'} );
			$this->set_email_address( $xml_object->{'email-address'} );
			$this->set_token( $xml_object->{'token'} );
			$this->set_dropbox( $xml_object->{'dropbox'} );
			$this->set_created_at( $xml_object->{'created-at'} );
			$this->set_updated_at( $xml_object->{'updated-at'} );
				
			return true;
			
		}
		
		public function get_created_at() {

			return $this->created_at;
			
		}
		
		public function get_dropbox() {
			
			return $this->dropbox;
		
		}
		
		public function get_email_address() {
			
			return $this->email_address;

		}
		
		public function get_id() {
			
			return $this->id;
		
		}
		
		public function get_name() {
			
			return $this->name;
		
		}
		
		public function get_token() {
			
			return $this->token;
		
		}
		
		public function get_updated_at() {
			
			return $this->updated_at;
		
		}

		public function set_created_at( $created_at ) {
			
			$this->created_at = (string)$created_at;
			
		}

		public function set_dropbox( $dropbox ) {
			
			$this->dropbox = (string)$dropbox;
			
		}

		public function set_email_address( $email_address ) {
			
			$this->email_address = (string)$email_address;
			
		}
		
		public function set_id( $id ) {
			
			$this->id = (string)$id;
		
		}
		
		public function set_name( $name ) {
			
			$this->name = (string)$name;
		
		}
		
		public function set_token( $token ) {
			
			$this->token = (string)$token;
	
		}
		
		public function set_updated_at( $updated_at ) {
			
			$this->updated_at = (string)$updated_at;
			
		}
		
	}
