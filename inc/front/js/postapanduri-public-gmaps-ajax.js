(function ($) {
    const {__, _x, _n, _nx} = wp.i18n;
    'use strict';

    function pp_render_map() {
        $.confirm({
            useBootstrap: false,
            confirmButtonClass: 'btn btn-success',
            cancelButtonClass: 'btn btn-danger',
            boxWidth: '90%',
            draggable: false,
            titleClass: 'title-lo',
            type: 'orange',
            title: __('Choose a pickup point', 'postapanduri'),
            buttons: {
                cancel: {
                    text: __('Close window', 'postapanduri'),
                    btnClass: 'btn-red btn-lo-left',
                },
                /*ok_default: {
                    text: __('Confirm and set as favorite', 'postapanduri'),
                    btnClass: 'btn-green btn-lo-right',
                    action: function () {
                        $.ajax({
                            type: "POST",
                            dataType: 'json',
                            url: ppa.ajaxurl,
                            method: 'post',
                            data: {
                                'action': 'ajax_set_pachetomat_default_lo',
                                'dulapid': last_dp_id,
                            },
                            beforeSend: function () {
                                set_page_loading(__('Please wait...', 'postapanduri'));
                            },
                            success: function () {
                                unset_page_loading();
                            },
                            error: function () {
                                unset_page_loading();
                            }
                        });
                    }
                },*/
                ok: {
                    text: __('Confirm pickup point', 'postapanduri'),
                    btnClass: 'btn-orange btn-lo-right'
                }
            },
            content: function () {
                let self = this;
                return $.ajax({
                    url: ppa.ajaxurl,
                    dataType: 'html',
                    method: 'post',
                    data: {
                        'action': 'ajax_load_map',
                    },
                }).done(function (response) {
                    self.setContentAppend(response);
                }).fail(function () {
                }).always(function () {
                });
            },
            contentLoaded: function () {
                $('.jconfirm-buttons > .btn-lo-right').prop('disabled', 'disabled');
            },
            onContentReady: function () {
                $('.pp-select2').select2({
                    dropdownCssClass: "pp-increasedzindexclass",
                    width: "100%"
                });

                let map = new GMaps({
                    div: '#pp-map-canvas',
                    lat: 46.203567,
                    lng: 25.003274,
                    zoom: 15,
                    disableDefaultUI: true
                });

                let mapToggle = document.getElementsByClassName('pp-map-toggle')[0];
                let mapWrap = document.getElementsByClassName('pp-map-wrap')[0];
                mapToggle.onclick = function () {
                    if (mapWrap.offsetHeight === 0) {
                        mapWrap.style.height = 'auto';
                    } else {
                        mapWrap.style.height = '0';
                    }
                };

                const $judete = $('#judete');
                const $localitati = $('#localitati');
                const $pachetomate = $('#pachetomate');
                const $dp_type = $('input.dp-type');

                $dp_type.on('change', function (e, data) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (!$(this).is(':checked')) {
                        return
                    }
                    let dp_tip = $(this).val();
                    $.ajax({
                        type: "POST",
                        dataType: 'json',
                        url: ppa.ajaxurl,
                        method: 'post',
                        data: {
                            'action': 'ajax_get_judete',
                            'dp_tip': dp_tip,
                        },
                        beforeSend: function () {
                            set_page_loading(__('Please wait...', 'postapanduri'));
                            $localitati.find('option').remove().end();
                            $pachetomate.find('option').remove().end();
                            $localitati.closest('.pp-form-group').hide();
                            $pachetomate.closest('.pp-form-group').hide();
                            $('.jconfirm-buttons > .btn-lo-right').prop('disabled', 'disabled');
                        },
                        success: function (response) {
                            let judete = response.judete;
                            $judete.find('option').remove().end();
                            $judete.append($('<option>', {
                                value: 0,
                                text: __('Choose county', 'postapanduri'),
                                disabled: true,
                                selected: true
                            }));

                            $.each(judete, function (index, value) {
                                $judete.append($('<option>', {
                                    value: value.dp_judet,
                                    text: value.dp_judet,
                                }));
                            });
                            let judet_selectat;
                            if (typeof data !== 'undefined' && typeof data.preselected_county !== 'undefined') {
                                judet_selectat = data.preselected_county;
                            } else {
                                judet_selectat = response.judet_selectat;
                            }

                            if (judet_selectat) {
                                if (typeof $('[value="' + judet_selectat + '"]', $judete) !== 'undefined') {
                                    let exists = 0 != $('[value="' + judet_selectat + '"]', $judete).length;
                                    if (exists) {
                                        $judete.val(judet_selectat);
                                        $judete.trigger('change', data);
                                    } else {
                                        $('option', $judete).eq(0).prop('selected', true);
                                    }
                                } else {
                                    $('option', $judete).eq(0).prop('selected', true);
                                }
                            }

                            if (!data) {
                                plotMarkers(response.pachetomate);
                            }
                            unset_page_loading();
                        },
                        error: function () {
                            unset_page_loading();
                        }
                    });
                });

                $judete.on('change', function (e, data) {
                    e.preventDefault();
                    e.stopPropagation();
                    let js = $('option:selected', this).val();
                    let dp_tip = $('.dp-type:checked', '#pp-types').val();
                    if (js != "") {
                        $('.pp-panel__body').show();
                        $.ajax({
                            type: "POST",
                            dataType: 'json',
                            url: ppa.ajaxurl,
                            method: 'post',
                            data: {
                                'action': 'ajax_get_localitati',
                                'judet': js,
                                'dp_tip': dp_tip,
                            },
                            beforeSend: function () {
                                set_page_loading(__('Please wait...', 'postapanduri'));
                                $judete.closest('li').find('input.shipping_method').attr('checked', true);
                                $localitati.find('option').remove().end();
                                $pachetomate.find('option').remove().end();
                                $localitati.closest('.pp-form-group').hide();
                                $pachetomate.closest('.pp-form-group').hide();
                                $('.jconfirm-buttons > .btn-lo-right').prop('disabled', 'disabled');
                            },
                            success: function (response) {
                                let localitati = response.localitati;
                                let pachetomate = response.pachetomate;
                                let localitate_selectata = response.localitate_selectata;

                                $localitati.append($('<option>', {
                                    value: 0,
                                    text: __('Choose city', 'postapanduri'),
                                    disabled: true,
                                    selected: true
                                }));
                                $pachetomate.append($('<option>', {
                                    value: 0,
                                    text: __('Choose pickup point', 'postapanduri'),
                                    disabled: true,
                                    selected: true
                                }));
                                $.each(localitati, function (index, value) {
                                    $localitati.append($('<option>', {
                                        value: value.dp_oras,
                                        text: value.dp_oras
                                    }));
                                });

                                if (localitati.length === 1) {
                                    $('#localitati option').eq(1).prop('selected', true);
                                    $localitati.trigger('change');
                                } else if (js === response.judet_selectat && localitate_selectata) {
                                    if (typeof $('[value="' + localitate_selectata + '"]', $localitati) !== 'undefined') {
                                        let exists = 0 != $('[value="' + localitate_selectata + '"]', $localitati).length;
                                        if (exists) {
                                            $localitati.val(localitate_selectata);
                                            $localitati.trigger('change', data);
                                        } else {
                                            $('option', $localitati).eq(0).prop('selected', true);
                                        }
                                    } else {
                                        $('option', $localitati).eq(0).prop('selected', true);
                                    }
                                }

                                if (!data) {
                                    plotMarkers(pachetomate);
                                }
                                $localitati.closest('.pp-form-group').show();
                                unset_page_loading();
                            },
                            error: function () {
                                unset_page_loading();
                            }
                        });
                    } else {
                        $localitati.closest('.pp-form-group').hide();
                        $pachetomate.closest('.pp-form-group').hide();
                    }
                });

                $localitati.on('change', function (e, data) {
                    e.preventDefault();
                    e.stopPropagation();
                    let os = $('option:selected', this).val();
                    let dp_tip = $('.dp-type:checked', '#pp-types').val();
                    if (os != "") {
                        $.ajax({
                            type: "POST",
                            dataType: 'json',
                            url: ppa.ajaxurl,
                            method: 'post',
                            data: {
                                'action': 'ajax_get_pachetomate',
                                'localitate': os,
                                'judet': $('option:selected', $judete).val(),
                                'dp_tip': dp_tip,
                            },
                            beforeSend: function () {
                                set_page_loading(__('Please wait...', 'postapanduri'));
                                $pachetomate.find('option').remove().end();
                                $pachetomate.closest('.pp-form-group').hide();
                                $('.jconfirm-buttons > .btn-lo-right').prop('disabled', 'disabled');
                            },
                            success: function (response) {
                                let pachetomate = response.pachetomate;
                                let pachetomat_selectat = response.pachetomat_selectat;

                                $pachetomate.append($('<option>', {
                                    value: 0,
                                    text: __('Choose pickup point', 'postapanduri'),
                                    disabled: true,
                                    selected: true
                                }));
                                $.each(pachetomate, function (index, value) {
                                    let p = $('<option>', {
                                        value: value.dp_id,
                                        text: value.dp_denumire + (value.dp_active == 10 ? ' - ' + __('Pickup point full', 'postapanduri') : '')
                                    });
                                    if (value.dp_active == 10) {
                                        p.prop('disabled', true);
                                    }
                                    $pachetomate.append(p);
                                });

                                if (pachetomate.length === 1 && !(os === response.localitate_selectata && pachetomat_selectat)) {
                                    if (!$('#pachetomate option').eq(1).prop('disabled')) {
                                        $('#pachetomate option').eq(1).prop('selected', true);
                                        $pachetomate.trigger('change', data);
                                    }
                                } else if (os === response.localitate_selectata && pachetomat_selectat) {
                                    if (typeof $('[value="' + pachetomat_selectat + '"]', $pachetomate) !== 'undefined') {
                                        let exists = 0 != $('[value="' + pachetomat_selectat + '"]', $pachetomate).length;
                                        if (exists) {
                                            $pachetomate.val(pachetomat_selectat);
                                            $pachetomate.trigger('change');
                                        } else {
                                            $('option', $pachetomate).eq(0).prop('selected', true);
                                        }
                                    } else {
                                        $('option', $pachetomate).eq(0).prop('selected', true);
                                    }
                                }
                                if (!data) {
                                    plotMarkers(pachetomate);
                                }
                                $pachetomate.closest('.pp-form-group').show();
                                unset_page_loading();
                            },
                            error: function () {
                                unset_page_loading();
                            }
                        });
                    } else {
                        $pachetomate.closest('.pp-form-group').hide();
                    }
                });

                $pachetomate.on('change', function (e, data) {
                    e.preventDefault();
                    e.stopPropagation();
                    let id = $('option:selected', this).val();
                    if (id > 0) {
                        $.ajax({
                            type: "POST",
                            dataType: 'json',
                            url: ppa.ajaxurl,
                            method: 'post',
                            data: {
                                'action': 'ajax_get_pachetomat',
                                'pachetomat': id
                            },
                            beforeSend: function () {
                                set_page_loading(__('Please wait...', 'postapanduri'));
                            },
                            success: function (response) {
                                $('body').trigger('update_checkout');
                                showMarkerDetails(response.pachetomat.dp_id);
                                $('.jconfirm-buttons > .btn-lo-right').prop('disabled', '');
                                last_dp_id = response.pachetomat_selectat;
                                $('#pp-selected-dp-map').text(__('Change pickup point', 'postapanduri'));
                                $('#pp-selected-dp-text').html(__('The parcel will be delivered at', 'postapanduri') + ' <b>' + response.tip_selectat_text + ' - ' + response.selected_name + '</b>');
                                unset_page_loading();
                            },
                            error: function () {
                                unset_page_loading();
                            }
                        });
                    }
                });

                function plotMarkers(m) {
                    if (m) {
                        map.removeMarkers();
                        let markers_data = [];
                        m.forEach(function (marker) {
                            let temperatura = '';
                            if (typeof marker.dp_temperatura !== 'undefined' && marker.dp_temperatura) {
                                temperatura = '<p><b>' + __('Temperature', 'postapanduri') + ':</b> ' + marker.dp_temperatura.split('.')[0] + '<sup>o</sup>C</p>';
                            }
                            markers_data.push({
                                id: marker.dp_id,
                                lat: parseFloat(marker.dp_gps_lat),
                                lng: parseFloat(marker.dp_gps_long),
                                run_ajax: true,
                                click: function (t) {
                                    let id = t.id;
                                    if (id > 0 && t.run_ajax && marker.dp_active > 0 && marker.dp_active != 10) {
                                        $.ajax({
                                            type: "POST",
                                            dataType: 'json',
                                            url: ppa.ajaxurl,
                                            method: 'post',
                                            data: {
                                                'action': 'ajax_get_pachetomat',
                                                'pachetomat': id
                                            },
                                            beforeSend: function () {
                                                set_page_loading(__('Please wait...', 'postapanduri'));
                                            },
                                            success: function (response) {
                                                $('.dp-type[value="' + response.pachetomat.dp_tip + '"]', '#pp-types').trigger('change', {
                                                    'preselected_county': response.pachetomat.dp_judet,
                                                    'preselected_state': response.pachetomat.dp_oras,
                                                    'preselected_dp_id': response.pachetomat.dp_id,
                                                    'preselected_dp_tip': response.pachetomat.dp_tip
                                                });
                                                showMarkerDetails(response.pachetomat.pachetomat_id);
                                                $('.jconfirm-buttons > .btn-lo-right').prop('disabled', '');
                                                unset_page_loading();
                                            },
                                            error: function () {
                                                unset_page_loading();
                                            }
                                        });
                                    } else {
                                        t.run_ajax = true;
                                    }
                                },
                                title: marker.dp_denumire,
                                icon: {
                                    size: new google.maps.Size(30, 30),
                                    url: marker.dp_tip == 0 ? icon_posta : icon
                                },
                                infoWindow: {
                                    content: '<div class="pp-map__infowindow infowindow"><div class="infowindow-header"><div class="infowindow-body"><h3 class="infowindow-title">' + marker.dp_denumire + '</h3><p>' + marker.dp_adresa + ', ' + marker.dp_oras + ', ' + marker.dp_judet + (marker.dp_indicatii && marker.dp_indicatii != '-' ? ' (' + marker.dp_indicatii + ')' : '') + '</p><hr class="hr--dashed" />' + temperatura + (marker.orar ? (__('Schedule', 'postapanduri') + marker.orar + '</div>') : '')
                                }
                            });
                        });
                        map.addMarkers(markers_data);
                        map.fitZoom();

                        if (map.getZoom() > 15) {
                            map.setZoom(15);
                        }
                    }
                }

                function showMarkerDetails(id) {
                    map.markers.forEach(function (value, index) {
                        if (value.id == id) {
                            let position = map.markers[index].getPosition();
                            let lat = position.lat();
                            let lng = position.lng();
                            map.setCenter(lat, lng);
                            map.setZoom(15);
                            map.markers[index].run_ajax = false;
                            google.maps.event.trigger(map.markers[index], 'click');
                        }
                    });
                }

                $('.dp-type:checked', '#pp-types').trigger("change");
            }
        });
    }

    $('body').on('click', '#pp-selected-dp-map', function (e) {
        e.preventDefault();
        e.stopPropagation();
        pp_render_map();
    });

    $('body').on('updated_checkout', function () {
        let sm = '';
        if ($('input.shipping_method').length == 1) {
            sm = $('input[type="hidden"].shipping_method', '#order_review').val();
        } else {
            sm = $('input.shipping_method:checked', '#order_review').val();
        }
        if (typeof sm !== 'undefined') {
            sm = sm.split('_')[0];
        }

        if (sm === 'pachetomat') {
            // verific daca am pachetomat default
            let postapanduri = null;
            try {
                postapanduri = document.cookie
                    .split('; ')
                    .find(row => row.startsWith('postapanduri='))
                    .split('=')[1];
            } catch (error) {
                postapanduri = null;
            }

            let postapanduri_default_dp = -1;
            if (postapanduri) {
                try {
                    postapanduri_default_dp = $.parseJSON(decodeURIComponent(postapanduri)).default_dpid;
                } catch (error) {
                    postapanduri_default_dp = -1;
                }
            }

            if (postapanduri_default_dp > 0 && !last_dp_id) {
                // am pachetomat default si nu am selectat un pachetomat
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: ppa.ajaxurl,
                    method: 'post',
                    data: {
                        'action': 'ajax_set_pachetomat_default',
                        'pachetomat': postapanduri_default_dp
                    },
                    beforeSend: function () {
                        set_page_loading(__('Please wait...', 'postapanduri'));
                    },
                    success: function (response) {
                        if (typeof response.pachetomat_selectat !== 'undefined') {
                            $('body').trigger('update_checkout');
                            last_dp_id = response.pachetomat_selectat;
                            $('#pp-selected-dp-map').text(__('Change pickup point', 'postapanduri'));
                            $('#pp-selected-dp-text').html(__('The parcel will be delivered at', 'postapanduri') + ' <b>' + response.tip_selectat_text + ' - ' + response.selected_name + '</b>');
                        }
                        unset_page_loading();
                    },
                    error: function () {
                        unset_page_loading();
                    }
                });
            } else {
                if (typeof last_dp_id === 'undefined' || !last_dp_id) {
                    $('#pp-selected-dp-map').text(__('Choose pickup point', 'postapanduri'));
                    pp_render_map();
                } else {
                    $('#pp-selected-dp-text').html(__('The parcel will be delivered at', 'postapanduri') + ' <b>' + dp_type_text + ' - ' + last_dp_name + '</b>');
                }
            }
        }
    });

    function set_page_loading(e) {
        if (e) $("#pl-msg").text(e); else $("#pl-msg").text(__('Please wait...', 'postapanduri'));
        $("#page-loading").removeClass().addClass("visible")
    }

    function unset_page_loading() {
        $("#page-loading").removeClass().addClass("hidden")
    }
})(jQuery);
