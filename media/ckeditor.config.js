/*
Copyright (c) 2003-2009, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.addStylesSet( 'my_styles', []);

/*
    { 
    	name : 'Таблица с прайсом', 
    	element : 'table', 
    	attributes : { 'class' : 'price_list' } 
    },
*/

var kcfinderPath = '/media/kcfinder-2.21';

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	config.language = 'ru';
	// config.skin = 'v2';
	// config.uiColor = '#AADC6E';

	config.toolbar_Full =
	[
	    ['Maximize', 'ShowBlocks', 'Source'],
//	    ['Cut','Copy','Paste','PasteText','PasteFromWord','-', 'SpellChecker', 'Scayt'],
	    ['Cut','Copy','Paste','PasteText','PasteFromWord'],

	    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	    '/',
	    ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
	    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
	    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
	    ['Link','Unlink','Anchor'],
/* 	    ['Image','Flash','Table','HorizontalRule','SpecialChar'], */
	    ['Image','Flash','Table'],

   	    ['Styles','Format']
//	    ['Styles','Format','Font','FontSize'],
//	    ['TextColor','BGColor'],
	];
	
	config.stylesCombo_stylesSet = 'my_styles';
	
	config.filebrowserBrowseUrl = kcfinderPath + '/browse.php?type=files';
	config.filebrowserImageBrowseUrl = kcfinderPath + '/browse.php?type=images';
	config.filebrowserFlashBrowseUrl = kcfinderPath + '/browse.php?type=flash';
	config.filebrowserUploadUrl = kcfinderPath + '/upload.php?type=files';
	config.filebrowserImageUploadUrl = kcfinderPath + '/upload.php?type=images';
	config.filebrowserFlashUploadUrl = kcfinderPath + '/upload.php?type=flash';
};