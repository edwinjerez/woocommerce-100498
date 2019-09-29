<?php
/**
 * Created by Code Monkeys LLC
 * http://www.codemonkeysllc.com
 * User: Spencer
 * Date: 10/24/2016
 * Time: 4:24 PM
 */

class cmHipaaForms {
    /*
     * ADMIN CLASSES
     */

    /*** ENCRYPT ***/
    public static function encrypt($string, $key, $iv) {
        $output = false;
        $encrypt_method = "AES-256-CBC";

        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);

        return $output;
    }

    /*** DECRYPT ***/
    public static function decrypt($string, $key, $iv) {
        $encrypt_method = "AES-256-CBC";

        return openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }

    /*** GENERATE RANDOM STRING ***/
    public static function randStringGen($length) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";

        return substr( str_shuffle( $chars ), 0, $length );
    }

    /*** GENERATE ENCRYPTION KEY ***/
    public static function keygen() {
        $secret_key = self::randStringGen(8);

        return hash('sha256', $secret_key);
    }

    /*** GENERATE IV KEY ***/
    public static function ivgen() {
        $secret_iv = self::randStringGen(8);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        return substr(hash('sha256', $secret_iv), 0, 16);
    }

    /*** GET ROOT DOMAIN ***/
    public static function getRootDomain($tld = false) {
        $url = get_site_url();
        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : '';
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $m)) {
            return ($tld === true) ? substr($m['domain'], ($pos = strpos($m['domain'], '.')) !== false ? $pos + 1 : 0) : $m['domain'];
        } else if($domain) {
            // FALLBACK IN CASE PREG_MATCH FAILS FOR SOME REASON
            return $domain;
        }
        return false;
    }

    /*** SUPPORT TICKET KEY ***/
    private function getTicketsKey() {
        return '3Y3t6F9AWtp3dkrLTlABWcBAj11ZRIfiBeEa7A95x90FRumhA8uBGb1n2Mf1SsOn';
    }

    /*** GET USERS WITH ADMIN OR HIPAA ROLES ***/
    private function getApprovedUsers() {
        $users = array();
        $roles = array('administrator', 'hipaa_forms');

        foreach ($roles as $role) {
            $users_query = new WP_User_Query(array(
                'fields' => 'all_with_meta',
                'role' => $role,
                'orderby' => 'display_name'
            ));
            $results = $users_query->get_results();
            if ($results) {
                $users = array_merge($users, $results);
            }
        }

        return $users;
    }

    /*** GET ACCOUNT INFO ***/
    private function getAccount($licenseKey, $domain) {
        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'getaccount',
            'license_key' => $licenseKey,
            'domain' => self::getRootDomain()
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        if($output) {
            foreach($output as $data) {
                $error = '';
                if(isset($data->error)) {
                    $error = $data->error;
                }

                if($error) {
                    $this->error = $error;
                } else {
                    $success = $data->success;

                    if ($success == 'success') {
                        if(isset($data->id)) {
                            $this->id = $data->id;
                        }
                        if(isset($data->customer_id)) {
                            $this->customer_id = $data->customer_id;
                        }
                        if(isset($data->partner_id)) {
                            $this->partner_id = $data->partner_id;
                        }
                        if(isset($data->first_name)) {
                            $this->first_name = $data->first_name;
                        }
                        if(isset($data->last_name)) {
                            $this->last_name = $data->last_name;
                        }
                        if(isset($data->email)) {
                            $this->email = $data->email;
                        }
                        if(isset($data->phone)) {
                            $this->phone = $data->phone;
                        }
                        if(isset($data->expiration_date)) {
                            $this->expiration_date = $data->expiration_date;
                        }
                        if(isset($data->baa)) {
                            $this->baa = $data->baa;
                        }
                        if(isset($data->add_ons)) {
                            $this->add_ons = $data->add_ons;
                        }
                        if(isset($data->aws_key)) {
                            $this->aws_key = $data->aws_key;
                        }
                        if(isset($data->aws_secret_key)) {
                            $this->aws_secret_key = $data->aws_secret_key;
                        }
                        if(isset($data->bucket)) {
                            $this->bucket = $data->bucket;
                        }
                    } else {
                        $this->error = 'API did not return a success message';
                    }
                }
            }
        } else {
            $this->error = 'API did not return a success message';
        }

        return $this;
    }

    /*** GET FILE UPLOAD URL ***/
    public function getFileUploadUrl($fileName) {
        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'getfileuploadurl',
            'license_key' => esc_attr(get_option('license_key')),
            'domain' => self::getRootDomain(),
            'file_name' => $fileName
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        if($output) {
            foreach($output as $data) {
                $error = '';
                if(isset($data->error)) {
                    $error = $data->error;
                }

                if($error) {
                    $this->error = $error;
                } else {
                    $success = $data->success;

                    if ($success == 'success') {
                        $this->file_upload_url = $data->file_upload_url;
                        $this->file_key = $data->file_key;
                    } else {
                        $this->error = 'API did not return a success message';
                    }
                }
            }
        } else {
            $this->error = 'API did not return a success message';
        }

        return $this;
    }

    /*** GET HIPAA USER ROLE CAPABILITIES ***/
    public function getHipaaCapabilities() {
        $selectedCaps = explode(',', esc_attr(get_option('hipaa_role_capabilities')));

        // SET CAPABILITIES ARRAY
        $caps = array(
            'activate_plugins' => 'Activate Plugins',
            'create_roles' => 'Create Roles',
            'create_users' => 'Create Users',
            'delete_documents' => 'Delete Documents',
            'delete_others_documents' => 'Delete Other\'s Documents',
            'delete_others_pages' => 'Delete Other\'s Pages',
            'delete_others_posts' => 'Delete Other\'s Posts',
            'delete_pages' => 'Delete Pages',
            'delete_plugins' => 'Delete Plugins',
            'delete_posts' => 'Delete Posts',
            'delete_private_documents' => 'Delete Private Documents',
            'delete_private_pages' => 'Delete Private Pages',
            'delete_private_posts' => 'Delete Private Posts',
            'delete_published_documents' => 'Delete Published Documents',
            'delete_published_pages' => 'Delete Published Pages',
            'delete_published_posts' => 'Delete Published Posts',
            'delete_roles' => 'Delete Roles',
            'delete_themes' => 'Delete Themes',
            'delete_users' => 'Delete Users',
            'edit_dashboard' => 'Edit Dashboard',
            'edit_documents' => 'Edit Documents',
            'edit_files' => 'Edit Files',
            'edit_others_documents' => 'Edit Other\'s Documents',
            'edit_others_pages' => 'Edit Other\'s Pages',
            'edit_others_posts' => 'Edit Other\'s Posts',
            'edit_pages' => 'Edit Pages',
            'edit_plugins' => 'Edit Plugins',
            'edit_posts' => 'Edit Posts',
            'edit_private_documents' => 'Edit Private Documents',
            'edit_private_pages' => 'Edit Private Pages',
            'edit_private_posts' => 'Edit Private Posts',
            'edit_published_documents' => 'Edit Published Documents',
            'edit_published_pages' => 'Edit Published Pages',
            'edit_published_posts' => 'Edit Published Posts',
            'edit_roles' => 'Edit Roles',
            'edit_themes' => 'Edit Themes',
            'edit_theme_options' => 'Edit Theme Options',
            'edit_users' => 'Edit Users',
            'export' => 'Export',
            'import' => 'Import',
            'install_plugins' => 'Install Plugins',
            'install_themes' => 'Install Themes',
            'list_roles' => 'List Roles',
            'list_users' => 'List Users',
            'manage_categories' => 'Manage Categories',
            'manage_links' => 'Manage Links',
            'manage_options' => 'Manage Options',
            'moderate_comments' => 'Moderate Comments',
            'override_document_lock' => 'Override Document Lock',
            'promote_users' => 'Promote Users',
            'publish_documents' => 'Publish Documents',
            'publish_pages' => 'Publish Pages',
            'publish_posts' => 'Publish Posts',
            'read_documents' => 'Read Documents',
            'read_document_revisions' => 'Read Document Revisions',
            'read_private_documents' => 'Read Private Documents',
            'read_private_pages' => 'Read Private Pages',
            'read_private_posts' => 'Read Private Posts',
            'remove_users' => 'Remove Users',
            'restrict_content' => 'Restrict Content',
            'switch_themes' => 'Switch Themes',
            'unfiltered_html' => 'Unfiltered HTML',
            'unfiltered_upload' => 'Unfiltered Upload',
            'update_core' => 'Update Core',
            'update_plugins' => 'Update Plugins',
            'update_themes' => 'Update Themes',
            'upload_files' => 'Upload Files'
        );

        // SET CAPABILITY INPUTS
        $options = array();
        foreach($caps as $key => $value) {
            if(in_array($key, $selectedCaps)) {
                $options[] = '
                    <div class="cm-hipaa-forms-role-cap-input">
                        <input type="checkbox" class="hipaa-role-capability-option" data-cap="' . $key . '" checked="checked" /> ' . $value . '
                    </div>
                ';
            } else {
                $options[] = '
                    <div class="cm-hipaa-forms-role-cap-input">
                        <input type="checkbox" class="hipaa-role-capability-option" data-cap="' . $key . '" /> ' . $value . '
                    </div>
                ';
            }
        }

        $options1 = array_slice($options, 0, 17);
        $options2 = array_slice($options, 17, 17);
        $options3 = array_slice($options, 34, 17);
        $options4 = array_slice($options, 51, 17);

        $content = '
            <div class="cm_hipaa_col_25">
                ' . implode('', $options1) . '
            </div>
            <div class="cm_hipaa_col_25">
                ' . implode('', $options2) . '
            </div>
            <div class="cm_hipaa_col_25">
                ' . implode('', $options3) . '
            </div>
            <div class="cm_hipaa_col_25">
                ' . implode('', $options4) . '
            </div>
        ';

        return $content;
    }

    /*** UPDATE HIPAA USER ROLE CAPABILITIES ***/
    public function updateHipaaCapabilities($selectedCaps) {
        // CONVERT SELECTED CAPABILITIES STRING TO ARRAY
        $selectedCaps = explode(',', $selectedCaps);
        $capabilities = array();

        // SET CAPABILITIES ARRAY
        $caps = array(
            'activate_plugins',
            'create_roles',
            'create_users',
            'delete_documents',
            'delete_others_documents',
            'delete_others_pages',
            'delete_others_posts',
            'delete_pages',
            'delete_plugins',
            'delete_posts',
            'delete_private_documents',
            'delete_private_pages',
            'delete_private_posts',
            'delete_published_documents',
            'delete_published_pages',
            'delete_published_posts',
            'delete_roles',
            'delete_themes',
            'delete_users',
            'edit_dashboard',
            'edit_documents',
            'edit_files',
            'edit_others_documents',
            'edit_others_pages',
            'edit_others_posts',
            'edit_pages',
            'edit_plugins',
            'edit_posts',
            'edit_private_documents',
            'edit_private_pages',
            'edit_private_posts',
            'edit_published_documents',
            'edit_published_pages',
            'edit_published_posts',
            'edit_roles',
            'edit_themes',
            'edit_theme_options',
            'edit_users',
            'export',
            'import',
            'install_plugins',
            'install_themes',
            'list_roles',
            'list_users',
            'manage_categories',
            'manage_links',
            'manage_options',
            'moderate_comments',
            'override_document_lock',
            'promote_users',
            'publish_documents',
            'publish_pages',
            'publish_posts',
            'read_documents',
            'read_document_revisions',
            'read_private_documents',
            'read_private_pages',
            'read_private_posts',
            'remove_users',
            'restrict_content',
            'switch_themes',
            'unfiltered_html',
            'unfiltered_upload',
            'update_core',
            'update_plugins',
            'update_themes',
            'upload_files'
        );

        // SET CAPABILITY TRUE/FALSE
        foreach($caps as $cap) {
            if(in_array($cap, $selectedCaps)) {
                $capabilities[$cap] = true;
            } else {
                $capabilities[$cap] = false;
            }
        }

        // REMOVE hipaa_forms USER ROLE
        remove_role('hipaa_forms');

        // CREATE USER ROLE AND SET CAPABILITIES
        add_role('hipaa_forms', __( 'HIPAA Forms' ), $capabilities);

        $hipaaRole = get_role('hipaa_forms');
        $hipaaRole->add_cap('access_hipaa_forms', true);

        return 'Capabilities updated';
    }

    /*** CREATE SELECTED USERS MODAL ***/
    public function createSelectedUsersModal($selectedUsers, $formId) {
        // GET APPROVED USERS (ADMIN & HIPAA USER ROLES)
        $approvedUsers = self::getApprovedUsers();

        if($selectedUsers) {
            $selectedUsers = explode(',', $selectedUsers);
        }

        // SET USER SELECT OPTIONS
        $selectedUserNames = array();
        $approvedUsersOptions = array();
        foreach($approvedUsers as $approvedUser) {
            $userSelected = '';
            if(is_array($selectedUsers) && in_array($approvedUser->ID, $selectedUsers)) {
                // IF APPROVED USER IS SELECTED USER SET OPTION AS SELECTED
                $userSelected = 'selected="selected"';

                // SET SELECTED USER DISPLAY NAME
                $selectedUserNames[] = $approvedUser->display_name;
            }

            $approvedUsersOptions[] = '
                <option value="' . $approvedUser->ID . '" ' . $userSelected . '>' . $approvedUser->display_name . '</option>
            ';
        }

        // SET CURRENT SELECTED USER TEXT
        if($selectedUsers) {
            $selectedUserText = 'CURRENT SELECTED USERS: ' . implode(', ', $selectedUserNames);
        } else {
            $selectedUserText = 'NO USER CURRENTLY SET';
        }

        $modal = '
            <div class="cm-hipaa-forms-modal cm-hipaa-forms-selected-users-modal">
                <div class="cm-hipaa-forms-modal-inner">
                    <div class="cm-hipaa-forms-modal-inner-top">
                        <div class="cm-hipaa-forms-su-modal-close">
                            <i class="material-icons">cancel</i>
                        </div>
                    </div>
                    <div class="cm-hipaa-forms-modal-inner-body">
                        <div class="cm-hipaa-forms-selected-users-modal-current-user">
                            ' . $selectedUserText . '
                        </div>
                        <div class="cm-hipaa-forms-modal-text">
                            Assign to a specific user:<br />
                            (CTRL + Click to select multiple)
                        </div>
                        <div class="cm-hipaa-forms-modal-inputs">
                            <select multiple class="cm-hipaa-forms-selected-user-select" data-current="' . implode(',', $selectedUsers) . '">
                                ' . implode('', $approvedUsersOptions) . '
                            </select>
                        </div>
                        <div class="cm-hipaa-forms-selected-users-submit cm-button active" data-id="' . $formId . '">
                            SAVE
                        </div>
                        <div class="cm-hipaa-forms-reassign-notice"></div>
                    </div>
                </div>
            </div>
        ';

        return $modal;
    }

    /*** REASSIGN SELECTED USER ***/
    public function reassignSelectedUser($selectedUsers, $formId) {
        $licenseKey = esc_attr(get_option('license_key'));

        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'reassign',
            'license_key' => $licenseKey,
            'domain' => self::getRootDomain(),
            'form_id' => $formId,
            'selected_user' => $selectedUsers
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        foreach($output as $data) {
            $error = '';
            if(isset($data->error)) {
                $error = $data->error;
            }

            if($error) {
                $results = array(
                    'error' => $error
                );
            } else {
                $results = array(
                    'success' => $data->success
                );
            }
        }

        return json_encode($results);
    }

    /**** GET SUBMITTED FORMS (DEPRECATED) ***/
    public function getForms($location, $formName, $firstName, $lastName, $phone, $email, $status, $limit, $page) {
        /*
        * Eastern ........... America/New_York
        * Central ........... America/Chicago
        * Mountain .......... America/Denver
        * Mountain no DST ... America/Phoenix
        * Pacific ........... America/Los_Angeles
        * Alaska ............ America/Anchorage
        * Hawaii ............ America/Adak
        * Hawaii no DST ..... Pacific/Honolulu
         */
        $licenseKey = esc_attr(get_option('license_key'));
        $customCss = esc_attr(get_option('hipaa_form_css'));
        $timeZone = esc_attr(get_option('time_zone'));

        if($timeZone == 'alaska') {
            $tz = 'America/Anchorage';
        } else if($timeZone == 'central') {
            $tz = 'America/Chicago';
        } else if($timeZone == 'eastern') {
            $tz = 'America/New_York';
        } else if($timeZone == 'hawaii') {
            $tz = 'America/Adak';
        } else if($timeZone == 'hawaii_no_dst') {
            $tz = 'Pacific/Honolulu';
        } else if($timeZone == 'mountain') {
            $tz = 'America/Denver';
        } else if($timeZone == 'mountain_no_dst') {
            $tz = 'America/Phoenix';
        } else if($timeZone == 'pacific') {
            $tz = 'America/Los_Angeles';
        } else {
            $tz = 'America/Chicago';
        }

        // GET USER ID
        $user = wp_get_current_user();
        $myId = $user->ID;
        $user_roles = $user->roles;
        $role = '';
        $myDisplayName = $user->display_name;
        $myFirstName = $user->first_name;
        $myLastName = $user->last_name;
        $myEmail = $user->user_email;

        // SET MY NAME
        $myName = '';
        if($myFirstName || $myLastName) {
            $myName = $myFirstName . ' ' . $myLastName;
        } else if($myDisplayName) {
            $myName = $myDisplayName;
        } else {
            $myName = $myEmail;
        }

        /// SET ADMINISTRATOR
        if(in_array('administrator', $user_roles)) {
            $role = 'administrator';
        } else if(in_array('hipaa_forms', $user_roles)) {
            $role = 'hipaa';
        }

        // VALIDATE ACCOUNT
        $validateAccount = json_decode(self::validateAccount());
        $product = '';
        if(isset($validateAccount->product)) {
            $product = $validateAccount->product;
        }

        $licenseStatus = '';
        if(isset($validateAccount->license_status)) {
            $licenseStatus = $validateAccount->license_status;
        }

        // GET FORMS SETTINGS
        $enabledForms = json_decode(trim(strip_tags(get_option('enabled_forms_settings'))));

        // LOOP FORMS
        $excludedForms = array();
        if(count($enabledForms)) {
            foreach ($enabledForms as $enabledForm) {
                // CHECK IF FORM IS SET TO SPECIFIC USERS ONLY
                if ($enabledForm->users_handler == 'specific') {
                    // CONVERT COMMA DELIMITED STRING OF USER ID'S TO ARRAY
                    $approvedUsers = explode(',', $enabledForm->approved_users);

                    if (!in_array($myId, $approvedUsers)) {
                        // IF MY ID NOT SET ADD FORM ID TO EXCLUDED FORMS ARRAY
                        $excludedForms[] = $enabledForm->id;
                    }
                }
            }
        }

        // CONVERT EXCLUDED FORMS ARRAY TO COMMA DELIMITED STRING
        $excludedForms = implode(',', $excludedForms);

        // SET OFFSET
        $offset = $page * $limit;

        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'get',
            'license_key' => $licenseKey,
            'domain' => self::getRootDomain(),
            'location' => $location,
            'form_name' => $formName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone,
            'email' => $email,
            'my_id' => $myId,
            'my_role' => $role,
            'my_email' => $myEmail,
            'excluded_forms' => $excludedForms,
            'status' => $status,
            'limit' => $limit,
            'offset' => $offset
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        if ($output) {
            foreach ($output as $data) {
                $error = '';
                if(isset($data->error)) {
                    $error = $data->error;
                }

                if ($error) {
                    if ($error == 'No BAA') {
                        $results = array(
                            'error' => $error,
                            'content' => '<div class="cm-sign-baa-button">You must sign the BAA Agreement!</div><div class="cm-sign-baa-notice">If you would rather use your own BAA please email it to contact@codemonkeysllc.com or call us at 715.941.1040</div>'
                        );
                    } else {
                        $results = array(
                            'error' => $error,
                            'content' => $error
                        );
                    }
                } else {
                    $success = $data->success;

                    if ($success == 'success') {
                        $formsData = $data->forms;
                        $totalResults = $data->total_results;
                        $stagingMessage = '';

                        if($data->is_staging == 'yes') {
                            $stagingMessage = '
                                <div class="cm-hipaa-forms-staging-message">
                                    You are currently on a staging server for testing purposes only.  Actual protected health information should not be passed from this domain.
                                </div>
                            ';
                        }

                        // GET CURRENT DEFAULT TIMEZONE
                        $defaultTz = date_default_timezone_get();
                        // SET DEFAULT TIMEZONE TO UTC
                        date_default_timezone_set('UTC');

                        $forms = array();
                        foreach ($formsData as $formData) {
                            $formId = $formData->form_id;
                            $formBuilderId = $formData->form_builder_id;
                            $formName = $formData->form_name;
                            $formLocation = $formData->location;
                            $formFirstName = $formData->first_name;
                            $formLastName = $formData->last_name;
                            $formEmail = $formData->email;
                            $formPhone = $formData->phone;
                            $usersTimezone = new DateTimeZone($tz);
                            $dtObj = new DateTime($formData->timestamp);
                            $dtObj->setTimeZone($usersTimezone);
                            $formDate = $dtObj->format('m-d-Y g:i a');
                            $tzAbbr = $dtObj->format('T');
                            $formFields = $formData->fields;
                            $formHtml = $formData->form_html;
                            $encryptKey = $formData->encrypt_key;
                            $encryptIv = $formData->encrypt_iv;
                            $status = $formData->status;
                            $selectedUser = $formData->selected_user;
                            $signature = $formData->signature;
                            $formNotes = '';
                            if(isset(json_decode($formData->form_notes)[0]->notes)) {
                                $formNotes = json_decode($formData->form_notes)[0]->notes;
                            }
                            $decryptedFields = '';
                            $decryptedForm = '';
                            $decryptedNotes = array();

                            // SET SIGNATURE
                            if ($signature) {
                                $signature = '
                                    <div class="cm-hipaa-submitted-form-signature">
                                        <img src="' . $signature . '" alt="Signature" />
                                    </div>
                                ';
                            }

                            // DECRYPT FIELDS
                            if($formFields) {
                                $decryptedFields = json_decode(self::decrypt($formFields, $encryptKey, $encryptIv));
                            }

                            // DECRYPT FORM HTML
                            if($formHtml) {
                                $decryptedForm = stripslashes(self::decrypt($formHtml, $encryptKey, $encryptIv));
                            }

                            // DECRYPT FORM NOTES
                            $notesIcon = '';
                            if(!empty($formNotes)) {
                                foreach($formNotes as $formNote) {
                                    $note = $formNote->note;
                                    $noteKey = $formNote->key;
                                    $noteIv = $formNote->iv;
                                    $decryptedNote = self::decrypt($note, $noteKey, $noteIv);

                                    $dtObj = new DateTime($formNote->timestamp);
                                    if($tz) {
                                        $dtObj->setTimeZone($usersTimezone);
                                    }
                                    $noteDate = $dtObj->format('M jS Y g:i a');
                                    $tzAbbr = $dtObj->format('T');

                                    $decryptedNotes[] = '
                                        <div class="cm-hipaa-submitted-form-note-wrapper">
                                            <div class="cm-hipaa-submitted-form-note-date">
                                                ' . $noteDate . ' ' . $tzAbbr . '
                                            </div>
                                            <div class="cm-hipaa-submitted-form-note-name">
                                                ' . $formNote->name . '
                                            </div>
                                            <div class="cm-hipaa-submitted-form-note">
                                                ' . $decryptedNote . '
                                            </div>
                                        </div>
                                    ';
                                }

                                $notesIcon = '
                                    <div class="cm-hipaa-submitted-form-notes-icon">
                                        <i class="material-icons" title="Notes added to form">speaker_notes</i>
                                    </div>';
                            }

                            // LOOP FIELDS AND PUSH LABEL - VALUE TO ARRAY
                            $fields = array();
                            $i = 1;
                            foreach ($decryptedFields as $decryptedField) {
                                $fieldType = $decryptedField->type;

                                if ($fieldType == 'textarea' || $fieldType == 'paragraph') {
                                    $fields[] = '
                                        <div class="clearfix"></div>
                                        <div class="cm-hipaa-submitted-form-field ' . $fieldType . ' full-width">
                                            <div class="cm-hipaa-submitted-form-field-inner">
                                                <div class="cm-hipaa-submitted-form-field-label">
                                                    ' . str_replace('*', '', stripslashes($decryptedField->label)) . '
                                                </div>
                                                <div class="cm-hipaa-submitted-form-field-value">
                                                    ' . stripslashes($decryptedField->value) . '
                                                </div>
                                            </div>
                                        </div>
                                    ';
                                } else {
                                    $fields[] = '
                                        <div class="cm-hipaa-submitted-form-field ' . $fieldType . ' float-left quarter-width">
                                            <div class="cm-hipaa-submitted-form-field-inner">
                                                <span class="cm-hipaa-submitted-form-field-label">
                                                    ' . str_replace('*', '', stripslashes($decryptedField->label)) . ':
                                                </span>
                                                <span class="cm-hipaa-submitted-form-field-value">
                                                    ' . stripslashes($decryptedField->value) . '
                                                </span>
                                            </div>
                                        </div>
                                    ';
                                }

                                if ($i % 4 == 0) {
                                    $fields[] = '
                                        <div class="clearfix"></div>
                                    ';
                                }

                                if ($fieldType == 'textarea') {
                                    $i = 1;
                                } else {
                                    $i++;
                                }
                            }

                            if(!$decryptedForm) {
                                $webForm = implode('', $fields);
                            } else {
                                $webForm = $decryptedForm;
                            }

                            // LOOP FORM SETTINGS & SET USERS HANDLER
                            $usersHandler = '';
                            foreach($enabledForms as $enabledForm) {
                                $enabledFormIds[] = $enabledForm->id;
                                // CHECK IF FORM SETTINGS MATCH THIS FORM
                                if($enabledForm->id == $formBuilderId || 'gform_' . $enabledForm->id == $formBuilderId) {
                                    $usersHandler = $enabledForm->users_handler;
                                }
                            }

                            // SET SELECTED USER ICON IF SET TO SELECTED ONLY
                            $selectedUsersIcon = '';
                            if($usersHandler == 'selected' && $role == 'administrator') {
                                $selectedUsersIcon = '
                                    <div class="cm-hipaa-submitted-form-transfer-user">
                                        <i class="material-icons" title="Assign Form to User" data="' . $selectedUser . '">group</i>
                                    </div>
                                ';
                            }

                            // SET DELETE ICON
                            $deleteIcon = '';
                            if($role == 'administrator') {
                                if($status == 0) {
                                    // IF STATUS 0 FOR ARCHIVED SHOW RESTORE ICON
                                    $deleteIcon = '
                                        <div class="cm-hipaa-submitted-form-restore">
                                            <i class="material-icons" title="Restore">restore</i>
                                        </div>
                                    ';
                                } else if($status == 1) {
                                    // IF STATUS 1 FOR NOT ARCHIVED SHOW DELETE ICON
                                    $deleteIcon = '
                                        <div class="cm-hipaa-submitted-form-delete">
                                            <i class="material-icons" title="Archive">delete</i>
                                        </div>
                                    ';
                                } else {
                                    // IF NO STATUS SHOW DELETE ICON
                                    $deleteIcon = '
                                        <div class="cm-hipaa-submitted-form-delete">
                                            <i class="material-icons" title="Archive">delete</i>
                                        </div>
                                    ';
                                }
                            }

                            // SET ADD NOTE
                            $addNote = '';
                            if($product == 'basic') {
                                $addNote = '<a href="https://www.hipaaforms.online/my-account/" target="_blank">UPGRADE your subscription to enable the notes feature!</a>';
                            } else if($product == 'standard' && $licenseStatus == 'expired') {
                                $addNote = 'Your subscription has expired.  <a href="https://www.hipaaforms.online/my-account/" target="_blank">UPGRADE your subscription to re-enable the notes feature!</a>';
                            } else {
                                $addNote = '
                                    <div class="cm-hipaa-form-notes-add-note-input-wrapper">
                                        <textarea class="cm-hipaa-form-notes-add-note-input" placeholder="ADD A NOTE..."></textarea>
                                    </div>
                                    <div class="cm-hipaa-form-notes-add-note-notice"></div>
                                    <div class="cm-button cm-hipaa-form-notes-add-note-submit" data-form-id="' . $formId . '" data-my-id="' . $myId . '" data-name="' . $myName . '" data-email="' . $myEmail . '">SUBMIT</div>
                                    <div class="clearfix"></div>
                                ';
                            }

                            $forms[] = '
                                <tr id="cm-hipaa-form-id-' . $formId . '" class="cm-hipaa-submitted-form" data-id="' . $formId . '">
                                    <td>
                                        <div class="cm-hipaa-submitted-form-options">
                                            <div class="cm-hipaa-submitted-form-toggle-fields">
                                                <i class="material-icons" title="Toggle Form">arrow_drop_down_circle</i>
                                            </div>
                                            ' . $deleteIcon . '
                                            ' . $selectedUsersIcon . '
                                            ' . $notesIcon . '
                                        </div>
                                    </td>
                                    <td>
                                        ' . $formLocation . '
                                    </td>
                                    <td>
                                        ' . $formFirstName . '
                                    </td>
                                    <td>
                                        ' . $formLastName . '
                                    </td>
                                    <td>
                                        ' . $formName . '
                                    </td>
                                    <td>
                                        ' . $formDate . ' ' . $tzAbbr . '
                                    </td>
                                    <td>
                                        <div class="cm-hipaa-submitted-form-pdf-section">
                                            <div class="cm-hipaa-generate-pdf-modal-button cm-ghost-button" data="' . $formId . '">
                                                Generate PDF
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="cm-hipaa-submitted-form-fields-row">
                                    <td colspan="7">
                                        <div class="cm-hipaa-submitted-form-fields">
                                            <div class="cm_hipaa_grid_row_nogap cm_hipaa_tour_tab_wrapper">
                                                <div class="cm-hipaa-tour-tab-menu cm_hipaa_col_20">
                                                    <ul>
                                                        <li data="tour-tab-submitted-form-' . $formId . '" class="cm-hipaa-tour-tab-link cm-hipaa-active-tour-tab-link">
                                                            <div class="cm-hipaa-tour-tab-link-inner">
                                                                <i class="material-icons">assignment</i> <span class="cm-hipaa-tab-name">FORM</span>
                                                            </div>
                                                        </li>
                                                        <li data="tour-tab-notes-' . $formId . '" class="cm-hipaa-tour-tab-link">
                                                            <div class="cm-hipaa-tour-tab-link-inner">
                                                                <i class="material-icons">comment</i> <span class="cm-hipaa-tab-name">NOTES</span>
                                                            </div>
                                                        </li>
                                                        <li data="tour-tab-history-' . $formId . '" class="cm-hipaa-tour-tab-link">
                                                            <div class="cm-hipaa-tour-tab-link-inner">
                                                                <i class="material-icons">history</i> <span class="cm-hipaa-tab-name">HISTORY</span>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="cm-hipaa-tour-tab-content-wrapper cm_hipaa_col_80">
                                                    <!-- SUBMITTED FORM -->
                                                    <section data="tour-tab-submitted-form-' . $formId . '" class="cm-hipaa-accordion-tab-link cm-hipaa-active-accordion-tab-link">
                                                        <div class="cm-hipaa-tour-tab-link-inner">
                                                            <i class="material-icons">assignment</i> <span class="cm-hipaa-tab-name">FORM</span>
                                                        </div>
                                                    </section>
                                                    <div data="tour-tab-submitted-form-' . $formId . '" class="cm-hipaa-tour-tab-content">
                                                        <div class="cm-hipaa-submitted-form-fields-inner">
                                                            <h2>' . $formName . '</h2>
                                                            ' . $webForm . '
                                                            <div class="clearfix"></div>
                                                            ' . $signature . '
                                                            <div class="cm-hipaa-submitted-form-webview-notes">
                                                                <h4>NOTES:</h4>
                                                                ' . implode('', $decryptedNotes) . '
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- NOTES -->
                                                    <section data="tour-tab-notes-' . $formId . '" class="cm-hipaa-accordion-tab-link">
                                                        <div class="cm-hipaa-tour-tab-link-inner">
                                                            <i class="material-icons">comment</i> <span class="cm-hipaa-tab-name">FORM SETTINGS</span>
                                                        </div>
                                                    </section>
                                                    <div data="tour-tab-notes-' . $formId . '" class="cm-hipaa-tour-tab-content">
                                                        <h3>NOTES</h3>
                                                        <div class="cm-hipaa-form-notes-wrapper">
                                                            <div class="cm-hipaa-form-notes-top">
                                                                <div class="cm-hipaa-form-notes-add-note">
                                                                    ' . $addNote . '
                                                                </div>
                                                            </div>
                                                            <div class="cm-hipaa-form-notes">
                                                                ' . implode('', $decryptedNotes) . '
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- HISTORY -->
                                                    <section data="tour-tab-history-' . $formId . '" class="cm-hipaa-accordion-tab-link">
                                                        <div class="cm-hipaa-tour-tab-link-inner">
                                                            <i class="material-icons">history</i> <span class="cm-hipaa-tab-name">FORM SETTINGS</span>
                                                        </div>
                                                    </section>
                                                    <div data="tour-tab-history-' . $formId . '" class="cm-hipaa-tour-tab-content">
                                                        <h3>FORM HISTORY COMING SOON!</h3>
                                                        <p>
                                                            While HIPAA regulations only requires that we log access to the forms we always want to go above and beyond what HIPAA requires.
                                                        </p>
                                                        <p>
                                                            In the next major release this tab will display a history of each time someone views or generates a PDF version of this specific form.
                                                        </p>
                                                        <p>
                                                            Not only will this add an additional layer of transparency but it will help many of our user\'s workflows by allowing multiple staff to see if someone else has already viewed the form and along with the new notes feature allow them to determine if the form still requires action or not.
                                                        </p>
                                                        <p>
                                                            In addition to the history here we will also indicate if the form has been viewed or not without needing to expand the form to save time.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            ';
                        }

                        // RESET DEFAULT TIMEZONE BACK TO SERVER DEFAULT
                        date_default_timezone_set($defaultTz);

                        $content = '
                            <style>' . $customCss . '</style>
                            ' . $stagingMessage . '
                            <table class="cm-hipaa-submitted-forms">
                                <thead class="cm-table-header">
                                    <td>
                                    </td>
                                    <td>
                                        OFFICE LOCATION
                                    </td>
                                    <td>
                                        FIRST NAME
                                    </td>
                                    <td>
                                        LAST NAME
                                    </td>
                                    <td>
                                        FORM NAME
                                    </td>
                                    <td>
                                        SUBMISSION DATE
                                    </td>
                                    <td>
                                        PDF
                                    </td>
                                </thead>
                                ' . implode('', $forms) . '
                            </table>
                        ';

                        $results = array(
                            'success' => 'success',
                            'content' => $content,
                            'total_results' => $totalResults
                        );
                    } else {
                        $results = array(
                            'error' => 'API did not return a success message',
                            'content' => 'API did not return a success message'
                        );
                    }
                }
            }
        } else {
            $results = array(
                'error' => 'No response from API',
                'content' => 'No response from API'
            );
        }

        return json_encode($results);
    }

    /**** GET SUBMITTED FORMS LIST ***/
    public function getSubmittedFormsList($location, $formName, $firstName, $lastName, $phone, $email, $status, $limit, $page) {
        $licenseKey = esc_attr(get_option('license_key'));
        $customCss = esc_attr(get_option('hipaa_form_css'));
        $timeZone = esc_attr(get_option('time_zone'));

        if($timeZone == 'alaska') {
            $tz = 'America/Anchorage';
        } else if($timeZone == 'central') {
            $tz = 'America/Chicago';
        } else if($timeZone == 'eastern') {
            $tz = 'America/New_York';
        } else if($timeZone == 'hawaii') {
            $tz = 'America/Adak';
        } else if($timeZone == 'hawaii_no_dst') {
            $tz = 'Pacific/Honolulu';
        } else if($timeZone == 'mountain') {
            $tz = 'America/Denver';
        } else if($timeZone == 'mountain_no_dst') {
            $tz = 'America/Phoenix';
        } else if($timeZone == 'pacific') {
            $tz = 'America/Los_Angeles';
        } else {
            $tz = 'America/Chicago';
        }

        // GET USER ID
        $user = wp_get_current_user();
        $myId = $user->ID;
        $user_roles = $user->roles;
        $role = '';
        $myDisplayName = $user->display_name;
        $myFirstName = $user->first_name;
        $myLastName = $user->last_name;
        $myEmail = $user->user_email;

        // SET MY NAME
        $myName = '';
        if($myFirstName || $myLastName) {
            $myName = $myFirstName . ' ' . $myLastName;
        } else if($myDisplayName) {
            $myName = $myDisplayName;
        } else {
            $myName = $myEmail;
        }

        /// SET ADMINISTRATOR
        if(in_array('administrator', $user_roles)) {
            $role = 'administrator';
        } else if(in_array('hipaa_forms', $user_roles)) {
            $role = 'hipaa';
        }

        // VALIDATE ACCOUNT
        $validateAccount = json_decode(self::validateAccount());
        $product = '';
        if(isset($validateAccount->product)) {
            $product = $validateAccount->product;
        }

        $fileUploadEnabled = false;
        if(isset($validateAccount->add_ons)) {
            $addOns = explode(',', $validateAccount->add_ons);

            if(in_array('fileupload', $addOns)) {
                $fileUploadEnabled = true;
            }
        }

        $licenseStatus = '';
        if(isset($validateAccount->license_status)) {
            $licenseStatus = $validateAccount->license_status;
        }

        // GET FORMS SETTINGS
        $enabledForms = json_decode(trim(strip_tags(get_option('enabled_forms_settings'))));

        // LOOP FORMS
        $excludedForms = array();
        if(count($enabledForms)) {
            foreach ($enabledForms as $enabledForm) {
                // CHECK IF FORM IS SET TO SPECIFIC USERS ONLY
                if ($enabledForm->users_handler == 'specific') {
                    // CONVERT COMMA DELIMITED STRING OF USER ID'S TO ARRAY
                    $approvedUsers = explode(',', $enabledForm->approved_users);

                    if (!in_array($myId, $approvedUsers)) {
                        // IF MY ID NOT SET ADD FORM ID TO EXCLUDED FORMS ARRAY
                        $excludedForms[] = $enabledForm->id;
                    }
                }
            }
        }

        // CONVERT EXCLUDED FORMS ARRAY TO COMMA DELIMITED STRING
        $excludedForms = implode(',', $excludedForms);

        // SET OFFSET
        $offset = $page * $limit;

        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'getformslist',
            'license_key' => $licenseKey,
            'domain' => self::getRootDomain(),
            'location' => $location,
            'form_name' => $formName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone,
            'email' => $email,
            'my_id' => $myId,
            'my_role' => $role,
            'my_email' => $myEmail,
            'excluded_forms' => $excludedForms,
            'status' => $status,
            'limit' => $limit,
            'offset' => $offset
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        if ($output) {
            foreach ($output as $data) {
                $error = '';
                if(isset($data->error)) {
                    $error = $data->error;
                }

                if ($error) {
                    if ($error == 'No BAA') {
                        $results = array(
                            'error' => $error,
                            'content' => '<div class="cm-sign-baa-button">You must sign the BAA Agreement!</div><div class="cm-sign-baa-notice">If you would rather use your own BAA please email it to contact@codemonkeysllc.com or call us at 715.941.1040</div>'
                        );
                    } else {
                        $results = array(
                            'error' => $error,
                            'content' => $error
                        );
                    }
                } else {
                    $success = $data->success;

                    if ($success == 'success') {
                        $formsData = $data->forms;
                        $totalResults = $data->total_results;
                        $stagingMessage = '';

                        if($data->is_staging == 'yes') {
                            $stagingMessage = '
                                <div class="cm-hipaa-forms-staging-message">
                                    You are currently on a staging server for testing purposes only.  Actual protected health information should not be passed from this domain.
                                </div>
                            ';
                        }

                        // GET CURRENT DEFAULT TIMEZONE
                        $defaultTz = date_default_timezone_get();
                        // SET DEFAULT TIMEZONE TO UTC
                        date_default_timezone_set('UTC');

                        $forms = array();
                        foreach ($formsData as $formData) {
                            $formId = $formData->form_id;
                            $formBuilderId = $formData->form_builder_id;
                            $formName = stripslashes($formData->form_name);
                            $formLocation = $formData->location;
                            $formFirstName = $formData->first_name;
                            $formLastName = $formData->last_name;
                            $formEmail = $formData->email;
                            $formPhone = $formData->phone;
                            $usersTimezone = new DateTimeZone($tz);
                            $dtObj = new DateTime($formData->timestamp);
                            $dtObj->setTimeZone($usersTimezone);
                            $formDate = $dtObj->format('m-d-Y g:i a');
                            $tzAbbr = $dtObj->format('T');
                            $status = $formData->status;
                            $customStatus = $formData->custom_status;
                            $selectedUsers = $formData->selected_user;
                            $formFiles = $formData->files;
                            $formViewed = $formData->viewed;
                            $formNotes = '';
                            if(isset(json_decode($formData->form_notes)[0]->notes)) {
                                $formNotes = json_decode($formData->form_notes)[0]->notes;
                            }
                            $decryptedNotes = array();

                            // GET CUSTOM STATUS OPTIONS
                            $customStatusSelect = '';
                            $customStatusEnabled = '';
                            $customStatusOptions = '';
                            if(esc_attr(get_option('hipaa_custom_status_enabled'))) {
                                $customStatusEnabled = esc_attr(get_option('hipaa_custom_status_enabled'));
                            }
                            if(esc_attr(get_option('hipaa_custom_status_options'))) {
                                $customStatusOptions = esc_attr(get_option('hipaa_custom_status_options'));
                            }

                            if($customStatusEnabled == 'yes' && $customStatusOptions) {
                                $customStatusOptions = explode(',', $customStatusOptions);
                                $customStatusOptionElements = array('<option value="">NO STATUS</option>');

                                foreach($customStatusOptions as $customStatusOption) {
                                    if($customStatusOption == $customStatus) {
                                        $customStatusOptionElements[] = '
                                            <option value="' . $customStatusOption . '" selected="selected">' . $customStatusOption . '</option>
                                        ';
                                    } else {
                                        $customStatusOptionElements[] = '
                                            <option value="' . $customStatusOption . '">' . $customStatusOption . '</option>
                                        ';
                                    }
                                }

                                $customStatusSelect = '
                                    <select class="cm-hipaa-forms-custom-status-select" data-form-id="' . $formId . '">
                                        ' . implode('', $customStatusOptionElements) . '
                                    </select>
                                    <div class="cm-hipaa-forms-custom-status-notice" data-form-id="' . $formId . '"></div>
                                ';
                            }

                            // DECRYPT FORM NOTES
                            $notesIcon = '';
                            if(!empty($formNotes)) {
                                foreach($formNotes as $formNote) {
                                    $note = $formNote->note;
                                    $noteKey = $formNote->key;
                                    $noteIv = $formNote->iv;
                                    $decryptedNote = self::decrypt($note, $noteKey, $noteIv);

                                    $dtObj = new DateTime($formNote->timestamp);
                                    if($tz) {
                                        $dtObj->setTimeZone($usersTimezone);
                                    }
                                    $noteDate = $dtObj->format('M jS Y g:i a');
                                    $tzAbbr = $dtObj->format('T');

                                    $decryptedNotes[] = '
                                        <div class="cm-hipaa-submitted-form-note-wrapper">
                                            <div class="cm-hipaa-submitted-form-note-date">
                                                ' . $noteDate . ' ' . $tzAbbr . '
                                            </div>
                                            <div class="cm-hipaa-submitted-form-note-name">
                                                ' . $formNote->name . '
                                            </div>
                                            <div class="cm-hipaa-submitted-form-note">
                                                ' . $decryptedNote . '
                                            </div>
                                        </div>
                                    ';
                                }

                                $notesIcon = '
                                    <div class="cm-hipaa-submitted-form-notes-icon">
                                        <i class="material-icons" title="Notes added to form">speaker_notes</i>
                                    </div>';
                            }

                            // SET FILES ARRAY
                            $files = array();

                            // LOOP FILE URLS
                            foreach($formFiles as $formFile) {
                                foreach($formFile as $file) {
                                    $fileUrl = $file->url;
                                    $fileTags = $file->tags;
                                    $tags = array();

                                    foreach($fileTags as $fileTag) {
                                        $tags[] = '
                                            <div class="cm-hipaa-forms-file-tag">
                                                ' . $fileTag->Value . '
                                            </div>
                                        ';
                                    }

                                    // CREATE FILE LINK AND PUSH TO FILES ARRAY
                                    $files[] = '
                                        <div class="cm-hipaa-forms-file-wrapper">
                                            <div class="cm-hipaa-forms-file-tags">
                                                ' . implode('', $tags) . '
                                            </div>
                                            <div class="cm-hipaa-forms-file-url">
                                                <a href="' . $fileUrl . '" target="_blank">' . ltrim(parse_url($fileUrl, PHP_URL_PATH), '/') . '</a>
                                            </div>
                                        </div>
                                    ';
                                }
                            }

                            // LOOP FORM SETTINGS & SET USERS HANDLER
                            $usersHandler = '';
                            foreach($enabledForms as $enabledForm) {
                                $enabledFormIds[] = $enabledForm->id;
                                // CHECK IF FORM SETTINGS MATCH THIS FORM
                                if($enabledForm->id == $formBuilderId || 'gform_' . $enabledForm->id == $formBuilderId) {
                                    $usersHandler = $enabledForm->users_handler;
                                }
                            }

                            // SET SELECTED USER ICON IF SET TO SELECTED ONLY
                            $selectedUsersIcon = '';
                            if($usersHandler == 'selected' && $role == 'administrator') {
                                $selectedUsersIcon = '
                                    <div class="cm-hipaa-submitted-form-transfer-user">
                                        <i class="material-icons" title="Assign Form to User" data="' . $selectedUsers . '">group</i>
                                    </div>
                                ';
                            }

                            // SET ARCHIVE ICON
                            $archiveIcon = '';
                            if($status == 0) {
                                // IF STATUS 0 FOR ARCHIVED SHOW RESTORE ICON
                                $archiveIcon = '
                                    <div class="cm-hipaa-submitted-form-restore">
                                        <i class="material-icons" title="Restore">restore</i>
                                    </div>
                                ';
                            } else if($status == 1) {
                                // IF STATUS 1 FOR NOT ARCHIVED SHOW DELETE ICON
                                $archiveIcon = '
                                    <div class="cm-hipaa-submitted-form-archive">
                                        <i class="material-icons" title="Archive">archive</i>
                                    </div>
                                ';
                            } else {
                                // IF NO STATUS SHOW DELETE ICON
                                $archiveIcon = '
                                    <div class="cm-hipaa-submitted-form-archive">
                                        <i class="material-icons" title="Archive">archive</i>
                                    </div>
                                ';
                            }

                            // SET DELETE ICON
                            $deleteIcon = '';
                            if($role == 'administrator') {
                                $deleteIcon = '
                                    <div class="cm-hipaa-submitted-form-destroy-confirm">
                                        <i class="material-icons" title="Delete">delete</i>
                                    </div>
                                ';
                            }

                            // SET ADD NOTE
                            $addNote = '';
                            if($product == 'basic') {
                                $addNote = '<a href="https://www.hipaaforms.online/my-account/" target="_blank">UPGRADE your subscription to enable the notes feature!</a>';
                            } else if($product == 'standard' && $licenseStatus == 'expired') {
                                $addNote = 'Your subscription has expired.  <a href="https://www.hipaaforms.online/my-account/" target="_blank">UPGRADE your subscription to re-enable the notes feature!</a>';
                            } else {
                                $addNote = '
                                    <div class="cm-hipaa-form-notes-add-note-input-wrapper">
                                        <textarea class="cm-hipaa-form-notes-add-note-input" placeholder="ADD A NOTE..."></textarea>
                                    </div>
                                    <div class="cm-hipaa-form-notes-add-note-notice"></div>
                                    <div class="cm-button cm-hipaa-form-notes-add-note-submit" data-form-id="' . $formId . '" data-my-id="' . $myId . '" data-name="' . $myName . '" data-email="' . $myEmail . '">SUBMIT</div>
                                    <div class="clearfix"></div>
                                ';
                            }

                            // SET FILE UPLOAD STATUS
                            $fileUploadStatus = '';
                            if($fileUploadEnabled == true) {
                                $fileUploadStatus = '
                                    <div class="cm-hipaa-form-file-status">
                                        File links expire after 1 hour, refresh page to regenerate file links.
                                    </div>
                                ';
                            } else {
                                $fileUploadStatus = '
                                    <div class="cm-hipaa-form-file-status">
                                        Your subscription doesn\'t include file upload capability.  Please <a href="https://www.hipaaforms.online/my-account/" target="_blank">UPGRADE YOUR SUBSCRIPTION</a> to enable file uploads.
                                    </div>
                                ';
                            }

                            // SET VIEWED CLASS
                            $formViewedClass = '';
                            if($formViewed == 'true') {
                                $formViewedClass = 'cm-hipaa-submitted-form-viewed';
                            }

                            $forms[] = '
                                <tr id="cm-hipaa-form-id-' . $formId . '" class="cm-hipaa-submitted-form  ' . $formViewedClass . '" data-id="' . $formId . '">
                                    <td>
                                        <div class="cm-hipaa-submitted-form-custom-status">
                                            ' . $customStatus . '
                                        </div>
                                        <div class="cm-hipaa-submitted-form-options">
                                            <div class="cm-hipaa-submitted-form-toggle-fields">
                                                <i class="material-icons" title="Toggle Form">arrow_drop_down_circle</i>
                                            </div>
                                            ' . $archiveIcon . '
                                            ' . $deleteIcon . '
                                            ' . $selectedUsersIcon . '
                                            ' . $notesIcon . '
                                        </div>
                                    </td>
                                    <td>
                                        ' . $formLocation . '
                                    </td>
                                    <td>
                                        ' . $formFirstName . '
                                    </td>
                                    <td>
                                        ' . $formLastName . '
                                    </td>
                                    <td>
                                        ' . $formName . '
                                    </td>
                                    <td>
                                        ' . $formDate . ' ' . $tzAbbr . '
                                    </td>
                                    <td>
                                        <div class="cm-hipaa-submitted-form-pdf-section">
                                            <div class="cm-hipaa-generate-pdf-modal-button cm-ghost-button" data="' . $formId . '">
                                                Generate PDF
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="cm-hipaa-submitted-form-fields-row">
                                    <td colspan="7">
                                        <div class="cm-hipaa-submitted-form-fields">
                                            <div class="cm_hipaa_grid_row_nogap cm_hipaa_tour_tab_wrapper">
                                                <div class="cm-hipaa-tour-tab-menu cm_hipaa_col_20">
                                                    <ul>
                                                        <li data="tour-tab-submitted-form-' . $formId . '" class="cm-hipaa-tour-tab-link cm-hipaa-active-tour-tab-link">
                                                            <div class="cm-hipaa-tour-tab-link-inner">
                                                                <i class="material-icons">assignment</i> <span class="cm-hipaa-tab-name">FORM</span>
                                                            </div>
                                                        </li>
                                                        <li data="tour-tab-files-' . $formId . '" class="cm-hipaa-tour-tab-link">
                                                            <div class="cm-hipaa-tour-tab-link-inner">
                                                                <i class="material-icons">attach_file</i> <span class="cm-hipaa-tab-name">FILES</span>
                                                            </div>
                                                        </li>
                                                        <li data="tour-tab-notes-' . $formId . '" class="cm-hipaa-tour-tab-link">
                                                            <div class="cm-hipaa-tour-tab-link-inner">
                                                                <i class="material-icons">comment</i> <span class="cm-hipaa-tab-name">NOTES</span>
                                                            </div>
                                                        </li>
                                                        <li data="tour-tab-history-' . $formId . '" class="cm-hipaa-tour-tab-link">
                                                            <div class="cm-hipaa-tour-tab-link-inner">
                                                                <i class="material-icons">history</i> <span class="cm-hipaa-tab-name">HISTORY</span>
                                                            </div>
                                                        </li>
                                                        <li data="tour-tab-export-' . $formId . '" class="cm-hipaa-tour-tab-link">
                                                            <div class="cm-hipaa-tour-tab-link-inner">
                                                                <i class="material-icons">save_alt</i> <span class="cm-hipaa-tab-name">EXPORT</span>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                    <!-- ADD OPTIONAL STATUS SELECT -->
                                                    ' . $customStatusSelect . '
                                                </div>
                                                <div class="cm-hipaa-tour-tab-content-wrapper cm_hipaa_col_80">
                                                    <!-- SUBMITTED FORM -->
                                                    <section data="tour-tab-submitted-form-' . $formId . '" class="cm-hipaa-accordion-tab-link cm-hipaa-active-accordion-tab-link">
                                                        <div class="cm-hipaa-tour-tab-link-inner">
                                                            <i class="material-icons">assignment</i> <span class="cm-hipaa-tab-name">FORM</span>
                                                        </div>
                                                    </section>
                                                    <div data="tour-tab-submitted-form-' . $formId . '" class="cm-hipaa-tour-tab-content">
                                                        <div id="cm-submitted-form-wrapper-' . $formId . '" class="cm-hipaa-submitted-form-fields-inner">
                                                            <!-- FORM WRAPPER -->
                                                        </div>
                                                    </div>
                                                    <!-- FILES -->
                                                    <section data="tour-tab-files-' . $formId . '" class="cm-hipaa-accordion-tab-link cm-hipaa-active-accordion-tab-link">
                                                        <div class="cm-hipaa-tour-tab-link-inner">
                                                            <i class="material-icons">attach_file</i> <span class="cm-hipaa-tab-name">FILES</span>
                                                        </div>
                                                    </section>
                                                    <div data="tour-tab-files-' . $formId . '" class="cm-hipaa-tour-tab-content">
                                                        <h3>FORM FILES</h3>
                                                        <div class="cm-hipaa-export-notice">ATTENTION! Files should only be downloaded to your computer if it is login protected & your hard drive is encrypted to remain HIPAA compliant.</div>
                                                        <div id="cm-submitted-files-' . $formId . '" class="cm-hipaa-submitted-form-files-wrapper">
                                                            ' . $fileUploadStatus . '
                                                            <div class="cm-hipaa-submitted-form-files">
                                                                <!-- FORM FILES -->
                                                                ' . implode('', $files) . '
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- NOTES -->
                                                    <section data="tour-tab-notes-' . $formId . '" class="cm-hipaa-accordion-tab-link">
                                                        <div class="cm-hipaa-tour-tab-link-inner">
                                                            <i class="material-icons">comment</i> <span class="cm-hipaa-tab-name">NOTES</span>
                                                        </div>
                                                    </section>
                                                    <div data="tour-tab-notes-' . $formId . '" class="cm-hipaa-tour-tab-content">
                                                        <h3>NOTES</h3>
                                                        <div class="cm-hipaa-form-notes-wrapper">
                                                            <div class="cm-hipaa-form-notes-top">
                                                                <div class="cm-hipaa-form-notes-add-note">
                                                                    ' . $addNote . '
                                                                </div>
                                                            </div>
                                                            <div class="cm-hipaa-form-notes">
                                                                ' . implode('', $decryptedNotes) . '
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- HISTORY -->
                                                    <section data="tour-tab-history-' . $formId . '" class="cm-hipaa-accordion-tab-link">
                                                        <div class="cm-hipaa-tour-tab-link-inner">
                                                            <i class="material-icons">history</i> <span class="cm-hipaa-tab-name">HISTORY</span>
                                                        </div>
                                                    </section>
                                                    <div data="tour-tab-history-' . $formId . '" class="cm-hipaa-tour-tab-content">
                                                        <h3>FORM HISTORY</h3>
                                                        <div class="cm-hipaa-submitted-form-history-options">
                                                            Results Per Page: <select class="cm-hipaa-submitted-form-history-limit" data-form-id="' . $formId . '">
                                                                <option value="5" selected="selected">5</option>
                                                                <option value="10">10</option>
                                                                <option value="20">20</option>
                                                                <option value="50">50</option>
                                                            </select>
                                                        </div>
                                                        <div id="cm-submitted-form-history-wrapper-' . $formId . '" class="cm-hipaa-submitted-form-history-wrapper">
                                                            <!-- FORM WRAPPER -->
                                                        </div>
                                                    </div>
                                                    <!-- EXPORT -->
                                                    <section data="tour-tab-export-' . $formId . '" class="cm-hipaa-accordion-tab-link">
                                                        <div class="cm-hipaa-tour-tab-link-inner">
                                                            <i class="material-icons">save_alt</i> <span class="cm-hipaa-tab-name">EXPORT</span>
                                                        </div>
                                                    </section>
                                                    <div data="tour-tab-export-' . $formId . '" class="cm-hipaa-tour-tab-content">
                                                        <h3>EXPORT</h3>
                                                        <div class="cm-hipaa-export-notice">ATTENTION! You should only export files to your computer if it is login protected & your hard drive is encrypted to remain HIPAA compliant.</div>
                                                        <div class="cm-hipaa-submitted-form-export-options">
                                                            <div class="cm-ghost-button cm-hipaa-export-form" data-form-id="' . $formId . '">EXPORT FORM</div>
                                                            <div class="cm-ghost-button cm-hipaa-export-form-notes" data-form-id="' . $formId . '">EXPORT FORM NOTES</div>
                                                            <div class="cm-ghost-button cm-hipaa-export-form-history" data-form-id="' . $formId . '">EXPORT FORM HISTORY</div>
                                                        </div>
                                                        <div class="cm-hipaa-submitted-form-export-results" data-form-id="' . $formId . '"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            ';
                        }

                        // RESET DEFAULT TIMEZONE BACK TO SERVER DEFAULT
                        date_default_timezone_set($defaultTz);

                        $content = '
                            <style>' . $customCss . '</style>
                            ' . $stagingMessage . '
                            <table class="cm-hipaa-submitted-forms">
                                <thead class="cm-table-header">
                                    <td>
                                    </td>
                                    <td>
                                        OFFICE LOCATION
                                    </td>
                                    <td>
                                        FIRST NAME
                                    </td>
                                    <td>
                                        LAST NAME
                                    </td>
                                    <td>
                                        FORM NAME
                                    </td>
                                    <td>
                                        SUBMISSION DATE
                                    </td>
                                    <td>
                                        PDF
                                    </td>
                                </thead>
                                ' . implode('', $forms) . '
                            </table>
                        ';

                        $results = array(
                            'success' => 'success',
                            'content' => $content,
                            'total_results' => $totalResults
                        );
                    } else {
                        $results = array(
                            'error' => 'API did not return a success message',
                            'content' => 'API did not return a success message'
                        );
                    }
                }
            }
        } else {
            $results = array(
                'error' => 'No response from API',
                'content' => 'No response from API'
            );
        }

        return json_encode($results);
    }

    /**** GET SUBMITTED FORM ***/
    public function getSubmittedForm($formId) {
        $licenseKey = esc_attr(get_option('license_key'));
        $customCss = esc_attr(get_option('hipaa_form_css'));
        $timeZone = esc_attr(get_option('time_zone'));

        if($timeZone == 'alaska') {
            $tz = 'America/Anchorage';
        } else if($timeZone == 'central') {
            $tz = 'America/Chicago';
        } else if($timeZone == 'eastern') {
            $tz = 'America/New_York';
        } else if($timeZone == 'hawaii') {
            $tz = 'America/Adak';
        } else if($timeZone == 'hawaii_no_dst') {
            $tz = 'Pacific/Honolulu';
        } else if($timeZone == 'mountain') {
            $tz = 'America/Denver';
        } else if($timeZone == 'mountain_no_dst') {
            $tz = 'America/Phoenix';
        } else if($timeZone == 'pacific') {
            $tz = 'America/Los_Angeles';
        } else {
            $tz = 'America/Chicago';
        }

        // GET USER DATA
        $user = wp_get_current_user();
        $myId = $user->ID;
        $user_roles = $user->roles;
        $role = '';
        $myDisplayName = $user->display_name;
        $myFirstName = $user->first_name;
        $myLastName = $user->last_name;
        $myEmail = $user->user_email;

        // SET MY NAME
        $myName = '';
        if($myFirstName || $myLastName) {
            $myName = $myFirstName . ' ' . $myLastName;
        } else if($myDisplayName) {
            $myName = $myDisplayName;
        } else {
            $myName = $myEmail;
        }

        /// SET ROLE
        if(in_array('administrator', $user_roles)) {
            $role = 'administrator';
        } else if(in_array('hipaa_forms', $user_roles)) {
            $role = 'hipaa';
        }

        // VALIDATE ACCOUNT
        $validateAccount = json_decode(self::validateAccount());
        $product = '';
        if(isset($validateAccount->product)) {
            $product = $validateAccount->product;
        }

        $licenseStatus = '';
        if(isset($validateAccount->license_status)) {
            $licenseStatus = $validateAccount->license_status;
        }

        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'getform',
            'license_key' => $licenseKey,
            'domain' => self::getRootDomain(),
            'form_id' => $formId,
            'my_id' => $myId,
            'my_role' => $role,
            'my_email' => $myEmail,
            'my_name' => $myName
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        if ($output) {
            foreach ($output as $data) {
                $error = '';
                if(isset($data->error)) {
                    $error = $data->error;
                }

                if ($error) {
                    if ($error == 'No BAA') {
                        $results = array(
                            'error' => $error,
                            'content' => '<div class="cm-sign-baa-button">You must sign the BAA Agreement!</div><div class="cm-sign-baa-notice">If you would rather use your own BAA please email it to contact@codemonkeysllc.com or call us at 715.941.1040</div>'
                        );
                    } else {
                        $results = array(
                            'error' => $error,
                            'content' => $error
                        );
                    }
                } else {
                    $success = $data->success;

                    if ($success == 'success') {
                        $formsData = $data->forms;

                        // GET CURRENT DEFAULT TIMEZONE
                        $defaultTz = date_default_timezone_get();
                        // SET DEFAULT TIMEZONE TO UTC
                        date_default_timezone_set('UTC');

                        $forms = array();
                        foreach ($formsData as $formData) {
                            $formId = $formData->form_id;
                            $formName = stripslashes($formData->form_name);
                            $usersTimezone = new DateTimeZone($tz);
                            $dtObj = new DateTime($formData->timestamp);
                            $dtObj->setTimeZone($usersTimezone);
                            $formDate = $dtObj->format('m-d-Y g:i a');
                            $tzAbbr = $dtObj->format('T');
                            $formHtml = $formData->form_html;
                            $encryptKey = $formData->encrypt_key;
                            $encryptIv = $formData->encrypt_iv;
                            $signature = $formData->signature;

                            // DECRYPT FORM HTML
                            $decryptedForm = '';
                            if($formHtml) {
                                $decryptedForm = stripslashes(self::decrypt($formHtml, $encryptKey, $encryptIv));
                            }

                            // GET FORM NOTES
                            $formNotes = '';
                            if(isset(json_decode($formData->form_notes)[0]->notes)) {
                                $formNotes = json_decode($formData->form_notes)[0]->notes;
                            }

                            // DECRYPT FORM NOTES
                            $decryptedNotes = array();
                            if(!empty($formNotes)) {
                                foreach($formNotes as $formNote) {
                                    $note = $formNote->note;
                                    $noteKey = $formNote->key;
                                    $noteIv = $formNote->iv;
                                    $decryptedNote = self::decrypt($note, $noteKey, $noteIv);

                                    $dtObj = new DateTime($formNote->timestamp);
                                    if($tz) {
                                        $dtObj->setTimeZone($usersTimezone);
                                    }
                                    $noteDate = $dtObj->format('M jS Y g:i a');
                                    $tzAbbr = $dtObj->format('T');

                                    $decryptedNotes[] = '
                                        <div class="cm-hipaa-submitted-form-note-wrapper">
                                            <div class="cm-hipaa-submitted-form-note-date">
                                                ' . $noteDate . ' ' . $tzAbbr . '
                                            </div>
                                            <div class="cm-hipaa-submitted-form-note-name">
                                                ' . $formNote->name . '
                                            </div>
                                            <div class="cm-hipaa-submitted-form-note">
                                                ' . $decryptedNote . '
                                            </div>
                                        </div>
                                    ';
                                }
                            }

                            // SET SIGNATURE
                            if ($signature) {
                                $signature = '
                                    <div class="cm-hipaa-submitted-form-signature">
                                        <img src="' . $signature . '" alt="Signature" />
                                    </div>
                                ';
                            }

                            $webForm = $decryptedForm;

                            $forms[] = '
                                <h2 class="cm-submitted-form-title">' . $formName . ' <i class="cm-hipaa-submitted-form-print material-icons" data-form-id="' . $formId . '">print</i></h2>
                                ' . $webForm . '
                                <div class="clearfix"></div>
                                ' . $signature . '
                                <div class="cm-hipaa-submitted-form-webview-notes">
                                    <h4>NOTES:</h4>
                                    ' . implode('', $decryptedNotes) . '
                                </div>
                            ';
                        }

                        // RESET DEFAULT TIMEZONE BACK TO SERVER DEFAULT
                        date_default_timezone_set($defaultTz);

                        $form = '
                            ' . implode('', $forms) . '
                        ';

                        $results = array(
                            'success' => 'success',
                            'form' => $form
                        );
                    } else {
                        $results = array(
                            'error' => 'API did not return a success message',
                            'content' => 'API did not return a success message'
                        );
                    }
                }
            }
        } else {
            $results = array(
                'error' => 'No response from API',
                'content' => 'No response from API'
            );
        }

        return json_encode($results);
    }

    /*** GET CALDERA FORMS LIST ***/
    public function getCalderaForms($enabledForms) {
        /*** GET CALDERA FORMS (ID & FORM NAME) ***/
        $forms = Caldera_Forms_Forms::get_forms(true, true);
        $oldEnabledForms = explode(',', esc_attr(get_option('caldera_enabled_form_ids'))); // OLD COMMA DELIMITED STRING
        $enabledForms = json_decode(trim($enabledForms)); // REMOVED STRIPTAGS, CANT REMEMBER WHY WE DID THIS TO BEGIN WITH OTHER THAN JUST BEING SAFE BUT DOESN'T SEEM TO BREAK ANYTHING

        if (!empty($forms)) {
            // LOOP ALL CALDERA FORMS
            $formsArray = array();
            foreach ($forms as $form) {
                $errors = array();
                $errorClass = '';
                $errorHeading = '';
                $errorIcon = '';
                $fieldsArray = array();
                $fieldSlugs = array();
                $formId = $form['ID'];
                $formName = $form['name'];
                $formData = Caldera_Forms_Forms::get_form($formId);
                $formDataMailer = $formData['mailer'];
                $formSenderName = $formDataMailer['sender_name'];
                $formSenderEmail = $formDataMailer['sender_email'];
                $formReplyTo = $formDataMailer['reply_to'];
                $formRecipients = $formDataMailer['recipients'];
                $formBccTo = $formDataMailer['bcc_to'];
                $formSubject = $formDataMailer['email_subject'];
                $fields = $formData['fields'];

                //var_dump($formData);

                /* GET FIELDS */
                foreach ($fields as $field) {
                    $fieldSlugs[] = $field['slug'];
                    $fieldsArray[] = $field['label'] . ' - ' . $field['slug'];
                }

                /* SET FIELD ERRORS */
                if (!in_array('first_name', $fieldSlugs)) {
                    $errors[] = 'No field with first_name slug';
                }

                if (!in_array('last_name', $fieldSlugs)) {
                    $errors[] = 'No field with last_name slug';
                }

                if (!in_array('email', $fieldSlugs)) {
                    $errors[] = 'No field with email slug';
                }

                $fieldsArray = implode('<br />', $fieldsArray);

                if (!empty($errors)) {
                    $errorClass = ' errors';
                    $errorIcon = '
                        <span class="cm-hipaa-toggle-errors"> <i class="material-icons" title="You Are Missing Required Fields!">warning</i></span>
                    ';
                    $errorHeading = '<h4>Errors</h4>';
                }

                $errors = implode('<br />', $errors);

                // GET FORM SETTINGS
                $formSettingsEnabled = '';
                $formSettingsShowSig = '';
                $formSettingsSuccessHandler = '';
                $formSettingsSuccessMessage = 'Thank you, your form has been encrypted to protect your privacy and submitted successfully!';
                $formSettingsSuccessRedirect = '';
                $formSettingsSuccessCallback = '';
                $formSettingsSuccessCallbackParams = '';
                $formSettingsUsersHandler = '';
                $formSettingsApprovedUsers = '';
                $formSettingsSelectedUserSlug = '';
                $formSettingsNotificationOption = '';
                $formSettingsNotificationFromName = '';
                $formSettingsNotificationFromEmail = '';
                $formSettingsNotificationSendTo = '';
                $formSettingsNotificationSubject = '';
                $formSettingsNotificationMessage = '';

                if(!empty($enabledForms)) {
                    foreach ($enabledForms as $enabledForm) {
                        if ($formId == $enabledForm->id) {
                            if(isset($enabledForm->enabled)){
                                $formSettingsEnabled = $enabledForm->enabled;
                            };
                            if(isset($enabledForm->show_signature)){
                                $formSettingsShowSig = $enabledForm->show_signature;
                            };
                            if(isset($enabledForm->success_handler)){
                                $formSettingsSuccessHandler = $enabledForm->success_handler;
                            };
                            if(isset($enabledForm->success_message)){
                                $formSettingsSuccessMessage = $enabledForm->success_message;
                            };
                            if(isset($enabledForm->success_redirect)){
                                $formSettingsSuccessRedirect = $enabledForm->success_redirect;
                            };
                            if(isset($enabledForm->success_callback)){
                                $formSettingsSuccessCallback = $enabledForm->success_callback;
                            };
                            if(isset($enabledForm->success_callback_params)){
                                $formSettingsSuccessCallbackParams = $enabledForm->success_callback_params;
                            };
                            if(isset($enabledForm->users_handler)){
                                $formSettingsUsersHandler = $enabledForm->users_handler;
                            };
                            if(isset($enabledForm->approved_users)){
                                $formSettingsApprovedUsers = $enabledForm->approved_users;
                            };
                            if(isset($enabledForm->selected_user_slug)){
                                $formSettingsSelectedUserSlug = $enabledForm->selected_user_slug;
                            };
                            if(isset($enabledForm->notification_option)){
                                $formSettingsNotificationOption = $enabledForm->notification_option;
                            };
                            if(isset($enabledForm->notification_from_name)){
                                $formSettingsNotificationFromName = $enabledForm->notification_from_name;
                            };
                            if(isset($enabledForm->notification_from_email)){
                                $formSettingsNotificationFromEmail = $enabledForm->notification_from_email;
                            };
                            if(isset($enabledForm->notification_sendto)){
                                $formSettingsNotificationSendTo = $enabledForm->notification_sendto;
                            };
                            if(isset($enabledForm->notification_subject)){
                                $formSettingsNotificationSubject = $enabledForm->notification_subject;
                            };
                            if(isset($enabledForm->notification_message)){
                                $formSettingsNotificationMessage = $enabledForm->notification_message;
                            };
                        }
                    }
                }

                // SET FROM NAME
                if(!$formSettingsNotificationFromName) {
                    if(get_option('hipaa_notification_from_name')) {
                        // SET FROM NAME SAME AS DEFAULT IF NO VALUE
                        $formSettingsNotificationFromName = esc_attr(get_option('hipaa_notification_from_name'));
                    } else {
                        // SET WOREDPRESS SITE TITLE AS NOTIFICATION EMAIL FROM NAME IF NO DEFAULT SET
                        $formSettingsNotificationFromName = get_bloginfo('name');
                    }
                }

                // SET FROM EMAIL
                if(!$formSettingsNotificationFromEmail) {
                    if(get_option('hipaa_notification_from_email')) {
                        // SET FROM EMAIL SAME AS DEFAULT IF NO VALUE
                        $formSettingsNotificationFromEmail = esc_attr(get_option('hipaa_notification_from_email'));
                    } else {
                        // SET WOREDPRESS ADMIN EMAIL AS NOTIFICATION EMAIL IF NO DEFAULT SET
                        $formSettingsNotificationFromEmail = get_bloginfo('admin_email');
                    }
                }

                // SET SENDTO EMAILS
                if(!$formSettingsNotificationSendTo) {
                    if(get_option('notification_email')) {
                        // SET SENDTO EMAILS SAME AS DEFAULT IF NO VALUE
                        $formSettingsNotificationSendTo = esc_attr(get_option('notification_email'));
                    } else {
                        // SET WOREDPRESS ADMIN AS NOTIFICATION EMAIL IF NO DEFAULT SET
                        $formSettingsNotificationSendTo = get_bloginfo('admin_email');
                    }
                }

                // SET DEFAULT FORM SPECIFIC SUBJECT SAME AS DEFAULT IF NO VALUE
                if(!$formSettingsNotificationSubject) {
                    if(get_option('hipaa_notification_email_subject')) {
                        // SET DEFAULT SUBJECT
                        $formSettingsNotificationSubject = esc_attr(get_option('hipaa_notification_email_subject'));
                    } else {
                        // IF NO DEFAULT SUBJECT CREATE ONE
                        $formSettingsNotificationSubject = 'HIPAA Form Submission {location}';
                    }

                }

                // SET DEFAULT FORM SPECIFIC MESSAGE SAME AS DEFAULT IF NO VALUE
                if(!$formSettingsNotificationMessage) {
                    if(get_option('hipaa_notification_email')) {
                        $formSettingsNotificationMessage = get_option('hipaa_notification_email');
                    } else {
                        $formSettingsNotificationMessage = '<table width="600px" style="border-collapse:collapse;">
                            <tbody>
                                <tr>
                                    <th><b>{formname} HIPAA Form Submission</b></th>
                                </tr>
                                <tr>
                                    <td style="height:30px"></td>
                                </tr>
                                <tr>
                                    <td>
                                        Location: {location}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        First Name: {firstname}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height:15px"></td>
                                </tr>
                                <tr>
                                    <td>
                                        Last Name: {lastname}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height:15px"></td>
                                </tr>
                                <tr>
                                    <td>
                                        Email: {email}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height:15px"></td>
                                </tr>
                                <tr>
                                    <td>
                                        Phone: {phone}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height:30px"></td>
                                </tr>
                                <tr>
                                    <td>
                                        Please log into the admin dashboard to view the form.
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Please do not reply to this email
                                    </td>
                                </tr>
                            </tbody>
                        </table>';
                    }
                }

                // CHECK IF FORM IS SELECTED - OLD ENABLED FORMS FOR BACKWARDS COMPATIBILITY
                if(in_array($formId, $oldEnabledForms) || $formSettingsEnabled == 'yes') {
                    $formSelectIcon = '<i class="material-icons">check_box</i>';
                    $formSelected = ' selected';
                } else {
                    $formSelectIcon = '<i class="material-icons">check_box_outline_blank</i>';
                    $formSelected = '';
                }

                // SET SIGNATURE CHECKBOX
                $showSigChecked = 'checked="checked"';
                if(!$formSettingsShowSig || $formSettingsShowSig == 'no') {
                    $showSigChecked = '';
                }

                // SET SUCCESS HANDLER RADIO
                $successHandlerMessage = 'checked="checked"';
                $successHandlerRedirect = '';
                $successHandlerCallback = '';
                $successHandlerMessageActive = 'active';
                $successHandlerRedirectActive = '';
                $successHandlerCallbackActive = '';
                if($formSettingsSuccessHandler === 'redirect') {
                    $successHandlerMessage = '';
                    $successHandlerCallback = '';
                    $successHandlerRedirect = 'checked="checked"';
                    $successHandlerMessageActive = '';
                    $successHandlerCallbackActive = '';
                    $successHandlerRedirectActive = 'active';
                } else if($formSettingsSuccessHandler === 'callback') {
                    $successHandlerMessage = '';
                    $successHandlerRedirect = '';
                    $successHandlerCallback = 'checked="checked"';
                    $successHandlerMessageActive = '';
                    $successHandlerRedirectActive = '';
                    $successHandlerCallbackActive = 'active';
                }

                // SET SUCCESS MESSAGE IF NOT SET
                if(!$formSettingsSuccessMessage) {
                    $formSettingsSuccessMessage = 'Thank you, your form has been encrypted to protect your privacy and submitted successfully!';
                }

                // SET USERS HANDLER RADIO
                $usersHandlerAll = 'checked="checked"';
                $usersHandlerSpecific = '';
                $usersHandlerSelected = '';
                $usersHandlerSpecificActive = '';
                $usersHandlerSelectedActive = '';
                if($formSettingsUsersHandler == 'specific') {
                    $usersHandlerAll = '';
                    $usersHandlerSpecific = 'checked="checked"';
                    $usersHandlerSelected = '';
                    $usersHandlerSpecificActive = 'active';
                    $usersHandlerSelectedActive = '';
                } else if($formSettingsUsersHandler == 'selected') {
                    $usersHandlerAll = '';
                    $usersHandlerSpecific = '';
                    $usersHandlerSelected = 'checked="checked"';
                    $usersHandlerSpecificActive = '';
                    $usersHandlerSelectedActive = 'active';
                }

                // SET APPROVED USERS CHECKBOXES
                $approvedUsers = self::getApprovedUsers();
                $formSettingsApprovedUsers = explode(',', $formSettingsApprovedUsers);
                $approvedUsersOptions = array();
                foreach($approvedUsers as $approvedUser) {
                    if(in_array($approvedUser->ID, $formSettingsApprovedUsers)) {
                        $userChecked = 'checked="checked"';
                    } else {
                        $userChecked = '';
                    }
                    $approvedUsersOptions[] = '
                        <input type="checkbox" name="cm-hipaa-forms-approved-users" value="' . $approvedUser->ID . '" ' . $userChecked . ' /> ' . $approvedUser->display_name . '
                    ';
                }

                // SET NOTIFICATION HANDLER RADIO
                $notificationHandlerDefault = 'checked="checked"';
                $notificationHandlerCustom = '';
                $notificationHandlerCustomActive = '';
                if($formSettingsNotificationOption == 'custom') {
                    $notificationHandlerDefault = '';
                    $notificationHandlerCustom = 'checked="checked"';
                    $notificationHandlerCustomActive = 'active';
                }

                $formsArray[] = '
                    <div id="' . $formId . '" class="cm-hipaa-select-form-item' . $errorClass . $formSelected . '">
                        <div class="cm-hipaa-select-form-item-name">
                            <span class="cm-hipaa-form-select">
                                ' . $formSelectIcon . '
                            </span>
                            <span class="cm-hipaa-toggle-fields" title="Show Fields">
                                <i class="material-icons" title="Toggle Fields">arrow_drop_down_circle</i>
                            </span>
                            <span class="cm-hipaa-select-form-info">
                                ' . $errorIcon . ' ' . $formName . ' - Caldera Form ID: ' . $formId . '
                            </span>
                        </div>
                        <div class="cm-hipaa-select-form-item-fields">
                            <form class="cm-hipaa-select-form-form" data="' . $formId . '">
                                <div class="cm-hipaa-select-form-field-wrapper">
                                    <h4>SIGNATURE FIELD</h4> <i class="material-icons cm-hipaa-setting-info" data-content="signature-settings-info" title="More Information">info</i>
                                    <div class="cm-hipaa-select-form-field">
                                        <input type="checkbox" name="cm-hipaa-forms-show-signature" value="yes" ' . $showSigChecked . ' /> Show Signature 
                                    </div>
                                </div>
                                <div class="cm-hipaa-select-form-field-wrapper">
                                    <h4>SUBMIT SUCCESS HANDLER</h4> <i class="material-icons cm-hipaa-setting-info" data-content="success-handler-info" title="More Information">info</i>
                                    <div class="cm-hipaa-select-form-field">
                                        <input type="radio" name="cm-hipaa-forms-success-handler" class="cm-hipaa-forms-success-message-radio" value="message" ' . $successHandlerMessage . ' /> Success Message
                                        <input type="radio" name="cm-hipaa-forms-success-handler" class="cm-hipaa-forms-success-redirect-radio" value="redirect" ' . $successHandlerRedirect . ' /> Success Redirect
                                        <input type="radio" name="cm-hipaa-forms-success-handler" class="cm-hipaa-forms-success-callback-radio" value="callback" ' . $successHandlerCallback . ' /> Success Callback
                                    </div>
                                    <div class="cm-hipaa-select-form-field">
                                        <div class="cm-hipaa-forms-success-handler-field cm-hipaa-forms-success-message-wrapper ' . $successHandlerMessageActive . '">
                                            <textarea name="cm-hipaa-forms-success-message" placeholder="Success Message">' . $formSettingsSuccessMessage . '</textarea>
                                        </div>
                                        <div class="cm-hipaa-forms-success-handler-field cm-hipaa-forms-success-redirect-wrapper ' . $successHandlerRedirectActive . '">
                                            <input type="text" name="cm-hipaa-forms-success-redirect" placeholder="Redirect URL" value="' . $formSettingsSuccessRedirect . '" />
                                        </div>
                                        <div class="cm-hipaa-forms-success-handler-field cm-hipaa-forms-success-callback-wrapper ' . $successHandlerCallbackActive . '">
                                            <div class="cm-hipaa-forms-experimental-notice">
                                                * The callback function is experimental
                                            </div>
                                            <input type="text" name="cm-hipaa-forms-success-callback" placeholder="Callback Function Name (no parenthesis)" value="' . $formSettingsSuccessCallback . '" /><br />
                                            <input type="text" name="cm-hipaa-forms-success-callback-params" placeholder="Callback Params (ie. param1, param2)" value="' . $formSettingsSuccessCallbackParams . '" />
                                        </div>
                                    </div>
                                </div>
                                <div class="cm-hipaa-select-form-field-wrapper">
                                    <h4>WHO CAN VIEW THIS FORM?</h4> <i class="material-icons cm-hipaa-setting-info" data-content="user-specific-info" title="More Information">info</i>
                                    <div class="cm-hipaa-select-form-field">
                                        <input type="radio" name="cm-hipaa-forms-users-handler" class="cm-hipaa-forms-users-all-radio" value="all" ' . $usersHandlerAll . ' /> All Admin/HIPAA Users
                                        <input type="radio" name="cm-hipaa-forms-users-handler" class="cm-hipaa-forms-users-specific-radio" value="specific" ' . $usersHandlerSpecific . ' /> Only Specific Users
                                        <input type="radio" name="cm-hipaa-forms-users-handler" class="cm-hipaa-forms-users-selected-radio" value="selected" ' . $usersHandlerSelected . ' /> Selected User
                                    </div>
                                    <div class="cm-hipaa-select-form-field cm-hipaa-forms-approved-users-wrapper ' . $usersHandlerSpecificActive . '">
                                        ' . implode('', $approvedUsersOptions) . '
                                    </div>
                                    <div class="cm-hipaa-select-form-field cm-hipaa-forms-selected-users-wrapper ' . $usersHandlerSelectedActive . '">
                                        <input type="text" name="cm-hipaa-forms-selected-user-slug" placeholder="Select Field Slug (option value must be user id)" value="' . $formSettingsSelectedUserSlug . '" />
                                    </div>
                                </div>
                                <div class="cm-hipaa-select-form-field-wrapper">
                                    <h4>NOTIFICATION EMAIL</h4> <i class="material-icons cm-hipaa-setting-info" data-content="form-specific-notification-info" title="More Information">info</i>
                                    <div class="cm-hipaa-select-form-field">
                                        <input type="radio" name="cm-hipaa-forms-notification-handler" class="cm-hipaa-forms-notification-default-radio" value="default" ' . $notificationHandlerDefault . ' /> Default
                                        <input type="radio" name="cm-hipaa-forms-notification-handler" class="cm-hipaa-forms-notification-custom-radio" value="custom" ' . $notificationHandlerCustom . ' /> Custom
                                    </div>
                                    <div class="cm-hipaa-forms-custom-notification-wrapper ' . $notificationHandlerCustomActive . '">
                                        <div class="cm-hipaa-select-form-field cm_hipaa_grid_row_nogap">
                                            <div class="cm_hipaa_col_50">
                                                <input type="text" name="cm-hipaa-selected-form-notification-from-name-input" placeholder="Name the notification email is from..." value="' . $formSettingsNotificationFromName . '" />
                                            </div>
                                            <div class="cm_hipaa_col_50">
                                                <input type="text" name="cm-hipaa-selected-form-notification-from-email-input" placeholder="Email the notification is from..." value="' . $formSettingsNotificationFromEmail . '" />
                                            </div>
                                        </div>
                                        <div class="cm-hipaa-select-form-field">
                                            <input type="text" name="cm-hipaa-selected-form-notification-sendto-input" placeholder="Emails to send notifications to..." value="' . $formSettingsNotificationSendTo . '" />
                                        </div>
                                        <div class="cm-hipaa-select-form-field">
                                            <input type="text" name="cm-hipaa-selected-form-notification-subject-input" placeholder="Notification Email Subject..." value="' . $formSettingsNotificationSubject . '" />
                                        </div>
                                        <div class="cm-hipaa-select-form-field">
                                            <textarea name="cm-hipaa-selected-form-notification-message-input" placeholder="Notification Email...">' . $formSettingsNotificationMessage . '</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="cm-form-settings-notice"></div>
                                <div class="cm-button cm-hipaa-forms-form-settings-submit">SUBMIT</div>
                                <div class="clearfix"></div>
                            </form>
                        </div>
                        <div class="cm-hipaa-select-form-item-errors">
                            ' . $errorHeading . '
                            ' . $errors . '
                        </div>
                    </div>
                ';
            }

            return implode('', $formsArray);
        }

        return false;
    }

    /*** GET GRAVITY FORMS LIST ***/
    public function getGravityForms($enabledForms) {
        // GET GRAVITY FORMS
        $forms = RGFormsModel::get_forms( null, 'title' );

        $oldEnabledForms = explode(',', esc_attr(get_option('gravity_enabled_form_ids'))); // OLD COMMA DELIMITED STRING
        //$enabledForms = json_decode(trim(strip_tags($enabledForms)));
        $enabledForms = json_decode(trim($enabledForms)); // REMOVED STRIPTAGS, CANT REMEMBER WHY WE DID THIS TO BEGIN WITH OTHER THAN JUST BEING SAFE BUT DOESN'T SEEM TO BREAK ANYTHING

        if (!empty($forms)) {
            $formsArray = array();
            // LOOP FORMS
            foreach($forms as $form) {
                $errors = array();
                $errorClass = '';
                $errorHeading = '';
                $errorIcon = '';
                $fieldsArray = array();
                $fieldClasses = array();
                $formId = $form->id;
                $formName = $form->title;
                $active = $form->is_active;

                if($active == 1) {
                    // GET FIELDS
                    $formMeta = RGFormsModel::get_form_meta($formId);
                    $formNotifications = $formMeta['notifications'];

                    if(is_array($formNotifications)) {
                        // GET EMAIL VALUES
                        foreach($formNotifications as $formNotification) {
                            if(!empty($formNotification['to'])) {
                                $mailTo = $formNotification['to'];
                            }
                            if(!empty($formNotification['fromName'])) {
                                $mailFromName = $formNotification['fromName'];
                            }
                            if(!empty($formNotification['from'])) {
                                $mailFromEmail = $formNotification['from'];
                            }
                            if(!empty($formNotification['bcc'])) {
                                $mailBcc = $formNotification['bcc'];
                            }
                            $adminEmail = get_bloginfo('admin_email');

                            // REPLACE MERGE TAGS IF USED
                            if($mailTo == '{admin_email}') {
                                $mailTo = $adminEmail;
                            }

                            if($mailFromEmail == '{admin_email}') {
                                $mailFromEmail = $adminEmail;
                            }
                        }
                    }

                    if(is_array($formMeta["fields"])){
                        // LOOP FIELDS
                        foreach($formMeta["fields"] as $field){
                            if(isset($field["inputs"]) && is_array($field["inputs"])){
                                // EXPLODE THE CLASSES IN CASE MULTIPLE CLASSES EXIST
                                $fieldClassesRaw = explode(' ', $field['cssClass']);

                                if(!empty($fieldClassesRaw)) {
                                    // IF CLASSES EXIST LOOP EACH CLASS AND PUSH TO THE FIELD CLASSES ARRAY
                                    foreach($fieldClassesRaw as $fieldClassRaw) {
                                        $fieldClasses[] = $fieldClassRaw;
                                    }
                                }


                                // IF OPTION FIELD SUCH AS CHECKBOX
                                $optionsArray = array();
                                foreach($field["inputs"] as $input) {
                                    if(!empty($input['cssClass'])) {
                                        $fieldClasses[] = $input["cssClass"];
                                    }
                                    $optionsArray[] = GFCommon::get_label($field, $input["id"]);
                                }

                                $fieldsArray[] = '<div class="cm-hipaa-select-form-input">Label: ' . GFCommon::get_label($field) . ' - Class: ' . $field["cssClass"] . '<div class="cm-hipaa-select-form-input-options">Option: ' . implode('<br />', $optionsArray) . '</div></div>';
                            } else if(!rgar($field, 'displayOnly')){
                                // EXPLODE THE CLASSES IN CASE MULTIPLE CLASSES EXIST
                                $fieldClassesRaw = explode(' ', $field['cssClass']);

                                if(!empty($fieldClassesRaw)) {
                                    // IF CLASSES EXIST LOOP EACH CLASS AND PUSH TO THE FIELD CLASSES ARRAY
                                    foreach($fieldClassesRaw as $fieldClassRaw) {
                                        $fieldClasses[] = $fieldClassRaw;
                                    }
                                }

                                $fieldsArray[] = '<div class="cm-hipaa-select-form-input">Label: ' . GFCommon::get_label($field) . ' - Class: ' . $field["cssClass"] . '</div>';
                            }
                        }
                    }

                    /* LOOP FIELD CLASSES AND SET FIELD ERRORS IF REQUIRED FIELD CLASSES DON'T EXIST */
                    if (!in_array('hipaa_forms_first_name', $fieldClasses) && !in_array('hipaa_forms_name', $fieldClasses)) {
                        $errors[] = 'No field with hipaa_forms_first_name class';
                    }

                    if (!in_array('hipaa_forms_last_name', $fieldClasses) && !in_array('hipaa_forms_name', $fieldClasses)) {
                        $errors[] = 'No field with hipaa_forms_last_name class';
                    }

                    if (!in_array('hipaa_forms_email', $fieldClasses)) {
                        $errors[] = 'No field with hipaa_forms_email class';
                    }

                    $fieldsArray = implode('', $fieldsArray);

                    if (!empty($errors)) {
                        $errorClass = ' errors';
                        $errorIcon = '
                            <span class="cm-hipaa-toggle-errors"> <i class="material-icons" title="You Are Missing Required Fields!">warning</i></span>
                        ';
                        $errorHeading = '<h4>Errors</h4>';
                    }
                    $errors = implode('<br />', $errors);

                    // GET FORM SETTINGS
                    $formSettingsEnabled = '';
                    $formSettingsShowSig = '';
                    $formSettingsSuccessHandler = '';
                    $formSettingsSuccessMessage = 'Thank you, your form has been encrypted to protect your privacy and submitted successfully!';
                    $formSettingsSuccessRedirect = '';
                    $formSettingsSuccessCallback = '';
                    $formSettingsSuccessCallbackParams = '';
                    $formSettingsUsersHandler = '';
                    $formSettingsApprovedUsers = '';
                    $formSettingsSelectedUserSlug = '';
                    $formSettingsNotificationOption = '';
                    $formSettingsNotificationFromName = '';
                    $formSettingsNotificationFromEmail = '';
                    $formSettingsNotificationSendTo = '';
                    $formSettingsNotificationSubject = '';
                    $formSettingsNotificationMessage = '';

                    if(!empty($enabledForms)) {
                        foreach ($enabledForms as $enabledForm) {
                            if ($formId == $enabledForm->id) {
                                if(isset($enabledForm->enabled)){
                                    $formSettingsEnabled = $enabledForm->enabled;
                                };
                                if(isset($enabledForm->show_signature)){
                                    $formSettingsShowSig = $enabledForm->show_signature;
                                };
                                if(isset($enabledForm->success_handler)){
                                    $formSettingsSuccessHandler = $enabledForm->success_handler;
                                };
                                if(isset($enabledForm->success_message)){
                                    $formSettingsSuccessMessage = $enabledForm->success_message;
                                };
                                if(isset($enabledForm->success_redirect)){
                                    $formSettingsSuccessRedirect = $enabledForm->success_redirect;
                                };
                                if(isset($enabledForm->success_callback)){
                                    $formSettingsSuccessCallback = $enabledForm->success_callback;
                                };
                                if(isset($enabledForm->success_callback_params)){
                                    $formSettingsSuccessCallbackParams = $enabledForm->success_callback_params;
                                };
                                if(isset($enabledForm->users_handler)){
                                    $formSettingsUsersHandler = $enabledForm->users_handler;
                                };
                                if(isset($enabledForm->approved_users)){
                                    $formSettingsApprovedUsers = $enabledForm->approved_users;
                                };
                                if(isset($enabledForm->selected_user_slug)){
                                    $formSettingsSelectedUserSlug = $enabledForm->selected_user_slug;
                                };
                                if(isset($enabledForm->notification_option)){
                                    $formSettingsNotificationOption = $enabledForm->notification_option;
                                };
                                if(isset($enabledForm->notification_from_name)){
                                    $formSettingsNotificationFromName = $enabledForm->notification_from_name;
                                };
                                if(isset($enabledForm->notification_from_email)){
                                    $formSettingsNotificationFromEmail = $enabledForm->notification_from_email;
                                };
                                if(isset($enabledForm->notification_sendto)){
                                    $formSettingsNotificationSendTo = $enabledForm->notification_sendto;
                                };
                                if(isset($enabledForm->notification_subject)){
                                    $formSettingsNotificationSubject = $enabledForm->notification_subject;
                                };
                                if(isset($enabledForm->notification_message)){
                                    $formSettingsNotificationMessage = $enabledForm->notification_message;
                                };
                            }
                        }
                    }

                    // SET FROM NAME
                    if(!$formSettingsNotificationFromName) {
                        if(get_option('hipaa_notification_from_name')) {
                            // SET FROM NAME SAME AS DEFAULT IF NO VALUE
                            $formSettingsNotificationFromName = esc_attr(get_option('hipaa_notification_from_name'));
                        } else {
                            // SET WOREDPRESS SITE TITLE AS NOTIFICATION EMAIL FROM NAME IF NO DEFAULT SET
                            $formSettingsNotificationFromName = get_bloginfo('name');
                        }
                    }

                    // SET FROM EMAIL
                    if(!$formSettingsNotificationFromEmail) {
                        if(get_option('hipaa_notification_from_email')) {
                            // SET FROM EMAIL SAME AS DEFAULT IF NO VALUE
                            $formSettingsNotificationFromEmail = esc_attr(get_option('hipaa_notification_from_email'));
                        } else {
                            // SET WOREDPRESS ADMIN EMAIL AS NOTIFICATION EMAIL IF NO DEFAULT SET
                            $formSettingsNotificationFromEmail = get_bloginfo('admin_email');
                        }
                    }

                    // SET SENDTO EMAILS
                    if(!$formSettingsNotificationSendTo) {
                        if(get_option('notification_email')) {
                            // SET SENDTO EMAILS SAME AS DEFAULT IF NO VALUE
                            $formSettingsNotificationSendTo = esc_attr(get_option('notification_email'));
                        } else {
                            // SET WOREDPRESS ADMIN AS NOTIFICATION EMAIL IF NO DEFAULT SET
                            $formSettingsNotificationSendTo = get_bloginfo('admin_email');
                        }
                    }

                    // SET DEFAULT FORM SPECIFIC SUBJECT SAME AS DEFAULT IF NO VALUE
                    if(!$formSettingsNotificationSubject) {
                        if(get_option('hipaa_notification_email_subject')) {
                            // SET DEFAULT SUBJECT
                            $formSettingsNotificationSubject = get_option('hipaa_notification_email_subject');
                        } else {
                            // IF NO DEFAULT SUBJECT CREATE ONE
                            $formSettingsNotificationSubject = 'HIPAA Form Submission {location}';
                        }

                    }

                    // SET DEFAULT FORM SPECIFIC MESSAGE SAME AS DEFAULT IF NO VALUE
                    if(!$formSettingsNotificationMessage) {
                        if(get_option('hipaa_notification_email')) {
                            $formSettingsNotificationMessage = get_option('hipaa_notification_email');
                        } else {
                            $formSettingsNotificationMessage = '<table width="600px" style="border-collapse:collapse;">
                            <tbody>
                                <tr>
                                    <th><b>{formname} HIPAA Form Submission</b></th>
                                </tr>
                                <tr>
                                    <td style="height:30px"></td>
                                </tr>
                                <tr>
                                    <td>
                                        Location: {location}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        First Name: {firstname}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height:15px"></td>
                                </tr>
                                <tr>
                                    <td>
                                        Last Name: {lastname}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height:15px"></td>
                                </tr>
                                <tr>
                                    <td>
                                        Email: {email}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height:15px"></td>
                                </tr>
                                <tr>
                                    <td>
                                        Phone: {phone}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height:30px"></td>
                                </tr>
                                <tr>
                                    <td>
                                        Please log into the admin dashboard to view the form.
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Please do not reply to this email
                                    </td>
                                </tr>
                            </tbody>
                        </table>';
                        }
                    }

                    // CHECK IF FORM IS SELECTED - OLD ENABLED FORMS FOR BACKWARDS COMPATIBILITY
                    if(in_array($formId, $oldEnabledForms) || $formSettingsEnabled == 'yes') {
                        $formSelectIcon = '<i class="material-icons">check_box</i>';
                        $formSelected = ' selected';
                    } else {
                        $formSelectIcon = '<i class="material-icons">check_box_outline_blank</i>';
                        $formSelected = '';
                    }

                    // SET SIGNATURE CHECKBOX
                    $showSigChecked = 'checked="checked"';
                    if(!$formSettingsShowSig || $formSettingsShowSig == 'no') {
                        $showSigChecked = '';
                    }

                    // SET SUCCESS HANDLER RADIO
                    $successHandlerMessage = 'checked="checked"';
                    $successHandlerRedirect = '';
                    $successHandlerCallback = '';
                    $successHandlerMessageActive = 'active';
                    $successHandlerRedirectActive = '';
                    $successHandlerCallbackActive = '';
                    if($formSettingsSuccessHandler === 'redirect') {
                        $successHandlerMessage = '';
                        $successHandlerCallback = '';
                        $successHandlerRedirect = 'checked="checked"';
                        $successHandlerMessageActive = '';
                        $successHandlerCallbackActive = '';
                        $successHandlerRedirectActive = 'active';
                    } else if($formSettingsSuccessHandler === 'callback') {
                        $successHandlerMessage = '';
                        $successHandlerRedirect = '';
                        $successHandlerCallback = 'checked="checked"';
                        $successHandlerMessageActive = '';
                        $successHandlerRedirectActive = '';
                        $successHandlerCallbackActive = 'active';
                    }

                    // SET SUCCESS MESSAGE IF NOT SET
                    if(!$formSettingsSuccessMessage) {
                        $formSettingsSuccessMessage = 'Thank you, your form has been encrypted to protect your privacy and submitted successfully!';
                    }

                    // SET USERS HANDLER RADIO
                    $usersHandlerAll = 'checked="checked"';
                    $usersHandlerSpecific = '';
                    $usersHandlerSelected = '';
                    $usersHandlerSpecificActive = '';
                    $usersHandlerSelectedActive = '';
                    if($formSettingsUsersHandler == 'specific') {
                        $usersHandlerAll = '';
                        $usersHandlerSpecific = 'checked="checked"';
                        $usersHandlerSelected = '';
                        $usersHandlerSpecificActive = 'active';
                        $usersHandlerSelectedActive = '';
                    } else if($formSettingsUsersHandler == 'selected') {
                        $usersHandlerAll = '';
                        $usersHandlerSpecific = '';
                        $usersHandlerSelected = 'checked="checked"';
                        $usersHandlerSpecificActive = '';
                        $usersHandlerSelectedActive = 'active';
                    }

                    // SET APPROVED USERS CHECKBOXES
                    $approvedUsers = self::getApprovedUsers();
                    $formSettingsApprovedUsers = explode(',', $formSettingsApprovedUsers);
                    $approvedUsersOptions = array();
                    foreach($approvedUsers as $approvedUser) {
                        if(in_array($approvedUser->ID, $formSettingsApprovedUsers)) {
                            $userChecked = 'checked="checked"';
                        } else {
                            $userChecked = '';
                        }
                        $approvedUsersOptions[] = '
                            <input type="checkbox" name="cm-hipaa-forms-approved-users" value="' . $approvedUser->ID . '" ' . $userChecked . ' /> ' . $approvedUser->display_name . '
                        ';
                    }

                    // SET NOTIFICATION HANDLER RADIO
                    $notificationHandlerDefault = 'checked="checked"';
                    $notificationHandlerCustom = '';
                    $notificationHandlerCustomActive = '';
                    if($formSettingsNotificationOption == 'custom') {
                        $notificationHandlerDefault = '';
                        $notificationHandlerCustom = 'checked="checked"';
                        $notificationHandlerCustomActive = 'active';
                    }

                    $formsArray[] = '
                        <div id="' . $formId . '" class="cm-hipaa-select-form-item' . $errorClass . $formSelected . '">
                            <div class="cm-hipaa-select-form-item-name">
                                <span class="cm-hipaa-form-select">
                                    ' . $formSelectIcon . '
                                </span>
                                <span class="cm-hipaa-toggle-fields" title="Show Fields">
                                    <i class="material-icons" title="Toggle Fields">arrow_drop_down_circle</i>
                                </span>
                                <span class="cm-hipaa-select-form-info">
                                    ' . $errorIcon . ' ' . $formName . ' - Gravity Form ID: ' . $formId . '
                                </span>
                            </div>
                            <div class="cm-hipaa-select-form-item-fields">
                                <form class="cm-hipaa-select-form-form" data="' . $formId . '">
                                    <div class="cm-hipaa-select-form-field-wrapper">
                                        <h4>SIGNATURE FIELD</h4>
                                        <div class="cm-hipaa-select-form-field">
                                            <input type="checkbox" name="cm-hipaa-forms-show-signature" value="yes" ' . $showSigChecked . ' /> Show Signature
                                        </div>
                                    </div>
                                    <div class="cm-hipaa-select-form-field-wrapper">
                                        <h4>SUBMIT SUCCESS HANDLER</h4>
                                        <div class="cm-hipaa-select-form-field">
                                            <input type="radio" name="cm-hipaa-forms-success-handler" class="cm-hipaa-forms-success-message-radio" value="message" ' . $successHandlerMessage . ' /> Success Message
                                            <input type="radio" name="cm-hipaa-forms-success-handler" class="cm-hipaa-forms-success-redirect-radio" value="redirect" ' . $successHandlerRedirect . ' /> Success Redirect
                                            <input type="radio" name="cm-hipaa-forms-success-handler" class="cm-hipaa-forms-success-callback-radio" value="callback" ' . $successHandlerCallback . ' /> Success Callback
                                        </div>
                                        <div class="cm-hipaa-select-form-field">
                                            <div class="cm-hipaa-forms-success-handler-field cm-hipaa-forms-success-message-wrapper ' . $successHandlerMessageActive . '">
                                                <textarea name="cm-hipaa-forms-success-message" placeholder="Success Message">' . $formSettingsSuccessMessage . '</textarea>
                                            </div>
                                            <div class="cm-hipaa-forms-success-handler-field cm-hipaa-forms-success-redirect-wrapper ' . $successHandlerRedirectActive . '">
                                                <input type="text" name="cm-hipaa-forms-success-redirect" placeholder="Redirect URL" value="' . $formSettingsSuccessRedirect . '" />
                                            </div>
                                            <div class="cm-hipaa-forms-success-handler-field cm-hipaa-forms-success-callback-wrapper ' . $successHandlerCallbackActive . '">
                                                <div class="cm-hipaa-forms-experimental-notice">
                                                    * The callback function is experimental
                                                </div>
                                                <input type="text" name="cm-hipaa-forms-success-callback" placeholder="Callback Function (no parenthesis)" value="' . $formSettingsSuccessCallback . '" /><br />
                                            <input type="text" name="cm-hipaa-forms-success-callback-params" placeholder="Callback Params (ie. param1, param2)" value="' . $formSettingsSuccessCallbackParams . '" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cm-hipaa-select-form-field-wrapper">
                                        <h4>WHO CAN VIEW THIS FORM?</h4>
                                        <div class="cm-hipaa-select-form-field">
                                            <input type="radio" name="cm-hipaa-forms-users-handler" class="cm-hipaa-forms-users-all-radio" value="all" ' . $usersHandlerAll . ' /> All Admin/HIPAA Users
                                            <input type="radio" name="cm-hipaa-forms-users-handler" class="cm-hipaa-forms-users-specific-radio" value="specific" ' . $usersHandlerSpecific . ' /> Only Specific Users
                                            <input type="radio" name="cm-hipaa-forms-users-handler" class="cm-hipaa-forms-users-selected-radio" value="selected" ' . $usersHandlerSelected . ' /> Selected User
                                        </div>
                                        <div class="cm-hipaa-select-form-field cm-hipaa-forms-approved-users-wrapper ' . $usersHandlerSpecificActive . '">
                                            ' . implode('', $approvedUsersOptions) . '
                                        </div>
                                        <div class="cm-hipaa-select-form-field cm-hipaa-forms-selected-users-wrapper ' . $usersHandlerSelectedActive . '">
                                            <input type="text" name="cm-hipaa-forms-selected-user-slug" placeholder="Select Field Class (option value must be user id)" value="' . $formSettingsSelectedUserSlug . '" />
                                        </div>
                                    </div>
                                    <div class="cm-hipaa-select-form-field-wrapper">
                                        <h4>NOTIFICATION EMAIL</h4> <i class="material-icons cm-hipaa-setting-info" data-content="form-specific-notification-info" title="More Information">info</i>
                                        <div class="cm-hipaa-select-form-field">
                                            <input type="radio" name="cm-hipaa-forms-notification-handler" class="cm-hipaa-forms-notification-default-radio" value="default" ' . $notificationHandlerDefault . ' /> Default
                                            <input type="radio" name="cm-hipaa-forms-notification-handler" class="cm-hipaa-forms-notification-custom-radio" value="custom" ' . $notificationHandlerCustom . ' /> Custom
                                        </div>
                                        <div class="cm-hipaa-forms-custom-notification-wrapper ' . $notificationHandlerCustomActive . '">
                                            <div class="cm-hipaa-select-form-field cm_hipaa_grid_row_nogap">
                                                <div class="cm_hipaa_col_50">
                                                    <input type="text" name="cm-hipaa-selected-form-notification-from-name-input" placeholder="Name the notification email is from..." value="' . $formSettingsNotificationFromName . '" />
                                                </div>
                                                <div class="cm_hipaa_col_50">
                                                    <input type="text" name="cm-hipaa-selected-form-notification-from-email-input" placeholder="Email the notification is from..." value="' . $formSettingsNotificationFromEmail . '" />
                                                </div>
                                            </div>
                                            <div class="cm-hipaa-select-form-field">
                                                <input type="text" name="cm-hipaa-selected-form-notification-sendto-input" placeholder="Emails to send notifications to..." value="' . $formSettingsNotificationSendTo . '" />
                                            </div>
                                            <div class="cm-hipaa-select-form-field">
                                                <input type="text" name="cm-hipaa-selected-form-notification-subject-input" placeholder="Notification Email Subject..." value="' . $formSettingsNotificationSubject . '" />
                                            </div>
                                            <div class="cm-hipaa-select-form-field">
                                                <textarea name="cm-hipaa-selected-form-notification-message-input" placeholder="Notification Email...">' . $formSettingsNotificationMessage . '</textarea>
                                                <div class="cm-hipaa-selected-form-notification-notice">
                                                    * Using complex CSS may not render as expected in some email clients. We recommend keeping this in a simple table structure & limited basic CSS.  If you receive an invalid json format error when saving the settings it may be due to CSS statements in this field.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cm-form-settings-notice"></div>
                                    <div class="cm-button cm-hipaa-forms-form-settings-submit">SUBMIT</div>
                                    <div class="clearfix"></div>
                                </form>
                            </div>
                            <div class="cm-hipaa-select-form-item-errors">
                                ' . $errorHeading . '
                                ' . $errors . '
                            </div>
                        </div>
                    ';
                }
            }

            return implode('', $formsArray);
        }

        return false;
    }

    /*** GENERATE PDF ***/
    public function generatePdf($formId, $pdfPassword) {
        $licenseKey = esc_attr(get_option('license_key'));
        $timeZone = esc_attr(get_option('time_zone'));
        $customCss = esc_attr(get_option('hipaa_form_css'));

        // GET USER DATA
        $user = wp_get_current_user();
        $myId = $user->ID;
        $user_roles = $user->roles;
        $role = '';
        $myDisplayName = $user->display_name;
        $myFirstName = $user->first_name;
        $myLastName = $user->last_name;
        $myEmail = $user->user_email;

        // SET MY NAME
        $myName = '';
        if($myFirstName || $myLastName) {
            $myName = $myFirstName . ' ' . $myLastName;
        } else if($myDisplayName) {
            $myName = $myDisplayName;
        } else {
            $myName = $myEmail;
        }

        /// SET ROLE
        if(in_array('administrator', $user_roles)) {
            $role = 'administrator';
        } else if(in_array('hipaa_forms', $user_roles)) {
            $role = 'hipaa';
        }

        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'getpdf',
            'license_key' => $licenseKey,
            'domain' => self::getRootDomain(),
            'form_id' => $formId,
            'time_zone' => $timeZone,
            'pdf_password' => $pdfPassword,
            'custom_css' => urlencode($customCss),
            'user_id' => $myId,
            'user_role' => $role,
            'user_name' => $myName,
            'email' => $myEmail
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        if($output) {
            foreach($output as $data) {
                $error = '';
                if(isset($data->error)) {
                    $error = $data->error;
                }

                if ($error) {
                    $results = array(
                        'error' => $error
                    );

                    return json_encode($results);
                } else {
                    $success = $data->success;

                    if($success == 'success') {
                        $pdf_url = $data->pdf_url;
                        $pdf_password = $data->pdf_password;

                        $modal_message = '
                            <div class="cm-hipaa-forms-generate-pdf-success">
                                <div class="cm-hipaa-forms-generate-pdf-success">
                                    <p>
                                        The PDF has been generated with encryption and password protection.
                                    </p>
                                    <p>
                                        Click the link below to open the PDF and enter your password (' . $pdf_password . ').
                                    </p>
                                    <p>
                                        After entering the password you will be able to view, print and download the form.
                                    </p>
                                    <p>
                                        DO NOT LOSE YOUR PASSWORD!
                                    </p>
                                    <p>
                                        You will not be able to open the PDF without the password even after it is downloaded.
                                    </p>
                                </div>
                                <a class="cm-button" href="' . $pdf_url . '" target="_blank"><i class="material-icons">picture_as_pdf</i> OPEN PDF</a>
                            </div>
                        ';

                        $form_pdf = '
                            <div class="cm-hipaa-submitted-form-pdf-url">
                                <a href="' . $pdf_url . '" target="_blank"><i class="material-icons">picture_as_pdf</i> PDF</a>
                            </div>
                        ';

                        $form_password = '
                            <div class="cm-hipaa-submitted-form-pdf-password">
                                <i class="material-icons">vpn_key</i> ' . $pdf_password . '
                            </div>
                        ';

                        $results = array(
                            'pdf_url' => $pdf_url,
                            'pdf_password' => $pdf_password,
                            'form_pdf' => $form_pdf,
                            'form_password' => $form_password,
                            'modal_message' => $modal_message
                        );

                        return json_encode($results);
                    } else {
                        $results = array(
                            'error' => 'API did not return a success message: ' . $success
                        );

                        return json_encode($results);
                    }
                }
            }
        } else {
            $results = array(
                'error' => 'There was an error in the api'
            );

            return json_encode($results);
        }

        return false;
    }

    /*** DELETE PDF ***/
    public function deletePdf($url) {
        $licenseKey = esc_attr(get_option('license_key'));

        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'deletepdf',
            'license_key' => $licenseKey,
            'domain' => self::getRootDomain(),
            'pdf_url' => $url
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        if ($output) {
            foreach ($output as $data) {
                $error = '';
                if(isset($data->error)) {
                    $error = $data->error;
                }

                if ($error) {
                    $results = array(
                        'error' => $error
                    );
                } else {
                    $success = $data->success;

                    if($success == 'success') {
                        $results = array(
                            'content' => 'PDF Deleted'
                        );
                    }
                }
            }
        } else {
            $results = array(
                'content' => 'No response from API'
            );
        }

        return json_encode($results);
    }

    /*** ARCHIVE FORM ***/
    public function archiveForm($licenseKey, $formId) {
        // GET USER DATA
        $user = wp_get_current_user();
        $myId = $user->ID;
        $user_roles = $user->roles;
        $role = '';
        $myDisplayName = $user->display_name;
        $myFirstName = $user->first_name;
        $myLastName = $user->last_name;
        $myEmail = $user->user_email;

        // SET MY NAME
        $myName = '';
        if($myFirstName || $myLastName) {
            $myName = $myFirstName . ' ' . $myLastName;
        } else if($myDisplayName) {
            $myName = $myDisplayName;
        } else {
            $myName = $myEmail;
        }

        /// SET ROLE
        if(in_array('administrator', $user_roles)) {
            $role = 'administrator';
        } else if(in_array('hipaa_forms', $user_roles)) {
            $role = 'hipaa';
        }

        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'archive',
            'license_key' => $licenseKey,
            'domain' => self::getRootDomain(),
            'form_id' => $formId,
            'user_id' => $myId,
            'user_role' => $role,
            'user_name' => $myName,
            'email' => $myEmail
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        foreach($output as $data) {
            $error = '';
            if(isset($data->error)) {
                $error = $data->error;
            }

            if($error) {
                $results = array(
                    'error' => $error
                );
            } else {
                $results = array(
                    'success' => $data->success
                );
            }
        }

        return json_encode($results);
    }

    /*** RESTORE ARCHIVED FORM ***/
    public function restoreForm($licenseKey, $formId) {
        // GET USER DATA
        $user = wp_get_current_user();
        $myId = $user->ID;
        $user_roles = $user->roles;
        $role = '';
        $myDisplayName = $user->display_name;
        $myFirstName = $user->first_name;
        $myLastName = $user->last_name;
        $myEmail = $user->user_email;

        // SET MY NAME
        $myName = '';
        if($myFirstName || $myLastName) {
            $myName = $myFirstName . ' ' . $myLastName;
        } else if($myDisplayName) {
            $myName = $myDisplayName;
        } else {
            $myName = $myEmail;
        }

        /// SET ROLE
        if(in_array('administrator', $user_roles)) {
            $role = 'administrator';
        } else if(in_array('hipaa_forms', $user_roles)) {
            $role = 'hipaa';
        }

        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'restore',
            'license_key' => $licenseKey,
            'domain' => self::getRootDomain(),
            'form_id' => $formId,
            'user_id' => $myId,
            'user_role' => $role,
            'user_name' => $myName,
            'email' => $myEmail
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        foreach($output as $data) {
            $error = '';
            if(isset($data->error)) {
                $error = $data->error;
            }

            if($error) {
                $results = array(
                    'error' => $error
                );
            } else {
                $results = array(
                    'success' => $data->success
                );
            }
        }

        return json_encode($results);
    }

    /*** DESTROY FORM ***/
    public function destroyForm($licenseKey, $formId) {
        // GET USER DATA
        $user = wp_get_current_user();
        $myId = $user->ID;
        $user_roles = $user->roles;
        $role = '';
        $myDisplayName = $user->display_name;
        $myFirstName = $user->first_name;
        $myLastName = $user->last_name;
        $myEmail = $user->user_email;

        // SET MY NAME
        $myName = '';
        if($myFirstName || $myLastName) {
            $myName = $myFirstName . ' ' . $myLastName;
        } else if($myDisplayName) {
            $myName = $myDisplayName;
        } else {
            $myName = $myEmail;
        }

        /// SET ROLE
        if(in_array('administrator', $user_roles)) {
            $role = 'administrator';
        } else if(in_array('hipaa_forms', $user_roles)) {
            $role = 'hipaa';
        }

        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'destroy',
            'license_key' => $licenseKey,
            'domain' => self::getRootDomain(),
            'form_id' => $formId,
            'user_id' => $myId,
            'user_role' => $role,
            'user_name' => $myName,
            'email' => $myEmail
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        foreach($output as $data) {
            $error = '';
            if(isset($data->error)) {
                $error = $data->error;
            }

            if($error) {
                $results = array(
                    'error' => $error
                );
            } else {
                $results = array(
                    'success' => $data->success
                );
            }
        }

        return json_encode($results);
    }

    /*** PRINT FORM ***/
    public function printForm($licenseKey, $formId) {
        // GET USER DATA
        $user = wp_get_current_user();
        $myId = $user->ID;
        $user_roles = $user->roles;
        $role = '';
        $myDisplayName = $user->display_name;
        $myFirstName = $user->first_name;
        $myLastName = $user->last_name;
        $myEmail = $user->user_email;

        // SET MY NAME
        $myName = '';
        if($myFirstName || $myLastName) {
            $myName = $myFirstName . ' ' . $myLastName;
        } else if($myDisplayName) {
            $myName = $myDisplayName;
        } else {
            $myName = $myEmail;
        }

        /// SET ROLE
        if(in_array('administrator', $user_roles)) {
            $role = 'administrator';
        } else if(in_array('hipaa_forms', $user_roles)) {
            $role = 'hipaa';
        }

        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'print',
            'license_key' => $licenseKey,
            'domain' => self::getRootDomain(),
            'form_id' => $formId,
            'user_id' => $myId,
            'user_role' => $role,
            'user_name' => $myName,
            'email' => $myEmail
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        foreach($output as $data) {
            $error = '';
            if(isset($data->error)) {
                $error = $data->error;
            }

            if($error) {
                $results = array(
                    'error' => $error
                );
            } else {
                $results = array(
                    'success' => $data->success
                );
            }
        }

        return json_encode($results);
    }

    /*** ACCESS LOG ***/
    public function accessLog($userId, $userName, $firstName, $lastName, $email, $userRole, $status) {
        $licenseKey = esc_attr(get_option('license_key'));
        $userRole = implode(',', $userRole);

        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'log',
            'license_key' => $licenseKey,
            'domain' => self::getRootDomain(),
            'user_id' => $userId,
            'user_name' => $userName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'user_role' => $userRole,
            'status' => $status
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        if($output) {
            foreach($output as $data) {
                if(isset($data->error)) {
                    return $data->error;
                }
            }
        }

        return false;
    }

    /*** GET ACCESS LOG ***/
    public function getAccessLogs($startDate, $endDate, $limit, $page) {
        $licenseKey = esc_attr(get_option('license_key'));
        $timeZone = esc_attr(get_option('time_zone'));

        if($timeZone == 'alaska') {
            $tz = 'America/Anchorage';
        } else if($timeZone == 'central') {
            $tz = 'America/Chicago';
        } else if($timeZone == 'eastern') {
            $tz = 'America/New_York';
        } else if($timeZone == 'hawaii') {
            $tz = 'America/Adak';
        } else if($timeZone == 'hawaii_no_dst') {
            $tz = 'Pacific/Honolulu';
        } else if($timeZone == 'mountain') {
            $tz = 'America/Denver';
        } else if($timeZone == 'mountain_no_dst') {
            $tz = 'America/Phoenix';
        } else if($timeZone == 'pacific') {
            $tz = 'America/Los_Angeles';
        } else {
            $tz = 'America/Chicago';
        }

        // SET OFFSET
        $offset = $page * $limit;

        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'getlogs',
            'license_key' => $licenseKey,
            'domain' => self::getRootDomain(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'limit' => $limit,
            'offset' => $offset
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        foreach($output as $data) {
            $error = '';
            if(isset($data->error)) {
                $error = $data->error;
            }

            if($error) {
                $results = array(
                    'error' => $error,
                    'content' => $error
                );
            } else {
                $success = $data->success;
                $totalResults = $data->total_results;

                if ($success == 'success') {
                    $logsData = $data->logs;

                    $logs = array();
                    foreach ($logsData as $logData) {
                        $logUserId = $logData->user_id;
                        $logUserName = $logData->username;
                        $logFirstName = $logData->first_name;
                        $logLastName = $logData->last_name;
                        $logEmail = $logData->email;
                        $logUserRoles = $logData->user_role;
                        $logStatus = $logData->status;
                        $domain = $logData->domain;
                        $usersTimezone = new DateTimeZone($tz);
                        $dtObj = new DateTime($logData->timestamp);
                        $dtObj->setTimeZone($usersTimezone);
                        $logDate = $dtObj->format('m-d-Y g:i a');
                        $tzAbbr = $dtObj->format('T');

                        if($logStatus == 1) {
                            $logStatus = 'Allowed';
                        } else if($logStatus == 0) {
                            $logStatus = 'Denied';
                        }

                        $logs[] = '
                            <tr class="cm-hipaa-forms-log">
                                <td>
                                    ' . $logUserId . '
                                </td>
                                <td>
                                    ' . $logUserName . '
                                </td>
                                <td>
                                    ' . $logFirstName . ' ' . $logLastName . '
                                </td>
                                <td>
                                    ' . $logEmail . '
                                </td>
                                <td>
                                    ' . $logUserRoles . '
                                </td>
                                <td>
                                    ' . $logStatus . '
                                </td>
                                <td>
                                    ' . $domain . '
                                </td>
                                <td>
                                    ' . $logDate . ' ' . $tzAbbr . '
                                </td>
                            </tr>
                        ';
                    }

                    $content = '
                            <table class="cm-hipaa-forms-logs">
                                <thead class="cm-table-header">
                                    <td>
                                        USER ID
                                    </td>
                                    <td>
                                        USERNAME
                                    </td>
                                    <td>
                                        NAME
                                    </td>
                                    <td>
                                        EMAIL
                                    </td>
                                    <td>
                                        USER ROLE
                                    </td>
                                    <td>
                                        STATUS
                                    </td>
                                    <td>
                                        DOMAIN
                                    </td>
                                    <td>
                                        DATE/TIME
                                    </td>
                                </thead>
                                ' . implode('', $logs) . '
                            </table>
                        ';

                    $results = array(
                        'success' => 'success',
                        'content' => $content,
                        'total_results' => $totalResults
                    );
                } else {
                    $results = array(
                        'error' => 'API did not return a success message',
                        'content' => 'API did not return a success message'
                    );
                }
            }
        }

        return json_encode($results);
    }

    /*** GET BAA FORM ***/
    public function getBaaForm() {
        $licenseKey = esc_attr(get_option('license_key'));

        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'getbaaform',
            'license_key' => $licenseKey,
            'domain' => self::getRootDomain()
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        foreach($output as $data) {
            $error = '';
            if(isset($data->error)) {
                $error = $data->error;
            }

            if ($error) {
                $results = array(
                    'error' => $error
                );
            } else {
                $success = $data->success;

                if($success == 'success') {
                    if($data->form == 'No BAA') {
                        $results = array(
                            'success' => $data->success,
                            'form' => '<div class="cm-sign-baa-button">You must sign the BAA Agreement!</div><div class="cm-sign-baa-notice">If you would rather use your own BAA please email it to contact@codemonkeysllc.com or call us at 715.941.1040</div>'
                        );
                    } else {
                        $results = array(
                            'success' => $data->success,
                            'form' => $data->form
                        );
                    }
                } else {
                    $results = array(
                        'error' => $error
                    );
                }
            }
        }

        return json_encode($results);
    }

    /*** SUBMIT BAA FORM ***/
    public function submitBaaForm($form, $signature, $signersName, $companyName) {
        $licenseKey = esc_attr(get_option('license_key'));
        $timezone = esc_attr(get_option('time_zone'));

        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'submitbaaform',
            'license_key' => $licenseKey,
            'domain' => self::getRootDomain(),
            'timezone' => $timezone,
            'form' => $form,
            'signature' => $signature,
            'signers_name' => $signersName,
            'company_name' => $companyName
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        foreach($output as $data) {
            $error = '';
            if(isset($data->error)) {
                $error = $data->error;
            }

            if ($error) {
                $results = array(
                    'error' => $error
                );
            } else {
                $success = $data->success;

                if($success == 'success') {
                    $results = array(
                        'success' => 'success',
                        'form' => $data->form
                    );
                } else {
                    $results = array(
                        'error' => $error
                    );
                }
            }
        }

        return json_encode($results);
    }

    /*** GET BAA PDF FORM URL ***/
    public function getBaaPdf() {
        $licenseKey = esc_attr(get_option('license_key'));

        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'getbaapdf',
            'license_key' => $licenseKey,
            'domain' => self::getRootDomain()
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        foreach($output as $data) {
            $error = '';
            if(isset($data->error)) {
                $error = $data->error;
            }

            if ($error) {
                if ($error == 'No BAA') {
                    $results = array(
                        'error' => $error,
                        'content' => '<div class="cm-sign-baa-button">You must sign the BAA Agreement!</div><div class="cm-sign-baa-notice">If you would rather use your own BAA please email it to contact@codemonkeysllc.com or call us at 715.941.1040</div>'
                    );
                } else {
                    $results = array(
                        'error' => $error,
                        'content' => $error
                    );
                }
            } else {
                $success = $data->success;

                if($success == 'success') {
                    $results = array(
                        'success' => 'success',
                        'content' => $data->form
                    );
                } else {
                    $results = array(
                        'error' => $error,
                        'content' => $error
                    );
                }
            }
        }

        return json_encode($results);
    }

    /*** GET SUPPORT TICKETS ***/
    public function getSupportTickets($status) {
        $licenseKey = esc_attr(get_option('license_key'));
        $domain = self::getRootDomain();

        // GET ACCOUNT DATA
        $account = self::getAccount($licenseKey, $domain);
        $accountId = '';
        if(isset($account->id)) {
            $accountId = $account->id;
        }

        // Create curl resource
        $curl_url = 'https://stagingserver.online/codemonkeysdev/support-api/';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'key' => self::getTicketsKey(),
            'action' => 'get',
            'account_id' => $accountId,
            'license_key' => $licenseKey,
            'domain' => $domain,
            'status' => $status
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        if($output) {
            foreach ($output as $data) {
                $error = '';
                if(isset($data->error)) {
                    $error = $data->error;
                }

                if ($error) {
                    $results = array(
                        'error' => $error
                    );
                } else {
                    $success = $data->success;

                    if($success == 'success') {
                        if($data->tickets) {
                            $timeZone = esc_attr(get_option('time_zone'));
                            $tz = '';

                            if($timeZone == 'alaska') {
                                $tz = 'America/Anchorage';
                            } else if($timeZone == 'central') {
                                $tz = 'America/Chicago';
                            } else if($timeZone == 'eastern') {
                                $tz = 'America/New_York';
                            } else if($timeZone == 'hawaii') {
                                $tz = 'America/Adak';
                            } else if($timeZone == 'hawaii_no_dst') {
                                $tz = 'Pacific/Honolulu';
                            } else if($timeZone == 'mountain') {
                                $tz = 'America/Denver';
                            } else if($timeZone == 'mountain_no_dst') {
                                $tz = 'America/Phoenix';
                            } else if($timeZone == 'pacific') {
                                $tz = 'America/Los_Angeles';
                            } else {
                                $tz = 'America/Chicago';
                            }

                            $ticketsArr = array();
                            foreach ($data->tickets as $ticket) {
                                if(!$ticket->parent_id) {
                                    // SET DATE/TIME BASED ON USER'S TIMEZONE
                                    $usersTimezone = '';
                                    if($tz) {
                                        $usersTimezone = new DateTimeZone($tz);
                                    }
                                    $dtObj = new DateTime($ticket->timestamp);
                                    if($tz) {
                                        $dtObj->setTimeZone($usersTimezone);
                                    }
                                    $ticketDate = $dtObj->format('M jS Y g:i a');
                                    $tzAbbr = $dtObj->format('T');

                                    // SET PRIORITY
                                    if ($ticket->priority == 'high') {
                                        $priorityClass = 'cm-support-ticket-priority-high';
                                        $priorityTitle = 'High Priority';
                                    } else {
                                        if ($ticket->priority == 'medium') {
                                            $priorityClass = 'cm-support-ticket-priority-medium';
                                            $priorityTitle = 'Medium Priority';
                                        } else {
                                            if ($ticket->priority == 'low') {
                                                $priorityClass = 'cm-support-ticket-priority-low';
                                                $priorityTitle = 'Low Priority';
                                            }
                                        }
                                    }

                                    // GET REPLIES TO THIS TICKET
                                    $replies = array();
                                    $repliesCount = 0;
                                    $lastReply = '';
                                    if($ticket->replies_count) {
                                        $repliesCount = $ticket->replies_count;
                                        $lastReply = '';

                                        foreach ($ticket->replies as $reply) {
                                            // SET DATE/TIME BASED ON USER'S TIMEZONE
                                            $replyTimezone = new DateTimeZone($tz);
                                            $replyDtObj = new DateTime($reply->timestamp);
                                            $replyDtObj->setTimeZone($replyTimezone);
                                            $replyDate = $replyDtObj->format('M jS Y g:i a');
                                            $replyTzAbbr = $replyDtObj->format('T');

                                            if ($reply->parent_id == $ticket->id) {
                                                $replies[] = '
                                                <div class="cm-support-ticket-reply">
                                                    <div class="cm-support-ticket-reply-datetime">
                                                        ' . $replyDate . ' ' . $replyTzAbbr . '
                                                    </div>
                                                    <div class="cm-support-ticket-reply-subject">
                                                        ' . stripslashes($reply->subject) . '
                                                    </div>
                                                    <div class="cm-support-ticket-reply-message">
                                                        ' . stripslashes($reply->message) . '
                                                    </div>
                                                    <div class="cm-support-ticket-reply-submitter">
                                                        Reply from: ' . $reply->first_name . ' ' . $reply->last_name . '
                                                    </div>
                                                </div>
                                            ';
                                            }
                                        }
                                    }

                                    // SET SUBMIT AND CLOSE BUTTONS BASED ON STATUS OF TICKET
                                    $closeTicketButton = '';
                                    if($ticket->status !== 'closed') {
                                        $closeTicketButton = '
                                            <div class="cm-support-ticket-close cm-button" data="' . $ticket->id . '">
                                                CLOSE TICKET
                                            </div>
                                        ';

                                        $submitButton = '
                                            <div class="cm-support-ticket-reply-submit cm-button" data="' . $ticket->id . '">
                                                SUBMIT
                                            </div>
                                        ';
                                    } else if($ticket->status == 'closed') {
                                        $submitButton = '
                                            <div class="cm-support-ticket-reply-submit cm-button" data="' . $ticket->id . '">
                                                REOPEN TICKET
                                            </div>
                                        ';
                                    }

                                    // BUILD THE TICKET
                                    $ticketsArr[] = '
                                        <div class="cm-support-ticket-wrapper">
                                            <div class="cm-support-ticket">
                                                <div class="cm-support-ticket-heading">
                                                    <div class="cm-support-ticket-datetime">
                                                        <span class="' . $priorityClass . '" title="' . $priorityTitle . '"><i class="material-icons">assistant_photo</i></span> ' . $ticketDate . ' ' . $tzAbbr . ' <span class="cm-support-ticket-toggle" title="View Ticket"><i class="material-icons">arrow_drop_down</i></span><span style="clear:both;"></span>
                                                    </div>
                                                    <div class="cm-support-ticket-subject">
                                                        ' . stripslashes($ticket->subject) . '
                                                    </div>
                                                    <div class="cm-support-ticket-submitter">
                                                        Submitted by: ' . $ticket->first_name . ' ' . $ticket->last_name . '
                                                    </div>
                                                    <div class="cm-support-ticket-reply-count">
                                                        ' . count($replies) . ' Replies
                                                    </div>
                                                </div>
                                                <div class="cm-support-ticket-body">
                                                    <div class="cm-support-ticket-message">
                                                        ' . stripslashes($ticket->message) . '
                                                    </div>
                                                    <div class="cm-support-ticket-replies-wrapper">
                                                        ' . implode('', $replies) . '
                                                    </div>
                                                    <div class="cm-support-ticket-reply-wrapper">
                                                        <div class="cm-support-ticket-reply-input-wrapper">
                                                            <textarea class="cm-support-ticket-reply-input" placeholder="Reply..."></textarea>
                                                        </div>
                                                        <div class="cm-support-ticket-reply-buttons-wrapper">
                                                            ' . $submitButton . '
                                                            ' . $closeTicketButton . '
                                                            <div class="clearfix"></div>
                                                            <div class="cm-support-ticket-reply-notice"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    ';
                                }
                            }

                            $tickets = '
                                <div class="cm-tickets-wrapper">
                                    <div class="cm-support-tickets">
                                        ' . implode('', $ticketsArr) . '
                                    </div>
                                </div>
                            ';

                            $results = array(
                                'success' => 'success',
                                'tickets' => $tickets
                            );
                        } else {
                            $results = array(
                                'success' => 'success',
                                'tickets' => 'No open tickets'
                            );
                        }
                    } else {
                        $results = array(
                            'error' => 'Support API did not return a success message.'
                        );
                    }
                }
            }
        }

        return json_encode($results);
    }

    /*** SUBMIT SUPPORT TICKET ***/
    public function submitSupportTicket($priority, $channel, $subject, $message, $parent_id) {
        $licenseKey = esc_attr(get_option('license_key'));
        $domain = self::getRootDomain();
        $product = 'hipaa_wp';

        // GET USER ROLES AND SET APPROVED/NOT APPROVED
        $user = wp_get_current_user();

        $user_id = $user->ID;
        $user_name = $user->user_login;
        $user_display_name = $user->display_name;
        $name_pieces = explode(" ", $user_display_name);
        $user_first_name = $name_pieces[0];
        $user_last_name = $name_pieces[1];
        $user_email = $user->user_email;
        $user_roles = $user->roles;

        // GET ACCOUNT DATA
        $account = self::getAccount($licenseKey, $domain);

        // Create curl resource
        $curl_url = 'https://stagingserver.online/codemonkeysdev/support-api/';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'key' => self::getTicketsKey(),
            'action' => 'post',
            'parent_id' => $parent_id,
            'license_key' => $licenseKey,
            'account_id' => $account->id,
            'customer_id' => $user_id,
            'partner_id' => $account->partner_id,
            'domain' => $domain,
            'url' => get_site_url(),
            'first_name' => $user_first_name,
            'last_name' => $user_last_name,
            'email' => $user_email,
            'phone' => $account->phone,
            'products' => $product,
            'type' => 'product',
            'priority' => $priority,
            'channel' => $channel,
            'subject' => stripslashes($subject),
            'message' => stripslashes($message),
            'status' => 'open'
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        if($output) {
            foreach ($output as $data) {
                $error = '';
                if(isset($data->error)) {
                    $error = $data->error;
                }

                if ($error) {
                    $results = array(
                        'error' => $error
                    );
                } else {
                    $success = $data->success;

                    if ($success == 'success') {
                        $results = array(
                            'success' => $data->success
                        );
                    } else {
                        $results = array(
                            'error' => $error
                        );
                    }
                }
            }
        } else {
            $results = array(
                'error' => 'API Did Not Respond'
            );
        }

        return json_encode($results);
    }

    /*** CLOSE SUPPORT TICKET ***/
    public function closeSupportTicket($ticket_id) {
        // Create curl resource
        $curl_url = 'https://stagingserver.online/codemonkeysdev/support-api/';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'key' => self::getTicketsKey(),
            'action' => 'closeticket',
            'ticket_id' => $ticket_id
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        if($output) {
            foreach ($output as $data) {
                $error = '';
                if(isset($data->error)) {
                    $error = $data->error;
                }

                if ($error) {
                    return $error;
                } else {
                    $success = $data->success;

                    if ($success == 'success') {
                        return $success;
                    } else {
                        return $error;
                    }
                }
            }
        }

        return false;
    }

    /*** GET LOCATIONS ***/
    public function getLocations() {
        $licenseKey = esc_attr(get_option('license_key'));

        // GET USER ID
        $user = wp_get_current_user();
        $myId = $user->ID;
        $user_roles = $user->roles;
        $role = '';

        /// SET ADMINISTRATOR
        if(in_array('administrator', $user_roles)) {
            $role = 'administrator';
        } else if(in_array('hipaa_forms', $user_roles)) {
            $role = 'hipaa';
        }

        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'getlocations',
            'license_key' => $licenseKey,
            'domain' => self::getRootDomain(),
            'user_id' => $myId,
            'user_role' => json_decode($role)
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        if ($output) {
            foreach ($output as $data) {
                $error = '';
                if(isset($data->error)) {
                    $error = $data->error;
                }

                if ($error) {
                    if ($error == 'No BAA') {
                        $results = array(
                            'error' => $error,
                            'content' => ''
                        );
                    } else {
                        $results = array(
                            'error' => $error,
                            'content' => ''
                        );
                    }
                } else {
                    $success = $data->success;

                    if($success == 'success') {
                        $results = array(
                            'success' => 'success',
                            'content' => $data->locations
                        );
                    } else {
                        $results = array(
                            'error' => $error,
                            'content' => ''
                        );
                    }
                }
            }
        } else {
            $results = array(
                'error' => 'API did not return a success message',
                'content' => ''
            );
        }

        return json_encode($results);
    }

    /*** GET SUBMITTED FORM NAMES FROM API ***/
    public function getFormNames() {
        $licenseKey = esc_attr(get_option('license_key'));

        // GET USER ID
        $user = wp_get_current_user();
        $myId = $user->ID;
        $user_roles = $user->roles;
        $role = '';

        /// SET ADMINISTRATOR
        if(in_array('administrator', $user_roles)) {
            $role = 'administrator';
        } else if(in_array('hipaa_forms', $user_roles)) {
            $role = 'hipaa';
        }

        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'getformnames',
            'license_key' => $licenseKey,
            'domain' => self::getRootDomain(),
            'user_id' => $myId,
            'user_role' => $role
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        if ($output) {
            foreach ($output as $data) {
                $error = '';
                if(isset($data->error)) {
                    $error = $data->error;
                }

                if ($error) {
                    if ($error == 'No BAA') {
                        $results = array(
                            'error' => $error,
                            'content' => ''
                        );
                    } else {
                        $results = array(
                            'error' => $error,
                            'content' => ''
                        );
                    }
                } else {
                    $success = $data->success;

                    if($success == 'success') {
                        $results = array(
                            'success' => 'success',
                            'content' => $data->form_names
                        );
                    } else {
                        $results = array(
                            'error' => $error,
                            'content' => ''
                        );
                    }
                }
            }
        } else {
            $results = array(
                'error' => 'API did not return a success message',
                'content' => ''
            );
        }

        return json_encode($results);
    }

    /*** VALIDATE ACCOUNT ***/
    public function validateAccount() {
        $licenseKey = esc_attr(get_option('license_key'));

        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'getaccount',
            'license_key' => $licenseKey,
            'domain' => self::getRootDomain()
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        foreach($output as $data) {
            $error = '';
            if(isset($data->error)) {
                $error = $data->error;
            }

            if ($error) {
                $results = array(
                    'error' => $error
                );
            } else {
                $success = $data->success;

                // CHECK IF ON STAGING DOMAIN
                $stagingMessage = '';
                if($data->staging == 'yes') {
                    $stagingMessage = '
                                <div class="cm-hipaa-forms-staging-message">
                                    ATTENTION!: You are currently on a staging server for testing purposes only.  Actual protected health information should not be passed from this domain.
                                </div>
                            ';
                }

                if($success == 'success') {
                    $results = array(
                        'success' => $data->success,
                        'staging' => $stagingMessage,
                        'product' => $data->product,
                        'add_ons' => $data->add_ons,
                        'license_status' => $data->license_status,
                        'license_status_message' => $data->license_status_message,
                        'days_to_disable' => $data->days_to_disable,
                        'this_months_submissions' => $data->this_months_submissions
                    );
                } else {
                    $results = array(
                        'error' => $error
                    );
                }
            }
        }

        return json_encode($results);
    }

    /*** SUBMIT FORM NOTE ***/
    public function submitNote($formId, $userId, $name, $email, $note) {
        $licenseKey = esc_attr(get_option('license_key'));

        // SET ROLE (Why are we breaking convention and getting user data from client side instead of here?)
        $user = wp_get_current_user();
        $user_roles = $user->roles;
        $role = '';
        if(in_array('administrator', $user_roles)) {
            $role = 'administrator';
        } else if(in_array('hipaa_forms', $user_roles)) {
            $role = 'hipaa';
        }

        // ENCRYPT THE NOTE
        $key = self::keygen();
        $iv = self::ivgen();
        $note = self::encrypt($note, $key, $iv);

        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'postnote',
            'license_key' => $licenseKey,
            'domain' => self::getRootDomain(),
            'form_id' => $formId,
            'user_id' => $userId,
            'user_role' => $role,
            'user_name' => $name,
            'email' => $email,
            'note' => $note,
            'key' => $key,
            'iv' => $iv
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        if ($output) {
            foreach ($output as $data) {
                $error = '';
                if(isset($data->error)) {
                    $error = $data->error;
                }

                if ($error) {
                    if ($error == 'No BAA') {
                        $results = array(
                            'error' => $error
                        );
                    } else {
                        $results = array(
                            'error' => $error
                        );
                    }
                } else {
                    $success = $data->success;

                    if($success == 'success') {
                        $results = array(
                            'success' => 'success'
                        );
                    } else {
                        $results = array(
                            'error' => $error
                        );
                    }
                }
            }
        } else {
            $results = array(
                'error' => 'API did not return a success message'
            );
        }

        return json_encode($results);
    }

    /*** GET FORM NOTES ***/
    public function getFormNotes($formId) {
        $licenseKey = esc_attr(get_option('license_key'));

        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'getformnotes',
            'license_key' => $licenseKey,
            'domain' => self::getRootDomain(),
            'form_id' => $formId
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        if ($output) {
            foreach ($output as $data) {
                $error = '';
                if(isset($data->error)) {
                    $error = $data->error;
                }

                if ($error) {
                    if ($error == 'No BAA') {
                        $results = array(
                            'error' => $error
                        );
                    } else {
                        $results = array(
                            'error' => $error
                        );
                    }
                } else {
                    $success = $data->success;

                    if($success == 'success') {
                        $formNotes = $data->notes;

                        $timeZone = esc_attr(get_option('time_zone'));
                        $tz = '';

                        if($timeZone == 'alaska') {
                            $tz = 'America/Anchorage';
                        } else if($timeZone == 'central') {
                            $tz = 'America/Chicago';
                        } else if($timeZone == 'eastern') {
                            $tz = 'America/New_York';
                        } else if($timeZone == 'hawaii') {
                            $tz = 'America/Adak';
                        } else if($timeZone == 'hawaii_no_dst') {
                            $tz = 'Pacific/Honolulu';
                        } else if($timeZone == 'mountain') {
                            $tz = 'America/Denver';
                        } else if($timeZone == 'mountain_no_dst') {
                            $tz = 'America/Phoenix';
                        } else if($timeZone == 'pacific') {
                            $tz = 'America/Los_Angeles';
                        } else {
                            $tz = 'America/Chicago';
                        }

                        // SET DATE/TIME BASED ON USER'S TIMEZONE
                        $usersTimezone = '';
                        if($tz) {
                            $usersTimezone = new DateTimeZone($tz);
                        }

                        // DECRYPT FORM NOTES
                        $decryptedNotes = array();
                        foreach($formNotes as $formNote) {
                            $note = $formNote->note;
                            $noteKey = $formNote->key;
                            $noteIv = $formNote->iv;
                            $decryptedNote = self::decrypt($note, $noteKey, $noteIv);

                            $dtObj = new DateTime($formNote->timestamp);
                            if($tz) {
                                $dtObj->setTimeZone($usersTimezone);
                            }
                            $noteDate = $dtObj->format('M jS Y g:i a');
                            $tzAbbr = $dtObj->format('T');

                            $decryptedNotes[] = '
                                <div class="cm-hipaa-submitted-form-note-wrapper">
                                    <div class="cm-hipaa-submitted-form-note-date">
                                        ' . $noteDate . ' ' . $tzAbbr . '
                                    </div>
                                    <div class="cm-hipaa-submitted-form-note-name">
                                        ' . $formNote->name . '
                                    </div>
                                    <div class="cm-hipaa-submitted-form-note">
                                        ' . $decryptedNote . '
                                    </div>
                                </div>
                            ';
                        }

                        $results = array(
                            'success' => $success,
                            'notes' => implode('', $decryptedNotes),
                            'form_notes' => $formNotes
                        );
                    } else {
                        $results = array(
                            'error' => $error
                        );
                    }
                }
            }
        } else {
            $results = array(
                'error' => 'API did not return a success message'
            );
        }

        return json_encode($results);
    }

    /*** GET FORM HISTORY ***/
    public function getFormHistory($formId, $page, $limit) {
        // GET LICENCE KEY
        $licenseKey = esc_attr(get_option('license_key'));

        // SET OFFSET
        if(!$page) {
            $page = 0;
        }
        if(!$limit) {
            $limit = 10;
        }
        $offset = $page * $limit;

        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'getformhistory',
            'license_key' => $licenseKey,
            'domain' => self::getRootDomain(),
            'form_id' => $formId,
            'limit' => $limit,
            'offset' => $offset
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        if ($output) {
            foreach ($output as $data) {
                $error = '';
                if(isset($data->error)) {
                    $error = $data->error;
                }

                if ($error) {
                    if ($error == 'No BAA') {
                        $results = array(
                            'error' => $error
                        );
                    } else {
                        $results = array(
                            'error' => $error
                        );
                    }
                } else {
                    $success = $data->success;

                    if($success == 'success') {
                        $formHistories = $data->history;
                        $totalResults = $data->total_results;

                        $timeZone = esc_attr(get_option('time_zone'));
                        $tz = '';

                        if($timeZone == 'alaska') {
                            $tz = 'America/Anchorage';
                        } else if($timeZone == 'central') {
                            $tz = 'America/Chicago';
                        } else if($timeZone == 'eastern') {
                            $tz = 'America/New_York';
                        } else if($timeZone == 'hawaii') {
                            $tz = 'America/Adak';
                        } else if($timeZone == 'hawaii_no_dst') {
                            $tz = 'Pacific/Honolulu';
                        } else if($timeZone == 'mountain') {
                            $tz = 'America/Denver';
                        } else if($timeZone == 'mountain_no_dst') {
                            $tz = 'America/Phoenix';
                        } else if($timeZone == 'pacific') {
                            $tz = 'America/Los_Angeles';
                        } else {
                            $tz = 'America/Chicago';
                        }

                        // SET DATE/TIME BASED ON USER'S TIMEZONE
                        $usersTimezone = '';
                        if($tz) {
                            $usersTimezone = new DateTimeZone($tz);
                        }

                        // DECRYPT FORM NOTES
                        $historyArr = array();
                        foreach($formHistories as $formHistory) {
                            //$historyItem = $formHistory->history;

                            $dtObj = new DateTime($formHistory->timestamp);
                            if($tz) {
                                $dtObj->setTimeZone($usersTimezone);
                            }
                            $historyDate = $dtObj->format('M jS Y g:i a');
                            $tzAbbr = $dtObj->format('T');

                            // SET MESSAGE
                            $historyMessage = '';
                            if($formHistory->event == 'viewed') {
                                $historyMessage = $formHistory->name . ' viewed the form';
                            } else if($formHistory->event == 'archived') {
                                $historyMessage = $formHistory->name . ' archived the form';
                            } else if($formHistory->event == 'restored') {
                                $historyMessage = $formHistory->name . ' restored the form';
                            } else if($formHistory->event == 'pdf') {
                                $historyMessage = $formHistory->name . ' created a PDF version of the form';
                            } else if($formHistory->event == 'exported form') {
                                $historyMessage = $formHistory->name . ' exported the form';
                            } else if($formHistory->event == 'exported notes') {
                                $historyMessage = $formHistory->name . ' exported the form notes';
                            } else if($formHistory->event == 'exported history') {
                                $historyMessage = $formHistory->name . ' exported the form history';
                            } else if($formHistory->event == 'opened print interface') {
                                $historyMessage = $formHistory->name . ' opened the print interface, form may have been printed or saved as insecure PDF.';
                            } else {
                                $historyMessage = $formHistory->name . ' ' . $formHistory->event;
                            }

                            $historyArr[] = '
                                <div class="cm-hipaa-submitted-form-history-item">
                                    <div class="cm-hipaa-submitted-form-history-info">
                                        Date: ' . $historyDate . ' ' . $tzAbbr . '
                                    </div>
                                    <div class="cm-hipaa-submitted-form-history-info">
                                        Name: ' . $formHistory->name . '
                                    </div>
                                    <div class="cm-hipaa-submitted-form-history-info">
                                        User ID: ' . $formHistory->user_id . '
                                    </div>
                                    <div class="cm-hipaa-submitted-form-history-info">
                                        User Role: ' . $formHistory->user_role . '
                                    </div>
                                    <div class="cm-hipaa-submitted-form-history-info">
                                        User Email: ' . $formHistory->email . '
                                    </div>
                                    <div class="cm-hipaa-submitted-form-history-event">
                                        ' . $historyMessage . '
                                    </div>
                                </div>
                            ';
                        }

                        $history = '
                            ' . implode('', $historyArr) . '
                        ';

                        $results = array(
                            'success' => $success,
                            'history' => $history,
                            'total_results' => $totalResults
                        );
                    } else {
                        $results = array(
                            'error' => $error
                        );
                    }
                }
            }
        } else {
            $results = array(
                'error' => 'API did not return a success message'
            );
        }

        return json_encode($results);
    }

    /**** EXPORT FORM ***/
    public function exportForm($formId) {
        $licenseKey = esc_attr(get_option('license_key'));
        $customCss = esc_attr(get_option('hipaa_form_css'));
        $timeZone = esc_attr(get_option('time_zone'));

        if($timeZone == 'alaska') {
            $tz = 'America/Anchorage';
        } else if($timeZone == 'central') {
            $tz = 'America/Chicago';
        } else if($timeZone == 'eastern') {
            $tz = 'America/New_York';
        } else if($timeZone == 'hawaii') {
            $tz = 'America/Adak';
        } else if($timeZone == 'hawaii_no_dst') {
            $tz = 'Pacific/Honolulu';
        } else if($timeZone == 'mountain') {
            $tz = 'America/Denver';
        } else if($timeZone == 'mountain_no_dst') {
            $tz = 'America/Phoenix';
        } else if($timeZone == 'pacific') {
            $tz = 'America/Los_Angeles';
        } else {
            $tz = 'America/Chicago';
        }

        // GET USER DATA
        $user = wp_get_current_user();
        $myId = $user->ID;
        $user_roles = $user->roles;
        $role = '';
        $myDisplayName = $user->display_name;
        $myFirstName = $user->first_name;
        $myLastName = $user->last_name;
        $myEmail = $user->user_email;

        // SET MY NAME
        $myName = '';
        if($myFirstName || $myLastName) {
            $myName = $myFirstName . ' ' . $myLastName;
        } else if($myDisplayName) {
            $myName = $myDisplayName;
        } else {
            $myName = $myEmail;
        }

        /// SET ROLE
        if(in_array('administrator', $user_roles)) {
            $role = 'administrator';
        } else if(in_array('hipaa_forms', $user_roles)) {
            $role = 'hipaa';
        }

        // VALIDATE ACCOUNT
        $validateAccount = json_decode(self::validateAccount());
        $product = '';
        if(isset($validateAccount->product)) {
            $product = $validateAccount->product;
        }

        $licenseStatus = '';
        if(isset($validateAccount->license_status)) {
            $licenseStatus = $validateAccount->license_status;
        }

        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'exportform',
            'license_key' => $licenseKey,
            'domain' => self::getRootDomain(),
            'form_id' => $formId,
            'my_id' => $myId,
            'my_role' => $role,
            'my_email' => $myEmail,
            'my_name' => $myName
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        if ($output) {
            foreach ($output as $data) {
                $error = '';
                if(isset($data->error)) {
                    $error = $data->error;
                }

                if ($error) {
                    if ($error == 'No BAA') {
                        $results = array(
                            'error' => $error,
                            'content' => '<div class="cm-sign-baa-button">You must sign the BAA Agreement!</div><div class="cm-sign-baa-notice">If you would rather use your own BAA please email it to contact@codemonkeysllc.com or call us at 715.941.1040</div>'
                        );
                    } else {
                        $results = array(
                            'error' => $error,
                            'content' => $error
                        );
                    }
                } else {
                    $success = $data->success;

                    if ($success == 'success') {
                        $formsData = $data->forms;

                        // GET CURRENT DEFAULT TIMEZONE
                        $defaultTz = date_default_timezone_get();
                        // SET DEFAULT TIMEZONE TO UTC
                        date_default_timezone_set('UTC');

                        $formId = '';
                        $formName = '';
                        $fields = array();
                        foreach ($formsData as $formData) {
                            $formId = $formData->form_id;
                            $formName = $formData->form_name;
                            $firstName = $formData->first_name;
                            $lastName = $formData->last_name;
                            $usersTimezone = new DateTimeZone($tz);
                            $dtObj = new DateTime($formData->timestamp);
                            $dtObj->setTimeZone($usersTimezone);
                            $formDate = $dtObj->format('m-d-Y g:i a');
                            $tzAbbr = $dtObj->format('T');
                            $formFields = $formData->fields;
                            $encryptKey = $formData->encrypt_key;
                            $encryptIv = $formData->encrypt_iv;
                            $signature = $formData->signature;

                            // DECRYPT FORM FIELDS
                            $decryptedFields = '';
                            if($formFields) {
                                $decryptedFields = json_decode(self::decrypt($formFields, $encryptKey, $encryptIv));

                                // TODO: LOOP FORM FIELDS, FORMAT & RETURN JSON TO JAVASCRIPT TO CREATE CSV
                                $fields[] = $decryptedFields;
                            }
                        }

                        // RESET DEFAULT TIMEZONE BACK TO SERVER DEFAULT
                        date_default_timezone_set($defaultTz);

                        $results = array(
                            'success' => 'success',
                            'form_id' => $formId,
                            'form_name' => $formName,
                            'first_name' => $firstName,
                            'last_name' => $lastName,
                            'fields' => $fields
                        );
                    } else {
                        $results = array(
                            'error' => 'API did not return a success message',
                            'content' => 'API did not return a success message'
                        );
                    }
                }
            }
        } else {
            $results = array(
                'error' => 'No response from API',
                'content' => 'No response from API'
            );
        }

        return json_encode($results);
    }

    /*** EXPORT FORM NOTES ***/
    public function exportFormNotes($formId) {
        $licenseKey = esc_attr(get_option('license_key'));

        // GET USER DATA
        $user = wp_get_current_user();
        $myId = $user->ID;
        $user_roles = $user->roles;
        $role = '';
        $myDisplayName = $user->display_name;
        $myFirstName = $user->first_name;
        $myLastName = $user->last_name;
        $myEmail = $user->user_email;

        // SET MY NAME
        $myName = '';
        if($myFirstName || $myLastName) {
            $myName = $myFirstName . ' ' . $myLastName;
        } else if($myDisplayName) {
            $myName = $myDisplayName;
        } else {
            $myName = $myEmail;
        }

        /// SET ROLE
        if(in_array('administrator', $user_roles)) {
            $role = 'administrator';
        } else if(in_array('hipaa_forms', $user_roles)) {
            $role = 'hipaa';
        }

        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'exportformnotes',
            'license_key' => $licenseKey,
            'domain' => self::getRootDomain(),
            'form_id' => $formId,
            'my_id' => $myId,
            'my_role' => $role,
            'my_email' => $myEmail,
            'my_name' => $myName
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        if ($output) {
            foreach ($output as $data) {
                $error = '';
                if(isset($data->error)) {
                    $error = $data->error;
                }

                if ($error) {
                    if ($error == 'No BAA') {
                        $results = array(
                            'error' => $error
                        );
                    } else {
                        $results = array(
                            'error' => $error
                        );
                    }
                } else {
                    $success = $data->success;

                    if($success == 'success') {
                        $formNotes = $data->notes;

                        $timeZone = esc_attr(get_option('time_zone'));
                        $tz = '';

                        if($timeZone == 'alaska') {
                            $tz = 'America/Anchorage';
                        } else if($timeZone == 'central') {
                            $tz = 'America/Chicago';
                        } else if($timeZone == 'eastern') {
                            $tz = 'America/New_York';
                        } else if($timeZone == 'hawaii') {
                            $tz = 'America/Adak';
                        } else if($timeZone == 'hawaii_no_dst') {
                            $tz = 'Pacific/Honolulu';
                        } else if($timeZone == 'mountain') {
                            $tz = 'America/Denver';
                        } else if($timeZone == 'mountain_no_dst') {
                            $tz = 'America/Phoenix';
                        } else if($timeZone == 'pacific') {
                            $tz = 'America/Los_Angeles';
                        } else {
                            $tz = 'America/Chicago';
                        }

                        // SET DATE/TIME BASED ON USER'S TIMEZONE
                        $usersTimezone = '';
                        if($tz) {
                            $usersTimezone = new DateTimeZone($tz);
                        }

                        // DECRYPT FORM NOTES
                        $decryptedNotes = array();
                        foreach($formNotes as $formNote) {
                            $note = $formNote->note;
                            $noteKey = $formNote->key;
                            $noteIv = $formNote->iv;
                            $decryptedNote = self::decrypt($note, $noteKey, $noteIv);

                            $dtObj = new DateTime($formNote->timestamp);
                            if($tz) {
                                $dtObj->setTimeZone($usersTimezone);
                            }
                            $noteDate = $dtObj->format('M jS Y g:i a');
                            $tzAbbr = $dtObj->format('T');

                            $decryptedNotes[] = array(
                                'date' => $noteDate . ' ' . $tzAbbr,
                                'domain' => $formNote->domain,
                                'name' => $formNote->name,
                                'user_id' => $formNote->user_id,
                                'email' => $formNote->email,
                                'note' => $decryptedNote
                            );
                        }

                        $results = array(
                            'success' => $success,
                            'form_id' => $formId,
                            'notes' => $decryptedNotes
                        );
                    } else {
                        $results = array(
                            'error' => $error
                        );
                    }
                }
            }
        } else {
            $results = array(
                'error' => 'API did not return a success message'
            );
        }

        return json_encode($results);
    }

    /*** EXPORT FORM HISTORY ***/
    public function exportFormHistory($formId) {
        // GET LICENCE KEY
        $licenseKey = esc_attr(get_option('license_key'));

        // GET USER DATA
        $user = wp_get_current_user();
        $myId = $user->ID;
        $user_roles = $user->roles;
        $role = '';
        $myDisplayName = $user->display_name;
        $myFirstName = $user->first_name;
        $myLastName = $user->last_name;
        $myEmail = $user->user_email;

        // SET MY NAME
        $myName = '';
        if($myFirstName || $myLastName) {
            $myName = $myFirstName . ' ' . $myLastName;
        } else if($myDisplayName) {
            $myName = $myDisplayName;
        } else {
            $myName = $myEmail;
        }

        /// SET ROLE
        if(in_array('administrator', $user_roles)) {
            $role = 'administrator';
        } else if(in_array('hipaa_forms', $user_roles)) {
            $role = 'hipaa';
        }

        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'exportformhistory',
            'license_key' => $licenseKey,
            'domain' => self::getRootDomain(),
            'form_id' => $formId,
            'my_id' => $myId,
            'my_role' => $role,
            'my_email' => $myEmail,
            'my_name' => $myName
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        if ($output) {
            foreach ($output as $data) {
                $error = '';
                if(isset($data->error)) {
                    $error = $data->error;
                }

                if ($error) {
                    if ($error == 'No BAA') {
                        $results = array(
                            'error' => $error
                        );
                    } else {
                        $results = array(
                            'error' => $error
                        );
                    }
                } else {
                    $success = $data->success;

                    if($success == 'success') {
                        $formHistories = $data->history;
                        $totalResults = $data->total_results;

                        $timeZone = esc_attr(get_option('time_zone'));
                        $tz = '';

                        if($timeZone == 'alaska') {
                            $tz = 'America/Anchorage';
                        } else if($timeZone == 'central') {
                            $tz = 'America/Chicago';
                        } else if($timeZone == 'eastern') {
                            $tz = 'America/New_York';
                        } else if($timeZone == 'hawaii') {
                            $tz = 'America/Adak';
                        } else if($timeZone == 'hawaii_no_dst') {
                            $tz = 'Pacific/Honolulu';
                        } else if($timeZone == 'mountain') {
                            $tz = 'America/Denver';
                        } else if($timeZone == 'mountain_no_dst') {
                            $tz = 'America/Phoenix';
                        } else if($timeZone == 'pacific') {
                            $tz = 'America/Los_Angeles';
                        } else {
                            $tz = 'America/Chicago';
                        }

                        // SET DATE/TIME BASED ON USER'S TIMEZONE
                        $usersTimezone = '';
                        if($tz) {
                            $usersTimezone = new DateTimeZone($tz);
                        }

                        // DECRYPT FORM NOTES
                        $history = array();
                        foreach($formHistories as $formHistory) {
                            $dtObj = new DateTime($formHistory->timestamp);
                            if($tz) {
                                $dtObj->setTimeZone($usersTimezone);
                            }
                            $historyDate = $dtObj->format('M jS Y g:i a');
                            $tzAbbr = $dtObj->format('T');

                            // SET MESSAGE
                            $historyMessage = '';
                            if($formHistory->event == 'viewed') {
                                $historyMessage = $formHistory->name . ' viewed the form';
                            } else if($formHistory->event == 'archived') {
                                $historyMessage = $formHistory->name . ' archived the form';
                            } else if($formHistory->event == 'restored') {
                                $historyMessage = $formHistory->name . ' restored the form';
                            } else if($formHistory->event == 'pdf') {
                                $historyMessage = $formHistory->name . ' created a PDF version of the form';
                            } else if($formHistory->event == 'exported form') {
                                $historyMessage = $formHistory->name . ' exported the form';
                            } else if($formHistory->event == 'exported notes') {
                                $historyMessage = $formHistory->name . ' exported the form notes';
                            } else if($formHistory->event == 'exported history') {
                                $historyMessage = $formHistory->name . ' exported the form history';
                            }

                            $history[] = array(
                                'date' => $historyDate . ' ' . $tzAbbr,
                                'name' => $formHistory->name,
                                'user_id' => $formHistory->user_id,
                                'user_role' => $formHistory->user_role,
                                'email' => $formHistory->email,
                                'message' => $historyMessage
                            );
                        }

                        $results = array(
                            'success' => $success,
                            'form_id' => $formId,
                            'history' => $history
                        );
                    } else {
                        $results = array(
                            'error' => $error
                        );
                    }
                }
            }
        } else {
            $results = array(
                'error' => 'API did not return a success message'
            );
        }

        return json_encode($results);
    }

    /*** UPDATE CUSTOM STATUS ***/
    public function updateCustomStatus($formId, $customStatus) {
        // GET LICENCE KEY
        $licenseKey = esc_attr(get_option('license_key'));

        // GET USER DATA
        $user = wp_get_current_user();
        $myId = $user->ID;
        $user_roles = $user->roles;
        $role = '';
        $myDisplayName = $user->display_name;
        $myFirstName = $user->first_name;
        $myLastName = $user->last_name;
        $myEmail = $user->user_email;

        // SET MY NAME
        $myName = '';
        if($myFirstName || $myLastName) {
            $myName = $myFirstName . ' ' . $myLastName;
        } else if($myDisplayName) {
            $myName = $myDisplayName;
        } else {
            $myName = $myEmail;
        }

        /// SET ROLE
        if(in_array('administrator', $user_roles)) {
            $role = 'administrator';
        } else if(in_array('hipaa_forms', $user_roles)) {
            $role = 'hipaa';
        }

        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'updatecustomstatus',
            'license_key' => $licenseKey,
            'domain' => self::getRootDomain(),
            'form_id' => $formId,
            'my_id' => $myId,
            'my_role' => $role,
            'my_email' => $myEmail,
            'my_name' => $myName,
            'custom_status' => $customStatus
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        if ($output) {
            foreach ($output as $data) {
                $error = '';
                if(isset($data->error)) {
                    $error = $data->error;
                }

                if ($error) {
                    if ($error == 'No BAA') {
                        $results = array(
                            'error' => $error
                        );
                    } else {
                        $results = array(
                            'error' => $error
                        );
                    }
                } else {
                    $success = $data->success;

                    if($success == 'success') {
                        $results = array(
                            'success' => $success
                        );
                    } else {
                        $results = array(
                            'error' => $error
                        );
                    }
                }
            }
        } else {
            $results = array(
                'error' => 'API did not return a success message'
            );
        }

        return json_encode($results);
    }

    /*
     * FRONT END CLASSES
     */

    /*** SUBMIT FORM ***/
    public function sendForm($licenseKey, $mailSenderName, $mailSenderEmail, $mailRecipients, $mailBccTo, $notificationSubject, $notificationMessage, $formId, $formName, $location, $locationEmail, $firstName, $lastName, $email, $phone, $fields, $formHtml, $signature, $selectedUser, $files) {
        // ENCRYPT THE FORM FIELDS
        $key = self::keygen();
        $iv = self::ivgen();
        $fields = self::encrypt($fields, $key, $iv);
        $formHtml = self::encrypt($formHtml, $key, $iv);

        $enabledFormsSettings = get_option('enabled_forms_settings'); // NEW JSON VERSION WITH FORM SETTINGS

        $notificationsDisabled = esc_attr(get_option('hipaa_disable_email_notifications'));

        // Create curl resource
        $curl_url = 'https://www.hipaaforms.online/hipaa-api';
        $curl = curl_init($curl_url);

        // Create post data
        $curl_post_data = array(
            'action' => 'post',
            'license_key' => $licenseKey,
            'domain' => self::getRootDomain(),
            'notification_email' => $mailRecipients,
            'form_id' => $formId,
            'form_name' => $formName,
            'location' => $location,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => $phone,
            'fields' => $fields,
            'form_html' => $formHtml,
            'key' => $key,
            'iv' => $iv,
            'signature' => $signature,
            'selected_user' => $selectedUser,
            'files' => $files
        );

        // Assign curl settings
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
        curl_setopt($curl, CURLOPT_REFERER, get_site_url());

        $output = json_decode(curl_exec($curl));

        // Close curl resource to free up system resources
        curl_close($curl);

        foreach($output as $data) {
            $error = '';
            if(isset($data->error)) {
                $error = $data->error;
            }

            if($error) {
                return $error;
            } else {
                if($notificationsDisabled == 'on') {
                    $results = array(
                        'success' => $data->success
                    );
                } else {
                    $subjectLocation = '';
                    $bodyLocation = '';
                    if ($location) {
                        $subjectLocation = ' - ' . $location;

                        $bodyLocation = '
                            <tr>
                                <td>
                                    Location: ' . $location . '
                                </td>
                            </tr>
                        ';
                    }

                    // SET NOTICE EMAIL SUBJECT
                    $subject = '';
                    if ($notificationSubject) {
                        // IF NOTIFICATION SUBJECT HAS VALUE & NOTIFICATION OPTION IS SET TO CUSTOM REPLACE MAGIC TAGS AND SET AS NOTIFICATION SUBJECT
                        $subject = str_replace(array('{formname}', '{firstname}', '{lastname}', '{email}', '{phone}', '{location}'), array($formName, $firstName, $lastName, $email, $phone, $location), $notificationSubject);
                    } else {
                        // IF NO CUSTOM OR DEFAULT SUBJECT SET A DEFAULT SUBJECT
                        $subject = 'HIPAA Forms Submission' . $subjectLocation;
                    }

                    // SET NOTIFICATION EMAIL FROM NAME
                    if (!$mailSenderName) {
                        $fromName = 'HIPAA FORM SUBMISSION';
                    } else {
                        $fromName = $mailSenderName;
                    }

                    // SET NOTIFICATION EMAIL FROM ADDRESS
                    $url = $_SERVER['SERVER_NAME'];
                    if (!$mailSenderEmail) {
                        $fromMail = 'hipaaforms@' . $url;
                    } else {
                        $fromMail = $mailSenderEmail;
                    }

                    // SET NOTIFICATION EMAIL RECIPIENTS (SEND TO)
                    $toMail = str_replace(' ', '', $mailRecipients);
                    $toMail = explode(',', $toMail);
                    if ($locationEmail) {
                        // IF SPECIFIC LOCATION EMAIL IS SET ADD THE LOCATION EMAIL TO RECIPIENTS STRING
                        array_push($toMail, $locationEmail);
                    }

                    if ($selectedUser) {
                        // SET USER BY ID
                        $user = get_user_by('id', $selectedUser);

                        // GET SELECTED USER'S EMAIL
                        array_push($toMail, $user->user_email);
                    }

                    //$toMail = explode(',', $toMail);
                    $toMail = array_unique($toMail);
                    $toMail = implode(',', $toMail);

                    // GET NOTIFICATION EMAIL & SET BODY
                    $notificationEmailMessage = '';
                    if ($notificationMessage) {
                        // STRIP SLASHES FROM NOTIFICATION EMAIL HTML
                        $notificationMessage = stripslashes($notificationMessage);

                        // IF NOTIFICATION MESSAGE HAS VALUE & NOTIFICATION OPTION IS SET TO CUSTOM REPLACE MAGIC TAGS AND SET AS NOTIFICATION MESSAGE
                        $notificationEmailMessage = str_replace(array('{formname}', '{firstname}', '{lastname}', '{email}', '{phone}', '{location}'), array($formName, $firstName, $lastName, $email, $phone, $location), $notificationMessage);
                    } else {
                        // IF NO DEFAULT NOTIFICATION MESSAGE SET A DEFAULT MESSAGE HERE
                        $notificationEmailMessage = '<table width="600px" style="border-collapse:collapse;">
                                <tbody>
                                    <tr>
                                        <th><b>' . $formName . ' HIPAA Form Submission Nothing Set</b></th>
                                    </tr>
                                    <tr>
                                        <td style="height:30px"></td>
                                    </tr>
                                    ' . $bodyLocation . '
                                    <tr>
                                        <td>
                                            First Name: ' . $firstName . '
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="height:15px"></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Last Name: ' . $lastName . '
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="height:15px"></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Email: ' . $email . '
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="height:15px"></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Phone: ' . $phone . '
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="height:30px"></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Please log into the admin dashboard to view the form.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Please do not reply to this email
                                        </td>
                                    </tr>
                                </tbody>
                            </table>';
                    }

                    // SET NOTIFICATION EMAIL BODY
                    $body = $notificationEmailMessage;

                    $headers = array();
                    $headers[] = 'Content-Type: text/html; charset=UTF-8';
                    $headers[] = 'From: ' . $fromName . ' <' . $fromMail . '>';
                    //$headers[] = 'Bcc: ' . $mailBccTo; - WE'RE NOT CURRENTLY USING THE BCC OPTION ANYWHERE

                    // CHECK IF SENDGRID PLUGIN ACTIVE
                    if (!function_exists('is_plugin_active') || !function_exists('is_plugin_active_for_network')) {
                        require_once(ABSPATH . '/wp-admin/includes/plugin.php');
                    }
                    if (is_multisite()) {
                        $sendGridEnabled = (is_plugin_active_for_network('sendgrid-email-delivery-simplified/wpsendgrid.php') || is_plugin_active('sendgrid-email-delivery-simplified/wpsendgrid.php'));
                    } else {
                        $sendGridEnabled = is_plugin_active('sendgrid-email-delivery-simplified/wpsendgrid.php');
                    }

                    // SEND EMAIL NOTICE
                    $mail = false;
                    $results = '';

                    if ($sendGridEnabled == 1) {
                        // IF SENDGRID PLUGIN ACTIVE SEND WITHOUT HEADERS (HEADERS BREAK SENDGRID)
                        $mail = wp_mail($toMail, $subject, $body);
                    } else {
                        $mail = wp_mail($toMail, $subject, $body, $headers);
                    }

                    if ($mail) {
                        $results = array(
                            'success' => $data->success
                        );
                    } else {
                        $results = array(
                            'error' => '- There was an error sending the notification email!',
                            'success' => $data->success
                        );
                    }
                }

                // RETURN SUCCESS NOTICE
                return json_encode($results);
            }
        }

        return false;
    }
}