
'use strict';

var DJTIMELINE = DJTIMELINE || {};

(function() {

	DJTIMELINE.namespace = function(nsString) {

	    var parts 	= nsString.split( '.' ),
	        parent 	= DJTIMELINE,
	        i;

	    if ( parts[0] === 'DJTIMELINE' ) {
	    	parts = parts.slice(1);
	    }

	    for ( i = 0; i < parts.length; i += 1 ) {
	    	if ( typeof parent[ parts[i] ] === 'undefined' ) {
	        	parent[ parts[i] ] = {};
	      	}
	      	parent = parent[ parts[i] ];
	    }

	    return parent;
	};
}());

//-----------------------------------------------------------------------------------------------
DJTIMELINE.namespace( 'controller' );

DJTIMELINE.controller = (function() {

  	var init = function init() {
  		handlers();
  		
    	var doc = document.documentElement;
    	doc.setAttribute('data-useragent', navigator.userAgent);
	};

    function handlers() {
    	jQuery(window).load(function() {
    		DJTIMELINE.main.init();
    	});
    };

	return {
		init: init
	};

})();

//-----------------------------------------------------------------------------------------------
jQuery(document).ready(function() {
	DJTIMELINE.controller.init();
});
