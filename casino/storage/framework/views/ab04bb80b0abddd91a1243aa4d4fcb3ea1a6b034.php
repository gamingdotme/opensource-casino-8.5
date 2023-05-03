<?php $__env->startSection('page-title', trans('app.add_tournament')); ?>
<?php $__env->startSection('page-heading', trans('app.add_tournament')); ?>

<?php $__env->startSection('content'); ?>

    <section class="content-header">
        <?php echo $__env->make('backend.partials.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </section>

    <section class="content">
        <?php echo Form::open(['route' => 'backend.tournament.store', 'files' => true, 'id' => 'tournament-form']); ?>

        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?php echo app('translator')->get('app.add_tournament'); ?></h3>
                <div class="pull-right box-tools">
                    <a href="javascript:;" class="btn btn-block btn-primary btn-sm" id="addPrize"><?php echo app('translator')->get('app.add'); ?> <?php echo app('translator')->get('app.prize'); ?></a>
                </div>
            </div>

            <div class="box-body">
                <?php echo $__env->make('backend.tournaments.partials.base', ['edit' => false], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary">
                    <?php echo app('translator')->get('app.add_tournament'); ?>
                </button>
            </div>
        </div>
        <?php echo Form::close(); ?>

    </section>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        $('.datepicker').datetimepicker({
            format: 'YYYY-MM-DD HH:mm',
            timeZone: '<?php echo e(config('app.timezone')); ?>'
        });

        $('#addPrize').click(function(){
            $('#prizes').append(
                '<div class="prize_item">\n' +
                '   <div class="form-group">\n' +
                '       <label></label>\n' +
                '       <div class="input-group">\n' +
                '           <input type="number" step="0.0000001" name="prize[]" class="form-control" required>\n' +
                '           <div class="input-group-btn">\n' +
                '               <button type="button" class="btn btn-info delete_prize">-</button>\n' +
                '           </div>\n' +
                '       </div>\n' +
                '   </div>\n' +
                '</div>'
            );
            change_prize_labels();
        });

        $( "#prizes" ).on( "click", '.delete_prize', function(event) {
            $(event.target).parents('.prize_item').remove();
            change_prize_labels();
        });

        $('#categories').on('select2:select', function (e) {
            var selected = $('#categories').val();
            if (selected.length === 0) {}
            get_games(selected);
        });
        $('#categories').on('select2:unselect', function (e) {
            var selected = $('#categories').val();
            get_games(selected);
        });

        function get_games(selected) {
            $.getJSON('<?php echo e(route('backend.tournament.games')); ?>', {'id' : selected}, function(data){
                $('#games').empty().trigger("change");
                if (Object.keys(data).length > 0) {
                    $.each(data, function( index, value ) {
                        $('#games').append(new Option(value, index, false, false)).trigger('change');
                    });
                }
            });
        }

        function change_prize_labels() {
            $( "#prizes label" ).each(function( index ) {
                $( this ).text(  "<?php echo app('translator')->get('app.prize'); ?> " + (parseInt(index) + 1) );
            });
        }


    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/betshopus/casino/resources/views/backend/tournaments/add.blade.php ENDPATH**/ ?>