# Basic Http Auth extension

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kyvour/basic-http-auth-extension/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/kyvour/basic-http-auth-extension/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/120aa8ce-dec7-4132-aafd-3205ae542d28/mini.png)](https://insight.sensiolabs.com/projects/120aa8ce-dec7-4132-aafd-3205ae542d28)
[![Latest Stable Version](https://poser.pugx.org/kyvour/basic-http-auth-extension/v/stable)](https://packagist.org/packages/kyvour/basic-http-auth-extension)
[![Latest Unstable Version](https://poser.pugx.org/kyvour/basic-http-auth-extension/v/unstable)](https://packagist.org/packages/kyvour/basic-http-auth-extension)
[![Total Downloads](https://poser.pugx.org/kyvour/basic-http-auth-extension/downloads)](https://packagist.org/packages/kyvour/basic-http-auth-extension)
[![License](https://poser.pugx.org/kyvour/basic-http-auth-extension/license)](https://packagist.org/packages/kyvour/basic-http-auth-extension)


This is a simple Behat extension which allows add credentials for Basic
Http Auth in behat.yml.

It calls setBasicAuth() method from Mink's session and sets credentials
before each scenario after session initialization.
