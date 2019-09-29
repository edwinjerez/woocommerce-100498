/**
 * Created by Spencer on 7/16/2018.
 * V2.2.5
 */

// IE FIX FOR startsWith METHOD
if (!String.prototype.startsWith) {
    String.prototype.startsWith = function(searchString, position) {
        position = position || 0;
        return this.indexOf(searchString, position) === position;
    };
}

jQuery(document).ready(function() {
    /*** CHECK IF FILE UPLOAD ENABLED ***/
    var fileUploadEnabled = cmHipaaScript.fileUpload;

    /*** CHECK IF FORMS ON THE PAGE ***/
    var forms = jQuery('form');
    var formBuilder = cmHipaaScript.formBuilder;

    var calderaEnabledForms;
    if(cmHipaaScript.calderaEnabledForms) {
        calderaEnabledForms = JSON.parse(cmHipaaScript.calderaEnabledForms);
    }

    var gravityEnabledForms;
    if(cmHipaaScript.gravityEnabledForms) {
        gravityEnabledForms = JSON.parse(cmHipaaScript.gravityEnabledForms);
    }

    var enabledFormsSettings;
    if(cmHipaaScript.enabledFormsSettings) {
        enabledFormsSettings = JSON.parse(cmHipaaScript.enabledFormsSettings);
    }

    formIds = [];
    // LOOP ENABLED CALDERA FORMS AND PUSH IDS TO ARRAY - DEPRECATED
    for(var i = 0; i < calderaEnabledForms.length; i++){
        if(calderaEnabledForms[i]) {
            formIds.push(calderaEnabledForms[i]);
        }
    }

    // LOOP ENABLED GRAVITY FORMS AND PUSH IDS TO ARRAY - DEPRECATED
    for(var i = 0; i < gravityEnabledForms.length; i++){
        if(gravityEnabledForms[i]) {
            formIds.push('gform_' + gravityEnabledForms[i]);
        }
    }

    /*** LOOP FORMS ***/
    forms.each(function() {
        var form = jQuery(this);
        var formMainId = form.attr('id');
        var formId = form.attr('data-form-id');
        if(!formId) {
            formId = formMainId;
        }

        // SET NEW SUBMIT BUTTON
        var newSubmitButton;
        if(formId && formId.startsWith('gform_')) {
            newSubmitButton = '<div class="cm-hipaa-forms-submit gravity cm-hipaa-forms-button active"><i class="material-icons">lock</i> SUBMIT</div>';
        } else {
            newSubmitButton = '<div class="cm-hipaa-forms-submit caldera cm-hipaa-forms-button active"><i class="material-icons">lock</i> SUBMIT</div>';
        }

        var showSig;
        if(!enabledFormsSettings) {
            // IF NEW ENABLED FORMS SETTINGS DOESN'T EXIST SET SIGNATURE TO YES
            showSig = 'yes';
        }

        // CHECK IF FORM OBJECT EXISTS IN ARRAY - NEW FORMS SETTINGS JSON OBJECT ARRAY METHOD
        var formFound;
        if(cmHipaaScript.enabledFormsSettings) {
            enabledFormsSettings.some(function (el) {
                if (el.id === formId && el.enabled === 'yes' || el.form_builder === 'gravity' && 'gform_' + el.id === formId && el.enabled === 'yes') {
                    formFound = true;

                    // IF SIGNATURE OPTION HAS VALUE SET IT
                    if(el.show_signature) {
                        showSig = el.show_signature;
                    }
                }
            });
        }

        // IF FORM ID IN DEPRECATED METHOD OR FORM FOUND IN NEW JSON OBJECT ARRAY METHOD
        if(jQuery.inArray(formId, formIds) !== -1 || formFound) {
            // CHECK IF SSL ENABLED
            var protocol = document.location.protocol;

            // SET INITIAL VALUES
            var submitButton = jQuery(this).find('input[type="submit"]');
            var gravityFileUploadEle = jQuery(this).find('.ginput_container_fileupload');
            var calderaFileUploadEle = jQuery(this).find('.file-prevent-overflow').parent();
            var calderaMultiFileUploadEle = jQuery(this).find('.cf-uploader-trigger').parent();
            var calderaAdvancedFileUploadEle = jQuery(this).find('.cf2-field-wrapper'); // EXPERIMENTAL - WILL OTHER NON-FILE UPLOAD FIELDS USE THIS CLASS IN THE FUTURE?
            if(calderaAdvancedFileUploadEle.length > 0) {
                calderaMultiFileUploadEle.push(calderaAdvancedFileUploadEle); // EXPERIMENTAL
            }
            var gravitySflEle = jQuery(this).find('.gform_save_link');
            var calderaFormPages;
            var gravityFormPages;
            var appendEle;

            calderaFormPages = jQuery(this).find('[data-formpage]');
            gravityFormPages = jQuery(this).find('.gform_page');

            // CHECK IF MULTI-PAGE FORM
            if(calderaFormPages.length) {
                appendEle = calderaFormPages.last();
            } else if(gravityFormPages.length) {
                appendEle = gravityFormPages.last();

                // STRIP PREVIOUS & NEXT ONCLICK & ONKEYPRESS METHODS
                jQuery(this).find('.gform_next_button, .gform_previous_button').removeAttr('onclick').removeAttr('onkeypress');

                // LOOP PAGES AND ADD PREVIOUS/NEXT BUTTONS
                gravityFormPages.each(function(index) {
                    index = +index+1;
                    var pageFooter = jQuery(this).find('.gform_page_footer');
                    var inputIndex = jQuery(this).find('.gform_page_fields input[tabindex]').last().attr('tabindex');
                    var previousButtonIndex;

                    // PREPEND PREVIOUS BUTTON TO LAST PAGE (NOT SURE WHY ONLY THAT PAGE DOESN'T GET IT)
                    if(index > 1 && index === gravityFormPages.length) {
                        previousButtonIndex = +inputIndex+1;

                        pageFooter.prepend('<input type="button" id="gform_previous_button_12" class="gform_previous_button button" value="Previous" tabindex="' + previousButtonIndex + '">');
                    }
                });
            } else {
                appendEle = form;
            }

            // SET LOADING ICON
            appendEle.append('<div class="cm-hipaa-forms-loading"><img src="' + cmHipaaScript.pluginUrl + '/images/loading/loading16.gif" alt="Securing Form" /><br /><br />SECURING FORM</div>');

            // ENSURE SSL ENABLED IF FORM SELECTED AS HIPAA COMPLIANT (checks both Wordpress is_ssl AND document protocol for good measure)
            if(protocol === 'https:' && cmHipaaScript.ssl === '1') {
                /*** VALIDATE ACCOUNT ***/
                jQuery.ajax({
                    method: 'POST',
                    url: ajax.ajax_url,
                    data: {
                        'action': 'cm_hipaa_validate_account',
                        'nonce': cmHipaaScript.nonce
                    },
                    success: function (data) {
                        // REMOVE LOADING ICON
                        jQuery('.cm-hipaa-forms-loading').remove();

                        var resultData = JSON.parse(data);

                        if(resultData.success === 'success') {
                            // CHECK IF STAGING
                            var stagingMessage = '';
                            if(resultData.staging) {
                                stagingMessage = resultData.staging;
                            }

                            // SET BASIC PRODUCT BADGE
                            var basicBadge = '';
                            if(resultData.product === 'basic' || resultData.license_status === 'expired') {
                                basicBadge = '<div class="cm-hipaa-powered-by-badge">POWERED BY <a href="https://www.hipaaforms.online" target="_blank">HIPAA FORMS</a> <i>for Wordpress</i></div>';
                            }

                            // IF BASIC PRODUCT CHECK IF MAX SUBMISSIONS REACHED
                            var maxSubmissionsMessage;
                            if(resultData.product === 'basic' && resultData.this_months_submissions >= 25) {
                                // CHANGING THIS WILL ENABLE YOUR FORM BUT THE SUBMISSIONS WILL STILL BE BLOCKED BY THE API. UPGRADING IS ONLY $50/MO, CODE MONKEYS HAVE BILLS TO PAY TOO ;).
                                maxSubmissionsMessage = '<div class="cm-hipaa-field-error">The maximum number of forms have been submitted for this month, please try again next month.</div>' + basicBadge;
                            }

                            if(submitButton.length > 0) {
                                // DISABLE & REMOVE ORIGINAL SUBMIT BUTTON TO PREVENT ACCIDENTAL GENERAL SUBMISSION
                                submitButton.attr('disabled', 'disabled').remove();
                            }

                            // CHECK IF GRAVITY FILE UPLOAD FIELD & FILE UPLOAD URL EXISTS & REPLACE WITH MESSAGE IF NO UPLOAD URL
                            if(gravityFileUploadEle && fileUploadEnabled !== 'yes') {
                                gravityFileUploadEle.html('<div class="cm-hipaa-field-error">Default file upload functionality is not HIPAA compliant & has been disabled, please upgrade HIPAA Forms for file upload capability.</div>');
                            } else if(gravityFileUploadEle && fileUploadEnabled === 'yes') {
                                // LOOP FILE INPUT FIELDS & REPLACE MULTI-FILE UPLOAD IF EXISTS
                                jQuery.each(gravityFileUploadEle, function() {
                                    var gfMultiFile = jQuery(this).find('.gform_fileupload_multifile');
                                    var fieldId = jQuery(this).parent().attr('id');
                                    var fileInputId = fieldId.replace('field', 'input');
                                    var fileInputEnd = fieldId.split('_').pop();
                                    var fileInputName = 'input_' + fileInputEnd;
                                    var fileInputDescribed = fieldId.replace('field', 'extensions_message');
                                    var tabIndex = jQuery(this).find('.gform_button_select_files').attr('tabindex');

                                    if(gfMultiFile.length > 0) {
                                        gfMultiFile.replaceWith('<input name="' + fileInputName + '" id="' + fileInputId + '" type="file" class="medium" aria-describedby="' + fileInputDescribed + '" tabindex="' + tabIndex + '"><div class="cm-hipaa-gf-multifile-upload-button">Add Another File</div>');
                                    }
                                });
                            }

                            // CHECK IF CALDERA FILE UPLOAD FIELD & FILE UPLOAD URL EXISTS & REPLACE WITH MESSAGE IF NO UPLOAD URL
                            if((calderaFileUploadEle || calderaMultiFileUploadEle) && fileUploadEnabled !== 'yes') {
                                calderaFileUploadEle.html('<div class="cm-hipaa-field-error">Default file upload functionality is not HIPAA compliant & has been disabled, please upgrade HIPAA Forms for file upload capability.</div>');
                            } else if(calderaMultiFileUploadEle && fileUploadEnabled === 'yes') {
                                // LOOP FILE INPUT FIELDS & REPLACE MULTI-FILE UPLOAD IF EXISTS
                                jQuery.each(calderaMultiFileUploadEle, function() {
                                    var fieldId = jQuery(this).parent().attr('data-field-wrapper');

                                    jQuery(this).replaceWith('<div class="file-prevent-overflow"><div id="' + fieldId + '_1_file_list" class="cf-multi-uploader-list"></div><input type="file" name="' + fieldId + '" value="" data-field="' + fieldId + '" class="" id="' + fieldId + '_1" aria-labelledby="' + fieldId + 'Label"><input type="hidden" name="' + fieldId + '"></div><div class="cm-hipaa-cf-multifile-upload-button">Add Another File</div>');
                                });
                            }

                            // CHECK IF GRAVITY FORMS "SAVE FOR LATER" LINK EXISTS AND REPLACE WITH NON-COMPLIANT MESSAGE
                            if(gravitySflEle) {
                                gravitySflEle.replaceWith('<div class="cm-hipaa-field-error">"Save For Later" has been disabled due to HIPAA non-compliance</div>');
                            }

                            var sigField = '';
                            if(showSig === 'yes') {
                                sigField = '<div class="cm-hipaa-form-signature-wrapper"><div class="cm-hipaa-form-signature-inner"><div class="cm-hipaa-form-signature" style="min-height:150px;"></div><div class="cm-hipaa-form-signature-label"><span class="cm-hipaa-form-signature-label-text">To sign left click and drag mouse or use your finger or stylus if touch screen</span> <span class="cm-hipaa-form-signature-reset cm-ghost-button" class="cm-hipaa-forms-button">RESET</span><span style="clear:both;"></span></div></div></div>';
                            }

                            // SET HIPAA PRIVACY NOTICE
                            var cmPrivacyNoticeMethod = cmHipaaScript.privacyNoticeMethod;
                            var cmPrivacyNoticeLabel = cmHipaaScript.privacyNoticeLabel;
                            var cmPrivacyNoticeCopy = cmHipaaScript.privacyNoticeCopy;
                            var cmPrivacyNoticeLink = cmHipaaScript.privacyNoticeLink;
                            var privacyStatement;

                            if(!cmPrivacyNoticeLabel) {
                                cmPrivacyNoticeLabel = 'I agree to the HIPAA Privacy Statement';
                            }

                            if(!cmPrivacyNoticeCopy) {
                                cmPrivacyNoticeCopy = '<p>\n' +
                                    'This "Notice of Information/Privacy Practices" is used to inform website visitors regarding our policies with the collection, use, and disclosure of Personal Information if anyone decided to use our Service.\n' +
                                    '</p>\n' +
                                    '<p>\n' +
                                    'If you choose to use our Service, then you agree to the collection and use of information in relation with this policy. The Personal Information that we collect are used for providing and improving the Service. We will not use or share your information with anyone except as described in this Privacy Policy.\n' +
                                    '</p>\n' +
                                    '<p>\n' +
                                    '<h3>Information Collection and Use</h3>\n' +
                                    'For a better experience while using our Service, we may require you to provide us with certain personally identifiable information, including but not limited to your name, phone number, and postal address. The information that we collect will be used to contact or identify you.\n' +
                                    '</p>\n' +
                                    '<p>\n' +
                                    '<h3>Log Data</h3>\n' +
                                    'We want to inform you that whenever you visit our Service, we collect information that your browser sends to us that is called Log Data. This Log Data may include information such as your computer’s Internet Protocol ("IP") address, browser version, pages of our Service that you visit, the time and date of your visit, the time spent on those pages, and other statistics.\n' +
                                    '</p>\n' +
                                    '<p>\n' +
                                    '<h3>Cookies</h3>\n' +
                                    'Cookies are files with small amount of data that is commonly used an anonymous unique identifier. These are sent to your browser from the website that you visit and are stored on your computer’s hard drive.\n' +
                                    '</p>\n' +
                                    '<p>\n' +
                                    'Our website uses these "cookies" to collection information and to improve our Service. You have the option to either accept or refuse these cookies, and know when a cookie is being sent to your computer. If you choose to refuse our cookies, you may not be able to use some portions of our Service.\n' +
                                    '</p>\n' +
                                    '<p>\n' +
                                    '<h3>Service Providers</h3>\n' +
                                    'We may employ third-party companies and individuals due to the following reasons:\n' +
                                    '<ul>\n' +
                                    '<li>To facilitate our Service;</li>\n' +
                                    '<li>To provide the Service on our behalf;</li>\n' +
                                    '<li>To perform Service-related services; or</li>\n' +
                                    '<li>To assist us in analyzing how our Service is used.</li>\n' +
                                    '<p>\n' +
                                    'We want to inform our Service users that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.\n' +
                                    '</p>\n' +
                                    '</p>\n' +
                                    '<p>\n' +
                                    '<h3>Security</h3>\n' +
                                    'We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and we cannot guarantee its absolute security.\n' +
                                    '</p>\n' +
                                    '<p>\n' +
                                    '<h3>Links to Other Sites</h3>\n' +
                                    'Our Service may contain links to other sites. If you click on a third-party link, you will be directed to that site. Note that these external sites are not operated by us. Therefore, we strongly advise you to review the Privacy Policy of these websites. We have no control over, and assume no responsibility for the content, privacy policies, or practices of any third-party sites or services.\n' +
                                    '</p>\n' +
                                    '<p>\n' +
                                    '<h3>Changes to This Privacy Policy</h3>\n' +
                                    'We may update our Privacy Policy from time to time. Thus, we advise you to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page. These changes are effective immediately, after they are posted on this page.\n' +
                                    '</p>';
                            }

                            var modalHTML;
                            if(cmPrivacyNoticeLink && cmPrivacyNoticeMethod === 'link') {
                                privacyStatement = '<span class="cm-hipaa-privacy-statement-link"><a href="' + cmPrivacyNoticeLink + '" target="_blank" >' + cmPrivacyNoticeLabel + '</a></span>';
                                modalHTML = '';
                            } else {
                                modalHTML = '<div class="cmprivacy-modal closed"><div class="cmprivacy-modal-inner"><div class="cmprivacy-modal-inner-top"><h2 class="cmprivacy-title">Notice of Information/Privacy Practices</h2><i class="cmprivacy-close-btn material-icons">cancel</i></div><div class="cmprivacy-content">'+ cmPrivacyNoticeCopy + '</div></div></div>';

                                privacyStatement = '<span class="cm-hipaa-privacy-statement"><a href="#" >' + cmPrivacyNoticeLabel + '</a></span>';
                            }

                            if(maxSubmissionsMessage) {
                                // PREPEND MAX FORM SUBMISSIONS MESSAGE
                                form.prepend('<div class="cm-hipaa-forms-prepend">'+ maxSubmissionsMessage +'</div>');

                                // APPEND MAX FORM SUBMISSIONS MESSAGE
                                appendEle.append('<div class="cm-hipaa-forms-prepend">'+ maxSubmissionsMessage +'</div>');

                                // DISABLE FORM INPUTS
                                form.find('input, textarea').attr('readonly', 'readonly');
                                form.find('select').attr('disabled', 'disabled');
                                form.find('input[type="checkbox"], input[type="radio"]').prop('disabled', true);
                            } else {
                                // APPEND ADDITIONAL FIELDS, SUBMIT BUTTON AND BADGE TO THE FORM
                                appendEle.append('<div class="cm-hipaa-forms-prepend">' + modalHTML + '<div class="cm-hipaa-forms-privacy-statement"><input type="checkbox" class="cm-hipaa-forms-privacy-agree" name="cm-hipaa-forms-privacy-agree" value="Yes" /> ' + privacyStatement + '</div><div class="cm-hipaa-forms-prepend-bottom"><div class="cm-hipaa-forms-ssl-notice"></div>' + sigField + '<div class="cm-hipaa-forms-submit-wrapper">' + newSubmitButton + '</div><div class="cm-hipaa-notice"></div>' + stagingMessage + '<div class="cm-hipaa-form-badge-wrapper"><img src="' + cmHipaaScript.pluginUrl + '/images/hipaa-compliant-badge.png" alt="HIPAA COMPLIANT FORM" /></div>' + basicBadge + '</div></div>').promise().done(function () {
                                    if (showSig === 'yes') {
                                        // INITIALIZE THE FORM SIGNATURE CANVAS FIELD IF VISIBLE
                                        var signature = appendEle.find(".cm-hipaa-form-signature");
                                        if (signature.is(':visible')) {
                                            signature.jSignature();
                                        }
                                    }
                                });
                            }
                        } else {
                            // DISABLE & REMOVE ORIGINAL SUBMIT BUTTON TO PREVENT ACCIDENTAL GENERAL SUBMISSION
                            submitButton.attr('disabled', 'disabled').remove();

                            appendEle.append(resultData.error);
                        }
                    },
                    error: function (errorThrown) {
                        // REMOVE LOADING ICON
                        jQuery('.cm-hipaa-forms-loading').remove();

                        console.log(errorThrown);
                    }
                });
            } else {
                // APPEND NOTICE THAT SSL IS NOT ENABLED
                form.append('<div class="cm-hipaa-forms-ssl-notice">This form is designated as a HIPAA compliant form however SSL (https) is not enabled so the form has been disabled.<br />Please ask the website administrators to fix this issue.</div>');

                // DISABLE AND REMOVE SUBMIT BUTTON TO ENSURE FORM CAN'T BE SUBMITTED
                submitButton.attr('disabled', 'disabled').remove();
            }
        }
    });

    /*** REINITIALIZE JSIGNATURE IF MULTI-PAGE FORM - JSIGNATURE DOESN'T INIT PROPERLY WHEN IN A HIDDEN ELEMENT ***/
    jQuery(document).on('click', '.cf-page-btn-next, .gform_next_button, .gf_step, .caldera_forms_form .breadcrumb a', function() {
        var signature = jQuery(this).closest('form').find('.cm-hipaa-form-signature');

        // SET TIMEOUT TO WAIT FOR FADEIN
        setTimeout(function() {
            if(signature.is(':visible') && signature.find('canvas').length === 0) {
                signature.jSignature();
            }
        },500);
    });

    /*** ADD VALID CLASS TO SIGNATURE FIELD ON CLICK ***/
    jQuery(document).on('click', '.jSignature', function() {
        if(!jQuery(this).hasClass('cm-valid-sig')) {
            jQuery(this).addClass('cm-valid-sig');
        }
    });

    /*** TOUCH EVENT VERSION ***/
    jQuery(document).on('touchstart', '.jSignature', function() {
        if(!jQuery(this).hasClass('cm-valid-sig')) {
            jQuery(this).addClass('cm-valid-sig');
        }
    });

    /*** REMOVE VALID CLASS FROM SIGNATURE FIELD ON RESET ***/
    jQuery(document).on('click', '.cm-hipaa-form-signature-reset', function() {
        jQuery(this).parent().parent().find('.jSignature').removeClass('cm-valid-sig');
    });

    /*** OVER-RIDE GRAVITY PREVIOUS/NEXT BUTTONS TO SHOW/HIDE INSTEAD OF RELOAD PAGE, DEFAULT FORM SUBMIT TO SELF ISN'T PROTECTED & NOT COMPLIANT! ***/
    jQuery(document).on('click', '.gform_previous_button, .gform_next_button', function(e) {
        var form = jQuery(this).closest('form');
        var formId = form.attr('id');
        var thisPage = jQuery(this).parent().parent();
        var pages = form.find('.gform_page');
        var pbPercent = Math.floor(100/pages.length);
        var progressBarTitle = form.find('.gf_progressbar_title');
        var progressBar = form.find('.gf_progressbar_percentage');
        var stepsWrapper = form.find('.gf_page_steps');
        var direction;

        // CLEAR ERRORS
        form.find('.gfield_error').removeClass('gfield_error');
        form.find('.validation_error, .validation_message').remove();

        if(jQuery(this).hasClass('gform_previous_button')) {
            direction = 'previous';
        } else if(jQuery(this).hasClass('gform_next_button')) {
            direction = 'next';
        }

        // LOOP PAGES
        pages.each(function(index) {
            index = index+1;

            if(jQuery(this).attr('id') === thisPage.attr('id') && direction === 'next') {
                // VALIDATE REQUIRED FIELDS ON PAGE
                var inputs = thisPage.find('input');
                var errors = [];

                // LOOP INPUTS
                inputs.each(function() {
                    var fieldWrapper = jQuery(this).closest('.gfield');
                    var fieldType = jQuery(this).attr('type');
                    var value = jQuery(this).val();

                    // IF CHECKBOX OR RADIO SET OPTIONS
                    var checkOrRadio = false;
                    var cbOptionChecked;
                    if(fieldType === 'checkbox' || fieldType === 'radio') {
                        checkOrRadio = true;
                        cbOptionChecked = jQuery(this).prop('checked');
                    }

                    // SET IF FIELD IS HIDDEN OR DISABLED
                    var isVisible = jQuery(this).is(':visible');
                    var isDisabled = jQuery(this).prop('disabled');

                    // SET IF FIELD IS REQUIRED
                    var required = false;
                    if(isDisabled !== true && isVisible === true && (jQuery(this).attr('required') || jQuery(this).attr('aria-required') || fieldWrapper.hasClass('gfield_contains_required') && checkOrRadio !== true && !jQuery(this).parent().hasClass('address_line_2'))) {
                        required = true;
                    }

                    // IF REQUIRED FIELD WITH NO VALUE PUSH FIELD TO ERRORS ARRAY
                    if (required && !value || isDisabled !== true && isVisible === true && checkOrRadio === true && fieldWrapper.hasClass('gfield_contains_required') && cbOptionChecked === false) {
                        var invalidInputId;

                        // IF CHECKBOX OR RADIO INPUT
                        if(checkOrRadio === true) {
                            var cbIsValid = false;

                            // GROUP OPTION INPUTS
                            var cbInputs = fieldWrapper.find('input');

                            // LOOP OPTIONS
                            cbInputs.each(function() {
                                if(jQuery(this).prop('checked') === true) {
                                    cbIsValid = true;
                                }
                            });

                            // PUSH ID TO ERROR ARRAY IF INVALID
                            if(cbIsValid !== true) {
                                invalidInputId = fieldWrapper.attr('id');
                            }
                        } else {
                            invalidInputId = jQuery(this).attr('id');
                        }

                        if(invalidInputId && errors.indexOf(invalidInputId) === -1) {
                            errors.push(invalidInputId);
                        }
                    }
                });

                if(errors.length > 0) {
                    // SET FIRST INVALID FIELD
                    var firstErrorId = errors[0];
                    var firstInvalidInput = jQuery('#' + firstErrorId);
                    var firstInvalidInputWrapper = firstInvalidInput.closest('li');

                    // ADD ERROR MESSAGE TO TOP OF FORM
                    form.find('.gform_body').before('<div class="validation_error">There was a problem with your submission. Errors have been highlighted below.</div>');

                    // LOOP INVALID INPUT ID'S
                    for(i=0; i<errors.length; i++) {
                        var invalidInput = jQuery('#' + errors[i]);
                        var invalidFieldType = invalidInput.attr('type');

                        // ADD ERROR CLASS & NOTICE MESSAGE
                        var invalidInputWrapper;
                        if(invalidFieldType === 'checkbox' || invalidFieldType === 'radio') {
                            invalidInputWrapper = invalidInput.parent().parent().parent().parent();
                            invalidInputWrapper.addClass('gfield_error').append('<div class="gfield_description validation_message">This field is required.</div>');
                        } else {
                            invalidInputWrapper = invalidInput.closest('li');
                            invalidInputWrapper.addClass('gfield_error').append('<div class="gfield_description validation_message">This field is required.</div>');
                        }
                    }

                    // SCROLL TO FIRST INVALID FIELD
                    jQuery('html, body').animate({
                        scrollTop: firstInvalidInputWrapper.offset().top
                    }, 500, 'linear');
                } else {
                    // FADE OUT THIS PAGE
                    jQuery(this).fadeOut().promise().done(function () {
                        // FADE IN NEXT PAGE
                        var nextPageEle = jQuery(this).next('.gform_page');
                        nextPageEle.fadeIn();

                        var nextIndex = index + 1;

                        // UPDATE PROGRESS BAR IF SET
                        if(progressBar) {
                            var nextPercent;
                            if (index + 1 === pages.length) {
                                nextPercent = 100;
                            } else {
                                nextPercent = (pbPercent * index) + pbPercent;
                            }
                            progressBar.css('width', nextPercent + '%').find('span').html(nextPercent + '%');
                            progressBarTitle.html('Step ' + nextIndex + ' of ' + pages.length);
                        }

                        // UPDATE STEPS IF SET
                        if(stepsWrapper) {
                            var stepLinks = stepsWrapper.find('.gf_step');
                            var pageId = nextPageEle.attr('id');
                            var pageIdArr = pageId.split('_');

                            var stepLinkId = 'gf_step_' + pageIdArr[2] + '_' + pageIdArr[3];

                            // LOOP STEP LINK AND SET NEXT STEP ACTIVE
                            jQuery.each(stepLinks, function() {
                                if(jQuery(this).attr('id') === stepLinkId) {
                                    jQuery(this).addClass('gf_step_active gpmpn-step-current');
                                } else {
                                    jQuery(this).removeClass('gf_step_active gpmpn-step-current');
                                }
                            });
                        }

                        // SCROLL TO FIRST INVALID FIELD
                        jQuery('html, body').animate({
                            scrollTop: form.offset().top
                        }, 300, 'linear');
                    });
                }
            } else if(jQuery(this).attr('id') === thisPage.attr('id') && direction === 'previous') {
                // FADE OUT THIS PAGE
                jQuery(this).fadeOut().promise().done(function() {
                    // FADE IN PREVIOUS PAGE
                    var prevPageEle = jQuery(this).prev('.gform_page');
                    prevPageEle.fadeIn();

                    var previousIndex = index - 1;

                    // UPDATE PROGRESS BAR
                    if(progressBar) {
                        prevPercent = (pbPercent * index) - pbPercent;
                        progressBar.css('width', prevPercent + '%').find('span').html(prevPercent + '%');
                        progressBarTitle.html('Step ' + previousIndex + ' of ' + pages.length);
                    }

                    // UPDATE STEPS IF SET
                    if(stepsWrapper) {
                        var stepLinks = stepsWrapper.find('.gf_step');
                        var pageId = prevPageEle.attr('id');
                        var pageIdArr = pageId.split('_');

                        var stepLinkId = 'gf_step_' + pageIdArr[2] + '_' + pageIdArr[3];

                        // LOOP STEP LINK AND SET NEXT STEP ACTIVE
                        jQuery.each(stepLinks, function() {
                            if(jQuery(this).attr('id') === stepLinkId) {
                                jQuery(this).addClass('gf_step_active gpmpn-step-current');
                            } else {
                                jQuery(this).removeClass('gf_step_active gpmpn-step-current');
                            }
                        });
                    }

                    // SCROLL TO FIRST INVALID FIELD
                    jQuery('html, body').animate({
                        scrollTop: form.offset().top
                    }, 300, 'linear');
                });
            }
        });
    });

    /*** ADD GRAVITY STEP LINK ACTION ***/
    jQuery(document).on('click', '.gf_step', function() {
        var form = jQuery(this).closest('form');
        var stepsWrapper = form.find('.gf_page_steps');
        var stepLinks = stepsWrapper.find('.gf_step');
        var stepId = jQuery(this).attr('id');
        var stepIdArr = stepId.split('_');
        var pageId = 'gform_page_' + stepIdArr[2] + '_' + stepIdArr[3];
        var curPage = form.find('.gform_page:visible');
        var curPageIdArr = curPage.attr('id').split('_');

        if(curPage.attr('id') !== pageId) {
            if(stepIdArr[3] > curPageIdArr[3]) {
                // CLEAR ERRORS
                form.find('.gfield_error').removeClass('gfield_error');
                form.find('.validation_error, .validation_message').remove();

                // VALIDATE REQUIRED FIELDS ON PREVIOUS PAGES
                var inputs = [];
                jQuery.each(form.find('.gform_page'), function() {
                    if(jQuery(this).attr('id').split('_')[3] < stepIdArr[3]) {
                        var pageInputs = jQuery(this).find('input');

                        pageInputs.each(function() {
                            inputs.push(jQuery(this).attr('id'));
                        });
                    } else {
                        return false;
                    }
                });

                var errors = [];
                // LOOP INPUTS
                for(i=0; i<inputs.length; i++) {
                    var curInput = jQuery('#' + inputs[i]);
                    var fieldWrapper = curInput.closest('.gfield');
                    var fieldType = curInput.attr('type');
                    var value = curInput.val();

                    // IF CHECKBOX OR RADIO SET OPTIONS
                    var checkOrRadio = false;
                    var cbOptionChecked;
                    if(fieldType === 'checkbox' || fieldType === 'radio') {
                        checkOrRadio = true;
                        cbOptionChecked = curInput.prop('checked');
                    }

                    // SET IF FIELD IS HIDDEN OR DISABLED
                    var isVisible = curInput.is(':visible');
                    var isDisabled = curInput.prop('disabled');

                    // SET IF FIELD IS REQUIRED
                    var required = false;
                    if(isDisabled !== true && isVisible === true && (curInput.attr('required') || curInput.attr('aria-required') || fieldWrapper.hasClass('gfield_contains_required') && checkOrRadio !== true && !curInput.parent().hasClass('address_line_2'))) {
                        required = true;
                    }

                    if (required === true && !value || isDisabled !== true && isVisible === true && checkOrRadio === true && fieldWrapper.hasClass('gfield_contains_required') && cbOptionChecked === false) {
                        var invalidInputId;

                        // IF CHECKBOX OR RADIO INPUT
                        if (fieldType === 'checkbox' || fieldType === 'radio') {
                            var cbIsValid;

                            // SET INPUT GROUP WRAPPER
                            var cbInputWrapper = curInput.parent().parent().parent().parent();

                            // GROUP OPTION INPUTS
                            var cbInputs = cbInputWrapper.find('input');

                            // LOOP OPTIONS
                            cbInputs.each(function () {
                                cbIsValid = false;

                                if (jQuery(this).prop('checked') === true) {
                                    cbIsValid = true;

                                    return false;
                                }
                            });

                            // PUSH ID TO ERROR ARRAY IF INVALID
                            if (cbIsValid !== true) {
                                invalidInputId = cbInputWrapper.attr('id');
                            }
                        } else {
                            invalidInputId = curInput.attr('id');
                        }
                        if (invalidInputId && errors.indexOf(invalidInputId) === -1) {
                            errors.push(invalidInputId);
                        }
                    }
                }

                if (errors.length > 0) {
                    // SET FIRST INVALID FIELD
                    var firstErrorId = errors[0];
                    var firstInvalidInput = jQuery('#' + firstErrorId);
                    var errorPage = firstInvalidInput.closest('.gform_page');
                    var errorPageIdArr = errorPage.attr('id').split('_');
                    var firstInvalidInputWrapper = firstInvalidInput.closest('li');

                    // ADD ERROR MESSAGE TO TOP OF FORM
                    form.find('.gform_body').before('<div class="validation_error">There was a problem with your submission. Errors have been highlighted below.</div>');

                    // LOOP INVALID INPUT ID'S
                    for (i = 0; i < errors.length; i++) {
                        var invalidInput = jQuery('#' + errors[i]);
                        var invalidFieldType = invalidInput.attr('type');

                        // ADD ERROR CLASS & NOTICE MESSAGE
                        var invalidInputWrapper;
                        if (invalidFieldType === 'checkbox' || invalidFieldType === 'radio') {
                            invalidInputWrapper = invalidInput.parent().parent().parent().parent();
                            invalidInputWrapper.addClass('gfield_error').append('<div class="gfield_description validation_message">This field is required.</div>');
                        } else {
                            invalidInputWrapper = invalidInput.closest('li');
                            invalidInputWrapper.addClass('gfield_error').append('<div class="gfield_description validation_message">This field is required.</div>');
                        }
                    }

                    if(errorPage.attr('id') !== curPage.attr('id')) {
                        curPage.fadeOut().promise().done(function() {
                            errorPage.fadeIn();

                            // UPDATE LINKS
                            jQuery.each(stepLinks, function () {
                                if (jQuery(this).attr('id') === 'gf_step_' + errorPageIdArr[2] + '_' + errorPageIdArr[3]) {
                                    jQuery(this).addClass('gf_step_active gpmpn-step-current');
                                } else {
                                    jQuery(this).removeClass('gf_step_active gpmpn-step-current');
                                }
                            });
                        });
                    }

                    // SCROLL TO FIRST INVALID FIELD
                    jQuery('html, body').animate({
                        scrollTop: firstInvalidInputWrapper.offset().top
                    }, 500, 'linear');
                } else {
                    // FADE OUT CURRENT PAGE
                    curPage.fadeOut().promise().done(function () {
                        // FADE IN SELECTED PAGE
                        jQuery('#' + pageId).fadeIn();

                        // UPDATE LINKS
                        jQuery.each(stepLinks, function () {
                            if (jQuery(this).attr('id') === stepId) {
                                jQuery(this).addClass('gf_step_active gpmpn-step-current');
                            } else {
                                jQuery(this).removeClass('gf_step_active gpmpn-step-current');
                            }
                        });
                    });
                }
            } else {
                // FADE OUT CURRENT PAGE
                curPage.fadeOut().promise().done(function () {
                    // FADE IN SELECTED PAGE
                    jQuery('#' + pageId).fadeIn();
                    // UPDATE LINKS
                    jQuery.each(stepLinks, function () {
                        if (jQuery(this).attr('id') === stepId) {
                            jQuery(this).addClass('gf_step_active gpmpn-step-current');
                        } else {
                            jQuery(this).removeClass('gf_step_active gpmpn-step-current');
                        }
                    });
                });
            }
        }
    });

    /*** GRAVITY MULTI-FILE UPLOAD ADD NEW FIELD ***/
    jQuery(document).on('click', '.cm-hipaa-gf-multifile-upload-button', function() {
        var fileUploadInputCount = jQuery(this).parent().find('input[type="file"]').length+1;
        var fieldId = jQuery(this).parent().parent().attr('id');
        var fileInputId = fieldId.replace('field', 'input');
        var fileInputEnd = fieldId.split('_').pop();
        var fileInputName = 'input_' + fileInputEnd;
        var fileInputDescribed = fieldId.replace('field', 'extensions_message');

        jQuery(this).before('<div class="multi-file-input-spacer"></div><input name="' + fileInputName + '_' + fileUploadInputCount + '" id="' + fileInputId + '_' + fileUploadInputCount + '" type="file" class="medium" aria-describedby="' + fileInputDescribed + '"><div class="cm-hipaa-gf-delete-file"><i class="material-icons">delete_forever</i></div>');
    });

    /*** CALDERA MULTI-FILE UPLOAD ADD NEW FIELD ***/
    jQuery(document).on('click', '.cm-hipaa-cf-multifile-upload-button', function() {
        var fileUploadInputCount = jQuery(this).parent().find('input[type="file"]').length+1;
        var fieldId = jQuery(this).parent().attr('data-field-wrapper');

        jQuery(this).before('<div class="multi-file-input-spacer"></div><div class="file-prevent-overflow"><div id="' + fieldId + '_' + fileUploadInputCount + '_file_list" class="cf-multi-uploader-list"></div><input type="file" name="' + fieldId + '_' + fileUploadInputCount + '" value="" data-field="' + fieldId + '" class="" id="' + fieldId + '_' + fileUploadInputCount + '" aria-labelledby="' + fieldId + 'Label"><input type="hidden" name="' + fieldId + '"></div><div class="cm-hipaa-cf-delete-file"><i class="material-icons">delete_forever</i></div>');
    });

    /*** GRAVITY MULTI-FILE DELETE ***/
    jQuery(document).on('click', '.cm-hipaa-gf-delete-file', function() {
        jQuery(this).prev().prev().remove();
        jQuery(this).prev().remove();
        jQuery(this).remove();
    });

    /*** CALDERA MULTI-FILE DELETE ***/
    jQuery(document).on('click', '.cm-hipaa-cf-delete-file', function() {
        jQuery(this).prev().prev().remove();
        jQuery(this).prev().remove();
        jQuery(this).remove();
    });

    /*** SUBMIT HIPAA ENABLED CALDERA FORM ***/
    jQuery(document).on('click', 'form .cm-hipaa-forms-submit.caldera', function(e){
        var submitButton = jQuery(this);
        var form = jQuery(this).parents('form:first');
        var formHtml = form.html();
        var noticeEle = form.find('.cm-hipaa-notice');
        var formMainId = form.attr('id');
        var formId = form.attr('data-form-id');
        if(!formId) {
            formId = formMainId;
        }

        // REMOVE ACTIVE CLASS FROM SUBMIT BUTTON TO PREVENT RE-SUBMITTING
        submitButton.removeClass('active').addClass('inactive');

        // GET FILE UPLOAD URL IF EXISTS
        var fileUploadUrl = cmHipaaScript.fileUploadUrl;

        var calderaEnabledForms = JSON.parse(cmHipaaScript.calderaEnabledForms); // DEPRECATED
        formIds = [];

        // LOOP CALDERA FORMS AND PUSH IDS TO ARRAY - DEPRECATED
        for(var i = 0; i < calderaEnabledForms.length; i++){
            if(calderaEnabledForms[i]) {
                formIds.push(calderaEnabledForms[i]);
            }
        }

        var enabledFormsSettings;
        if(cmHipaaScript.enabledFormsSettings) {
            enabledFormsSettings = JSON.parse(cmHipaaScript.enabledFormsSettings); // NEW JSON OBJECT ARRAY
        }


        // CHECK IF FORM OBJECT EXISTS IN ARRAY - NEW FORMS SETTINGS JSON OBJECT ARRAY METHOD
        var formFound;
        var successHandler;
        var successMessage;
        var successRedirect;
        var successCallback;
        var successCallbackParams;
        var selectedUserSlug = '';
        var showSignature;
        var notificationOption = '';
        var notificationFromName = '';
        var notificationFromEmail = '';
        var notificationSendTo = '';
        var notificationSubject = '';
        var notificationMessage = '';

        if(cmHipaaScript.enabledFormsSettings) {
            enabledFormsSettings.some(function (el) {
                if (el.id === formId && el.enabled === 'yes') {
                    formFound = true;
                    successHandler = el.success_handler;
                    successMessage = el.success_message;
                    successRedirect = el.success_redirect;
                    successCallback = el.success_callback;
                    successCallbackParams = el.success_callback_params;
                    selectedUserSlug = el.selected_user_slug;
                    showSignature = el.show_signature;
                    notificationOption = el.notification_option;
                    notificationFromName = el.notification_from_name;
                    notificationFromEmail = el.notification_from_email;
                    notificationSendTo = el.notification_sendto;
                    notificationSubject = el.notification_subject;
                    notificationMessage = el.notification_message;
                }
            });
        }

        // SET SUCCESS MESSAGE IF NOT SET
        if(!successMessage) {
            successMessage = 'Thank you, your form has been encrypted to protect your privacy and submitted successfully!';
        }

        // IF FORM ID IS IN ENABLED FORMS PREVENT DEFAULT SUBMIT AND SEND TO CODE MONKEYS
        if(jQuery.inArray(formId, formIds) !== -1 || formFound) {
            e.preventDefault(); //Prevent the normal submission action
            var defaultBorder;

            // GET FILE INPUTS
            var fileInputs = form.find('input[type=file]');

            // CLONE THE FORM AS A HIDDEN VERSION IN ORDER TO REPLACE INPUTS WITH VALUES AND FORMAT TO SAVE
            form.after('<div class="cm-hipaa-forms-hidden-form" style="display:none;"></div>');
            form.clone().appendTo('.cm-hipaa-forms-hidden-form');
            var hiddenForm = jQuery('.cm-hipaa-forms-hidden-form form');
            var hiddenFormInputs = jQuery('.cm-hipaa-forms-hidden-form form :input');

            // REMOVE THE UNWANTED APPENDED ITEMS AT BOTTOM OF FORM
            hiddenForm.find('.cm-hipaa-forms-prepend-bottom, .cmprivacy-modal, .cmprivacy-modal-overlay').remove();

            // REMOVE STEP LINK BREADCRUMBS IF EXIST
            hiddenForm.find('.breadcrumb').remove();

            // LOOP CLONED HIDDEN FORM INPUTS AND GET LABELS => VALUES & FULL HTML VERSION OF FORM
            var formFields = [];
            var validationErrors = [];

            var inputs = jQuery('#' + formMainId + ' :input');
            hiddenFormInputs.each(function() {
                var label = form.find('label[for="' + jQuery(this).attr('id') + '"]').text().trim();
                var value = jQuery(this).val();
                var dataField = jQuery(this).attr('data-field');
                var fieldId = jQuery(this).attr('id');
                var fieldName = jQuery(this).attr('name');
                var fieldType = jQuery(this).attr('type');
                defaultBorder = jQuery(this).css('border');
                var required = jQuery(this).attr('required');
                var checkboxParentLabel = '';
                var checkboxLabel = '';
                var radioValue = '';
                var checkboxValue = '';
                var fieldValue = '';
                var optionLabel = '';
                var optionValue = '';

                // IGNORE INITIAL HIDDEN CALDERA FIELDS & NEXT/PREVIOUS BUTTONS
                if(fieldName !== '_cf_verify' && fieldName !== '_wp_http_referer' && fieldName !== '_cf_frm_id' && fieldName !== '_cf_frm_ct' && fieldName !== 'cfajax' && fieldName !== '_cf_cr_pst' && fieldType !== 'button' && !jQuery(this).hasClass('button_trigger_1') && !jQuery(this).hasClass('button_trigger_2')) {
                    if (!fieldType) {
                        if (jQuery(this).is('select')) {
                            fieldType = 'select';
                            var visibleSelect = form.find('#' + fieldId);
                            var optionText = visibleSelect.find('option:selected').text();
                            value = visibleSelect.val();
                            fieldValue = value;
                        } else if (jQuery(this).is('textarea')) {
                            fieldType = 'textarea';
                            var visibleTextArea = form.find('#' + fieldId);
                            value = visibleTextArea.attr('value');
                            fieldValue = value;
                            // TRY THE VALUE ATTR FIRST AND IF NO VALUE TRY NORMAL JQUERY VAL(), NOT SURE WHY ONE WORKS OVER THE OTHER SOMETIMES
                            if(!value || value === 'undefined') {
                                value = visibleTextArea.val();
                                fieldValue = value;
                            }
                        }
                    }

                    // GET REQUIRED FIELDS THAT DO NOT HAVE A VALUE
                    if (required && !value && form.find('#' + fieldId).is(':visible') || (fieldType === 'checkbox' || fieldType === 'radio') && (required || jQuery(this).attr('data-parsley-required') === 'true') && jQuery(this).prop('checked') === false && form.find('#' + fieldId).is(':visible')) {
                        var errorExists;

                        if(fieldType === 'checkbox' || fieldType === 'radio') {
                            var optionsGroup;
                            var visibleOptionsGroup;
                            if(fieldType === 'checkbox') {
                                optionsGroup = jQuery(this).data('parsley-group');
                                visibleOptionsGroup = form.find('input[data-parsley-group="' + optionsGroup + '"]');
                            } else if(fieldType === 'radio') {
                                optionsGroup = jQuery(this).data('radio-field');
                                visibleOptionsGroup = form.find('input[data-radio-field="' + optionsGroup + '"]');
                            }
                            var cbIgnore;

                            visibleOptionsGroup.each(function() {
                                if(jQuery(this).prop('checked') === true) {
                                    cbIgnore = true;

                                    return false;
                                }
                            });

                            if(validationErrors.length > 0) {
                                validationErrors.some(function(el) {
                                    if(el.fieldId === fieldId || el.fieldGroup === optionsGroup) {
                                        errorExists = true;
                                    }
                                });
                            }

                            if(!errorExists && !cbIgnore) {
                                validationErrors.push({
                                    'fieldId': fieldId,
                                    'label': form.find('label[for="' + optionsGroup + '"]').text(),
                                    'fieldType': fieldType,
                                    'fieldGroup': optionsGroup
                                });
                            }
                        } else {
                            validationErrors.push({
                                'fieldId': fieldId,
                                'label': form.find('label[for="' + jQuery(this).attr('id') + '"]').text(),
                                'fieldType': fieldType,
                                'fieldGroup': ''
                            });
                        }
                    }

                    // SET CHECKBOX OR RADIO FIELD LABEL AND INPUT LABEL
                    if (fieldType === 'checkbox' || fieldType === 'radio') {
                        if (jQuery(this).prop('checked') === true) {
                            checkboxLabel = jQuery(this).parent().text().trim();

                            if(jQuery(this).hasClass('cm-hipaa-forms-privacy-agree')) {
                                label = 'Privacy Agreement';
                            } else {
                                label = jQuery(this).parent().parent().parent().find('.control-label').text().trim();

                                if(!label) {
                                    label = jQuery(this).parent().parent().parent().parent().find('.control-label').text().trim();

                                    if(!label) {
                                        label = checkboxLabel;
                                    }
                                }
                            }

                            checkboxParentLabel = label;
                            optionLabel = checkboxLabel;
                            optionValue = value;
                        }
                    }

                    // REPLACE HIDDEN HTML VERSION INPUTS WITH VALUES
                    if (fieldType === 'select') {
                        jQuery(this).replaceWith(optionText);
                    } else if (fieldType === 'checkbox') {
                        // SET CHECKBOX VALUE IF DIFFERENT THAN LABEL
                        if(value !== checkboxLabel) {
                            checkboxValue = value;
                        }

                        if (jQuery(this).prop('checked') === true) {
                            // SET CHECKED CHECKBOX IMAGE AND VALUE
                            value = '<span class="cm-hipaa-forms-checkbox-value cm-hipaa-forms-checkbox-checked" style="background: #f7f5a091;"><img src="https://www.hipaaforms.online/api-assets/checkbox.png" alt="Checked" style="height: 16px; padding-right: 5px; vertical-align: middle;" />' + checkboxValue + '</span>';

                            if(checkboxParentLabel) {
                                fieldValue = 'checked';
                            }
                        } else {
                            // SET UNCHECKED CHECKBOX IMAGE AND VALUE
                            value = '<span class="cm-hipaa-forms-checkbox-value  cm-hipaa-forms-checkbox-not-checked"><img src="https://www.hipaaforms.online/api-assets/checkbox-unchecked.png" alt="Not Checked" style="height: 16px; padding-right: 5px; vertical-align: middle;" /></span>';
                        }
                        jQuery(this).replaceWith(value);
                    } else if (fieldType === 'radio') {
                        // SET RADIO VALUE IF DIFFERENT THAN LABEL
                        if(value !== checkboxLabel) {
                            radioValue = value;
                        }

                        if (jQuery(this).prop('checked') === true) {
                            // SET CHECKED RADIO BUTTON IMAGE AND VALUE
                            value = '<span class="cm-hipaa-forms-radio-value cm-hipaa-forms-radio-checked" style="background: #f7f5a091;"><img src="https://www.hipaaforms.online/api-assets/radio-checked.png" alt="Selected" style="height: 16px; padding-right: 5px; vertical-align: middle;" />' + radioValue + '</span>';

                            if(checkboxParentLabel) {
                                fieldValue = 'checked';
                            }
                        } else {
                            // SET UNCHECKED RADIO BUTTON IMAGE AND VALUE
                            value = '<span class="cm-hipaa-forms-radio-value cm-hipaa-forms-radio-not-checked"><img src="https://www.hipaaforms.online/api-assets/radio-unchecked.png" alt="Not Selected" style="height: 16px; padding-right: 5px; vertical-align: middle;" /></span>';
                        }

                        jQuery(this).replaceWith(value);
                    } else {
                        fieldValue = value;
                        jQuery(this).replaceWith(value);
                    }

                    if (label && fieldValue) {
                        // IF LABEL EXISTS PUSH TO FIELDS ARRAY
                        formFields.push({
                            'label': label,
                            'option_label': optionLabel,
                            'option_value': optionValue,
                            'value': fieldValue,
                            'data_field': dataField,
                            'field_id': fieldId,
                            'field_type': fieldType,
                            'option_text': optionText
                        });
                    }
                }
            });

            // GET SIGNATURE SVG BASE64 DATA
            var signatureEle = form.find('.cm-hipaa-form-signature');
            var datapair = signatureEle.jSignature("getData", "svgbase64");
            var signature;
            if(datapair) {
                signature = "data:" + datapair[0] + "," + datapair[1];

                formFields.push({
                    'label': 'Signature',
                    'option_label': 'data:',
                    'option_value': datapair[0],
                    'value': datapair[1],
                    'data_field': '',
                    'field_id': '',
                    'field_type': 'signature',
                    'option_text': ''
                });
            }


            // SET PRIVACY AGREEMENT ELEMENT
            var privacyAgreementEle = form.find('.cm-hipaa-forms-privacy-agree');

            // RESET ERRORS
            //inputs.css('border', defaultBorder);
            jQuery('.form-group').removeClass('has-error');
            jQuery('input, select, textarea').removeAttr('aria-invalid').removeClass('parsley-error');
            jQuery('.caldera_ajax_error_block').remove();
            privacyAgreementEle.parent().css('border', '0');
            if(signatureEle) {
                signatureEle.find('.jSignature').css('border', '0');
            }
            noticeEle.html('');

            // MULTI-PAGE FORM RENDERING
            var calderaFormPages = hiddenForm.find('[data-formpage]');
            calderaFormPages.each(function(index) {
                // MAKE PAGE ELEMENT VISIBLE
                jQuery(this).css({
                    'visibility': 'visible',
                    'display': 'block'
                });

                // REMOVE BUTTONS
                jQuery(this).find('input[type="button"]').closest('.form-group').remove();
                jQuery(this).find('button').remove();

                // APPEND PAGE BREAK TO END OF PAGE FOR MPDF IF NOT LAST ELEMENT
                if(index !== (calderaFormPages.length - 1)) {
                    jQuery(this).append('<pagebreak />');
                }
            });

            var nonce = cmHipaaScript.nonce;

            if(!jQuery.isEmptyObject(validationErrors)) {
                // VALIDATE FIELDS
                jQuery.each(validationErrors, function(key, val) {
                    var invalidFieldId = val.fieldId;
                    var invalidFieldType = val.fieldType;
                    var invalidFieldGroup = val.fieldGroup;
                    var invalidInput = jQuery('#' + invalidFieldId);
                    var invalidFieldWrapper;

                    if(invalidFieldGroup) {
                        invalidFieldWrapper = jQuery('#' + invalidFieldGroup + '-wrap');
                    } else {
                        invalidFieldWrapper = jQuery('#' + invalidFieldId + '-wrap');
                    }

                    // ADD ERROR CLASS TO FIELD GROUP ELEMENT & INPUT
                    invalidFieldWrapper.addClass('has-error');
                    invalidInput.attr('aria-invalid', 'true').addClass('parsley-error');
                    jQuery('label[for="' + invalidFieldId + '"]').addClass('parsley-error');

                    // APPEND NOTICE
                    invalidFieldWrapper.append('<span class="help-block caldera_ajax_error_block filled" aria-live="polite"><span class="parsley-required">This value is required.</span></span>');

                    // UPDATE NOTICE ELEMENT
                    noticeEle.append('<p>' + val.label + ' is a required field!</p>');
                });

                // REMOVE HIDDEN FORM
                jQuery('.cm-hipaa-forms-hidden-form').remove();

                // SET FIRST INVALID FIELD DEPENDING ON IF CHECKBOX/FIELDGROUP
                var scrollTopEle;
                if(validationErrors[0].fieldGroup) {
                    scrollTopEle = '#' + validationErrors[0].fieldGroup + '-wrap';
                } else {
                    scrollTopEle = '#' + validationErrors[0].fieldId + '-wrap';
                }

                // SCROLL TO FIRST INVALID FIELD
                jQuery('html, body').animate({
                    scrollTop: jQuery(scrollTopEle).offset().top
                }, 500, 'linear');

                // ADD ACTIVE CLASS BACK TO SUBMIT BUTTON
                submitButton.removeClass('inactive').addClass('active');
            } else if(privacyAgreementEle.prop('checked') !== true) {
                // ENSURE THE HIPAA PRIVACY CHECKBOX IS CHECKED
                privacyAgreementEle.parent().css('border', '1px solid red');
                noticeEle.html('You must agree to the HIPAA Privacy Agreement');

                // REMOVE HIDDEN FORM
                jQuery('.cm-hipaa-forms-hidden-form').remove();

                // ADD ACTIVE CLASS BACK TO SUBMIT BUTTON
                submitButton.removeClass('inactive').addClass('active');
            } else if(showSignature === 'yes' && !signatureEle.find('.jSignature').hasClass('cm-valid-sig')) {
                // ADD RED BORDER TO SIGNATURE FIELD
                signatureEle.find('.jSignature').css('border', '1px solid red');

                // UPDATE NOTICE ELEMENT
                noticeEle.html('You must sign the form');

                // REMOVE HIDDEN FORM
                jQuery('.cm-hipaa-forms-hidden-form').remove();

                // ADD ACTIVE CLASS BACK TO SUBMIT BUTTON
                submitButton.removeClass('inactive').addClass('active');
            } else {
                // IF FILE INPUTS EXIST & FILE UPLOAD ENABLED
                if(fileInputs.length > 0 && fileUploadEnabled === 'yes') {
                    // SHOW LOADING BAR
                    noticeEle.html('<div class="cm-hipaa-forms-file-upload-status">Uploading Files...</div><div class="cm-hipaa-forms-progress-wrapper"><div class="cm-hipaa-forms-progress"><div class="cm-hipaa-forms-progress-bar"><!--    <div class="cm-hipaa-forms-progress-label">10%</div>--></div></div></div>');
                    cmHipaaFormsProgress();

                    var fileInputsLength = fileInputs.length;
                    var iterations = 0;
                    var fileKeys = [];

                    fileInputs.each(function(index) {
                        var fileInputParentId = jQuery(this).parent().parent().attr('id');
                        var fileLabel = jQuery(this).parent().parent().find('label').text();
                        fileLabel = fileLabel.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');

                        // GET FILE
                        var theFormFile = jQuery(this).get()[0].files[0];

                        if(theFormFile) {
                            var fileType = theFormFile.type;
                            var fileName = theFormFile.name;
                            // GET PRESIGNED FILE UPLOAD URL
                            jQuery.ajax({
                                method: 'POST',
                                url: ajax.ajax_url,
                                data: {
                                    'action': 'cm_hipaa_get_file_upload_url',
                                    'file_name': fileName,
                                    'nonce': cmHipaaScript.nonce
                                },
                                success: function (data) {
                                    var resultData = JSON.parse(data);
                                    var fileUploadUrl = resultData.file_upload_url;
                                    var fileKey = resultData.file_key;

                                    // PUSH FILE KEYS TO ARRAY
                                    fileKeys.push(fileKey);

                                    // UPLOAD THE FILE
                                    jQuery.ajax({
                                        url: fileUploadUrl, // the presigned URL
                                        type: 'PUT',
                                        processData: false,
                                        contentType: fileType,
                                        headers: {'x-amz-tagging': 'label=' + fileLabel},
                                        data: theFormFile,
                                        success: function () {
                                            var hiddenFileUploadContainer = hiddenForm.find('#' + fileInputParentId);
                                            var existingFiles = hiddenFileUploadContainer.find('.cm_hipaa_file_input'); // GET EXISTING FILES ALREADY UPLOADED IN HIDDEN FORM
                                            hiddenFileUploadContainer.html(''); // CLEAR THE CONTAINER ELEMENT

                                            // PREPEND LABEL TO FILE CONTAINER ELEMENT
                                            hiddenFileUploadContainer.prepend('<label>' + fileLabel + '</label>');

                                            jQuery.each(existingFiles, function() {
                                                // APPEND EXISTING FILES ALREADY UPLOADED BACK INTO CONTAINER ELEMENT
                                                hiddenFileUploadContainer.append(jQuery(this));
                                            });

                                            // APPEND NEW UPLOADED FILE TO CONTAINER ELEMENT
                                            hiddenFileUploadContainer.append('<div class="cm_hipaa_file_input" data-file-key="' + fileKey + '">' + fileKey + '</div>');


                                            iterations = iterations + 1;
                                            if (iterations === fileInputsLength) {
                                                // UPDATE FILE UPLOAD STATUS
                                                jQuery('.cm-hipaa-forms-file-upload-status').html('Submitting Form...');

                                                // SET TIMEOUT TO ENSURE INPUTS OVERWRITTEN
                                                setTimeout(function () {
                                                    // SUBMIT FORM
                                                    jQuery.ajax({
                                                        method: 'POST',
                                                        url: ajax.ajax_url,
                                                        data: {
                                                            'action': 'cm_hipaa_submit_caldera_form',
                                                            'formId': formId,
                                                            'formFields': formFields,
                                                            'formHtml': hiddenForm.html(),
                                                            'signature': signature,
                                                            'nonce': nonce,
                                                            'selectedUserSlug': selectedUserSlug,
                                                            'notification_option': notificationOption,
                                                            'notification_from_name': notificationFromName,
                                                            'notification_from_email': notificationFromEmail,
                                                            'notification_sendto': notificationSendTo,
                                                            'notification_subject': notificationSubject,
                                                            'notification_message': notificationMessage,
                                                            'files': fileKeys.join(',')
                                                        },
                                                        success: function (data) {
                                                            var resultData = JSON.parse(data);

                                                            if (resultData.success === 'success') {
                                                                if (successHandler === 'message') {
                                                                    // DISPLAY THE SUCCESS NOTICE
                                                                    noticeEle.html(successMessage + ' ' + resultData.error);
                                                                } else if (successHandler === 'redirect') {
                                                                    // REDIRECT TO SUCCESS PAGE
                                                                    window.location = successRedirect;
                                                                } else if (successHandler === 'callback') {
                                                                    // MAKE SURE CALLBACK IS AN EXISTING FUNCTION
                                                                    fnExists = typeof window[successCallback] === 'function';
                                                                    if (fnExists) {
                                                                        if (successCallbackParams) {
                                                                            window[successCallback].apply(null, successCallbackParams.split(','));
                                                                        } else {
                                                                            window[successCallback]();
                                                                        }
                                                                    } else {
                                                                        console.log(successCallback + ' is not an existing function!');
                                                                    }
                                                                    if (successMessage) {
                                                                        noticeEle.html(successMessage + ' ' + resultData.error);
                                                                    } else {
                                                                        // DISPLAY THE SUCCESS NOTICE
                                                                        noticeEle.html('Thank you, your form has been encrypted to protect your privacy and submitted successfully!');
                                                                    }
                                                                } else {
                                                                    // DISPLAY THE SUCCESS NOTICE
                                                                    noticeEle.html('Thank you, your form has been encrypted to protect your privacy and submitted successfully!');
                                                                }
                                                                // RESET THE FORM
                                                                form[0].reset();
                                                                if (signature) {
                                                                    signatureEle.jSignature("reset");
                                                                }
                                                            } else {
                                                                // JUST A FAIL-SAFE JUST IN CASE WE DON'T ACTUALLY GET A SUCCESS MESSAGE
                                                                noticeEle.html(resultData.error);
                                                            }
                                                            hiddenForm.remove();

                                                            // ADD ACTIVE CLASS BACK TO SUBMIT BUTTON
                                                            submitButton.removeClass('inactive').addClass('active');
                                                        },
                                                        error: function (errorThrown) {
                                                            console.log(errorThrown);
                                                            noticeEle.html(errorThrown);
                                                            hiddenForm.remove();
                                                        }
                                                    });
                                                }, 1000);
                                            }
                                        }
                                    });
                                },
                                error: function (errorThrown) {
                                    console.log(errorThrown);
                                }
                            });
                        } else {
                            hiddenForm.find('#' + fileInputParentId).find('.file-prevent-overflow').html('<div class="cm_hipaa_file_input" data-file-key="">No File Attached</div>');

                            iterations = iterations + 1;
                            if (iterations === fileInputsLength) {
                                // UPDATE FILE UPLOAD STATUS
                                jQuery('.cm-hipaa-forms-file-upload-status').html('Submitting Form...');

                                // SET TIMEOUT TO ENSURE INPUTS OVERWRITTEN
                                setTimeout(function () {
                                    // SUBMIT FORM
                                    jQuery.ajax({
                                        method: 'POST',
                                        url: ajax.ajax_url,
                                        data: {
                                            'action': 'cm_hipaa_submit_caldera_form',
                                            'formId': formId,
                                            'formFields': formFields,
                                            'formHtml': hiddenForm.html(),
                                            'signature': signature,
                                            'nonce': nonce,
                                            'selectedUserSlug': selectedUserSlug,
                                            'notification_option': notificationOption,
                                            'notification_from_name': notificationFromName,
                                            'notification_from_email': notificationFromEmail,
                                            'notification_sendto': notificationSendTo,
                                            'notification_subject': notificationSubject,
                                            'notification_message': notificationMessage,
                                            'files': fileKeys.join(',')
                                        },
                                        success: function (data) {
                                            var resultData = JSON.parse(data);
                                            var formError = '';
                                            if(resultData.error) {
                                                formError = resultData.error;
                                            }

                                            if (resultData.success === 'success') {
                                                if (successHandler === 'message') {
                                                    // DISPLAY THE SUCCESS NOTICE
                                                    noticeEle.html(successMessage + ' ' + formError);
                                                } else if (successHandler === 'redirect') {
                                                    // REDIRECT TO SUCCESS PAGE
                                                    window.location = successRedirect;
                                                } else if (successHandler === 'callback') {
                                                    // MAKE SURE CALLBACK IS AN EXISTING FUNCTION
                                                    fnExists = typeof window[successCallback] === 'function';
                                                    if (fnExists) {
                                                        if (successCallbackParams) {
                                                            window[successCallback].apply(null, successCallbackParams.split(','));
                                                        } else {
                                                            window[successCallback]();
                                                        }
                                                    } else {
                                                        console.log(successCallback + ' is not an existing function!');
                                                    }
                                                    if (successMessage) {
                                                        noticeEle.html(successMessage + ' ' + formError);
                                                    } else {
                                                        // DISPLAY THE SUCCESS NOTICE
                                                        noticeEle.html('Thank you, your form has been encrypted to protect your privacy and submitted successfully!');
                                                    }
                                                } else {
                                                    // DISPLAY THE SUCCESS NOTICE
                                                    noticeEle.html('Thank you, your form has been encrypted to protect your privacy and submitted successfully!');
                                                }
                                                // RESET THE FORM
                                                form[0].reset();
                                                if (signature) {
                                                    signatureEle.jSignature("reset");
                                                }
                                            } else {
                                                // JUST A FAIL-SAFE JUST IN CASE WE DON'T ACTUALLY GET A SUCCESS MESSAGE
                                                noticeEle.html(formError);
                                            }
                                            hiddenForm.remove();

                                            // ADD ACTIVE CLASS BACK TO SUBMIT BUTTON
                                            submitButton.removeClass('inactive').addClass('active');
                                        },
                                        error: function (errorThrown) {
                                            console.log(errorThrown);
                                            noticeEle.html(errorThrown);
                                            hiddenForm.remove();

                                            // ADD ACTIVE CLASS BACK TO SUBMIT BUTTON
                                            submitButton.removeClass('inactive').addClass('active');
                                        }
                                    });
                                }, 1000);
                            }
                        }
                    });
                } else {
                    // SHOW LOADING BAR
                    noticeEle.html('<div class="cm-hipaa-forms-progress-wrapper"><div class="cm-hipaa-forms-progress"><div class="cm-hipaa-forms-progress-bar"><!--    <div class="cm-hipaa-forms-progress-label">10%</div>--></div></div></div>');
                    cmHipaaFormsProgress();

                    // SUBMIT FORM
                    jQuery.ajax({
                        method: 'POST',
                        url: ajax.ajax_url,
                        data: {
                            'action': 'cm_hipaa_submit_caldera_form',
                            'formId': formId,
                            'formFields': formFields,
                            'formHtml': hiddenForm.html(),
                            'signature': signature,
                            'nonce': nonce,
                            'selectedUserSlug': selectedUserSlug,
                            'notification_option': notificationOption,
                            'notification_from_name': notificationFromName,
                            'notification_from_email': notificationFromEmail,
                            'notification_sendto': notificationSendTo,
                            'notification_subject': notificationSubject,
                            'notification_message': notificationMessage
                        },
                        success: function (data) {
                            var resultData = JSON.parse(data);
                            var formError = '';
                            if(resultData.error) {
                                formError = resultData.error;
                            }

                            if (resultData.success === 'success') {
                                if(successHandler === 'message') {
                                    // DISPLAY THE SUCCESS NOTICE
                                    noticeEle.html(successMessage + ' ' + formError);
                                } else if(successHandler === 'redirect') {
                                    // REDIRECT TO SUCCESS PAGE
                                    window.location = successRedirect;
                                } else if(successHandler === 'callback') {
                                    // MAKE SURE CALLBACK IS AN EXISTING FUNCTION
                                    fnExists = typeof window[successCallback] === 'function';
                                    if(fnExists) {
                                        if(successCallbackParams) {
                                            window[successCallback].apply(null, successCallbackParams.split(','));
                                        } else {
                                            window[successCallback]();
                                        }
                                    } else {
                                        console.log(successCallback + ' is not an existing function!');
                                    }

                                    if(successMessage) {
                                        noticeEle.html(successMessage + ' ' + formError);
                                    } else {
                                        // DISPLAY THE SUCCESS NOTICE
                                        noticeEle.html('Thank you, your form has been encrypted to protect your privacy and submitted successfully!');
                                    }
                                } else {
                                    // DISPLAY THE SUCCESS NOTICE
                                    noticeEle.html('Thank you, your form has been encrypted to protect your privacy and submitted successfully!');
                                }


                                // RESET THE FORM
                                form[0].reset();

                                if(signature) {
                                    signatureEle.jSignature("reset");
                                }
                            } else {
                                // JUST A FAIL-SAFE JUST IN CASE WE DON'T ACTUALLY GET A SUCCESS MESSAGE
                                noticeEle.html(formError);
                            }

                            hiddenForm.remove();

                            // ADD ACTIVE CLASS BACK TO SUBMIT BUTTON
                            submitButton.removeClass('inactive').addClass('active');
                        },
                        error: function (errorThrown) {
                            console.log(errorThrown);
                            noticeEle.html(errorThrown);
                            hiddenForm.remove();

                            // ADD ACTIVE CLASS BACK TO SUBMIT BUTTON
                            submitButton.removeClass('inactive').addClass('active');
                        }
                    });
                }
            }
        }
    });

    /*** SUBMIT HIPAA ENABLED GRAVITY FORM ***/
    jQuery(document).on('click', 'form .cm-hipaa-forms-submit.gravity.active', function(e){
        var submitButton = jQuery(this);
        var form = jQuery(this).parents('form:first');
        var noticeEle = form.find('.cm-hipaa-notice');
        var formMainId = form.attr('id');
        var formId = form.attr('data-form-id');
        if(!formId) {
            formId = formMainId;
        }

        // REMOVE ACTIVE CLASS TO PREVENT RE-SUBMITTING
        submitButton.removeClass('active').addClass('inactive');

        // REMOVE REQUIRED FIELD NOTIFICATION ERROR IF EXISTS
        jQuery('.validation_error').remove();

        // GET FILE UPLOAD URL IF EXISTS
        var fileUploadUrl = cmHipaaScript.fileUploadUrl;

        var gravityEnabledForms = JSON.parse(cmHipaaScript.gravityEnabledForms); // DEPRECATED
        formIds = [];

        // LOOP GRAVITY FORMS AND PUSH IDS TO ARRAY - DEPRECATED
        for(var i = 0; i < gravityEnabledForms.length; i++){
            if(gravityEnabledForms[i]) {
                formIds.push('gform_' + gravityEnabledForms[i]);
            }
        }

        var enabledFormsSettings;
        if(cmHipaaScript.enabledFormsSettings) {
            enabledFormsSettings = JSON.parse(cmHipaaScript.enabledFormsSettings); // NEW JSON OBJECT ARRAY
        }


        // CHECK IF FORM OBJECT EXISTS IN ARRAY - NEW FORMS SETTINGS JSON OBJECT ARRAY METHOD
        var formFound;
        var successHandler;
        var successMessage;
        var successRedirect;
        var successCallback;
        var successCallbackParams;
        var selectedUserSlug = '';
        var showSignature;
        var notificationOption = '';
        var notificationFromName = '';
        var notificationFromEmail = '';
        var notificationSendTo = '';
        var notificationSubject = '';
        var notificationMessage = '';

        if(cmHipaaScript.enabledFormsSettings) {
            enabledFormsSettings.some(function (el) {
                if (el.form_builder === 'gravity' && 'gform_' + el.id === formId && el.enabled === 'yes') {
                    formFound = true;
                    successHandler = el.success_handler;
                    successMessage = el.success_message;
                    successRedirect = el.success_redirect;
                    successCallback = el.success_callback;
                    successCallbackParams = el.success_callback_params;
                    selectedUserSlug = el.selected_user_slug;
                    showSignature = el.show_signature;
                    notificationOption = el.notification_option;
                    notificationFromName = el.notification_from_name;
                    notificationFromEmail = el.notification_from_email;
                    notificationSendTo = el.notification_sendto;
                    notificationSubject = el.notification_subject;
                    notificationMessage = el.notification_message;
                }
            });
        }

        // SET SUCCESS MESSAGE IF NOT SET
        if(!successMessage) {
            successMessage = 'Thank you, your form has been encrypted to protect your privacy and submitted successfully!';
        }

        // IF FORM ID IS IN ENABLED FORMS PREVENT DEFAULT SUBMIT AND SEND TO CODE MONKEYS
        if(jQuery.inArray(formId, formIds) !== -1 || formFound) {
            e.preventDefault(); //Prevent the normal submission action

            var location;
            var locationEmail;
            var firstName;
            var lastName;
            var email;
            var phone;
            var selectedUser;
            var defaultBorder;

            // GET FILE INPUTS
            var fileInputs = form.find('input[type=file]');

            // CLONE THE FORM AS A HIDDEN VERSION IN ORDER TO REPLACE INPUTS WITH VALUES AND FORMAT TO SAVE
            form.after('<div class="cm-hipaa-forms-hidden-form" style="display:none;"></div>');
            form.clone().appendTo('.cm-hipaa-forms-hidden-form');
            var hiddenForm = jQuery('.cm-hipaa-forms-hidden-form form');
            var hiddenFormInputs = jQuery('.cm-hipaa-forms-hidden-form form :input');

            // REMOVE THE UNWANTED APPENDED ITEMS AT BOTTOM OF FORM
            hiddenForm.find('.cm-hipaa-forms-prepend-bottom, .cmprivacy-modal, .cmprivacy-modal-overlay').remove();
            hiddenForm.find('.gform_footer .gform_hidden').remove();

            // FIND DATE FIELDS
            var dateContainers = hiddenForm.find('.ginput_container_date');

            // LOOP DATE FIELDS
            var dateInputId = '';
            dateContainers.each(function() {
                // SET MAIN INPUT ID
                dateInputId = jQuery(this).find('.datepicker').attr('id');

                // REMOVE ICON
                jQuery(this).find('.ui-datepicker-trigger').remove();

                // REMOVE HIDDEN INPUT WITH ICON PATH
                jQuery(this).find('.gform_hidden').remove();
            });

            // FIND AND REMOVE HIDDEN DATEPICKER ICON INPUT IF EXISTS
            hiddenForm.find('.ginput_container_date').next('.gform_hidden').remove();

            // REMOVE STEPS LINKS AT TOP OF FORM IF EXIST
            hiddenForm.find('.gf_page_steps').remove();

            // REMOVE ADD/DELETE ROW ICONS IF EXIST
            hiddenForm.find('.add_list_item').remove();
            hiddenForm.find('.delete_list_item').remove();

            // REMOVE MULTI-SELECT SELECT OPTIONS ELEMENT
            hiddenForm.find('.chosen-drop').remove();

            // LOOP CLONED HIDDEN FORM INPUTS AND GET LABELS => VALUES & FULL HTML VERSION OF FORM
            var formFields = [];
            var validationErrors = [];

            var inputs = jQuery('#' + formMainId + ' :input');
            hiddenFormInputs.each(function() {
                var label = form.find('label[for="' + jQuery(this).attr('id') + '"]').text().trim();
                var value = jQuery(this).val();
                var fieldId = jQuery(this).attr('id');
                var fieldName = jQuery(this).attr('name');
                var fieldType = jQuery(this).attr('type');
                var gravityClassEle = jQuery(this).closest('.gfield');
                var gravityClass = gravityClassEle.attr('class');
                defaultBorder = jQuery(this).css('border');

                var locationSelect;
                var selectedUserSelect;
                var visibleSelect;
                var optionText;
                var checkboxParentLabel = '';
                var checkboxLabel;
                var radioValue = '';
                var checkboxValue = '';
                var fieldValue = '';
                var optionLabel = '';
                var optionValue = '';

                // IGNORE GRAVITY SPECIFIC HIDDEN FIELDS NOT NEEDED TO SHOW ON FORM
                if(fieldName !== 'is_submit_10' && fieldName !== 'gform_submit' && fieldName !== 'gform_unique_id' && fieldName !== 'state_10' && fieldName !== 'gform_target_page_number_10' && fieldName !== 'gform_source_page_number_10' && fieldName !== 'gform_field_values') {
                    // GET REQUIRED GRAVITY FIELDS
                    if (gravityClassEle.hasClass('hipaa_forms_office_location')) {
                        // SET LOCATION VALUES
                        locationSelect = form.find('#' + fieldId);
                        location = locationSelect.find('option:selected').text();
                        locationEmail = locationSelect.val();
                    } else if (gravityClassEle.hasClass('hipaa_forms_first_name') && jQuery(this).val()) {
                        firstName = jQuery(this).val();
                    } else if (gravityClassEle.hasClass('hipaa_forms_last_name') && jQuery(this).val()) {
                        lastName = jQuery(this).val();
                    } else if (gravityClassEle.hasClass('hipaa_forms_name')) {
                        if(jQuery(this).parent().hasClass('name_first') && jQuery(this).val()) {
                            firstName = jQuery(this).val();
                        } else if(jQuery(this).parent().hasClass('name_last') && jQuery(this).val()) {
                            lastName = jQuery(this).val();
                        }
                    } else if (gravityClassEle.hasClass('hipaa_forms_email')) {
                        email = jQuery(this).val();
                    } else if (gravityClassEle.hasClass('hipaa_forms_phone')) {
                        phone = jQuery(this).val();
                    } else if (selectedUserSlug && gravityClassEle.hasClass(selectedUserSlug)) {
                        selectedUserSelect = form.find('#' + fieldId);
                        selectedUser = selectedUserSelect.val();
                    }

                    if (!fieldType) {
                        if (jQuery(this).is('select')) {
                            fieldType = 'select';
                            visibleSelect = form.find('#' + fieldId);
                            optionText = visibleSelect.find('option:selected').text();
                            value = visibleSelect.val();
                            fieldValue = value;
                        } else if (jQuery(this).is('textarea')) {
                            fieldType = 'textarea';
                            var visibleTextArea = form.find('#' + fieldId);
                            value = visibleTextArea.attr('value');
                            fieldValue = value;
                            // TRY THE VALUE ATTR FIRST AND IF NO VALUE TRY NORMAL JQUERY VAL(), NOT SURE WHY ONE WORKS OVER THE OTHER SOMETIMES
                            if(!value || value === 'undefined') {
                                value = visibleTextArea.val();
                                fieldValue = value;
                            }
                        }
                    }

                    // IF CHECKBOX OR RADIO SET OPTIONS
                    var cbOptionsWrapper;
                    var checkOrRadio = false;
                    var cbOptionChecked;
                    if(fieldType === 'checkbox' || fieldType === 'radio') {
                        checkOrRadio = true;
                        cbOptionsWrapperId = gravityClassEle.attr('id');
                        cbOptionsWrapper = form.find('#' + cbOptionsWrapperId);
                        cbOptionChecked = form.find('#' + fieldId).prop('checked');
                    }

                    // CHECK IF FIELD IS FILE UPLOAD & SET AS HIDDEN INPUT VALUE (SAFARI REQUIRED FIELD FIX)
                    if(fieldType === 'file') {
                        value = form.find('#' + fieldId).val();
                    }

                    var isVisible = form.find('#' + fieldId).is(':visible');
                    var isDisabled = jQuery(this).prop('disabled');

                    // SET IF FIELD IS REQUIRED
                    var required = false;
                    if(isDisabled !== true && isVisible === true && (jQuery(this).attr('required') || jQuery(this).attr('aria-required') || gravityClassEle.hasClass('gfield_contains_required') && checkOrRadio !== true && !jQuery(this).parent().hasClass('address_line_2'))) {
                        required = true;
                    }


                    // GET REQUIRED FIELDS THAT DO NOT HAVE A VALUE
                    if (required && !value || isDisabled !== true && isVisible === true && checkOrRadio === true && cbOptionsWrapper.hasClass('gfield_contains_required') && cbOptionChecked === false) {
                        // MAKE SURE OBJECT DOES NOT ALREADY EXIST, CLEARING ARRAY ABOVE DOESN'T SEEM TO WORK HERE
                        var errorFound;
                        var cbIgnore;

                        // IF CHECKBOX SPLIT NAME AT PERIOD FOR CONSISTENCY
                        if(fieldType === 'checkbox' && !jQuery(this).parent().hasClass('gchoice_select_all')) {
                            fieldName = fieldName.split('.')[0];
                        }

                        // IF CHECKBOX OR RADIO, GROUP OPTIONS & ENSURE ONE IS CHECKED
                        if(cbOptionsWrapper) {
                            var cbOptions = cbOptionsWrapper.find('input');
                            cbOptions.each(function() {
                                if(jQuery(this).prop('checked')) {
                                    cbIgnore = true;

                                    return false;
                                }
                            });
                        }

                        if(validationErrors.length > 0) {
                            validationErrors.some(function(el) {
                                // CHECK IF ERROR ALREADY EXISTS IN VALIDATION ERRORS ARRAY
                                if(el.fieldId === fieldId || el.name === fieldName) {
                                    errorFound = true;
                                }
                            });
                        }

                        if(!errorFound && !cbIgnore) {
                            validationErrors.push({
                                'fieldId': fieldId,
                                'label': label,
                                'name': fieldName,
                                'visible': isVisible
                            });
                        }
                    }

                    // SET CHECKBOX OR RADIO FIELD LABEL AND INPUT LABEL
                    if (fieldType === 'checkbox' || fieldType === 'radio') {
                        if (jQuery(this).prop('checked') === true) {
                            checkboxLabel = jQuery(this).parent().find('label').text().trim();

                            if(jQuery(this).hasClass('cm-hipaa-forms-privacy-agree')) {
                                label = 'Privacy Agreement';
                            } else {
                                label = cbOptionsWrapper.find('.gfield_label').text().trim();
                            }

                            checkboxParentLabel = label;
                            optionLabel = checkboxLabel;
                            optionValue = value;
                        }
                    }

                    // REPLACE HIDDEN HTML VERSION INPUTS WITH VALUES
                    if (fieldType === 'select') {
                        jQuery(this).replaceWith(optionText);
                    } else if (fieldType === 'checkbox') {
                        // SET CHECKBOX VALUE IF DIFFERENT THAN LABEL
                        if(value !== checkboxLabel) {
                            checkboxValue = value;
                        }

                        if (jQuery(this).prop('checked') === true) {
                            // SET CHECKED CHECKBOX IMAGE AND VALUE
                            value = '<span class="cm-hipaa-forms-checkbox-value cm-hipaa-forms-checkbox-checked" style="background: #f7f5a091;"><img src="https://www.hipaaforms.online/api-assets/checkbox.png" alt="Checked" style="height: 16px; padding-right: 5px; vertical-align: middle;" />' + checkboxValue + '</span>';

                            if(checkboxParentLabel) {
                                fieldValue = 'checked';
                            }
                        } else {
                            // SET UNCHECKED CHECKBOX IMAGE AND VALUE
                            value = '<span class="cm-hipaa-forms-checkbox-value  cm-hipaa-forms-checkbox-not-checked"><img src="https://www.hipaaforms.online/api-assets/checkbox-unchecked.png" alt="Not Checked" style="height: 16px; padding-right: 5px; vertical-align: middle;" /></span>';
                        }
                        jQuery(this).replaceWith(value);
                    } else if (fieldType === 'radio') {
                        // SET RADIO VALUE IF DIFFERENT THAN LABEL
                        if(value !== checkboxLabel) {
                            radioValue = value;
                        }

                        if (jQuery(this).prop('checked') === true) {
                            // SET CHECKED RADIO BUTTON IMAGE AND VALUE
                            value = '<span class="cm-hipaa-forms-radio-value cm-hipaa-forms-radio-checked" style="background: #f7f5a091;"><img src="https://www.hipaaforms.online/api-assets/radio-checked.png" alt="Selected" style="height: 16px; padding-right: 5px; vertical-align: middle;" />' + radioValue + '</span>';

                            if(checkboxParentLabel) {
                                fieldValue = 'checked';
                            }
                        } else {
                            // SET UNCHECKED RADIO BUTTON IMAGE AND VALUE
                            value = '<span class="cm-hipaa-forms-radio-value cm-hipaa-forms-radio-not-checked"><img src="https://www.hipaaforms.online/api-assets/radio-unchecked.png" alt="Not Selected" style="height: 16px; padding-right: 5px; vertical-align: middle;" /></span>';
                        }
                        jQuery(this).replaceWith(value);
                    } else {
                        jQuery(this).replaceWith(value);
                    }

                    if (label && fieldValue) {
                        // IF LABEL EXISTS PUSH TO FIELDS ARRAY
                        formFields.push({
                            'label': label,
                            'option_label': optionLabel,
                            'option_value': optionValue,
                            'value': fieldValue,
                            'field_id': fieldId,
                            'field_type': fieldType,
                            'option_text': optionText
                        });
                    }
                }
            });

            // GET SIGNATURE SVG BASE64 DATA
            var signature;
            var signatureEle;
            var datapair;
            if(showSignature === 'yes') {
                signatureEle = form.find('.cm-hipaa-form-signature');
                datapair = signatureEle.jSignature("getData", "svgbase64");
                if(datapair) {
                    signature = "data:" + datapair[0] + "," + datapair[1];

                    formFields.push({
                        'label': 'Signature',
                        'option_label': 'data:',
                        'option_value': datapair[0],
                        'value': datapair[1],
                        'field_id': '',
                        'field_type': 'signature',
                        'option_text': ''
                    });
                }
            }

            // SET PRIVACY AGREEMENT ELEMENT
            var privacyAgreementEle = form.find('.cm-hipaa-forms-privacy-agree');

            // RESET ERRORS
            jQuery('.gfield_error').removeClass('gfield_error');
            privacyAgreementEle.parent().css('border', '0');
            if(signatureEle) {
                signatureEle.find('.jSignature').css('border', '0');
            }
            noticeEle.html('');
            form.find('.validation_error, .validation_message').remove();

            // MULTI-PAGE FORM RENDERING
            var gravityFormPages = hiddenForm.find('.gform_page');
            if(gravityFormPages && gravityFormPages.length > 0) {
                // REMOVE PROGRESS BAR
                hiddenForm.find('.gf_progressbar_wrapper').remove();

                gravityFormPages.each(function(index) {
                    // MAKE PAGE ELEMENT VISIBLE
                    jQuery(this).css({
                        'display': 'block'
                    });

                    // REMOVE BUTTONS
                    jQuery(this).find('.gform_page_footer').remove();

                    // APPEND PAGE BREAK TO END OF PAGE FOR MPDF IF NOT LAST ELEMENT
                    if(index !== (gravityFormPages.length - 1)) {
                        jQuery(this).append('<pagebreak />');
                    }
                });
            }

            var nonce = cmHipaaScript.nonce;

            if(!jQuery.isEmptyObject(validationErrors)) {
                // ADD ERROR MESSAGE TO TOP OF FORM
                form.find('.gform_body').before('<div class="validation_error">There was a problem with your submission. Errors have been highlighted below.</div>');

                // VALIDATE FIELDS
                jQuery.each(validationErrors, function(key, val) {
                    var invalidFieldId = val.fieldId;
                    var invalidField = jQuery('#' + invalidFieldId);
                    var invalidFieldType = invalidField.attr('type');
                    var requiredMessage;

                    if(invalidField.closest('li').hasClass('hipaa_forms_office_location') && !locationEmail) {
                        requiredMessage = 'Location select options must have a value!  This should be an email adress for the person at the specific location that should receive the form submission notice.';
                    } else {
                        requiredMessage = 'This field is required.';
                    }

                    // ADD ERROR CLASS & NOTICE MESSAGE
                    var invalidFieldWrapper;
                    if(invalidFieldType === 'checkbox' || invalidFieldType === 'radio') {
                        invalidFieldWrapper = invalidField.parent().parent().parent().parent();
                        invalidFieldWrapper.addClass('gfield_error').append('<div class="gfield_description validation_message">' + requiredMessage + '</div>');
                    } else {
                        invalidFieldWrapper = invalidField.closest('li');
                        invalidFieldWrapper.addClass('gfield_error').append('<div class="gfield_description validation_message">' + requiredMessage + '</div>');
                    }
                });

                // REMOVE HIDDEN FORM
                jQuery('.cm-hipaa-forms-hidden-form').remove();

                // SCROLL TO FIRST INVALID FIELD
                jQuery('html, body').animate({
                    scrollTop: jQuery('#' + validationErrors[0].fieldId).offset().top-125
                }, 500, 'linear');

                // ADD ACTIVE CLASS BACK TO SUBMIT BUTTON
                submitButton.removeClass('inactive').addClass('active');
            } else if(privacyAgreementEle.prop('checked') !== true) {
                // ADD RED BORDER TO PRIVACY FIELD
                privacyAgreementEle.parent().css('border', '1px solid red');

                // UPDATE NOTICE ELEMENT
                noticeEle.html('You must agree to the HIPAA Privacy Agreement');

                // REMOVE HIDDEN FORM
                jQuery('.cm-hipaa-forms-hidden-form').remove();

                // ADD ACTIVE CLASS BACK TO SUBMIT BUTTON
                submitButton.removeClass('inactive').addClass('active');
            } else if(showSignature === 'yes' && !signatureEle.find('.jSignature').hasClass('cm-valid-sig')) {
                // ADD RED BORDER TO SIGNATURE FIELD
                signatureEle.find('.jSignature').css('border', '1px solid red');

                // UPDATE NOTICE ELEMENT
                noticeEle.html('You must sign the form');

                // REMOVE HIDDEN FORM
                jQuery('.cm-hipaa-forms-hidden-form').remove();

                // ADD ACTIVE CLASS BACK TO SUBMIT BUTTON
                submitButton.removeClass('inactive').addClass('active');
            } else {
                // IF FILE INPUTS EXIST & FILE UPLOAD ENABLED
                if(fileInputs.length > 0 && fileUploadEnabled === 'yes') {
                    // SHOW LOADING BAR
                    noticeEle.html('<div class="cm-hipaa-forms-file-upload-status">Uploading Files...</div><div class="cm-hipaa-forms-progress-wrapper"><div class="cm-hipaa-forms-progress"><div class="cm-hipaa-forms-progress-bar"><!--    <div class="cm-hipaa-forms-progress-label">10%</div>--></div></div></div>');
                    cmHipaaFormsProgress();

                    var fileInputsLength = fileInputs.length;
                    var iterations = 0;
                    var fileKeys = [];

                    fileInputs.each(function(index) {
                        var fileInputParentId = jQuery(this).parent().parent().attr('id');
                        var fileLabel = jQuery(this).parent().parent().find('label').text();
                        fileLabel = fileLabel.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');

                        // GET FILE
                        var theFormFile = jQuery(this).get()[0].files[0];

                        if(theFormFile) {
                            var fileType = theFormFile.type;
                            var fileName = theFormFile.name;

                            // GET PRESIGNED FILE UPLOAD URL
                            jQuery.ajax({
                                method: 'POST',
                                url: ajax.ajax_url,
                                data: {
                                    'action': 'cm_hipaa_get_file_upload_url',
                                    'file_name': fileName,
                                    'nonce': cmHipaaScript.nonce
                                },
                                success: function (data) {
                                    var resultData = JSON.parse(data);
                                    var fileUploadUrl = resultData.file_upload_url;
                                    var fileKey = resultData.file_key;

                                    // PUSH FILE KEYS TO ARRAY
                                    fileKeys.push(fileKey);

                                    // UPLOAD THE FILE
                                    jQuery.ajax({
                                        url: fileUploadUrl, // the presigned URL
                                        type: 'PUT',
                                        processData: false,
                                        contentType: fileType,
                                        headers: {'x-amz-tagging': 'label=' + fileLabel},
                                        data: theFormFile,
                                        success: function () {
                                            var hiddenFileUploadContainer = hiddenForm.find('#' + fileInputParentId).find('.ginput_container_fileupload');
                                            var existingFiles = hiddenForm.find('#' + fileInputParentId).find('.cm_hipaa_file_input'); // GET EXISTING FILES ALREADY UPLOADED IN HIDDEN FORM
                                            hiddenFileUploadContainer.html(''); // CLEAR THE CONTAINER ELEMENT

                                            jQuery.each(existingFiles, function() {
                                                // APPEND EXISTING FILES ALREADY UPLOADED BACK INTO CONTAINER ELEMENT
                                                hiddenFileUploadContainer.append(jQuery(this));
                                            });

                                            // APPEND NEW UPLOADED FILE TO CONTAINER ELEMENT
                                            hiddenFileUploadContainer.append('<div class="cm_hipaa_file_input" data-file-key="' + fileKey + '">' + fileKey + '</div>');

                                            iterations = iterations + 1;
                                            if (iterations === fileInputsLength) {
                                                // UPDATE FILE UPLOAD STATUS
                                                jQuery('.cm-hipaa-forms-file-upload-status').html('Submitting Form...');
                                                // SET TIMEOUT TO ENSURE INPUTS OVERWRITTEN
                                                setTimeout(function () {
                                                    // SUBMIT FORM
                                                    jQuery.ajax({
                                                        method: 'POST',
                                                        url: ajax.ajax_url,
                                                        data: {
                                                            'action': 'cm_hipaa_submit_gravity_form',
                                                            'formId': formId,
                                                            'formFields': formFields,
                                                            'formHtml': hiddenForm.html(),
                                                            'signature': signature,
                                                            'nonce': nonce,
                                                            'selectedUser': selectedUser,
                                                            'location': location,
                                                            'locationEmail': locationEmail,
                                                            'firstName': firstName,
                                                            'lastName': lastName,
                                                            'email': email,
                                                            'phone': phone,
                                                            'notification_option': notificationOption,
                                                            'notification_from_name': notificationFromName,
                                                            'notification_from_email': notificationFromEmail,
                                                            'notification_sendto': notificationSendTo,
                                                            'notification_subject': notificationSubject,
                                                            'notification_message': notificationMessage,
                                                            'files': fileKeys.join(',')
                                                        },
                                                        success: function (data) {
                                                            var resultData = JSON.parse(data);

                                                            if (resultData.success === 'success') {
                                                                if (successHandler === 'message') {
                                                                    // DISPLAY THE SUCCESS NOTICE
                                                                    noticeEle.html(successMessage + ' ' + resultData.error);
                                                                } else if (successHandler === 'redirect') {
                                                                    // REDIRECT TO SUCCESS PAGE
                                                                    window.location = successRedirect;
                                                                } else if (successHandler === 'callback') {
                                                                    // MAKE SURE CALLBACK IS AN EXISTING FUNCTION
                                                                    fnExists = typeof window[successCallback] === 'function';
                                                                    if (fnExists) {
                                                                        if (successCallbackParams) {
                                                                            window[successCallback].apply(null, successCallbackParams.split(','));
                                                                        } else {
                                                                            window[successCallback]();
                                                                        }
                                                                    } else {
                                                                        console.log(successCallback + ' is not an existing function!');
                                                                    }
                                                                    if (successMessage) {
                                                                        noticeEle.html(successMessage + ' ' + resultData.error);
                                                                    } else {
                                                                        // DISPLAY THE SUCCESS NOTICE
                                                                        noticeEle.html('Thank you, your form has been encrypted to protect your privacy and submitted successfully!');
                                                                    }
                                                                } else {
                                                                    // DISPLAY THE SUCCESS NOTICE
                                                                    noticeEle.html('Thank you, your form has been encrypted to protect your privacy and submitted successfully!');
                                                                }
                                                                // RESET THE FORM
                                                                form[0].reset();
                                                                if (signature) {
                                                                    signatureEle.jSignature("reset");
                                                                }
                                                            } else {
                                                                // JUST A FAIL-SAFE JUST IN CASE WE DON'T ACTUALLY GET A SUCCESS MESSAGE
                                                                noticeEle.html(resultData.error);
                                                            }
                                                            hiddenForm.remove();

                                                            // ADD ACTIVE CLASS BACK TO SUBMIT BUTTON
                                                            submitButton.removeClass('inactive').addClass('active');
                                                        },
                                                        error: function (errorThrown) {
                                                            console.log(errorThrown);
                                                            noticeEle.html(errorThrown);
                                                            hiddenForm.remove();

                                                            // ADD ACTIVE CLASS BACK TO SUBMIT BUTTON
                                                            submitButton.removeClass('inactive').addClass('active');
                                                        }
                                                    });
                                                }, 500);
                                            }
                                        }
                                    });
                                },
                                error: function (errorThrown) {
                                    console.log(errorThrown);
                                }
                            });
                        } else {
                            hiddenForm.find('#' + fileInputParentId).find('.ginput_container_fileupload').html('<div class="cm_hipaa_file_input" data-file-key="">No File Attached</div>');

                            iterations = iterations + 1;
                            if (iterations === fileInputsLength) {
                                // UPDATE FILE UPLOAD STATUS
                                jQuery('.cm-hipaa-forms-file-upload-status').html('Submitting Form...');
                                // SET TIMEOUT TO ENSURE INPUTS OVERWRITTEN
                                setTimeout(function () {
                                    // SUBMIT FORM
                                    jQuery.ajax({
                                        method: 'POST',
                                        url: ajax.ajax_url,
                                        data: {
                                            'action': 'cm_hipaa_submit_gravity_form',
                                            'formId': formId,
                                            'formFields': formFields,
                                            'formHtml': hiddenForm.html(),
                                            'signature': signature,
                                            'nonce': nonce,
                                            'selectedUser': selectedUser,
                                            'location': location,
                                            'locationEmail': locationEmail,
                                            'firstName': firstName,
                                            'lastName': lastName,
                                            'email': email,
                                            'phone': phone,
                                            'notification_option': notificationOption,
                                            'notification_from_name': notificationFromName,
                                            'notification_from_email': notificationFromEmail,
                                            'notification_sendto': notificationSendTo,
                                            'notification_subject': notificationSubject,
                                            'notification_message': notificationMessage,
                                            'files': fileKeys.join(',')
                                        },
                                        success: function (data) {
                                            var resultData = JSON.parse(data);
                                            var formError = '';
                                            if(resultData.error) {
                                                formError = resultData.error;
                                            }

                                            if (resultData.success === 'success') {
                                                if (successHandler === 'message') {
                                                    // DISPLAY THE SUCCESS NOTICE
                                                    noticeEle.html(successMessage + ' ' + formError);
                                                } else if (successHandler === 'redirect') {
                                                    // REDIRECT TO SUCCESS PAGE
                                                    window.location = successRedirect;
                                                } else if (successHandler === 'callback') {
                                                    // MAKE SURE CALLBACK IS AN EXISTING FUNCTION
                                                    fnExists = typeof window[successCallback] === 'function';
                                                    if (fnExists) {
                                                        if (successCallbackParams) {
                                                            window[successCallback].apply(null, successCallbackParams.split(','));
                                                        } else {
                                                            window[successCallback]();
                                                        }
                                                    } else {
                                                        console.log(successCallback + ' is not an existing function!');
                                                    }
                                                    if (successMessage) {
                                                        noticeEle.html(successMessage + ' ' + formError);
                                                    } else {
                                                        // DISPLAY THE SUCCESS NOTICE
                                                        noticeEle.html('Thank you, your form has been encrypted to protect your privacy and submitted successfully!');
                                                    }
                                                } else {
                                                    // DISPLAY THE SUCCESS NOTICE
                                                    noticeEle.html('Thank you, your form has been encrypted to protect your privacy and submitted successfully!');
                                                }
                                                // RESET THE FORM
                                                form[0].reset();
                                                if (signature) {
                                                    signatureEle.jSignature("reset");
                                                }
                                            } else {
                                                // JUST A FAIL-SAFE JUST IN CASE WE DON'T ACTUALLY GET A SUCCESS MESSAGE
                                                noticeEle.html(formError);
                                            }
                                            hiddenForm.remove();

                                            // ADD ACTIVE CLASS BACK TO SUBMIT BUTTON
                                            submitButton.removeClass('inactive').addClass('active');
                                        },
                                        error: function (errorThrown) {
                                            console.log(errorThrown);
                                            noticeEle.html(errorThrown);
                                            hiddenForm.remove();

                                            // ADD ACTIVE CLASS BACK TO SUBMIT BUTTON
                                            submitButton.removeClass('inactive').addClass('active');
                                        }
                                    });
                                }, 500);
                            }
                        }
                    });
                } else {
                    // SHOW LOADING BAR
                    noticeEle.html('<div class="cm-hipaa-forms-progress-wrapper"><div class="cm-hipaa-forms-progress"><div class="cm-hipaa-forms-progress-bar"><!--    <div class="cm-hipaa-forms-progress-label">10%</div>--></div></div></div>');
                    cmHipaaFormsProgress();

                    // SUBMIT FORM
                    jQuery.ajax({
                        method: 'POST',
                        url: ajax.ajax_url,
                        data: {
                            'action': 'cm_hipaa_submit_gravity_form',
                            'formId': formId,
                            'formFields': formFields,
                            'formHtml': hiddenForm.html(),
                            'signature': signature,
                            'nonce': nonce,
                            'selectedUser': selectedUser,
                            'location': location,
                            'locationEmail': locationEmail,
                            'firstName': firstName,
                            'lastName': lastName,
                            'email': email,
                            'phone': phone,
                            'notification_option': notificationOption,
                            'notification_from_name': notificationFromName,
                            'notification_from_email': notificationFromEmail,
                            'notification_sendto': notificationSendTo,
                            'notification_subject': notificationSubject,
                            'notification_message': notificationMessage
                        },
                        success: function (data) {
                            var resultData = JSON.parse(data);
                            var formError = '';
                            if(resultData.error) {
                                formError = resultData.error;
                            }

                            if (resultData.success === 'success') {
                                if(successHandler === 'message') {
                                    // DISPLAY THE SUCCESS NOTICE
                                    noticeEle.html(successMessage + ' ' + formError);
                                } else if(successHandler === 'redirect') {
                                    // REDIRECT TO SUCCESS PAGE
                                    window.location = successRedirect;
                                } else if(successHandler === 'callback') {
                                    // MAKE SURE CALLBACK IS AN EXISTING FUNCTION
                                    fnExists = typeof window[successCallback] === 'function';
                                    if(fnExists) {
                                        if(successCallbackParams) {
                                            window[successCallback].apply(null, successCallbackParams.split(','));
                                        } else {
                                            window[successCallback]();
                                        }
                                    } else {
                                        console.log(successCallback + ' is not an existing function!');
                                    }

                                    if(successMessage) {
                                        noticeEle.html(successMessage + ' ' + formError);
                                    } else {
                                        // DISPLAY THE SUCCESS NOTICE
                                        noticeEle.html('Thank you, your form has been encrypted to protect your privacy and submitted successfully!');
                                    }
                                } else {
                                    // DISPLAY THE SUCCESS NOTICE
                                    noticeEle.html('Thank you, your form has been encrypted to protect your privacy and submitted successfully!');
                                }

                                // RESET THE FORM
                                form[0].reset();

                                if(signature) {
                                    signatureEle.jSignature("reset");
                                }
                            } else {
                                // JUST A FAIL-SAFE JUST IN CASE WE DON'T ACTUALLY GET A SUCCESS MESSAGE
                                noticeEle.html(formError);
                            }

                            hiddenForm.remove();

                            // ADD ACTIVE CLASS BACK TO SUBMIT BUTTON
                            submitButton.removeClass('inactive').addClass('active');
                        },
                        error: function (errorThrown) {
                            console.log(errorThrown);
                            noticeEle.html(errorThrown);
                            hiddenForm.remove();

                            // ADD ACTIVE CLASS BACK TO SUBMIT BUTTON
                            submitButton.removeClass('inactive').addClass('active');
                        }
                    });
                }
            }
        }
    });

    /*** RESET SIGNATURE ***/
    jQuery(document).on('click', '.cm-hipaa-form-signature-reset', function() {
        jQuery(this).parent().parent().find('.cm-hipaa-form-signature').jSignature("reset");
    });

    /*** PRIVACY MODAL WINDOW ***/
    jQuery(document).on('click', '.cmprivacy-modal .cmprivacy-close-btn', function() {
        jQuery('.cmprivacy-modal').toggle('closed');
        jQuery('.cmprivacy-modal-overlay').toggle('closed');
    });
    jQuery(document).on('click', '.cm-hipaa-privacy-statement a', function(e) {
        e.preventDefault();
        jQuery('.cmprivacy-modal').toggle('closed');
        jQuery('.cmprivacy-modal-overlay').toggle('closed');
    });
});

//Custom progress bar
function cmHipaaFormsProgress() {
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