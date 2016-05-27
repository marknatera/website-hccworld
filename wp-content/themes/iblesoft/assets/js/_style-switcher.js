jQuery(function($) {
			  
	"use strict";			  

	var $styleSwitcher = $('.style_switcher');
	var $head 		   = $('head');
	var $logoImg 	   = $(".logo img");
				  
	$('.gear').click(function(){ 
		$styleSwitcher.toggleClass("gear-unfolded") 
	});

	$(".style_switcher .styles ul.color_variations").on( "click", "li", function(){

		var styleColor = this.className.replace("style-","");
		$head.find("link[href^='./assets/css/style-']").remove();
		if ( styleColor !== "default" ){
			$head.append('<link rel="stylesheet" href="./assets/css/style-' + styleColor + '.css" type="text/css" />');
		}
		$logoImg.attr("src", "./assets/images/medicus-header-logo-x64.png");

	});

	//========================== DYNAMIC STYLE SWITCHER ================================================

	$(".main section.instant").each(function( index, section ){

		var $styleSwitcherPanel = $("#style-switcher-template");
		var $section = $(section);
			$section.prepend( $styleSwitcherPanel.html() );
		// ENABLE LIVE CONTENT EDITING ON SECTION .container (ALTERNATIVE: document.body.designMode = "on")
		// $section.find(".container").attr("contentEditable", true);

	});

	initSectionStyleSwitcher($);

	/*** INITIALIZE DRAG-AND-DROP IMAGE CHANGE ON TEASER BOXES ***/
	(function(){

		// DISABLE DEFAULT WINDOW DRAG AND DROP FUNCTIONALITY
		window.ondragover   = function(e) { e.preventDefault(); return false };
		window.ondrop       = function(e) { e.preventDefault(); return false };

		function changeImageURL(url, filename){

			var anchor = document.createElement("a");
				anchor.href = url;	// .protocol, .hostname, .port, .pathname, .search, .hash, .host

			url = anchor.pathname;
			url = url.split("/");
			url.pop();
			url = url.join("/") + "/" + filename;

			return url;

		}

		function handleFileSelect(evt) {

			evt.stopPropagation();
			evt.preventDefault();

			var files = evt.dataTransfer.files; 
			var url;

			// HAS BACKGROUND IMAGE ALREADY CHANGED ONCE?
			if( evt.target.getAttribute("data-backgroundimage") !== null ){

				url = evt.target.getAttribute("data-backgroundimage");
				url = changeImageURL(url, files[0].name);

			} else {

				url = evt.target.style.backgroundImage.match(/url\((.*)\)/);
				url = url[1];
				url = changeImageURL( url, files[0].name );

			}

 		    if ( files[0].type.match('image.*') ){

				var reader = new FileReader();
					reader.onload = (function(theFile) {
						return function(e) {
							evt.target.style.backgroundImage = "url(" + e.target.result + ")";	
							evt.target.setAttribute("data-backgroundimage", url);
							handleDragLeave(evt);
						};
					})(files[0]);

				reader.readAsDataURL(files[0]);		// READ IMAGE FILE AS DATA URL

 		    }

		}

		function handleDragLeave(evt){
			evt.target.style.opacity = "1";
		}

		function handleDragOver(evt) {
			evt.target.style.opacity = "0.25";
			evt.stopPropagation();
			evt.preventDefault();
			// evt.dataTransfer.dropEffect = 'copy'; // Explicitly show this is a copy.
		}

		$(".teaser_box a").each(function(){

		  // CACHE JQUERY SELECTOR
		  var $this = $(this);

		  // CHECK IF background-image CSS RULE IS SET
		  if ( $this.css("backgroundImage") !== "none" ){

		    this.addEventListener('dragenter', handleDragOver, false);
		    this.addEventListener('dragleave', handleDragLeave, false);
		    this.addEventListener('drop', handleFileSelect, false);
		    
		  }

		});

	})();

	//END======================= DYNAMIC STYLE SWITCHER ================================================
   
});

//========================== SECTION STYLE SWITCHER ================================================

function initSectionStyleSwitcher($){

	(function($){

		var sectionStyleSwitcher = '.section_style_switcher';

		$('.handler:not(".downloader")').click(function(){ 
			$(this).parent(sectionStyleSwitcher).toggleClass("handler-unfolded"); 
		});

		/*=====----====== DOWNLOADR ======------======*/

		var ENABLE_JADE = true;

		var injectJS = function( jsURL, callback ){

			var script = document.createElement('script');
				script.type = 'text/javascript';
				script.src = jsURL;
			var head = document.getElementsByTagName('head')[0];
				head.appendChild(script);

			setTimeout(function(){
				callback && callback();
			}, 250);
		}

		if ( ENABLE_JADE ){
			injectJS('assets/js/libs/html2jade/he.min.js', function(){
				injectJS('assets/js/libs/html2jade/html2jade.min.js');
			});
		}

		var downloadLink = function( name, content, mimetype ) {

			var a           	= document.createElement('a');
				a.href          = window.URL.createObjectURL(new Blob([content], {type: mimetype}));
				a.download      = name;
				a.textContent   = 'Download';
				a.style.display = "none";

			document.body.appendChild(a);

			$(a).get(0).click();

		}

		$(".handler.downloader").on("click", function(){

			// CACHING jQuery VERSION OF this
			var $this = $(this);
			// CLONE ELEMENT
			$the_section = $this.parent().parent().clone();
			// REMOVE SECTION STYLE SWITCHER
			$the_section.find(".section_style_switcher").remove();
			$the_section.find(".container").removeAttr("contentEditable");


			// REPLACE DATA URI IMAGES WITH URI LINKS
			$the_section.find("a[data-backgroundImage]").each(function(index,el){

				$(el)
				.css("background-image", "url('" + $(el).data("backgroundimage") + "')")
				.removeAttr("data-backgroundimage");

			}); // REPLACE WITH LINK URL

			// WRAP INTO P SO THAT WE CAN GET THE section CONTAINER ALSO
			$the_section = $the_section.wrap('<p/>').parent();
			// DOWNLOAD LINK

			// console.log($the_section.html()); return;
			
			downloadLink('custom_section.html', $the_section.html(), 'text/html');
			// DOWNLOAD JADE VERSION
			if ( ENABLE_JADE ){
				Html2Jade.convertHtml( $the_section.html(), { bodyless: true }, function( err,jade ){
					downloadLink( "custom_section.jade", jade, 'text/plain');
				});
			}


		});

		/*^====----====== DOWNLOADR ======------=====^*/

		// ON CLICK: .color_section
		$('.section_styles a.color_section').on("click", function(){ 

			var noToggleClass = "transparent";

			var $this = $(this);
			var the_class    = $this.text();
			var $the_section = $this.parent().parent().parent().parent();		

				if ( $this.text() !== 'transparent' ){
					$this.parent().find("a.color_section.on:not(:contains('transparent'))").not(this).toggleClass('on');
				}

				$this.toggleClass('on');

				if ( $this.text() === noToggleClass ){
					$the_section.toggleClass(noToggleClass);	
				} else {
					if ( $the_section.hasClass(noToggleClass) ){
						$the_section.attr("class","").addClass(noToggleClass + " " + the_class);
					} else {
						$the_section.attr("class","").addClass(the_class);	
					}
				}

		});

		// ON CLICK: All the rest... to be separated out using classes
		// var $section_styles = $('.section_styles a');
		var $section_styles = $('.section_styles a:not(".color_section")');

		$section_styles.on("click", function(){ 

			var $this = $(this);
			var the_class    = $this.text();
			var $the_section = $this.parent().parent().parent().parent();		
				window.$this = $this;

				var header_height = $('.header').height();
		    	var window_height = $(window).height();
		    	var usable_height = window_height - header_height; 

				$the_section.toggleClass(the_class); 
				$this.toggleClass('on');
				$this.parent().parent().parent().parent().css( "min-height", 0 );
				$this.parent().parent().parent().parent('.full_height').css( "min-height", usable_height );
				$this.parent().parent().parent().parent('.full_height').children('.container').css('padding-top' , 0);
				$this.parent().parent().parent().parent('.vertical_center').children('.container').css('padding-top' , 0);
				$this.parent().parent().parent().parent('.vertical_bottom').children('.container').css('padding-top' , 0);

				var container_height = $this.parent().parent().parent().parent('.full_height.vertical_center').children('.container').height();
				var top_padding = (usable_height - container_height -55) * 0.5;
				if (top_padding > 0) { $this.parent().parent().parent().parent('.full_height.vertical_center').children('.container').css('padding-top' , top_padding); };  
				var top_padding_2 = (usable_height - container_height -55);
	          	if (top_padding_2 > 0) { $this.parent().parent().parent().parent('.full_height.vertical_bottom').children('.container').css('padding-top' , top_padding - header_height + 55); }; 


		});
	
		var section_header_styles = $('.section_header_styles a');

		section_header_styles.on("click", function(){ 

			var the_class_2    = $(this).text();
			var $the_section_2 = $(this).parent().parent().parent().parent().children('.container').children('.row').children('.section_header');		

				$the_section_2.toggleClass(the_class_2); 
				$(this).toggleClass('on');
				

		});

		var $col_styles = $('.column_styles a');
		var $col_select = $('ul.col_select li');
		
		$col_select.on("click", function(){
			$('ul.col_select li.on').removeClass('on');
			$(this).addClass('on');
		});

		$col_styles.on("click", function(){ 

			var the_col = $('ul.col_select li.on').text();
			var the_class_3 = $(this).text();
			var the_section_3 = $(this).parent().parent().parent().parent().children('.container').children('.row').children('div[class *="col-"]:nth-child(' + the_col + ')').not('.section_header');		

				the_section_3.toggleClass(the_class_3); 
				$(this).toggleClass('on');

				//================== SAME COLUMN HEIGHT ==========================================

				 var sameHeightCols = $(".same_height_col");

				 if ( !( window.matchMedia && window.matchMedia( "only screen and (max-width: 480px)" ).matches && sameHeightCols.length > 0 ) ){
				    sameHeightCols.conformity();
				    $(window).on( "resize", function() {  sameHeightCols.conformity();  });
				 } 

				 //END=============== SAME COLUMN HEIGHT ==========================================
				
		});

	}(jQuery));


}

//END======================= SECTION STYLE SWITCHER ================================================
    