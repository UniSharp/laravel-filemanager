<div class="row fill">
    <div class="col-md-8 fill">
        <div class="crop-container">
            <img src="{!! $img !!}" class="img-responsive">
        </div>
    </div>
    <div class="col-md-4 fill">
        <div class="img-preview"></div>

        <button class="btn btn-primary" onclick="crop()">Crop</button>
        {!! Form::open(array('url' => '/laravel-filemanager/crop', 'role' => 'form', 'name' => 'cropForm',
            'id' => 'cropForm', 'method' => 'post')) !!}
            <input type="hidden" id="img" name="img" value="{!! $img !!}">
            <input type="hidden" id="dir" name="dir" value="{!! $dir !!}">
            <input type="hidden" id="dataX" name="dataX">
            <input type="hidden" id="dataY" name="dataY">
            <input type="hidden" id="dataWidth" name="dataWidth">
            <input type="hidden" id="dataHeight" name="dataHeight">
        {!! Form::close() !!}
    </div>

</div>

<script>
    $(document).ready(function () {
        var $dataX = $('#dataX'),
                $dataY = $('#dataY'),
                $dataHeight = $('#dataHeight'),
                $dataWidth = $('#dataWidth');

        $('.crop-container > img').cropper({
            //aspectRatio: 16 / 9,
            preview: ".img-preview",
            crop: function (data) {
                // Output the result data for cropping image.
                $dataX.val(Math.round(data.x));
                $dataY.val(Math.round(data.y));
                $dataHeight.val(Math.round(data.height));
                $dataWidth.val(Math.round(data.width));
            }
        });
    });

    function crop() {
        /*$.ajax({
            type: "POST",
            dataType: "text",
            url: "/laravel-filemanager/crop",
            data: "img="
                + $("#image").val()
                + "dir="
                + $("#dir").val()
                + "dataX="
                + $("#dataX").val()
                + "dataY="
                + $("#dataY").val()
                + "dataHeight="
                + $("#dataHeight").val()
                + "dataWidth="
                + $("#dataWidth").val(),
            cache: false
        }).done(function (data) {

        });*/
        $("#cropForm").submit();
    }
</script>