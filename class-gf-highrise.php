<?php
	
GFForms::include_feed_addon_framework();

class GFHighrise extends GFFeedAddOn {
	
	protected $_version = GF_HIGHRISE_VERSION;
	protected $_min_gravityforms_version = '1.9.12';
	protected $_slug = 'gravityformshighrise';
	protected $_path = 'gravityformshighrise/highrise.php';
	protected $_full_path = __FILE__;
	protected $_url = 'http://www.gravityforms.com';
	protected $_title = 'Gravity Forms Highrise Add-On';
	protected $_short_title = 'Highrise';
	protected $_enable_rg_autoupgrade = true;
	protected $api = null;
	protected $_new_custom_fields = array();
	private static $_instance = null;

	/* Permissions */
	protected $_capabilities_settings_page = 'gravityforms_highrise';
	protected $_capabilities_form_settings = 'gravityforms_highrise';
	protected $_capabilities_uninstall = 'gravityforms_highrise_uninstall';

	/* Members plugin integration */
	protected $_capabilities = array( 'gravityforms_highrise', 'gravityforms_highrise_uninstall' );

	/**
	 * Get instance of this class.
	 * 
	 * @access public
	 * @static
	 * @return $_instance
	 */	
	public static function get_instance() {
		
		if ( self::$_instance == null ) {
			self::$_instance = new self;
		}

		return self::$_instance;
		
	}

	/**
	 * Register needed styles.
	 * 
	 * @access public
	 * @return array $styles
	 */
	public function styles() {
		
		$styles = array(
			array(
				'handle'  => 'gform_highrise_form_settings_css',
				'src'     => $this->get_base_url() . '/css/form_settings.css',
				'version' => $this->_version,
				'enqueue' => array(
					array( 'admin_page' => array( 'form_settings' ) ),
				)
			)
		);
		
		return array_merge( parent::styles(), $styles );
		
	}

	/**
	 * Setup plugin settings fields.
	 * 
	 * @access public
	 * @return array
	 */
	public function plugin_settings_fields() {
						
		return array(
			array(
				'title'       => '',
				'description' => $this->plugin_settings_description(),
				'fields'      => array(
					array(
						'name'              => 'account_url',
						'label'             => esc_html__( 'Account URL', 'gravityformshighrise' ),
						'type'              => 'text',
						'class'             => 'small',
						'after_input'       => '.highrisehq.com',
						'feedback_callback' => array( $this, 'has_valid_account_url' )
					),
					array(
						'name'              => 'api_token',
						'label'             => esc_html__( 'API Token', 'gravityformshighrise' ),
						'type'              => 'text',
						'class'             => 'medium',
						'feedback_callback' => array( $this, 'initialize_api' )
					),
					array(
						'type'              => 'save',
						'messages'          => array(
							'success' => esc_html__( 'Highrise settings have been updated.', 'gravityformshighrise' )
						),
					),
				),
			),
		);
		
	}

	/**
	 * Prepare plugin settings description.
	 * 
	 * @access public
	 * @return string $description
	 */
	public function plugin_settings_description() {
		
		$description  = '<p>';
		$description .= sprintf(
			esc_html__( 'Highrise is a contact management tool makes it easy to track tasks, contacts and notes. Use Gravity Forms to collect customer information and automatically add them to your Highrise account. If you don\'t have a Highrise account, you can %1$s sign up for one here.%2$s', 'gravityformshighrise' ),
			'<a href="http://www.highrise.com/" target="_blank">', '</a>'
		);
		$description .= '</p>';
		
		if ( ! $this->initialize_api() ) {
			
			$description .= '<p>';
			$description .= esc_html__( 'Gravity Forms Highrise Add-On requires your account URL and API Token, which can be found in the API Token tab on the My Info page.', 'gravityformshighrise' );
			$description .= '</p>';
			
		}
				
		return $description;
		
	}
	
	/**
	 * Setup fields for feed settings.
	 * 
	 * @access public
	 * @return array
	 */
	public function feed_settings_fields() {
		
		/* Build base fields array. */
		$base_fields = array(
			'title'  => '',
			'fields' => array(
				array(
					'name'           => 'feed_name',
					'label'          => esc_html__( 'Feed Name', 'gravityformshighrise' ),
					'type'           => 'text',
					'required'       => true,
					'default_value'  => $this->get_default_feed_name(),
					'tooltip'        => '<h6>'. esc_html__( 'Name', 'gravityformshighrise' ) .'</h6>' . esc_html__( 'Enter a feed name to uniquely identify this setup.', 'gravityformshighrise' )
				),
				array(
					'name'           => 'action',
					'label'          => esc_html__( 'Action', 'gravityformshighrise' ),
					'required'       => true,
					'type'           => 'hidden',
					'default_value'  => 'contact'
				)
			)
		);
		
		/* Build contact fields array. */
		$contact_fields = array(
			'title'  => esc_html__( 'Contact Details', 'gravityformshighrise' ),
			'fields' => array(
				array(
					'name'           => 'contact_standard_fields',
					'label'          => esc_html__( 'Map Fields', 'gravityformshighrise' ),
					'type'           => 'field_map',
					'field_map'      => $this->standard_fields_for_feed_mapping(),
					'tooltip'        => '<h6>'. esc_html__( 'Map Fields', 'gravityformshighrise' ) .'</h6>' . esc_html__( 'Select which Gravity Form fields pair with their respective Highrise fields.', 'gravityformshighrise' )
				),
				array(
					'name'           => 'contact_custom_fields',
					'label'          => '',
					'type'           => 'dynamic_field_map',
					'field_map'      => $this->custom_fields_for_feed_mapping(),
				),
				array(
					'name'           => 'contact_note',
					'label'          => esc_html__( 'Contact Note', 'gravityformshighrise' ),
					'type'           => 'textarea',
					'class'          => 'medium merge-tag-support mt-position-right mt-hide_all_fields',
				),		
				array(
					'name'           => 'contact_visible_to',
					'label'          => esc_html__( 'Contact Visibility', 'gravityformshighrise' ),
					'type'           => 'select',
					'tooltip'        => '<h6>'. esc_html__( 'Contact Visibility', 'gravityformshighrise' ) .'</h6>' . esc_html__( 'Choose who has access to this contact.', 'gravityformshighrise' ),
					'choices'        => $this->visible_to_for_feed_setting()
				),
			)
		);

		/* Build conditional logic fields array. */
		$conditional_fields = array(
			'title'  => '',
			'fields' => array(
				array(
					'name'           => 'feed_condition',
					'type'           => 'feed_condition',
					'label'          => esc_html__( 'Conditional Logic', 'gravityformshighrise' ),
					'checkbox_label' => esc_html__( 'Enable', 'gravityformshighrise' ),
					'instructions'   => esc_html__( 'Export to Highrise if', 'gravityformshighrise' ),
					'tooltip'        => '<h6>' . esc_html__( 'Conditional Logic', 'gravityformshighrise' ) . '</h6>' . esc_html__( 'When conditional logic is enabled, form submissions will only be exported to Highrise when the condition is met. When disabled, all form submissions will be posted.', 'gravityformshighrise' )
				),
				
			)
		);
		
		return array( $base_fields, $contact_fields, $conditional_fields );
		
	}

	/**
	 * Fork of maybe_save_feed_settings to create new Highrise custom fields.
	 * 
	 * @access public
	 * @param int $feed_id
	 * @param int $form_id
	 * @return int $feed_id
	 */
	public function maybe_save_feed_settings( $feed_id, $form_id ) {

		if ( ! rgpost( 'gform-settings-save' ) ) {
			return $feed_id;
		}

		// store a copy of the previous settings for cases where action would only happen if value has changed
		$feed = $this->get_feed( $feed_id );
		$this->set_previous_settings( $feed['meta'] );

		$settings = $this->get_posted_settings();
		$settings = $this->create_new_custom_fields( $settings );
		$sections = $this->get_feed_settings_fields();
		$settings = $this->trim_conditional_logic_vales( $settings, $form_id );

		$is_valid = $this->validate_settings( $sections, $settings );
		$result   = false;

		if ( $is_valid ) {
			$feed_id = $this->save_feed_settings( $feed_id, $form_id, $settings );
			if ( $feed_id ){
				GFCommon::add_message( $this->get_save_success_message( $sections ) );
			}
			else{
				GFCommon::add_error_message( $this->get_save_error_message( $sections ) );
			}
		}
		else{
			GFCommon::add_error_message( $this->get_save_error_message( $sections ) );
		}

		return $feed_id;
	}

	/**
	 * Prepare Highrise visible to options for feed settings field.
	 * 
	 * @access public
	 * @return array $visible_to
	 */
	public function visible_to_for_feed_setting() {
				
		$visible_to = array(
			array(
				'label' => esc_html__( 'Everyone in your account', 'gravityformshighrise' ),
				'value' => 'Everyone'
			)
		);
		
		/* If Highrise API credentials are invalid, return the visible to array. */
		if ( ! $this->initialize_api() )
			return $lists;
			
		/* Add the current Highrise user. */
		$visible_to[] = array(
			'label' => $this->api->me()->name,
			'value' => 'Owner'
		);
		
		/* Get the current Highrise groups. */
		$highrise_groups = $this->api->get_groups();
		
		if ( ! empty( $highrise_groups ) ) {
			
			$groups = array();
			
			foreach ( $highrise_groups as $group ) {
				
				$groups[] = array(
					'label' => $group->name,
					'value' => $group->id	
				);
				
			}
			
			$visible_to[] = array(
				'label'  => esc_html__( 'Groups', 'gravityformshighrise' ),
				'choices' => $groups
			);
			
		}
		
		return $visible_to;
		
	}

	/**
	 * Prepare standard fields for feed field mapping.
	 * 
	 * @access public
	 * @return array $fields
	 */
	public function standard_fields_for_feed_mapping() {
		
		return array(
			array(	
				'name'          => 'first_name',
				'label'         => esc_html__( 'First Name', 'gravityformshighrise' ),
				'required'      => true,
				'default_value' => $this->get_first_field_by_type( 'name', 3 )
			),
			array(	
				'name'          => 'last_name',
				'label'         => esc_html__( 'Last Name', 'gravityformshighrise' ),
				'required'      => true,
				'default_value' => $this->get_first_field_by_type( 'name', 6 )
			),
			array(	
				'name'          => 'title',
				'label'         => esc_html__( 'Title', 'gravityformshighrise' ),
			),
			array(	
				'name'          => 'company',
				'label'         => esc_html__( 'Company', 'gravityformshighrise' ),
			),
		);
		
	}

	/**
	 * Prepare contact and custom fields for feed field mapping.
	 * 
	 * @access public
	 * @return array $fields
	 */
	public function custom_fields_for_feed_mapping() {
		
		return array(
			array(
				'label'   => esc_html__( 'Choose a Field', 'gravityformshighrise' ),	
			),
			array(	
				'value'   => 'background',
				'label'   => esc_html__( 'Background', 'gravityformshighrise' ),
			),
			array(	
				'value'   => 'linkedin',
				'label'   => esc_html__( 'LinkedIn URL', 'gravityformshighrise' ),
			),
			array(	
				'value'   => 'twitter',
				'label'   => esc_html__( 'Twitter Username', 'gravityformshighrise' ),
			),
			array(	
				'label'   => esc_html__( 'Email Address', 'gravityformshighrise' ),
				'choices' => array(
					array(
						'label' => esc_html__( 'Work', 'gravityformshighrise' ),
						'value' => 'email_work'	
					),
					array(
						'label' => esc_html__( 'Home', 'gravityformshighrise' ),
						'value' => 'email_home'	
					),
					array(
						'label' => esc_html__( 'Other', 'gravityformshighrise' ),
						'value' => 'email_other'	
					),
				)
			),
			array(	
				'label'   => esc_html__( 'Phone Number', 'gravityformshighrise' ),
				'choices' => array(
					array(
						'label' => esc_html__( 'Work', 'gravityformshighrise' ),
						'value' => 'phone_work'	
					),
					array(
						'label' => esc_html__( 'Mobile', 'gravityformshighrise' ),
						'value' => 'phone_mobile'	
					),
					array(
						'label' => esc_html__( 'Fax', 'gravityformshighrise' ),
						'value' => 'phone_fax'	
					),
					array(
						'label' => esc_html__( 'Pager', 'gravityformshighrise' ),
						'value' => 'phone_pager'	
					),
					array(
						'label' => esc_html__( 'Home', 'gravityformshighrise' ),
						'value' => 'phone_home'	
					),
					array(
						'label' => esc_html__( 'Skype', 'gravityformshighrise' ),
						'value' => 'phone_skype'	
					),
					array(
						'label' => esc_html__( 'Other', 'gravityformshighrise' ),
						'value' => 'phone_other'	
					),
				)
			),
			array(	
				'label'   => esc_html__( 'Website', 'gravityformshighrise' ),
				'choices' => array(
					array(
						'label' => esc_html__( 'Work', 'gravityformshighrise' ),
						'value' => 'website_work'	
					),
					array(
						'label' => esc_html__( 'Personal', 'gravityformshighrise' ),
						'value' => 'website_personal'	
					),
					array(
						'label' => esc_html__( 'Other', 'gravityformshighrise' ),
						'value' => 'website_other'	
					),
				)
			),
			array(	
				'label'   => esc_html__( 'Address', 'gravityformshighrise' ),
				'choices' => array(
					array(
						'label' => esc_html__( 'Work', 'gravityformshighrise' ),
						'value' => 'address_work'	
					),
					array(
						'label' => esc_html__( 'Home', 'gravityformshighrise' ),
						'value' => 'address_home'	
					),
					array(
						'label' => esc_html__( 'Other', 'gravityformshighrise' ),
						'value' => 'address_other'	
					),
				)
			),
			array(	
				'label'   => esc_html__( 'Instant Messenger', 'gravityformshighrise' ),
				'choices' => array(
					array(
						'label' => esc_html__( 'AIM', 'gravityformshighrise' ),
						'value' => 'im_aim'	
					),
					array(
						'label' => esc_html__( 'MSN', 'gravityformshighrise' ),
						'value' => 'im_msn'	
					),
					array(
						'label' => esc_html__( 'ICQ', 'gravityformshighrise' ),
						'value' => 'im_icq'	
					),
					array(
						'label' => esc_html__( 'Jabber', 'gravityformshighrise' ),
						'value' => 'im_jabber'	
					),
					array(
						'label' => esc_html__( 'Yahoo', 'gravityformshighrise' ),
						'value' => 'im_yahoo'	
					),
					array(
						'label' => esc_html__( 'Skype', 'gravityformshighrise' ),
						'value' => 'im_skype'	
					),
					array(
						'label' => esc_html__( 'QQ', 'gravityformshighrise' ),
						'value' => 'im_qq'	
					),
					array(
						'label' => esc_html__( 'Sametime', 'gravityformshighrise' ),
						'value' => 'im_sametime'	
					),
					array(
						'label' => esc_html__( 'Gadu-Gadu', 'gravityformshighrise' ),
						'value' => 'im_gadu-gadu'	
					),
					array(
						'label' => esc_html__( 'Google Talk', 'gravityformshighrise' ),
						'value' => 'im_gtalk'	
					),
					array(
						'label' => esc_html__( 'Other', 'gravityformshighrise' ),
						'value' => 'im_other'	
					),
				)
			),
			array(
				'label'   => esc_html__( 'Custom Fields', 'gravityformshighrise' ),
				'choices' => $this->get_custom_fields()
			)
		);
		
	}

	/**
	 * Create new Highrise custom fields.
	 * 
	 * @access public
	 * @param array $settings
	 * @return array $settings
	 */
	public function create_new_custom_fields( $settings ) {

		global $_gaddon_posted_settings;

		/* If no custom fields are set or if the API credentials are invalid, return settings. */
		if ( empty( $settings['contact_custom_fields'] ) || ! $this->initialize_api() ) {
			return $settings;
		}
	
		/* Loop through each custom field. */
		foreach ( $settings['contact_custom_fields'] as $index => &$field ) {
			
			/* If no custom key is set, move on. */
			if ( rgblank( $field['custom_key'] ) ) {
				continue;
			}
				
			try {
				
				$field_name = $field['custom_key'];
				
				/* Create new field. */
				$new_field = new Highrise\HighriseCustomField( $this->api );
				$new_field->set_label( $field_name );
				$new_field->save();
			
				/* Replace key for field with new shortcut name and reset custom key. */
				$field['key'] = 'custom_' . $new_field->get_id();
				$field['custom_key'] = '';
				
				/* Update POST field to ensure front-end display is up-to-date. */
				$_gaddon_posted_settings['contact_custom_fields'][ $index ]['key'] = 'custom_' . $new_field->get_id();
				$_gaddon_posted_settings['contact_custom_fields'][ $index ]['custom_key'] = '';
				
				/* Push to new custom fields array to update the UI. */			
				$this->_new_custom_fields[] = array(
					'label' => $field_name,
					'value' => 'custom_' . $new_field->get_id(),
				);
				
			} catch ( Exception $e ) {
				
				$this->log_error( __METHOD__ . '(): Could not created custom field "' . $new_field->get_label() . '"; ' . $e->getMessage() );
				
			}
			
		}
		
		return $settings;
		
	}
	
	/**
	 * Get Highrise custom fields.
	 * 
	 * @access public
	 * @return array $custom_fields
	 */
	public function get_custom_fields() {
		
		$custom_fields = array();
		
		/* If API instance is not initialized, return custom fields array. */
		if ( ! $this->initialize_api() ) {
			return $custom_fields;
		}
		
		/* Get the Highrise custom fields. */
		$hr_fields = $this->api->get_custom_fields();
		
		/* Add Highrise custom fields to custom fields array. */
		if ( ! empty( $hr_fields ) ) {
			
			foreach ( $hr_fields as $custom_field ) {
				
				$custom_fields[] = array(
					'label'   => $custom_field->get_label(),
					'value'   => 'custom_' . $custom_field->get_id()
				);
				
			}
			
		}

		if ( ! empty( $this->_new_custom_fields ) ) {
			
			foreach ( $this->_new_custom_fields as $new_field ) {
				
				$found_custom_field = false;
				foreach ( $custom_fields as $field ) {
					
					if ( $field['value'] == $new_field['value'] )
						$found_custom_field = true;
					
				}
				
				if ( ! $found_custom_field )
					$custom_fields[] = array(
						'label' => $new_field['label'],
						'value' => $new_field['value']	
					);
				
			}
			
		}
		
		/* Add "Add New Custom Field" option. */
		$custom_fields[] = array(
			'label'   => esc_html__( 'Add New Custom Field', 'gravityformshighrise' ),
			'value'   => 'gf_custom'
		);
		
		/* Return custom fields array. */
		return $custom_fields;
		
	}

	/**
	 * Setup columns for feed list table.
	 * 
	 * @access public
	 * @return array
	 */
	public function feed_list_columns() {
		
		return array(
			'feed_name' => esc_html__( 'Name', 'gravityformshighrise' ),
			'action'    => esc_html__( 'Action', 'gravityformshighrise' )
		);
		
	}

	/**
	 * Get value for action feed list column.
	 * 
	 * @access public
	 * @param array $feed
	 * @return string $action
	 */
	public function get_column_value_action( $feed ) {

		switch ( $feed['meta']['action'] ) {
			
			case 'contact':
				return esc_html__( 'Create a New Contact', 'gravityformshighrise' );
			case 'case-contact':
				return esc_html__( 'Create a New Case, Assign New Contact to Case', 'gravityformshighrise' );
			
		}
		
	}

	/**
	 * Set feed creation control.
	 * 
	 * @access public
	 * @return bool
	 */
	public function can_create_feed() {
		
		return $this->initialize_api();
		
	}
	
	/**
	 * Process feed.
	 * 
	 * @access public
	 * @param array $feed
	 * @param array $entry
	 * @param array $form
	 * @return void
	 */
	public function process_feed( $feed, $entry, $form ) {
		
		$this->log_debug( __METHOD__ . '(): Processing feed.' );
		
		/* If API instance is not initialized, exit. */
		if ( ! $this->initialize_api() ) {
			
			$this->add_feed_error( esc_html__( 'Feed was not processed because API was not initialized.', 'gravityformshighrise' ), $feed, $entry, $form );
			return;
			
		}
		
		/* Setup mapped fields array. */
		$contact_standard_fields = $this->get_field_map_fields( $feed, 'contact_standard_fields' );
		$contact_custom_fields = $this->get_dynamic_field_map_fields( $feed, 'contact_custom_fields' );

		/* Setup contact data array. */
		$contact_data = array(
			'first_name' => $this->get_field_value( $form, $entry, $contact_standard_fields['first_name'] ),
			'last_name'  => $this->get_field_value( $form, $entry, $contact_standard_fields['last_name'] ),
			'title'      => $this->get_field_value( $form, $entry, $contact_standard_fields['title'] ),
			'company'    => $this->get_field_value( $form, $entry, $contact_standard_fields['company'] ),
			'note'       => $feed['meta']['contact_note']
		);
		
		/* If the first name is empty, exit. */
		if ( rgblank( $contact_data['first_name'] ) ) {
			
			$this->add_feed_error( esc_html__( 'Contact could not be created as first name was not provided.', 'gravityformshighrise' ), $feed, $entry, $form );
			
			return;			
		
		}
		
		/* Start building the contact. */
		$contact = new Highrise\HighrisePerson( $this->api );
		$contact->set_First_name( $contact_data['first_name'] );
		$contact->set_last_name( $contact_data['last_name'] );
		$contact->set_title( $contact_data['title'] );
		$contact->set_company_name( $contact_data['company'] );
		
		/* Add any mapped email fields. */
		foreach ( $contact_custom_fields as $field_key => $field ) {
			
			/* Get the email address. */
			$email_address = $this->get_field_value( $form, $entry, $field );

			/* If this is not an email address field or the email address is blank, move on. */
			if ( strpos( $field_key, 'email_' ) !== 0 || rgblank( $email_address ) )
				continue;
			
			/* Prepare the location field. */
			$location = ucfirst( str_replace( 'email_', '', $field_key ) );
			
			/* Add the email address to the contact. */
			$contact->add_email_address( $email_address, $location );
			
		}
		
		/* Add any mapped phone numbers. */
		foreach ( $contact_custom_fields as $field_key => $field ) {
			
			/* Get the phone number. */
			$phone_number = $this->get_field_value( $form, $entry, $field );

			/* If this is not an phone number field or the phone number is blank, move on. */
			if ( strpos( $field_key, 'phone_' ) !== 0 || rgblank( $phone_number ) )
				continue;
			
			/* Prepare the location field. */
			$location = ucfirst( str_replace( 'phone_', '', $field_key ) );
			
			/* Add the phone number to the contact. */
			$contact->add_phone_number( $phone_number, $location );
			
		}
		
		/* Add any mapped websites. */
		foreach ( $contact_custom_fields as $field_key => $field ) {
			
			/* Get the website address. */
			$website_address = $this->get_field_value( $form, $entry, $field );

			/* If this is not an website address field or the website address is blank, move on. */
			if ( strpos( $field_key, 'website_' ) !== 0 || rgblank( $website_address ) )
				continue;
			
			/* Prepare the location field. */
			$location = ucfirst( str_replace( 'website_', '', $field_key ) );
			
			/* Add the website address to the contact. */
			$contact->add_web_address( $website_address, $location );
			
		}

		/* Add any mapped address. */
		foreach ( $contact_custom_fields as $field_key => $field ) {
			
			/* If this is not an address mapped field, move on. */
			if ( strpos( $field_key, 'address_' ) !== 0 )
				continue;
			
			$address_field = GFFormsModel::get_field( $form, $field );
			
			/* If the selected field is not an address field, move on. */
			if ( GFFormsModel::get_input_type( $address_field ) !== 'address' )
				continue;
				
			/* Prepare the location field. */
			$location = ucfirst( str_replace( 'address_', '', $field_key ) );

			/* Prepare the address. */
			$address_field_id = $address_field->id;
			$address = array(
				'street'  => $entry[$address_field_id . '.1'] .' '. $entry[$address_field_id . '.2'],
				'city'    => $entry[$address_field_id . '.3'],
				'state'   => $entry[$address_field_id . '.4'],
				'zip'     => $entry[$address_field_id . '.5'],
				'country' => $entry[$address_field_id . '.6']
			);
			
			/* Add the address to the contact. */
			$contact->add_address( $address['street'], $address['city'], $address['state'], $address['zip'], $address['country'], $location );
			
		}

		/* Add any mapped instant message clients. */
		foreach ( $contact_custom_fields as $field_key => $field ) {
			
			/* Get the IM handle. */
			$handle = $this->get_field_value( $form, $entry, $field );

			/* If this is not an IM field or the IM handle is blank, move on. */
			if ( strpos( $field_key, 'im_' ) !== 0 || rgblank( $handle ) )
				continue;
			
			/* Prepare the protocl field. */
			$protocol = str_replace( 'im_', '', $field_key );
			$protocols = array(
				'aim'       => 'AIM',
				'msn'       => 'MSN',
				'icq'       => 'ICQ',
				'jabber'    => 'Jabber',
				'yahoo'     => 'Yahoo',
				'skype'     => 'Skype',
				'qq'        => 'QQ',
				'sametime'  => 'Sametime',
				'gadu-gadu' => 'Gadu-Gadu',
				'gtalk'     => 'Google Talk',
				'other'     => 'other'
			);
			$protcol = $protocols[$protocol];
			
			/* Add the website address to the contact. */
			$contact->add_instant_messenger( $protcol, $handle );
			
		}
		
		/* Add any mapped custom fields. */
		foreach ( $contact_custom_fields as $field_key => $field ) {
			
			/* Get the field value. */
			$field_value = $this->get_field_value( $form, $entry, $field );

			/* If this is not an custom field or the field value is blank, move on. */
			if ( strpos( $field_key, 'custom_' ) !== 0 || rgblank( $field_value ) )
				continue;
			
			/* Get the custom field ID. */
			$custom_field_id = str_replace( 'custom_', '', $field_key );
			
			/* Create a new custom field object. */
			$custom_field = new Highrise\HighriseCustomField( $this->api );
			$custom_field->set_subject_field_id( $custom_field_id );
			$custom_field->set_xml_type( 'subject_data' );
			
			/* Add custom field object to contact. */
			$contact->add_custom_field( $custom_field, $field_value );
			
		}
		
		/* Add any LinkedIn URLs or Twitter usernames. */
		foreach ( $contact_custom_fields as $field_key => $field ) {
			
			/* Get the field value. */
			$field_value = $this->get_field_value( $form, $entry, $field );

			/* If this is not a LinkedIn/Twitter field or the field value is blank, move on. */
			if ( ! in_array( $field_key, array( 'linkedin', 'twitter' ) ) || rgblank( $field_value ) )
				continue;
			
			if ( $field_key == 'linkedin' )
				$contact->set_linkedin_url( $field_value );

			if ( $field_key == 'twitter' )
				$contact->add_twitter_account( $field_value );
			
		}
		
		/* Add visibility state. */
		$contact_visibility = $feed['meta']['contact_visible_to'];
		if ( is_numeric( $contact_visibility ) ) {
			
			$contact->set_visible_to( 'NamedGroup' );
			$contact->set_group_id( $contact_visibility );
			
		} else {
			
			$contact->set_visible_to( $contact_visibility );
			
		}

		try {
			
			/* Save the contact. */
			$contact->save();
			
			/* Log that case was saved. */
			$this->log_debug( __METHOD__ . '(): Contact #'. $contact->get_id() .' created.' );
				
		} catch ( Exception $e ) {
			
			/* Log that case was not saved. */
			$this->add_feed_error( sprintf(
				esc_html__( 'Contact could not be created. %s', 'gravityformshighrise' ),
				$e->getMessage()
			), $feed, $entry, $form );
			
			return;
			
		}


		/* Add contact note if needed. */
		if ( ! rgblank( $contact_data['note'] ) ) {
			
			/* Replace merge tags. */
			$contact_data['note'] = GFCommon::replace_variables( $contact_data['note'], $form, $entry );
			
			/* Create new note object. */
			$note = new Highrise\HighriseNote( $this->api );
			$note->set_body( $contact_data['note'] );
			$note->set_subject_type( 'Party' );
			$note->set_subject_id( $contact->get_id() );
			$note->save();
			
			$this->log_debug( __METHOD__ . '(): Note #'. $note->get_id() .' added to contact #'. $contact->get_id() . '.' );
			
		}

	}	

	/**
	 * Initializes Highrise API if credentials are valid.
	 * 
	 * @access public
	 * @return bool
	 */
	public function initialize_api() {

		if ( ! is_null( $this->api ) ) {
			return true;
		}
		
		/* Load the Highrise API library. */
		require_once 'includes/highrise-autoload.php';

		$test = new Highrise\HighriseAPI();

		/* Get the plugin settings */
		$settings = $this->get_plugin_settings();
		
		/* If any of the account information fields are empty, return null. */
		if ( rgblank( $settings['account_url'] ) || rgblank( $settings['api_token'] ) )
			return null;
		
		$highrise = new Highrise\HighriseAPI();
		$highrise->set_account( $settings['account_url'] );
		$highrise->set_token( $settings['api_token'] );
		
		try {
			
			/* Run API test. */
			$highrise->me();
			
			/* Log that test passed. */
			$this->log_debug( __METHOD__ . '(): API credentials are valid.' );
			
			/* Assign Highrise object to the class. */
			$this->api = $highrise;
			
			return true;
			
		} catch ( Exception $e ) {
			
			/* Log that test failed. */
			$this->log_error( __METHOD__ . '(): API credentials are invalid; '. $e->getMessage() );			

			return false;
			
		}
		
	}
	
	/**
	 * Checks validity of Highrise account URL.
	 * 
	 * @access public
	 * @param mixed $account_url
	 * @return void
	 */
	public function has_valid_account_url( $account_url ) {
		
		/* If no API URL is set, return null. */
		if ( rgblank( $account_url ) )
			return null;
		
		$this->log_debug( __METHOD__ . "(): Validating account url {$account_url}." );
		
		$highrise = new Highrise\HighriseAPI();
		$highrise->set_account( $account_url );
		
		try {
			
			/* Run API test. */
			$highrise->me();
						
		} catch ( Exception $e ) {
			
			if ( $e->getMessage() == 'User not found' ) {
				
				/* Log that test failed. */
				$this->log_error( __METHOD__ . '(): Account URL is invalid; '. $e->getMessage() );			
	
				return false;

				
			} else {
				
				/* Log that test passed. */
				$this->log_debug( __METHOD__ . '(): Account URL is valid.' );
				
				return true;

				
			}
			
		}
		
	}

}