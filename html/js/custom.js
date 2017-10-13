$(document).ready(function () {

    $('#app').on('change', 'input.update-field-value', function () {
        var field_id = $(this).val();
        $.ajax({
            url: '/fields/' + field_id,
            method: 'PUT',
            data: {
                'enabled': $(this).prop('checked') ? 1 : 0
            },
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')
            },
            success: function (response) {
                console.log(response);
            }
        })
        ;
    });


    $('textarea#keywords').keyup(function () {

        if ($(this).val().trim().length > 0) {
            $('input#scrape').prop('disabled', false);
        } else {
            $('input#scrape').prop('disabled', true);
        }
    });

    $.ajaxSetup({cache: false});
    $.getScript('//connect.facebook.net/en_US/sdk.js', function () {
        FB.init({
            appId: '1083006501758342',
            version: 'v2.7' // or v2.1, v2.2, v2.3, ...
        });


    });
    // Execute some code here
    var lines;

    var collectedSpan = $('span#processed');

    var textArrPendingIds = $('textarea#pendingIds');
    var processNextPage = function (url) {
        if ('undefined' !== typeof url) {
            var n = 0;
            var x = FB.api(url, 'get', {}, function (response) {
                $.each(response.data, function () {
                    n++;


                    pushPendingIdsToQueue(this.id);
                });
                if ('undefined' !== typeof response.paging && 'undefined' !== typeof response.paging.next) {
                    processNextPage(response.paging.next);
                }

            });
        }
    };
    $("input#scrape").click(function () {
        $.ajax({
            url: '/keyword',
            method: 'POST',
            dataType:'json',
            data: {
                'keywords': $('textarea#keywords').val().split('\n')
            },
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')
            },
            success: function (response) {
                console.log(response);
            }
        })
        ;
    });
});