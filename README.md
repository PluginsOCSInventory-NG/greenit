# OCS Inventory Plugin - Green IT

<p align="center">
  <img src="https://cdn.ocsinventory-ng.org/common/banners/banner660px.png" height=300 width=660 alt="Banner">
</p>

<h1 align="center">Plugin GreenIT</h1>
<p align="center">
  <b>Some Links :</b><br>
  <a href="http://ask.ocsinventory-ng.org" target="_blank">Ask question</a> |
  <a href="https://www.ocsinventory-ng.org/?utm_source=github-ocs" target="_blank">Website</a> |
  <a href="https://www.ocsinventory-ng.org/en/#ocs-pro-en" target="_blank">OCS Professional</a>
</p>

## Description
Currently supported on Windows, this plugin is made to retrieve gather power consumption information.

> _**IMPORTANT NOTE : This plugin is working with a windows service application downloadable <a href="https://github.com/Atineon/ocsinventory-service_greenit" target="_blank">here</a>**_



## Prerequisites
*The following dependency need to be installed on your server*
- [Perl module]  DateTime.pm

## Installation
To install the plugin on your server :
- Download the plugin and extract to the server extensions folder. _(<a href="https://wiki.ocsinventory-ng.org/10.Plugin-engine/Using-plugins-installer/#plugin-activation" target="_blank">Documentation</a>)_

- Copy the files of datafilter folder into your server folder _(/etc/ocsinventory-server)_ :

    ![tree](https://i.postimg.cc/pVk79B1r/Capture-d-cran-du-2023-05-09-16-22-38.png)

- Set the crontab :
    - Use the command `$ crontab -e` in the server terminal
    - Add those two crontabs :
        - `0 5 * * 1 php /usr/share/ocsinventory-reports/ocsreports/extensions/greenit/script/cron_stats.php --mode full`
        - `0 * * * * php /usr/share/ocsinventory-reports/ocsreports/extensions/greenit/script/cron_stats.php --mode delta`
    > *NOTE : Those two crontabs are default one. You are allow to change the execution time. (By default, every Mondays at 5 a.m for full mode and every hours for delta mode)*
    