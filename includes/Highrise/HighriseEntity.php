<?php

	namespace Highrise;
	
	class HighriseEntity extends HighriseAPI {

		protected $url_base;
		protected $error_check;
		protected $original_tags;
		protected $original_custom_fields = array();
		public    $id;
		public    $background;
		public    $created_at;
		public    $updated_at;
		public    $type;
		public    $owner_id;
		public    $group_id;
		public    $author_id;
		public    $contact_details;
		public    $visible_to;
		public    $email_addresses;
		public    $phone_numbers;
		public    $addresses;
		public    $web_addresses;
		public    $instant_messengers;
		public    $twitter_accounts;
		public    $tags;
		public    $notes;
		public    $custom_fields = array();

		public function __construct( HighriseAPI $highrise ) {
			
			$this->highrise = $highrise;

			$this->addresses = array();
			$this->email_addresses = array();
			$this->instant_messengers = array();
			$this->phone_numbers = array();
			$this->twitter_accounts = array();
			$this->web_addresses = array();

			$this->set_visible_to( 'Everyone' );
			
		}

		public function create_xml( $xml ) {

			$xml->addChild( 'id',$this->get_id() );
			$xml->id->addAttribute( 'type','integer' );

			$created_at = $xml->addChild( 'created-at', $this->get_created_at() );
			$created_at->addAttribute( 'type', 'datetime' );

			$updated_at = $xml->addChild( 'updated-at', $this->get_updated_at() );
			$updated_at->addAttribute( 'type', 'datetime' );

			$xml->addChild( 'background', htmlspecialchars( $this->get_background() ) );
			$xml->addChild( 'visible-to', $this->get_visible_to() );
			$xml->addChild( 'group-id',   $this->get_group_id() );
			$xml->addChild( 'owner-id',   $this->get_owner_id() );
			$xml->addChild( 'type',       $this->get_type() );

			$contact_data       = $xml->addChild( 'contact-data' );
			$addresses          = $contact_data->addChild( 'addresses' );
			$email_addresses    = $contact_data->addChild( 'email-addresses' );
			$instant_messengers = $contact_data->addChild( 'instant-messengers' );
			$phone_numbers      = $contact_data->addChild( 'phone-numbers' );
			$twitter_accounts   = $contact_data->addChild( 'twitter-accounts' );
			$web_addresses      = $contact_data->addChild( 'web-addresses' );

			if ( ! empty( $this->addresses ) ) {
				
				foreach ( $this->addresses as $_address ) {
					
					$address = $addresses->addChild( 'address' );
					$_address->create_xml( $address );
					
				}
				
			}

			if ( ! empty( $this->email_addresses ) ) {
				
				foreach ( $this->email_addresses as $_email_address ) {
					
					$email_address = $email_addresses->addChild( 'email-address' );
					$_email_address->create_xml( $email_address );
					
				}
				
			}
			
			if ( ! empty( $this->instant_messengers ) ) {
				
				foreach ( $this->instant_messengers as $_instant_messenger ) {
					
					$instant_messenger = $instant_messengers->addChild( 'instant-messenger' );
					$_instant_messenger->create_xml( $instant_messenger );
					
				}
				
			}

			if ( ! empty( $this->phone_numbers ) ) {
				
				foreach ( $this->phone_numbers as $_phone_number ) {
					
					$phone_number = $phone_numbers->addChild( 'phone-number' );
					$_phone_number->create_xml( $phone_number );
					
				}
				
			}

			if ( ! empty( $this->twitter_accounts ) ) {
				
				foreach ( $this->twitter_accounts as $_twitter_account ) {
					
					$twitter_account = $twitter_accounts->addChild( 'twitter-account' );
					$_twitter_account->create_xml( $twitter_account );
					
				}
				
			}

			if ( ! empty( $this->web_addresses ) ) {
				
				foreach ( $this->web_addresses as $_web_address ) {
					
					$web_address = $web_addresses->addChild( 'web-address' );
					$_web_address->create_xml( $web_address );
					
				}
				
			}

			return $xml;
			
		}
		
		public function load_from_xml_object( $xml_object ) {
			
			if ( $this->debug )
				print_r( $xml_object );
			
			$this->set_id( $xml_object->id );
			$this->set_author_id( $xml_object->{'author-id'} );
			$this->set_background( $xml_object->{'background'} );
			$this->set_visible_to( $xml_object->{'visible-to'} );	
			$this->set_created_at( $xml_object->{'created-at'} );
			$this->set_updated_at( $xml_object->{'updated-at'} );
			$this->set_group_id( $xml_object->{'group-id'} );
			$this->set_owner_id( $xml_object->{'owner-id'} );
			$this->load_contact_data_from_xml_object( $xml_object->{'contact-data'} );
			$this->load_tags_from_xml_object( $xml_object->{'tags'} );	
			$this->load_custom_fields_from_xml_object( $xml_object->{'subject_datas'} );
			
		}
		
		public function load_contact_data_from_xml_object( $xml_object ) {

			$this->addresses = array();
			$this->email_addresses = array();
			$this->instant_messengers = array();
			$this->phone_numbers = array();
			$this->twitter_accounts = array();
			$this->web_addresses = array();
			
			if ( isset( $xml_object->{'addresses'} ) ) {
				
				foreach( $xml_object->{'addresses'}->{'address'} as $_address ) {
					
					$address = new HighriseAddress();

					$address->set_id( $_address->id );
					$address->set_city( $_address->city );
					$address->set_country($_address->country );
					$address->set_location( $_address->location );
					$address->set_state( $_address->state );
					$address->set_street( $_address->street );
					$address->set_zip( $_address->zip );

					$this->addresses[] = $address;
					
				}
				
			}

			if ( isset( $xml_object->{'email-addresses'} ) ) {
				
				foreach( $xml_object->{'email-addresses'}->{'email-address'} as $_email_address) {
					
					$email_address = new HighriseEmailAddress( $_email_address->{'id'}, $_email_address->{'address'}, $_email_address->{'location'} );
					
					$this->email_addresses[] = $email_address;
					
				}
				
			}

			if ( isset( $xml_object->{'instant-messengers'} ) ) {
				
				foreach( $xml_object->{'instant-messengers'}->{'instant-messenger'} as $_instant_messenger ) {
					
					$instant_messenger = new HighriseInstantMessenger( $_instant_messenger->{'id'}, $_instant_messenger->{'protocol'}, $_instant_messenger->{'address'}, $_instant_messenger->{'location'} );
					
					$this->instant_messengers[] = $instant_messenger;
					
				}
				
			}
			
			if ( isset( $xml_object->{'phone-numbers'} ) ) {
				
				foreach( $xml_object->{'phone-numbers'}->{'phone-number'} as $_phone_number ) {
					
					$phone_number = new HighrisePhoneNumber( $_phone_number->{'id'}, $_phone_number->{'number'}, $_phone_number->{'location'} );
					
					$this->phone_numbers[] = $phone_number;
					
				}
				
			}

			if ( isset( $xml_object->{'twitter-accounts'} ) ) {
				
				foreach( $xml_object->{'twitter-accounts'}->{'twitter-account'} as $_twitter_account ) {
					
					$twitter_account = new HighriseTwitterAccount( $_twitter_account->{'id'}, $_twitter_account->{'username'}, $_twitter_account->{'location'} );
					
					$this->twitter_accounts[] = $twitter_account;
					
				}
				
			}
			
			if ( isset( $xml_object->{'web-addresses'} ) ) {
				
				foreach( $xml_object->{'web-addresses'}->{'web-address'} as $_web_address ) {
					
					$web_address = new HighriseWebAddress( $_web_address->{'id'}, $_web_address->{'url'}, $_web_address->{'location'} );
					
					$this->web_addresses[] = $web_address;
					
				}
				
			}
			
		}

		public function load_custom_fields_from_xml_object( $xml_object ) {
			
			$this->original_custom_fields = array();
			$this->custom_fields = array();
			
			if ( count( $xml_object->{'subject_data'} ) > 0 ) {
				
				foreach ( $xml_object->{'subject_data'} as $field ) {
					
					$custom_field = new HighriseCustomField( $this->highrise );
					$custom_field->set_id( $xml_object->{'id'} );
					$custom_field->set_value( $xml_object->{'value'} );
					$custom_field->set_subject_field_id( $xml_object->{'subject_field_id'} );
					$custom_field->set_subject_field_label( $xml_object->{'subject_field_label'} );
					
					$this->original_custom_fields[ $custom_field->get_subject_field_label() ] = 1;
				
					$this->add_custom_field( $custom_field );
					
				}
				
			}
			
		}

		public function load_tags_from_xml_object( $xml_object ) {
			
			$this->original_tags = array();
			$this->tags = array();
			
			if ( count( $xml_object->{'tag'} ) > 0 ) {
				
				foreach ( $xml_object->{'tag'} as $_tag) {
					
					$tag = new HighriseTag( $_tag->{'id'}, $_tag->{'name'}, $this->type );
					$original_tags[ $tag->get_name() ] = 1;
						
					$this->add_tag( $tag );
					
				}
				
			}
			
		}

		public function to_xml() {
			
			$xml = new \SimpleXMLElement( '<entity></entity>' );
			$xml = $this->create_xml( $xml );
			
			return $xml->asXML();
			
		}

		public function delete() {
			
			$this->highrise->make_request( $this->url_base . '/' . $this->get_id(), array(), 'DELETE' );
			$this->highrise->check_for_errors( $this->error_check, 200 );
				
		}

		public function save() {
			
			$xml = $this->to_xml();
			
			if ( ! is_null( $this->get_id() ) ) {
				
				$new_xml = $this->highrise->make_request( $this->url_base . '/' . $this->get_id(), $xml, 'PUT' );
				$this->highrise->check_for_errors( $this->error_check );
				
			} else {
				
				$new_xml = $this->highrise->make_request( $this->url_base, $xml, 'POST' );
				$this->highrise->check_for_errors( $this->error_check, 201 );
				
			}
			
			$tags = $this->tags;
			$original_tags = $this->original_tags;
				
			$this->load_from_xml_object( simplexml_load_string( $new_xml ) );
			$this->tags = $tags;
			$this->original_tags = $original_tags;
			$this->save_tags();
		
			return true;
			
		}

		public function add_address( $street, $city, $state, $zip, $country, $location = 'Work' ) {

			$address = new HighriseAddress();
			$address->set_street( $street );
			$address->set_city( $city );
			$address->set_state( $state );
			$address->set_zip( $zip );
			$address->set_country( $country );
			$address->set_location( $location );
			
			$this->addresses[] = $address;
			
		}

		public function add_custom_field( $field, $value = false ) {
			
			if ( ! $field instanceof HighriseCustomField ) {
				throw new \Exception( '$field must be an instant of HighriseCustomField' );
			}
			
			if ( ! isset( $this->custom_fields[ $field->get_subject_field_id() ] ) ) {
				
				$this->custom_fields[ $field->get_subject_field_id() ] = $field;
				$this->original_custom_fields[ $field->get_subject_field_id() ] = 1;
				
				if ( $value )
					$this->custom_fields[ $field->get_subject_field_id() ]->set_value( $value );
					
			} else {
				
				$custom_field = new HighriseCustomField( $this->highrise );
				$custom_field->set_value( $field );
				$this->custom_fields[ $field->get_subject_field_id() ] = $custom_field;
				
			}
			
		}

		public function add_email_address( $address, $location = 'Work' ) {
			
			$email_address = new HighriseEmailAddress( null, $address, $location );
			
			$this->email_addresses[] = $email_address;
			
		}
		
		public function add_instant_messenger( $protocol, $address, $location = 'Work' ) {
			
			$instant_messenger = new HighriseInstantMessenger( null, $protocol, $address, $location );
				
			$this->instant_messengers[] = $instant_messenger;
			
		}

		public function add_note( HighriseNote $note ) {
			
			$note->set_subject_id( $this->id );
			$note->set_subject_type( 'Party' );
			$note->save();
			
			$this->notes[$note->id] = $note;
			
		}

		public function add_phone_number( $number, $location = 'Work' ) {
			
			$phone_number = new HighrisePhoneNumber( null, $number, $location );
			
			$this->phone_numbers[] = $phone_number;
			
		}

		public function add_tag( $tag ) {
			
			if ( $tag instanceof HighriseTag && ! isset( $this->tags[ $tag->get_name() ] ) ) {
				
				$this->tags[ $tag->get_name() ] = $tag;
				$this->original_tags[ $tag->get_id() ] = 1;
				
			} else if ( ! isset( $this->tags[ $tag ] ) ) {
				
				$new_tag = new HighriseTag();
				$new_tag->set_name( $tag );
				$this->tags[ $tag ] = $new_tag;
				
			}
			
		}

		public function add_twitter_account( $username, $location = 'Business' ) {
			
			$twitter_account = new HighriseTwitterAccount( null, $username, $location );
			
			$this->twitter_accounts[] = $twitter_account;
			
		}

		public function add_web_address( $url, $location = 'Work' ) {
			
			$web_address = new HighriseWebAddress( null, $url, $location);
			
			$this->web_addresses[] = $web_address;
			
		}

		public function get_addresses() {
			
			return $this->addresses;
			
		}

		public function get_author_id() {
			
			return $this->author_id;
			
		}

		public function get_background() {
			
			return $this->background;
			
		}

		public function get_company_id() {
			
			return $this->company_id;
		
		}

		public function get_created_at() {
			
			return $this->created_at;
			
		}

        public function get_custom_fields() {
	        
            return $this->custom_fields;
            
        }

		public function get_email_addresses() {
			
			return $this->email_addresses;
			
		}

		public function get_group_id() {
			
			return $this->group_id;
			
		}
		
		public function get_id() {
			
			return $this->id;
			
		}

		public function get_instant_messengers() {
			
			return $this->instant_messengers;
			
		}

		public function get_notes() {
			
			$this->notes = array();
			
			$xml = $this->make_request( $this->url_base . '/' . $this->id . '/notes' );
			$xml_object = simplexml_load_string( $xml );

			if ( $this->debug )
				print_r( $xml_object );
			
			if ( isset( $xml_object->note ) && count( $xml_object->note ) > 0) {
				
				foreach( $xml_object->note as $_note ) {
					
					$note = new HighriseNote( $this->highrise );
					$note->load_from_xml_object( $_note );
					$note->set_subject_id( $this->id );
					$note->set_subject_id( 'Party' );
					
					$this->notes[$note->id] = $note;
					
				}
				
			}
			
			return $this->notes;
			
		}

		public function get_owner_id() {
			
			return $this->owner_id;
			
		}

		public function get_phone_numbers() {
			
			return $this->phone_numbers;
		
		}

		public function get_tags() {
			
			return $this->tags;
			
		}

		public function get_twitter_accounts() {
			
			return $this->twitter_accounts;
			
		}

		public function get_type() {
			
			return (string)$this->type;
			
		}
		
		public function get_updated_at() {
			
			return $this->updated_at;
			
		}
		
		public function get_visible_to() {
			
			return $this->visible_to;
		
		}
		
		public function get_web_addresses() {
			
			return $this->web_addresses;
			
		}

		public function has_tag( $tag_name ) {
			
			if ( ! empty( $this->tags ) ) {
				
				foreach ($this->tags as $tag) {
					
					if ( $tag->get_name() === $tag_name )
						return true;
						
				}
				
			}
			
			return false;
			
		}

		public function save_tags() {
			
			if ( is_array( $this->tags ) ) {
				
				foreach ( $this->tags as $tag_name => $tag ) {
					
					if ( is_null( $tag->get_id() ) ) {
						
						$new_tag_xml = '<name>' . $tag->get_name() . '</name>';
						$new_tag = $this->highrise->make_request( $this->url_base . '/' . $this->get_id() . '/tags', $new_tag_xml, 'POST' );
						$this->highrise->check_for_errors( $this->error_check, array( 200, 201 ) );
						
					}
					
					unset( $this->original_tags[ $tag->get_id() ] );
					
				}
				
				if ( is_array( $this->original_tags ) ) {
					
					foreach ( $this->original_tags as $tag_id => $tag_value ) {
						
						$new_tag = $this->highrise->make_request( $this->url_base . '/' . $this->get_id() . '/tags/' . $tag_id, array(), 'DELETE' );
						$this->highrise->check_for_errors( $this->error_check, 200 );
						
					}
					
				}
				
				foreach( $this->tags as $tag_name => $tag ) {
					
					$this->original_tags[ $tag->get_id() ] = 1;
					
				}	

			}
						
		}

		public function set_author_id( $author_id ) {
			
			$this->author_id = (string)$author_id;
			
		}
		
		public function set_background( $background ) {
			
			$this->background = (string)$background;
			
		}

		public function set_company_id( $company_id ) {
			
			$this->company_id = (string)$company_id;
			
		}

		public function set_created_at( $created_at ) {
			
			$this->created_at = (string)$created_at;
			
		}

		public function set_group_id( $group_id ) {
			
			$this->group_id = (string)$group_id;
			
		}

		public function set_id( $id ) {
			
			$this->id = (string)$id;
			
		}
		
		public function set_owner_id( $owner_id ) {
			
			$this->owner_id = (string)$owner_id;
			
		}

		public function set_type( $type ) {
			
			$this->type = (string)$type;
			
		}

		public function set_updated_at( $updated_at ) {
			
			$this->updated_at = (string)$updated_at;
			
		}
		
		public function set_visible_to( $visible_to ) {
			
			$valid_permissions = array( 'Everyone', 'Owner', 'NamedGroup' );
			
			if ( ! is_null( $visible_to ) && ! in_array( $visible_to, $valid_permissions ) )
				throw new \Exception( $visible_to . ' is not a valid visibility permission. Available visibility permissions: ' . implode( ', ', $valid_permissions ) );
			
			$this->visible_to = (string)$visible_to;
			
		}

	}
