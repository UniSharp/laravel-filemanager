## Note
Check `vendor/unisharp/laravel-filemanager/src/views/demo.blade.php`, which already integrated all options from below.

## WYSIWYG Editor Integration:
### Option 1: CKEditor

```html
<textarea id="my-editor" name="content" class="form-control">{!! old('content', 'test editor content') !!}</textarea>
<script src="//cdn.ckeditor.com/4.6.2/standard/ckeditor.js"></script>
<script>
  var options = {
    filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
    filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token={{csrf_token()}}',
    filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
    filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token={{csrf_token()}}'
  };
</script>
```

* Sample 1 - Replace by ID:

  ```html
  <script>
  CKEDITOR.replace('my-editor', options);
  </script>
  ```

* Sample 2 - With JQuery Selector:

  ```html
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <script src="/vendor/unisharp/laravel-ckeditor/adapters/jquery.js"></script>
  <script>
  $('textarea.my-editor').ckeditor(options);
  </script>
  ```

### Option 2: TinyMCE4

```html
<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
<textarea name="content" class="form-control my-editor">{!! old('content', $content) !!}</textarea>
<script>
  var editor_config = {
    path_absolute : "/",
    selector: "textarea.my-editor",
    plugins: [
      "advlist autolink lists link image charmap print preview hr anchor pagebreak",
      "searchreplace wordcount visualblocks visualchars code fullscreen",
      "insertdatetime media nonbreaking save table contextmenu directionality",
      "emoticons template paste textcolor colorpicker textpattern"
    ],
    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media",
    relative_urls: false,
    file_browser_callback : function(field_name, url, type, win) {
      var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
      var y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight;

      var cmsURL = editor_config.path_absolute + 'laravel-filemanager?field_name=' + field_name;
      if (type == 'image') {
        cmsURL = cmsURL + "&type=Images";
      } else {
        cmsURL = cmsURL + "&type=Files";
      }

      tinyMCE.activeEditor.windowManager.open({
        file : cmsURL,
        title : 'Filemanager',
        width : x * 0.8,
        height : y * 0.8,
        resizable : "yes",
        close_previous : "no"
      });
    }
  };

  tinymce.init(editor_config);
</script>
```

## Standalone button
If you are going to use filemanager independently, meaning set the value of an input to selected photo/file url, follow this structure:

1. Create a button, input, and image preview holder if you are going to choose images.
  Specify the id to the input and image preview by `data-input` and `data-preview`.

    ```html
    <div class="input-group">
      <span class="input-group-btn">
        <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary">
          <i class="fa fa-picture-o"></i> Choose
        </a>
      </span>
      <input id="thumbnail" class="form-control" type="text" name="filepath">
    </div>
    <img id="holder" style="margin-top:15px;max-height:100px;">
    ``` 
1. Import lfm.js(run `php artisan vendor:publish` if you need).

    ```html
    <script src="/vendor/laravel-filemanager/js/lfm.js"></script>
    ```

1. Init filemanager with type. (requires jQuery)

    ```javascript
    $('#lfm').filemanager('image');
    ```
    or
    ```javascript
    $('#lfm').filemanager('file');
    ```

    Domain can be specified in the second parameter(optional, but will be required when developing on Windows mechines) :

    ```javascript
    var domain = "{{ url() }}";
    $('#lfm').filemanager('image', {prefix: domain});
    ```
    
## JavaScript integration
In case you are developing javascript application and you want dynamically to trigger filemanager popup, you can create function like this. It doesn't rely on jQuery.


```javascript
var lfm = function(options, cb) {

	var route_prefix = (options && options.prefix) ? options.prefix : '/laravel-filemanager';

	window.open(route_prefix + '?type=' + options.type || 'file', 'FileManager', 'width=900,height=600');
	window.SetUrl = cb;
}
```

And use it like this:

```javascript
lfm({type: 'image', prefix: 'prefix'}, function(url, path) {

});
```
