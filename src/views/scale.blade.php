<div class="row fill">
    <div class="col-md-12 fill">

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
