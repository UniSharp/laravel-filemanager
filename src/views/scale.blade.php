<div class="row fill">
    <div class="col-md-8 fill">
        <img src="{!! $img !!}?r={{ str_random(40) }}" class="img img-responsive">
    </div>
    <div class="col-md-4 fill">
        <button class="btn btn-info" onclick="loadImages()">Cancel</button>
    </div>
</div>

<script>
    $(document).ready(function () {


    });

    function performScale() {

        $.ajax({
            type: "GET",
            dataType: "text",
            url: "/laravel-filemanager/cropimage",
            data: {
                img: $("#img").val(),
                dir: $("#dir").val(),
                dataX: $("#dataX").val(),
                dataY: $("#dataY").val(),
                dataHeight: $("#dataHeight").val(),
                dataWidth: $("#dataWidth").val()
            },
            cache: false
        }).done(function (data) {
            loadImages();
        });

        //$("#cropForm").submit();
    }
</script>
