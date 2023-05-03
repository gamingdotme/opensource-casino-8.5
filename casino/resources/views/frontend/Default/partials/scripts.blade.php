<script type="text/javascript">

    @if((isset ($errors) && count($errors) > 0) || Session::get('success', false))
    show_notifications();
    @endif

    $('body').on('click', '#send', function(event){
        var pincode = $('#inputPin').val();
        $.ajax({
            url: "{{ route('frontend.profile.pincode') }}",
            type: "GET",
            data: {pincode : pincode},
            dataType: "json",
            success: function(data){
                if( data.fail ){
                    $('.modal__notifications-block').html('<div class="alert alert-danger"><h4>Error</h4><p>' + data.error + '</p></div>');
                    show_notifications();
                }
                if( data.success ){
                    window.location.reload();
                }
            }
        });
    });

    $('body').on('click', '#verifyMyPhone', function(event){
        var phone = $('#myPhone').val();
        $.ajax({
            url: "{{ route('frontend.profile.phone') }}",
            type: "GET",
            data: {phone : phone},
            dataType: "json",
            success: function(data){
                if( data.fail ){
                    $('#verifyMyPhone').parents('.modal').find('.error-message').html(data.text).show();
                }
                if( data.success ){
                    show_modal('modal-invite-2');
                }
            }
        });
    });


    $('body').on('click', '#ckeckCode', function(event){
        var code = $('#myCode').val();
        $.ajax({
            url: "{{ route('frontend.profile.code') }}",
            type: "GET",
            data: {code : code},
            dataType: "json",
            success: function(data){
                $('#inputPhone').val('');
                if( data.fail ){
                    $('#ckeckCode').parents('.modal').find('.error-message').html(data.text).show();
                }
                if( data.success ){
                    window.location.reload();
                }
            }
        });
    });

    $('body').on('click', '#sendPhone', function(event){
        var phone = $('#inputPhone').val();
        $.ajax({
            url: "{{ route('frontend.profile.sms') }}",
            type: "GET",
            data: {phone : phone},
            dataType: "json",
            success: function(data){
                $('#inputPhone').val('');
                if( data.fail ){
                    $('#sendPhone').parents('.modal').find('.error-message').html(data.text).show();
                }
                if( data.success ){

                    if( !$('.modal__invite-phones').length){
                        $('.modal__invite-title').text('Invited friends');
                        $('.modal__invite-subtitle').remove();
                        $('.modal__invite-place').addClass('modal__invite-phones').removeClass('modal__invite-place');
                    }

                    $('.modal__invite-phones').append(
                        '<div class="modal__invite-row">' +
                        '<div class="modal__invite-info">' +
                        '<div class="modal__invite-date">'+ data.data.created +'</div>' +
                        '<span class="modal__invite-valid">Until '+ data.data.until +'</span>' +
                        '<div class="modal__invite-phones-value">'+ data.data.phone +'</div>' +
                        '</div>' +
                        '</div>'
                    );



                }
            }
        });
    });

    $('body').on('click', '.take_reward', function(event){
        var reward_id = $(event.target).data('id');
        console.log(reward_id);

        $.ajax({
            url: "{{ route('frontend.profile.reward') }}",
            type: "GET",
            data: {reward_id : reward_id},
            dataType: "json",
            success: function(data){
                console.log(data);
                if( data.fail ){
                    $(event.target).parents('.modal__invite-row').find('.error-message').html(data.text).show();
                }
                if( data.success ){
                    /*
                    $('.close-btn').click();
                    var popup = $('div.popup-1');
                    popup.find('.popup__value').attr('data-attr', parseInt(data.value));
                    popup.find('.popup__value').html(parseInt(data.value));
                    popup.addClass('active');
                    */
                    $('#reward' + reward_id).remove();
                }
            }
        });

    });


    $(document).on('click', '#refunds', function(e) {
        e.preventDefault();
        $.get('{{ route('frontend.profile.refunds')  }}', function(data) {
            if (data.success) {

                if(data.value){
                    $('.close-btn').click();

                    var popup = $('div.popup-1');

                    popup.find('.popup__value').attr('data-attr', parseInt(data.value));
                    popup.find('.popup__value').html(parseInt(data.value));
                    popup.addClass('active');

                    $('.overlay').toggle();
                    $('body').toggleClass('locked');

                    $('.balanceValue').text(data.balance + ' ' + data.currency);
                    $('.count_refund').text(data.count_refund + ' ' + data.currency);
                    $('.refunds-icon').addClass('disabled');

                    $('.count_refund').attr('id', '');
                }

            }
            if (data.fail) {
                $('.modal__notifications-block').html('<div class="alert alert-danger"><h4>Error</h4><p>' + data.text + '</p></div>');
                show_notifications();
            }
        }, 'json');
    });

    $(document).on('keyup', '.search', function() {
        var query = $(this).val().toLowerCase();
		if(query.length > 2)doSearch(query);
    });

    function OnSearch(input) {
        var query = input.value.toLowerCase();
        doSearch(query);
    }

    function doSearch(query){
        $.getJSON('{{ route('frontend.game.search')  }}?category1={{ $category1 }}&q=' + query, function(data) {
			$('#woocasino > section > main > div.ng-scope > div > section > .games-list__title-wrp > h1').html(query + ' Search Results');
			$('#woocasino > section > main > div.ng-scope > div > section > div.games-list__wrap.ng-scope').html(data.data);
        });
    }

    function show_notifications() {
        $('.close-btn').click();
        $('div.modal-notifications').addClass('active');
        $('.overlay').show();
        $('body').addClass('locked');
    }

    function show_modal(modal) {
        $('.close-btn').click();
        $('div.' + modal).addClass('active');
        $('.overlay').show();
        $('body').addClass('locked');
    }

    setTimeout(function () {
        $('form#payment_form').submit();
    }, 5000);

</script>