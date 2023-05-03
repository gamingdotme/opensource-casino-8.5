@if(isset ($errors) && count($errors) > 0)
    <script>
        $(function(){
            var msg = '<?php echo implode(chr(13),$errors->all()) ?>';
            swal({
                title: msg,
                icon: "warning",
            });
        })
    </script>
@endif

@if(Session::get('success'))
    <?php $data = Session::get('success'); ?>
    @if (is_array($data) && !isset($data['title']))
        @foreach ($data as $msg)
            <div class="alert alert-success alert-notification" style="color: #FFF !important">
                <i class="fa fa-check"></i>
                {{ $msg }}
            </div>
        @endforeach
    @else
        <script>
            $(function(){
                swal({
                    title: "{{ isset($data['title'])? $data['title'] : 'success' }}",
                    text: "{{ isset($data['title'])? $data['msg'] : $data }}",
                    icon: "success",
                });
            });
        </script>
    @endif
@endif
