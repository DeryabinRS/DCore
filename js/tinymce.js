tinymce.init({
    selector:'#TINYArea',
    language: 'ru',
    forced_root_block : '',
    height: 250,
    plugins: [
        'advlist autolink lists link image charmap print preview hr anchor pagebreak',
        'searchreplace wordcount visualblocks visualchars code fullscreen',
        'insertdatetime media nonbreaking save table contextmenu directionality',
        'emoticons template paste textcolor colorpicker textpattern imagetools responsivefilemanager'
    ],
    toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent ' +
        '| link unlink image print preview media | forecolor backcolor emoticons | removeformat responsivefilemanager',
    image_advtab: true,
    relative_urls : false,
    templates: [
        { title: 'Test template 1', content: 'Test 1' },
        { title: 'Test template 2', content: 'Test 2' }
    ],
    image_advtab: true ,

    external_filemanager_path:"/inc/filemanager/",
    filemanager_title:"Responsive Filemanager" ,
    external_plugins: { "filemanager" : "/inc/filemanager/plugin.min.js"}
});