## CUSTOM THEME

### [1.0.1] - 2020/11/19
- Fixed bug from post-launch smoke test.

### [1.0.0] - 2020/11/18
- Launch of the website.

---

## VORTEX THEME

### 2020/11/02
- Added Babel and `polyfills.js` to permit usage of Javascript ES6.

### 2020/10/27
- Added a script on `save_post` that clear cache of page linked to post type.

### 2020/10/22
- Added default classes to block.

### 2020/10/21
- Added Autocomplete search
- Added required plugin default json

### 2020/08/12
- Fixed and error on image select field type module (double folder path)
- Fixed an error on 404 page (was escaping render_block() with esc_html() instead of wp_kses_post())

### 2020/08/06
- Fixed an error in inc/common/common.default.img.php. The use of Sage class was missing.

### 2020/08/04
- Removed instances of hooks related to Yoast SEO plugin. 

### 2020/07/27
- Added composer scripts : phpcs, phpmd, phpunit and phpstan
- Fix all PHPCS Errors and Warnings
- add new function Vortex\files\puts_content to write content in a local file.

### 2020/07/23
- Added datetime helpers.

### 2020/07/12
- Added breadcrumbs that can be used with `get_partial( 'template/breadcrumbs/breadcrumbs' )`.
- Added sitemap that can be used with `template-sitemap.php`
- Changed navigation a lot including naming, files structure, logics, effets when editing a menu in admin and more.
- Added a mixted function of `get_partial` and `get_template_part` named `get_template_part_with_data`.

### 2020/06/18
- Added basic styling for table HTML elements
- Changed 404 page content. Now uses a block array rendered with render_block() so block filters are applied on the 404 page.

### 2020/06/15
- Pass theme trought PHPCS for compliance
- The files in folder inc should have a second look

### 2020/06/05
- Added WPML config file details in documentation folder

### 2020/05/06
- Upgrade of the function vtx_get_thumbnail_url() and creation of vtx_get_thumbnail() that does just like get_the_post_thumbnail() and get_the_post_thumbnail_url() but with fallback images that can be set in option page "Default image by post type". You can pass all the same argument than the native fonctions. If no image is set in these option, the fallback is the default image in dist/image/defaults repo. 

### 2020/05/05
  - Added a button to allow a tabbing user to skip directly to content and skip navigation
  - Added a script that detect if a user is using the keyboard to navigate and add a class to body + CSS to remove the outline on focus element if the user is not using a keyboard
  
### 2020/03/17
  - Added hierarchical support for permalinks of post type linked to page. Documentation about this in DOC_TECH.md.

### 2020/03/13
  - Added new utility functions (get_default_img_src, get_html_acf_link_array).

### 2020/03/04
  - Added new sass functions.
  - Added sticky column in option page for menus.
  
### 2020/03/02
  - Updated accessibility of dropdown menus.

### 2020/02/28
  - Added client documentation regarding Gutenberg blocks and post types.

### 2020/02/19
  - Added dynamic filter for function "get_partial".
  
### 2020/02/05
  - Added fselect module (php template, script and styling). Renders a multiselect field based on FacetWP's one with custom options.
  - Updated Icomoon files to include new arrow icon.
  - Updated mobile menu styling with new arrow icon.

### 2020/02/03
  - Added FacetWP json settings auto-save

### 2020/01/09
  - Added styling to header and made it fixed on top of the page.
  - Added mobile menu

### 2019/11/01
 - Added filter save_post for post-type link to page. It fix the 404 problem when changing the slug of a linked page.

### 2019/10/08
 - Added Composer
 - New CustomPostTypes class that extend [PostTypes](https://posttypes.jjgrainger.co.uk/post-types/) library
 - add subpage options "post_type_link_to_page"
 - change SCSS structure

### 2019/09/20
 - added a simple way to display popup. You can set it to limit how many time to display it by user session.

### 2019/07/04
 - add page option by default
 - add template-redirect
 - add partial social-media and associated SCSS

### 2019/05/08
 - better wysiwyg style with default list styling (true mathematical vertical center for UL).
 - add breakpoints to admin editor style

### 2019/05/03
 - fix error add_filter callback remove_page_template_from_dropdown called without namespace.
 - add favicon only for admin.

### 2019/05/02
 - change method of styling wysiwyg in admin.

### 2019/04/08
 - remove bootstrapp and associated variables
 - add reset.scss to replace reboot.scss

### 2019/03/29
 - add wysiwyg style with default list styling.

### 2019/03/21
 - add modernizr with settings: touchscreen, focus-within and placeholder as defaults
 - add style to make clickable phone links disabled on desktop (not true for now. Debate still ongoing)
 - add dropdown-menu by default

### 2019/03/19
 - add defaults for favicon front-end and back-end
 - add default for theme screenshot
 - convert files name in Kebab Case
 - replace "<?=" with "<?php"
 - move some add_action before the function declaration for clarity
 - replace some (only one) _e() to _ex() and __() to _x()
 - remove theme comments support by default
 - edit changelog for quick custom theme
 - remove file _variables.scss
 - add folder variables and some variables files (colors, fonts, etc.)
 - add "font-size: 62.5%;" principle on selector "html" in global.scss
 - add fixe for sticky bottom footer (not compatible with IE11)

### 2019/04/11
 - add WooCommerce theme support
