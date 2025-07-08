$(document).ready(function() {
    var config = window.meest2OrderFormUrls;
    $('#input-sender-city, #input-sender-address, #input-sender-city-address, #input-sender-branch').select2({
        placeholder: config.text_select,
        allowClear: true
    });

    function populateCities(region_id) {
        $.ajax({
            url: config.ajax_get_cities_url,
            type: 'get',
            data: { region_id: region_id },
            dataType: 'json',
            success: function(data) {
                var citySelect = $('#input-sender-city');
                citySelect.empty();
                citySelect.append('<option value="">' + config.text_select + '</option>');
                $.each(data, function(index, city) {
                    citySelect.append('<option value="' + city.city_id + '">' + city.name_ua + '</option>');
                });
                citySelect.val(config.shipping_meest2_sender_city).trigger('change');
            }
        });
    }

    function populateCitiesAddress(region_id) {
        $.ajax({
            url: config.ajax_get_cities_url,
            type: 'get',
            data: { region_id: region_id },
            dataType: 'json',
            success: function(data) {
                var citySelect = $('#input-sender-city-address');
                citySelect.empty();
                citySelect.append('<option value="">' + config.text_select + '</option>');
                $.each(data, function(index, city) {
                    citySelect.append('<option value="' + city.city_id + '">' + city.name_ua + '</option>');
                });
                citySelect.val(config.shipping_meest2_sender_city).trigger('change');
            }
        });
    }

    function populateAddresses(city_id) {
        $.ajax({
            url: config.ajax_get_addresses_url,
            type: 'get',
            data: { city_id: city_id },
            dataType: 'json',
            success: function(data) {
                var addressSelect = $('#input-sender-address');
                addressSelect.empty();
                addressSelect.append('<option value="">' + config.text_select + '</option>');
                $.each(data, function(index, address) {
                    addressSelect.append('<option value="' + address.street_id + '">' + address.type_ua + ' ' + address.name_ua + '</option>');
                });
                addressSelect.val(config.shipping_meest2_sender_address).trigger('change');
            }
        });
    }

    function populateBranches(city_id) {
        $.ajax({
            url: config.ajax_get_branches_url,
            type: 'get',
            data: { city_id: city_id },
            dataType: 'json',
            success: function(data) {
                var branchSelect = $('#input-sender-branch');
                branchSelect.empty();
                branchSelect.append('<option value="">' + config.text_select + '</option>');
                $.each(data, function(index, branch) {
                    branchSelect.append('<option value="' + branch.branch_id + '">' + branch.short_name + ' (' + branch.address_more_information + ') ' + '</option>');
                });
                branchSelect.select2({
                    placeholder: config.text_select,
                    allowClear: true,
                    width: '100%'
                }).val(config.shipping_meest2_sender_branch).trigger('change');
            }
        });
    }

    if ($('#input-sender-region').val()) {
        populateCities($('#input-sender-region').val());
    }

    if ($('#input-sender-region-address').val()) {
        populateCitiesAddress($('#input-sender-region-address').val());
    }

    if ($('#input-sender-city-address').val()) {
        populateAddresses($('#input-sender-city-address').val());
    }

    $('#input-sender-region').change(function() {
        var region_id = $(this).val();
        populateCities(region_id);
    });

    $('#input-sender-region-address').change(function() {
        var region_id = $(this).val();
        populateCitiesAddress(region_id);
    });

    $('#input-sender-city').change(function() {
        var city_id = $(this).val();
        populateBranches(city_id);
    });

    $('#input-sender-city-address').change(function() {
        var city_id = $(this).val();
        populateAddresses(city_id);
    });

    $('#input-recipient_city').select2({
        placeholder: config.text_select,
        allowClear: true
    });

    $('#input-recipient_city_address').select2({
        placeholder: config.text_select,
        allowClear: true
    });

    $('#input-recipient_branch').select2({
        placeholder: config.text_select,
        allowClear: true
    });

    function populateRecipientCities(region_id) {
        $.ajax({
            url: config.ajax_get_cities_url,
            type: 'get',
            data: { region_id: region_id },
            dataType: 'json',
            success: function(data) {
                var citySelect = $('#input-recipient_city');
                citySelect.empty();
                citySelect.append('<option value="">' + config.text_select + '</option>');
                $.each(data, function(index, city) {
                    citySelect.append('<option value="' + city.city_id + '">' + city.name_ua + '</option>');
                });
                citySelect.val(config.shipping_meest2_recipient_city).trigger('change');
            }
        });
    }

    function populateRecipientCitiesAddress(region_id) {
        $.ajax({
            url: config.ajax_get_cities_url,
            type: 'get',
            data: { region_id: region_id },
            dataType: 'json',
            success: function(data) {
                var citySelect = $('#input-recipient_city_address');
                citySelect.empty();
                citySelect.append('<option value="">' + config.text_select + '</option>');
                $.each(data, function(index, city) {
                    citySelect.append('<option value="' + city.city_id + '">' + city.name_ua + '</option>');
                });
                citySelect.val(config.shipping_meest2_recipient_city).trigger('change');
            }
        });
    }

    function populateRecipientBranches(city_id) {
        $.ajax({
            url: config.ajax_get_branches_url,
            type: 'get',
            data: { city_id: city_id },
            dataType: 'json',
            success: function(data) {
                var branchSelect = $('#input-recipient_branch');
                branchSelect.empty();
                branchSelect.append('<option value="">' + config.text_select + '</option>');
                $.each(data, function(index, branch) {
                    branchSelect.append('<option value="' + branch.branch_id + '">' + branch.short_name + ' (' + branch.address_more_information + ') ' + '</option>');
                });
            }
        });
    }

    function populateRecipientAddresses(city_id) {
        $.ajax({
            url: config.ajax_get_addresses_url,
            type: 'get',
            data: { city_id: city_id },
            dataType: 'json',
            success: function(data) {
                var addressSelect = $('#input-recipient-address');
                addressSelect.empty();
                addressSelect.append('<option value="">' + config.text_select + '</option>');
                $.each(data, function(index, address) {
                    addressSelect.append('<option value="' + address.street_id + '">' + address.type_ua + ' ' + address.name_ua + '</option>');
                });
                addressSelect.select2({
                    placeholder: config.text_select,
                    allowClear: true
                }).val(config.shipping_meest2_recipient_address).trigger('change');
            }
        });
    }

    $('#input-recipient_region').change(function() {
        var region_id = $(this).val();
        populateRecipientCities(region_id);
    });

    $('#input-recipient_region_address').change(function() {
        var region_id = $(this).val();
        populateRecipientCitiesAddress(region_id);
    });

    $('#input-recipient_city').change(function() {
        var city_id = $(this).val();
        populateRecipientBranches(city_id);
    });

    $('#input-recipient_city_address').change(function() {
        var city_id = $(this).val();
        populateRecipientAddresses(city_id);
    });

    if ($('#input-recipient_region').val()) {
        populateRecipientCities($('#input-recipient_region').val());
    }

    if ($('#input-recipient_region_address').val()) {
        populateRecipientCitiesAddress($('#input-recipient_region_address').val());
    }

    $('#input-departure_date').datepicker({
        format: 'dd.mm.yyyy',
        autoclose: true,
        todayHighlight: true
    });

    $('.input-group-btn button').click(function() {
        $('#input-departure_date').datepicker('show');
    });
// });

// $(document).ready(function() {
    $('input[name="recipient_address_type"]').change(function() {
        var selectedType = $(this).val();
        $('#recipient_delivery_type').val(selectedType);
        $('#recipient_branch_delivery').hide();
        $('#recipient_address_delivery').hide();
        if (selectedType === 'branch') {
            $('#recipient_branch_delivery').show();
        } else if (selectedType === 'doors') {
            $('#recipient_address_delivery').show();
        }
    });

    $('input[name="sender_address_type"]').change(function() {
        var selectedType = $(this).val();
        $('#sender_delivery_type').val(selectedType);
        $('#sender_branch_delivery').hide();
        $('#sender_address_delivery').hide();
        if (selectedType === 'branch') {
            $('#sender_branch_delivery').show();
        } else if (selectedType === 'doors') {
            $('#sender_address_delivery').show();
        }
    });

    $('#toggle-cod').change(function() {
        if ($(this).is(':checked')) {
            $('.cod-amount-container').slideDown();
        } else {
            $('.cod-amount-container').slideUp();
        }
    });
// });

let placeIndex = 1;
var textSelect = config.text_select;
document.getElementById('add-place').addEventListener('click', function() {
    const container = document.getElementById('places-container');
    const newPlace = document.createElement('div');
    newPlace.classList.add('place-item');
    newPlace.dataset.index = placeIndex;
    newPlace.innerHTML = `
        <legend class="seat-legend">${config.text_seat}</legend>
        <div class="form-group">
            <label class="col-sm-3 control-label">${config.entry_weight}</label>
            <div class="col-sm-9">
                <div class="input-group input-group-70">
                    <input type="text" step="0.01" name="places[${placeIndex}][weight]" value="" placeholder="${config.entry_weight}" class="custom-input-70" required/>
                    <span class="input-group-addon addon-blue">${config.text_kg}</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">${config.entry_length}</label>
            <div class="col-sm-9">
                <div class="input-group input-group-70">
                    <input type="text" step="0.01" name="places[${placeIndex}][length]" value="" placeholder="${config.entry_length}" class="custom-input-70" required/>
                    <span class="input-group-addon addon-blue">${config.text_cm}</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">${config.entry_width}</label>
            <div class="col-sm-9">
                <div class="input-group input-group-70">
                    <input type="text" step="0.01" name="places[${placeIndex}][width]" value="" placeholder="${config.entry_width}" class="custom-input-70" required/>
                    <span class="input-group-addon addon-blue">${config.text_cm}</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">${config.entry_height}</label>
            <div class="col-sm-9">
                <div class="input-group input-group-70">
                    <input type="text" step="0.01" name="places[${placeIndex}][height]" value="" placeholder="${config.entry_height}" class="custom-input-70" required/>
                    <span class="input-group-addon addon-blue">${config.text_cm}</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">${config.entry_quantity}</label>
            <div class="col-sm-9">
                <div class="input-group input-group-70">
                    <input type="text" step="1" name="places[${placeIndex}][quantity]" value="" placeholder="${config.entry_quantity}" class="custom-input-70" required/>
                    <span class="input-group-addon addon-blue">${config.text_pc}</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">${config.entry_insurance}</label>
            <div class="col-sm-9">
                <div class="input-group input-group-70">
                    <input type="text" step="0.01" name="places[${placeIndex}][insurance]" value="" placeholder="${config.entry_insurance}" class="custom-input-70" required/>
                    <span class="input-group-addon addon-blue">${config.text_uah}</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-9 col-sm-offset-3 text-right">
                <button type="button" class="btn btn-danger remove-place pull-right">${config.text_delete_seat}</button>
            </div>
        </div>
    `;
    container.appendChild(newPlace);
    if (container.children.length > 1) {
        const removeButtons = document.querySelectorAll('.remove-place');
        removeButtons.forEach(button => {
            button.style.display = 'inline-block';
        });
    }
    placeIndex++;
});

document.getElementById('places-container').addEventListener('click', function(event) {
    if (event.target.classList.contains('remove-place')) {
        const placeItem = event.target.closest('.place-item');
        placeItem.remove();
        if (this.children.length === 1) {
            const removeButtons = document.querySelectorAll('.remove-place');
            removeButtons.forEach(button => {
                button.style.display = 'none';
            });
        }
    }
});

// $(document).ready(function() {
    $('#form-meest-cn').on('submit', function(event) {
        let isValid = true;
        $('#form-meest-cn input, #form-meest-cn select').each(function() {
            const skipIds = [
                'input-parcel-number',
                'input-recipient-building-address',
                'input-recipient-floor-address',
                'input-recipient-apartment-address',
                'input-sender-building',
                'input-sender-floor',
                'input-sender-apartment',
                'input-card-number',
                'input-ownername',
                'input-ownermobile'
            ];

            if (skipIds.includes(this.id)) {
                return;
            }

            // if ($(this).attr('id') === 'input-parcel-number') {
            //     return;
            // }
            if ($(this).is(':visible') && ($(this).val() === '' || $(this).val() === null)) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        if ($('input[name="sender_address_type"]:checked').val() === 'doors' && $('#input-sender-address').val() === '') {
            $('#input-sender-address').addClass('is-invalid');
            isValid = false;
        }
        if ($('input[name="recipient_address_type"]:checked').val() === 'doors' && $('#input-recipient-address').val() === '') {
            $('#input-recipient-address').addClass('is-invalid');
            isValid = false;
        }
        if (!isValid) {
            event.preventDefault();
            alert(config.text_fill_required_fields);
        }
    });
    $('#form-meest-cn input, #form-meest-cn select').on('input change', function() {
        if ($(this).val() !== '' && $(this).val() !== null) {
            $(this).removeClass('is-invalid');
        }
    });
    $('#input-recipient_contact_person_phone').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 12) {
            this.value = this.value.slice(0, 12);
        }
    });
    $('#input-recipient_contact_person_phone_address').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 12) {
            this.value = this.value.slice(0, 12);
        }
    });
// });

});
