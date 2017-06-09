

jQuery(".glossary").hover(function(e){
    e.preventDefault();
    jQuery(this).find(".popup").fadeToggle("fast");
});
