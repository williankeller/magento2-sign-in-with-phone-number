# Sign in With Phone Number for Magento 2

This extension allow your customers to login to your Magento store using their phone number. Also it is possible to login using both (email or phone number) at the same field, this extension is capable to handle the field value and access the store with the provided data - Mobile login extention for Magento 2.
- Login with mobile phone number or email.
- Possibility to create account using mobile phone number.
- Change phone number under customer dashboard.

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/045049ad5e1e4750ac9a7e2544df46fd)](https://www.codacy.com/manual/magestat/magento2-sign-in-with-phone-number)
[![Build Status](https://travis-ci.org/magestat/magento2-sign-in-with-phone-number.svg?branch=develop)](https://travis-ci.org/magestat/magento2-sign-in-with-phone-number) 
[![Packagist](https://img.shields.io/packagist/v/magestat/module-sign-in-with-phone-number.svg)](https://packagist.org/packages/magestat/module-sign-in-with-phone-number) 
[![Downloads](https://img.shields.io/packagist/dt/magestat/module-sign-in-with-phone-number.svg)](https://packagist.org/packages/magestat/module-sign-in-with-phone-number)

## Installation

### Install via composer (recommended)

Run the following command in Magento 2 root folder:
```sh
composer require magestat/module-sign-in-with-phone-number
```

```sh
composer install
```

### Using GIT clone

Run the following command in Magento 2 root folder:
```sh
git clone git@github.com:magestat/magento2-sign-in-with-phone-number.git app/code/Magestat/SigninPhoneNumber
```

## Activation

Run the following command in Magento 2 root folder:
```sh
php bin/magento module:enable Magestat_SigninPhoneNumber
```
```sh
php bin/magento setup:upgrade
```

Clear the caches:
```sh
php bin/magento cache:clean
```

## Configuration

1. Go to **STORES** > **Configuration** > **MAGESTAT** > **Sign in With Phone Number**.
2. Select **Enabled** option to enable the module.
3. Under **Settings** tab, change the **Sign in Mode** to fit to your login process.

## Uninstall

```sh
php bin/magento module:uninstall -r Magestat_SigninPhoneNumber
```

## Contribution

Want to contribute to this extension? The quickest way is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).


## Support

If you encounter any problems or bugs, please open an issue on [GitHub](https://github.com/magestat/magento2-sign-in-with-phone-number/issues).

Need help setting up or want to customize this extension to meet your business needs? Please open an issue and I'll add this feature if it's a good one.
