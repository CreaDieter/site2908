$(document).ready(function() {
	$(".mobiletoggle").click(function(){
		$("#menu ul").stop(true,true).slideToggle("fast");
	});

	$(".languagetoggle").click(function(){
		$("#languagemobile").stop(true,true).slideToggle("fast");
	});

	$(".openaanbod").click(function(){
		if(!$("#submenu > ul").is(":visible")) {
			$(this).addClass('open');
		} else {
			$(this).removeClass('open');
		}
		$("#submenu > ul").stop(true,true).slideToggle("fast");
	});

	$('#slideshow').cycle({
	    speed: 1000,
	    slides: '> div',
	    prev: '#prevslide',
	    next: '#nextslide'
	});

	$('#submenu > ul > li ul').click(function (e) {
		e.stopPropagation();
	}).hide();

	$('.enlarge').nm();

	$('#submenu > ul > li').each(function(){
		if($(this).hasClass('open')){
			$(this).find('ul').show();
		}
	});

	$('#submenu > ul > li').click(function () {
		$(this).addClass('open').siblings().removeClass('open');
		var selfClick = $(this).find('ul:first').is(':visible');
		if (selfClick) {
			return;
		}
		$(this).parent().find('> li ul:visible').slideToggle();
		$(this).find('ul:first').stop(true, true).slideToggle('slow', function() {

		});
		if ($(this).children('ul').length > 0) {
			return false;
		}
	});

	$(".categoryBlock").hover(function() {
		if ($(this).children('ul').length > 0) {
			alert("ola");
		}
	})

	var slideshows = $('.cycle-slideshow').on('cycle-pager-activated', function(e, opts) {
	    slideshows.not(this).cycle('goto', opts.currSlide);
	});

	$('#carouselgallery .cycle-slide').click(function(){
	    var index = $("#carouselgallery").data('cycle.API').getSlideIndex(this);
	    slideshows.cycle('goto', index);
	});

	$('.errors').prev(".zend_form input[type=text], .zend_form input[type=email], .zend_form textarea").addClass('errorinput');

	isMobile();
});

$(window).resize(function() {
	isMobile();
});

function isMobile() {
    var windowWidth = $(window).width();
    if(windowWidth > 580){
    	$("#content_homepage #eyecatcher #video").fitVids();
    }
}