/**
 * Created by Spencer on 7/16/2018.
 * V2.2.5
 */

jQuery(document).ready(function() {
    /*** CHECK IF SSL ENABLED ***/
    var protocol = document.location.protocol;

    if(protocol !== 'https:') {
        jQuery('.cm-hipaa-tabs-wrapper').prepend('<div class="cm-hipaa-forms-ssl-notice">SSL MUST BE ENABLED!</div>');

        jQuery('#cm-submitted-forms-results').html('<p>SSL MUST BE ENABLED TO SUBMIT AND VIEW FORMS (url must start with https://)!</p><p>Please contact your hosting provider or website administrator to find out how to setup SSL on your website.</p><p>If your hosting provider is unable to help you setup an SSL certificate or if the SSL certificate is too expensive you may consider moving to our managed HIPAA Compliant hosting solution with free SSL certificates included <a href="https://www.hipaaforms.online/product/hipaa-compliant-managed-hosting/" target="_blank">CLICK HERE FOR DETAILS</a></p>');
    } else {
        /*** GET FORMS ON PAGE LOAD ***/
        cmGetSubmittedFormsList();
    }

    /*** GET BAA AGREEMENT PDF URL ON PAGE LOAD ***/
    cmGetBaaPdf();

    /*** GET OPEN SUPPORT TICKETS ***/
    cmGetTickets('open', jQuery('#cm-hipaa-forms-open-tickets'));

    /*** GET CLOSED SUPPORT TICKETS ***/
    cmGetTickets('closed', jQuery('#cm-hipaa-forms-closed-tickets'));

    /*** TABS ***/
    jQuery('.cm-hipaa-tab').click(function() {
        var data = jQuery(this).attr('data');
        var content = jQuery('.cm-hipaa-tab-content[data="' + data + '"]');

        if(!content.is(':visible')) {
            jQuery('.cm-hipaa-tab').removeClass('active');
            jQuery(this).addClass('active');

            jQuery('.cm-hipaa-tab-content').fadeOut().promise().done(function() {
                content.fadeIn();
            });
        }
    });

    /*** TOUR TABS ***/
    jQuery(document).on('click', '.cm-hipaa-tour-tab-link', function() {
        // Variable Declarations
        var wrapper = jQuery(this).parent().parent().parent();
        var tabData = jQuery(this).attr('data');
        var activeTourTab = wrapper.find('.cm-hipaa-active-tour-tab-link');
        var tourTabSections = wrapper.find('.cm-hipaa-tour-tab-content');
        var sectionToOpen = wrapper.find('.cm-hipaa-tour-tab-content[data="' + tabData + '"]');

        if (!sectionToOpen.is(":visible")) {
            // Hide all tour tabs & display new tab
            tourTabSections.hide();
            sectionToOpen.fadeIn();

            // Set clicked link to active
            activeTourTab.removeClass('cm-hipaa-active-tour-tab-link');
            jQuery(this).addClass('cm-hipaa-active-tour-tab-link');
        }
    });

    /*** MOBILE ACCORDION TABS ***/
    jQuery(document).on('click', '.cm-hipaa-accordion-tab-link', function() {
        // Variable Declarations
        var tabData = jQuery(this).attr('data');
        var activeTourTab = jQuery('.cm-hipaa-active-accordion-tab-link');
        var tourTabSections = jQuery('.cm-hipaa-tour-tab-content');
        var sectionToOpen = jQuery(this).next('.cm-hipaa-tour-tab-content[data="' + tabData + '"]');

        if (!sectionToOpen.is(":visible")) {
            // Hide all tour tabs & display new tab
            tourTabSections.slideUp();
            sectionToOpen.slideDown();

            // Set clicked link to active
            activeTourTab.removeClass('cm-hipaa-active-accordion-tab-link');
            jQuery(this).addClass('cm-hipaa-active-accordion-tab-link');
        } else {
            tourTabSections.slideUp();
            activeTourTab.removeClass('cm-hipaa-active-accordion-tab-link');
        }
    });

    /*** GENERIC TOGGLES ***/
    jQuery(document).on('click', '.cm-toggle', function() {
        jQuery(this).next().slideToggle();
    });

    /*** TOGGLE FIELDS ***/
    jQuery(document).on('click', '.cm-hipaa-toggle-fields', function() {
        var content = jQuery(this).parent().parent().find('.cm-hipaa-select-form-item-fields');

        // UPDATE ICON
        if(content.is(':visible')) {
            jQuery(this).find('i').css({
                '-webkit-transform': 'rotate(0deg)',
                '-moz-transform': 'rotate(0deg)',
                '-o-transform': 'rotate(0deg)',
                '-ms-transform': 'rotate(0deg)',
                'transform': 'rotate(0deg)'
            });
        } else {
            jQuery(this).find('i').css({
                '-webkit-transform': 'rotate(180deg)',
                '-moz-transform': 'rotate(180deg)',
                '-o-transform': 'rotate(180deg)',
                '-ms-transform': 'rotate(180deg)',
                'transform': 'rotate(180deg)'
            });
        }

        content.slideToggle();
    });

    /*** TOGGLE ERRORS ***/
    jQuery(document).on('click', '.cm-hipaa-toggle-errors', function() {
        var content = jQuery(this).parent().parent().parent().find('.cm-hipaa-select-form-item-errors');

        content.slideToggle();
    });

    /*** GET/FILTER SUBMITTED FORMS ***/
    jQuery('#cm-hipaa-submitted-forms-search').click(function() {
        var location = jQuery('#cm-submitted-form-filter-location').val();
        var formName = jQuery('#cm-submitted-form-filter-form-name').val();
        var firstName = jQuery('#cm-submitted-form-filter-first-name').val();
        var lastName = jQuery('#cm-submitted-form-filter-last-name').val();
        var phone = jQuery('#cm-submitted-form-filter-phone').val();
        var email = jQuery('#cm-submitted-form-filter-email').val();
        var status = jQuery('#cm-submitted-form-filter-status').val();
        var limit = jQuery('#cm-submitted-form-filter-limit').val();
        var page = jQuery('#cm-submitted-form-filter-page').val();
        var resultsEle = jQuery('#cm-submitted-forms-results');

        // GET FORMS
        cmGetSubmittedFormsList(location, formName, firstName, lastName, phone, email, status, limit, 0, resultsEle);
    });

    /*** SUBMITTED FORMS PAGINATION PREVIOUS ***/
    jQuery(document).on('click', '#cm-hipaa-submitted-forms-prev .cm-pagination-button', function() {
        var location = jQuery('#cm-submitted-form-filter-location').val();
        var formName = jQuery('#cm-submitted-form-filter-form-name').val();
        var firstName = jQuery('#cm-submitted-form-filter-first-name').val();
        var lastName = jQuery('#cm-submitted-form-filter-last-name').val();
        var phone = jQuery('#cm-submitted-form-filter-phone').val();
        var email = jQuery('#cm-submitted-form-filter-email').val();
        var status = jQuery('#cm-submitted-form-filter-status').val();
        var limit = jQuery('#cm-submitted-form-filter-limit').val();
        var page = jQuery('#cm-submitted-form-filter-page').val();
        var resultsEle = jQuery('#cm-submitted-forms-results');
        var newPage = parseInt(page, 10)-1;

        cmGetSubmittedFormsList(location, formName, firstName, lastName, phone, email, status, limit, newPage, resultsEle);
    });

    /*** SUBMITTED FORMS PAGINATION NEXT ***/
    jQuery(document).on('click', '#cm-hipaa-submitted-forms-next .cm-pagination-button', function() {
        var location = jQuery('#cm-submitted-form-filter-location').val();
        var formName = jQuery('#cm-submitted-form-filter-form-name').val();
        var firstName = jQuery('#cm-submitted-form-filter-first-name').val();
        var lastName = jQuery('#cm-submitted-form-filter-last-name').val();
        var phone = jQuery('#cm-submitted-form-filter-phone').val();
        var email = jQuery('#cm-submitted-form-filter-email').val();
        var status = jQuery('#cm-submitted-form-filter-status').val();
        var limit = jQuery('#cm-submitted-form-filter-limit').val();
        var page = jQuery('#cm-submitted-form-filter-page').val();
        var resultsEle = jQuery('#cm-submitted-forms-results');
        var newPage = parseInt(page, 10)+1;

        cmGetSubmittedFormsList(location, formName, firstName, lastName, phone, email, status, limit, newPage, resultsEle);
    });

    /*** UPDATE FORM BUILDER ***/
    jQuery(document).on('change', '#cm-hipaa-form-builder-select', function() {
        if(jQuery(this).val()) {
            cmUpdateFormBuilder(jQuery(this).val());
        }
    });

    /*** SELECT / DESELECT FORM ***/
    jQuery(document).on('click', '.cm-hipaa-form-select', function() {
        var item = jQuery(this).parent().parent();
        var formId = item.attr('id');
        var formBuilder = jQuery('#selected-form-builder').val();
        var enabledIdsInput;
        if(formBuilder === 'caldera') {
            enabledIdsInput = jQuery('form#cm_hipaa_forms_options input[name="caldera_enabled_form_ids"]');
        } else if(formBuilder === 'gravity') {
            enabledIdsInput = jQuery('form#cm_hipaa_forms_options input[name="gravity_enabled_form_ids"]');
        }
        var settingsForm = jQuery(this).parent().parent().parent().find('.cm-hipaa-select-form-form');
        var enabledIds = enabledIdsInput.val();
        var selected;
        var newEnabledIds;
        var thisOption = jQuery(this);

        if(item.hasClass('selected')) {
            item.removeClass('selected');
            thisOption.html('<i class="material-icons">check_box_outline_blank</i>');

            // REMOVE FORM ID FROM ENABLED FORMS INPUT STRING - DEPRECATED
            //newEnabledIds = enabledIds.replace(formId + ',', '');

            // UPDATE ENABLED FORMS INPUT - DEPRECATED, REMOVE VALUE
            enabledIdsInput.val('');

            selected = false;

            // SAVE FORM
            cmSaveFormSettings(item);
        } else if(!item.hasClass('selected') && !item.hasClass('errors')) {
            var selectedForms = jQuery('.cm-hipaa-select-form-item.selected');

            /*** VALIDATE ACCOUNT ***/
            thisOption.html('<img style="float:left;width:24px;height:24px;margin:auto;" src="' + hipaaScript.pluginUrl + '/images/loading/loading19.gif" />');
            jQuery.ajax({
                method: 'POST',
                url: ajax.ajax_url,
                data: {
                    'action': 'cm_hipaa_validate_account',
                    'nonce': hipaaScript.nonce
                },
                success: function (data) {
                    var resultData = JSON.parse(data);

                    if (resultData.success === 'success' && resultData.product === 'basic') {
                        if(selectedForms.length >= 1) {
                            // IF A FORM IS ALREADY SELECTED SHOW MESSAGE ONLY ONE FORM ALLOWED
                            alert('Only 1 form can be selected under the Basic Free subscription. Upgrade to the standard subscription to enable multiple forms');
                            thisOption.html('<i class="material-icons">check_box_outline_blank</i>');
                        } else {
                            item.addClass('selected');
                            thisOption.html('<i class="material-icons">check_box</i>');

                            // APPEND FORM ID TO ENABLED FORMS INPUT STRING - DEPRECATED
                            //newEnabledIds = enabledIds + formId + ',';

                            // UPDATE ENABLED FORMS INPUT - DEPRECATED, REMOVE VALUE
                            enabledIdsInput.val('');

                            selected = true;

                            // SAVE FORM
                            cmSaveFormSettings(item);
                        }
                    } else {
                        item.addClass('selected');
                        thisOption.html('<i class="material-icons">check_box</i>');

                        // APPEND FORM ID TO ENABLED FORMS INPUT STRING - DEPRECATED
                        //newEnabledIds = enabledIds + formId + ',';

                        // UPDATE ENABLED FORMS INPUT - DEPRECATED, REMOVE VALUE
                        enabledIdsInput.val('');

                        selected = true;

                        // SAVE FORM
                        cmSaveFormSettings(item);
                    }
                },
                error: function (errorThrown) {
                    console.log(errorThrown);
                }
            });
        }
    });

    /*** SAVE FORM SETTINGS ***/
    jQuery(document).on('click', '.cm-hipaa-forms-form-settings-submit', function() {
        var formWrapper = jQuery(this).parent().parent().parent();
        cmSaveFormSettings(formWrapper);
    });

    /*** UPDATE HIPAA ROLE CAPABILITIES ***/
    jQuery(document).on('click', '#cm-hipaa-role-capabilities-submit', function() {
        cmUpdateHipaaRoleCapabilities();
    });

    /*** UPDATE HIPAA EMAIL NOTIFICATION ***/
    jQuery(document).on('click', '#cm-hipaa-notification-email-submit', function() {
        cmUpdateHipaaEmailNotification();
    });

    /*** SAVE CSS FIELD ***/
    jQuery(document).on('click', '#cm-hipaa-forms-css-submit', function() {
        var visibleCss = jQuery(this).parent().find('#hipaa-form-css-visible').val();

        // ADD VISIBLE CSS TO HIDDEN INPUT
        jQuery('input[name="hipaa_form_css"]').val(visibleCss).promise().done(function() {
            // SAVE THE OPTIONS FORM
            jQuery('#cm_hipaa_forms_options').find('input[type="submit"]').trigger('click');
        });
    });

    /*** SUCCESS HANDLER RADIO BUTTONS ***/
    jQuery(document).on('change', 'input[name="cm-hipaa-forms-success-handler"]', function() {
        var value = jQuery(this).val();
        var wrapperEle = jQuery(this).parent().parent().find('.cm-hipaa-forms-success-handler-field');
        var messageEle = jQuery(this).parent().parent().find('.cm-hipaa-forms-success-message-wrapper');
        var redirectEle = jQuery(this).parent().parent().find('.cm-hipaa-forms-success-redirect-wrapper');
        var callbackEle = jQuery(this).parent().parent().find('.cm-hipaa-forms-success-callback-wrapper');

        if(value === 'redirect') {
            wrapperEle.fadeOut().promise().done(function() {
                redirectEle.fadeIn();
            });
        } else if(value === 'message') {
            wrapperEle.fadeOut().promise().done(function() {
                messageEle.fadeIn();
            });
        } else if(value === 'callback') {
            wrapperEle.fadeOut().promise().done(function() {
                callbackEle.fadeIn();
            });
        }
    });

    /*** USERS HANDLER RADIO BUTTONS ***/
    jQuery(document).on('change', 'input[name="cm-hipaa-forms-users-handler"]', function() {
        var value = jQuery(this).val();
        var specificEle = jQuery(this).parent().parent().find('.cm-hipaa-forms-approved-users-wrapper');
        var selectedEle = jQuery(this).parent().parent().find('.cm-hipaa-forms-selected-users-wrapper');

        if(value === 'specific') {
            selectedEle.fadeOut().promise().done(function() {
                specificEle.fadeIn();
            });
        } else if(value === 'selected') {
            specificEle.fadeOut().promise().done(function() {
                selectedEle.fadeIn();
            });
        } else {
            specificEle.fadeOut();
            selectedEle.fadeOut();
        }
    });

    /*** OPEN SELECTED USERS MODAL ***/
    jQuery(document).on('click', '.cm-hipaa-submitted-form-transfer-user i', function() {
        var formId = jQuery(this).parent().parent().parent().parent().attr('data-id');
        var selectedUser = jQuery(this).attr('data');
        var nonce = hipaaScript.nonce;

        // CREATE MODAL
        jQuery.ajax({
            method: 'POST',
            url: ajax.ajax_url,
            data: {
                'action': 'cm_hipaa_selected_users_modal',
                'form_id': formId,
                'selected_user': selectedUser,
                'nonce': nonce
            },
            success: function (data) {
                // APPEND MODAL TO BODY
                jQuery('body').append(data).promise().done(function() {
                    // FADE IN MODAL
                    jQuery('.cm-hipaa-forms-modal').fadeIn();
                });
            },
            error: function (errorThrown) {
                console.log(errorThrown);
            }
        });
    });

    /*** REASSIGN SELECTED USERS ***/
    jQuery(document).on('click', '.cm-hipaa-forms-selected-users-submit.active', function() {
        var formId = jQuery(this).attr('data-id');
        var selectInput = jQuery(this).parent().find('.cm-hipaa-forms-selected-user-select');
        var selectedUsers = selectInput.val() || [];
        var notice = jQuery('.cm-hipaa-forms-reassign-notice');
        var nonce = hipaaScript.nonce;

        if(jQuery.isArray(selectedUsers)) {
            selectedUsers = selectedUsers.join(',');
        }

        jQuery(this).removeClass('active');

        // SEND TO API
        notice.html('<div class="cm-hipaa-forms-loading-form"><img style="text-align:center;margin:auto;max-height:50px;max-width:90%;padding:25px;" src="' + hipaaScript.pluginUrl + '/images/loading/loading14.gif" /></div>');
        jQuery.ajax({
            method: 'POST',
            url: ajax.ajax_url,
            data: {
                'action': 'cm_hipaa_reassign_selected_user',
                'form_id': formId,
                'selected_user': selectedUsers,
                'nonce': nonce
            },
            success: function (data) {
                var updateData = JSON.parse(data);
                var message;

                if(updateData.error) {
                    message = updateData.error;
                } else if(updateData.success === 'success') {
                    message = 'Form has been reassigned!'
                } else {
                    message = 'Unknown error';
                }

                // UPDATE THE MODAL WINDOW
                jQuery('.cm-hipaa-forms-modal-inner-body').html(message);

                // UPDATE ICON DATA ATTRIBUTE
                jQuery('#cm-hipaa-form-id-' + formId + ' .cm-hipaa-submitted-form-transfer-user i').attr('data', selectedUsers);

                notice.html('');
            },
            error: function (errorThrown) {
                console.log(errorThrown);
                notice.html('errorThrown');
            }
        });
    });

    /*** CLOSE SELECTED USER MODAL ***/
    jQuery(document).on('click', '.cm-hipaa-forms-su-modal-close', function() {
        jQuery('.cm-hipaa-forms-selected-users-modal').fadeOut().promise().done(function() {
            jQuery(this).remove();
        });
    });

    /*** FORM SPECIFIC NOTIFICATION RADIO BUTTONS ***/
    jQuery(document).on('change', 'input[name="cm-hipaa-forms-notification-handler"]', function() {
        var value = jQuery(this).val();
        var customNotificationEle = jQuery(this).parent().parent().find('.cm-hipaa-forms-custom-notification-wrapper');

        if(value === 'custom') {
            customNotificationEle.fadeIn();
        } else {
            customNotificationEle.fadeOut();
        }
    });

    /*** OPEN SETTINGS INFO MODAL ***/
    jQuery(document).on('click', '.cm-hipaa-setting-info', function() {
        var content = jQuery(this).data('content');
        var modal = jQuery('.cm-hipaa-setting-info-modal[data-content="' + content + '"]');

        modal.fadeIn();
    });

    /*** CLOSE SETTINGS INFO MODAL ***/
    jQuery(document).on('click', '.cm-hipaa-setting-info-modal-close', function() {
        jQuery(this).parent().parent().parent().fadeOut();
    });

    /*** OPEN GENERATE PDF MODAL ***/
    jQuery(document).on('click', '.cm-hipaa-generate-pdf-modal-button', function() {
        var formId = jQuery(this).attr('data');

        // CREATE THE MODAL
        jQuery('body').append('<div class="cm-hipaa-forms-modal"><div class="cm-hipaa-forms-modal-inner"><div class="cm-hipaa-forms-modal-inner-top"><div class="cm-hipaa-forms-pdf-modal-close"><i class="material-icons">cancel</i></div></div><div class="cm-hipaa-forms-modal-inner-body"><div class="cm-hipaa-forms-modal-text">Specify a password that will be used to open the encrypted PDF:</div><div class="cm-hipaa-forms-modal-inputs"><input id="cm-hipaa-forms-pdf-password-input" type="text" placeholder="PASSWORD (min. 6 characters)" value="" /></div><div class="cm-hipaa-forms-generate-pdf cm-button" data="' + formId + '">GENERATE PDF</div></div></div></div>');

        // FADE IN MODAL
        jQuery('.cm-hipaa-forms-modal').fadeIn();
    });

    /*** CLOSE PDF MODAL ***/
    jQuery(document).on('click', '.cm-hipaa-forms-pdf-modal-close', function() {
        var modal = jQuery(this).parent().parent().parent();
        var pdfUrl = jQuery(this).attr('data-url');
        var nonce = hipaaScript.nonce;

        if(pdfUrl) {
            // DELETE PDF
            jQuery.ajax({
                method: 'POST',
                url: ajax.ajax_url,
                data: {
                    'action': 'cm_hipaa_delete_pdf',
                    'url': pdfUrl,
                    'nonce': nonce
                },
                success: function (data) {
                    //console.log(data);
                },
                error: function (errorThrown) {
                    console.log(errorThrown);
                }
            });
        }

        // REMOVE MODAL WINDOW
        modal.fadeOut().promise().done(function() {
            modal.remove();
        });
    });

    /*** ACTIVATE/DEACTIVATE GENERATE PDF BUTTON BASED ON PASSWORD FIELD ***/
    jQuery(document).on('keyup', '#cm-hipaa-forms-pdf-password-input', function(e) {
        if(jQuery(this).val().length >= 6 || event.keyCode == 13) {
            jQuery('.cm-hipaa-forms-generate-pdf').addClass('active').promise().done(function() {
                // FIRE GENERATE PDF BUTTON ON ENTER WHILE IN PASSWORD INPUT
                if(e.keyCode == 13) {
                    jQuery('.deal_search_submit.active').trigger('click');
                }
            });
        } else {
            jQuery('.cm-hipaa-forms-generate-pdf').removeClass('active');
        }
    });

    /*** GENERATE PDF ***/
    jQuery(document).on('click', '.cm-hipaa-forms-generate-pdf.active', function() {
        var formId = jQuery(this).attr('data');
        var pdfPassword = jQuery('#cm-hipaa-forms-pdf-password-input').val();
        var nonce = hipaaScript.nonce;

        // CREATE PDF
        jQuery.ajax({
            method: 'POST',
            url: ajax.ajax_url,
            data: {
                'action': 'cm_hipaa_generate_pdf',
                'form_id': formId,
                'pdf_password': pdfPassword,
                'nonce': nonce
            },
            success: function (data) {
                var pdfData = JSON.parse(data);

                if(pdfData.error) {
                    // IF ERROR UPDATE THE MODAL WINDOW WITH ERROR MESSAGE
                    jQuery('.cm-hipaa-forms-modal-inner-body').html(pdfData.error);
                } else {
                    // UPDATE THE MODAL WINDOW
                    jQuery('.cm-hipaa-forms-modal-inner-body').html(pdfData.modal_message);

                    // ADD PDF URL TO CLOSE BUTTON
                    jQuery('.cm-hipaa-forms-modal-close').attr('data-url', pdfData.pdf_url);
                }
            },
            error: function (errorThrown) {
                console.log(errorThrown);
                jQuery('.cm-hipaa-forms-modal-inner-body').html(errorThrown);
            }
        });
    });

    /*** TOGGLE SUBMITTED FORM FIELDS ***/
    jQuery(document).on('click', '.cm-hipaa-submitted-form-toggle-fields', function() {
        var formRow = jQuery(this).parent().parent().parent();
        var formId = formRow.attr('data-id');
        var nonce = hipaaScript.nonce;
        var fields = formRow.next().find('.cm-hipaa-submitted-form-fields');

        // TODO: UPDATE FORM HISTORY


        if(fields.is(':visible')) {
            // UPDATE ICON
            jQuery(this).find('i').css({
                '-webkit-transform': 'rotate(0deg)',
                '-moz-transform': 'rotate(0deg)',
                '-o-transform': 'rotate(0deg)',
                '-ms-transform': 'rotate(0deg)',
                'transform': 'rotate(0deg)'
            });
        } else {
            // UPDATE ICON
            jQuery(this).find('i').css({
                '-webkit-transform': 'rotate(180deg)',
                '-moz-transform': 'rotate(180deg)',
                '-o-transform': 'rotate(180deg)',
                '-ms-transform': 'rotate(180deg)',
                'transform': 'rotate(180deg)'
            });

            // GET FORM
            cmGetSubmittedForm(formId);

            // SHADE FORM ROW
            formRow.addClass('cm-hipaa-submitted-form-viewed');
        }

        // TOGGLE FIELDS
        fields.slideToggle();
    });

    /*** ARCHIVE FORM ***/
    jQuery(document).on('click', '.cm-hipaa-submitted-form-archive', function() {
        var formRow = jQuery(this).parent().parent().parent();
        var formId = formRow.attr('data-id');
        var statusFilter = jQuery('#cm-submitted-form-filter-status').val();
        var thisIcon = jQuery(this);
        var nonce = hipaaScript.nonce;

        // DELETE FORM
        jQuery.ajax({
            method: 'POST',
            url: ajax.ajax_url,
            data: {
                'action': 'cm_hipaa_archive_form',
                'form_id': formId,
                'nonce': nonce
            },
            success: function (data) {
                var returnData = JSON.parse(data);

                if(returnData.success === 'success') {
                    if(statusFilter === 'all') {
                        thisIcon.replaceWith('<div class="cm-hipaa-submitted-form-restore"><i class="material-icons" title="Restore">restore</i></div>');
                    } else {
                        formRow.remove();
                        formRow.next().remove();
                    }
                }
            },
            error: function (errorThrown) {
                console.log(errorThrown);
            }
        });
    });

    /*** RESTORE ARCHIVED FORM ***/
    jQuery(document).on('click', '.cm-hipaa-submitted-form-restore', function() {
        var formRow = jQuery(this).parent().parent().parent();
        var formId = formRow.attr('data-id');
        var statusFilter = jQuery('#cm-submitted-form-filter-status').val();
        var thisIcon = jQuery(this);
        var nonce = hipaaScript.nonce;

        // DELETE FORM
        jQuery.ajax({
            method: 'POST',
            url: ajax.ajax_url,
            data: {
                'action': 'cm_hipaa_restore_form',
                'form_id': formId,
                'nonce': nonce
            },
            success: function (data) {
                var returnData = JSON.parse(data);

                if(returnData.success === 'success') {
                    if(statusFilter === 'all') {
                        thisIcon.replaceWith('<div class="cm-hipaa-submitted-form-delete"><i class="material-icons" title="Archive">archive</i></div>');
                    } else {
                        formRow.remove();
                        formRow.next().remove();
                    }
                }
            },
            error: function (errorThrown) {
                console.log(errorThrown);
            }
        });
    });

    /*** DESTROY FORM CONFIRM MODAL ***/
    jQuery(document).on('click', '.cm-hipaa-submitted-form-destroy-confirm', function() {
        var formRow = jQuery(this).parent().parent().parent();
        var formRowId = formRow.attr('id');
        var formId = formRow.attr('data-id');

        // CREATE THE MODAL
        jQuery('body').append('<div class="cm-hipaa-forms-modal"><div class="cm-hipaa-forms-modal-inner"><div class="cm-hipaa-forms-modal-inner-top"><div class="cm-hipaa-forms-destroy-modal-close"><i class="material-icons">cancel</i></div></div><div class="cm-hipaa-forms-modal-inner-body"><div class="cm-hipaa-forms-modal-text">Are you sure you want to permanently destroy this form?</div><div class="cm-hipaa-submitted-form-destroy cm-button" data-form-id="' + formId + '" data-form-row="' + formRowId + '">DESTROY FORM</div><div id="cm-hipaa-submitted-form-destroy-notice"></div></div></div></div>');

        // FADE IN MODAL
        jQuery('.cm-hipaa-forms-modal').fadeIn();
    });

    /*** DESTROY FORM ***/
    jQuery(document).on('click', '.cm-hipaa-submitted-form-destroy', function() {
        var modal = jQuery(this).parent().parent().parent();
        var notice = jQuery('#cm-hipaa-submitted-form-destroy-notice');
        var formRowId = jQuery(this).attr('data-form-row');
        var formId = jQuery(this).attr('data-form-id');
        var nonce = hipaaScript.nonce;

        // DELETE FORM
        notice.html('<div class="cm-hipaa-forms-loading-form"><img style="text-align:center;margin:auto;" src="' + hipaaScript.pluginUrl + '/images/loading/loading14.gif" /></div>');
        jQuery.ajax({
            method: 'POST',
            url: ajax.ajax_url,
            data: {
                'action': 'cm_hipaa_destroy_form',
                'form_id': formId,
                'nonce': nonce
            },
            success: function (data) {
                var returnData = JSON.parse(data);

                if(returnData.success === 'success') {
                    notice.html('Form Destroyed');

                    var formRow = jQuery('#' + formRowId);
                    formRow.remove();
                    formRow.next().remove();

                    // REMOVE MODAL WINDOW
                    modal.fadeOut().promise().done(function() {
                        modal.remove();
                    });
                } else {
                    notice.html('There was a problem destroying the form');
                }
            },
            error: function (errorThrown) {
                console.log(errorThrown);
                notice.html('There was a problem destroying the form<br />' + errorThrown);
            }
        });
    });

    /*** CLOSE DESTROY FORM MODAL ***/
    jQuery(document).on('click', '.cm-hipaa-forms-destroy-modal-close', function() {
        var modal = jQuery(this).parent().parent().parent();

        // REMOVE MODAL WINDOW
        modal.fadeOut().promise().done(function() {
            modal.remove();
        });
    });

    /*** GET/FILTER ACCESS LOGS ***/
    jQuery('#cm-hipaa-forms-logs-search').click(function() {
        var startDate = jQuery('#cm-hipaa-forms-logs-filter-start-date').val();
        var endDate = jQuery('#cm-submitted-form-filter-end-date').val();
        var limit = jQuery('#cm-hipaa-forms-logs-filter-limit').val();
        var page = jQuery('#cm-hipaa-forms-logs-filter-page').val();
        var resultsEle = jQuery('#cm-hipaa-forms-logs-results');

        // GET FORMS
        cmGetLogs(startDate, endDate, limit, 0, resultsEle);
    });

    /*** ACCESS LOGS PAGINATION PREVIOUS ***/
    jQuery(document).on('click', '#cm-hipaa-forms-logs-prev .cm-pagination-button', function() {
        var startDate = jQuery('#cm-hipaa-forms-logs-filter-start-date').val();
        var endDate = jQuery('#cm-submitted-form-filter-end-date').val();
        var limit = jQuery('#cm-hipaa-forms-logs-filter-limit').val();
        var page = jQuery('#cm-hipaa-forms-logs-filter-page').val();
        var resultsEle = jQuery('#cm-hipaa-forms-logs-results');
        var newPage = parseInt(page, 10)-1;

        cmGetLogs(startDate, endDate, limit, newPage, resultsEle);
    });

    /*** ACCESS LOGS PAGINATION NEXT ***/
    jQuery(document).on('click', '#cm-hipaa-forms-logs-next .cm-pagination-button', function() {
        var startDate = jQuery('#cm-hipaa-forms-logs-filter-start-date').val();
        var endDate = jQuery('#cm-submitted-form-filter-end-date').val();
        var limit = jQuery('#cm-hipaa-forms-logs-filter-limit').val();
        var page = jQuery('#cm-hipaa-forms-logs-filter-page').val();
        var resultsEle = jQuery('#cm-hipaa-forms-logs-results');
        var newPage = parseInt(page, 10)+1;

        cmGetLogs(startDate, endDate, limit, newPage, resultsEle);
    });

    /*** OPEN BAA FORM MODAL ***/
    jQuery(document).on('click', '.cm-sign-baa-button', function() {
        // CREATE THE MODAL
        jQuery('body').append('<div class="cm-baa-modal"><div class="cm-baa-modal-inner"><div class="cm-baa-modal-inner-top"><div class="cm-baa-modal-close"><i class="material-icons">cancel</i></div></div><div class="cm-baa-modal-inner-body"><div class="cm-baa-modal-form"></div></div></div></div>');
        var nonce = hipaaScript.nonce;

        jQuery.ajax({
            method: 'POST',
            url: ajax.ajax_url,
            data: {
                'action': 'cm_hipaa_get_baa_form',
                'nonce': nonce
            },
            success: function (data) {
                var resultData = JSON.parse(data);

                jQuery('.cm-baa-modal-form').html(resultData.form);

                jQuery("#cm-baa-form-signature").jSignature();
            },
            error: function (errorThrown) {
                console.log(errorThrown);
                jQuery('.cm-baa-modal-form').html(errorThrown);
            }
        });

        // FADE IN MODAL
        jQuery('.cm-baa-modal').fadeIn();
    });

    /*** CLOSE BAA MODAL ***/
    jQuery(document).on('click', '.cm-baa-modal-close', function() {
        var modal = jQuery(this).parent().parent().parent();

        // REMOVE MODAL WINDOW
        modal.fadeOut().promise().done(function() {
            modal.remove();

            setTimeout(function() {
                location.reload();
            }, 1500);
        });
    });

    /*** HIDE SIGN HERE IMAGE ***/
    jQuery(document).on('click', '#cm-baa-form-signature', function() {
        jQuery('.cm-baa-form-sign-here').hide();
    });

    /*** RESET BAA SIGNATURE ***/
    jQuery(document).on('click', '#cm-baa-form-signature-reset', function() {
        jQuery('#cm-baa-form-signature').jSignature("reset");
        jQuery('.cm-baa-form-sign-here').show();
    });

    /*** UPDATE COMPANY NAME TEXT ***/
    jQuery(document).on('keyup', '#cm-baa-form-company-name', function() {
        jQuery('#cm-baa-form-company-name-text').html(jQuery(this).val());
        jQuery('.cm-baa-form-company-name-text').html(jQuery(this).val());
    });

    /*** PRIVACY NOTICE METHOD RADIO OPTION ***/
    jQuery(document).on('change', 'input[name="cm-hipaa-forms-privacy-type"]', function() {
        if(jQuery(this).val() === 'modal') {
            jQuery('#cm-hipaa-forms-privacy-link').fadeOut().promise().done(function() {
                jQuery('#cm-hipaa-forms-privacy-copy').fadeIn();
            });
        } else if(jQuery(this).val() === 'link') {
            jQuery('#cm-hipaa-forms-privacy-copy').fadeOut().promise().done(function() {
                jQuery('#cm-hipaa-forms-privacy-link').fadeIn();
            });
        }
    });

    /*** SAVE PRIVACY OPTIONS ***/
    jQuery(document).on('click', '#cm-hipaa-forms-privacy-submit', function() {
        cmHipaaSavePrivacyOptions();
    });

    /*** SUBMIT BAA FORM ***/
    jQuery(document).on('click', '#cm-baa-form-submit', function() {
        var agreeEle = jQuery('#cm-baa-form-agree');
        var noticeEle = jQuery('#cm-baa-form-notice');
        var signHere = jQuery('.cm-baa-form-sign-here');
        var signatureEle = jQuery('#cm-baa-form-signature');
        var signersNameInput = jQuery('#cm-baa-form-signers-name');
        var signersName = signersNameInput.val();
        var companyNameInput = jQuery('#cm-baa-form-company-name');
        var companyName = companyNameInput.val();
        var nonce = hipaaScript.nonce;

        // RESET ERRORS
        agreeEle.css('border', '1px solid #b4b9be');
        companyNameInput.css('border', '1px solid #b4b9be');
        signersNameInput.css('border', '1px solid #b4b9be');
        signatureEle.css('border', '0');
        noticeEle.html('');

        // GET SIGNATURE SVG BASE64 DATA
        var datapair = signatureEle.jSignature("getData", "svgbase64");
        var signature = "data:" + datapair[0] + "," + datapair[1];

        // VALIDATE FORM
        if(agreeEle.attr('checked') !== 'checked') {
            agreeEle.css('border', '2px solid red');
            noticeEle.html('You must agree to the BAA Agreement!');
        } else if(signHere.is(':visible')) {
            signatureEle.css('border', '2px solid red');
            noticeEle.html('You must sign the BAA Agreement!');
        } else if(!companyName) {
            companyNameInput.css('border', '2px solid red');
            noticeEle.html('You must add your company name!');

            // SCROLL TO FIELD
            var formWrapper = jQuery('.cm-baa-modal-form');
            //formWrapper.scrollTop(formWrapper.scrollTop() + companyNameInput.position().top);

            formWrapper.animate({
                scrollTop: companyNameInput.parent().scrollTop() + companyNameInput.offset().top - companyNameInput.parent().offset().top
            }, {
                duration: 1000,
                specialEasing: {
                    width: 'linear',
                    height: 'easeOutBounce'
                },
                complete: function (e) {
                    console.log("animation completed");
                }
            });
        } else if(!signersName) {
            signersNameInput.css('border', '2px solid red');
            noticeEle.html('You must add your name!');
        } else {
            // REPLACE COMPANY NAME INPUT WITH VALUE
            companyNameInput.replaceWith('<span style="font-weight: bold">' + companyName + '</span>');
            var form = jQuery('.cm-baa-form').html();

            // SUBMIT THE FORM
            jQuery.ajax({
                method: 'POST',
                url: ajax.ajax_url,
                data: {
                    'action': 'cm_hipaa_submit_baa_form',
                    'form': form,
                    'signature': signature,
                    'signers_name': signersName,
                    'company_name': companyName,
                    'nonce': nonce
                },
                success: function (data) {
                    var resultData = JSON.parse(data);

                    if(resultData.error) {
                        noticeEle.html(resultData.error);
                    } else {
                        // UPDATE THE MODAL WINDOW
                        jQuery('.cm-baa-form-wrapper').html('Thank you for signing the BAA! You should now be authorized to use the HIPAA FORMS Service.<div><a class="cm-button" href="' + resultData.form + '" target="_blank">View BAA</a></div>');

                        // UPDATE THE SUBMITTED FORMS VIEW
                        jQuery('#cm-submitted-forms-results').html('Thank you for signing the BAA! You should now be authorized to use the HIPAA FORMS Service.<div><a class="cm-button" href="' + resultData.form + '" target="_blank">View BAA</a></div>');

                        // UPDATE THE SETTING TAB
                        jQuery('#cm-hipaa-forms-signed-baa').html('<div><a href="' + resultData.form + '" target="_blank">View BAA</a></div>');
                    }
                },
                error: function (errorThrown) {
                    console.log(errorThrown);
                    noticeEle.html(errorThrown);
                }
            });
        }
    });

    /*** SUBMIT SUPPORT TICKET ***/
    jQuery('#cm-hipaa-forms-submit-ticket').click(function() {
        var priorityEle = jQuery('#cm-hipaa-forms-submit-ticket-priority');
        var channelEle = jQuery('#cm-hipaa-forms-submit-ticket-channel');
        var subjectEle = jQuery('#cm-hipaa-forms-submit-ticket-subject');
        var messageEle = jQuery('#cm-hipaa-forms-submit-ticket-message');
        var noticeEle = jQuery('#cm-hipaa-forms-submit-ticket-notice');
        var priority = priorityEle.val();
        var channel = channelEle.val();
        var subject = subjectEle.val();
        var message = messageEle.val();

        cmSubmitTicket(priorityEle, channelEle, subjectEle, messageEle, noticeEle, priority, channel, subject, message, '');
    });

    /*** TOGGLE SUPPORT TICKETS ***/
    jQuery(document).on('click', '.cm-support-ticket-toggle', function() {
        var ticketBody = jQuery(this).parent().parent().parent().find('.cm-support-ticket-body');

        // UPDATE ICON
        if(ticketBody.is(':visible')) {
            jQuery(this).html('<i class="material-icons">arrow_drop_down</i>');
        } else {
            jQuery(this).html('<i class="material-icons">arrow_drop_up</i>');
        }

        // TOGGLE TICKET
        ticketBody.slideToggle();
    });

    /*** REPLY TO SUPPORT TICKET ***/
    jQuery(document).on('click', '.cm-support-ticket-reply-submit', function() {
        var parentTicketId = jQuery(this).attr('data');
        var messageEle = jQuery(this).parent().parent().find('.cm-support-ticket-reply-input');
        var noticeEle = jQuery(this).parent().find('.cm-support-ticket-reply-notice');
        var message = messageEle.attr('value');

        cmSubmitTicket('', '', '', messageEle, noticeEle, '', '', '', message, parentTicketId);
    });

    /*** CLOSE TICKETS ***/
    jQuery(document).on('click', '.cm-support-ticket-close', function() {
        var ticketId = jQuery(this).attr('data');
        var noticeEle = jQuery(this).parent().find('.cm-support-ticket-reply-notice');

        cmCloseTicket(ticketId, noticeEle);
    });

    /*** SUBMIT FORM NOTE ***/
    jQuery(document).on('click', '.cm-hipaa-form-notes-add-note-submit', function() {
        var formId = jQuery(this).data('form-id');
        var userId = jQuery(this).data('my-id');
        var name = jQuery(this).data('name');
        var email = jQuery(this).data('email');
        var noteEle = jQuery(this).parent().find('.cm-hipaa-form-notes-add-note-input');
        var note = noteEle.val();
        var noticeEle = jQuery(this).parent().find('.cm-hipaa-form-notes-add-note-notice');
        var notesEle = jQuery(this).parent().parent().parent().find('.cm-hipaa-form-notes');

        cmSubmitNote(formId, userId, name, email, note, noteEle, notesEle);
    });

    /*** UPDATE FORM HISTORY LIMIT ***/
    jQuery(document).on('change', '.cm-hipaa-submitted-form-history-limit', function() {
        var limit = jQuery(this).val();
        var formId = jQuery(this).attr('data-form-id');

        cmGetFormHistory(formId, 0, limit);
    });

    /*** PAGINATE FORM HISTORY ***/
    jQuery(document).on('click', '.cm-hipaa-submitted-form-history-pag-button', function() {
        var formId = jQuery(this).attr('data-form-id');
        var parentWrapper = jQuery('#cm-submitted-form-history-wrapper-' + formId);
        var direction = jQuery(this).attr('data-direction');
        var limit = parentWrapper.parent().find('.cm-hipaa-submitted-form-history-limit').val();
        var page = parentWrapper.find('.cm-submitted-form-history-page').val();
        var newPage;

        if(direction === 'next') {
            newPage = (parseInt(page, 10));
        } else if(direction === 'previous') {
            newPage = (parseInt(page, 10)-2);
        }

        cmGetFormHistory(formId, newPage, limit);
    });

    /*** EXPORT FORM ***/
    jQuery(document).on('click', '.cm-hipaa-export-form', function() {
        var formId = jQuery(this).attr('data-form-id');

        cmHipaaExportForm(formId);
    });

    /*** EXPORT FORM NOTES ***/
    jQuery(document).on('click', '.cm-hipaa-export-form-notes', function() {
        var formId = jQuery(this).attr('data-form-id');

        cmHipaaExportFormNotes(formId);
    });

    /*** EXPORT FORM HISTORY ***/
    jQuery(document).on('click', '.cm-hipaa-export-form-history', function() {
        var formId = jQuery(this).attr('data-form-id');

        cmHipaaExportFormHistory(formId);
    });

    /*** PRINT SUBMITTED FORM ***/
    jQuery(document).on('click', '.cm-hipaa-submitted-form-print', function() {
        var pluginCSS = hipaaScript.pluginUrl + 'css/admin-style.css';
        var gravityCSS = hipaaScript.siteUrl + '/wp-content/plugins/gravityforms/forms.min.css';
        var calderaCSS = hipaaScript.siteUrl + '/wp-content/plugins/caldera-forms/assets/css/caldera-form.css';
        var printCSS = hipaaScript.pluginUrl + '/css/print.css';
        var form = jQuery(this).closest('.cm-hipaa-submitted-form-fields-inner');
        var formId = jQuery(this).attr('data-form-id');
        var nonce = hipaaScript.nonce;

        form.printThis({
            debug: false,
            printDelay: 500,
            printContainer: false,
            importCSS: true,
            importStyle: true,
            copyTagClasses: true,
            loadCSS: [pluginCSS, gravityCSS, calderaCSS, printCSS]
        });

        // LOG PRINT EVENT
        jQuery.ajax({
            method: 'POST',
            url: ajax.ajax_url,
            data: {
                'action': 'cm_hipaa_print_form',
                'form_id': formId,
                'nonce': nonce
            },
            success: function (data) {
                var returnData = JSON.parse(data);

                if(returnData.success === 'success') {
                    console.log('Print Event Logged');
                } else {
                    console.log('There was a problem logging the print event');
                }
            },
            error: function (errorThrown) {
                console.log('There was a problem logging the print event <br />' + errorThrown);
            }
        });
    });

    /*** ADD STATUS INPUT ***/
    jQuery(document).on('click', '.cm-hipaa-form-settings-add-input', function() {
        jQuery('.cm-hipaa-forms-setting-status-options').append('<div class="cm-hipaa-forms-setting-status-option-wrapper"><input type="text" class="cm-hipaa-forms-setting-status-option" placeholder="Status Option..." /> <i class="material-icons cm-hipaa-form-settings-remove-input">remove_circle_outline</i></div>');
    });

    /*** REMOVE STATUS INPUT ***/
    jQuery(document).on('click', '.cm-hipaa-form-settings-remove-input', function() {
        jQuery(this).closest('.cm-hipaa-forms-setting-status-option-wrapper').remove();
    });

    /*** SAVE CUSTOM STATUS OPTIONS ***/
    jQuery(document).on('click', '#cm-hipaa-forms-custom-status-submit', function() {
        cmHipaaSaveStatusOptions();
    });

    /*** UPDATE FORM STATUS ***/
    jQuery(document).on('change', '.cm-hipaa-forms-custom-status-select', function() {
        var formId = jQuery(this).attr('data-form-id');
        var status = jQuery(this).val();

        cmHipaaUpdateFormStatus(formId, status);
    });
});

jQuery(window).ready(function() {
    // LOOP FORM SELECT ELEMENTS & DESELECT IF CURRENTLY SELECTED AND HAS ERRORS
    var formSelectEles = jQuery('.cm-hipaa-select-form-item');

    formSelectEles.each(function() {
        if(jQuery(this).hasClass('selected') && jQuery(this).hasClass('errors')) {
            jQuery(this).find('.cm-hipaa-form-select').trigger('click');

            jQuery('.cm-hipaa-forms-update-notice').append('<div class="cm-hipaa-forms-update-notice-error"><i class="material-icons" title="You Are Missing Required Fields!">warning</i> Required fields have been removed from form ID ' + jQuery(this).attr('id') + ' and has been disabled.</div><div class="cm-hipaa-forms-update-notice-sub-error">Go to settings->form settings & click the error icon to review missing fields or alert your web developer or administrator as this form is no longer secured.</div>');
        }
    });
});

//Custom progress bar
function cmHipaaMoveProgress() {
    var elem = jQuery(".cm-hipaa-forms-progress-bar");
    var width = 10;
    var id = setInterval(frame, 25);
    function frame() {
        if (width >= 100) {
            width = 10;
        } else {
            width++;
            elem.css('width', width + '%');
            jQuery(".cm-hipaa-forms-progress-label").html( width + '%');
        }
    }
}

/*** UPDATE FORM BUILDER ***/
function cmUpdateFormBuilder(formBuilder) {
    // UPDATE FORM
    jQuery('input[name="form_builder"]').val(formBuilder).promise().done(function() {
        var availableFormsWrapper = jQuery('#cm-hipaa-forms-available-forms-wrapper');
        var nonce = hipaaScript.nonce;

        // SAVE FORM
        availableFormsWrapper.html('<img src="' + hipaaScript.pluginUrl + '/images/loading/loading16.gif" />');
        jQuery('form#cm_hipaa_forms_options').ajaxSubmit({
            success: function(){
                // UPDATE AVAILABLE FORMS
                jQuery.ajax({
                    method: 'POST',
                    url: ajax.ajax_url,
                    data: {
                        'action': 'cm_hipaa_update_available_forms',
                        'nonce': nonce,
                        'form_builder': formBuilder
                    },
                    success: function (data) {
                        availableFormsWrapper.html(data);
                    },
                    error: function (errorThrown) {
                        console.log(errorThrown);
                        availableFormsWrapper.html(errorThrown);
                    }
                });
            },
            error: function (errorThrown) {
                availableFormsWrapper.html(errorThrown);
            }
        });
    });
}

/*** UPDATE HIPAA ROLE CAPABILITIES ***/
function cmUpdateHipaaRoleCapabilities() {
    var capsWrapper = jQuery('.cm-hipaa-role-capabilities-wrapper');
    var capInputs = capsWrapper.find('.hipaa-role-capability-option');
    var caps = [];

    capInputs.each(function() {
        if(jQuery(this).prop('checked') === true) {
            // PUSH INPUT NAME TO ARRAY
            caps.push(jQuery(this).data('cap'));
        }
    });

    // CONVERT ARRAY TO COMMA DELIMITED STRING
    var capabilities = caps.join(',');
    console.log(capabilities);

    // UPDATE HIDDEN INPUT VALUE IN OPTIONS FORM
    jQuery('input[name="hipaa_role_capabilities"]').val(capabilities).promise().done(function() {
        var nonce = hipaaScript.nonce;
        var noticeEle = jQuery('#cm-hipaa-role-capabilities-notice');

        // SAVE FORM
        noticeEle.html('<img src="' + hipaaScript.pluginUrl + '/images/loading/loading10.gif" />');
        jQuery('form#cm_hipaa_forms_options').ajaxSubmit({
            success: function(){
                // UPDATE AVAILABLE FORMS
                jQuery.ajax({
                    method: 'POST',
                    url: ajax.ajax_url,
                    data: {
                        'action': 'cm_hipaa_update_user_role',
                        'nonce': nonce,
                        'capabilities': capabilities
                    },
                    success: function (data) {
                        noticeEle.html(data);
                    },
                    error: function (errorThrown) {
                        noticeEle.html(errorThrown);
                    }
                });
            },
            error: function (errorThrown) {
                noticeEle.html(errorThrown);
            }
        });
    });
}

/*** SAVE FORM SETTINGS ***/
function cmSaveFormSettings(formWrapper) {
    var formBuilder = jQuery('#selected-form-builder').val();
    var enabledFormsSettingsInput = jQuery('input[name="enabled_forms_settings"]');
    var enabledFormsSettings = enabledFormsSettingsInput.val();
    if(enabledFormsSettings) {
        enabledFormsSettings = JSON.parse(enabledFormsSettings);
    } else {
        enabledFormsSettings = [];
    }

    var formId = formWrapper.attr('id');
    var formEnabled = 'no';
    if(formWrapper.hasClass('selected')) {
        formEnabled = 'yes';
    }
    var showSig = formWrapper.find('input[name="cm-hipaa-forms-show-signature"]:checked').val();
    var successHandler = formWrapper.find('input[name="cm-hipaa-forms-success-handler"]:checked').val();
    var successMessage = formWrapper.find('textarea[name="cm-hipaa-forms-success-message"]').val();
    var successRedirect = formWrapper.find('input[name="cm-hipaa-forms-success-redirect"]').val();
    var successCallback = formWrapper.find('input[name="cm-hipaa-forms-success-callback"]').val();
    var successCallbackParams = formWrapper.find('input[name="cm-hipaa-forms-success-callback-params"]').val();
    var usersHandler = formWrapper.find('input[name="cm-hipaa-forms-users-handler"]:checked').val();
    var approvedUsersInputs = formWrapper.find('input[name="cm-hipaa-forms-approved-users"]:checked');
    var selectedUserSlug = formWrapper.find('input[name="cm-hipaa-forms-selected-user-slug"]').val();
    var notificationOption = formWrapper.find('input[name="cm-hipaa-forms-notification-handler"]:checked').val();
    var notificationFromName = formWrapper.find('input[name="cm-hipaa-selected-form-notification-from-name-input"]').val();
    var notificationFromEmail = formWrapper.find('input[name="cm-hipaa-selected-form-notification-from-email-input"]').val();
    var notificationSendTo = formWrapper.find('input[name="cm-hipaa-selected-form-notification-sendto-input"]').val();
    var notificationSubject = formWrapper.find('input[name="cm-hipaa-selected-form-notification-subject-input"]').val();
    var notificationMessage = formWrapper.find('textarea[name="cm-hipaa-selected-form-notification-message-input"]').val();
    var noticeEle = formWrapper.find('.cm-form-settings-notice');
    var approvedUsersArr = [];

    approvedUsersInputs.each(function() {
        approvedUsersArr.push(jQuery(this).val());
    });

    var approvedUsers = approvedUsersArr.join(',');

    // CHECK IF FORM OBJECT EXISTS IN ARRAY
    var formFound;
    enabledFormsSettings.some(function(el) {
        if(el.id === formId) {
            formFound = true;
        }
    });
    if(formFound) {
        // LOOP ENABLED FORMS SETTINGS
        enabledFormsSettings.forEach(function(enabledFormSettings) {
            if(enabledFormSettings.id === formId) {
                // IF OBJECT FORM ID EQUALS THIS FORM ID UPDATE OBJECT VALUES
                enabledFormSettings.form_builder = formBuilder;
                enabledFormSettings.enabled = formEnabled;
                enabledFormSettings.show_signature = showSig;
                enabledFormSettings.success_handler = successHandler;
                enabledFormSettings.success_message = successMessage;
                enabledFormSettings.success_redirect = successRedirect;
                enabledFormSettings.success_callback = successCallback;
                enabledFormSettings.success_callback_params = successCallbackParams;
                enabledFormSettings.users_handler = usersHandler;
                enabledFormSettings.approved_users = approvedUsers;
                enabledFormSettings.selected_user_slug = selectedUserSlug;
                enabledFormSettings.notification_option = notificationOption;
                enabledFormSettings.notification_from_name = notificationFromName;
                enabledFormSettings.notification_from_email = notificationFromEmail;
                enabledFormSettings.notification_sendto = notificationSendTo;
                enabledFormSettings.notification_subject = notificationSubject;
                enabledFormSettings.notification_message = notificationMessage;
            }
        });
    } else {
        // CREATE FORM OBJECT AND SET VALUES
        var formSettings = {};
        formSettings.form_builder = formBuilder;
        formSettings.id = formId;
        formSettings.enabled = formEnabled;
        formSettings.show_signature = showSig;
        formSettings.success_handler = successHandler;
        formSettings.success_message = successMessage;
        formSettings.success_redirect = successRedirect;
        formSettings.success_callback = successCallback;
        formSettings.success_callback_params = successCallbackParams;
        formSettings.users_handler = usersHandler;
        formSettings.approved_users = approvedUsers;
        formSettings.selected_user_slug = selectedUserSlug;
        formSettings.notification_option = notificationOption;
        formSettings.notification_from_name = notificationFromName;
        formSettings.notification_from_email = notificationFromEmail;
        formSettings.notification_sendto = notificationSendTo;
        formSettings.notification_subject = notificationSubject;
        formSettings.notification_message = notificationMessage;

        // PUSH NEW FORM SETTINGS OBJECT TO ENABLED FORMS SETTINGS ARRAY
        enabledFormsSettings.push(formSettings);
    }

    var enabledFormsSettingsJson = JSON.stringify(enabledFormsSettings);

    // VALIDATE JSON STRING
    if(isJsonString(enabledFormsSettingsJson) === true) {
        // ADD/UPDATE FORM SETTINGS TO HIDDEN OPTION INPUT
        enabledFormsSettingsInput.val(JSON.stringify(enabledFormsSettings)).promise().done(function() {
            // SAVE FORM
            noticeEle.html('<img src="' + hipaaScript.pluginUrl + '/images/loading/loading10.gif" />');
            jQuery('form#cm_hipaa_forms_options').ajaxSubmit({
                success: function(){
                    noticeEle.html('Settings Saved');
                },
                error: function (errorThrown) {
                    noticeEle.html(errorThrown);
                }
            });
        });
    } else {
        noticeEle.html('Settings were prevented from saving due to an invalid JSON format, this can sometimes happen when adding custom css.  Please submit a support ticket and mention the error: Invalid Json');
    }
}

/*** VALIDATE JSON STRING ***/
function isJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

/*** SAVE HIPAA DEFAULT EMAIL NOTIFICATION ***/
function cmUpdateHipaaEmailNotification() {
    var disableNotifications = jQuery('#cm-hipaa-disable-notifications-input').prop('checked');
    var disableNotificationsInput = jQuery('input[name="hipaa_disable_email_notifications"]');
    var sendFromName = jQuery('#cm-hipaa-notification-from-name-input').val();
    var sendFromNameInput = jQuery('input[name="hipaa_notification_from_name"]');
    var sendFromEmail = jQuery('#cm-hipaa-notification-from-email-input').val();
    var sendFromEmailInput = jQuery('input[name="hipaa_notification_from_email"]');
    var sendToEmails = jQuery('#cm-hipaa-notification-sendto-input').val();
    var sendToEmailsInput = jQuery('input[name="notification_email"]');
    var limitEmailSendTo = jQuery('#cm-hipaa-notification-email-fallback-input').prop('checked');
    var limitEmailSendToInput = jQuery('input[name="limit_notification_email"]');
    var emailSubject = jQuery('#cm-hipaa-notification-subject-input').val();
    var emailNotificationSubjectInput = jQuery('input[name="hipaa_notification_email_subject"]');
    var emailMessageEle = jQuery('#cm-hipaa-notification-input');
    var emailMessage = emailMessageEle.attr('value');
    if(!emailMessage) {
        // FALLBACK IN CASE ATTR(VALUE) DOESN'T WORK
        emailMessage = emailMessageEle.val();
    }
    var emailNotificationInput = jQuery('input[name="hipaa_notification_email_message"]');
    var noticeEle = jQuery('#cm-hipaa-notification-email-notice');

    // ADD FROM NAME TO HIDDEN INPUT
    sendFromNameInput.val(sendFromName);

    // ADD FROM EMAIL TO HIDDEN INPUT
    sendFromEmailInput.val(sendFromEmail);

    // ADD SENDTO EMAILS TO HIDDEN INPUT
    sendToEmailsInput.val(sendToEmails);

    // ADD DISABLE INPUT OPTION TO HIDDEN INPUT
    if(disableNotifications === true) {
        disableNotificationsInput.val('on');
    } else {
        disableNotificationsInput.val('off');
    }

    // ADD SENDTO FALLBACK OPTION TO HIDDEN INPUT
    if(limitEmailSendTo === true) {
        limitEmailSendToInput.val('on');
    } else {
        limitEmailSendToInput.val('off');
    }

    // ADD SUBJECT TO HIDDEN INPUT
    emailNotificationSubjectInput.val(emailSubject);

    // ADD/UPDATE NOTIFICATION EMAIL TO HIDDEN OPTION INPUT
    emailNotificationInput.val(emailMessage).promise().done(function() {
        // SAVE FORM
        noticeEle.html('<img src="' + hipaaScript.pluginUrl + '/images/loading/loading10.gif" />');
        jQuery('form#cm_hipaa_forms_options').ajaxSubmit({
            success: function(){
                noticeEle.html('Settings Saved');
            },
            error: function (errorThrown) {
                noticeEle.html(errorThrown);
            }
        });
    });
}

// SAVE PRIVACY OPTIONS
function cmHipaaSavePrivacyOptions() {
    var privacyMethod = jQuery('input[name="cm-hipaa-forms-privacy-type"]:checked').val();
    var privacyLabel = jQuery('#cm-hipaa-forms-privacy-label').val();
    var privacyCopy = jQuery('#cm-hipaa-forms-privacy-copy').attr('value');
    var privacyLink = jQuery('#cm-hipaa-forms-privacy-link').val();
    var noticeEle = jQuery('#cm-hipaa-forms-privacy-feedback');

    if(!privacyCopy) {
        privacyCopy = '';
    }

    if(privacyMethod) {
        jQuery('input[name="privacy_notice_method"]').val(privacyMethod);
    }

    if(privacyLabel) {
        jQuery('input[name="privacy_notice_label"]').val(privacyLabel);
    }

    if(privacyLink) {
        jQuery('input[name="privacy_notice_link"]').val(privacyLink);
    }

    // ADD/UPDATE FORM SETTINGS TO HIDDEN OPTION INPUT
    jQuery('input[name="privacy_notice_copy"]').val(privacyCopy).promise().done(function() {
        // SAVE FORM
        noticeEle.html('<img src="' + hipaaScript.pluginUrl + '/images/loading/loading10.gif" />');
        jQuery('form#cm_hipaa_forms_options').ajaxSubmit({
            success: function(){
                noticeEle.html('Settings Saved');
            },
            error: function (errorThrown) {
                noticeEle.html(errorThrown);
            }
        });
    });
}

/*** SAVE CUSTOM STATUS OPTIONS ***/
function cmHipaaSaveStatusOptions() {
    var masterInput = jQuery('input[name="hipaa_custom_status_options"]');
    var masterEnabledInput = jQuery('input[name="hipaa_custom_status_enabled"]');
    var customStatusEnabled = jQuery('#cm-hipaa-forms-setting-enable-status').prop('checked');
    var optionInputs = jQuery('.cm-hipaa-forms-setting-status-option');
    var noticeEle = jQuery('#cm-hipaa-custom-status-feedback');
    var options = [];

    if(customStatusEnabled === true) {
        masterEnabledInput.val('yes');
    } else {
        masterEnabledInput.val('no');
    }

    // LOOP OPTION INPUTS
    optionInputs.each(function() {
        options.push(jQuery(this).val());
    });

    masterInput.val(options.join()).promise().done(function() {
        // SAVE FORM
        noticeEle.html('<img src="' + hipaaScript.pluginUrl + '/images/loading/loading10.gif" />');
        jQuery('form#cm_hipaa_forms_options').ajaxSubmit({
            success: function(){
                noticeEle.html('Settings Saved');

                // CLEAR STATUS UPDATE MESSAGE ON TIMEOUT
                setTimeout(function() {
                    noticeEle.fadeOut().promise().done(function() {
                        noticeEle.html('').show();
                    });
                }, 3000);
            },
            error: function (errorThrown) {
                noticeEle.html(errorThrown);
            }
        });
    });
}

/*** UPDATE FORM STATUS ***/
function cmHipaaUpdateFormStatus(formId, status) {
    var noticeEle = jQuery('.cm-hipaa-forms-custom-status-notice[data-form-id="' + formId + '"]');
    var nonce = hipaaScript.nonce;

    noticeEle.html('<img src="' + hipaaScript.pluginUrl + '/images/loading/loading10.gif" />');
    jQuery.ajax({
        method: 'POST',
        url: ajax.ajax_url,
        data: {
            'action': 'cm_hipaa_update_custom_status',
            'form_id': formId,
            'custom_status': status,
            'nonce': nonce
        },
        success: function (data) {
            var statusData = JSON.parse(data);

            if(statusData.success === 'success') {
                noticeEle.html('Status Updated');

                // UPDATE STATUS IN LIST VIEW
                jQuery('#cm-hipaa-form-id-' + formId + ' .cm-hipaa-submitted-form-custom-status').html(status);

                // CLEAR STATUS UPDATE MESSAGE ON TIMEOUT
                setTimeout(function() {
                    noticeEle.fadeOut().promise().done(function() {
                        noticeEle.html('').show();
                    });
                }, 3000);
            } else {
                noticeEle.html(statusData.error);
            }
        },
        error: function (errorThrown) {
            console.log(errorThrown);
            noticeEle.html(errorThrown);
        }
    });
}

// GET BAA PDF URL
function cmGetBaaPdf() {
    var nonce = hipaaScript.nonce;

    jQuery.ajax({
        url: ajax.ajax_url,
        data: {
            'action': 'cm_hipaa_get_baa_pdf',
            'nonce': nonce
        },
        success: function (data) {
            var resultData = JSON.parse(data);

            if(resultData.error && resultData.content) {
                jQuery('#cm-hipaa-forms-signed-baa').html(resultData.content);
            } else if(resultData.error) {
                jQuery('#cm-hipaa-forms-signed-baa').html(resultData.error);
            } else if(resultData.success) {
                jQuery('#cm-hipaa-forms-signed-baa').html('<div><a href="' + resultData.content + '" target="_blank">View BAA</a></div>');
            } else {
                jQuery('#cm-hipaa-forms-signed-baa').html('There was an unknown error, please submit a support ticket.');
            }
        },
        error: function (errorThrown) {
            console.log(errorThrown);
            jQuery('#cm-hipaa-forms-signed-baa').html(errorThrown);
        }
    });
}

// GET FORMS (DEPRECATED)
function cmGetForms(location, formName, firstName, lastName, phone, email, status, limit, page, resultsEle) {
    if(!page) {
        page = 0;
    }

    if(!limit) {
        limit = 10;
    }

    if(!resultsEle) {
        resultsEle = jQuery('#cm-submitted-forms-results');
    }

    resultsEle.html('<div class="cm-hipaa-forms-progress-wrapper"><div class="cm-hipaa-forms-progress"><div class="cm-hipaa-forms-progress-bar"><!--    <div class="cm-hipaa-forms-progress-label">10%</div>--></div></div></div>');
    cmHipaaMoveProgress();
    var nonce = hipaaScript.nonce;

    jQuery.ajax({
        url: ajax.ajax_url,
        data: {
            'action': 'cm_hipaa_get_forms',
            'location': location,
            'form_name': formName,
            'first_name': firstName,
            'last_name': lastName,
            'phone': phone,
            'email': email,
            'status': status,
            'limit': limit,
            'page': page,
            'nonce': nonce
        },
        success:function(data) {
            var resultData = JSON.parse(data);

            if(resultData.error && resultData.content) {
                // UPDATE RESULTS
                resultsEle.html(resultData.content);
            } else if(resultData.error) {
                // UPDATE RESULTS
                resultsEle.html(resultData.error);
            } else {
                jQuery('.cm-hipaa-forms-progress-wrapper').remove();
                var totalResults = resultData.total_results;

                // UPDATE TOTAL RESULTS VALUE
                jQuery('#cm-submitted-form-filter-total').val(totalResults);

                // ADD PAGINATION BUTTONS
                var resultsShown = +page * +limit;
                var totalResultsLeft = +totalResults - resultsShown;
                var totalPages = Math.ceil(+totalResults / +limit);
                var prevButton = '';
                var nextButton = '';
                var resultsCount;
                var paginationButtons = '';

                // SET PREVIOUS BUTTON
                if(page > 0) {
                    prevButton = '<div id="cm-hipaa-submitted-forms-prev" class="col_33""><div class="cm-pagination-button"><i class="material-icons">chevron_left</i> Previous</div></div>';
                } else {
                    prevButton = '<div id="cm-hipaa-submitted-forms-prev" class="col_33"></div>';
                }

                // SET NEXT BUTTON
                if(totalResultsLeft > limit) {
                    nextButton = '<div id="cm-hipaa-submitted-forms-next" class="col_33"><div class="cm-pagination-button">Next <i class="material-icons">chevron_right</i></div></div>';
                } else {
                    nextButton = '<div id="cm-hipaa-submitted-forms-next" class="col_33"></div>';
                }

                // SET PAGE COUNT (PAGE X OF X)
                if(totalPages === parseInt(totalPages, 10) && totalPages > 0) {
                    resultsCount = '<div class="cm-hipaa-submitted-forms-count col_33">Page ' + (parseInt(page, 10) + 1) + ' of ' + totalPages + '</div>';
                }

                // SHOW PAGINATION BUTTONS IF NEEDED
                if(prevButton || nextButton) {
                    paginationButtons = '<div class="cm-submitted-forms-pagination-wrapper"><div class="cm-submitted-forms-pagination grid_row">' + prevButton + resultsCount + nextButton + '</div></div>';
                }

                // UPDATE RESULTS
                resultsEle.html(resultData.content + ' ' + paginationButtons);

                // UPDATE PAGE
                jQuery('#cm-submitted-form-filter-page').val(page);
            }
        },
        error: function(errorThrown){
            console.log(errorThrown);
        }
    });
}

// GET SUBMITTED FORMS LIST
function cmGetSubmittedFormsList(location, formName, firstName, lastName, phone, email, status, limit, page, resultsEle) {
    if(!page) {
        page = 0;
    }

    if(!limit) {
        limit = 10;
    }

    if(!resultsEle) {
        resultsEle = jQuery('#cm-submitted-forms-results');
    }

    resultsEle.html('<div class="cm-hipaa-forms-progress-wrapper"><div class="cm-hipaa-forms-progress"><div class="cm-hipaa-forms-progress-bar"><!--    <div class="cm-hipaa-forms-progress-label">10%</div>--></div></div></div>');
    cmHipaaMoveProgress();
    var nonce = hipaaScript.nonce;

    jQuery.ajax({
        url: ajax.ajax_url,
        data: {
            'action': 'cm_hipaa_get_submitted_forms_list',
            'location': location,
            'form_name': formName,
            'first_name': firstName,
            'last_name': lastName,
            'phone': phone,
            'email': email,
            'status': status,
            'limit': limit,
            'page': page,
            'nonce': nonce
        },
        success:function(data) {
            var resultData = JSON.parse(data);

            if(resultData.error && resultData.content) {
                // UPDATE RESULTS
                resultsEle.html(resultData.content);
            } else if(resultData.error) {
                // UPDATE RESULTS
                resultsEle.html(resultData.error);
            } else {
                jQuery('.cm-hipaa-forms-progress-wrapper').remove();
                var totalResults = resultData.total_results;

                // UPDATE TOTAL RESULTS VALUE
                jQuery('#cm-submitted-form-filter-total').val(totalResults);

                // ADD PAGINATION BUTTONS
                var resultsShown = +page * +limit;
                var totalResultsLeft = +totalResults - resultsShown;
                var totalPages = Math.ceil(+totalResults / +limit);
                var prevButton = '';
                var nextButton = '';
                var resultsCount;
                var paginationButtons = '';

                // SET PREVIOUS BUTTON
                if(page > 0) {
                    prevButton = '<div id="cm-hipaa-submitted-forms-prev" class="col_33""><div class="cm-pagination-button"><i class="material-icons">chevron_left</i> Previous</div></div>';
                } else {
                    prevButton = '<div id="cm-hipaa-submitted-forms-prev" class="col_33"></div>';
                }

                // SET NEXT BUTTON
                if(totalResultsLeft > limit) {
                    nextButton = '<div id="cm-hipaa-submitted-forms-next" class="col_33"><div class="cm-pagination-button">Next <i class="material-icons">chevron_right</i></div></div>';
                } else {
                    nextButton = '<div id="cm-hipaa-submitted-forms-next" class="col_33"></div>';
                }

                // SET PAGE COUNT (PAGE X OF X)
                if(totalPages === parseInt(totalPages, 10) && totalPages > 0) {
                    resultsCount = '<div class="cm-hipaa-submitted-forms-count col_33">Page ' + (parseInt(page, 10) + 1) + ' of ' + totalPages + '</div>';
                }

                // SHOW PAGINATION BUTTONS IF NEEDED
                if(prevButton || nextButton) {
                    paginationButtons = '<div class="cm-submitted-forms-pagination-wrapper"><div class="cm-submitted-forms-pagination grid_row">' + prevButton + resultsCount + nextButton + '</div></div>';
                }

                // UPDATE RESULTS
                resultsEle.html(resultData.content + ' ' + paginationButtons);

                // UPDATE PAGE
                jQuery('#cm-submitted-form-filter-page').val(page);
            }
        },
        error: function(errorThrown){
            console.log(errorThrown);
        }
    });
}

// GET SUBMITTED FORM
function cmGetSubmittedForm(formId) {
    var resultsEle = jQuery('#cm-submitted-form-wrapper-' + formId);
    var nonce = hipaaScript.nonce;

    // SHOW LOADING ICON
    resultsEle.html('<div class="cm-hipaa-forms-loading-form"><img style="text-align:center;margin:auto;" src="' + hipaaScript.pluginUrl + '/images/loading/loading14.gif" /></div>');

    jQuery.ajax({
        url: ajax.ajax_url,
        data: {
            'action': 'cm_hipaa_get_submitted_form',
            'form_id': formId,
            'nonce': nonce
        },
        success:function(data) {
            var resultData = JSON.parse(data);

            if(resultData.error) {
                resultsEle.html(resultData.error);
            } else {
                // ADD FORM
                resultsEle.html(resultData.form);
            }

            // GET FORM HISTORY
            var historyLimit = resultsEle.parent().parent().find('.cm-hipaa-submitted-form-history-limit').val();
            if(!historyLimit) {
                historyLimit = 10;
            }

            cmGetFormHistory(formId, 0, historyLimit);
        },
        error: function(errorThrown){
            console.log(errorThrown);
        }
    });
}

// GET SUBMITTED FORM HISTORY
function cmGetFormHistory(formId, page, limit) {
    var resultsEle = jQuery('#cm-submitted-form-history-wrapper-' + formId);
    var nonce = hipaaScript.nonce;

    if(!page) {
        page = 0;
    }

    if(!limit) {
        limit = 10;
    }

    // SHOW LOADING ICON
    resultsEle.html('<div class="cm-hipaa-forms-loading-form"><img style="text-align:center;margin:auto;" src="' + hipaaScript.pluginUrl + '/images/loading/loading14.gif" /></div>');

    jQuery.ajax({
        url: ajax.ajax_url,
        data: {
            'action': 'cm_hipaa_get_form_history',
            'form_id': formId,
            'page': page,
            'limit': limit,
            'nonce': nonce
        },
        success:function(data) {
            var resultData = JSON.parse(data);

            if(resultData.error) {
                resultsEle.html(resultData.error);
            } else {
                // ADD FORM
                //resultsEle.html(resultData.history);


                var totalResults = resultData.total_results;

                // UPDATE TOTAL RESULTS VALUE
                jQuery('#cm-submitted-form-history-total').val(totalResults);

                // ADD PAGINATION BUTTONS
                var resultsShown = +page * +limit;
                var totalResultsLeft = +totalResults - resultsShown;
                var totalPages = Math.ceil(+totalResults / +limit);
                var newPage = (parseInt(page, 10) + 1);
                var prevButton = '';
                var nextButton = '';
                var resultsCount;
                var paginationButtons = '';

                // SET PREVIOUS BUTTON
                if(page > 0) {
                    prevButton = '<div class="cm-hipaa-submitted-form-history-pag-button-wrapper col_33""><div class="cm-hipaa-submitted-form-history-pag-button cm-pagination-button" data-form-id="' + formId + '" data-direction="previous"><i class="material-icons">chevron_left</i> Previous</div></div>';
                } else {
                    prevButton = '<div class="cm-hipaa-submitted-form-history-pag-button-wrapper col_33"></div>';
                }

                // SET NEXT BUTTON
                if(totalResultsLeft > limit) {
                    nextButton = '<div class="cm-hipaa-submitted-form-history-pag-button-wrapper col_33""><div class="cm-hipaa-submitted-form-history-pag-button cm-pagination-button" data-form-id="' + formId + '" data-direction="next">Next <i class="material-icons">chevron_right</i></div></div>';
                } else {
                    nextButton = '<div class="cm-hipaa-submitted-form-history-pag-button-wrapper col_33"></div>';
                }

                // SET PAGE COUNT (PAGE X OF X)
                if(totalPages === parseInt(totalPages, 10) && totalPages > 0) {
                    resultsCount = '<div class="cm-hipaa-submitted-form-history-count col_33">Page ' + newPage + ' of ' + totalPages + '</div>';
                }

                // SHOW PAGINATION BUTTONS IF NEEDED
                if(prevButton || nextButton) {
                    paginationButtons = '<div class="cm-submitted-form-history-pagination-wrapper"><div class="cm-submitted-form-history-pagination grid_row">' + prevButton + resultsCount + nextButton + '</div></div>';
                }

                // ADD PAGE INPUT
                var pageInput = '<input type="hidden" class="cm-submitted-form-history-page" data-form-id="' + formId + '" value="' + newPage + '" />';

                // UPDATE RESULTS
                resultsEle.html(resultData.history + paginationButtons + pageInput);
            }
        },
        error: function(errorThrown){
            console.log(errorThrown);
        }
    });
}

// GET LOGS
function cmGetLogs(startDate, endDate, limit, page, resultsEle) {
    if(!page) {
        page = 0;
    }

    if(!limit) {
        limit = 10;
    }

    if(!resultsEle) {
        resultsEle = jQuery('#cm-hipaa-forms-logs-results');
    }

    resultsEle.html('<div class="cm-hipaa-forms-progress-wrapper"><div class="cm-hipaa-forms-progress"><div class="cm-hipaa-forms-progress-bar"><!--    <div class="cm-hipaa-forms-progress-label">10%</div>--></div></div></div>');
    cmHipaaMoveProgress();
    var nonce = hipaaScript.nonce;

    jQuery.ajax({
        url: ajax.ajax_url,
        data: {
            'action': 'cm_hipaa_get_logs',
            'first_name': startDate,
            'last_name': endDate,
            'limit': limit,
            'page': page,
            'nonce': nonce
        },
        success:function(data) {
            var resultData = JSON.parse(data);

            jQuery('.cm-hipaa-forms-progress-wrapper').remove();
            var totalResults = resultData.total_results;

            // UPDATE TOTAL RESULTS VALUE
            jQuery('#cm-hipaa-forms-logs-filter-total').val(totalResults);

            // ADD PAGINATION BUTTONS
            var resultsShown = +page * +limit;
            var totalResultsLeft = +totalResults - resultsShown;
            var totalPages = Math.ceil(+totalResults / +limit);
            var prevButton = '';
            var nextButton = '';
            var paginationButtons = '';

            // SET PREVIOUS BUTTON
            if(page > 0) {
                prevButton = '<div id="cm-hipaa-forms-logs-prev" class="col_33""><div class="cm-pagination-button"><i class="material-icons">chevron_left</i> Previous</div></div>';
            } else {
                prevButton = '<div id="cm-hipaa-forms-logs-prev" class="col_33"></div>';
            }

            // SET NEXT BUTTON
            if(totalResultsLeft > limit) {
                nextButton = '<div id="cm-hipaa-forms-logs-next" class="col_33"><div class="cm-pagination-button">Next <i class="material-icons">chevron_right</i></div></div>';
            } else {
                nextButton = '<div id="cm-hipaa-forms-logs-next" class="col_33"></div>';
            }

            // SET PAGE COUNT (PAGE X OF X)
            if(totalPages === parseInt(totalPages, 10) && totalPages > 0) {
                var resultsCount = '<div class="cm-hipaa-forms-logs-count col_33">Page ' + (parseInt(page, 10) + 1) + ' of ' + totalPages + '</div>';
            }

            // SHOW PAGINATION BUTTONS IF NEEDED
            if(prevButton || nextButton) {
                paginationButtons = '<div class="cm-hipaa-forms-logs-pagination-wrapper"><div class="cm-hipaa-forms-logs-pagination grid_row">' + prevButton + resultsCount + nextButton + '</div></div>';
            }

            // UPDATE RESULTS
            resultsEle.html(resultData.content + ' ' + paginationButtons);
            console.log(page);
            // UPDATE PAGE
            jQuery('#cm-hipaa-forms-logs-filter-page').val(page);
        },
        error: function(errorThrown){
            console.log(errorThrown);
        }
    });
}

// SUBMIT TICKET
function cmSubmitTicket(priorityEle, channelEle, subjectEle, messageEle, noticeEle, priority, channel, subject, message, parentId) {
    // RESET VALIDATION
    if(priorityEle) {
        priorityEle.css('border', '1px solid #ddd');
    }
    if(channelEle) {
        channelEle.css('border', '1px solid #ddd');
    }
    if(subjectEle) {
        subjectEle.css('border', '1px solid #ddd');
    }
    if(messageEle) {
        messageEle.css('border', '1px solid #ddd');
    }
    if(noticeEle) {
        noticeEle.html('test');
    }

    // VALIDATE
    if(priorityEle && !priority) {
        priorityEle.css('border', '1px solid red');
        noticeEle.html('Please select a priority level');
    } else if(channelEle && !channel) {
        channelEle.css('border', '1px solid red');
        noticeEle.html('Please select a reason');
    } else if(subjectEle && !subject) {
        subjectEle.css('border', '1px solid red');
        noticeEle.html('Please enter a subject');
    } else if(messageEle && !message) {
        messageEle.css('border', '1px solid red');
        noticeEle.html('Please enter a message');
    } else {
        noticeEle.html('<img src="' + hipaaScript.pluginUrl + '/images/loading/loading10.gif" />');
        var nonce = hipaaScript.nonce;

        jQuery.ajax({
            method: 'POST',
            url: ajax.ajax_url,
            data: {
                'action': 'cm_hipaa_submit_support_ticket',
                'priority': priority,
                'channel': channel,
                'subject': subject,
                'message': message,
                'parent_id': parentId,
                'nonce': nonce
            },
            success: function (data) {
                var resultData = JSON.parse(data);

                if(resultData.error) {
                    noticeEle.html(resultData.error);
                } else if(resultData.success) {
                    noticeEle.html(resultData.success);
                }

                // UPDATE SUPPORT TICKETS
                cmGetTickets('open', jQuery('#cm-hipaa-forms-open-tickets'));
                cmGetTickets('closed', jQuery('#cm-hipaa-forms-closed-tickets'));
            },
            error: function (errorThrown) {
                console.log(errorThrown);
                noticeEle.html(errorThrown);
            }
        });
    }
}

// GET TICKETS
function cmGetTickets(status, updateEle) {
    updateEle.html('<img src="' + hipaaScript.pluginUrl + '/images/loading/loading10.gif" />');
    var nonce = hipaaScript.nonce;

    jQuery.ajax({
        method: 'POST',
        url: ajax.ajax_url,
        data: {
            'action': 'cm_hipaa_get_support_tickets',
            'status': status,
            'nonce': nonce
        },
        success: function (data) {
            var ticketsData = JSON.parse(data);
            var tickets;
            if(ticketsData.success && ticketsData.success === 'success') {
                tickets = ticketsData.tickets;
            } else if(ticketsData.error) {
                tickets = ticketsData.error;
            } else {
                tickets = 'There was an error';
            }

            updateEle.html(tickets);
        },
        error: function (errorThrown) {
            console.log(errorThrown);
            updateEle.html(errorThrown);
        }
    });
}

// CLOSE TICKET
function cmCloseTicket(ticketId, noticeEle) {
    var nonce = hipaaScript.nonce;

    jQuery.ajax({
        method: 'POST',
        url: ajax.ajax_url,
        data: {
            'action': 'cm_hipaa_close_support_ticket',
            'ticket_id': ticketId,
            'nonce': nonce
        },
        success: function (data) {
            if(data === 'success') {
                // UPDATE OPEN SUPPORT TICKETS
                cmGetTickets('open', jQuery('#cm-hipaa-forms-open-tickets'));

                // UPDATE CLOSED SUPPORT TICKETS
                cmGetTickets('closed', jQuery('#cm-hipaa-forms-closed-tickets'));
            } else {
                noticeEle.html(data);
            }
        },
        error: function (errorThrown) {
            console.log(errorThrown);
            noticeEle.html(errorThrown);
        }
    });
}

/*** SUBMIT FORM NOTE ***/
function cmSubmitNote(formId, userId, name, email, note, noteEle, notesEle) {
    var nonce = hipaaScript.nonce;

    notesEle.html('<img class="cm-hipaa-forms-loading-center" src="' + hipaaScript.pluginUrl + '/images/loading/loading16.gif" />');
    jQuery.ajax({
        method: 'POST',
        url: ajax.ajax_url,
        data: {
            'action': 'cm_hipaa_submit_note',
            'form_id': formId,
            'user_id': userId,
            'name': name,
            'email': email,
            'note': note,
            'nonce': nonce
        },
        success: function (data) {
            var noteData = JSON.parse(data);

            if(noteData.success === 'success') {
                // CLEAR NOTE TEXTAREA
                noteEle.val('');

                // UPDATE NOTES
                cmGetNotes(formId, notesEle);
            } else {
                notesEle.html(noteData.error);
            }
        },
        error: function (errorThrown) {
            console.log(errorThrown);
            notesEle.html(errorThrown);
        }
    });
}

/*** GET FORM NOTES ***/
function cmGetNotes(formId, notesEle) {
    var nonce = hipaaScript.nonce;

    notesEle.html('<img class="cm-hipaa-forms-loading-center" src="' + hipaaScript.pluginUrl + '/images/loading/loading16.gif" />');
    jQuery.ajax({
        method: 'POST',
        url: ajax.ajax_url,
        data: {
            'action': 'cm_hipaa_get_notes',
            'form_id': formId,
            'nonce': nonce
        },
        success: function (data) {
            var notesData = JSON.parse(data);

            if(notesData) {
                notesEle.html(notesData.notes);
            } else {
                notesEle.html('No results');
            }
        },
        error: function (errorThrown) {
            console.log(errorThrown);
            notesEle.html(errorThrown);
        }
    });
}

/*** EXPORT FORM ***/
function cmHipaaExportForm(formId) {
    var nonce = hipaaScript.nonce;
    var resultsEle = jQuery('.cm-hipaa-submitted-form-export-results[data-form-id="' + formId + '"]');

    resultsEle.html('<img class="cm-hipaa-forms-loading-center" src="' + hipaaScript.pluginUrl + '/images/loading/loading16.gif" />');
    jQuery.ajax({
        method: 'POST',
        url: ajax.ajax_url,
        data: {
            'action': 'cm_hipaa_export_form',
            'form_id': formId,
            'nonce': nonce
        },
        success: function (data) {
            //console.log(data);
            var exportData = JSON.parse(data);
            var formId = exportData.form_id;
            /* NOT CURRENTLY NEEDED
            var formName = exportData.form_name.replace(/ /g,"_");
            var firstName = exportData.first_name;
            var lastName = exportData.last_name;
            */
            var formFields = exportData.fields;

            if(exportData) {
                // TODO: FORMAT FIELDS AND REPLACE FAKE PATHS AND CHECKMARK IMAGES
                var csv = '';

                jQuery.each(formFields, function() {
                    // Loop the array of objects
                    var fields = jQuery(this);

                    for(var row = 0; row < fields.length; row++) {
                        var keysAmount = Object.keys(fields[row]).length;
                        var keysCounter = 0;
                        var valuesCounter = 0;

                        // If this is the first row, generate the headings
                        if(row === 0){
                            // Loop each property of the object
                            for(var key in fields[row]) {
                                // This is to not add a comma at the last cell
                                // The '\r\n' adds a new line
                                csv += key + (keysCounter+1 < keysAmount ? ',' : '\r\n' );
                                keysCounter++;
                            }
                        }

                        for(var key in fields[row]) {
                            var fieldValue;

                            if(fields[row][key]) {
                                fieldValue = fields[row][key].trim();
                            } else {
                                fieldValue = 'NA';
                            }

                            csv += fieldValue + (valuesCounter+1 < keysAmount ? ',' : '\r\n' );
                            valuesCounter++;
                        }

                        keysCounter = 0;
                        valuesCounter = 0;
                    }
                });

                // Once we are done looping, download the .csv by creating a link
                var link = document.createElement('a');
                link.id = 'download-form-csv-' + formId;
                link.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(csv));
                link.setAttribute('download', 'hipaa_form_' + formId + '.csv');
                document.body.appendChild(link);
                document.querySelector('#download-form-csv-' + formId).click();

                resultsEle.html('');
            } else {
                resultsEle.html('No results');
            }
        },
        error: function (errorThrown) {
            console.log(errorThrown);
            resultsEle.html(errorThrown);
        }
    });
}

/*** EXPORT FORM NOTES ***/
function cmHipaaExportFormNotes(formId) {
    var nonce = hipaaScript.nonce;
    var resultsEle = jQuery('.cm-hipaa-submitted-form-export-results[data-form-id="' + formId + '"]');

    resultsEle.html('<img class="cm-hipaa-forms-loading-center" src="' + hipaaScript.pluginUrl + '/images/loading/loading16.gif" />');
    jQuery.ajax({
        method: 'POST',
        url: ajax.ajax_url,
        data: {
            'action': 'cm_hipaa_export_form_notes',
            'form_id': formId,
            'nonce': nonce
        },
        success: function (data) {
            var exportData = JSON.parse(data);
            var formId = exportData.form_id;
            var formNotes = exportData.notes;

            if(exportData) {
                var csv = '';
                var heading = false;

                jQuery.each(formNotes, function() {
                    // Loop the array of objects
                    var notes = jQuery(this);

                    for(var row = 0; row < notes.length; row++) {
                        var keysAmount = Object.keys(notes[row]).length;
                        var keysCounter = 0;
                        var valuesCounter = 0;

                        // If this is the first row, generate the headings
                        if(row === 0 && heading === false){
                            // Loop each property of the object
                            for(var key in notes[row]) {
                                // This is to not add a comma at the last cell
                                // The '\r\n' adds a new line
                                csv += key + (keysCounter+1 < keysAmount ? ',' : '\r\n' );
                                keysCounter++;
                            }

                            heading = true;
                        }

                        for(var key in notes[row]) {
                            var noteValue;

                            if(notes[row][key]) {
                                noteValue = notes[row][key].trim();
                            } else {
                                noteValue = 'NA';
                            }

                            csv += noteValue + (valuesCounter+1 < keysAmount ? ',' : '\r\n' );
                            valuesCounter++;
                        }


                        keysCounter = 0;
                        valuesCounter = 0;
                    }
                });

                // Once we are done looping, download the .csv by creating a link
                var link = document.createElement('a');
                link.id = 'download-notes-csv-' + formId;
                link.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(csv));
                link.setAttribute('download', 'hipaa_form_notes_' + formId + '.csv');
                document.body.appendChild(link);
                document.querySelector('#download-notes-csv-' + formId).click();

                resultsEle.html('');
            } else {
                resultsEle.html('No results');
            }
        },
        error: function (errorThrown) {
            console.log(errorThrown);
            resultsEle.html(errorThrown);
        }
    });
}

/*** EXPORT FORM HISTORY ***/
function cmHipaaExportFormHistory(formId) {
    var nonce = hipaaScript.nonce;
    var resultsEle = jQuery('.cm-hipaa-submitted-form-export-results[data-form-id="' + formId + '"]');

    resultsEle.html('<img class="cm-hipaa-forms-loading-center" src="' + hipaaScript.pluginUrl + '/images/loading/loading16.gif" />');
    jQuery.ajax({
        method: 'POST',
        url: ajax.ajax_url,
        data: {
            'action': 'cm_hipaa_export_form_history',
            'form_id': formId,
            'nonce': nonce
        },
        success: function (data) {
            var exportData = JSON.parse(data);
            var formId = exportData.form_id;
            var formHistory = exportData.history;

            if(exportData) {
                var csv = '';
                var heading = false;

                jQuery.each(formHistory, function() {
                    // Loop the array of objects
                    var history = jQuery(this);

                    for(var row = 0; row < history.length; row++) {
                        var keysAmount = Object.keys(history[row]).length;
                        var keysCounter = 0;
                        var valuesCounter = 0;

                        // If this is the first row, generate the headings
                        if(row === 0 && heading === false){
                            // Loop each property of the object
                            for(var key in history[row]) {
                                // This is to not add a comma at the last cell
                                // The '\r\n' adds a new line
                                csv += key + (keysCounter+1 < keysAmount ? ',' : '\r\n' );
                                keysCounter++;
                            }

                            heading = true;
                        }

                        for(var key in history[row]) {
                            var historyValue;

                            if(history[row][key]) {
                                historyValue = history[row][key].trim();
                            } else {
                                historyValue = 'NA';
                            }

                            csv += historyValue + (valuesCounter+1 < keysAmount ? ',' : '\r\n' );
                            valuesCounter++;
                        }


                        keysCounter = 0;
                        valuesCounter = 0;
                    }
                });

                // Once we are done looping, download the .csv by creating a link
                var link = document.createElement('a');
                link.id = 'download-history-csv-' + formId;
                link.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(csv));
                link.setAttribute('download', 'hipaa_form_history_' + formId + '.csv');
                document.body.appendChild(link);
                document.querySelector('#download-history-csv-' + formId).click();

                resultsEle.html('');
            } else {
                resultsEle.html('No results');
            }
        },
        error: function (errorThrown) {
            console.log(errorThrown);
            resultsEle.html(errorThrown);
        }
    });
}