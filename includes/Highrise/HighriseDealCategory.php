<?php

	namespace Highrise;

	class HighriseDealCategory extends HighriseCategory {
		
		public function __construct( HighriseAPI $highrise ) {

			parent::__construct( $highrise, 'deal' );
			
		}
	
	}
