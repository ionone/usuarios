<?php
error_reporting(E_ALL);
include ("incl/data.php");
include ("incl/functions.php");
include ("incl/session_gestion.php");
initiate();
$TitlePag = "title_Picture";
$btnActive = 4;

// Tratamiento formulario
$picture_name = "";
$message = "";
$uploads_dir = $_SERVER['DOCUMENT_ROOT'] . '/img/tmp';
if (isset($_FILES['picture']['error']) && $_FILES['picture']['error'] == 0) {
    $tmp_name = $_FILES["picture"]["tmp_name"];
    $picture_name = $_FILES['picture']['name'];
    move_uploaded_file($tmp_name, "$uploads_dir/$picture_name");
} elseif (isset($_FILES['picture']['error'])) {
    switch ($_FILES['picture']['error']) {
        case UPLOAD_ERR_INI_SIZE:
            $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
            break;
        case UPLOAD_ERR_FORM_SIZE:
            $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
            break;
        case UPLOAD_ERR_PARTIAL:
            $message = "The uploaded file was only partially uploaded";
            break;
        case UPLOAD_ERR_NO_FILE:
            $message = "No file was uploaded";
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            $message = "Missing a temporary folder";
            break;
        case UPLOAD_ERR_CANT_WRITE:
            $message = "Failed to write file to disk";
            break;
        case UPLOAD_ERR_EXTENSION:
            $message = "File upload stopped by extension";
            break;
        default:
            $message = "Unknown upload error";
            break;
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <?php include ("incl/header.php") ?>
    </head>
    <body>
        <div class="container">
            <?php include ("incl/navbar.php") ?>
            <div class="jumbotron">
                <?php echo $message ?>
                <?php if ($picture_name != "") { ?>
                    <header class="codrops-header">
                        <h2>Image Resizing &amp; Cropping <br /><span>with Canvas</span></h2>
                    </header>
                    <div class="component">
                        <div class="overlay">
                            <div class="overlay-inner">
                            </div>
                        </div>
                        <img class="resize-image" style="background-size: auto" src="<?php echo "/img/tmp/" . $picture_name ?>" alt="image for resizing"/>
                        <button class="btn-crop js-crop">Recortar<img class="icon-crop" src="img/crop.svg"></button>
                    </div>
                    <div class="a-tip">
                        <p><strong>Hint:</strong> hold <span>SHIFT</span> while resizing to keep the original aspect ratio.</p>
                    </div>
                    <div><a class="btn btn-default" href="<?php echo $_SERVER['PHP_SELF']?>"><?php echo __('bt_Cancel', $lang) ?></a></div>
                <?php } else { ?>
                    <div class="row">
                        <p class="lead">
                            <form enctype="multipart/form-data" action="temp.php" method="POST">                                
                                <input type="hidden" name="MAX_FILE_SIZE" value="3145728" />
                                <label class="left"><?php echo __('lb_Upload', $lang) ?></label><br/>
                                <input name="picture" type="file" class="btn btn-lg btn-info" /><br/>
                                <input type="submit" value="Enviar fichero" class="btn btn-lg btn-success"/><br/>
                                Max. 3Mb
                            </form>
                        </p>
                    </div>
                <?php } ?>                
            </div><!-- /content -->
            <?php include ("incl/footer.php") ?>
            <script>                
                var resizeableImage = function (image_target) {
                    // Some variable and settings
                    var $container,
                            orig_src = new Image(),
                            image_target = $(image_target).get(0),
                            event_state = {},
                            constrain = false,
                            min_width = 60, // Change as required
                            min_height = 60,
                            max_width = 800, // Change as required
                            max_height = 900,
                            resize_canvas = document.createElement('canvas');

                    init = function () {

                        // When resizing, we will always use this copy of the original as the base
                        orig_src.src = image_target.src;

                        // Wrap the image with the container and add resize handles
                        $(image_target).wrap('<div class="resize-container"></div>')
                                .before('<span class="resize-handle resize-handle-nw"></span>')
                                .before('<span class="resize-handle resize-handle-ne"></span>')
                                .after('<span class="resize-handle resize-handle-se"></span>')
                                .after('<span class="resize-handle resize-handle-sw"></span>');

                        // Assign the container to a variable
                        $container = $(image_target).parent('.resize-container');

                        // Add events
                        $container.on('mousedown touchstart', '.resize-handle', startResize);
                        $container.on('mousedown touchstart', 'img', startMoving);
                        $('.js-crop').on('click', crop);
                    };

                    startResize = function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        saveEventState(e);
                        $(document).on('mousemove touchmove', resizing);
                        $(document).on('mouseup touchend', endResize);
                    };

                    endResize = function (e) {
                        e.preventDefault();
                        $(document).off('mouseup touchend', endResize);
                        $(document).off('mousemove touchmove', resizing);
                    };

                    saveEventState = function (e) {
                        // Save the initial event details and container state
                        event_state.container_width = $container.width();
                        event_state.container_height = $container.height();
                        event_state.container_left = $container.offset().left;
                        event_state.container_top = $container.offset().top;
                        event_state.mouse_x = (e.clientX || e.pageX || e.originalEvent.touches[0].clientX) + $(window).scrollLeft();
                        event_state.mouse_y = (e.clientY || e.pageY || e.originalEvent.touches[0].clientY) + $(window).scrollTop();

                        // This is a fix for mobile safari
                        // For some reason it does not allow a direct copy of the touches property
                        if (typeof e.originalEvent.touches !== 'undefined') {
                            event_state.touches = [];
                            $.each(e.originalEvent.touches, function (i, ob) {
                                event_state.touches[i] = {};
                                event_state.touches[i].clientX = 0 + ob.clientX;
                                event_state.touches[i].clientY = 0 + ob.clientY;
                            });
                        }
                        event_state.evnt = e;
                    };

                    resizing = function (e) {
                        var mouse = {}, width, height, left, top, offset = $container.offset();
                        mouse.x = (e.clientX || e.pageX || e.originalEvent.touches[0].clientX) + $(window).scrollLeft();
                        mouse.y = (e.clientY || e.pageY || e.originalEvent.touches[0].clientY) + $(window).scrollTop();

                        // Position image differently depending on the corner dragged and constraints
                        if ($(event_state.evnt.target).hasClass('resize-handle-se')) {
                            width = mouse.x - event_state.container_left;
                            height = mouse.y - event_state.container_top;
                            left = event_state.container_left;
                            top = event_state.container_top;
                        } else if ($(event_state.evnt.target).hasClass('resize-handle-sw')) {
                            width = event_state.container_width - (mouse.x - event_state.container_left);
                            height = mouse.y - event_state.container_top;
                            left = mouse.x;
                            top = event_state.container_top;
                        } else if ($(event_state.evnt.target).hasClass('resize-handle-nw')) {
                            width = event_state.container_width - (mouse.x - event_state.container_left);
                            height = event_state.container_height - (mouse.y - event_state.container_top);
                            left = mouse.x;
                            top = mouse.y;
                            if (constrain || e.shiftKey) {
                                top = mouse.y - ((width / orig_src.width * orig_src.height) - height);
                            }
                        } else if ($(event_state.evnt.target).hasClass('resize-handle-ne')) {
                            width = mouse.x - event_state.container_left;
                            height = event_state.container_height - (mouse.y - event_state.container_top);
                            left = event_state.container_left;
                            top = mouse.y;
                            if (constrain || e.shiftKey) {
                                top = mouse.y - ((width / orig_src.width * orig_src.height) - height);
                            }
                        }

                        // Optionally maintain aspect ratio
                        if (constrain || e.shiftKey) {
                            height = width / orig_src.width * orig_src.height;
                        }

                        if (width > min_width && height > min_height && width < max_width && height < max_height) {
                            // To improve performance you might limit how often resizeImage() is called
                            resizeImage(width, height);
                            // Without this Firefox will not re-calculate the the image dimensions until drag end
                            $container.offset({'left': left, 'top': top});
                        }
                    }

                    resizeImage = function (width, height) {
                        resize_canvas.width = width;
                        resize_canvas.height = height;
                        resize_canvas.getContext('2d').drawImage(orig_src, 0, 0, width, height);
                        $(image_target).attr('src', resize_canvas.toDataURL("image/png"));
                    };

                    startMoving = function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        saveEventState(e);
                        $(document).on('mousemove touchmove', moving);
                        $(document).on('mouseup touchend', endMoving);
                    };

                    endMoving = function (e) {
                        e.preventDefault();
                        $(document).off('mouseup touchend', endMoving);
                        $(document).off('mousemove touchmove', moving);
                    };

                    moving = function (e) {
                        var mouse = {}, touches;
                        e.preventDefault();
                        e.stopPropagation();

                        touches = e.originalEvent.touches;

                        mouse.x = (e.clientX || e.pageX || touches[0].clientX) + $(window).scrollLeft();
                        mouse.y = (e.clientY || e.pageY || touches[0].clientY) + $(window).scrollTop();
                        $container.offset({
                            'left': mouse.x - (event_state.mouse_x - event_state.container_left),
                            'top': mouse.y - (event_state.mouse_y - event_state.container_top)
                        });
                        // Watch for pinch zoom gesture while moving
                        if (event_state.touches && event_state.touches.length > 1 && touches.length > 1) {
                            var width = event_state.container_width, height = event_state.container_height;
                            var a = event_state.touches[0].clientX - event_state.touches[1].clientX;
                            a = a * a;
                            var b = event_state.touches[0].clientY - event_state.touches[1].clientY;
                            b = b * b;
                            var dist1 = Math.sqrt(a + b);

                            a = e.originalEvent.touches[0].clientX - touches[1].clientX;
                            a = a * a;
                            b = e.originalEvent.touches[0].clientY - touches[1].clientY;
                            b = b * b;
                            var dist2 = Math.sqrt(a + b);

                            var ratio = dist2 / dist1;

                            width = width * ratio;
                            height = height * ratio;
                            // To improve performance you might limit how often resizeImage() is called
                            resizeImage(width, height);
                        }
                    };

                    crop = function () {
                        //Find the part of the image that is inside the crop box
                        var crop_canvas,
                                left = $('.overlay').offset().left - $container.offset().left,
                                top = $('.overlay').offset().top - $container.offset().top,
                                width = $('.overlay').width(),
                                height = $('.overlay').height();

                        crop_canvas = document.createElement('canvas');
                        crop_canvas.width = width;
                        crop_canvas.height = height;

                        crop_canvas.getContext('2d').drawImage(image_target, left, top, width, height, 0, 0, width, height);
                        //window.open(crop_canvas.toDataURL("image/png"));
                        var data = crop_canvas.toDataURL('image/png');
                        var xhr = new XMLHttpRequest();
                        xhr.onreadystatechange = function () {
                            // request complete
                            if (xhr.readyState == 4) {
                                window.open('http://<?php echo $_SERVER['SERVER_NAME']?>/profile.php', '_self');
                                //window.open('http://<?php echo $_SERVER['SERVER_NAME']?>/profile.php?' + xhr.responseText, '_self');
                            }
                        }
                        xhr.open('POST', 'http://<?php echo $_SERVER['SERVER_NAME']?>/incl/uploads.php?user=<?php echo $_SESSION["token"] ?>', true);
                        xhr.setRequestHeader('Content-Type', 'application/upload');
                        xhr.send(data);
                    }

                    init();
                };
                // Kick everything off with the target image
                resizeableImage($('.resize-image'));
            </script>
        </div> <!-- /container -->
    </body>
</html>