(function($) {
	/* icon */
tinymce.PluginManager.add( 'cwssvgi_sc', function ( ed, url ){
	ed.addButton( 'cwssvgi_icon', {
		title: 'CWS SVG Icons',
		text: 'Insert SVG',
		icon: false,
		onclick: function() { onClick('tmce', null) },
	});
});

function onClick(src, parent) {
	if (!cwssvgi_mf) {
		cwssvgi_mf = new wp.media.view.MediaFrame.svgi({
			title : 'Insert SVG Icons and Animations',
			multiple : false,
			filters : true,
			search: true,
			source: src,
			parent: parent, // parent node, needed for VC dialog
			frame : 'manage',
			library : {type: 'cwssvgi-icons'},
			button : { text : 'Insert SVG Icon' }
		});
	} else {
		cwssvgi_mf.options.parent = parent;
		cwssvgi_mf.options.src = src;
	}
	cwssvgi_mf.open();
}

$(document).ready(function() {
	$("body").unbind("click.vcSvgGalleryWidget").on("click.vcSvgGalleryWidget", ".cws_svg_icon", function(e) {
		e.preventDefault();
		var parent = e.target.parentNode;
		onClick('vc', parent);
	});
	$("body").unbind("click.vcSvgGalleryWidgetR").on("click.vcSvgGalleryWidgetR", ".cws_svg_icon_remove", function(e) {
		e.preventDefault();
		var parent = jQuery(e.target.parentNode);
		parent.find('a.cws_svg_icon').show();
		parent.find('a.cws_svg_icon_remove').hide();
		parent.find('input').val('');
		parent.find('i.svg').remove();
	});
});

	var cwssvgi_mf = null;
	var l10n = wp.media.view.l10n;

	wp.media.view.cwsRouter = wp.media.view.Menu.extend({
		tagName:   'div',
		className: 'media-router',
		property:  'contentMode',
		ItemView:  wp.media.view.RouterItem,
		region:    'router',

		initialize: function() {
			this.controller.on( 'content:render', this.update, this );
			// Call 'initialize' directly on the parent class.
			Menu.prototype.initialize.apply( this, arguments );
		},

		update: function() {
			var mode = this.controller.content.mode();
			if ( mode ) {
				this.select( mode );
			}
		}
	});

	wp.media.view.cws_svgiItem = wp.Backbone.View.extend({
		tagName: 'li',
		role: 'checkbox',
		className: 'cws-item attachment save-ready',

		initialize: function() {
			this.$el.attr('data-name', this.model.get('name'));
		},

		render: function() {
			this.$el.html( '<div class="js--select-attachment"><i class="svg">'+
				this.model.get('content')+'</i></div><button type="button" class="button-link check" tabindex="-1"><span class="media-modal-icon"></span><span class="screen-reader-text">Deselect</span></button>' );
			return this;
		}
	});

	wp.media.view.cwsItem_svgiAnim = wp.Backbone.View.extend({
		tagName: 'li',
		role: 'checkbox',
		className: 'cws-item attachment save-ready',

		initialize: function() {
			this.$el.attr('data-name', this.model.get('name'));
		},

		render: function() {
			this.$el.html( '<div class="js--select-attachment"><i class="svg">'+
				this.model.get('content')+'</i><span class="title">'+this.model.get('name')+'</span></div><button type="button" class="button-link check" tabindex="-1"><span class="media-modal-icon"></span><span class="screen-reader-text">Deselect</span></button>' );
			return this;
		}
	});

		wp.media.view.Toolbar.cwssvgi = wp.media.view.Toolbar.extend({
		initialize: function() {
			_.defaults( this.options, {
				//event : 'inserter',
				close : true,
				items : {
					primary    : {
						id       : 'cwssvgi-button',
						style    : 'primary',
						text     : this.controller.options.button.text,
						priority : 80,
						click    : function(e) {
							var selection = this.controller.state().get('sel');
							var svg_name = selection.models.reduce(function(full, current, index){
								return full + (index === 0 ? '' : ',') + current.id;
							}, '');
							if (svg_name.length > 0) {
								// get selected model
								var model = this.controller.state().frame.content.get().collection.where({name: svg_name});
								if (1 === model.length) {
									var svg_height = this.controller.state().frame.$el.find('#cwssvgi-h').val();
									var svg_width = this.controller.state().frame.$el.find('#cwssvgi-w').val();
									switch (model[0].attributes.mode) {
										case 'normal':
											var svgi_collection = this.controller.state().frame.$el.find('#cwssvgi-collection').val();
											var sc = '[cwssvg width='+svg_width+' height='+svg_height+' collection="' + svgi_collection + '" name="' + svg_name + '" /]';
											break;
										case 'animations':
											var sc = '[cwssvgi title="' + svg_name + '" height="' + svg_height + '" width="' + svg_width + '"/]'
											break;
									}
									var source = this.controller.options.source;
									switch (source) {
										case 'tmce':
											tinyMCE.activeEditor.selection.setContent(sc, { format: 'html' });
											break;
										case 'vc':
											var parent = jQuery(this.controller.options.parent);
											var svg = {'width':svg_width, 'height':svg_height, 'collection':svgi_collection,'name':svg_name };
											parent.find('input').val(JSON.stringify(svg));
											parent.find('a.cws_svg_icon').hide();
											parent.find('a.cws_svg_icon_remove').show();
											var svg_content = model[0].get('content');
											var svg_name = svg_content.match('class="(.+?\.svg)"')[1];
											var styles = 'style="width:'+svg_width+'px;height:'+svg_height+'px"';
											svg_content = svg_content.replace('class="'+svg_name+'" ', 'class="'+svg_name+'" name="' + svg_name + '" data-mce-name="' + svg_name + '" ');
											svg_content = svg_content.replace('</svg>', '&nbsp;</svg>');
											parent.find('i.svg').remove();
											parent.append('<i class="svg" '+ styles+'>'+svg_content+'</i>');
											break;
									}
								}
								this.controller.state().frame.content.get().clearSelection(e);
							}
							cwssvgi_mf.close();
						}
					}
				}
			});
			wp.media.view.Toolbar.prototype.initialize.apply( this, arguments );
		},

		refresh: function() {
			var selection = this.controller.state().get('sel');
			// @TODO i think this is redundant
			this.get( 'primary' ).model.set( 'disabled', !selection.length );
			wp.media.view.Toolbar.prototype.refresh.apply( this, arguments );
		}

	});

		wp.media.view.cwssvgi = wp.media.View.extend({
		events: {
			'click .cws-item' 					: 'toggleSelection',
			'click .cws-item.selected'	: 'removeSelection',
			'click .button-link.check'	: 'removeSelection',
			'change #cwssvgi-collection': 'UpdateCollection',
		},

		initialize: function() {
				if (undefined === window.cwssvgi) {
					return;
				}
			var _this = this;
			//this.createToolbar();
			//this.clearItems();

			this.model = Backbone.Model.extend({
				name: '',
			});

			var svgi_collection = Backbone.Collection.extend({
				model: this.model,
			});

			this.collection = new svgi_collection;

			//this.createToolbar();
			switch (this.options.mode) {
				case 'normal':
					this.renderIcons();
					this.UpdateCollection();
					break;
				case 'animations':
					this.renderIconsAnim();
					this.UpdateCollectionAnim();
					break;
			}

		},

		createToolbar: function( toolbar ) {
				toolbar.view = new wp.media.view.Toolbar.cwssvgi({
				controller: this
			});
		},

		toggleSelection: function(e) {
			var _name = e.currentTarget.dataset['name'];
			if (!this.options.controller.options.multiple) {
				this.clearSelection(e);
			}
			if ( this.getSelection().get( _name ) ) {
				this.removeFromSelection( e.currentTarget, {id: _name} );
			} else {
				this.addToSelection( e.currentTarget, {id: _name} );
			}
			this.views.parent.toolbar.get().refresh();
		},

		removeSelection: function(e) {
			var _name = e.currentTarget.dataset['name'];
			this.removeFromSelection( e.currentTarget, {id: _name} );
		},

		addToSelection: function( target, id ) {
			target.className += ' selected';
			this.getSelection().add( id );
		},

		removeFromSelection: function( target, id ) {
			$(target).removeClass('selected');
			this.getSelection().remove( id );
		},

		clearSelection: function(event) {
			var ul_parent = $(event.target).closest('.media-frame').find(this.tagName);
			var selection = this.getSelection().models.map(function(model){return model.get('id')});
			selection.forEach(function(_name){
				this.removeFromSelection( $(ul_parent).find('.cws-item[data-name="'+_name+'"]'), {id: _name} );
			}, this);
			this.getSelection().reset();
		},

		getSelection : function() {
			return this.controller.state().get('sel');
		},

		UpdateCollection: function() {
			// need to use ajax here to get new collection
			this.collection.reset(); // clean up any collections

			var svg_collections = this.$el.find('#cwssvgi-collection')[0];
			var svg_col = svg_collections.options[svg_collections.selectedIndex].value;
			var that = this;
			jQuery.ajax({
				type: 'post',
				async: true,
				dataType: 'text',
				url: ajaxurl,
				data: {
					action: 'cwssvgi_ajax_get_collection',
					nonce: window.cwssvgi_nonce,
					collection: svg_col,
				},
				error: function(resp) {},
				success: function(resp) {
					that.$el.find('li').remove();
					var data = jQuery(resp);
					var container = document.createDocumentFragment();
					data.find('svg').each(function(){
						that.collection.add( [ {name: this.className.baseVal, mode: 'normal', content: this.outerHTML} ] );
					});
					that.collection.each( function( model ) {
						container.appendChild( that.renderIcon( model ) );
					}, that );
					that.$el.append( container );
				}
			});
		},

		UpdateCollectionAnim: function() {
			// need to use ajax here to get new collection
			this.collection.reset(); // clean up any collections
			var that = this;
			jQuery.ajax({
				type: 'post',
				async: true,
				dataType: 'text',
				url: ajaxurl,
				data: {
					action: 'cwssvgi_ajax_get_animations',
					nonce: window.cwssvgi_nonce,
				},
				error: function(resp) {},
				success: function(resp) {
					that.$el.find('li').remove();
					var data = jQuery(resp);
					data.find('svg').each(function(){
						// <i data-title=""><svg></svg></i>
						that.collection.add( [ {name: this.parentNode.dataset['title'], mode: 'animations', content: this.outerHTML} ] );
					});
					var container = document.createDocumentFragment();
					that.collection.each( function( model ) {
						container.appendChild( that.renderIconAnim( model ) );
					}, that );
					that.$el.append( container );
				}
			});
		},

		renderIconsAnim: function() {
			//var svg_cols = wp.template('cwssvgi-icons')().split(',');
			//var options = '';

			this.$el.append( '<div class="search">'+
				'<span><label for="cwssvgia-h">Height</label><input name="cwssvgia-h" id="cwssvgia-h" value="120"></span>'+
				'<span><label for="cwssvgia-w">Width</label><input name="cwssvgia-w" id="cwssvgia-w" value="120"></span>'+
				'</div>' );

			if ( this.collection && this.collection.models.length ) {
				var container = document.createDocumentFragment();
				this.collection.each( function( model ) {
					container.appendChild( this.renderIconAnim( model ) );
				}, this );
				this.$el.append( container );
			}
			return this;
		},

		renderIcons: function() {
				var options = '';
				if (undefined !== window.cwssvgi) {
			var svg_cols = window.cwssvgi.collections;
			for (col in svg_cols){
				options += '<option value="'+col+'">' + col + '</option>';
			}
				}
			this.$el.append( '<div class="search"><span><select id="cwssvgi-collection">'+options+'</select></span>'+
				'<span><label for="cwssvgi-h">Height</label><input name="cwssvgi-h" id="cwssvgi-h" value="64"></span>'+
				'<span><label for="cwssvgi-w">Width</label><input name="cwssvgi-w" id="cwssvgi-w" value="64"></span>'+
				'</div>' );

			if ( this.collection && this.collection.models.length ) {
				var container = document.createDocumentFragment();
				this.collection.each( function( model ) {
					container.appendChild( this.renderIcon( model ) );
				}, this );
				this.$el.append( container );
			}
			return this;
		},

		updateIcon: function(filter, model) {
			var _name = model.get('name');
			if (-1 !== _name.indexOf(filter)) {
				this.$el.find('li[data-name="'+_name+'"]').show();
			} else {
				this.$el.find('li[data-name="'+_name+'"]').hide();
			}
		},

		renderIcon: function(model) {
			var view = new wp.media.view.cws_svgiItem({
				model : model,
			});
			return view.render().el;
		},

		renderIconAnim: function(model) {
			var view = new wp.media.view.cwsItem_svgiAnim ({
				model : model,
			});
			return view.render().el;
		},
	});

		wp.media.view.MediaFrame.svgi = wp.media.view.MediaFrame.extend({
		initialize: function() {
			wp.media.view.MediaFrame.prototype.initialize.apply( this, arguments );

			_.defaults( this.options, {
				selection: [],
				library:   {},
				multiple:  false,
				state:    'library',
				uploader: false,
			});

			this.states.add([
				new wp.media.controller.Library({
					library:   wp.media.query( this.options.library ),
					multiple:  this.options.multiple,
					title:     this.options.title,
					sel: 	new Backbone.Collection(),
					ifilter: '',
					priority:  20
				})
			]);

			this.on( 'router:create:browse', this.createRouter, this );
			this.on( 'router:render:browse', this.browseRouter, this );

			this.on( 'content:render:animation', this.onFrameAnimation, this );

			this.on( 'content:create:normal', this.onFrameNormal, this);
			this.on( 'open', this.onOpen, this );
			//this.on( 'toolbar:create:select', this.createSelectToolbar, this );
			this.on( 'toolbar:create', this.createToolbar, this );
			this.on( 'selection:toggle', this.onSelect, this );
		},

		browseRouter: function( routerView ) {
			routerView.set({
				animation: {
					text: 'Animation',
					priority: 20
				},
				normal: {
					text: 'Normal',
					priority: 40
				}
			});
		},

		createToolbar: function(toolbar) {
				toolbar.view = new wp.media.view.Toolbar.cwssvgi( {
				controller : this,
				props: new Backbone.Model({id:'props'}),
			} );
		},

		onFrameNormal: function(service){
				this.content.set( new wp.media.view.cwssvgi( {
				service    : service,
				controller : this,
				mode : 'normal',
				tagName:   'ul',
				className  : 'clearfix attachments'
			} ) );
		},

		onFrameAnimation: function(service){
				this.content.set( new wp.media.view.cwssvgi( {
				service    : service,
				controller : this,
				mode : 'animations',
				tagName:   'ul',
				className  : 'clearfix attachments animations'
			} ) );
		},

		onOpen: function(contentRegion){
			this.$el.find( '.media-frame-content' ).attr( 'data-columns', '12' );
		},

		createSelectToolbar: function( toolbar, options ) {
			options = options || this.options.button || {};
			options.controller = this;

			toolbar.view = new wp.media.view.Toolbar.Select( options );
		}
	});

}(jQuery));
