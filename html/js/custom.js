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
                    log('Data was successfully purged');
                    clearResults();
                }
            },
            onerror: function (err) {
                if (err) {
                    log('Error:' + err);
                }
            }
        })
    });
    var spnProcessed = $('span#processed');
    var updateTotalCounter = function (newlyAddedRowsCount) {
        if (parseInt(spnProcessed.html()) !== newlyAddedRowsCount) {
            spnProcessed.html(parseInt(newlyAddedRowsCount));
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
        var keywords = $('textarea#keywords').val().split('\n');
        log('Scraping Started: ' + getDateTime());
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
                log('Sending keywords to server');

            },
            success: function (response) {
                $('input#scrape').prop('disabled', false);
                log('Processing your keywords');
            },
            onerror: function (err) {
                console.log(err);
                log('Error:' + err);
            }
        })
        ;
    });

});