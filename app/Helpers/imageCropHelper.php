<?php
function cropImage($img='') {
    ?>
    <link rel="stylesheet" href="https://unpkg.com/cropperjs/dist/cropper.css">
    <link rel="stylesheet" href="http://localhost/bathr/public/image_cropper/css/main.css">
    
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">                    
                    <div class="ibox-content">
                        <div class="col-sm-12">
                            <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Crop Image</button>
                            <div class="modal fade" id="myModal" role="dialog">
                                <div class="modal-dialog modal-md">

                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>                                        
                                        </div>
                                        <div class="modal-body">

                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-9">
                                                        <!-- <h3>Demo:</h3> -->
                                                        <div class="img-container">
                                                            <?php echo $img; ?>
                                                        </div>
                                                    </div>                               
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-md-9 docs-buttons">
                                                        <div class="btn-group"> 
                                                            <label class="btn btn-primary btn-upload" for="inputImage" title="Upload image file">
                                                                <input type="file" class="sr-only" id="inputImage" name="file" accept=".jpg,.jpeg,.png,.gif,.bmp,.tiff">
                                                                <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="Import image with Blob URLs">
                                                                    <span class="fa fa-upload"></span>
                                                                </span>
                                                            </label>
                                                        </div>

                                                        <div class="btn-group btn-group-crop">                                        
                                                            <button type="button" class="btn btn-success" data-method="getCroppedCanvas" data-option="{ &quot;width&quot;: 200, &quot;height&quot;: 200 }" data-dismiss="modal">
                                                                <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;getCroppedCanvas&quot;, { width: 200, height: 200 })">
                                                                    Crop Image
                                                                </span>
                                                            </button>
                                                        </div>                                    
                                                        <!-- Show the cropped image in modal -->
                                                    </div><!-- /.docs-buttons -->
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <!-- Shibly: cropper html code start -->
                            <br/><br/>                           
                            <!-- Shibly: cropper html code end -->
                        </div>                    
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js"></script>
    <script src="https://fengyuanchen.github.io/js/common.js"></script>
    <script src="https://unpkg.com/cropperjs/dist/cropper.js"></script>
    <script src="http://localhost/bathr/public/image_cropper/js/jquery-cropper.js"></script>
    <script src="http://localhost/bathr/public/image_cropper/js/main.js"></script>
    <?php
}
?>
