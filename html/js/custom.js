var editor; // use a global for the submit and return data rendering in the examples


$(document).ready(function () {



    //////// End datatables
    var log = function (line) {
        $('textarea#log').append(line + '\n');
        return true;
    };
    var clearResults = function () {
        $('textarea#results').val('');
        $('input#nextPageUrl').val('/page');
    };
    $('input#purge').click(function () {
        log('Purging all data.');
        $.ajax({
            url: '/purge',
            method: 'GET',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')
            },
            beforeSend: function () {

            },
            success: function (response) {
                if (true == response.status) {
                    window.location.reload();
                }
            },
            onerror: function (err) {
                if (err) {
                    log('Error:' + err);
                }
            }
        })
    });
    var n, spnProcessed = $('span#processed');
    var updateTotalCounter = function (newlyAddedRowsCount) {
        if (parseInt(spnProcessed.html()) !== newlyAddedRowsCount) {
            n = parseInt(newlyAddedRowsCount);
            if ('NaN' != spnProcessed) {
                spnProcessed.html(n);
            }

        }

    };


    var renderResults = function (timeOut) {

        setTimeout(function () {
            st.fetchData();
            renderResults(10000);
            /* $.ajax({
                 url: $('input#nextPageUrl').val(),
                 method: 'GET',
                 dataType: 'json',
                 headers: {
                     'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')
                 },
                 beforeSend: function () {

                 },
                 success: function (response) {

                     if (response.next_page_url) {
                         $('input#nextPageUrl').val(response.next_page_url);
                     }
                     renderResults();


                     if ($('input#currentPageUrl').val() != $('input#nextPageUrl').val()) {
                         $('input#currentPageUrl').val($('input#nextPageUrl').val());
                         updateTotalCounter(response.body);



                     }
                 },
                 onerror: function (err) {
                     if (err) {
                         log('Error:' + err);
                     }
                 }
             })*/
        }, timeOut);

    };

    renderResults(0);

    var sendAjax = function (url, callback, method, data) {
        data = data || {};
        method = method || 'GET';
        $.ajax({
            url: url,
            method: method,
            data: data,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')
            },
            success: function (response) {
                callback(response);
            }
        })
    };


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
    var loadingDiv = $('#loadingDiv');
    var successDiv = $('#scrapeCompleteDiv');
    var actionButtons = $('.actionsDiv');
    var i0 = 0, i1 = 0, i2 = 0;
    setInterval(function () {
        $.ajax({
            url: '/check_status',
            method: 'GET',
            data: {},
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')
            },
            success: function (response) {
                if (true === response.status) {
                    switch (response.body) {
                        case 0: // no search yet
                            i2 = 0;
                            i0++;
                            if (i0 >= 3) {
                                loadingDiv.hide();
                                successDiv.hide();
                                actionButtons.show();
                            }

                            break;
                        case 1: // search progress
                            i2 = 0;
                            i1++;
                            if (i1 >= 3) {

                                actionButtons.hide();
                                successDiv.hide();
                                loadingDiv.show();
                            }

                            break;
                        case 2: // search completed
                            i2++;
                            if (i2 >= 3) {
                                sendAjax('/set_last_scrape_completed',function (response) {
                                    // do nothing
                                });

                                loadingDiv.hide();
                                actionButtons.hide();
                                successDiv.show();
                            }

                            break;

                    }
                }

            }
        })
        ;
    }, 2500);

    $('#app').on('change keyup mouseup', '.form2Component', function () {


        $.ajax({
            url: $('form.form2').attr('action'),
            method: 'PUT',
            dataType: 'json',
            data: $('.form2Component').serialize(),
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')
            },
            beforeSend: function () {


            },
            success: function (response) {

            },
            onerror: function (err) {

            }
        })
        ;
    });

    $('textarea#keywords').keyup(function () {

    });

    function getDateTime() {
        var now = new Date();
        var year = now.getFullYear();
        var month = now.getMonth() + 1;
        var day = now.getDate();
        var hour = now.getHours();
        var minute = now.getMinutes();
        var second = now.getSeconds();
        if (month.toString().length == 1) {
            var month = '0' + month;
        }
        if (day.toString().length == 1) {
            var day = '0' + day;
        }
        if (hour.toString().length == 1) {
            var hour = '0' + hour;
        }
        if (minute.toString().length == 1) {
            var minute = '0' + minute;
        }
        if (second.toString().length == 1) {
            var second = '0' + second;
        }
        var dateTime = year + '/' + month + '/' + day + ' ' + hour + ':' + minute + ':' + second;
        return dateTime;
    }

    $("input#download").click(function () {
        window.open('/csv', '_blank');

        // window.location = '/csv';
    });
    $("input#scrape").click(function () {
        $('.actionsDiv').hide();
        $('#loadingDiv').show();
        var keywords = $('textarea#keywords').val().split('\n');

        $.ajax({
            url: '/keyword',
            method: 'POST',
            dataType: 'json',
            data: {
                'keywords': keywords
            },
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')
            },
            beforeSend: function () {
                $('.actionsDiv').hide();
                $('#loadingDiv').show();

            },
            success: function (response) {
                log('Processing your keywords');
            },
            onerror: function (err) {
                $('#loadingDiv').hide();
                $('.actionsDiv').show();

            }
        })
        ;
    });

});