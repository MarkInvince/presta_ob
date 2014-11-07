Ask For a Quote Module for PrestaShop by Presta FABRIQUE
version 3.3 (21.07.2014)
http://www.presta-shop-modules.com

Tested under PrestaShop 1.4, 1.5 and 1.6 and only on default theme

FEATURES
Your clients have the possibility to ask details about certain products. The process is very similar to the normal "Add to cart - Submit order" one, but obviously there is no checkout involved.
 - users can ask price details about products
 - My Quotes is added to My Account list
 - Quotes side and top box - aka "Quotes Cart"
 - animated add to quote cart effect
 - quantity is also saved (even when Presta is in catalog mode)
 - client and admin can bargain on price
 - quotes have statuses: agreed or not agreed (set by admin)
 - one page login / register (just as one page checkout) with fast register (OPC)
 - module is PrestaShop 1.5 and Prestabox compatible
 - per category permissions added (visible only on selected categories)
 - guest checkout option
 - quotation Terms and conditions
 - quantity change on overview page
 - products can be quoted with different attributes and color combos
 - email alerts for both client and admin (at submit and at bargain)
 - under PrestaShop 1.6 module is responsive!
 - quotes are gouped based on submit date
 - total original and bargained prices per groups
 - comments can be submitted for each seperate quote group

Back office options:
 - Quote status can be set as "Agreed" at bargain end
 - separate menupoint under Orders tab is automatically added
 - Fast checkout can be enabled / disabled
 - category permission management
 - Guest checkout can be enabled / disabled
 - Bargain can be enabled / disabled
 - Terms and Conditions can be filled in multilanguage format and can be set as mandatory or not
 - Multiple and custom receiver email addresses can be set
 - admin can delete quotes (useful for abandoned requests)

------------------------
CHANGELOG
0.1 to 0.3 (0.2 got updated again just before release due to a bug discovered)

- removed mandatory catalog mode: both this module and Cart can be active the same time
- added simple checkout: less fields when client submits order
- fixed bug with transplant to left column
- fixed a display bug in BO, when same product was quoted twice or more but with diff. attributes
- fixed an email related bug

0.3 to 0.4
- module was adapted to work with SSL enabled stores

0.4 to 0.4.2
- bug fix related to the SSL implement
- bug fix with left menu add to quote cart action

0.4.2 to 1.0
 - added Prestashop 1.5 compatibility
 - quantity is saved with quote
 - bargain possibility is added
 - bugs corrected and styles added

1.0 to 1.1
 - dedicated Tab in 1.5 BackOffice now works
 - module has become Prestabox compatible (rewrite)
 - module is multistore compatible

1.1 to 1.1.2
 - translation bug fixed

1.1.2 to 1.2
 - per category permissions added
 - several look related fixes

1.2 to 2.0
 - added guest checkout
 - added Terms and Conditions
 - added possibility to update quantity on overview page
 - several CSS improvements
 - several structure improvements

2.0 to 2.1
 - when same product is added twice, quantity is updated
 - same product can be quoted again with different attributes
 - product in cart is a link
 - autosave message on overview page when quantity is updated
 - SEO link - 1.4 ONLY
 - email attributes bug
 - several code and style optimisations

2.1 to 2.2
 - base script changed and button can be added to product list, featured etc.
   (see guide on module configuration page)
 - URL bug fix on 1.5

2.2 to 2.3
 - module adapted to Prestashop v. 1.5.4

2.3 to 2.4
 - smaller bugfixes

2.4 to 2.5
 - smaller bugfixes
 - radio button attribute detection fix
 - created email folders for different languages (not translated)
 - added several visual enhancements (product image fly to the cart, other cart animations)

2.5 to 2.6
 - reference code bug fix

2.6 to 2.7
 - added header cart block for one column design theme compatibility
 - bargain option can be toggled (enabled / disabled)
 - Terms and Conditions made rich text compatible (WYSIWYG editor)
 - staff email was optimized for instant reply possibility and contains full client data
 - if logged in, instant submit option is available in the quote cart
 - redesigned Back office settings page
 - JS and code enhancements

2.7 to 2.8
 - fixed compatibility bugs with Presta 1.4
 - fixed compatibility for product names containing special characters

2.8 to 2.8.1
 - fixed bug to show correct combination image in overview page

2.8.1 to 2.9
 - added mail alerts when bargains are submitted
 - possibility to select email receivers, even multiple and custom ones, not just admin

2.9 to 3.0
 - module is Prestashop 1.6 compatible (functionality and responsive)
 - bargain and details windows are floting above the right row
 - counter for submitted bargains beside details link
 - module works for Quick view also (default template)

3.0 to 3.1
 - quotes are grouped based on submit
 - automatic or custom group naming (name is editable later)
 - message to seller at submit page
 - total original and bargained price calculated per group
 - draggable and floating details and bargain windows
 - numerous fixes and code optimisations (based on Prestashop Validator)
 - in Back office quotes are visible only from the dedicated Tab

3.1 to 3.2
 - fixed bug with multicurrency. now all quotes are saved in the currency they were submitted
 - fixed a bug with combination prices. if a combo has lower price, module now detects it
 - fixed a bug with quote list duplication on login
 - added an exeption handler for Prestashop 1.6.0.8 jquery loading changes
 - if no price defined, this is represented accordingly in history page
 - footer my account block now has a quote link

 3.2 to 3.3
 - added possibility to for admin to delete a quote
 - fixed some attribute related bugs


UPDATING
Update to new version is not possible, and old version must be uninstalled first!

------------------------

CONTENTS of package

1) askforaquote files and folders

2) readme_en.txt (this file)

------------------------

INTSALL

In both 1.4, 1.5 and 1.6:

 - head to your Back Office / Modules tab

 - in 1.4 press: "Add a new module from my computer" link
   in 1.5 and 1.6 press: "Add new module" button

 - browse for the ZIP file you just downloaded and press "Upload this module" button

 - BO - Positions tab: move our module as first of "Top of pages" hook

 - your done

 - Dedicated tab is automatically added under "Orders"

------------------------

HOW TO DEBUG AND MODIFY/EDIT THE MODULE

1) follow all steps from install procedure

2) in your Back Office go to the Tab: Preferences - Performance

3) set the following as stated below
- Force compile: enabled
- Cache: disabled
Also, all Javascript related compression must be disabled in the options below on that page!
(if these settings are like stated, than please move to next step)

3b) clear Browser cookies!

4) edit settings in TPL and CSS files and go anywhere on your front office and refresh with CTRL + F5

5) keep this procedure till you are OK with all looks

6) go back to your back office and set back those 2 options as they were at step 3

------------------------

NOTICE:

1) The module was tested with an unmodified version of PrestaShop and on the default theme.

2) The module does not overwrite any core PrestaShop files. If you are asked to overwrite anything, interrupt the process, and contact us to solve the issue.

3) For suggestions, bugs and help with modified and edited versions of PS pls contact us using the form on www.presta-fabrique.com or at www.presta-shop-modules.com