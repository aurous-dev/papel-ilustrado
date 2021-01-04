# WP-Base v1.1

This is a base WP installation theme, with custom plugins inside to work with. It has: 

**Plugins:**
- Yoast SEO Plugin
- Advanced Custom Fields
- Duplicate Post
- Custom Post Order
- Classic Editor (To avoid Gutenberg)
- Duplicator
- Custom Post Type
- Contact Form 7
- WP-Migrate-db (NEW)

**Tools included:**

- VUEjs
- Webpack
- jQuery
- Font Awesome
- Bootstrap
- Sass mq
- Slick Slider


# Installation

- Clone this repo
- Copy the contents of wp-config-sample to new file wp-config:
```
cp wp-config-sample.php wp-config-php
```
- Change the new **wp-config.php** file with your database credentials (the db should be created already).
- Complete the WP installation steps.
- Login to your new WP installation.
- Go to Appereance > Themes and select **Base Theme** as your theme.
- Go to **Plugins** and activate/deactivate as you need.
- **Important!** Install your dependencies
```
NPM Install
```
- To exec Webpack run:
```
NPM run build
```
- You are ready to start developing