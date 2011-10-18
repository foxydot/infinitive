/* 

TabManager Class:
	Version: 0.052
	Author: Marty Stake

Dependencies:
	prototype.js
	
updated toggle() to work with 'contained'
	
*/

if(typeof(TabManager) == 'undefined')
	var TabManager = {};

TabManager = Class.create();

TabManager.controller = { 

		options : { 
			// we can use this to allow the tab sets to talk to each other before they completely "start" -- if you dont do this, then you will get errors that a tab set does not exist when you try to access it in any of the callback functions - you need to use activateAll after you define all of the tab sets to get them running //
			defer: 	false
		},
			
		getTabSet : function(index) {
			return this.instances[index];
		},
			 
		instances: [],
			
		activate: function(tabset) {
			tabset.start[tabset.options.mode].bind(tabset)();		
		},
	
		activateAll: function() {
			this.instances.each(function(set) {
				this.start(set);
			}.bind(this));
		}
		
	},

TabManager.prototype = {
		
		initialize: function(baseId, options) {
		
			this.slideIDs = [];
			this.triggerIDs = [];
			
			TabManager.controller.instances.push(this);
			
			this.options = {
	
				baseName: 'tabs',
							
				triggers: true,
				triggerWrapper: 'tabs-triggers',
				triggerBaseNode: '',
				triggerActivator: 'a',
				parentHasOnClass: true,
				
				buttons: false,
				buttonWrapper: '',
				buttonForward: '',
				buttonBack: '',
				buttonRotate: '',
				buttonStopRotate: '',
				
				slides: '',
				useSlideClass: false,
				slideBaseWrapper: 'div',
				
				firstIndex: 1,
				action: 'click',
				onState: 'on',
				offState: 'off',
				hoverState: 'hover',
				noFollow: false,
				
				autoPlayDelay: 2000,
				effectDuration: .5,
				
				mode: 'firstOn'
			}
			
			// check to see if the base tab wrapper is the first property passed in the constructor.  If not, pass the options property to the element property //
			if (typeof baseId == 'string') 
				options.baseName = baseId;
			else {
				options = baseId;
			}
		
			Object.extend(this.options, options || {});
			
			this.currentSlide = this.options.firstIndex ? this.options.firstIndex - 1 : 0;
		
			// set up the trigger items which change the slides  //
			if(this.options.triggers) {

				// set up the trigger items which change the slides  //
				if (this.options.triggerSelector) { // this is here if we dont want to use a separated trigger wrapper div //
					var triggers = $$(this.options.triggerSelector);
				}
				else {
					var triggers = $(this.options.triggerWrapper).getElementsByTagName(this.options.triggerActivator);
				}

				this.triggers = $A(triggers);
				this.totalSlides = this.setTriggers(this.triggers);
			} 
			
			/* set up the data that switches when the trigger is clicked 
			 if we pass in something thats not an array, make it one.  If we don't
			 pass in the slide wrapper, use the base id */
			
			if (this.options.slides != '') 
				this.slides = (typeof this.options.slides == 'object') ? this.options.slides : [this.options.slides]
			else 
				this.slides = [this.options.baseName]
			
			if(!this.options.remote) {
				this.totalSlides = this.setSlides(this.slides);
			}
			else {
				// set up some AJAX options //
				this.slideContainer = $$(this.options.remote.slideContainer)[0];
			}
			
			if(this.options.buttons) {
				this.setButtons();
			}
		
		
			// call before start //
			if (typeof this.options.beforeStart == 'function')
				this.options.beforeStart.apply(this, arguments);
		
				// start the TabManager on the chosen mode if we are starting right away //
			if (!TabManager.controller.options.defer) {
				this.start[this.options.mode].bind(this)(); 
			}
		
		},
		
/*	Property: start
		Pick a display mode based on a user defined option.
*/
		start: {
			
			'contained' : function() {
				this.currentSlide = -1;
			},
			
			/*	Property: firstOn
					Show the first tab based on the firstIndex option
			*/

			'firstOn' : function() {
				this.showFirst();
			},

			/*	Property: autoPlay
					Automatically switch through the tabs in order until the user triggers a tab.
			*/

			'autoPlay' : function() {
				this.options.autoPlay = true,
				this.showFirst();
				this.setAutoPlay();
			},

			/*	Property: autoPlayOnce
					Automatically switch through the tabs in order once, then stop on the first tab.
			*/			

			'autoPlayOnce' : function() {
				this.autoPlayCounter = 0;
				this.options.autoPlayOnceThru = true,
				this.showFirst();
				this.setAutoPlay();
			}
		
		},
		
/*	Property: setTriggers
		Set up the elements on the page that will control the tab structure - the action on these will cause the "slides" to change.
		
		Arguments:
		els - an array of elements
		
		Returns: 
		the number of tabs
*/		
		
		setTriggers: function(els) { 
			
			for(x=0; x<els.length; x++) {
				
				this.triggerIDs[x] = els[x].id = els[x].id || this.options.baseName + '-tr-' + x;
			
				// hide the initial state that is on for javascriptless browsers
				this.getBase(els[x].id).removeClassName(this.options.onState);				
				
				// attach the action //
				Event.observe(els[x], this.options.action, this.swap.bind(this) );
			
				// call before hiding - passes the tab element //
				if (typeof this.options.beforeSetup == 'function')
						this.options.beforeSetup.apply(this, [els[x]]);
				
				// make sure you dont follow the link if the option is set that way //
				if ( (this.options.action != 'click') && (this.options.noFollow) ) 
						Event.observe(els[x], 'click', function(evt) { Event.stop(evt) } )
			
				els[x].style.outline = 'none';
				els[x].style.cursor = 'pointer';
			
			}
			
			return els.length;
		},
		
/*	Property: setSlides
		Set up the elements on the page that will show and hide based on interaction with the triggers.
		
		Arguments:
		els - an array of ids or a single id
		
		Returns: 
		the number of slides
*/		

		setSlides: function(ids) { 
		
			this.slides.each(function(set, nodeCounter) {
		
				this.slideIDs[nodeCounter] = [];
		
				slideSet = this.options.useSlideClass ? 
				$A( $(set).getElementsByClassName(this.options.useSlideClass, this.options.slideBaseWrapper) ) : 
				$A( $(set).getElementsByTagName(this.options.slideBaseWrapper) )
				
				slideSet.each(function(subEl, counter) {
					
						this.slideIDs[nodeCounter][counter] = $(subEl).id = $(subEl).id || this.options.baseName + '-sl-' + nodeCounter + '-' + counter;
						$(subEl).style.display = 'none'; // hide em all and set up the initial "on" so it wont goof up the page if JS is off //
						
				}.bind(this) );
				
				
			
			}.bind(this) );
			
			
			
			return slideSet.length;

		},

/*	Property: getIndex
		Get the index of an element based on a node.  If no node, use the main trigger wrapper
		
		Arguments:
		node - the base node holding the element
		el - the element we want to find the index of
		
		Returns: 
		the index
*/			
		
		getIndex: function(node, el) { // takes an element //
					
			if (el == null) {
				el = node;
				node = this.triggers;
			}
				
			// do this in case we need to use spans or ems or whatever and we are not using a whole triggerActivator to show the slide //	
			while (node.indexOf(el) < 0) {
				el = el.parentNode;
			}
			
			return node.indexOf(el);
		},
		
/*	Property: getBase
		Get the wrapper element for a specific id.  Based on user options for parentHasOnClass and triggerBaseNode.
		
		Options:
		- if parentHasOnClass is true, the immediate parent element is returned
		- if there is a triggerBaseNode return that element
		- if neither is true, return the original element
		
		Arguments:
		id - the id of the trigger
		
		Returns: 
		the element which we will apply a class
*/			
		
		getBase: function(id) { 
		
			// the getBase returns the extended node which carries the on class for the trigger //
			
			// if parentHasOnClass is true, the immediate parent is returned //
			if (this.options.parentHasOnClass) {
				return $( $(id).parentNode );
			}
			
			else {
				
				// otherwise, get the node that will hold the on class //
				if (this.options.triggerBaseNode) {
					var t = $(id).tagName
					var parent = $(id);
					
					while( t != this.options.triggerBaseNode.toUpperCase()) {
						parent = parent.parentNode
						t = parent.tagName
					}
					return $(parent);
				}
				
				// otherwise, the trigger carries the on class //
				else {
					return $(id);
				}
			}
			
		},
		
		getCurrentTrigger: function() {
			return this.triggers[this.currentSlide];
		},
		
		
/*	Property: swap
		Switch the slides based on the trigger acted upon and stop any automatic looping
		
		Arguments:
		e - event / which is turned into an index 	
*/
		
		swap: function(evt) { 
			
			// if we are showing an effect just cut out so we cant click a button a million times and mess up the effects //
			if (this.showing || this.hiding || (this.remoteLoading && this.options.remote.useTrigger) ) {
				if(evt) Event.stop(evt);
				
				if ( (typeof evt == 'number') || (typeof evt == 'string') )  {
					return;
				} // from flash or getURL or if you use inline js calls.  whoa. //
				
				return false;
			}
			
			switch(typeof evt) {
				case 'number':
					var next = evt;
					break
				case 'string':
					var next = this.getIndex(this.slideIDs[0], evt);
					break
				default:
					var next = this.getIndex(this.triggers, Event.element(evt));
					break
			}
			
			var current = this.currentSlide;
			
			if (current != next) {
				this.currentSlide = next;
				this.toggle(current, next);
			}
			
			// dont go to the linked page and stop default action //
			if(evt) Event.stop(evt);
			// if we are rotating, override it and shut it off //
			this.stopAutoPlay();	
			
			
		},
		
		
/*	Property: smartSwap
		Switch the slides based on automatic looping or button presses
		
		Arguments:
		e - event / which is turned into an index 	
		direction - whether to move forward or backward in the slide set.  Can be 'forward' or 'backward'.  If not provided default to 'forward'
		
*/
		
		smartSwap: function(evt, direction) {
			
			// we will take the total number of els and auto go through them.
	
			// if we are showing an effect just cut out so we cant click a button a million times and mess up the effects //
			if (this.showing || this.hiding || (this.remoteLoading && this.options.remote.useTrigger)) {
				if(evt) Event.stop(evt);
				
				if ( (typeof evt == 'number') || (typeof evt == 'string') )  {
					return;
				} // from flash or getURL whoa. //
				
				return false;
			}
	
			var current = this.currentSlide;
		
			switch (direction) {
				case 'forward' :
					next = (current >= this.totalSlides-1) ? 0 : current + 1;
					this.stopAutoPlay();
					break;
				case 'backward' :
					next = (current == 0) ? this.totalSlides-1 : current - 1;
					this.stopAutoPlay();
					break;
				default:
					next = (current >= this.totalSlides-1) ? 0 : current + 1;
					break;
			}
				
			// set to the next slide so we know where we are //
			this.currentSlide = next;	
				
			this.toggle(current, next);
	
			// if we have the autoPlayFlag on, cycle through the elements only once //
			if (this.options.mode == 'autoPlayOnce' ) {
				if (this.autoPlayCounter < this.totalSlides - 1) 
					this.autoPlayCounter++;
				else { 
					this.stopAutoPlay();	 
				}		
			}
				
			// dont go to the linked page if onclick //
			if(evt) Event.stop(evt);
		
		},
		
	
		toggle : function(current, next) { // takes indexes for the next and current slides //
			
			this.show(next);
			if (current >= 0 ) this.hide(current);
			
		},
		
		show : function(evt, index) { // takes an index //
			
			index = evt;
			
			// call before showing //
			if (typeof this.options.beforeShow == 'function')
				this.options.beforeShow.apply(this, arguments);
					
			
			if (this.options.triggers) { 

				/* look to see if we passed in an ID (to turn on the tab with the bookmark in the URL). */
				if (typeof index == 'string') {
					index = this.getIndex(index, this.triggerIDs);
				}
				
				parentNode = this.getBase(this.triggers[index].id)
				parentNode.addClassName(this.options.onState);
			}
			
			if (!this.options.remote) {
			
				this.slides.each( function(set, counter)  {
						// turn on vertical constraining for the wrapper -- you must have a height on the base wrapper (in the CSS) for this to work //
						if (this.options.verticalConstrain) this.verticalConstrain(index, set);
						slideId = this.slideIDs[counter][index];
						this.change( $(slideId) );
				}.bind(this) )

			}
			
			else {
				// call getData with the index number and the either an html file or the rel attribute which points to a file.  TODO: loop backwards through the nodes to find the "a" with the rel.  We would do this if the trigger is a <span> or something like that. //
				var data = parentNode.getAttribute('rel') || this.triggers[index].getAttribute('rel') ||  this.triggers[index].parentNode.getAttribute('rel') || this.options.remote.url;
				this.getData(index, data);
			}
	
			// call after showing //
			if (typeof this.options.afterShow == 'function')
				this.options.afterShow.apply(this, arguments);
	
		},
		
		hide : function(evt, index) { // takes an index //
		
			index = evt;
		
			// call before hiding //
			if (typeof this.options.beforeHide == 'function')
				this.options.beforeHide.apply(this, arguments);
			
			if (this.options.triggers) { 
				parentNode = this.getBase(this.triggers[index].id)
				$(parentNode).removeClassName(this.options.onState);
			}
			
			if (!this.options.remote) {
			
				this.slides.each( function(set, counter)  {
						slideId = this.slideIDs[counter][index];
						this.change( $(slideId) );
				}.bind(this) )
				
			}

			// call after showing //
			if (typeof this.options.afterHide == 'function')
				this.options.afterHide.apply(this, arguments);
			
		},
		
		change: function(el) { // takes an element

			if (!el) return; // if we start with all off, then dont change //

			// if no effects just toggle on and off //
			if (!this.options.effects) {
				el.style.display = ( (el.style.display) == 'block' ) ? 'none' : 'block';
				return true;
			}
			
			else {
			
				// show //
				if (el.style.display == 'none') {
					
					if (this.options.effects.fade) {
					
						this.showing = new Effect.Appear(el, 
							{ duration: this.options.effectDuration, 
							  to: .99999,
							  afterFinish: function() { this.showing = null }.bind(this) } );
					}
					
					if (this.options.effects.slide) {
					
						this.showing = new Effect.BlindDown(el, 
							{ duration: this.options.effectDuration, 
							  afterFinish: function() { this.showing = null }.bind(this) } );
					}
					
						  
					// use the provided show animation function //
					if (this.options.showEffect) {
						this.options.showEffect.apply(this, arguments)
					}		  
							  
					return true;
				}
					
				
				// hide //
				if (el.style.display == 'block') {
					
					if (this.options.effects.fade) {
					
						this.hiding = new Effect.Fade(el, 
							{ duration: this.options.effectDuration, 
							  to: 0,
							  afterFinish: function() { this.hiding = null }.bind(this) } );
					}
					
					if (this.options.effects.slide) {
					
						this.hiding = new Effect.BlindUp(el, 
							{ duration: this.options.effectDuration, 
							  afterFinish: function() { this.hiding = null }.bind(this) } );
					}
					
					// use the provided hide animation function //
					if (this.options.hideEffect) {
						this.options.hideEffect.apply(this, arguments)
					}
					
					return true;
									
				}
			}
		
			
		},
		
		setAutoPlay: function(evt) {
			
			if (!this._TabsPlayer) {
				this._TabsPlayer = setInterval(this.smartSwap.bind(this), this.options.autoPlayDelay);
			}
			
			if(evt) { 
				Event.stop(evt);
				this.options.autoPlayOnceThru = false;
			}
		
		},
		
		stopAutoPlay: function(evt) {
			
			clearInterval(this._TabsPlayer);
			this._TabsPlayer = null;
			this.options.autoPlayOnceThru = false;
			if(evt) Event.stop(evt);
		
		},
		
		
		showFirst : function() {
		
			// if we link to a bookmark inside of a hidden tab, show it! //
			var bookmark = window.location.href.split('#');
			if (bookmark[1]) {
			
				var len = this.options.remote ? this.triggerIDs.length : this.slideIDs[0].length
			
				for(var x = 0; x<len; x++) {
					if ($(bookmark[1])) {
						// if the id is inside of a hidden slide or the an actual tab itself //
						// also check the base trigger for the ID if not on the trigger itself //
						if ($(bookmark[1]).descendantOf(this.slideIDs[0][x]) || 
							(bookmark[1] == this.triggerIDs[x]) ||
							(bookmark[1] == this.getBase(this.triggerIDs[x]).id ) ) {
								this.currentSlide = x;
								this.show(x);
								return true;				
						}
					}
				}
			
			}
			
			// no bookmark.  Use the options setting to turn on the first slide //
			this.show(this.currentSlide);
		}

}

TabManager.Controls = {

		controlForward : {},
		controlBackward : {},
		controlRotate : {},
		controlPause : {},
	
	
		setButtons: function() {
		
			if (!this.options.buttonWrapper) {
				this.options.buttonWrapper = document;
			}
	
		
			if(this.options.buttonForward) {
				var buttonForward = $(this.options.buttonWrapper).getElementsByClassName(this.options.buttonForward);
				Event.observe( $(buttonForward[0]), 'click', this.smartSwap.bindAsEventListener(this, 'forward') );
			}
			
			if(this.options.buttonBack) {
				var buttonBack = $(this.options.buttonWrapper).getElementsByClassName(this.options.buttonBack);
				Event.observe( $(buttonBack[0]), 'click', this.smartSwap.bindAsEventListener(this, 'backward') );
			}
			
			if(this.options.buttonRotate) {
				var buttonRotate = $(this.options.buttonWrapper).getElementsByClassName(this.options.buttonRotate);
				Event.observe( $(buttonRotate[0]), 'click', this.setAutoPlay.bindAsEventListener(this) );
			}
			
			if(this.options.buttonStopRotate) {
				var buttonStopRotate = $(this.options.buttonWrapper).getElementsByClassName(this.options.buttonStopRotate);
				Event.observe( $(buttonStopRotate[0]), 'click', this.stopAutoPlay.bindAsEventListener(this) );
				
			}
			
		}
}

TabManager.Placement = {
	
		verticalConstrain: function(index, set) {
			// hidden slides need to be positioned absolutely, and the wrapper relatively (or absolutely) - height is based on the wrapper holding the slides, not the main base wrapper, unless you uncomment the next line //
			
			//set = this.options.baseName;
			var slideOffset = this.options.verticalOffset || 0;
			
			var baseHeight = $(set).getHeight();
			
			var totalBasePadding = parseInt($(set).getStyle('padding-top')) + parseInt($(set).getStyle('padding-bottom'));
			var totalBaseBordersTop = parseInt($(set).getStyle('border-top-width')) || 0
			var totalBaseBordersBottom = parseInt($(set).getStyle('border-bottom-width')) || 0
		
			var totalBaseBorders = totalBaseBordersTop + totalBaseBordersBottom;
		
			baseHeight = baseHeight - totalBasePadding - totalBaseBorders;
			
			var parentNode = this.getBase(this.triggers[index].id)
			var triggerTop = parentNode.offsetTop;
		
			var slideId = this.slideIDs[0][index];
			var slideHeight = $(slideId).getHeight();
			
			var slideBasePadding = parseInt($(slideId).getStyle('padding-top')) + parseInt($(slideId).getStyle('padding-bottom'));
			var slideBordersTop = parseInt($(slideId).getStyle('border-top-width')) || 0
			var slideBordersBottom = parseInt($(slideId).getStyle('border-bottom-width')) || 0
			
			var slideBorders = slideBordersTop + slideBordersBottom;
			
			// include the borders and padding in the slide //
			slideHeight = slideHeight + slideBasePadding + slideBordersTop + slideBordersBottom;
		
			var useScroll = true;
			if (useScroll) {
				if (slideHeight > baseHeight) {
					$(slideId).style.height = baseHeight - slideBasePadding - slideBorders + 'px';
					$(slideId).style.overflow = 'auto';
					
				}
			}
			
			var useOffset = false;
			
			
			
			// if the height of the slide is less than the total height of the container, orient to the top. Else, pop to the bottom of the container //
			var verticalOrientation = ((triggerTop + slideHeight) < (baseHeight + slideOffset) ) ? 'top' : 'bottom';
		
			switch(verticalOrientation) {
				case 'top' :
					useOffset = true
				break
							
				case 'bottom': 
					useOffset = false //set this true to use an offset on the bottom ones //
				break
			}
			
			
			if (useOffset) {
				if (verticalOrientation == 'top') {
					var verticalPosition = ((triggerTop - slideOffset) > 0) ? triggerTop - slideOffset : -slideOffset;
				}
				else {
					// this is wrong //
					var verticalPosition = 0 + slideOffset;
				}
			}
			else {
				var verticalPosition = 0 - slideOffset;
			}
			
			if (verticalOrientation == 'top') {
				$(slideId).style.top = verticalPosition + 'px';
				$(slideId).style.bottom = 'auto';
			}
			else {
				$(slideId).style.top = 'auto';
				$(slideId).style.bottom = verticalPosition + 'px';
			}
				
		
	}
}


// extend the AJAX functions //
TabManager.AJAX = { 

			/*todo*/ setRemoteStructure: function(el, structure) {
				el = $(el);
				
				// if there is already something in the HTML to hold the slide, put the slide there //
				if (el) {
					return el;
				}
				else {
					// build the wrapper to hold the slide or the slide itself //
				}
			},
			
			indicateLoad : function(state) {
				
				
				if (state == 'load') {
					// show spinner //
					var loading = '<p class=\"loading\">';
					loading += this.options.remote.spinner ? '<img src=\"' + this.options.remote.spinner + '\ " />' : 'Loading...'
					loading += '</p>';
					
					if(this.options.remote.useTrigger)  {
						this.triggerContents = this.getCurrentTrigger().innerHTML;
						this.getCurrentTrigger().innerHTML = loading;
					}
					else {
						$(this.slideContainer).innerHTML = loading;
					}
					
				}
				
				else {
					//hide spinner//
					if(this.options.remote.useTrigger) {
						this.getCurrentTrigger().innerHTML = this.triggerContents;
					}
					
					
				}

			},
			
			getData: function(index, url, options) {
					
				this.remoteLoading = true;	
				var cacheNum = this.options.remote.cache ? 0 : new Date().getTime();
				
				if (this.options.remote.indicateLoad) this.indicateLoad('load');
				
				var req = new Ajax.Request(url, 
					  {
					  	method: 'get',
						parameters: { slide: index,
									  ms: cacheNum 
									},
						onSuccess: this.update.bind(this),
						onComplete: function() {
													if (typeof this.options.afterRemote == 'function')
														this.options.afterRemote.apply(this);
												
												}.bind(this),
						onFailure: function() { alert('I in ur page messing up ur ajax'); }
					  });
	
			},
			
			update: function(transport) { 
			
					window.setTimeout(function() {
						$(this.slideContainer).update(transport.responseText); 
						
						if (typeof this.options.afterUpdate == 'function')
								this.options.afterUpdate.apply(this);
					
						if (this.options.remote.indicateLoad) {
							this.indicateLoad('done');
						}
					
						this.remoteLoading = false;
						
					}.bind(this), 
					
					this.options.remote.delay || 0);
					
							
			}
			

};
	
// add the add-on methods to the main tab prototype //
Object.extend(TabManager.prototype, TabManager.Controls)
Object.extend(TabManager.prototype, TabManager.Placement)
Object.extend(TabManager.prototype, TabManager.AJAX)
	

// overwrite prototype's show method to specifically add block -- necessary if we want to use Effect.Appear and hide the background with CSS display: none; //

var newShow = { show : function(element) {
	$(element).style.display = 'block';
	return element;
  	}
  }
// 
Element.addMethods(newShow);
