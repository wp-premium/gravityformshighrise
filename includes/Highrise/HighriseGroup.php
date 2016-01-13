<?php

	namespace Highrise;

	class HighriseGroup {
		
	    public $id;
	    public $name;
	    public $users;
	
	    public function load_from_xml_object( $xml_object ) {
		    
	        $this->set_id( $xml_object->{'id'} );
	        $this->set_name( $xml_object->{'name'} );
	        $this->set_users( $xml_object->{'users'} );
	
	        return true;
	        
	    }
	
	    public function get_id() {
		    
	        return $this->id;
	        
	    }
	    
	    public function get_name() {
		    
	        return $this->name;
	        
	    }
	    
	    public function get_users() {
		    
	        return $this->users;
	        
	    }
	
	    public function set_id( $id ) {
		    
	        $this->id = (string)$id;
	        
	    }
	    
	    public function set_name( $name ) {
		    
	        $this->name = (string)$name;
	        
	    }
	    
	    public function set_users( $users ) {
		    
	        $_users = array();
	        
	        foreach ( $users->{'user'} as $_user ) {
		        
	            $user = new HighriseUser();
	            $user->load_from_xml_object( $_user );
	            
	            $_users[] = $user;
	            
	        }
	
	        $this->users = $_users;
	        
	    }
	
	}
