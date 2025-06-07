<?php
?>
<div class="container">

    <h1>Excel Upload</h1>


    <form method="POST" action="<?php echo base_url().'upload/readexcel'?>" enctype="multipart/form-data">

        <div class="form-group">

            <label>Upload Excel File</label>

            <input type="file" name="file" class="form-control">

        </div>

        <div class="form-group">

            <button type="submit" name="Submit" class="btn btn-success">Upload</button>

        </div>

        <p>Download Demo File from here : <a href="demo.ods"><strong>Demo.ods</strong></a></p>

    </form>

</div>

