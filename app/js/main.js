(function() {

	console.log('main.js loaded');

	setUpMobile = function() {
		var navButton 			= $('#nav-btn'),
			header 				= $('header'),
			closedHeight 		= $('.closed-nav').height(),
			openClass 			= $('.open-nav'),
			mainNavHeight 		= $('#main-nav').height(),
			openHeight 			= $('.open-nav').height(closedHeight + mainNavHeight)
		;

		navButton.on('click', function() {
			openClass.height(mainNavHeight + closedHeight);
			if ( header.hasClass('closed-nav') ) {
				header.toggleClass('open-nav');
			} else {
				header.toggleClass('closed-nav');
			}
		});
	}
	setUpMobile();
})();