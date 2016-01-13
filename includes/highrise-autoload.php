<?php
	
	namespace Highrise;
	
	function autoload ( $name ) {
		
	    if ( \substr_compare( $name, "Highrise\\", 0, 9 ) !== 0 )
			return;
	
	    /* Take the "Highrise\" prefix off. */
	    $stem = \substr( $name, 9 );
	
	    /* Convert "\" and "_" to path separators. */
	    $pathified_stem = \str_replace( array( "\\", "_" ), '/' , $stem );
	
	    $path = __DIR__ . '/Highrise/' . $pathified_stem . '.php';
	    
	    if ( \is_file( $path ) )
	        require_once $path;
	    
	}
	
	\spl_autoload_register( 'Highrise\autoload' );
