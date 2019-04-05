<?php
	
	namespace Highrise;

	class HighriseAPI {
		
		protected $curl;
		public    $account;
		public    $debug;
		public    $token;
		
		public function __construct() {
			
			$this->curl = curl_init();
			
		}
		
		public function make_request( $action, $options = array(), $method = 'GET' ) {
			
			curl_setopt( $this->curl, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $this->curl, CURLOPT_HTTPHEADER,     array( 'Accept: application/xml', 'Content-Type: application/xml', 'User-Agent: Gravity Forms (http://gravityforms.com/add-ons/highrise)' ) );


			/***
			 * Determines if the cURL CURLOPT_SSL_VERIFYPEER option is enabled.
			 *
			 * @since 1.2
			 *
			 * @param bool is_enabled True to enable peer verification. False to bypass peer verification. Defaults to true.
			 */
			$verify_peer = apply_filters( 'gform_highrise_verifypeer', true );
			curl_setopt( $this->curl, CURLOPT_SSL_VERIFYPEER, $verify_peer );

			/***
			 * Determines if the cURL CURLOPT_SSL_VERIFYHOST option is enabled.
			 *
			 * @since 1.2
			 *
			 * @param bool is_enabled True to enable host verification. False to bypass host verification. Defaults to true.
			 */
			$verify_host = apply_filters( 'gform_highrise_verifyhost', true );
			curl_setopt( $this->curl, CURLOPT_SSL_VERIFYHOST, $verify_host ? 2 : 0 );
			
			/* Build request URL */
			$request_url = 'https://' . $this->account . '.highrisehq.com/' . $action . '.xml';
			
			switch ( $method ) {
				
				case 'GET':
					curl_setopt( $this->curl, CURLOPT_CUSTOMREQUEST, 'GET' );
					$request_url .= '?' . http_build_query( $options );
					break;
					
				case 'POST':
					curl_setopt( $this->curl, CURLOPT_CUSTOMREQUEST, 'POST' );
					curl_setopt( $this->curl, CURLOPT_POST, true );
					curl_setopt( $this->curl, CURLOPT_POSTFIELDS, $options );
					break;
					
				case 'DELETE':
				case 'PUT':
					curl_setopt( $this->curl, CURLOPT_CUSTOMREQUEST, $method );
					break;
				
			}
				
			
			curl_setopt( $this->curl, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $this->curl, CURLOPT_VERBOSE, ( $this->debug ) ? true : false );
			curl_setopt( $this->curl, CURLOPT_URL, $request_url );
			
			$response = curl_exec( $this->curl );

			if ( $this->debug ) {
				
				echo '<strong>Request URL: </strong>' . $request_url . '<br />';
				
				echo '<strong>Request Options: </strong>';
				echo '<pre>';
				var_dump( $options );
				echo '</pre>';
				
				echo '<strong>Response: </strong>';
				echo '<pre>';
				var_dump( $response );
				echo '</pre>';
				
			}
			
			return $response;
		
		}
			
		public function check_for_errors( $type, $expected_status = array( 200 ) ) {
			
			if ( ! is_array( $expected_status) )
				$expected_status = array( $expected_status );
			
			if ( ! in_array( $this->get_last_return_status(), $expected_status ) ) {

				switch( $this->get_last_return_status() ) {
					
					case 404:
						throw new \Exception( $type . ' not found' );
						break;
						
					case 403:
						throw new \Exception( 'Access denied to ' . $type . ' resource' );
						break;
					
					case 507:
						throw new \Exception( 'Cannot create ' . $type . ': Insufficient storage in your Highrise Account' );
						break;
					
					default:
						throw new \Exception( 'API for ' . $type . ' returned Status Code: ' . $this->get_last_return_status() . ' Expected Code: ' . implode( ',', $expected_status ) );
						break;
						
				}	
							
			}
			
		}

		protected function get_last_return_status() {
			
			return curl_getinfo( $this->curl, CURLINFO_HTTP_CODE );
			
		}
		
		protected function get_xml_object_for_action( $action ) {
			
			$xml = $this->make_request( $action );
			$xml_object = simplexml_load_string( $xml );
			
			return $xml_object;
			
		}

		private function parse_listing( $action, $paging_results, $type, $options = array() ) {

			$offset = 0;
			$objects = array();
			
			while ( true ) {
				
				/* Add offset to options array. */
				$options['n'] = $offset;
				
				$xml = $this->make_request( $action, $options );
				$this->check_for_errors( ucwords( $type ) );
				$xml_object = simplexml_load_string( $xml );

				foreach ( $xml_object->$type as $_object ) {
					
					switch ( $type ) {
						
						case 'company':
							$object = new HighriseCompany( $this );
							break;
							
						case 'email':
							$object = new HighriseEmail( $this );
							break;
							
						case 'note':
							$object = new HighriseNote( $this );
							break;
							
						case 'person':
							$object = new HighrisePerson( $this );
							break;
						
						default:
							throw new \Exception( 'Invalid type ' . $type . ' in parse_listing' );
							break;
							
					}
					
					$object->load_from_xml_object( $_object );
					
					$objects[] = $object;
					
				}
				
				if ( count( $xml_object ) != $paging_results )
					break;
				
				$offset += $paging_results;
				
			}
			
			return $objects;
			
		}

		private function parse_tasks( $xml ) {
			
			$xml_object = simplexml_load_string( $xml );
					
			$tasks = array();
			foreach ( $xml_object->task as $_task ) {
				
				$task = new HighriseTask( $this );
				$task->load_from_xml_object($_task);
				
				$tasks[] = $task;
				
			}

			return $tasks;
		
		}
				
		public function get_assigned_tasks() {
			
			$xml = $this->make_request( 'tasks/assigned' );
			$this->check_for_errors( 'Tasks' );
			
			return $this->parse_tasks( $xml );
			
		}

        public function get_cases( $status = 'open' ) {
	        
            $xml = $this->make_request( 'cases/' . $status );
			$this->check_for_errors( 'Kase' );
            $xml_object = simplexml_load_string( $xml );

            $cases = array();
            foreach ( $xml_object->kase as $_case ) {
	            
                $case = new HighriseCase( $this );
                $case->load_from_xml_object( $_case );
                
                $cases[] = $case;
                
            }
            
			return $cases;
			
		}

		protected function get_categories( $type ) {
		
			$xml = $this->make_request( $type . '_categories' );
			$this->check_for_errors( ucwords( $type ) . ' Categories' );
			$xml_object = simplexml_load_string( $xml );
			
			$categories = array();
			foreach ( $xml_object->{"$type-category"} as $_category ) {
				
				if ( $type == 'deal' )
					$category = new HighriseDealCategory( $this );
					
				if ( $type == 'task' )
					$category = new HighriseTaskCategory( $this );
				
				$category->load_from_xml_object( $_category );
				$categories[(string)$_category->name] = $category;
				
			}
		
			return $categories;
		
		}

		public function get_companies() {
			
			return $this->parse_listing( 'companies', 500, 'company' );
		
		}

		public function get_companies_by_tag_id( $tag_id ) {
			
			$options = array( 'tag_id' => $tag_id );
			
			return $this->parse_listing( 'companies', 500, 'company', $options );
			
		}

		public function get_companies_by_tag_name( $tag_name ) {
			
			$tags = $this->get_tags();
			foreach( $tags as $tag ) {
				
				if ( $tag->name == $tag_name )
					$tag_id = $tag->id;
					
			}
			
			if ( ! isset( $tag_id ) )
				throw new \Exception( 'Tag ' . $tag_name . ' not found' );
			
			return $this->get_companies_by_tag_id( $tag_id );
			
		}

		public function get_company( $id ) {
			
			$xml = $this->make_request( 'companies/'. $id );
			$this->check_for_errors( 'Company' );
			$xml_object = simplexml_load_string( $xml );
			
			$company = new HighriseCompany( $this );
			$company->load_from_xml_object( $xml_object );
			
			return $company;
			
		}
		
		public function get_company_emails( $company_id ) {
			
			return $this->parse_listing( 'companies/' . $company_id . '/emails', 25, 'email' );
		
		}

		public function get_company_notes( $company_id ) {
			
			return $this->parse_listing( 'companies/' . $company_id . '/notes', 25, 'note' );
			
		}

		public function get_completed_tasks() {
			
			$xml = $this->make_request( 'tasks/completed' );
			$this->check_for_errors( 'Tasks' );
			
			return $this->parse_tasks( $xml );
			
		}

		public function get_custom_fields() {
			
			$xml = $this->make_request( 'subject_fields' );
			$this->check_for_errors( 'Custom Fields' );
			$xml_object = simplexml_load_string( $xml );			
			
			$fields = array();
			foreach( $xml_object->{'subject-field'} as $_field ) {
				
				$field = new HighriseCustomField( $this );
				$field->set_id( $_field->id );
				$field->set_label( $_field->label );
								
				$fields[(string)$_field->label] = $field;
			
			}
			
			return $fields;
			
		}

		public function get_deal( $id ) {
			
			$xml = $this->make_request( 'deals/' . $id );
			$this->check_for_errors( 'Deal' );
			$deal_xml = simplexml_load_string( $xml );
			
			$deal = new HighriseDeal( $this );
			$deal->load_from_xml_object( $deal_xml );
			
			return $deal;
			
		}

		public function get_deal_categories() {
		
			return $this->get_categories( 'deal' );
		
		}

		public function get_deals() {
			
			$xml = $this->make_request( 'deals' );
			$this->check_for_errors( 'Deals' );
			$xml_object = simplexml_load_string( $xml );
			
			$deals = array();
			foreach ( $xml_object->deal as $_deal ) {
				
				$deal = new HighriseDeal( $this );
				$deal->load_from_xml_object( $_deal );
				
				$deals[] = $deal;
				
			}
			
			return $deals;
			
		}
		
		public function get_email( $id ) {
			
			$xml = $this->make_request( 'emails/' . $id );
			$this->check_for_errors( 'Email' );
			$xml_object = simplexml_load_string( $xml );
			
			$email = new HighriseEmail( $this );
			$email->load_from_xml_object( $xml_object );
			
			return $email;
			
		}

        public function get_groups() {
	        
            $xml = $this->make_request( 'groups' );
            $this->check_for_errors( 'Groups' );
            $xml_object = simplexml_load_string( $xml );

            $groups = array();
            foreach ( $xml_object->group as $xml_group ) {
	            
                $group = new HighriseGroup();
                $group->load_from_xml_object( $xml_group );
                
                $groups[] = $group;
            }

            return $groups;
            
        }

		public function get_note( $id ) {
			
			$xml = $this->make_request( 'notes/' . $id );
			$this->check_for_errors( 'Note' );
			$xml_object = simplexml_load_string( $xml );
			
			$note = new HighriseNote( $this );
			$note->load_from_xml_object( $xml_object );
			
			return $note;
			
		}

		public function get_people() {
			
			return $this->parse_listing( 'people', 500, 'person' );
		
		}

		public function get_people_by_company_id( $company_id ) {
			
			return $this->parse_listing( 'companies/' . $company_id . '/people', 500, 'person' );
			
		}

		public function get_people_by_email( $email ) {
			
			return $this->search_people( array( 'email' => $email ) );
			
		}

		public function get_people_by_tag_id( $tag_id ) {
			
			$options = array( 'tag_id' => $tag_id );
			
			return $this->parse_listing( 'people', 500, 'person', $options );
			
		}

		public function get_people_by_tag_name( $tag_name ) {
			
			$tags = $this->get_tags();
			foreach( $tags as $tag ) {
				
				if ( $tag->name == $tag_name )
					$tag_id = $tag->id;
					
			}
			
			if ( ! isset( $tag_id ) )
				throw new \Exception( 'Tag ' . $tag_name . ' not found' );
			
			return $this->get_people_by_tag_id( $tag_id );
			
		}

		public function get_people_by_title( $title ) {
			
			$options = array( 'title' => $title );

			return $this->parse_listing( 'people', 500, 'person', $options );
			
		}

		public function get_person( $id ) {
			
			$xml = $this->make_request( 'people/'. $id );
			$this->check_for_errors( 'Person' );
			$xml_object = simplexml_load_string( $xml );
			
			$person = new HighrisePerson( $this );
			$person->load_from_xml_object( $xml_object );
			
			return $person;
			
		}
		
		public function get_person_emails( $person_id ) {

			return $this->parse_listing( 'people/' . $person_id . '/emails', 25, 'email' );
		
		}

		public function get_person_notes( $person_id ) {
			
			return $this->parse_listing( 'people/' . $person_id . 'notes', 25, 'note' );
			
		}

		public function get_tags() {
			
			$xml = $this->make_request( 'tags' );
			$this->check_for_errors( 'Tags' );
			$xml_object = simplexml_load_string( $xml );			
			
			$tags = array();
			foreach( $xml_object->tag as $tag ) {
				
				$tags[(string)$tag->name] = new HighriseTag( (string)$tag->id, (string)$tag->name );
			
			}
			
			return $tags;
		}

		public function get_task( $id ) {
			
			$xml = $this->make_request( 'tasks/' . $id );
			$this->check_for_errors( 'Task' );
			$task_xml = simplexml_load_string( $xml );
			
			$task = new HighriseTask( $this );
			$task->load_from_xml_object( $task_xml );
			
			return $task;
			
		}

	    public function get_task_categories() {
		    
			return $this->get_categories( 'task' );
	      
	    }
	    
		public function get_upcoming_tasks() {
			
			$xml = $this->make_request( 'tasks/upcoming' );
			$this->check_for_errors( 'Tasks' );
			
			return $this->parse_tasks( $xml );
			
		}

		public function get_users() {
			
			$xml = $this->make_request( 'users' );
			$this->check_for_errors( 'User' );
			
			$xml_object = simplexml_load_string( $xml );
			
			$users = array();
			foreach ( $xml_object->user as $xml_user ) {
				
				$user = new HighriseUser();
				$user->load_from_xml_object( $xml_user );
				
				$users[] = $user;
				
			}
			
			return $users;
			
		}

		public function get_user_by_email( $email ) {

			foreach ( $this->get_users() as $user ) {
				
				if ( strtolower( trim( $email ) ) == strtolower( trim( $user->email_address ) ) )
					return $user;
				
			} 
			
			return false;
		}

		public function me() {
			
			$xml = $this->make_request( 'me' );
			$this->check_for_errors( 'User' );
			
			$user = new HighriseUser();
			$user->load_from_xml_object( simplexml_load_string( $xml ) );
			
			return $user;
			
		}
		
		public function search_companies( $query ) {
			
			$options = array();
			foreach ( $query as $criteria => $value ) {
				
				$options['criteria[' . $criteria . ']'] = $value; 
				
			}

			return $this->parse_listing( 'companies/search', 25, 'company', $options );
			
		}

		public function search_people( $query ) {
			
			$options = array();
			foreach ( $query as $criteria => $value ) {
				
				$options['criteria[' . $criteria . ']'] = $value; 
				
			}

			return $this->parse_listing( 'people/search', 25, 'person', $options );
			
		}

		public function set_account( $account ) {
			
			$this->account = $account;
			
		}
		
		public function set_token( $token ) {
			
			$this->token = $token;
			curl_setopt( $this->curl, CURLOPT_USERPWD, $this->token . ':x' );
			
		}

	}
