#Basic Http Auth extension
This is a simple Behat extension which allows add credentials for Basic
Http Auth in behat.yml.

It calls setBasicAuth() method from Mink's session and sets credentials
before each scenario after session initialization.
