(function($) {

    /** 
     * With theses two functions, we can get and set a LocalStorage with an expiration date. 
     * If the time is expired or the LS key is not set, it return false. If the value to set/get is an array, he will stringify/json parse him
     */
    function setWithExpiry(key, value, ttl) {

        if (typeof value === 'object') {
            value = JSON.stringify(value);
        }

        var now = new Date();

        var item = {
            value: value,
            expiry: now.getTime() + ttl
        };

        localStorage.setItem(key, JSON.stringify(item));
    }

    function getWithExpiry(key) {
        var itemStr = localStorage.getItem(key);

        if (!itemStr) {
            return null;
        }
        var item = JSON.parse(itemStr);
        var now = new Date();

        if (now.getTime() > item.expiry) {
            localStorage.removeItem(key);
            return null;
        }

        var value_parsed = jQuery.parseJSON(item.value);

        if (typeof value_parsed === 'object') {
            return value_parsed;
        } else {
            return item.value;
        }

    }



    function init_fuse(search_index, options, fuse_index) {

        fuse = new Fuse(search_index, options, fuse_index);

        function search_with_fuse(event) {
            var autocomplete_bloc = $(event.target).closest('.wrapper-search-autocomplete').find('.autocomplete');
            var result = fuse.search($(this).val());

            var url = location.protocol + "//" + location.host;
            var id = 1;
            var search = {
                "URL": url + "/?s=" + $(this).val(),
                "Title": search_options.search_label + ' : ' + $(this).val(),
                "ID": 0,
                "Selected": 'selected'
            };

            if ('' === $(this).val()) {
                result = [];
            }

            if (0 === result.length) {
                // Hide results li if no result
                autocomplete_bloc.empty();
                autocomplete_bloc.hide();
            } else {
                // display result (erase and rebuild the list)
                autocomplete_bloc.empty();
                autocomplete_bloc.hide();
                autocomplete_bloc.append(window.autocomplete_item(search));
                $('#search-item-0').addClass('selected');
                for (var key in result.slice(0, 5)) {

                    if (typeof(result[key].item.Url_a_lier) !== "undefined" && result[key].item.Url_a_lier !== null && result[key].item.Url_a_lier.length > 0) {
                        result_url = result[key].item.Url_a_lier;
                    } else {
                        result_url = url + "/?page_id=" + result[key].item.ID;
                    }

                    data = {
                        URL: result_url,
                        Title: result[key].item.Title,
                        ID: id,
                        Selected: ''
                    };

                    window.li_last = 'search-item-' + id;
                    id = id + 1;

                    autocomplete_bloc.append(window.autocomplete_item(data));
                }
                autocomplete_bloc.show();
            }
        }

        /* Launch the fuse search on user actions */
        window.input_search.on('keyup', { data: $(this) }, search_with_fuse);
        window.input_search.on('click', { data: $(this) }, search_with_fuse);
    }

    $(document).ready(function() {
        var options, fuse_index, ls_search, ls_autocomplete_search_options;

        window.input_search = $('.wrapper-search-autocomplete #search');

        var autocomplete_results = '<ul class="autocomplete search-results__group__items" style="display: none;"></ul>';
        $('.wrapper-search-autocomplete').append(autocomplete_results);

        window.autocomplete_bloc = $('.wrapper-search-autocomplete .autocomplete');
        window.autocomplete_item = wp.template('autocomplete-item');

        window.input_search.attr('autocomplete', 'off');

        ls_search = getWithExpiry('searchindex_' + search_options.language);
        ls_autocomplete_search_options = getWithExpiry('autocomplete_search_options_' + search_options.language);


        /** If the index exist in LocalStorage AND if the saved Timestamp in LocalStorage is the same as the server one  AND if the secret key is the same or defined, so we use the LS. Otherwise we get the index, build it and save it */
        if (null !== ls_autocomplete_search_options && null !== ls_search && search_options.autocomplete_search_version === ls_autocomplete_search_options.saved_version && search_options.secret_key === ls_autocomplete_search_options.secret_key) {
            parsed_fuse_index = Fuse.parseIndex(ls_search.fuse_index); //fuse need to parse his built index in order to use it
            init_fuse(ls_search.search_index, ls_search.options, parsed_fuse_index);
        } else {

            /** We build the endpoint with the lang parameter */
            var endpoint = '/wp-json/search/index/' + search_options.language;


            /** If the Secret key is defined, we add it in the endpoint in order to receive the full index */
            if (0 !== search_options.secret_key.length) {
                endpoint = endpoint + '/' + search_options.secret_key + '/';
            }


            window.jQuery.getJSON(endpoint, function(search_index) {

                // all options here https://fusejs.io/api/options.html 
                options = {
                    keys: [{
                            name: "Title",
                            weight: 0.50,
                        },
                        {
                            name: "Tags",
                            weight: 0.10,
                        },
                        {
                            name: "ManualTags",
                            weight: 0.60,
                        }
                    ],
                    minMatchCharLength: 2,
                    threshold: 0.2,
                    distance: 500
                };

                //We build the fuse index to improve performance
                fuse_index = Fuse.createIndex(options.keys, search_index);

                ls_search = {
                    'search_index': search_index,
                    'options': options,
                    'fuse_index': fuse_index,
                };

                ls_search_options = {
                    'saved_version': search_options.autocomplete_search_version,
                    'secret_key': search_options.secret_key,
                };
                //Set the index, the options and the fuse index built
                setWithExpiry('searchindex_' + search_options.language, ls_search, 604800000); /* expire in one week */
                //Set the index timestamp (in order to compare it if the server's index have been updated since)
                setWithExpiry('autocomplete_search_options_' + search_options.language, ls_search_options, 604800000); /* expire in one week */

                init_fuse(ls_search.search_index, ls_search.options, ls_search.fuse_index);
            });
        }


        /* Hide sugestion on click outside */
        $(document).mouseup(function(e) {
            if (!window.autocomplete_bloc.is(e.target) && window.autocomplete_bloc.has(e.target).length === 0) {
                window.autocomplete_bloc.hide();
            }
        });


        /* New search button on search page open sugestions */
        $('#new-search').click(function() {
            window.input_search.val($(this).data('search'));
            window.input_search.click();
            window.input_search.select();

        });

        /* Prevent submit empty search */
        $('form.search-form.form-inline').submit(function() {
            if ($.trim(window.input_search.val()) === "") {
                return false;
            }
        });



    });



    /**
     * Accessibility: This ugly part manage the keyboard navigation
     */
    var li_first = 'search-item-0';
    var liSelected = li_first;

    $(window).on('keyup', function(e) {

        if (e.which === 40 || e.which === 39) {
            $('#' + li_first).removeClass('selected');
            if (liSelected) {
                liSelected = $('#' + liSelected);
                liSelected.removeClass('selected');
                next = liSelected.next();
                if (next.length > 0) {
                    next.addClass('selected');
                    liSelected = next.attr('id');
                } else {
                    $('#' + li_first).addClass('selected');
                    liSelected = li_first;
                }
            } else {
                $('#' + li_first).addClass('selected');
                liSelected = li_first;
            }
        } else if (e.which === 38 || e.which === 37) {
            $('#' + li_first).removeClass('selected');
            if (liSelected) {
                liSelected = $('#' + liSelected);
                liSelected.removeClass('selected');
                next = liSelected.prev();
                if (next.length > 0) {
                    next.addClass('selected');
                    liSelected = next.attr('id');
                } else {
                    $('#' + window.li_last).addClass('selected');
                    liSelected = window.li_last;
                }
            } else {
                $('#' + window.li_last).addClass('selected');
                liSelected = window.li_last;
            }
        } else if (e.which === 13) {

            var selected = $('#' + liSelected);

            if (li_first !== liSelected) {
                $('#' + li_first).removeClass('selected');
                $('#' + liSelected).addClass('selected');
                $('.autocomplete').hide();

                e.preventDefault();
                window.input_search.val(selected.children(0).text());
                $('#' + liSelected).children(0)[0].click();
            }
        }
    });




})(jQuery);