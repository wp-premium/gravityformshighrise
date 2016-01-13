<?php

	namespace Highrise;

	class HighriseCase extends HighriseAPI {
		
		private $highrise;
		public  $id;
		public  $account_id;
		public  $author_id;
		public  $category_id;
		public  $closed_at;
		public  $created_at;
		public  $updated_at;
		public  $group_id;
		public  $visible_to;
		public  $name;
		public  $owner_id;
		public  $party_id;
		public  $parties;
		public  $party;
		
		public function __construct( HighriseAPI $highrise ) {
			
			$this->highrise = $highrise;
			$this->parties = array();

		}

		public function create_xml( $xml ) {

			$xml->addChild( 'id', $this->get_id() );
			$xml->id->addAttribute( 'type','integer' );

			$created_at = $xml->addChild( 'created-at', $this->get_created_at() );
			$created_at->addAttribute( 'type', 'datetime' );

			$updated_at = $xml->addChild( 'updated-at', $this->get_updated_at() );
			$updated_at->addAttribute( 'type', 'datetime' );

			$xml->addChild( 'background', htmlspecialchars( $this->get_background() ) );
			$xml->addChild( 'name',       htmlspecialchars( $this->get_name() ) );
			$xml->addChild( 'visible-to', $this->get_visible_to() );
			$xml->addChild( 'group-id',   $this->get_group_id() );
			$xml->addChild( 'owner-id',   $this->get_owner_id() );
			$xml->addChild( 'author-id',  $this->get_author_id() );

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

		public function load_from_xml_object( $xml_object ){

			$this->set_id( $xml_object->{'id'} );
			$this->set_author_id( $xml_object->{'author-id'} );
			$this->set_created_at( $xml_object->{'created-at'} );
			$this->set_closed_at( $xml_object->{'closed-at'} );
			$this->set_group_id( $xml_object->{'group-id'} );
			$this->set_name( $xml_object->{'name'} );
			$this->set_owner_id( $xml_object->{'owner-id'} );
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
			
			$xml = new \SimpleXMLElement( '<kase></kase>' );
			$xml = $this->create_xml( $xml );
			
			return $xml->asXML();
			
		}

		public function delete() {
			
			$this->highrise->make_request( 'kase/' . $this->get_id(), array(), 'DELETE' );
			$this->highrise->check_for_errors( 'Kase', 200 );
				
		}

		public function save() {
			
			$xml = $this->to_xml();
			
			if ( ! is_null( $this->get_id() ) ) {

				$new_xml = $this->highrise->make_request( 'kases/' . $this->get_id(), $xml, 'PUT' );
				$this->highrise->check_for_errors( 'Kase' );
				
			} else {
				
				$new_xml = $this->highrise->make_request( 'kases', $xml, 'POST' );
				$this->highrise->check_for_errors( 'Kase', 201 );
				
			}
		
			$this->load_from_xml_object( simplexml_load_string( $new_xml ) );
		
			return true;
			
		}
		
		public function add_party( HighriseParty $party ) {
			
			if ( ! $party instanceof HighriseParty )
				throw new \Exception( '$party must be an instanceof HighriseParty' );
			
			$this->parties[] = $party;
			
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
		
		public function get_closed_at() {
			
			return $this->closed_at;
			
		}

		public function get_created_at() {
			
			return $this->created_at;
			
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
		
		public function get_updated_at() {
			
			return $this->updated_at;
			
		}
		
		public function get_visible_to() {
			
			return $this->visible_to;
			
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

		public function set_closed_at( $closed_at ) {
			
			$this->closed_at = (string)$closed_at;
			
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
		
		public function set_name( $name ) {
			
			$this->name = (string)$name;
			
		}

		public function set_owner_id( $owner_id ) {
			
			$this->owner_id = (string)$owner_id;
			
		}

		public function set_party_id( $party_id ) {
			
			$this->party_id = (string)$party_id;
			
		}

		public function set_updated_at( $updated_at ) {
			
			return $this->updated_at = (string) $updated_at;
			
		}

		public function set_visible_to( $visible_to ) {
			
			$valid_permissions = array( 'Everyone', 'Owner', 'NamedGroup' );
			
			if ( ! is_null( $visible_to ) && ! in_array( $visible_to, $valid_permissions ) )
				throw new \Exception( $visible_to . ' is not a valid visibility permission. Available visibility permissions: ' . implode( ', ', $valid_permissions ) );
			
			$this->visible_to = (string)$visible_to;
			
		}

	}
