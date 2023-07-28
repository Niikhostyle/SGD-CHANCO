/**
 * @license Copyright (c) 2003-2022, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	 //config.language = 'fr';
	 //config.uiColor = '#AADC6E';
	 config.height = '400px'; 
	 config.extraPlugins = 'colorbutton';
	 // Brazil colors only.
config.colorButton_colors = '00923E,F8C100,28166F';

config.colorButton_colors = 'FontColor1/FF9900,FontColor2/0066CC,FontColor3/F00';

// CKEditor color palette available before version 4.6.2.
config.colorButton_colors =
    '000,800000,8B4513,2F4F4F,008080,000080,4B0082,696969,' +
    'B22222,A52A2A,DAA520,006400,40E0D0,0000CD,800080,808080,' +
    'F00,FF8C00,FFD700,008000,0FF,00F,EE82EE,A9A9A9,' +
    'FFA07A,FFA500,FFFF00,00FF00,AFEEEE,ADD8E6,DDA0DD,D3D3D3,' +
    'FFF0F5,FAEBD7,FFFFE0,F0FFF0,F0FFFF,F0F8FF,E6E6FA,FFF';


	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbar = [
		{ name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo', 'Preview' ] },
		{ name: 'editing', items: [ 'Scayt' ] },
		{ name: 'links', items: [ 'Link', 'Unlink' ] },
		{ name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule' ] },
		{ name: 'tools', items: [ 'Maximize' ] },
		{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Strike','Underline', '-', 'RemoveFormat' ] },
		{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
		{ name: 'styles', items: [ 'Styles', 'Format','TextColor', 'BGColor' ] },
		{ name: 'Source', items: [ 'Source' ] }

	];

};
