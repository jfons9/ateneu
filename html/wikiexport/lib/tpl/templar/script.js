/* put 'DOKUWIKI:include_once' before any additional js you need */
/* DOKUWIKI:include_once js/bootstrap-affix.js */
/* KUWIKI:include_once js/bootstrap-alert.js */
/* KUWIKI:include_once js/bootstrap-button.js */
/* KUWIKI:include_once js/bootstrap-carousel.js */
/* DOKUWIKI:include_once js/bootstrap-collapse.js */
/* DOKUWIKI:include_once js/bootstrap-dropdown.js */
/* KUWIKI:include_once js/bootstrap-modal.js */
/* KUWIKI:include_once js/bootstrap-popover.js */
/* KUWIKI:include_once js/bootstrap-scrollspy.js */
/* KUWIKI:include_once js/bootstrap-tab.js */
/* KUWIKI:include_once js/bootstrap-tooltip.js */
/* KUWIKI:include_once js/bootstrap-transition.js */
/* KUWIKI:include_once js/bootstrap-typeahead.js */
//Fix for .navbar-fixed-top when accessing anchors
var shiftWindow = function() { scrollBy(0, -45); };
if (location.hash) shiftWindow();
if ("onhashchange" in window) {
	window.onhashchange=shiftWindow;
} else 
	jQuery(window).bind('popstate', shiftWindow);
