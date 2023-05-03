<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('page-title'); ?> - <?php echo e(settings('app_name')); ?></title>

    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="/back/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/back/bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="/back/bower_components/Ionicons/css/ionicons.min.css">
    <link rel="stylesheet" href="/back/dist/css/AdminLTE.min.css">

    <link rel="stylesheet" href="/back/dist/css/skins/_all-skins.min.css">

    <link rel="stylesheet" href="/back/bower_components/morris.js/morris.css">
    <link rel="stylesheet" href="/back/bower_components/jvectormap/jquery-jvectormap.css">
    <link rel="stylesheet" href="/back/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="/back/bower_components/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="/back/bower_components/bootstrap-daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="/back/bower_components/bootstrap-tagsinput/bootstrap-tagsinput.css">
    <link rel="stylesheet" href="/back/bower_components/croppie/croppie.css">
    <link rel="stylesheet" href="/back/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
    <link rel="stylesheet" href="/back/bower_components/select2/dist/css/select2.css">
    <link rel="stylesheet" href="/back/bower_components/select2/dist/css/select2-bootstrap.min.css">
    <link rel="stylesheet" href="/back/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">

    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="/back/plugins/iCheck/all.css">

    <link rel="stylesheet" href="/back/dist/css/new.css">
	<link rel="stylesheet" href="/back/dist/css/custom.css">														

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <style>
        .toolbar {
            float: left;
            width: 75%;
        }
    </style>

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-black sidebar-mini <?php if(isset($_COOKIE['sidebar-collapse']) && $_COOKIE['sidebar-collapse'] == 'true'): ?> sidebar-collapse <?php endif; ?>">
<div class="wrapper">

    <?php echo $__env->make('backend.partials.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('backend.partials.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <?php echo $__env->yieldContent('content'); ?>
    </div>

    <!-- /.content-wrapper -->
    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <strong><a href="http://flaming999.info/"><?php echo e(settings('app_name')); ?></a></strong>
        </div>
            <strong>Core 8.5</strong> {<strong>rev.</strong> 2290}
    </footer>

</div>
<!-- ./wrapper -->

<script src="/back/bower_components/jquery/dist/jquery.min.js"></script>
<script src="/back/bower_components/jquery-ui/jquery-ui.min.js"></script>
<script>
    var timezon = '<?php echo e(date_default_timezone_get()); ?>';
    $.widget.bridge('uibutton', $.ui.button);
</script>
<script src="/back/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="/back/bower_components/raphael/raphael.min.js"></script>
<script src="/back/bower_components/morris.js/morris.min.js"></script>
<script src="/back/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<script src="/back/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="/back/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<script src="/back/bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
<script src="/back/bower_components/moment/min/moment.min.js"></script>
<script src="/back/bower_components/moment/min/moment-timezone-with-data-1970-2030.min.js"></script>

<script src="/back/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="/back/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="/back/bower_components/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script src="/back/bower_components/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
<script src="/back/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<script src="/back/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script src="/back/bower_components/fastclick/lib/fastclick.js"></script>
<script src="/back/bower_components/croppie/croppie.js"></script>
<script src="/back/bower_components/select2/dist/js/select2.js"></script>
<script src="/back/dist/js/adminlte.js"></script>
<!--<script src="/back/js/sweetalert.min.js"></script>-->
<script src="/back/js/delete.handler.js"></script>
<script src="/back/bower_components/jquery-validation/jquery.validate.min.js"></script>
<script src="/back/bower_components/jquery-validation/additional-methods.min.js"></script>
<script src="/back/plugins/jquery-cookie/jquery.cookie.min.js"></script>

<script src="/back/bower_components/ckeditor5/ckeditor.js"></script>
<script src="/back/bower_components/ckeditor5/sample.js"></script>

<link href="/back/bower_components/sweetalert2/bootstrap-4.css" rel="stylesheet">
<link rel="stylesheet" href="/back/dist/css/additional.css">
<script src="/back/bower_components/sweetalert2/sweetalert2.js"></script>

<!-- DataTables -->
<script src="/back/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="/back/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

<!-- iCheck 1.0.1 -->
<script src="/back/plugins/iCheck/icheck.min.js"></script>

<!-- InputMask -->
<script src="/back/plugins/input-mask/jquery.inputmask.js"></script>
<script src="/back/plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
<script src="/back/plugins/input-mask/jquery.inputmask.extensions.js"></script>

<script src="/back/dist/js/demo.js"></script>

<script type="text/javascript">
    $(function() {
        moment.tz.setDefault("<?php echo e(config('app.timezone')); ?>");

        setInterval(function(){
            $.get('/refresh-csrf').done(function(data){
                $('[name="csrf-token"]').attr('content', data);
                $('[name="_token"]').val(data);
            });
        }, 5000);

    });
</script>

<?php echo $__env->yieldContent('scripts'); ?>

</body>
</html><?php /**PATH /home/betshopus/casino/resources/views/backend/layouts/app.blade.php ENDPATH**/ ?>