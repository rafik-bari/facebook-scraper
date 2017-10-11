$(document).ready(function () {
    $('textarea#keywords').keyup(function () {
        console.log($(this).val(), $(this).val().trim().length);
        if ($(this).val().trim().length > 0) {
            $('input#scrape').prop('disabled', false);
        } else {
            $('input#scrape').prop('disabled', true);
        }
    });

    $.ajaxSetup({cache: true});
    $.getScript('//connect.facebook.net/en_US/sdk.js', function () {
        FB.init({
            appId: '1083006501758342',
            version: 'v2.7' // or v2.1, v2.2, v2.3, ...
        });


    });
    // Execute some code here
    var lines;
    var queueSpan = $('span#queue');
    var collectedSpan = $('span#processed');
    var textArrPendingIds = $('textarea#pendingIds');
    var processNextPage = function (url) {
        if ('undefined' !== typeof url) {
            var n = 0;
            var x = FB.api(url, 'get', {}, function (response) {
                $.each(response.data, function () {
                    n++;
                    textArrPendingIds.append(this.id + '\n');
                    queueSpan.val(parseInt(queueSpan.innerText) + 1);
                    if (n % 10) {
                        textArrPendingIds.change();
                    }
                });
                if ('undefined' !== typeof response.paging && 'undefined' !== typeof response.paging.next) {
                    processNextPage(response.paging.next);
                }

            });
        }
    };
    $("input#scrape").click(function () {

        var i = 0;
        if ('undefined' !== typeof FB) {
            lines = $('textarea#keywords').val().split('\n');

            $.each(lines, function () {
                i++;
                var x = FB.api('https://graph.facebook.com/search', 'get', {
                    q: this,
                    type: 'page',
                    limit: 10000
                }, function (response) {
                    $.each(response.data, function () {
                        textArrPendingIds.append(this.id + '\n');
                        if (i % 10) {
                            textArrPendingIds.change();
                        }
                        queueSpan.html(parseInt(queueSpan.html()) + 1);
                    });
                    if ('undefined' !== typeof response.paging && 'undefined' !== typeof response.paging.next) {

                        processNextPage(response.paging.next);
                    }

                });
            });

        }


        // process lines in queue


        var ids_lines;
        $('textarea#pendingIds').on("change input paste keyup", function () {

            if ('undefined' !== typeof FB) {
                ids_lines = textArrPendingIds.val().split('\n');
                textArrPendingIds.val(''); // reset textArea
                // psh all ids to queued jobs on backend
                $.ajax({
                    url: 'pendingPage',
                    method: 'POST',
                    data: {page_ids: JSON.stringify(ids_lines)},
                    headers: {
                        'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')
                    },
                    success: function (response) {
                        console.log(response);
                    }
                })

            }
        });


        if ('undefined' !== typeof FB) {

            var x = FB.api('/1377316249228679', 'get', {fields: 'name,id'}, function (response) {
                console.log(response);
            });

        }
    });
});