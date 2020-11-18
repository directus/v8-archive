//////////////////////////////////////////////////////////////////////////////
// Imports

import SmoothScroll from 'smooth-scroll';
import $ from 'cash-dom';

//////////////////////////////////////////////////////////////////////////////
// Page Load

window.onload = function() {
	setTimeout(revealPage, 500);
}

function revealPage() {
	$("body").removeClass("loading");
}

//////////////////////////////////////////////////////////////////////////////
// Smooth scroll to internal anchor links

var scroll = new SmoothScroll('a[href*="#"]', {
	speed: 1250,
	speedAsDuration: true,
	easing: 'easeInOutQuart'
});

//////////////////////////////////////////////////////////////////////////////
// Full width images with captions

$('p').has('img').css('width','calc(100% + 1px)');

$.each($("p img"), function() {
	$(this).wrap("<figure></figure>");
	var altText = $(this).attr('alt');
	$(this).after('<figcaption>'+altText+'</figcaption>');
});

//////////////////////////////////////////////////////////////////////////////
// Navigation events

// Toggle Nav: Click Nav Menu
$('.menu').on('click', function() {
	$('body').toggleClass('menu-open');
	refractMenu = $('body').hasClass('menu-open');
	navReflow();
});

// Toggle Nav: Press "Escape"
$(document).on('keyup', function(e) {
	if(e.which == 27) { // Escape
		$('body').toggleClass('menu-open');
		refractMenu = $('body').hasClass('menu-open');
		navReflow();
	}
});

function navReflow() {
	$('.overlay li').each(function( index ) {
		$(this).css('animation', 'none');
		$(this).outerHeight(); // Force reflow to reset css animation
		$(this).css('animation', null);
	});
}

// Click Nav Item
$('header nav li').on('click', function() {
	$('body').removeClass('menu-open');
	refractMenu = false;
	navReflow();
});

//////////////////////////////////////////////////////////////////////////////
// Show button when email exists

$('form#email input').on('keyup', function() {
	let email = document.getElementById("email_input");
	if(!email || email.value.length == 0 || !email.validity.valid){
		$('form#email').removeClass('enabled');
	} else {
		$('form#email').addClass('enabled');
	}
});

$('form#email input').on('blur', function() {
	let email = document.getElementById("email_input");
	if(!email || email.value.length == 0 || !email.validity.valid){
		$('form#email').removeClass('enabled');
	} else {
		$('form#email').addClass('enabled');
	}
});

//////////////////////////////////////////////////////////////////////////////
// Prism Effect for Logo

let refractHover = false;
let refractMenu = false;

let refractIntervalR = setInterval(refractR, 1000);
let refractIntervalG = setInterval(refractG, 1234);
let refractIntervalB = setInterval(refractB, 1379);
//clearInterval(refractInterval);

refractR();
refractG();
refractB();

$(".rgb").on({
	mouseenter: function () {
		refractHover = true;
	},
	mouseleave: function () {
		refractHover = false;
	}
});

function refractR() {
	if(refractHover || refractMenu) {
		$('.rgb-r').css({'transform' : 'rotate(0deg) translate(' + getOffset(1) +'px, 0px)'});
	} else {
		$('.rgb-r').css({'transform' : 'rotate(0deg) translate(0px, 0px)'});
	}
}

function refractG() {
	if(refractHover || refractMenu) {
		$('.rgb-g').css({'transform' : 'rotate(0deg) translate(' + getOffset(2) +'px, ' + getOffset(1) + 'px)'});
	} else {
		$('.rgb-g').css({'transform' : 'rotate(0deg) translate(0px, 0px)'});
	}
}

function refractB() {
	if(refractHover || refractMenu) {
		$('.rgb-b').css({'transform' : 'rotate(' + getOffset(1) +'deg) translate(' + getOffset(1) +'px, ' + getOffset(1) + 'px)'});
	} else {
		$('.rgb-b').css({'transform' : 'rotate(0deg) translate(0px, 0px)'});
	}
}

function getOffset(multiplier) {
	return (Math.random() * (multiplier*2)) - multiplier;
}

//////////////////////////////////////////////////////////////////////////////
// Effects on enter viewport

var $win = $(window);
var $vis = $('#visible');
var fadeThreshold = 33;

$win.on('scroll', function () {
	$('.fadeIn').each(function (index, value) {
		let percentSeen = percentageSeen( $(this) );
		if (percentSeen > 0 && percentSeen < fadeThreshold){

			// Condense elements
			$(this).find('h1')
				.css('padding-top', (fadeThreshold - percentSeen)*2 + 'px')
				.css('padding-bottom', (fadeThreshold - percentSeen)*2 + 'px');

			let newsOpacity = percentSeen / fadeThreshold;
			$(this).css('opacity', newsOpacity);
		} else if (percentSeen > 0 && percentSeen < 100) {
			$(this).css('opacity', 1);
			$(this).find('h1')
				.css('padding-top', '0px')
				.css('padding-bottom', '0px');
		}
	});
	$('.fadeOut').each(function (index, value) {
		let percentSeen = percentageSeen( $(this) );
		if (percentSeen > (100 - fadeThreshold) && percentSeen < 100){
			let invertedPercent = Math.abs(percentSeen - 100);
			let newsOpacity = invertedPercent / fadeThreshold;
			$(this).css('opacity', newsOpacity);
		} else if (percentSeen > 0 && percentSeen < 100) {
			$(this).css('opacity', 1);
		}
	});
});

function percentageSeen (el) {
	var $element = el;
	var viewportHeight = $(window).height(),
			scrollTop = (document.documentElement && document.documentElement.scrollTop) || document.body.scrollTop, // $win.scrollTop(),
			elementOffsetTop = $element.offset().top,
			elementHeight = $element.height();

	if (elementOffsetTop > (scrollTop + viewportHeight)) {
		return 0;
	} else if ((elementOffsetTop + elementHeight) < scrollTop) {
		return 100;
	} else {
		var distance = (scrollTop + viewportHeight) - elementOffsetTop;
		var percentage = distance / ((viewportHeight + elementHeight) / 100);
		percentage = Math.round(percentage);
		return percentage;
	}
}

$win.trigger('scroll');

//////////////////////////////////////////////////////////////////////////////
