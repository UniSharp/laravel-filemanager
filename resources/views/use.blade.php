<script type='text/javascript'>
  function getUrlParam(paramName) {
    var reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
    var match = window.location.search.match(reParam);
    return ( match && match.length > 1 ) ? match[1] : null;
  }

  var funcNum = getUrlParam('CKEditorFuncNum');

  var par = window.parent;
  var op = window.opener;
  var o = (par && par.CKEDITOR) ? par : ((op && op.CKEDITOR) ? op : false);

  if (op) window.close();
  if (o !== false) o.CKEDITOR.tools.callFunction(funcNum, "{{ $file }}");
</script>
