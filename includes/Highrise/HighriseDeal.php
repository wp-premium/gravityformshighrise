<?php

	namespace Highrise;

	class HighriseDeal extends HighriseAPI {
		
		private $highrise;
		public  $id;
		public  $account_id;
		public  $author_id;
		public  $background;
		public  $category_id;
		public  $created_at;
		public  $updated_at;
		public  $currency;
		public  $duration;
		public  $group_id;
		public  $visible_to;
		public  $name;
		public  $owner_id;
		public  $party_id;
		public  $price;
		public  $price_type;
		public  $responsible_party_id;
		public  $status;
		public  $status_changed_on;
		public  $parties;
		public  $party;
		
		public function __construct( HighriseAPI $highrise ) {
			
			$this->highrise = $highrise;
			$this->parties = array();

		}

		public function create_xml( $xml ) {

			if ( $this->get_name() == null )
				throw new \Exception( 'You must set a name.' );

			$xml->addChild( 'id', $this->get_id() );
			$xml->{'id'}->addAttribute( 'type', 'integer' );

			$xml->addChild( 'name', $this->get_name() );

			$xml->addChild( 'account-id', $this->get_account_id() );
			$xml->{'account-id'}->addAttribute( 'type', 'integer' );

			$xml->addChild( 'author-id', $this->get_author_id() );
			$xml->{'author-id'}->addAttribute( 'type', 'integer' );

			$xml->addChild( 'background', $this->get_background() );

			$xml->addChild( 'category-id', $this->get_category_id() );
			$xml->{'category-id'}->addAttribute( 'type', 'integer' );

			$xml->addChild( 'created-at', $this->get_created_at() );
			$xml->{'created-at'}->addAttribute( 'type', 'datetime' );

			$xml->addChild( 'updated-at', $this->get_updated_at() );
			$xml->{'updated-at'}->addAttribute( 'type', 'datetime' );

			$xml->addChild( 'currency', $this->get_currency() );

			$xml->addChild( 'duration', $this->get_duration() );
			$xml->{'duration'}->addAttribute( 'type', 'integer' );

			$xml->addChild( 'group-id', $this->get_group_id() );
			$xml->{'group-id'}->addAttribute( 'type', 'integer' );

			$xml->addChild( 'owner-id', $this->get_owner_id() );
			$xml->{'owner-id'}->addAttribute( 'type', 'integer' );

			$xml->addChild( 'party-id', $this->get_party_id() );
			$xml->{'party-id'}->addAttribute( 'type', 'integer' );

			$xml->addChild( 'price', $this->get_price() );
			$xml->{'price'}->addAttribute( 'type', 'integer' );

			$xml->addChild( 'price-type', $this->get_price_type() );

			$xml->addChild( 'responsible-party-id', $this->get_responsible_party_id() );
			$xml->{'responsible-party-id'}->addAttribute( 'type', 'integer' );

			$xml->addChild( 'status', $this->get_status() );

			$xml->addChild( 'status-changed-on', $this->get_status_changed_on() );
			$xml->{'status-changed-on'}->addAttribute( 'type', 'date' );

			$xml->addChild( 'visible-to', $this->get_visible_to() );

			//if ( ! empty( $this->parties ) ) {
			//	
			//	$parties = $xml->addChild( 'parties' );
			//	$parties->addAttribute( 'type', 'array' );
			//	
			//	foreach ( $this->parties as $_party ) {
			//		
			//		$party = $parties->addChild( 'party' );
			//		$_party->create_xml( $party );
			//		
			//	}
			//	
			//}

			return $xml;

		}

		
		public function load_from_xml_object( $xml_object ) {
	
			if ( $this->debug )
				print_r( $xml_object );

			$this->set_id( $xml_object->{'id'} );
			$this->set_account_id( $xml_object->{'account-id'} );
			$this->set_author_id( $xml_object->{'author-id'} );
			$this->set_background( $xml_object->{'background'} );
			$this->set_category_id( $xml_object->{'category-id'} );
			$this->set_created_at( $xml_object->{'created-at'} );
			$this->set_currency( ( empty( $xml_object->{'currency'} ) ) ? 'USD' : $xml_object->{'currency'} );
			$this->set_duration( $xml_object->{'duration'} );
			$this->set_group_id( $xml_object->{'group-id'} );
			$this->set_name( $xml_object->{'name'} );
			$this->set_owner_id( $xml_object->{'owner-id'} );
			$this->set_party_id( $xml_object->{'party-id'} );
			$this->set_price( $xml_object->{'price'} );
			$this->set_price_type( $xml_object->{'price-type'} );
			$this->set_responsible_party_id( $xml_object->{'responsible-party-id'} );
			$this->set_status( $xml_object->{'status'} );
			$this->set_status_changed_on( $xml_object->{'status-changed-on'} );
			$this->set_updated_at( $xml_object->{'updated-at'} );
			$this->set_visible_to( $xml_object->{'visible-to'} );
			$this->load_party_from_xml_object( $xml_object->{'party'} );
			$this->load_parties_from_xml_object( $xml_object->{'parties'} );

			return true;
		}

		public function load_party_from_xml_object( $xml_object ) {

			if ( ! is_null( $xml_object ) ) {
				
				$this->party = new HighriseParty( $this->highrise );
				$this->party->load_from_xml_object( $xml_object );
				
			}

		}

		public function load_parties_from_xml_object( $xml_object ) {
			
			foreach ( $xml_object->{'party'} as $_party ) {
				
				$party = new HighriseParty( $this->highrise );
				$party->load_from_xml_object( $_party );
				
				$this->parties[] = $party;
				
			}
			
		}
		
		public function to_xml() {

			$xml = new \SimpleXMLElement( '<deal></deal>' );
			$xml = $this->create_xml( $xml );
			
			return $xml->asXML();
			
		}		

		public function delete() {
			
			$this->highrise->make_request( 'deals/' . $this->get_id(), array(), 'DELETE' );
			$this->highrise->check_for_errors( 'Deal', 200 );
			$this->deleted = true;
			
		}

		public function save() {
			
			if ( $this->get_name() == null )
				throw new \Exception( 'You must set a name.' );

			if ( is_null( $this->id ) ) {

				$new_xml = $this->highrise->make_request( 'deals', $this->to_xml(), 'POST' );
				$this->highrise->check_for_errors( 'Deal', 201 );
				$this->load_from_xml_object( $xml_object );

			} else {
				
				$this->highrise->make_request( 'deals/' . $this->get_id(), $this->to_xml(), 'PUT' );
				$this->highrise->check_for_errors( 'Deal', 200 );
				
			}
			
			return true;
			
		}

		public function update_status( $status ) {
			
			$valid_status = array( 'pending', 'won', 'lost' );
			
			if ( ! in_array( $status, $valid_status ) )
				throw new \Exception( $status . ' is not a valid status type. Available status names: ' . implode( ', ', $valid_status ) );
			
			$status_update_xml = '<status><name>' . $status . '</name></status>';
			$this->highrise->make_request( 'deals/' . $this->get_id() . '/status', $status_update_xml, 'PUT' );
			$this->highrise->check_for_errors( 'Deals', 200 );
			
			return true;
			
		}

		public function add_party( HighriseParty $party ) {
			
			if ( ! $party instanceof HighriseParty )
				throw new \Exception( '$party must be an instanceof HighriseParty' );
			
			$this->parties[] = $party;
			
		}
		
		public function assign_to_user( HighriseUser $user ) {
			
			if ( ! $user instanceof HighriseUser )
				throw new Exception( '$user must be an instance of HighriseUser' );
			
			$this->set_owner_id( $user->get_id() );
		
		}

		public function get_account_id() {
		
			return $this->account_id;
			
		}

		public function get_author_id() {
			
			return $this->author_id;
			
		}

		public function get_background() {
			
			return $this->background;
			
		}

		public function get_category_id() {
			
			return $this->category_id;
			
		}

		public function get_created_at() {
			
			return $this->created_at;
			
		}

		public function get_currency() {
			
			return $this->currency;
			
		}

		public function get_duration() {
			
			return $this->duration;
			
		}

		public function get_group_id() {
			
			return $this->group_id;
			
		}

		public function get_id() {
			
			return $this->id;
			
		}

		public function get_name() {
			
			return $this->name;
			
		}

		public function get_owner_id() {
			
			return $this->owner_id;
			
		}
		
		public function get_parties() {
			
			return $this->parties;
			
		}
		
		public function get_party() {
			
			return $this->party;
			
		}

		public function get_party_id() {
			
			return $this->party_id;
			
		}

		public function get_price() {
			
			return $this->price;
			
		}

		public function get_price_type() {
			
			return $this->price_type;
			
		}

		public function get_responsible_party_id() {
			
			return $this->responsible_party_id;
			
		}
		
		public function get_status() {
			
			return $this->status;
			
		}
		
		public function get_status_changed_on() {
			
			return $this->status_changed_on;
			
		}
		
		public function get_updated_at() {
			
			return $this->updated_at;
			
		}

		public function get_visible_to() {
			
			return $this->visible_to;
			
		}

		public function set_account_id( $account_id ) {
			
			$this->account_id = (string)$account_id;
			
		}

		public function set_author_id( $author_id ) {
			
			$this->author_id = (string)$author_id;
			
		}

		public function set_background( $background ) {
			
			$this->background = (string)$background;
			
		}
		
		public function set_category_id( $category_id ) {
			
			$this->category_id = (string)$category_id;
			
		}

		public function set_created_at( $created_at ) {
			
			$this->created_at = (string)$created_at;
			
		}
		
		public function set_currency( $currency = 'USD' ) {
			
			$this->currency = (string)$currency;
			
		}

		public function set_duration( $duration ) {
			
			$this->duration = (string)$duration;
			
		}

		public function set_group_id( $group_id ) {
			
			$this->group_id = (string)$group_id;
			
		}
		
		public function set_id( $id ) {
			
			$this->id = (string)$id;
			
		}

		public function set_name( $name ) {
			
			$this->name = (string)$name;
			
		}

		public function set_owner_id( $owner_id ) {
			
			$this->owner_id = (string)$owner_id;
			
		}

		public function set_party_id( $party_id ) {
			
			$this->party_id = (string)$party_id;
			
		}
		
		public function set_price( $price ) {
			
			$this->price = (string)$price;
			
		}

		public function set_price_type( $price_type ) {
			
			$valid_price_types = array( 'fixed', 'hour', 'month', 'year' );
			$price_type = strtolower( $price_type );
			
			if ( ! is_null( $price_type ) && ! in_array( $price_type, $valid_price_types ) )
				throw new \Exception( $price_type . ' is not a valid price type. Available price types: ' . implode( ', ', $valid_price_types ) );
			
			$this->price_type = (string)$price_type;

		}
		
		public function set_responsible_party_id( $responsible_party_id ) {
			
			$this->responsible_party_id = (string)$responsible_party_id;
		
		}

		public function set_status( $status ) {
			
			$valid_statuses = array( 'pending', 'won', 'lost' );
			$status = strtolower( $status );
			
			if ( ! is_null( $status ) && ! in_array( $status, $valid_statuses ) )
				throw new \Exception( $status . ' is not a valid status. Available statuses: ' . implode( ', ', $valid_statuses ) );

			$this->status = (string)$status;

		}

		public function set_status_changed_on( $status_changed_on ) {
			
			$this->status_changed_on = (string)$status_changed_on;
		
		}

		public function set_updated_at( $updated_at ) {
			
			return $this->updated_at = (string) $updated_at;
			
		}

		public function set_visible_to( $visible_to ) {
			
			$valid_permissions = array( 'Everyone', 'Owner' );
			
			if ( ! is_null( $visible_to ) && ! in_array( $visible_to, $valid_permissions ) )
				throw new \Exception( $visible_to . ' is not a valid visibility permission. Available visibility permissions: ' . implode( ', ', $valid_permissions ) );
			
			$this->visible_to = (string)$visible_to;
			
		}

	}
