<div class="row">
  <div class="col-md-8" id="containment">
    <img id="resize" src="{{ $img->url . '?timestamp=' . $img->updated }}" height="{{ $height }}" width="{{ $width }}">
  </div>
  <div class="col-md-4">

    <table class="table table-compact table-striped">
      <thead></thead>
      <tbody>
        @if ($scaled)
        <tr>
          <td>{{ trans('laravel-filemanager::lfm.resize-ratio') }}</td>
          <td>{{ number_format($ratio, 2) }}</td>
        </tr>
        <tr>
          <td>{{ trans('laravel-filemanager::lfm.resize-scaled') }}</td>
          <td>
            {{ trans('laravel-filemanager::lfm.resize-true') }}
          </td>
        </tr>
        @endif
        <tr>
          <td>{{ trans('laravel-filemanager::lfm.resize-old-height') }}</td>
          <td>{{ $original_height }}px</td>
        </tr>
        <tr>
          <td>{{ trans('laravel-filemanager::lfm.resize-old-width') }}</td>
          <td>{{ $original_width }}px</td>
        </tr>
        <tr>
          <td>{{ trans('laravel-filemanager::lfm.resize-new-height') }}</td>
          <td><span id="height_display"></span></td>
        </tr>
        <tr>
          <td>{{ trans('laravel-filemanager::lfm.resize-new-width') }}</td>
          <td><span id="width_display"></span></td>
        </tr>
      </tbody>
    </table>

    <button class="btn btn-primary" onclick="doResize()">{{ trans('laravel-filemanager::lfm.btn-resize') }}</button>
    <button class="btn btn-info" onclick="loadItems()">{{ trans('laravel-filemanager::lfm.btn-cancel') }}</button>

    <input type="hidden" id="img" name="img" value="{{ $img->name }}">
    <input type="hidden" name="ratio" value="{{ $ratio }}"><br>
    <input type="hidden" name="scaled" value="{{ $scaled }}"><br>
    <input type="hidden" id="original_height" name="original_height" value="{{ $original_height }}"><br>
    <input type="hidden" id="original_width" name="original_width" value="{{ $original_width }}"><br>
    <input type="hidden" id="height" name="height" value="{{ $height }}"><br>
    <input type="hidden" id="width" name="width" value="{{ $width }}">

  </div>
</div>

<script>
  $(document).ready(function () {
    $("#height_display").html($("#resize").height() + "px");
    $("#width_display").html($("#resize").width() + "px");

    $("#resize").resizable({
      aspectRatio: true,
      containment: "#containment",
      handles: "n, e, s, w, se, sw, ne, nw",
      resize: function (event, ui) {
        $("#width").val($("#resize").width());
        $("#height").val($("#resize").height());
        $("#height_display").html($("#resize").height() + "px");
        $("#width_display").html($("#resize").width() + "px");
      }
    });
  });

  function doResize() {
    performLfmRequest('doresize', {
      img: $("#img").val(),
      dataX: $("#dataX").val(),
      dataY: $("#dataY").val(),
      dataHeight: $("#height").val(),
      dataWidth: $("#width").val()
    }).done(loadItems);
  }
</script>
