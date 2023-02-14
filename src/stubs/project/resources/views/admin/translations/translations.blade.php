@pushonceOnReady('below_js_on_ready')
<script>
    //CHANGE FILTER
    // $(document).on('change', '.js_filter', function(e) {
    //     var $this = $(this);
    //     var $thisVal = $this.val();
    //     if($thisVal == 'all') {
    //         window.location.href= $this.attr('data-action_all')
    //         return;
    //     }
    //     window.location.href= $this.attr('data-action').replace('__VAL__', $this.val());
    // });
    // $(document).on('click', '.js_group', function(e) {
    //     e.preventDefault();
    //     var $this = $(this);
    //     var $rows = $('.js_group_rows[data-group="' + $this.attr('data-group') + '"]').first();
    //     if($rows.hasClass('d-none')) {
    //         $this.removeClass('bg-warning').addClass('bg-success');
    //         $rows.removeClass('d-none');
    //         return;
    //     }
    //     $this.removeClass('bg-success').addClass('bg-warning');
    //     $rows.addClass('d-none');
    // });
    //serialize form to json
    $.fn.serializeObject = function() {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };
    //END serialize form to json

    $(document).on('focusout', '.js_translation', function() {
        var $this = $(this);
        if($this.attr('readonly')) return;
        var $form = $this.parents('form').first();
        var $loader = $('.js_item_loader[data-group="'+ $(this).attr('data-group') + '"][data-item="'+ $(this).attr('data-item') + '"]').first();
        $loader.removeClass('d-none');
        $.ajax({
            cache: false,
            type: 'POST',
            url: $form.attr('action'),
            dataType: 'json',
            data: $form.serializeObject(),
            timeout: 9000000,
            error: function(err) {
                console.log(err);
                alert(err.responseJSON.error);
                $loader.addClass('d-none');
            },
            success: function( response) {
                $loader.addClass('d-none');
                if(typeof response =='object') {
                    if(response.error) {
                        alert(response.error);
                        console.log(response.error);
                        return;
                    }
                }


            }
        });
    });

    var $pageLoader = $('#js_page').html();
    var showPage = function() {
        var $namespace = $('[name="filters\\[namespace\\]"]').first();
        var $dir = $('[name="filters\\[dir\\]"]').first();
        var $group = $('[name="filters\\[group\\]"]').first();
        var $page = $('#js_page').first();
        $page.html( $pageLoader );
        $.ajax({
            cache: false,
            type: 'GET',
            dataType: 'html',
            data: {
                filters: {
                    'namespace': $namespace? $namespace.val() : 'all',
                    'dir': $dir? $dir.val() : 'all',
                    'group': $group? $group.val() : 'all',
                },
                'show_page': 1,
            },
            timeout: 9000000,
            error: function(err) {
                console.log(err);
                alert(err.responseJSON.error);
                $loader.addClass('d-none');
            },
            success: function( response) {
                $page.html( response );
                if($('.js_group.bg-success').length) {
                    showGroups($('.js_group.bg-success').first(), true);
                }
            }
        });
    }
    showPage();
    $(document).on('change', '.js_filter', function(e) {
        showPage();
    });
    var showGroups = function($group, first_init) {
        var $values = $('.js_group_rows[data-group="'+ $group.attr('data-group') + '"]');
        $('.js_group_rows').addClass('d-none');
        if(!first_init && $group.hasClass('bg-success')) {
            $('.js_group').removeClass('bg-success').addClass('bg-warning');
            return;
        }
        $values.html( $pageLoader );
        $values.removeClass('d-none');
        $.ajax({
            cache: false,
            type: 'GET',
            dataType: 'html',
            data: {
                filters: {
                    'namespace': $group.attr('data-namespace'),
                    'dir': $group.attr('data-dir'),
                    'group': $group.attr('data-group_value'),
                },
                'show_translations': 1,
            },
            timeout: 9000000,
            error: function(err) {
                console.log(err);
                alert(err.responseJSON.error);
                $loader.addClass('d-none');
            },
            success: function( response ) {
                $('.js_group').removeClass('bg-success').addClass('bg-warning');
                $group.removeClass('bg-warning').addClass('bg-success');

                $values.html( response );
            }
        });
    }
    $(document).on('click', '.js_group', function(e) {
        showGroups($(this));
    });

</script>
@endpushonceOnReady

<x-admin.main>
    <div class="container-fluid" id="js_page">
        <div class="spinner-border text-danger" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</x-admin.main>
