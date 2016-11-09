(function( $ ){

    $.fn.filemanager = function(type) {
        type = type || 'image';

        if (type === 'image' || type === 'images') {
            type = 'Images';
        } else {
            type = 'Files';
        }

        this.on('click', function(e) {
            localStorage.setItem('target_input', $(this).data('input'));
            localStorage.setItem('target_preview', $(this).data('preview'));
            window.open('/laravel-filemanager?type=' + type, 'FileManager', 'width=900,height=600');
            return false;
        });
    }

})(jQuery);


function SetUrl(url){
  //set the value of the desired input to image url
  var target_input = $('#' + localStorage.getItem('target_input'));
  target_input.val(url);

  //set or change the preview image src
  var target_preview = $('#' + localStorage.getItem('target_preview'));
  target_preview.attr('src',url);
}
