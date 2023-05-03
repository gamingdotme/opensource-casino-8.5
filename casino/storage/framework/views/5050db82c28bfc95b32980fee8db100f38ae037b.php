<?php if(isset ($errors) && count($errors) > 0): ?>
    <script>
        $(function(){
            var msg = '<?php echo implode(chr(13),$errors->all()) ?>';
            swal({
                title: msg,
                icon: "warning",
            });
        })
    </script>
<?php endif; ?>

<?php if(Session::get('success')): ?>
    <?php $data = Session::get('success'); ?>
    <?php if(is_array($data) && !isset($data['title'])): ?>
        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="alert alert-success alert-notification" style="color: #FFF !important">
                <i class="fa fa-check"></i>
                <?php echo e($msg); ?>

            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>
        <script>
            $(function(){
                swal({
                    title: "<?php echo e(isset($data['title'])? $data['title'] : 'success'); ?>",
                    text: "<?php echo e(isset($data['title'])? $data['msg'] : $data); ?>",
                    icon: "success",
                });
            });
        </script>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH /home/betshopus/casino/resources/views/frontend/partials/messages.blade.php ENDPATH**/ ?>