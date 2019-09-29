=== HIPAA FORMS - Add HIPAA Compliant Webforms to Your Wordpress Website ===
Contributors: Sfraise77, Dmoore8200
Tags: hipaa forms, caldera forms, gravity forms, hipaa compliance, secure forms, health forms, medical forms
Requires at least: 4.7
Tested up to: 5.2.2
Stable tag: 2.2.5
License: GPLv2

Add HIPAA Compliant web forms easily to your Wordpress website using the HIPAA FORMS SaaS Service and Caldera or Gravity Forms.

== Description ==
The HIPAA FORMS plugin allows you to create web forms using Caldera Forms or Gravity Forms just like you would a simple contact form.  A simple checkbox next to the form within the plugin admin interface instantly takes over your form, appends the form with a HIPAA Compliant badge and signature field where users can sign by dragging their mouse or with their finger on touch screens, and upon submit encrypts the data and pushes it to the HIPAA FORMS Service API which then stores it within a HIPAA Compliant storage solution.

Users with login credentials and the appropriate user roles (administrator or hipaa) can then log into your Wordpress administrator dashboard and search/view the submitted forms and even generate an encrypted and password protected PDF file of the form which can then be printed or saved to a hard drive.

Here's how the HIPAA FORMS Service and integrated Wordpress plugin secures the protected health information of your forms:
1. On submit the entire form is encrypted requiring 2 separate keys to decrypt.
2. Once the form data is encrypted it sends the encrypted data to the HIPAA FORMS Service API where it remains encrypted on a HIPAA Compliant storage solution.
3. When someone with login credentials logs into your Wordpress administrator dashboard with either and administrator or hipaa user role that user can then go to the HIPAA FORMS plugin interface and view the forms that have been submitted.  While you can view the decrypted forms here the data never actually leaves the HIPAA FORMS Service servers, you're simply pulling them from the api, decrypting and viewing in your browser, the data never actually touches your hosting server.
4. The only way the data can leave the HIPAA FORMS Service servers is if you click the "generate pdf" button and create an encrypted password protected pdf file.  At this point you can print or save the pdf to your hard drive but since the pdf remains encrypted and password protected the form data remains safe in transit.
5. Once you close the pdf generation window the pdf file is then destroyed on the HIPAA FORMS Service server removing any chance of a bad actor gaining access to the file.
6. Each time a user accesses the HIPAA FORMS plugin admin interface a log entry is created and stored on the HIPAA FORMS Service which you can review at any time from the HIPAA FORMS plugin interface.  This is required by HIPAA Regulations to ensure any potential data breach can be back-traced.
7. Since the form data is encrypted and remains on the HIPAA FORMS Service database we ensure that protected data can not be tampered with and changed by anyone.
8. Both a SSL certificate and a BAA agreement between the user and Code Monkeys LLC (the company that developed and maintains the HIPAA FORMS Service and Wordpress plugin) is required.  The ability to submit or view forms is disabled until both of these requirements have been met.

NEW IN V1.5.5:
Version 1.5.5 is our first "major" update to the plugin since releasing it.  This update includes an improved user interface and the following specific form settings:
1. Option to show/hide the signature field
2. Option to specify a success message or a redirect url after a form is submitted
3. Option to set who can see the submitted forms with the following options:
    A. All users with admin/hipaa user role
    B. Only specific users
    C. Only a specific doctor/user selected within a form (ie. Patient selects a specific doctor in a form, only that doctor will see the submitted form).  NOTE: Admins see all forms regardless of settings.

While we believe we've made the entire process as simple as possible we also understand that there may be questions or issues sometimes that the user needs addressed.  Given the urgency and importance of a service such as this we've built a complete support ticket system directly into the HIPAA FORMS Wordpress plugin interface to allow the users to submit and track support tickets without ever needing to leave their own administrator dashboard.  The HIPAA FORMS Service team at Code Monkeys LLC strives to respond to tickets within 1 business day.  Users can also call Code Monkeys LLC directly for support between 9am and 5pm CST.

NOTE: A subscription-based license key to access the HIPAA FORMS Service API is required from <a href="https://www.hipaaforms.online" target="_blank">https://www.hipaaforms.online</a> in order to submit and view forms (a free version is available, no credit card required).  Your website must also have SSL enabled (url should show https://).


== Web Designer Friendly ==
We know that the owners or board members of dental clinics, health clinics, hospitals and insurance agencies aren’t the ones that will be implementing this service.  It’s YOU, the web designers and developers that have the task of finding a solution and making it work.

Our initial primary goal was to build a solution for our own website builds so we’ve done everything we can to make the installation, setup and implementation of this service as seamless and efficient as possible.  If you can build a contact form with Caldera Forms you can build HIPAA Compliant web forms with our service, in fact once you have the plugin setup and build the form all you really need to do is check a box and your form is instantly compliant.

We also want to ensure that YOU have the knowledge and ability to protect both yourself and your client.  While we require your client to sign a BAA agreement with us we don’t require that the web designer/developer have a BAA in place but we STRONGLY recommend it.  A BAA agreement protects your client, not you or us but it is actually REQUIRED according to HIPAA Regulations and without the BAA in place your client is not in compliance and may be violating both federal and state privacy laws.  While YOU may not have a legal obligation to have a BAA in place with your client, as your client’s technical expert on all thing relating to their website have a moral obligation to make your client aware that a BAA agreement should be in place between you and them.

If you’re not an agency or freelance but work on the website directly for a company you should still ensure that your company has a BAA agreement in place for all employees and any 3rd party IT professionals that may have access to protected health information.  Fines for violating HIPAA Regulations can be in the six figures and your company could be out of business leaving you looking for a new job if your company is not compliant.

Regardless of if you’re an agency, freelancer or work directly for a company that takes protected private health information, if you have any questions or need help with anything relating to compliance or how to put a BAA agreement in place between yourself and your client please don’t hesitate to submit a support ticket or give us a call.


== Screenshots ==
1. This is the interface showing the submitted forms.
2. This is an example form showing the appended HIPAA Compliant badge, privacy statement agreement and signature field.


== Installation ==
1.  Upload your plugin folder to the '/wp-content/plugins' directory.
2.  Activate the plugin through the 'Plugins' menu in WordPress.

SETUP:
First you must get an API license key for the HIPAA FORMS Service which does offer a free subscription option.  This service is what handles the storage and PDF generation of the forms and forms CAN NOT be submitted without a subscription.  Once you subscribe to the service a license key will be generated and emailed to you.  Visit <a href="https://www.hipaaforms.online" target="_blank">HIPAA FORMS Service</a> to purchase a subscription.

Next make sure you have Caldera Forms or Gravity Forms installed and active.  Caldera is a free form builder plugin that can easily be installed from the "plugins->add" interface and searching for Caldera Forms.  Gravity Forms is a premium paid form builder plugin that can be purchased <a href="https://www.gravityforms.com/" target="_blank">HERE</a> No additional extensions are needed.

Now that you have your license key and Caldera or Gravity Forms installed and activated, go to the "Settings" tab and add your license key, select your preferred compatible form builder plugin (ie. Caldera or Gravity), enter the email address you wish to receive form submission notices to and set your preferred time zone.  If you plan on using the custom HIPAA FORMS user role you can also set what permissions that role should have here.

Once the plugin settings are save you need to create a form.  You must have 4 required fields in the form which are needed in order to filter/search the submitted forms. These fields are:

First Name: Must have a text input
Caldera: Must have the slug "first_name"
Gravity: Must have the class "hipaa_forms_first_name"

Last Name: Must have a text input
Caldera: Must have the slug "last_name"
Gravity: Must have the class "hipaa_forms_last_name"

Phone: Must have a text or phone input
Caldera: Must have the slug "phone"
Gravity: Must have the class "hipaa_forms_phone"

Email: Must have a text or email input
Caldera: Must have the slug "email"
Gravity: Must have the class "hipaa_forms_email"

These standardized fields are required in order to search or filter the forms by these options.

If a form has all of the required fields then you will have the option to select it by checking the box at the left of the form. If the form does NOT have all of the required fields a warning icon will show next to the form. Clicking on the warning/toggle icon will display what fields are missing.

Once you have checked the box next to a form it should be HIPAA Compliant. To verify the form is now compliant go to where the form is rendered on the page and you should now see an additional section at the bottom of the form right above the submit button showing a checkbox to agree to the HIPAA privacy agreement, a badge showing the form is encrypted and HIPAA compliant and the signature field which a user can sign by left clicking and dragging their mouse or by simply using their finger or stylus if on a touch screen.  You should also see a padlock on the submit button indicating that the form submit function is secure.  If you do NOT see these something is not correct and the form will NOT be HIPAA compliant and you should submit a support ticket so our support staff can troubleshoot the issue before attempting to use the form.  It is your responsibility to ensure forms are in a compliant state before allowing patients to submit private protected health information and failure to do so could result in fines.

When someone submits the HIPAA Compliant form the default form submit button is replaced with a custom button (indicated by the padlock icon) and instead the form will be encrypted and an API call is made to the HIPAA FORMS API and saved into a HIPAA Compliant database storage solution. The only fields that are not encrypted are the required first name, last name, phone # and email in order to allow searching/filtering the forms in the "Submitted Forms" tab. The form itself (and all fields within it) are encrypted at the time of submission in order to protect the data in transit as well as at rest within the HIPAA FORMS Service data solution. If you tried to look at the form at this point you would just see a long string of nonsense letters, numbers and characters and would not be able to see any of the actual form data.

The only way the form data can be viewed at this point is by logging into the Wordpress admin panel with valid username and password credentials for an account with the appropriate user role associated to it (administrator or hipaaforms) and opening the "Submitted Forms" tab within the HIPAA FORMS interface. Here another API request is sent to the HIPAA FORMS API and the submitted forms data is pulled. Once the data is returned from the API the associated encryption keys are then used to decrypt the form data and then display that data on the screen. While you can view this data within this tab the actual data is never stored anywhere on your server, it simply pulls it from the HIPAA FORMS Service data solution and displays it. The only way for this data to actually ever leave our secure system is to generate an encrypted PDF file with password protection.

To generate a PDF version of the form click the "Generate PDF" button next to the form. This will bring up a modal window (popup) asking you to set a password. Once a password is provided click generate. The modal window should update with a link to open the newly created PDF, once you enter the password the form will load and you will be able to view the form, print it or save it to your computer.  If you do not keep track of the password you will NOT be able to view the PDF file.

The PDF file must be encrypted and password protected to ensure that the form can never be intercepted and read as it's transferred between the data base and you. This should also help keep you more compliant internally as the file can not be read without the correct password once it's saved to your computer. While ensuring that the PDF files are encrypted and password protected should keep the data safe and compliant we HIGHLY recommend that any computer you download the PDFs to have encrypted hard drives to be safe and ensure compliance. Once you print or download the PDF to your computer Code Monkeys LLC and all associates covered within the BAA agreement bear no liability for the handling of the data.

We strongly recommend that you keep up with HIPAA regulation changes and that you work with a qualified attorney and/or HIPAA Compliance professional to ensure compliance.


== Frequently Asked Questions ==
REQUIREMENTS:

YOU MUST HAVE CALDERA OR GRAVITY FORMS INSTALLED & ACTIVE
Currently the HIPAA Forms plugins is only integrated with Caldera & Gravity Forms.  Caldera Forms is a free form builder plugin and can be installed by searching for it in the Wordpress plugin repository (plugins->add new).  Gravity Forms is a premium paid form builder plugin that can be purchased <a href="https://www.gravityforms.com/" target="_blank">HERE</a> No additional extensions are needed.

YOU MUST HAVE SSL ENABLED
The HIPAA FORMS plugin checks to ensure SSL (https) is enabled and being used.  Any forms set as HIPAA Compliant will be deactivated if the url does not start with https://.  If you're unable to setup SSL with your current host or if your current host's cost is too expensive consider a managed hosting (and optional Wordpress maintenance package) from Code Monkeys. We automatically issue free SSL certificates to all of our hosting customers. <a href="https://www.codemonkeysllc.com/hosting" target="_blank">CLICK HERE FOR DETAILS</a>

YOU MUST HAVE A VALID LICENSE KEY
You can purchase a license key from <a href="https://www.hipaaforms.online" target="_blank">hipaaforms.online</a> as a free, monthly, quarterly or annual subscription basis.  While this plugin is free to install and use, the HIPAA FORMS plugin is integrated with an API service and you must have an active account with a valid license key to use this service. A free API subcription is available but limits you to 1 form and up to 25 form submissions per month while the paid subscription is $55/mo and allows unlimited forms and form submissions.  If your paid subscription expires you will automatically be dropped down to the basic plan and limited to 25 form submissions per month.

A subscription account & license key is required as a BAA agreement is required and the encrypted E-PHI data is stored on the HIPAA Forms API database in order to meet HIPAA/PIPEDA regulations and there must be a way to validate who you are and that all of the requirements are in place to do so.

YOU CAN ONLY SUBMIT & VIEW FORMS FROM YOUR ASSOCIATED DOMAIN
Forms can only be submitted and viewed from the domain you added to your HIPAA FORMS Service subscription account at the time of checkout.  When a request is made to the HIPAA FORMS Service API it does a check against your license key, domain and if a BAA agreement has been signed.  If any of those things are not valid the API request is denied and an error will be returned specifying what the issue is.  Only one license key and domain is allowed per subscription meaning you can NOT use the same license or domain on more than one website.  This is done as an additional security measure to ensure that even if a license key is stolen form data would not be accessible.  If you need to change the domain associated with your license key you can do so by logging in at <a href="https://www.hipaaforms.online/my-account" target="_blank">https://www.hipaaforms.online/my-account</a>

YOU MUST SIGN THE BAA AGREEMENT
A Business Associate Agreement (BAA) typically is required for companies that are subject to the Health Insurance Portability and Accountability Act (HIPAA) to ensure that protected health information (PHI) is appropriately safeguarded. Failure to manage data privacy risks with non-business associate vendors may lead to both violations of HIPAA and state privacy laws.  The BAA agreement is in place for your protection and forms can not be submitted or viewed until it is in place.  We also recommend that you have a BAA in place with your web designer if they work on the site as a 3rd party contractor.


COMMON ISSUES:

EMAIL NOTICES GOING TO SPAM
Default Wordpress emails get sent through your host's domain which often times will be flagged as spam.  We highly recommend installing an email SMTP plugin for Wordpress and using the SMTP settings for a legit email address. This will allow Wordpress to send emails from the SMTP server instead of from your host.

FORMS ARE DISABLED / HIPAA COMPLIANT BADGE DOESN'T APPEAR
If you do NOT see the additional section at the bottom of the form with the HIPAA compliant badge then there is an issue somewhere and the form will NOT be disabled as it will not be HIPAA compliant. A common reason this might happen is if you do NOT have SSL (https://) enabled or if the user is viewing the http:// version of the page. We strongly recommend that you setup a redirect in your .htaccess file or by using a plugin to ensure all pages are served the https:// version of the page. If this is the case the form will be disabled and you should see a warning notice at the bottom of the form instead of the badge.

Another common reason you might not see this section is if your license key has expired. If this is the case you should see a warning notice at the bottom of the form and the form will be disabled. Reactivating your license key will solve the issue and your form will be enabled again.

A less common reason for this would be if another plugin is causing a Javascript/jQuery error or conflict.

NONCE EXPIRED ERROR
Wordpress uses a nonce (number used once) to help secure your site during things like form submissions and AJAX calls, although its not really a "number used once" in the traditional sense.  Instead this is a hash token that can be used multiple times within a 12 or 24 hour period at which point the nonce will expire.  What happens is if your cache expiration is set beyond 12 hours the nonce will also be cached resulting in a validation error as that nonce will have expired.

This is not just a specific issue to our plugin, if your cache expiration is set too long it can cause issues with many other plugins as well.

To solve this issue make sure you have any caching plugins such as W3TC or Super Cache set to expire before 12 hours.

If the problem persists with caching plugins completely deactivated then its most likely an issue with a server-side cache on your hosting server.  You will need to contact your hosting company and request the caching be reduced to under 12 hours.  If you are on an ultra-cheap shared hosting solution from someone like HostGator you will most likely need to move to another host as they will not adjust their caching to play well with Wordpress nonces and honestly if you rely on your website for you business which we would assume you do if you are using this plugin then you really should spend a little extra for a good reliable host, you really do get what you pay for when it comes to hosting solutions.

LONGER FORMS ARE SUBMITTING BUT NOT SHOW THE FORM INFORMATION IN THE SUBMITTED FORMS VIEW
Very long forms may exceed the max_input_vars setting in your hosting server's PHP.ini.  This will cause the form to submit however the actual form will most likely be empty and not actually sent through the API since it's larger than your limit.

To solve this increase you max_input_vars in your PHP.ini.  If you are on a shared hosing account and do not have access to the PHP.ini settings or are unsure on how to change them there are 3rd party plugins available in the Wordpress plugin repository that will allow you to change your settings from the Wordpress admin panel.


COMMON QUESTIONS:

WHAT IS A BAA?
A Business Associate Agreement (BAA) typically is required for companies that are subject to the Health Insurance Portability and Accountability Act (HIPAA) to ensure that protected health information (PHI) is appropriately safeguarded. Failure to manage data privacy risks with non-business associate vendors may lead to both violations of HIPAA and state privacy laws.  You will be unable to use the HIPAA FORMS Service until you have signed the BAA with Code Monkeys LLC (the developers of the service) and will receive a notice to do so within the "submitted forms" tab as well as in the settings tab until it has been signed.  We HIGHLY recommend that you have a BAA in place with your web designer as well if you use a 3rd party contractor for web design service.

CAN WE USE OUR OWN BAA INSTEAD OF YOURS?
Yes, in most cases we will sign your BAA in place of our default agreement.

You can email your signed BAA to us at spencer at codemonkeysllc.com. We will review it and assuming everything looks good we will sign it and replace the BAA on file for your account.

CAN WE MODIFY/EXTEND THIS PLUGIN?
WE STRONGLY RECOMMEND NOT MODIFYING THE FUNCTIONALITY OF THIS PLUGIN!

This plugin is released under the GPL license and is open source allowing you to modify the plugin however we strongly recommend against attempting to modify the core functionality of the plugin.  The plugin simply acts as an interface to the API service where most of the "under the hood" functionality lives however some functionality such as encryption prior to sending the form data to the API happens within the plugin.  Breaking or disabling this encryption process could result in non-encrypted private protected sensitive health information being submitted which would be a HIPAA violation and may lead to both violations of HIPAA and state privacy laws.

While we recommend not modifying the core functionality of the plugin changing the CSS/Styles is totally fine and recommended.

CAN IMAGES/FILES BE ATTACHED TO FORMS?
We offer a secure HIPAA compliant file upload add-on option with unlimited uploads and unlimited storage to our service for an additional $30/mo or $300/yr. This option is not available with our basic free subscription.

With our file upload option enabled the basic file upload fields within Caldera or Gravity Forms are over-ridden by our plugin and the files are submitted directly from the browser to our secure encrypted file storage system when the form is submitted.

If files have been uploaded and attached to a submitted form you’ll be able to view those files from within the submitted form interface of the HIPAA Forms dashboard.

Secure generic pre-signed access URLs are generated when you load the submitted form that expire after 1 hour for greater security.

HOW CAN WE USE THIS ON A STAGING SITE?
The answer to this question depends on HOW you handle a staging version of the website.

You are only able to submit and view forms from within the domain associated with your license key.  If your staging version in under a subdomain of that domain you will be fine, the root domain is all that matters.  However if your staging version is under a different domain you will only be able to use the service from staging OR live, not both at the same time.

If you are "pre-launch" we would recommend setting the domain on your HIPAA FORMS Service account to your staging server domain first.  Then once you are ready to go live simply switch the domain to the live domain.

We understand that this can be frustrating to developers that do not have a staging version under the same root domain as we're developers ourselves.  We are exploring possible solutions to this for future releases to help with this issue.


== Changelog ==
V2.2.5
NEW OPTION
1. Added the ability to disable email notifications

V2.2.4
BUG FIXES
1. Fixed UI issues with re-assigning users on specific forms.
2. Fixed bug with file uploader in Safari browser not passing required field validation.

V2.2.3
MINOR UPDATE
1. Removed unused BCC option from notification email headers which caused notification emails to break in rare situations
2. Update FAQ's
3. Added check if required fields removed from selected forms and if so automatically deactivate form
4. Added additional time zone options

V2.2.2
1. Tweaked support tickets

V2.2.1
BUG FIX & ADDED FEATURE
1. Added fix to domain check on some edge case scenarios
2. Tweaked email error handling to prevent breaking preventing redirect or callback function from firing
3. Added check on name fields to ensure value is set before passing into identifiers as some edge cases have multiple conditional identifier fields hidden.
4. Added custom status option

V2.2.0
Missing files in last commit

V2.1.9
BUG FIX
Previous version did not add new js file to repository

V2.1.8
ADDED FEATURE
1. Added print/save to PDF button to web view version of form
2. Added function to log print event to form history

V2.1.7
MAJOR RELEASE
1. Removed add list item icons from passing to submitted Gravity forms
2. Fixed API bug causing PDF generation to break if field value was not a string
3. Removed select options from multi-select Gravity fields on submitted forms
4. Changed archive icon & now allow hipaa user role to archive forms
5. Added destroy form option for admin users
6. Fixed Caldera bug causing drag 'n draw signature to not initialize using multi-page breadcrumb step links
7. Stripped slashes from form names in PDF
8. Added Wordfence issue FAQ

V2.1.6
BUG FIX
1. Removed Gravity required field validation error from top of form on re-submit
2. Removed unwanted hidden footer inputs that displayed nonsense data at bottom of Gravity forms
3. Fixed slashes not being removed from submitted form names

V2.1.5
BUG FIX
Fixed Gravity Forms bug causing non-hipaa enabled forms to have submit button removed.  This only happened if a form was set as HIPAA compliant but then later de-selected.

V2.1.4
EXPERIMENTAL CALDERA ADVANCED FILE UPLOAD 2.0 INTEGRATION
Added experimental support for Caldera advanced file upload 2.0 fields.  This should be used with caution as it requires hooking onto the cf2-field-wrapper class and replacing the wrapper with our own upload inputs.  Caldera appends to this wrapper class the same as us via Javascript and has the potential if Javascript delays of appending AFTER us.  Using Caldera advanced file upload 2.0 fields is currently not recommended.

V2.1.3
BUG FIX
Fixed bug with Caldera Forms if file upload add-on is enabled but no file upload field exists in form getting stuck in file upload mode.

V2.1.2
BUG FIX
Submit button wasn't resetting to active on required input error.

V2.1.1
MINOR UPDATE
1. Disabled submit button on submit to prevent double submissions
2. Added support for Gravity advanced name field

V2.1.0
BUG FIX
Fixed undefined add on property notice

V2.0.9
BUG FIXES/MULTI-FILE UPLOAD SUPPORT
1. Fixed bug in default email notification message showing default template instead of saved template
2. Fixed bug breaking file upload when set to required
3. Added multi-file upload support to Caldera
4. Added multi-file upload support to Gravity
5. Added file upload validation check to disable if not enabled on advanced file upload fields
6. Remove default multi-file upload to prevent possibility of uploading files to server before over-ride is complete

V2.0.8
BUG FIX
Fixed bug omitting non-select/check/radio field into raw fields data in export and identifiers in form list

V2.0.7
BUG FIX
Previous fix broke file upload capability check

V2.0.6
BUG FIX
Fixed foreach bugs in enqueue.php

v2.0.5
SECURITY FIX
Added new server-side method of removing submit button from Gravity forms set as HIPAA compliant since Javascript method has potential of failing to remove it.

V2.0.4
MAJOR UPDATE!
1. Removed deprecated fields only option and removed additional fields call to API to reduce call size and increase speed of loading forms
2. Improved file upload handling if no file selected
3. Added option to export forms, form notes & form history to CSV files
4. Reworked raw form field submission to strip all html and include additional options for export

V2.0.3
MAJOR UPDATE!
1. Form history improvements and bug fixes
2. Submitted forms list now displays unviewed forms as white and viewed forms as gray
3. File upload now available

V2.0.2
MAJOR UPDATE!
File upload capability added for subscriptions that enable this new feature

V2.0.1
1. DOCS UPDATE
2. Added cm-submitted-form-title class to submitted form title h2 tag in order to style it easier from CSS

V2.0
BUG FIXES
1. Fixed "select all" checkbox bug in Gravity Forms.
2. Added better way of checking if Gravity Forms Active while keeping original as fail safe.

V1.9.9
BUG FIXES
1. Fixed Gravity advanced multifield address field being ignored by validation if required.
2. Added previous version fix for required conditional hidden radio/checkbox fields to multipage/multistep forms.

V1.9.8
1. Put fix in for conditional hidden radio/checkbox fields in Gravity Forms
2. Started form specific history although will not be 100% until next release.

V1.9.7
1. Changed front-end nonce back to original method to prevent input from being added to head
2. Major change to how submitted forms are pulled from API to reduce amount of data being passed to solve some hosts to break on very long forms.  Actual form doesn't pull until toggled now.

V1.9.6
1. Fixed required conditional fields breaking validation if hidden
2. Added archived forms interface
3. Add notes to bottom of web & PDF versions of submitted forms

V1.9.5
BUG FIXES
1. Stripped slashes from custom email notification html
2. Removed Gravity Forms date picker icon and hidden icon path field on submitted forms

V1.9.4
BUG FIXES
1. Added fix for hipaa user role not able to view dashboard in some situations
2. Added fix for saving selected form settings
3. Added validation check to prevent saving malformed json when saving selected form settings

V1.9.3
BUG FIX
Fixed bug if new notes property doesn't exist

V1.9.2
BUG FIX
Fixed undeclared variable bug if no "send to" option set in selected form settings.

V1.9.1
MAJOR RELEASE!
Added notes feature for standard subscribers

V1.9.0
MAJOR RELEASE!
1. Added more options to default notification email settings.
2. Added form-specific custom notification email option.
3. Changed internal notification email handling significantly, please test & report any issues asap!

V1.8.9.1
Minor fix for unset location variable causing email notice to break.

V1.8.9
Fixed stupid undefined property notice

V1.8.8
1. Added further support for the free basic API subscription option
2. Changed the plugin to revert to the free basic option if a paid subscription expires instead of deactivating the forms.
3. Changed nonce handling on front end to try and get around aggressive cache issues.

V1.8.7
Added support for free basic option

V1.8.5
1. Added support for Gravity Forms multi-step links
2. Fixed style bug in admin settings tabs
3. Started adding option to customize notification email subject

V1.8.4
1. Fixed Caldera validation on checkbox/radio groups

V1.8.3
1. Fixed unclosed tag in js causing Caldera validation to break on Safari.
2. Added 7 day grace period and alert message to admin submitted forms view on subscription expiration.

V1.8.2
1. Added fallback fix for jQuery not getting values from textarea fields in some cases.
2. Started adding the new interface for form history & notes.

V1.8.1
1. Tested for Wordpress version 4.9.8
2. Updated docs/faq's

V1.7.9
Bug fix for adding/changing form builder if only 1 option exists

V1.7.8
1. Updated settings
2. Completely changed how to manage the hipaa_forms user role capabilities
3. Added option to customize notification emails

V1.7.7
1. Increased z-index for privacy modal to ensure it sits over other fixed & absolute positioned elements
2. Removed needless WP admin settings menu item as all settings are updated from within the plugin itself
3. Fixed pagination bug

V1.7.6
1. Added option to filter forms by form name.
2. Updated initial view before adding a license key.
3. Test on Wordpres 4.9.7

V1.7.5
1. Fixed bug mistakenly setting a value of 1 for a selected user if not set causing users with hipaa_user role to not see forms.
2. Fixed validation error on optional location select field if set to required and no value is set for an option.
3. Added support for secondary staging domain to be used.
4. Fixed css issue with sending animation bar not showing when submitting a form.

V1.7.4
1. Fixed support ticket bug not showing replies.

V1.7.3
1. Fixed signature field validation error.
2. Added HIPAA privacy notice customization options.
3. Added info icons to settings.
4. Updated BAA to be more clear on who the covered entity & who the associate or subcontractor is.

V1.7.2
Fixed bug in iOS not getting textarea values using .val(), not using .attr('value'), frickin iOS.

V1.7.1
1. Improved form validation for both Gravity & Caldera relying on their built-in error classes as well as adding a "scroll-to first error" function.
2. Fixed a bug with checkbox/radio input validation on both Caldera & Gravity.
3. Added function to replace Gravity file upload input with message that it is not HIPAA compliant & has been removed.
4. Improved default mobile styling a little for appended items at bottom of form on mobile.
5. Added touchstart event listener for mobile to validate signature field.
6. Added some security to support ticket system.

V1.7.0
Bug fix for Gravity Forms if signature option has no value

V1.6.9
1. Fixed multiple signature initialization when clicking back and next again in Gravity Forms multipage
2. Add additional Gravity Forms validation messages on error
3. Fixed Gravity multipage progress bar not reflecting current step
4. Added scroll to top of form when clicking prev/next in Gravity multipage

V1.6.8
Something went wrong with the last SVN commit and some JS files weren't included

V1.6.7
1. Add callback function option
2. Over-rode Gravity Forms multipage next/previous function to make HIPAA compliant & enabled multipage
3. Improved field validation for Gravity Forms
4. Add optional signature field validation
5. Removed HIPAA privacy modal from showing in submitted forms
6. Other minor improvements

V1.6.6
Minor update

V1.6.5
Minor fix for unset variable on plugin options
Changed privacy policy to load in modal instead of external link

V1.6.4
Minor fix for unset variable if Gravity not selected.
Updated expired nonce notice

V1.6.3
Fixed deprecated attr() checkbox check

V1.6.2
Fixed PDF delete on modal window close

V1.6.1
Fix to remove email notice headers if using SendGrid plugin

V1.6
Bug fix for Gravity forms allowing submissions with empty required fields

V1.5.9:
CRITICAL BUG FIX

V1.5.8:
Bug fix

V1.5.7:
Fixed minor loop warning for Gravity Forms

V1.5.6:
Basic design and code cleanup

V1.5.5:
MAJOR RELEASE!
This update includes an improved user interface and the following specific form settings:
1. Option to show/hide the signature field
2. Option to specify a success message or a redirect url after a form is submitted
3. Option to set who can see the submitted forms with the following options:
    A. All users with admin/hipaa user role
    B. Only specific users
    C. Only a specific doctor/user selected within a form (ie. Patient selects a specific doctor in a form, only that doctor will see the submitted form).  NOTE: Admins see all forms regardless of settings.

== Upgrade Notice ==
BUG FIXES
1. Fixed Gravity advanced multifield address field being ignored by validation if required.
2. Added previous version fix for required conditional hidden radio/checkbox fields to multipage/multistep forms.

== Planned For Next Major Release ==
1. Finish form-specific history interface

== Currently In Development Premium Add-Ons ==
1. Secure file upload
2. Secure save for later ability
3. Appointment manager
4. Improved Notes Interface/Functionality
5. Patient Communication Portal (Virtual Visit/HouseCall)
    A) Real-Time 2-Way Messaging
    B) Patient Access to Submitted Forms
    C) Video E-Visit

