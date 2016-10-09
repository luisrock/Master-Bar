
// jQuery to hide Master Bar when "x" is clicked

jQuery(document).ready(function() {
 
// the #masterbar-wrapper id is hidden initially via the mab-styles.css file
     
	 // hide Master Bar when "x" is clicked
     jQuery('.mbhide a').click(function() {
     jQuery('.masterbar').slideUp('medium');
     return false;
  
  });

});