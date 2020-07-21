## Release 0.11.0
- Add global contact form popup, triggered on floating button click.
- Add alternate WA numbers. Could be used in select_channel field tag, allowing user/visitor select channel/number to send.
- Restyling quickshop list.
- Bugs Fixed.
- Code cleanups.

## Release 0.10.16
- Redesign quickshop's product detail popup.
- Improve sticky functionality on quickshop order review.
- Bugs fixed on WA resend options.

## Release 0.10.15
- Add weight calculation on quickshop for future use.
- Improve max-height of quickshop's order review only when sticky active.
- Fix go to item on quickshop order review for variation item.
- Fix variant button label missing after load new page on active toggle.

## Release 0.10.14
- Add option to resend whatsapp message.
- Add custom hooks to quickshop order review.

## Release 0.10.13
- Fix non-session woocommerce order inside cf7.
- Re-styling order review list and add to cart/checkout button for quickshop.

## Release 0.10.12
- Fix woocommerce session on cf7 submission for checkout.

## Release 0.10.11
- Fix quickshop & checkout shortcode not rendered inside contact form.

## Release 0.10.10
- Fix missing Hooks object causing not sending WA message on basic contact form.

## Release 0.10.9
- Fix some bugs and code improvements.
- Improve styling options for quickshop.

## Release 0.10.8
- Add id & max-height attribute on cf7sendwa-checkout shortcode
- Add js hook to filter sticky top & bottom on cf7sendwa-checkout
- Add js hook to filter phone number & text message before send to whatsapp

## Release 0.10.7
- Add attachments field in contact form's WhatsApp tab
- Bugs Fixed and code cleanups

## Release 0.10.6
- Bugs fixed for shipping total.
- Add custom filter for replacable template.

## Release 0.10.5
- Improve : Add custom hooks
- Fix order note in woocommerce order
- Fix additional meta data in woocommerce order
- Other bugs fixed and code cleanups 

## Release 0.10.4
- Improve : Make woo-orderdetail more readable on WhatsApp message
- Improve : Add sku & weight to Item Cart for further use.
- Several bugs fixed

## Release 0.10.3
- Add : Hooks for custom settings
- Improve : Quick shop improvements and code cleanups

## Release 0.10.2
- Improve : Quick shop improvements
- Quick shop bugs fixed.

## Release 0.10.1
- Improve : Add sticky option on checkout item summary
- Improve : Add remove item on checkout summary
- Improve : Option to make quantity editable 
- Improve : Add scroll to product when checkout's item summary clicked
- Code optimization and cleanups

## Release 0.10.0
- Add : Product list inside your Contact Form for quick shop.
- Add : Use quick shop outside the contact form.
- Code optimizations and cleanups. 

## Release 0.9.3
- Improvement: Convert mobile number entry when user not defined country code 
- Improvement: Add option to set default country code

## Release 0.9.2
- Fix: Displayed shipping address on checkout page when logged in customer change address on cart view.

## Relase 0.9.1
- Fix: Warning illegal offset, when WhatsApp tab not in use.

## Relase 0.9.0
- Add: Autorespond for WhatsApp API users.
- Add: WhatsApp template Tab

## Relase 0.8.3
- Add: API integration to WABlas.
- Add: API integration to RuangWA.
- Some code cleanup.


## Relase 0.8.2
- Add: API integration to Fonnte.
- Bug Fixing for Custom WhatsApp API.

## Relase 0.8.1
- Add: Option to disable redirect after WooCommerce checkout/order.
- Add: Shortcode [cf7sendwa-received-link] and [cf7sendwa-payment-link] to be used in success message.

## Relase 0.8.0
- Add: Save WooCommerce order for logged in customer.
- Add: Option to redirect ( thank you or payment page ) after send WooCommerce order.

## Relase 0.7.1
- Add: Custom whatsapp API via action hook.

## Relase 0.7.0
- Add: Contact form as popup modal.
- Some bugs fixed


## Relase 0.6.5
- Add: Option to require shipping method for shippable cart.
- Add: Option to make cart totals box in cart page full width.
- Some bugs fixed

## Relase 0.6.4
- Improvement: Access Twilio API using native WordPress remote post library instead of using Twilio SDK.
- Improvement: Empty cart validation for woocommerce checkout.
- Several bugs fixed


## Relase 0.6.0
- Add: Woocommerce Integration for order checkout
- Serveral bugs fixed


## Relase 0.5.5
- Add: File attachments
- Serveral bugs fixed


## First Relase 0.4.2
- Send WhatsApp message from mail template using traditional WhatsApp API url (https://api.whatsapp.com/)
- Integration with Twilio API (see https://solusipress.com/kirim-pesan-whatsapp-dengan-contact-form-7-dan-twilio-api/)