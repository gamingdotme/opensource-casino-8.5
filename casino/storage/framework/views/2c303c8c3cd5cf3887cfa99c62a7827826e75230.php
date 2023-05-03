<?php $__env->startSection('page-title', trans('app.edit_tournament')); ?>
<?php $__env->startSection('page-heading', $tournament->name); ?>

<?php $__env->startSection('content'); ?>
    <section class="content-header">
        <?php echo $__env->make('backend.partials.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </section>
    <section class="content">
        <?php echo Form::open(['route' => array('backend.tournament.update', $tournament->id), 'files' => true, 'id' => 'tournament-form']); ?>

        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?php echo app('translator')->get('app.edit_tournament'); ?></h3>
                <div class="pull-right box-tools">
                    <a href="javascript:;" class="btn btn-block btn-primary btn-sm" id="addPrize"><?php echo app('translator')->get('app.add'); ?> <?php echo app('translator')->get('app.prize'); ?></a>
                </div>
            </div>
            <div class="box-body">

                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a data-toggle="tab" href="#details"><?php echo app('translator')->get('app.tournament_details'); ?></a>
                        </li>
                        <li >
                            <a data-toggle="tab" href="#stats"><?php echo app('translator')->get('app.tournament_stats'); ?></a>
                        </li>
                    </ul>
                    <div class="tab-content" id="nav-tabContent">
                        <div class=" active tab-pane" id="details">
                            <?php echo $__env->make('backend.tournaments.partials.base', ['edit' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div>
                        <div class="tab-pane" id="stats">

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo app('translator')->get('app.login'); ?></th>
                                        <th><?php echo app('translator')->get('app.is_bot'); ?></th>
                                        <th><?php echo app('translator')->get('app.points'); ?></th>
                                        <th><?php echo app('translator')->get('app.prize'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if( count($tournament->stats) ): ?>
                                        <?php $index=1; ?>
                                        <?php $__currentLoopData = $tournament->stats->where('prize_id', '!=', 0)->sortBy('prize_id'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($index); ?></td>
                                                <td><?php echo e($stat->is_bot ? $stat->bot->username : $stat->user->username); ?></td>
                                                <td><?php echo e($stat->is_bot ? __('app.yes') : __('app.no')); ?></td>
                                                <td><?php echo e($stat->points); ?></td>
                                                <td><?php echo e($stat->prize ? number_format($stat->prize->prize, 2,".","") : ''); ?></td>
                                            </tr>
                                            <?php $index++; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php $__currentLoopData = $tournament->stats->where('prize_id', '=', 0)->sortByDesc('points'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($index); ?></td>
                                                <td><?php echo e($stat->is_bot ? $stat->bot->username : $stat->user->username); ?></td>
                                                <td><?php echo e($stat->is_bot ? __('app.yes') : __('app.no')); ?></td>
                                                <td><?php echo e($stat->points); ?></td>
                                                <td><?php echo e($stat->prize ? number_format($stat->prize->prize, 2,".","") : ''); ?></td>
                                            </tr>
                                            <?php $index++; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <tr><td colspan="5"><?php echo app('translator')->get('app.no_data'); ?></td></tr>
                                    <?php endif; ?>
                                    </tbody>
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo app('translator')->get('app.login'); ?></th>
                                        <th><?php echo app('translator')->get('app.is_bot'); ?></th>
                                        <th><?php echo app('translator')->get('app.points'); ?></th>
                                        <th><?php echo app('translator')->get('app.prize'); ?></th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>


            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">
                    <?php echo app('translator')->get('app.edit_tournament'); ?>
                </button>
                <?php if (\Auth::user()->hasPermission('tournaments.delete')) : ?>
                <a href="<?php echo e(route('backend.tournament.delete', $tournament->id)); ?>"
                   class="btn btn-danger"
                   data-method="DELETE"
                   data-confirm-title="<?php echo app('translator')->get('app.please_confirm'); ?>"
                   data-confirm-text="<?php echo app('translator')->get('app.are_you_sure_delete_tournament'); ?>"
                   data-confirm-delete="<?php echo app('translator')->get('app.yes_delete_him'); ?>">
                    <?php echo app('translator')->get('app.delete_tournament'); ?>
                </a>
                <?php endif; ?>
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

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/betshopus/casino/resources/views/backend/tournaments/edit.blade.php ENDPATH**/ ?>