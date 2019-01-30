Sign in With Phone Number for Magento 2
=====================

This extension allows customers to access your store using their phone number.
Also is possible to login using both, email or phone number at the same field.
- Login with phone number or email.
- Possibility to create account using phone number.
- Change phone number under customer dashboard.

[![Build Status](https://travis-ci.org/magestat/magento2-sign-in-with-phone-number.svg?branch=develop)](https://travis-ci.org/magestat/magento2-sign-in-with-phone-number) [![Packagist](https://img.shields.io/packagist/v/magestat/module-sign-in-with-phone-number.svg)](https://packagist.org/packages/magestat/module-sign-in-with-phone-number) 

## 1. Installation

### Install via composer (recommend)

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

## 2. Activation

Run the following command in Magento 2 root folder:
```sh
php bin/magento module:enable Magestat_SigninPhoneNumber --clear-static-content
php bin/magento setup:upgrade
```

Clear the caches:
```sh
php bin/magento cache:clean
```

## 3. Configuration

1. Go to **Stores** > **Configuration** > **Magestat** > **Sign in With Phone Number**.
2. Select **Enabled** option to enable the module.
3. Under *Settings* tab, change the **Sign in Mode** to fit to your login process.

## 4. Uninstall

```sh
php bin/magento module:uninstall -r Magestat_SigninPhoneNumber
```


## Contribution

Want to contribute to this extension? The quickest way is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).


## Support

If you encounter any problems or bugs, please open an issue on [GitHub](https://github.com/magestat/magento2-sign-in-with-phone-number/issues).
Need help setting up or want to customize this extension to meet your business needs? Please email willianlkeller@outlook.com and if we like your idea we will add this feature for free or at a discounted rate.

© Magestat.
