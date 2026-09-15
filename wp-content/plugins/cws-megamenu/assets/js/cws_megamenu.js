if ( window.pagenow == 'nav-menus' ){
	document.addEventListener( 'DOMContentLoaded', function (){
		window.cws_megamenu = new cws_megamenu ();
	}, false );
}

function cws_megamenu (){
	this.megamenu_item_class = "menu-item-megamenu_item";
	this.items = [];
	this.init = cws_megamenu_init;
	this.get_items_data = cws_megamenu_get_items_data;
	this.check_if_item_processed = cws_megamenu_check_if_item_processed;
	this.init ();
}

function cws_megamenu_init (){
	var that = this;
	var i;
	var existed_item_els = document.getElementsByClassName( that.megamenu_item_class );
	var existed_items = that.get_items_data.apply( that, existed_item_els );
	document.addEventListener( 'DOMNodeInserted', function ( e ){
		var el = e.srcElement ? e.srcElement : e.target;
		var re = new RegExp( that.megamenu_item_class );
		var megamenu_obj;
		if ( re.test( el.className ) && !that.check_if_item_processed( el.id ) ){
			new cws_megamenu_item ( el.id );
			that.items.push( el.id );
		}
	}, false );	
}

function cws_megamenu_check_if_item_processed ( id ){
	var that = this;
	return that.items.indexOf( id ) != -1;	
}

function cws_megamenu_get_items_data (){
	var that = this;
	var i, req;
	var els = arguments;
	var item_ids = [];
	for ( i = 0; i < els.length; i++ ){
		item_ids[i] = els[i].id;
	}
	req = new XMLHttpRequest();
	req.onreadystatechange = function (){
		var items_data, i;
		if ( req.readyState == 4 && req.status == 200 ){
			items_data = JSON.parse( req.responseText );
			for ( item_id in items_data ){
				new cws_megamenu_item( item_id, items_data[item_id] );
				that.items.push( item_id );
			}
		}
	}
	req.open( "POST", ajaxurl, true );
	req.setRequestHeader( "Content-type", "application/x-www-form-urlencoded" );
	req.send( "action=cws_megamenu_items_data&ids=" + JSON.stringify( item_ids ) );	
}

function cws_megamenu_item ( id, data ){
	var match, opt_key;
	if ( id == undefined ) return false;
	this.id = id;
	this.data = typeof data == "object" ? data : {};
	this.opts = window.cws_megamenu_opts;

	for ( opt_key in this.opts ){
		if ( this.data[opt_key] == undefined ){
			if ( this.opts[opt_key]['value'] != undefined ){
				this.data[opt_key] = this.opts[opt_key]['value'];
			}
			else{
				this.data[opt_key] = "";
			}
		}
	}

	this.el = document.getElementById ( this.id );

	this.settings_container = null;
	
	for ( i = 0; i < this.el.childNodes.length; i++ ){
		if ( /menu-item-settings/.test( this.el.childNodes[i].className ) ){
			this.settings_container = this.el.childNodes[i];
			break;
		}		
	}

	match = /menu-item-(\d+)/.exec( this.id );
	if ( match == null ){
		return false;
	}
	this.item_id_ind = match[1];

	this.opts_html = {}
	this.render_opts_html = cws_megamenu_render_opts_html;
	this.render_opt_html = cws_megamenu_render_opt_html;
	this.insert_opts_html = cws_megamenu_insert_opts_html;
	this.render_opts_html ();
	this.insert_opts_html ();
}

function cws_megamenu_render_opts_html ( ){
	var that = this;
	var opt_key;
	for ( opt_key in that.opts ){
		that.render_opt_html ( opt_key );
	}
}

function cws_megamenu_render_opt_html ( opt_key ){
	if ( opt_key == undefined ) return false;
	var that = this;
	var output = "";
	var opt = that.opts[opt_key];
	var opt_val = that.data[opt_key];


	var opt_id = "edit-menu-item-" + opt_key + "-" + that.item_id_ind;
	var opt_name = "menu-item-" + opt_key + "[" + that.item_id_ind + "]";
	var node_el, node_el_className, label, text, input, input_className, opt_classname;
	node_el_className = "field-" + opt_key + " description description-wide";
	input_className = "widefat edit-menu-item-" + opt_key;
	node_el = document.createElement( "p" );
	node_el.className = node_el_className;
	input = document.createElement( "input" );
	input.type = opt.type;
	input.id = opt_id;
	input.className = input_className;
	input.name = opt_name;

	input.value = opt_val;

	if ( opt.title != undefined && opt.title.length ){
		label = document.createElement( "label" );
		label.htmlFor = opt_id;
		text = document.createTextNode( opt.title );
		label.appendChild( text );
		label.appendChild( document.createElement( "br" ) );
		label.appendChild( input );
		node_el.appendChild( label );
	}
	else{
		node_el.appendChild( input );
	}
	that.opts_html[opt_key] = node_el;	
}

function cws_megamenu_insert_opts_html (){
	var that = this;
	var i, opt_key;
	for ( i = 0; i < that.settings_container.childNodes.length; i++ ){
		if ( /field-move/.test( that.settings_container.childNodes[i].className ) ){
			utmost = that.settings_container.childNodes[i];
			break;
		}		
	}
	for ( opt_key in that.opts_html ){
		that.settings_container.insertBefore( that.opts_html[opt_key], utmost );
	}
}
