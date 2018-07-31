# AppMonitor
* EN - Monitoring for Web apps based on Selenium Webdriver, FB Chrome Driver, RRDTool et NSCA
* FR - Supervision d'applications Web basée sur Selenium Webdriver, FB Chrome Driver, RRDTool et NSCA

## Features
* Define a scenario in wich you describe steps to test if a website is available, if login is successful, if different links work and get response time for each step plus total time.
* Each scenario is written in a seperate file, stored in the `testcases` folder and contains the steps to use, so you can define different steps and actions for each one.
* The TestSelenium script is lightweight and fast, so you can use it not only for unit testing, but also for monitoring your apps. That's why it's packed with RRDTool and NSCA integration.

## Technologies
* PHP Object Oriented scripts
* Selenium WebDriver https://www.seleniumhq.org/
* FB Chrome Driver https://github.com/facebook/php-webdriver
* RRDTool https://oss.oetiker.ch/rrdtool/
* NSCA https://exchange.nagios.org/directory/Addons/Passive-Checks/NSCA--2D-Nagios-Service-Check-Acceptor/details
* PHPMailer https://github.com/PHPMailer/PHPMailer

## Installation
### Scripts
Clone or Copy the content of the folder into an apache server

### RRD
(doc in progress)

### Mail
You need to set up a SMTP server

### Nagios NSCA
(doc in progress)

### External App status system
You can use a SSH command to create files with the status.
We use a JS script to check the status each app and to create a list of div, styled to show if each app is available.

### Log file (work in progress)

## Configuration
(doc in progress)

## Usage
(doc in progress)

## Examples
(doc in progress)